<?php
/**
 * Chức năng: DatabaseSeeder chính của dự án Laravel.
 * Lý do chỉnh sửa: Gọi thêm CenterSeeder, SettingSeeder và BannerSeeder của module VaccineRegistration để nạp dữ liệu động MySQL.
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed các bảng dữ liệu cho mô đun Tiêm chủng
        $this->call(\Modules\VaccineRegistration\Database\Seeders\VaccineSeeder::class);
        $this->call(\Modules\VaccineRegistration\Database\Seeders\CenterSeeder::class);
        $this->call(\Modules\VaccineRegistration\Database\Seeders\SettingSeeder::class);
        $this->call(\Modules\VaccineRegistration\Database\Seeders\BannerSeeder::class);
        $this->call(\Modules\VaccineRegistration\Database\Seeders\ArticleSeeder::class);
    }
}
