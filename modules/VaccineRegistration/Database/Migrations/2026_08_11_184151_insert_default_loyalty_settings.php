<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!\Illuminate\Support\Facades\DB::table('settings')->where('key', 'loyalty_settings')->exists()) {
            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'key' => 'loyalty_settings',
                'value' => json_encode([
                    'enabled' => true,
                    'vnd_per_earned_point' => 10000,
                    'min_order_value_to_earn' => 0,
                    'min_order_value_to_redeem' => 0,
                    'point_expiry_months' => 0,
                    'redeem_value_type' => 'vnd',
                    'redeem_vnd_per_point' => 100,
                    'redeem_percent_bps_per_point' => 10,
                    'max_redeem_percent' => 50,
                    'max_redeem_amount' => null,
                    'birthday_multiplier' => 1.0,
                    'tiers' => [
                        ['name' => 'Bạc', 'min_points' => 100, 'multiplier' => 1.1],
                        ['name' => 'Vàng', 'min_points' => 500, 'multiplier' => 1.2],
                        ['name' => 'Kim Cương', 'min_points' => 1000, 'multiplier' => 1.5]
                    ],
                    'campaigns' => []
                ], JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tránh xóa cấu hình thực tế khi rollback
    }
};
