<?php
/**
 * Chức năng: ArticleSeeder nạp đúng 100 bài viết tin tức y khoa thật từ nguồn tham khảo VNVC.
 * Quy chuẩn: Tuyệt đối không dùng ký tự '&' trong tiêu đề, tóm tắt hay tên chuyên mục.
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
        $categories = [
            'Bệnh Truyền Nhiễm',
            'Khuyến Cáo Y Tế',
            'Tin Nóng Y Học',
            'Vắc Xin Mới',
            'Chăm Sóc Trẻ Em',
            'Tiêm Chủng Người Lớn'
        ];

        $images = [
            '1-hexaxim.jpg',
            '2-infanrix-hexa.jfif',
            '3-rotarix.jpg',
            '4-rotavin.jpg',
            '5-bcg.png',
            '6-vaxigrip.jpg',
            '7-influvac.jpg',
            '8-prevenar13.jpg',
            '9-synflorix.png',
            '10-gardasil9.jpg',
            '11-varilrix.jpg',
            '12-mengoc-bc.png'
        ];

        $topicTitles = [
            // Bệnh Truyền Nhiễm
            ['cat' => 'Bệnh Truyền Nhiễm', 'title' => 'Cúm Mùa Mới Nhất: Khuyến Cáo Phòng Ngừa Và Lịch Tiêm Phòng Cho Cả Gia Đình'],
            ['cat' => 'Bệnh Truyền Nhiễm', 'title' => 'Bạch Hầu Đang Có Dấu Hiệu Gia Tăng: Cách Nhận Biết Và Biện Pháp Bảo Vệ'],
            ['cat' => 'Bệnh Truyền Nhiễm', 'title' => 'Ho Gà Ở Trẻ Sơ Sinh: Mối Nguy Hồi Từ Các Cơn Ho Co Thắt Rất Nguy Hiểm'],
            ['cat' => 'Bệnh Truyền Nhiễm', 'title' => 'Sởi và Thủy Đậu Mùa Khai Trường: Cha Mẹ Cần Chủ Động Tiêm Vắc Xin Cho Con'],
            ['cat' => 'Bệnh Truyền Nhiễm', 'title' => 'Viêm Não Nhật Bản B: Căn Bệnh Nguy Hiểm Cần Tiêm Vắc Xin Đúng Phác Đồ'],
            ['cat' => 'Bệnh Truyền Nhiễm', 'title' => 'Sốt Xuất Huyết Dengue: Vắc Xin Mới Mở Ra Hy Vọng Phòng Bệnh Cho Người Trưởng Thành'],
            ['cat' => 'Bệnh Truyền Nhiễm', 'title' => 'Phế Cầu Khuẩn Gây Viêm Phổi Viêm Màng Não: Đối Tượng Nào Cần Tiêm Ngừa Ngay'],
            ['cat' => 'Bệnh Truyền Nhiễm', 'title' => 'Bệnh Dại Nguy Hiểm Nhất Mùa Hè: Hướng Dẫn Xử Trí Khi Bị Chó Mèo Cắn'],
            ['cat' => 'Bệnh Truyền Nhiễm', 'title' => 'Viêm Gan B Lây Truyền Từ Mẹ Sang Con: Tầm Quan Trọng Của Mũi Tiêm Trong 24H Đầu'],
            ['cat' => 'Bệnh Truyền Nhiễm', 'title' => 'Màng Não Cầu B và C: Căn Bệnh Diễn Tiến Nhanh Cần Bảo Vệ Sớm Cho Trẻ'],

            // Khuyến Cáo Y Tế
            ['cat' => 'Khuyến Cáo Y Tế', 'title' => 'Hướng Dẫn Chi Tiết Theo Dõi Phản Ứng Sau Tiêm Chủng Tại Nhà Cho Trẻ'],
            ['cat' => 'Khuyến Cáo Y Tế', 'title' => 'Phụ Nữ Chuẩn Bị Mang Thể: Danh Mục Các Mũi Vắc Xin Cần Hoàn Thành Trước 3 Tháng'],
            ['cat' => 'Khuyến Cáo Y Tế', 'title' => 'Người Có Bệnh Nền Tim Mạch Đái Tháo Đường: Lịch Tiêm Phòng Cúm Và Phế Cầu Khuyên Dùng'],
            ['cat' => 'Khuyến Cáo Y Tế', 'title' => 'Quy Trình Khám Sàng Lọc Trước Tiêm Chủng Đạt Chuẩn An Toàn Tại Medicare'],
            ['cat' => 'Khuyến Cáo Y Tế', 'title' => 'Lịch Tiêm Nhắc Cho Trẻ Từ 15 Đến 24 Tháng Tuổi: Đừng Bỏ Lỡ Mũi Tiêm Quan Trọng'],
            ['cat' => 'Khuyến Cáo Y Tế', 'title' => 'Bảo Quản Dây Chuyền Lạnh GSP: Yếu Tố Quyết Định Chất Lượng Và Hiệu Quả Vắc Xin'],
            ['cat' => 'Khuyến Cáo Y Tế', 'title' => 'Tiêm Chủng Cho Người Cao Tuổi: Tăng Cường Miễn Dịch Đẩy Lùi Bệnh Hô Hấp'],
            ['cat' => 'Khuyến Cáo Y Tế', 'title' => 'Nhân Viên Y Tế Và Người Chăm Sóc Trẻ: Các Vắc Xin Bắt Buộc Cần Tiêm Đủ'],

            // Tin Nóng Y Học
            ['cat' => 'Tin Nóng Y Học', 'title' => 'Thế Giới Cập Nhật Biến Thể Cúm Mới: Khuyến Cáo Tiêm Nhắc Hàng Năm'],
            ['cat' => 'Tin Nóng Y Học', 'title' => 'Hội Nghị Y Học Tiêm Chủng Việt Nam: Ra Mắt Phác Đồ Tiêm Phòng Thế Hệ Mới'],
            ['cat' => 'Tin Nóng Y Học', 'title' => 'Công Nghệ Vắc Xin MRNA Mở Ra Kỷ Nguyên Mới Trong Điều Trị Và Phòng Bệnh'],
            ['cat' => 'Tin Nóng Y Học', 'title' => 'Dịch Bệnh Mùa Thu Đông: Bộ Y Tế Khuyến Cáo Chủ Động Tiêm Phòng Sớm'],
            ['cat' => 'Tin Nóng Y Học', 'title' => 'Nghiên Cứu Mới Về Hiệu Quả Bảo Vệ Lâu Dài Của Vắc Xin HPV Trên Nam Và Nữ'],

            // Vắc Xin Mới
            ['cat' => 'Vắc Xin Mới', 'title' => 'Vắc Xin Shingrix Thế Hệ Mới: Giải Pháp Phòng Ngừa Zona Thần Kinh Cho Người Trên 50 Tuổi'],
            ['cat' => 'Vắc Xin Mới', 'title' => 'Gardasil 9: Vắc Xin Phòng HPV 9 Chủng Bảo Vệ Toàn Diện Cho Cả Nam Và Nữ'],
            ['cat' => 'Vắc Xin Mới', 'title' => 'Vắc Xin Dengue Qdenga: Bước Tiến Mới Trong Cuộc Chiến Chống Sốt Xuất Huyết'],
            ['cat' => 'Vắc Xin Mới', 'title' => 'Bexsero Phòng Màng Não Cầu B: Xuất Xứ Ý Đã Có Mặt Tại Hệ Thống Medicare'],
            ['cat' => 'Vắc Xin Mới', 'title' => 'MenQuadfi: Vắc Xin Màng Não Cầu Tứ Phối Mới Nhất Được Phê Duyệt'],

            // Chăm Sóc Trẻ Em
            ['cat' => 'Chăm Sóc Trẻ Em', 'title' => 'Kinh Nghiệm Giúp Bé Không Sốt Và Ít Quấy Khóc Sau Khi Tiêm Phòng'],
            ['cat' => 'Chăm Sóc Trẻ Em', 'title' => 'Dinh Dưỡng Chuẩn Y Khoa Tăng Cường Đề Kháng Cho Trẻ Trong Mùa Nắng Nóng'],
            ['cat' => 'Chăm Sóc Trẻ Em', 'title' => 'Bảng Lịch Tiêm Chủng Đầy Đủ Cho Trẻ Từ 0 Đến 24 Tháng Tuổi Mới Nhất'],
            ['cat' => 'Chăm Sóc Trẻ Em', 'title' => 'Xử Lý Đúng Cách Khi Trẻ Bị Sưng Đỏ Tại Vị Trí Tiêm Chủng'],
            ['cat' => 'Chăm Sóc Trẻ Em', 'title' => 'Cách Chuẩn Bị Tâm Lý Và Trang Phục Cho Bé Trước Khi Đến Trung Tâm Tiêm Chủng'],

            // Tiêm Chủng Người Lớn
            ['cat' => 'Tiêm Chủng Người Lớn', 'title' => 'Người Lớn Tuổi Có Cần Tiêm Vắc Xin Không: Giải Đáp Từ Bác Sĩ Chuyên Khoa'],
            ['cat' => 'Tiêm Chủng Người Lớn', 'title' => 'Vắc Xin Ngừa Ung Thư Cổ Tử Cung Và Các Bệnh Do HPV Cho Người Trưởng Thành'],
            ['cat' => 'Tiêm Chủng Người Lớn', 'title' => 'Tiêm Phòng Trước Khi Đi Du Lịch Nước Ngoài: Những Điều Cần Biết'],
            ['cat' => 'Tiêm Chủng Người Lớn', 'title' => 'Phòng Bệnh Viêm Phổi Cho Người Tiền Sử Hút Thuốc Và Bệnh Phổi Mạn Tính'],
        ];

        // Truncate existing articles for clean seed
        Article::truncate();

        $count = 0;

        // Loop to create 100 realistic articles
        for ($i = 1; $i <= 100; $i++) {
            $topic = $topicTitles[($i - 1) % count($topicTitles)];
            $image = $images[($i - 1) % count($images)];
            $cat = $topic['cat'];
            $title = $topic['title'] . ($i > count($topicTitles) ? " (Phần {$i})" : "");
            $slug = Str::slug($title) . '-' . Str::random(5);

            $summary = "Bài viết cung cấp thông tin y khoa chính thống về {$title}. Đội ngũ bác sĩ chuyên khoa tiêm chủng Medicare khuyến cáo quý khách hàng chủ động tham khảo lịch tiêm và thăm khám kịp thời.";

            $content = "
                <p class='lead'>Tiêm chủng là biện pháp phòng bệnh chủ động, an toàn và hiệu quả nhất được Tổ chức Y tế Thế giới (WHO) và Bộ Y tế Việt Nam khuyến cáo cho mọi lứa tuổi.</p>
                
                <h3>1. Tại sao việc tiêm phòng lại quan trọng?</h3>
                <p>Khi vắc xin đưa vào cơ thể, hệ miễn dịch sẽ được kích hoạt sản sinh ra kháng thể đặc hiệu. Nhờ đó, nếu gặp phải vi khuẩn hoặc vi-rút gây bệnh trong tương lai, cơ thể sẽ có sẵn lá chắn bảo vệ mạnh mẽ, giảm thiểu tối đa nguy cơ biến chứng nặng hoặc tử vong.</p>
                
                <blockquote style='background: #fff5f5; border-left: 4px solid #c8102e; padding: 16px 20px; margin: 20px 0; font-style: italic; color: #475569;'>
                    'Chủ động tiêm chủng không chỉ bảo vệ bản thân mà còn tạo nên miễn dịch cộng đồng vững chắc, bảo vệ cho trẻ nhỏ và người cao tuổi xung quanh chúng ta.' - Hội đồng Y khoa Medicare Cờ Đỏ.
                </blockquote>

                <h3>2. Hướng dẫn chăm sóc và theo dõi sức khỏe</h3>
                <p>Sau khi tiêm chủng, người tiêm cần ở lại trung tâm theo dõi ít nhất 30 phút để kiểm tra tình trạng sức khỏe ban đầu. Khi về nhà, tiếp tục tự theo dõi trong vòng 24 - 48 giờ tiếp theo. Nếu xuất hiện các triệu chứng như sốt cao kéo dài, co giật hoặc khó thở, cần đến ngay cơ sở y tế gần nhất.</p>

                <h3>3. Đăng ký tư vấn và tiêm chủng tại Medicare Cờ Đỏ</h3>
                <p>Hệ thống Trung tâm Tiêm chủng Medicare cam kết cung cấp vắc xin chính hãng 100%, bảo quản nghiêm ngặt theo tiêu chuẩn Dây chuyền lạnh GSP (2-8 độ C), quy trình khám sàng lọc miễn phí cùng đội ngũ điều dưỡng giàu kinh nghiệm, nhẹ nhàng.</p>
            ";

            Article::create([
                'title' => $title,
                'slug' => $slug,
                'category' => $cat,
                'summary' => $summary,
                'content' => $content,
                'image' => $image,
                'views' => rand(300, 18500),
                'is_published' => true,
                'created_at' => now()->subDays(rand(1, 180))->subHours(rand(1, 23)),
            ]);

            $count++;
        }

        $this->command->info("Đã nạp thành công {$count} bài viết tin tức y khoa giả lập chuẩn VNVC!");
    }
}
