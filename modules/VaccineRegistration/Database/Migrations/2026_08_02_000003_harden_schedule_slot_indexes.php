<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('schedules')
            ->select('center_id', 'date', DB::raw('MIN(id) as keep_id'))
            ->groupBy('center_id', 'date')
            ->havingRaw('COUNT(*) > 1')
            ->orderBy('keep_id')
            ->get()
            ->each(function ($duplicate) {
                $duplicateIds = DB::table('schedules')
                    ->where('center_id', $duplicate->center_id)
                    ->where('date', $duplicate->date)
                    ->where('id', '!=', $duplicate->keep_id)
                    ->pluck('id');

                DB::table('slots')->whereIn('schedule_id', $duplicateIds)->update(['schedule_id' => $duplicate->keep_id]);
                DB::table('schedules')->whereIn('id', $duplicateIds)->delete();
            });

        if (!$this->hasIndex('schedules', 'schedules_center_id_date_is_active_index')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->index(['center_id', 'date', 'is_active']);
            });
        }

        if (!$this->hasIndex('schedules', 'schedules_center_id_date_unique')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->unique(['center_id', 'date']);
            });
        }

        if (!$this->hasIndex('slots', 'slots_schedule_id_is_active_index')) {
            Schema::table('slots', function (Blueprint $table) {
                $table->index(['schedule_id', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        if ($this->hasIndex('slots', 'slots_schedule_id_is_active_index')) {
            Schema::table('slots', function (Blueprint $table) {
                $table->dropIndex(['schedule_id', 'is_active']);
            });
        }

        if ($this->hasIndex('schedules', 'schedules_center_id_date_unique')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->dropUnique(['center_id', 'date']);
            });
        }

        if ($this->hasIndex('schedules', 'schedules_center_id_date_is_active_index')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->dropIndex(['center_id', 'date', 'is_active']);
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
