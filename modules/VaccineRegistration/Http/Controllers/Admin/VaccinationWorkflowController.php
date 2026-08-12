<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Modules\VaccineRegistration\Models\InventoryLot;
use Modules\VaccineRegistration\Models\Registration;
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

        if ($registration->payment_status !== Registration::PAYMENT_PAID) {
            $msg = 'Bệnh nhân chưa hoàn tất thanh toán hóa đơn. Không thể thực hiện quy trình lâm sàng.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }
            return redirect()->back()->withErrors(['payment_status' => $msg]);
        }

        if (AdminContext::isBranchAdmin() && (int) $registration->center_id !== (int) AdminContext::centerId()) {
            abort(403, 'Bạn không có quyền tiếp nhận bệnh nhân thuộc chi nhánh khác.');
        }

        $oldStatus = $registration->status;
        $registration->checkIn();
        $newStatus = $registration->fresh()->status;
        if ($oldStatus !== $newStatus) {
            AuditLogger::log(
                'vaccination.checked_in',
                'registration',
                $registration->id,
                ['status' => $oldStatus],
                ['status' => $newStatus],
                $registration->center_id
            );
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tiếp nhận bệnh nhân thành công.',
                'data' => $registration->fresh(['patient']),
            ]);
        }

        return redirect()->back()->with('success', 'Tiếp nhận bệnh nhân thành công.');
    }

    /**
     * Step 2: Clinical Screening.
     * Records screening_status ('eligible', 'deferred', 'contraindicated') and notes.
     */
    public function screening(Request $request, $id)
    {
        $registration = Registration::findOrFail($id);

        if ($registration->payment_status !== Registration::PAYMENT_PAID) {
            $msg = 'Bệnh nhân chưa hoàn tất thanh toán hóa đơn. Không thể thực hiện quy trình lâm sàng.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }
            return redirect()->back()->withErrors(['payment_status' => $msg]);
        }

        if (AdminContext::isBranchAdmin() && (int) $registration->center_id !== (int) AdminContext::centerId()) {
            abort(403, 'Bạn không có quyền khám sàng lọc cho bệnh nhân thuộc chi nhánh khác.');
        }

        $validated = $request->validate([
            'screening_status' => 'required|string|in:eligible,deferred,contraindicated',
            'screening_notes' => 'nullable|string',
        ]);
        $oldValues = $registration->only(['screening_status', 'screening_notes']);
        $registration->screening($validated['screening_status'], $validated['screening_notes'] ?? null);
        $newValues = $registration->fresh()->only(['screening_status', 'screening_notes']);
        if ($oldValues !== $newValues) {
            AuditLogger::log(
                'vaccination.screened',
                'registration',
                $registration->id,
                $oldValues,
                $newValues,
                $registration->center_id
            );
        }

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

        if ($registration->payment_status !== Registration::PAYMENT_PAID) {
            $msg = 'Bệnh nhân chưa hoàn tất thanh toán hóa đơn. Không thể thực hiện quy trình lâm sàng.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }
            return redirect()->back()->withErrors(['payment_status' => $msg]);
        }

        if (AdminContext::isBranchAdmin() && (int) $registration->center_id !== (int) AdminContext::centerId()) {
            abort(403, 'Bạn không có quyền thực hiện tiêm cho bệnh nhân thuộc chi nhánh khác.');
        }

        if ($registration->screening_status !== 'eligible') {
            $screeningStatus = match ($registration->screening_status) {
                'deferred' => 'tạm hoãn tiêm',
                'contraindicated' => 'chống chỉ định tiêm',
                default => 'chưa đủ điều kiện tiêm',
            };
            $msg = "Không thể thực hiện tiêm chủng vì kết quả khám sàng lọc là: {$screeningStatus}.";
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
            'inventory_lot_id' => 'nullable|integer|exists:inventory_lots,id',
            'observation_minutes' => 'nullable|integer|min:1',
            'observation_notes' => 'nullable|string',
        ]);
        $oldRegistrationStatus = $registration->status;

        // Kiểm tra xem vaccine_id có nằm trong phiếu đăng ký không
        $hasVaccine = $registration->vaccines()->where('vaccines.id', $validated['vaccine_id'])->exists();
        if (! $hasVaccine) {
            throw ValidationException::withMessages([
                'vaccine_id' => 'Vắc xin được chọn không nằm trong danh sách đăng ký của lịch hẹn này.',
            ]);
        }

        try {
            $dose = DB::transaction(function () use ($registration, $validated, $oldRegistrationStatus) {
                $lotId = $validated['inventory_lot_id'] ?? null;
                $lot = null;

                if ($lotId) {
                    // Lock lô vắc xin
                    $lot = InventoryLot::lockForUpdate()->findOrFail($lotId);

                    // Kiểm tra các ràng buộc bảo mật y tế của lô vắc xin
                    if ((int) $lot->vaccine_id !== (int) $validated['vaccine_id']) {
                        throw ValidationException::withMessages([
                            'inventory_lot_id' => 'Lô vắc xin này không thuộc loại vắc xin đã chọn.',
                        ]);
                    }

                    if ((int) $lot->center_id !== (int) $registration->center_id) {
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
                        'note' => 'Tiêm chủng cho lịch hẹn '.$registration->registration_code,
                    ]);
                }

                // Ghi nhận liều tiêm
                $dose = $registration->administer(
                    AdminContext::user()?->id,
                    $validated['vaccine_id'],
                    $lotId,
                    $validated['observation_minutes'] ?? 30,
                    $validated['observation_notes'] ?? null
                );

                AuditLogger::log(
                    'vaccination.administered',
                    'registration',
                    $registration->id,
                    ['status' => $oldRegistrationStatus, 'available_quantity' => $lot ? ($lot->available_quantity + 1) : null],
                    [
                        'status' => $registration->fresh()->status,
                        'administered_dose_id' => $dose->id,
                        'vaccine_id' => (int) $validated['vaccine_id'],
                        'inventory_lot_id' => $lotId,
                        'available_quantity' => $lot ? $lot->fresh()->available_quantity : null,
                        'observation_notes' => $validated['observation_notes'] ?? null,
                    ],
                    $registration->center_id
                );

                return $dose;
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
        } catch (ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => collect($e->errors())->flatten()->first() ?: 'Thông tin thực hiện tiêm không hợp lệ.',
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors());
        } catch (\Throwable $e) {
            Log::error('Lỗi kỹ thuật khi thực hiện tiêm chủng.', [
                'registration_id' => $registration->id,
                'exception' => $e,
            ]);

            $message = 'Không thể hoàn tất việc tiêm chủng lúc này. Vui lòng thử lại.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return redirect()->back()->withErrors(['error' => $message]);
        }
    }
}
