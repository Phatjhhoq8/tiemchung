<?php
/**
 * Chức năng: SettingSeeder nạp cấu hình hệ thống thương hiệu Medicare quản lý chuỗi nhiều chi nhánh.
 * Lý do chỉnh sửa: Thay địa chỉ trụ sở chính đơn lẻ thành danh sách các chi nhánh trụ sở trong hệ thống.
 */

namespace Modules\VaccineRegistration\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\VaccineRegistration\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'site_name' => 'Medicare',
            'brand_title' => 'Hệ Thống Tiêm Chủng Medicare',
            'hotline' => '0938 60 38 39',
            'hotline_2' => '0932 477 184',
            'email' => 'cskh@medicare.vn',
            'address' => 'Chi nhánh 1: Cờ Đỏ (Cổng BV Quân Dân Y TP Cần Thơ) | Chi nhánh 2: Thới Lai (Thị trấn Thới Lai, TP Cần Thơ)',
            'working_hours' => 'Tất cả các ngày trong tuần (Từ 7:00 - 17:00 kể cả Chủ Nhật và ngày Lễ)',
            'footer_text' => '© 2026 Medicare - Hệ Thống Tiêm Chủng Vắc Xin Trẻ Em và Người Lớn với chuỗi chi nhánh phục vụ tận tâm.',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
