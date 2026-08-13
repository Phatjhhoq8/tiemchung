<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $keys = DB::table('settings')->where('key', 'like', 'loyalty_settings%')->get();
        foreach ($keys as $row) {
            $settings = json_decode($row->value, true);
            if (is_array($settings)) {
                $settings['vnd_per_earned_point'] = 1000;
                $settings['max_redeem_percent'] = 50;
                DB::table('settings')
                    ->where('id', $row->id)
                    ->update([
                        'value' => json_encode($settings, JSON_UNESCAPED_UNICODE),
                        'updated_at' => now()
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = DB::table('settings')->where('key', 'like', 'loyalty_settings%')->get();
        foreach ($keys as $row) {
            $settings = json_decode($row->value, true);
            if (is_array($settings)) {
                $settings['vnd_per_earned_point'] = 10000;
                DB::table('settings')
                    ->where('id', $row->id)
                    ->update([
                        'value' => json_encode($settings, JSON_UNESCAPED_UNICODE),
                        'updated_at' => now()
                    ]);
            }
        }
    }
};
