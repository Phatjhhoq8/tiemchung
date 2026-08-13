<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function run(): void
    {
        // Thêm key footer_working_hours vào settings
        DB::table('settings')->insertOrIgnore([
            [
                'key' => 'footer_working_hours',
                'value' => 'Mở cửa 7:30 – 17:00 (không nghỉ trưa)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'footer_working_hours_draft',
                'value' => 'Mở cửa 7:30 – 17:00 (không nghỉ trưa)',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')
            ->whereIn('key', ['footer_working_hours', 'footer_working_hours_draft'])
            ->delete();
    }
};
