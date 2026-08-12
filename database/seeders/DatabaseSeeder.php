<?php
/**
 * Chức năng: DatabaseSeeder chính của dự án Laravel.
 * Lý do chỉnh sửa: Chỉ nạp dữ liệu khởi tạo chung; danh mục vaccine do quản trị viên nhập và duy trì.
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Concerns\PreventsProductionSeeding;

class DatabaseSeeder extends Seeder
{
    use PreventsProductionSeeding;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->assertSafeSeedingTarget();

        $testDataPath = database_path('seeders/test_data.sql');
        if (file_exists($testDataPath)) {
            // Nạp dữ liệu test thực tế đã lưu
            $this->command->info('Loading custom test data from test_data.sql...');
            \Illuminate\Support\Facades\DB::unprepared(file_get_contents($testDataPath));
        } else {
            // Fallback chạy các seeders mặc định
            $this->call(\Modules\VaccineRegistration\Database\Seeders\CenterSeeder::class);
            $this->call(\Modules\VaccineRegistration\Database\Seeders\ScheduleSeeder::class);
            $this->call(\Modules\VaccineRegistration\Database\Seeders\SettingSeeder::class);
            $this->call(\Modules\VaccineRegistration\Database\Seeders\BannerSeeder::class);
            $this->call(\Modules\VaccineRegistration\Database\Seeders\ArticleSeeder::class);
        }
    }
}
