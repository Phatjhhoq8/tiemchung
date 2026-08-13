<?php
/**
 * Chức năng: SettingSeeder nạp cấu hình hệ thống thương hiệu Medicare quản lý chuỗi nhiều chi nhánh.
 * Lý do chỉnh sửa: Thay một địa chỉ trụ sở chính thành danh sách các chi nhánh trong hệ thống.
 */

namespace Modules\VaccineRegistration\Database\Seeders;

use Database\Seeders\Concerns\PreventsProductionSeeding;
use Illuminate\Database\Seeder;
use Modules\VaccineRegistration\Models\Setting;

class SettingSeeder extends Seeder
{
    use PreventsProductionSeeding;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->assertSafeSeedingTarget();

        $fields = \Modules\VaccineRegistration\Support\SiteContentRegistry::getFields();

        foreach ($fields as $key => $meta) {
            $value = $meta['default'];

            // Khởi tạo key chính thức
            Setting::firstOrCreate(
                ['key' => $key],
                ['value' => $value]
            );

            // Khởi tạo key nháp
            Setting::firstOrCreate(
                ['key' => $key . '_draft'],
                ['value' => $value]
            );
        }

        // Khởi tạo cấu hình loyalty mặc định nếu chưa có
        $loyaltySettings = json_encode([
            'enabled' => true,
            'vnd_per_earned_point' => 1000,
            'min_order_value_to_earn' => 0,
            'min_order_value_to_redeem' => 0,
            'point_expiry_months' => 0,
            'redeem_value_type' => 'vnd',
            'redeem_vnd_per_point' => 1,
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
        ], JSON_UNESCAPED_UNICODE);

        Setting::firstOrCreate(
            ['key' => 'loyalty_settings'],
            ['value' => $loyaltySettings]
        );
    }
}
