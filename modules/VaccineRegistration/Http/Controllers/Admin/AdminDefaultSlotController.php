<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\DefaultSlot;
use Modules\VaccineRegistration\Support\AdminContext;
use Illuminate\Support\Facades\DB;

class AdminDefaultSlotController extends Controller
{
    /**
     * Display default slots configuration view.
     */
    public function index(Request $request)
    {
        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get(['id', 'name']);
        
        // Resolve center context
        $selectedCenterId = AdminContext::resolveListCenterId($request);
        if (!$selectedCenterId && $centers->isNotEmpty()) {
            $selectedCenterId = $centers->first()->id;
        }

        if ($selectedCenterId) {
            AdminContext::assertCanManageCenter((int) $selectedCenterId);
        }

        // Fetch default slots grouped by day_of_week
        $defaultSlots = DefaultSlot::where('center_id', $selectedCenterId)
            ->orderBy('day_of_week')
            ->orderBy('start_at')
            ->get()
            ->groupBy('day_of_week');

        return view('vaccine::admin.schedules.default', compact('centers', 'selectedCenterId', 'defaultSlots'));
    }

    /**
     * Update default slots for a specific day of week and center.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'center_id' => 'required|exists:centers,id',
            'day_of_week' => 'required|integer|min:1|max:7',
            'slots' => 'nullable|array',
            'slots.*.start_at' => 'required|date_format:H:i',
            'slots.*.end_at' => 'required|date_format:H:i',
            'slots.*.capacity' => 'required|integer|min:1',
            'slots.*.is_active' => 'nullable|boolean',
        ]);

        $centerId = (int) $validated['center_id'];
        $dayOfWeek = (int) $validated['day_of_week'];

        AdminContext::assertCanManageCenter($centerId);

        // Check if end_at is after start_at
        if (!empty($validated['slots'])) {
            foreach ($validated['slots'] as $slot) {
                if ($slot['end_at'] <= $slot['start_at']) {
                    return back()->withErrors(['slots' => 'Giờ kết thúc phải sau giờ bắt đầu.'])->withInput();
                }
            }
        }

        DB::transaction(function () use ($centerId, $dayOfWeek, $validated) {
            // Delete old default slots for this day and center
            DefaultSlot::where('center_id', $centerId)
                ->where('day_of_week', $dayOfWeek)
                ->delete();

            // Insert new default slots
            if (!empty($validated['slots'])) {
                foreach ($validated['slots'] as $slotData) {
                    DefaultSlot::create([
                        'center_id' => $centerId,
                        'day_of_week' => $dayOfWeek,
                        'start_at' => $slotData['start_at'],
                        'end_at' => $slotData['end_at'],
                        'capacity' => $slotData['capacity'],
                        'is_active' => isset($slotData['is_active']) ? (bool) $slotData['is_active'] : true,
                    ]);
                }
            }
        });

        return redirect()->route('admin.default-slots.index', ['center_id' => $centerId, 'tab' => $dayOfWeek])
            ->with('success', 'Cập nhật cấu hình khung giờ mặc định thành công.');
    }
}
