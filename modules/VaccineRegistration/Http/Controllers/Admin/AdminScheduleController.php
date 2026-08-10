<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Schedule;
use Modules\VaccineRegistration\Models\Slot;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminScheduleController extends Controller
{
    /**
     * Display a 7-day weekly grid listing of schedules.
     */
    public function index(Request $request)
    {
        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get(['id', 'name']);
        $selectedCenterId = AdminContext::resolveListCenterId($request);

        $pivotDate = $request->filled('date')
            ? Carbon::parse($request->input('date'))
            : ($request->filled('week_start') ? Carbon::parse($request->input('week_start')) : now());

        $weekStart = $pivotDate->copy()->startOfWeek();
        $weekEnd = $pivotDate->copy()->endOfWeek();

        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = $weekStart->copy()->addDays($i)->toDateString();
        }

        $schedules = collect();
        if ($selectedCenterId) {
            Schedule::generateFromDefaults($selectedCenterId, $weekStart->toDateString(), $weekEnd->toDateString());

            $schedules = Schedule::with(['center', 'slots' => fn ($q) => $q->orderBy('start_at')])
                ->where('center_id', $selectedCenterId)
                ->whereIn('date', $dates)
                ->get()
                ->keyBy(fn ($item) => $item->date->format('Y-m-d'));
        }

        $dayNames = [
            1 => 'Thứ 2',
            2 => 'Thứ 3',
            3 => 'Thứ 4',
            4 => 'Thứ 5',
            5 => 'Thứ 6',
            6 => 'Thứ 7',
            0 => 'Chủ Nhật',
        ];

        $weekGrid = [];
        foreach ($dates as $dateStr) {
            $cDate = Carbon::parse($dateStr);
            $schedule = $schedules->get($dateStr);
            $slots = $schedule ? $schedule->slots : collect();

            $totalCapacity = $slots->sum('capacity');
            $totalReserved = $slots->sum('reserved_count');
            $isActive = $schedule ? (bool) $schedule->is_active : true;

            $weekGrid[] = [
                'date' => $dateStr,
                'formatted_date' => $cDate->format('d/m/Y'),
                'day_name' => $dayNames[$cDate->dayOfWeek] ?? '',
                'schedule' => $schedule,
                'schedule_id' => $schedule?->id,
                'is_active' => $isActive,
                'total_capacity' => $totalCapacity,
                'total_reserved' => $totalReserved,
                'slots' => $slots,
            ];
        }

        $prevWeekDate = $weekStart->copy()->subWeek()->toDateString();
        $nextWeekDate = $weekStart->copy()->addWeek()->toDateString();
        $currentWeekDate = now()->startOfWeek()->toDateString();
        $headerRange = $weekStart->format('d/m/Y') . ' - ' . $weekEnd->format('d/m/Y');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'week_start' => $weekStart->toDateString(),
                    'week_end' => $weekEnd->toDateString(),
                    'prev_week' => $prevWeekDate,
                    'next_week' => $nextWeekDate,
                    'current_week' => $currentWeekDate,
                    'header_range' => $headerRange,
                    'selected_center_id' => $selectedCenterId,
                    'week_grid' => $weekGrid,
                ],
            ]);
        }

        return view('vaccine::admin.schedules.index', compact(
            'weekGrid',
            'centers',
            'selectedCenterId',
            'weekStart',
            'weekEnd',
            'prevWeekDate',
            'nextWeekDate',
            'currentWeekDate',
            'headerRange'
        ));
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

        AdminContext::assertCanManageCenter((int) $validated['center_id']);

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
                'schedule' => $schedule,
            ], 201);
        }

        return redirect()->route('admin.schedules.index')->with('success', 'Tạo lịch làm việc thành công.');
    }

    /**
     * Copy schedule from source day to target days.
     * Blocks copy if any target date has existing bookings (reserved_count > 0 or linked registrations).
     */
    public function copySchedule(Request $request)
    {
        $validated = $request->validate([
            'center_id' => 'required|exists:centers,id',
            'source_date' => 'required|date',
            'target_dates' => 'required|array|min:1',
            'target_dates.*' => 'required|date|different:source_date',
        ]);

        AdminContext::assertCanManageCenter((int) $validated['center_id']);

        $sourceSchedule = Schedule::with('slots')
            ->where('center_id', $validated['center_id'])
            ->where('date', $validated['source_date'])
            ->first();

        if (!$sourceSchedule || $sourceSchedule->slots->isEmpty()) {
            throw ValidationException::withMessages([
                'source_date' => 'Lịch ngày nguồn không tồn tại hoặc không có khung giờ nào.'
            ]);
        }

        // SAFETY GUARD: Check if any target date has slots with reserved_count > 0 or linked Registration records
        $targetSchedules = Schedule::with('slots')
            ->where('center_id', $validated['center_id'])
            ->whereIn('date', $validated['target_dates'])
            ->get()
            ->keyBy(fn ($item) => $item->date->format('Y-m-d'));

        foreach ($validated['target_dates'] as $targetDate) {
            $targetSched = $targetSchedules->get($targetDate);
            if ($targetSched) {
                $reservedCount = $targetSched->slots->sum('reserved_count');
                $slotIds = $targetSched->slots->pluck('id');
                $registrationCount = Registration::whereIn('slot_id', $slotIds)->count();
                $totalBookings = max($reservedCount, $registrationCount);

                if ($totalBookings > 0) {
                    $formattedDate = Carbon::parse($targetDate)->format('d/m/Y');
                    throw ValidationException::withMessages([
                        'target_dates' => "Không thể sao chép đè lịch ngày {$formattedDate} vì đã có {$totalBookings} lượt đặt tiêm!"
                    ]);
                }
            }
        }

        DB::transaction(function () use ($validated, $sourceSchedule) {
            foreach ($validated['target_dates'] as $targetDate) {
                $targetSchedule = Schedule::updateOrCreate(
                    [
                        'center_id' => $validated['center_id'],
                        'date' => $targetDate,
                    ],
                    [
                        'is_active' => $sourceSchedule->is_active,
                        'note' => $sourceSchedule->note,
                    ]
                );

                $targetSchedule->slots()->delete();

                foreach ($sourceSchedule->slots as $sourceSlot) {
                    $targetSchedule->slots()->create([
                        'start_at' => $sourceSlot->start_at,
                        'end_at' => $sourceSlot->end_at,
                        'capacity' => $sourceSlot->capacity,
                        'reserved_count' => 0,
                        'is_active' => $sourceSlot->is_active,
                    ]);
                }
            }
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sao chép lịch làm việc thành công.'
            ]);
        }

        return redirect()->back()->with('success', 'Sao chép lịch làm việc thành công.');
    }

    /**
     * Toggle active status for a specific schedule date.
     */
    public function toggleDayStatus(Request $request)
    {
        $validated = $request->validate([
            'center_id' => 'required|exists:centers,id',
            'date' => 'required|date',
            'is_active' => 'required|boolean',
        ]);

        AdminContext::assertCanManageCenter((int) $validated['center_id']);

        $schedule = Schedule::firstOrCreate(
            ['center_id' => $validated['center_id'], 'date' => $validated['date']],
            ['is_active' => true]
        );

        $schedule->is_active = (bool) $validated['is_active'];
        $schedule->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái ngày làm việc thành công.',
                'is_active' => $schedule->is_active,
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái ngày làm việc thành công.');
    }

    /**
     * Delete entire schedule and slots for a specific date if no bookings exist.
     */
    public function destroyDay(Request $request)
    {
        $validated = $request->validate([
            'center_id' => 'required|exists:centers,id',
            'date' => 'required|date',
        ]);

        AdminContext::assertCanManageCenter((int) $validated['center_id']);

        $schedule = Schedule::with('slots')
            ->where('center_id', $validated['center_id'])
            ->where('date', $validated['date'])
            ->first();

        if ($schedule) {
            $reservedCount = $schedule->slots->sum('reserved_count');
            $slotIds = $schedule->slots->pluck('id');
            $registrationCount = Registration::whereIn('slot_id', $slotIds)->count();
            $totalBookings = max($reservedCount, $registrationCount);

            if ($totalBookings > 0) {
                $formattedDate = Carbon::parse($validated['date'])->format('d/m/Y');
                throw ValidationException::withMessages([
                    'date' => "Không thể xóa lịch ngày {$formattedDate} vì đã có {$totalBookings} lượt đặt tiêm!"
                ]);
            }

            $schedule->slots()->delete();
            $schedule->delete();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa lịch ngày thành công.'
            ]);
        }

        return redirect()->back()->with('success', 'Xóa lịch ngày thành công.');
    }

    /**
     * Display the specified schedule.
     */
    public function show(Request $request, $id)
    {
        $schedule = Schedule::with(['center', 'slots'])->findOrFail($id);

        AdminContext::assertCanManageCenter((int) $schedule->center_id);

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

        AdminContext::assertCanManageCenter((int) $schedule->center_id);

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
        $schedule = Schedule::with('slots')->findOrFail($id);

        AdminContext::assertCanManageCenter((int) $schedule->center_id);

        $reservedCount = $schedule->slots->sum('reserved_count');
        $slotIds = $schedule->slots->pluck('id');
        $registrationCount = Registration::whereIn('slot_id', $slotIds)->count();
        $totalBookings = max($reservedCount, $registrationCount);

        if ($totalBookings > 0) {
            $formattedDate = $schedule->date->format('d/m/Y');
            throw ValidationException::withMessages([
                'schedule' => "Không thể xóa lịch ngày {$formattedDate} vì đã có {$totalBookings} lượt đặt tiêm!"
            ]);
        }

        $schedule->slots()->delete();
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

        AdminContext::assertCanManageCenter((int) $schedule->center_id);

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
