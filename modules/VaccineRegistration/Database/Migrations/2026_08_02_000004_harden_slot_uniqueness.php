<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('slots')
            ->select('schedule_id', 'start_at', 'end_at', DB::raw('MIN(id) as keep_id'))
            ->groupBy('schedule_id', 'start_at', 'end_at')
            ->havingRaw('COUNT(*) > 1')
            ->orderBy('keep_id')
            ->get()
            ->each(function ($duplicate) {
                $slots = DB::table('slots')
                    ->where('schedule_id', $duplicate->schedule_id)
                    ->where('start_at', $duplicate->start_at)
                    ->where('end_at', $duplicate->end_at)
                    ->orderBy('id')
                    ->get();

                $duplicateIds = $slots->pluck('id')->reject(fn ($id) => (int) $id === (int) $duplicate->keep_id);
                if ($duplicateIds->isEmpty()) {
                    return;
                }

                $reservedCount = (int) $slots->sum('reserved_count');
                $capacity = max((int) $slots->max('capacity'), $reservedCount);

                DB::table('registrations')->whereIn('slot_id', $duplicateIds)->update(['slot_id' => $duplicate->keep_id]);
                DB::table('slots')->where('id', $duplicate->keep_id)->update([
                    'capacity' => $capacity,
                    'reserved_count' => $reservedCount,
                    'is_active' => $slots->contains(fn ($slot) => (bool) $slot->is_active),
                ]);
                DB::table('slots')->whereIn('id', $duplicateIds)->delete();
            });

        if (!$this->hasIndex('slots', 'slots_schedule_time_unique')) {
            Schema::table('slots', function (Blueprint $table) {
                $table->unique(['schedule_id', 'start_at', 'end_at'], 'slots_schedule_time_unique');
            });
        }
    }

    public function down(): void
    {
        if ($this->hasIndex('slots', 'slots_schedule_time_unique')) {
            Schema::table('slots', function (Blueprint $table) {
                $table->dropUnique('slots_schedule_time_unique');
            });
        }
    }

    private function hasIndex(string $table, string $name): bool
    {
        return collect(Schema::getIndexes($table))->contains(
            fn (array $index) => $index['name'] === $name
        );
    }
};
