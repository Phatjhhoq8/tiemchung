<?php

namespace Modules\VaccineRegistration\Database\Seeders;

use Database\Seeders\Concerns\PreventsProductionSeeding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\VaccineRegistration\Models\Center;

class ScheduleSeeder extends Seeder
{
    use PreventsProductionSeeding;

    public function run(): void
    {
        $this->assertSafeSeedingTarget();

        $slots = [
            ['start_at' => '08:00', 'end_at' => '09:00', 'capacity' => 12],
            ['start_at' => '09:30', 'end_at' => '10:30', 'capacity' => 12],
            ['start_at' => '14:00', 'end_at' => '15:00', 'capacity' => 12],
        ];

        $dates = array_map(fn (int $offset) => today()->addDays($offset)->toDateString(), range(1, 14));
        $timestamp = now();

        Center::active()->select('id')->orderBy('id')->chunkById(100, function ($centers) use ($dates, $slots, $timestamp) {
            $centerIds = $centers->pluck('id')->all();
            $scheduleRows = [];

            foreach ($centerIds as $centerId) {
                foreach ($dates as $date) {
                    $scheduleRows[] = [
                        'center_id' => $centerId,
                        'date' => $date,
                        'is_active' => true,
                        'note' => null,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }
            }

            DB::table('schedules')->insertOrIgnore($scheduleRows);

            $scheduleIds = DB::table('schedules')
                ->whereIn('center_id', $centerIds)
                ->whereIn('date', $dates)
                ->pluck('id');
            $slotRows = [];

            foreach ($scheduleIds as $scheduleId) {
                foreach ($slots as $slot) {
                    $slotRows[] = [
                        'schedule_id' => $scheduleId,
                        'start_at' => $slot['start_at'],
                        'end_at' => $slot['end_at'],
                        'capacity' => $slot['capacity'],
                        'reserved_count' => 0,
                        'is_active' => true,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }
            }

            DB::table('slots')->insertOrIgnore($slotRows);
        });
    }
}
