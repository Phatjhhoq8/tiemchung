<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Registration;

class VaccinationWorkflowController extends Controller
{
    /**
     * Step 1: Patient Check-in.
     * Sets registration status to 'checked_in' and links/creates central patient record.
     */
    public function checkIn(Request $request, $id)
    {
        $registration = Registration::findOrFail($id);

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
            'vaccine_id' => 'nullable|integer|exists:vaccines,id',
            'inventory_lot_id' => 'nullable|integer|exists:inventory_lots,id',
            'observation_minutes' => 'nullable|integer|min:1',
            'observation_notes' => 'nullable|string',
        ]);

        try {
            $dose = $registration->administer(
                auth()->id(),
                $validated['vaccine_id'] ?? null,
                $validated['inventory_lot_id'] ?? null,
                $validated['observation_minutes'] ?? 30,
                $validated['observation_notes'] ?? null
            );

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
