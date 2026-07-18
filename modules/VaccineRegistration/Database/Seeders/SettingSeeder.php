<?php
/**
 * Chức năng: SettingSeeder nạp cấu hình hệ thống mặc định của phòng tiêm chủng.
 * Lý do tạo: Tự động khởi tạo dữ liệu cấu hình key-value cho website.
 */

namespace Modules\VaccineRegistration\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\VaccineRegistration\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Setting::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $settings = [
            ['key' => 'site_name', 'value' => 'Medicare Cờ Đỏ'],
            ['key' => 'hotline', 'value' => '0938 60 38 39'],
            ['key' => 'hotline_2', 'value' => '0932 477 184'],
            ['key' => 'email', 'value' => 'cskh@medicarecodo.vn'],
            ['key' => 'address', 'value' => 'Ấp Thới Hòa, Thị trấn Cờ Đỏ, Huyện Cờ Đỏ, TP. Cần Thơ'],
            ['key' => 'footer_text' , 'value' => '© 2026 Phòng tiêm chủng vắc xin Medicare Cờ Đỏ. Đảm bảo an toàn - chất lượng hàng đầu.'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
