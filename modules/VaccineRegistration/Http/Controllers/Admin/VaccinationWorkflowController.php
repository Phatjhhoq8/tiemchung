<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\InventoryLot;
use Modules\VaccineRegistration\Models\StockMovement;
use Modules\VaccineRegistration\Support\AdminContext;

class VaccinationWorkflowController extends Controller
{
    /**
     * Step 1: Patient Check-in.
     * Sets registration status to 'checked_in' and links/creates central patient record.
     */
    public function checkIn(Request $request, $id)
    {
        $registration = Registration::findOrFail($id);

        if (AdminContext::isBranchAdmin() && (int)$registration->center_id !== (int)AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

        $registration->checkIn();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Check-in thành công. Trạng thái đã được chuyển sang checked_in.',
                'data' => $registration->fresh(['patient']),
            ]);
        }

        return redirect()->back()->with('success', 'Check-in bệnh nhân thành công.');
    }

    /**
     * Step 2: Clinical Screening.
     * Records screening_status ('eligible', 'deferred', 'contraindicated') and notes.
     */
    public function screening(Request $request, $id)
    {
        $registration = Registration::findOrFail($id);

        if (AdminContext::isBranchAdmin() && (int)$registration->center_id !== (int)AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

        $validated = $request->validate([
            'screening_status' => 'required|string|in:eligible,deferred,contraindicated',
            'screening_notes' => 'nullable|string',
        ]);

        $registration->screening($validated['screening_status'], $validated['screening_notes'] ?? null);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Sàng lọc lâm sàng hoàn tất.',
                'data' => $registration->fresh(),
            ]);
        }

        return redirect()->back()->with('success', 'Kết quả khám sàng lọc đã được ghi nhận.');
    }

    /**
     * Step 3: Vaccine Administration Execution.
     * Records dose administered, vaccinator, lot number, observation timer, and sets status to 'completed'.
     * Blocks execution if screening_status is 'deferred' or 'contraindicated'.
     */
    public function administer(Request $request, $id)
    {
        $registration = Registration::findOrFail($id);

        if (AdminContext::isBranchAdmin() && (int)$registration->center_id !== (int)AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

        if ($registration->screening_status !== 'eligible') {
            $msg = "Không thể thực hiện tiêm chủng. Bệnh nhân có trạng thái khám sàng lọc là '{$registration->screening_status}' (Cần 'eligible').";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }
            return redirect()->back()->withErrors(['screening_status' => $msg]);
        }

        $validated = $request->validate([
            'vaccine_id' => 'required|integer|exists:vaccines,id',
            'inventory_lot_id' => 'required|integer|exists:inventory_lots,id',
            'observation_minutes' => 'nullable|integer|min:1',
            'observation_notes' => 'nullable|string',
        ]);

        // Kiểm tra xem vaccine_id có nằm trong phiếu đăng ký không
        $hasVaccine = $registration->vaccines()->where('vaccines.id', $validated['vaccine_id'])->exists();
        if (!$hasVaccine) {
            throw ValidationException::withMessages([
                'vaccine_id' => 'Vắc xin được chọn không nằm trong danh sách đăng ký của lịch hẹn này.',
            ]);
        }

        try {
            $dose = DB::transaction(function () use ($registration, $validated) {
                // Lock lô vắc xin
                $lot = InventoryLot::lockForUpdate()->findOrFail($validated['inventory_lot_id']);

                // Kiểm tra các ràng buộc bảo mật y tế của lô vắc xin
                if ((int)$lot->vaccine_id !== (int)$validated['vaccine_id']) {
                    throw ValidationException::withMessages([
                        'inventory_lot_id' => 'Lô vắc xin này không thuộc loại vắc xin đã chọn.',
                    ]);
                }

                if ((int)$lot->center_id !== (int)$registration->center_id) {
                    throw ValidationException::withMessages([
                        'inventory_lot_id' => 'Lô vắc xin không thuộc chi nhánh của lịch hẹn.',
                    ]);
                }

                if ($lot->status !== 'active') {
                    throw ValidationException::withMessages([
                        'inventory_lot_id' => 'Lô vắc xin hiện đang có trạng thái không khả dụng (thu hồi hoặc cách ly).',
                    ]);
                }

                if ($lot->expires_at->isBefore(today())) {
                    throw ValidationException::withMessages([
                        'inventory_lot_id' => 'Lô vắc xin này đã hết hạn sử dụng.',
                    ]);
                }

                if ($lot->available_quantity <= 0) {
                    throw ValidationException::withMessages([
                        'inventory_lot_id' => 'Lô vắc xin đã hết số lượng khả dụng.',
                    ]);
                }

                // Trừ tồn kho
                $lot->decrement('available_quantity');

                // Tạo StockMovement
                StockMovement::create([
                    'inventory_lot_id' => $lot->id,
                    'user_id' => AdminContext::user()?->id,
                    'type' => 'export',
                    'quantity' => 1,
                    'note' => 'Tiêm chủng cho lịch hẹn ' . $registration->registration_code,
                ]);

                // Ghi nhận liều tiêm
                return $registration->administer(
                    AdminContext::user()?->id,
                    $validated['vaccine_id'],
                    $validated['inventory_lot_id'],
                    $validated['observation_minutes'] ?? 30,
                    $validated['observation_notes'] ?? null
                );
            });

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thực hiện tiêm vắc xin và lưu thông tin theo dõi thành công.',
                    'data' => [
                        'registration' => $registration->fresh(),
                        'administered_dose' => $dose->fresh(['vaccine', 'inventoryLot', 'administrator']),
                    ],
                ]);
            }

            return redirect()->back()->with('success', 'Thực hiện tiêm thành công.');
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
