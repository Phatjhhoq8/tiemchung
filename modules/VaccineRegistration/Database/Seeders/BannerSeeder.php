<?php
/**
 * Chức năng: BannerSeeder nạp danh sách slider trang chủ Medicare Cờ Đỏ.
 * Lý do tạo: Tự động khởi tạo dữ liệu slider động cho website.
 */

namespace Modules\VaccineRegistration\Database\Seeders;

use Database\Seeders\Concerns\PreventsProductionSeeding;
use Illuminate\Database\Seeder;
use Modules\VaccineRegistration\Models\Banner;

class BannerSeeder extends Seeder
{
    use PreventsProductionSeeding;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->assertSafeSeedingTarget();

        $banners = [
            [
                'title' => 'Đăng Ký Tiêm Chủng Trực Tuyến Dễ Dàng',
                'subtitle' => 'Lựa chọn vắc xin linh hoạt, đăng ký tiêm chủng an toàn tại các trung tâm Medicare Cờ Đỏ.',
                'image_url' => 'images/banners/banner_family.jpg',
                'link_url' => '/vaccines',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Vắc Xin Sốt Xuất Huyết Qdenga Mới Nhất',
                'subtitle' => 'Đã có sẵn tại Medicare Cờ Đỏ. Bảo vệ trẻ em từ 4 tuổi và người lớn khỏi virus Dengue nguy hiểm.',
                'image_url' => 'images/banners/banner1.jpg',
                'link_url' => '/vaccines',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Bảo Vệ Người Cao Tuổi & Người Có Bệnh Nền',
                'subtitle' => 'Chủ động tiêm phòng Cúm mùa, Phế cầu khuẩn và Zona thần kinh để củng cố hệ miễn dịch.',
                'image_url' => 'images/banners/banner2.jpg',
                'link_url' => '/vaccines',
                'sort_order' => 3,
                'is_active' => true,
            ]
        ];

        foreach ($banners as $banner) {
            Banner::updateOrCreate(['title' => $banner['title']], $banner);
        }
    }
}
