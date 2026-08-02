<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Schedule;
use Modules\VaccineRegistration\Models\Slot;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminScheduleController extends Controller
{
    /**
     * Display a listing of schedules.
     */
    public function index(Request $request)
    {
        $query = Schedule::with(['center', 'slots']);
        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get(['id', 'name']);
        $selectedCenterId = AdminContext::isBranchAdmin()
            ? AdminContext::centerId()
            : ($request->filled('center_id') ? $request->integer('center_id') : AdminContext::selectedCenterId());

        if (AdminContext::isBranchAdmin()) {
            $query->where('center_id', AdminContext::centerId());
        } elseif ($selectedCenterId) {
            $query->where('center_id', $selectedCenterId);
        }

        if ($request->filled('date')) {
            $query->where('date', $request->input('date'));
        }

        $schedules = $query->latest('date')->paginate(15);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $schedules
            ]);
        }

        return view('vaccine::admin.schedules.index', compact('schedules', 'centers', 'selectedCenterId'));
    }

    /**
     * Store a newly created schedule in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'center_id' => 'required|exists:centers,id',
            'date' => 'required|date|after_or_equal:today',
            'note' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'slots' => 'required|array|min:1',
            'slots.*.start_at' => 'required|date_format:H:i',
            'slots.*.end_at' => 'required|date_format:H:i',
            'slots.*.capacity' => 'required|integer|min:1',
            'slots.*.is_active' => 'nullable|boolean',
        ]);

        if (AdminContext::isBranchAdmin() && (int)$validated['center_id'] !== (int)AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

        foreach ($validated['slots'] ?? [] as $slot) {
            if ($slot['end_at'] <= $slot['start_at']) {
                return back()->withErrors(['slots' => 'Giờ kết thúc phải sau giờ bắt đầu.'])->withInput();
            }
        }

        $schedule = Schedule::updateOrCreate([
            'center_id' => $validated['center_id'],
            'date' => $validated['date'],
        ], [
            'note' => $validated['note'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (!empty($validated['slots'])) {
            foreach ($validated['slots'] as $slotData) {
                $schedule->slots()->firstOrCreate([
                    'start_at' => $slotData['start_at'],
                    'end_at' => $slotData['end_at'],
                ], [
                    'capacity' => $slotData['capacity'],
                    'reserved_count' => 0,
                    'is_active' => $slotData['is_active'] ?? true,
                ]);
            }
        }

        $schedule->load('slots');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tạo lịch làm việc thành công.',
                'schedule' => $schedule
            ], 201);
        }

        return redirect()->route('admin.schedules.index')->with('success', 'Tạo lịch làm việc thành công.');
    }

    /**
     * Display the specified schedule.
     */
    public function show(Request $request, $id)
    {
        $schedule = Schedule::with(['center', 'slots'])->findOrFail($id);

        if (AdminContext::isBranchAdmin() && (int)$schedule->center_id !== (int)AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'schedule' => $schedule
            ]);
        }

        return view('vaccine::admin.schedules.show', compact('schedule'));
    }

    /**
     * Update the specified schedule in storage.
     */
    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        if (AdminContext::isBranchAdmin() && (int)$schedule->center_id !== (int)AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

        $validated = $request->validate([
            'date' => 'sometimes|required|date',
            'note' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $schedule->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật lịch làm việc thành công.',
                'schedule' => $schedule
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật lịch làm việc thành công.');
    }

    /**
     * Remove the specified schedule from storage.
     */
    public function destroy(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        if (AdminContext::isBranchAdmin() && (int)$schedule->center_id !== (int)AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

        $schedule->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa lịch làm việc thành công.'
            ]);
        }

        return redirect()->route('admin.schedules.index')->with('success', 'Xóa lịch làm việc thành công.');
    }

    /**
     * Add a slot to a schedule.
     */
    public function storeSlot(Request $request, $scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);

        if (AdminContext::isBranchAdmin() && (int)$schedule->center_id !== (int)AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

        $validated = $request->validate([
            'start_at' => 'required|string',
            'end_at' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $slot = $schedule->slots()->create([
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'capacity' => $validated['capacity'],
            'reserved_count' => 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm khung giờ thành công.',
                'slot' => $slot
            ], 201);
        }

        return redirect()->back()->with('success', 'Thêm khung giờ thành công.');
    }
}
