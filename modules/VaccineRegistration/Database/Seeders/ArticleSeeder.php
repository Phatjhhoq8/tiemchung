<?php
/**
 * Chức năng: ArticleSeeder nạp danh sách các bài viết kiến thức y tế & tin tức thật của Medicare Cờ Đỏ.
 * Lý do tạo: Nạp dữ liệu thực tế vào CSDL MySQL thay vì hardcode.
 */

namespace Modules\VaccineRegistration\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\VaccineRegistration\Models\Article;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Tại Sao Nên Tiêm Vắc Xin Cúm Mùa Hàng Năm Cho Cả Gia Đình?',
                'slug' => Str::slug('Tại Sao Nên Tiêm Vắc Xin Cúm Mùa Hàng Năm Cho Cả Gia Đình'),
                'summary' => 'Virus cúm biến đổi liên tục qua mỗi năm. Việc tiêm nhắc lại vắc xin cúm tứ giá Vaxigrip Tetra / Influvac Tetra giúp bảo vệ đường hô hấp tối ưu cho trẻ em và người lớn.',
                'content' => 'Bệnh cúm mùa là bệnh nhiễm trùng đường hô hấp cấp tính do virus cúm gây ra. Virus cúm có khả năng biến đổi antigenic hàng năm, vì vậy Tổ chức Y tế Thế giới (WHO) khuyến cáo mọi đối tượng từ 6 tháng tuổi trở lên nên tiêm vắc xin phòng cúm nhắc lại mỗi năm một lần.',
                'image' => '13. Vaxigrip Tetra.jpg',
                'category' => 'Khuyến cáo Y tế',
                'is_published' => true,
                'is_featured' => true,
                'views' => 1420,
            ],
            [
                'title' => 'Đã Có Vắc Xin Sốt Xuất Huyết Qdenga: Hướng Dẫn Chi Tiết Độ Tuổi & Lịch Tiêm',
                'slug' => Str::slug('Đã Có Vắc Xin Sốt Xuất Huyết Qdenga Hướng Dẫn Chi Tiết Độ Tuổi Lịch Tiêm'),
                'summary' => 'Lần đầu tiên Việt Nam đưa vào sử dụng vắc xin phòng bệnh Sốt xuất huyết Qdenga (Nhật Bản). Tìm hiểu phác đồ 2 mũi tiêm chuẩn y khoa tại Medicare Cờ Đỏ.',
                'content' => 'Vắc xin Qdenga do hãng dược phẩm Takeda (Nhật Bản) nghiên cứu và phát triển, giúp phòng ngừa cả 4 tuýp huyết thanh virus Dengue (DEN-1, DEN-2, DEN-3, DEN-4). Hiệu quả giảm nguy cơ mắc bệnh lên đến 80% và giảm tỷ lệ nhập viện do sốt xuất huyết nặng lên đến 90%.',
                'image' => '33. Qdenga.jfif',
                'category' => 'Vắc Xin Mới',
                'is_published' => true,
                'is_featured' => true,
                'views' => 2180,
            ],
            [
                'title' => 'Lịch Tiêm Chủng Mới Nhất Dành Cho Trẻ Từ 0 Đến 24 Tháng Tuổi',
                'slug' => Str::slug('Lịch Tiêm Chủng Mới Nhất Dành Cho Trẻ Từ 0 Đến 24 Tháng Tuổi'),
                'summary' => 'Tổng hợp đầy đủ các mũi tiêm bắt buộc và khuyến cáo quan trọng giúp trẻ xây dựng hệ miễn dịch vững chắc trong những năm đầu đời.',
                'content' => 'Trong 24 tháng đầu đời, hệ miễn dịch của trẻ còn rất non nớt. Việc tiêm chủng đầy đủ các mũi 6in1 (Hexaxim/Infanrix Hexa), Rota (Rotarix/Rotavin), Phế cầu (Prevenar 13/20), Cúm và Viêm não Nhật Bản đúng lịch đóng vai trò quyết định giúp trẻ lớn khôn khỏe mạnh.',
                'image' => '10. Prevenar 13.jpg',
                'category' => 'Chăm Sóc Bé',
                'is_published' => true,
                'is_featured' => true,
                'views' => 980,
            ],
        ];

        foreach ($articles as $articleData) {
            Article::updateOrCreate(
                ['slug' => $articleData['slug']],
                $articleData
            );
        }
    }
}
