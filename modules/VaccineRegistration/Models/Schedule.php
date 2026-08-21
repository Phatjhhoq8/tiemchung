<?php

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'center_id',
        'date',
        'is_active',
        'note',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'is_active' => 'boolean',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }

    public static function generateFromDefaults($centerId, $startDate, $endDate)
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        $defaultSlots = DefaultSlot::where('center_id', $centerId)
            ->where('is_active', true)
            ->get()
            ->groupBy('day_of_week');

        if ($defaultSlots->isEmpty()) {
            return;
        }

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dayOfWeek = $date->dayOfWeekIso; // 1 = Thứ 2, ..., 7 = Chủ nhật
            
            if ($defaultSlots->has($dayOfWeek)) {
                $schedule = self::firstOrCreate([
                    'center_id' => $centerId,
                    'date' => $date->toDateString(),
                ], [
                    'is_active' => true,
                    'note' => null,
                ]);

                if ($schedule->wasRecentlyCreated) {
                    foreach ($defaultSlots[$dayOfWeek] as $defaultSlot) {
                        $schedule->slots()->create([
                            'start_at' => $defaultSlot->start_at,
                            'end_at' => $defaultSlot->end_at,
                            'capacity' => $defaultSlot->capacity,
                            'reserved_count' => 0,
                            'is_active' => $defaultSlot->is_active,
                        ]);
                    }
                }
            }
        }
    }
}

