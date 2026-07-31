<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Schedule;
use Modules\VaccineRegistration\Models\Slot;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminSlotController extends Controller
{
    /**
     * Display a listing of slots.
     */
    public function index(Request $request)
    {
        $query = Slot::with('schedule.center');

        if ($request->filled('schedule_id')) {
            $query->where('schedule_id', $request->input('schedule_id'));
        }

        $slots = $query->paginate(20);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $slots
            ]);
        }

        return view('vaccine::admin.slots.index', compact('slots'));
    }

    /**
     * Store a newly created slot.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'start_at' => 'required|string',
            'end_at' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $schedule = Schedule::findOrFail($validated['schedule_id']);
        if (AdminContext::isBranchAdmin() && (int)$schedule->center_id !== (int)AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

        $slot = Slot::create([
            'schedule_id' => $validated['schedule_id'],
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'capacity' => $validated['capacity'],
            'reserved_count' => 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tạo khung giờ thành công.',
                'slot' => $slot
            ], 201);
        }

        return redirect()->back()->with('success', 'Tạo khung giờ thành công.');
    }

    /**
     * Update slot capacity or details.
     */
    public function update(Request $request, $id)
    {
        $slot = Slot::with('schedule')->findOrFail($id);

        if (AdminContext::isBranchAdmin() && (int)$slot->schedule->center_id !== (int)AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

        $validated = $request->validate([
            'start_at' => 'sometimes|required|string',
            'end_at' => 'sometimes|required|string',
            'capacity' => 'sometimes|required|integer|min:0',
            'reserved_count' => 'sometimes|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $slot->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật khung giờ thành công.',
                'slot' => $slot
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật khung giờ thành công.');
    }

    /**
     * Remove the specified slot.
     */
    public function destroy(Request $request, $id)
    {
        $slot = Slot::with('schedule')->findOrFail($id);

        if (AdminContext::isBranchAdmin() && (int)$slot->schedule->center_id !== (int)AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

        $slot->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa khung giờ thành công.'
            ]);
        }

        return redirect()->back()->with('success', 'Xóa khung giờ thành công.');
    }
}
