<?php
/**
 * Chức năng: CenterSeeder nạp danh sách trung tâm tiêm chủng Medicare Cờ Đỏ.
 * Lý do tạo: Tự động khởi tạo dữ liệu trung tâm động để khách hàng chọn khi đăng ký.
 */

namespace Modules\VaccineRegistration\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\VaccineRegistration\Models\Center;

class CenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Center::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $centers = [
            [
                'name' => 'Medicare Cờ Đỏ (Trụ sở chính)',
                'address' => 'Ấp Thới Hòa, Thị trấn Cờ Đỏ, Huyện Cờ Đỏ, TP. Cần Thơ',
                'phone' => '0938603839',
                'is_active' => true,
            ],
            [
                'name' => 'Medicare Thới Lai',
                'address' => 'Thị trấn Thới Lai, Huyện Thới Lai, TP. Cần Thơ',
                'phone' => '0932477184',
                'is_active' => true,
            ],
            [
                'name' => 'Medicare Ô Môn',
                'address' => 'Đường 26 Tháng 3, Phường Châu Văn Liêm, Quận Ô Môn, TP. Cần Thơ',
                'phone' => '0938603839',
                'is_active' => true,
            ],
            [
                'name' => 'Medicare Vĩnh Thạnh',
                'address' => 'Thị trấn Vĩnh Thạnh, Huyện Vĩnh Thạnh, TP. Cần Thơ',
                'phone' => '0932477184',
                'is_active' => true,
            ],
            [
                'name' => 'Medicare Ninh Kiều',
                'address' => 'Đường Nguyễn Văn Cừ, Phường An Khánh, Quận Ninh Kiều, TP. Cần Thơ',
                'phone' => '0938603839',
                'is_active' => true,
            ],
            [
                'name' => 'Medicare Cái Răng',
                'address' => 'Phường Hưng Thạnh, Quận Cái Răng, TP. Cần Thơ',
                'phone' => '0932477184',
                'is_active' => true,
            ]
        ];

        foreach ($centers as $center) {
            Center::create($center);
        }
    }
}
