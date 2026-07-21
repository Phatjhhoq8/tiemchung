<?php
/**
 * Chức năng: CenterSeeder nạp danh sách 2 chi nhánh trung tâm tiêm chủng chính thức thuộc hệ thống Medicare.
 * Lý do chỉnh sửa: Quản lý 2 chi nhánh hoạt động thực tế theo đúng yêu cầu hệ thống Medicare.
 */

namespace Modules\VaccineRegistration\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\VaccineRegistration\Models\Center;

class CenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $centers = [
            [
                'name' => 'Medicare Cờ Đỏ (Chi nhánh 1)',
                'address' => 'Cổng Bệnh viện Quân Dân Y TP Cần Thơ, Ấp Thới Bình, Xã Cờ Đỏ, TP. Cần Thơ',
                'phone' => '0938 60 38 39',
                'is_active' => true,
            ],
            [
                'name' => 'Medicare Thới Lai (Chi nhánh 2)',
                'address' => 'Thị trấn Thới Lai, Huyện Thới Lai, TP. Cần Thơ',
                'phone' => '0932 477 184',
                'is_active' => true,
            ],
        ];

        foreach ($centers as $center) {
            Center::updateOrCreate(['name' => $center['name']], $center);
        }
    }
}
