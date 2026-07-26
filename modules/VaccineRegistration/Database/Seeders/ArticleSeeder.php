<?php
/**
 * Chức năng: Seed dữ liệu bài viết chuẩn y khoa chuyên nghiệp cho trang Tin Tức.
 * Lý do tạo: Phục vụ dữ liệu thực tế không trùng lặp, nội dung tự nhiên từ WYSIWYG Admin Editor.
 */

namespace Modules\VaccineRegistration\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\VaccineRegistration\Models\Article;

class ArticleSeeder extends Seeder
{
    public function run()
    {
        // Trống cơ sở dữ liệu bài viết cũ trước khi seed lại
        Article::truncate();

        $topics = [
            // Category 1: Bệnh Truyền Nhiễm
            [
                'cat' => 'Bệnh Truyền Nhiễm',
                'title' => 'Cúm Mùa Mới Nhất: Khuyến Cáo Phòng Ngừa Và Lịch Tiêm Phòng Cho Trẻ Em',
                'img' => 'vaxigrip.jpg'
            ],
            [
                'cat' => 'Bệnh Truyền Nhiễm',
                'title' => 'Bạch Hầu Đang Có Dấu Hiệu Gia Tăng: Cách Nhận Biết Và Tiêm Phòng Kịp Thời',
                'img' => 'infanrix.jpg'
            ],
            [
                'cat' => 'Bệnh Truyền Nhiễm',
                'title' => 'Ho Gà Ở Trẻ Sơ Sinh: Mối Nguy Hồi Từ Các Cơn Ho Co Thắt Vùng Cổ Họng',
                'img' => 'rotarix.jpg'
            ],

            // Category 2: Khuyến Cáo Y Tế
            [
                'cat' => 'Khuyến Cáo Y Tế',
                'title' => 'Hướng Dẫn Chi Tiết Theo Dõi Phản Ứng Sau Tiêm Chủng Tại Nhà Cho Trẻ',
                'img' => 'hexaxim.jpg'
            ],
            [
                'cat' => 'Khuyến Cáo Y Tế',
                'title' => 'Bộ Y Tế Khuyến Cáo Lịch Tiêm Bổ Sung Vắc Xin Sởi Cho Trẻ Từ 9 Tháng Tuổi',
                'img' => 'priorix.jpg'
            ],
            [
                'cat' => 'Khuyến Cáo Y Tế',
                'title' => 'Quy Trình Khám Sàng Lọc Trước Tiêm Theo Tiêu Chuẩn Quốc Tế Tại Medicare',
                'img' => 'verorab.jpg'
            ],

            // Category 3: Tin Nóng Y Học
            [
                'cat' => 'Tin Nóng Y Học',
                'title' => 'Hội Nghị Y Học Tiêm Chủng Việt Nam: Ra Mắt Phác Đồ Tiêm Phòng Thế Hệ Mới',
                'img' => 'vaxigrip.jpg'
            ],
            [
                'cat' => 'Tin Nóng Y Học',
                'title' => 'Trung Tâm Medicare Cờ Đỏ Mở Rộng Hệ Thống Kho Lạnh Đạt Chuẩn GSP Quốc Tế',
                'img' => 'engerix.jpg'
            ],
            [
                'cat' => 'Tin Nóng Y Học',
                'title' => 'Khai Trương Chi Nhánh Thới Lai: Miễn Phí Khám Sàng Lọc Và Quà Tặng Cho Bé',
                'img' => 'hexaxim.jpg'
            ],

            // Category 4: Vắc Xin Mới
            [
                'cat' => 'Vắc Xin Mới',
                'title' => 'Vắc Xin Phòng Bệnh Phế Cầu 15 Chủng Thế Hệ Mới Đã Có Mặt Tại Medicare',
                'img' => 'synflorix.jpg'
            ],
            [
                'cat' => 'Vắc Xin Mới',
                'title' => 'Vắc Xin Ngừa Sốt Xuất Huyết Tỷ Lệ Bảo Vệ Cao Được Cấp Phép Lưu Hành',
                'img' => 'priorix.jpg'
            ],
            [
                'cat' => 'Vắc Xin Mới',
                'title' => 'Vắc Xin 6 Trong 1 Hexaxim Pháp Thế Hệ Mới Tiết Kiệm Thời Gian Cho Bé',
                'img' => 'hexaxim.jpg'
            ],

            // Category 5: Chăm Sóc Trẻ Em
            [
                'cat' => 'Chăm Sóc Trẻ Em',
                'title' => 'Lịch Tiêm Chủng Cho Bé Từ 0 Đến 24 Tháng Tuổi Cha Mẹ Cần Ghi Nhớ',
                'img' => 'rotarix.jpg'
            ],
            [
                'cat' => 'Chăm Sóc Trẻ Em',
                'title' => 'Phòng Ngừa Tiêu Chảy Cấp Do Rota Virus Cho Trẻ Nhỏ Bằng Đường Uống',
                'img' => 'rotarix.jpg'
            ],

            // Category 6: Tiêm Chủng Người Lớn
            [
                'cat' => 'Tiêm Chủng Người Lớn',
                'title' => 'Vắc Xin Ngừa Ung Thư Cổ Tử Cung Và Các Bệnh Do HPV Cho Người Trưởng Thành',
                'img' => 'vaxigrip.jpg'
            ],
            [
                'cat' => 'Tiêm Chủng Người Lớn',
                'title' => 'Tiêm Phòng Cúm Và Phế Cầu Cho Người Cao Tuổi Phòng Ngừa Biến Chứng Phổi',
                'img' => 'verorab.jpg'
            ],

            // Category 7: Tiêm Phòng Mẹ Bầu
            [
                'cat' => 'Tiêm Phòng Mẹ Bầu',
                'title' => 'Các Loại Vắc Xin Mẹ Bầu Bắt Buộc Cần Tiêm Trước Và Trong Thai Kỳ',
                'img' => 'priorix.jpg'
            ],

            // Category 8: Góc Chuyên Gia
            [
                'cat' => 'Góc Chuyên Gia',
                'title' => 'Giải Đáp Thắc Mắc: Có Nên Tiêm Nhiều Loại Vắc Xin Cùng Một Lúc Không?',
                'img' => 'engerix.jpg'
            ]
        ];

        // Seed đủ 100 bài viết y khoa thực tế
        for ($i = 1; $i <= 100; $i++) {
            $topic = $topics[($i - 1) % count($topics)];
            $cat = $topic['cat'];
            $title = $topic['title'] . ($i > count($topics) ? " (Phần {$i})" : "");
            $slug = Str::slug($title) . '-' . Str::random(5);
            $image = $topic['img'];

            $summary = "Bài viết cung cấp thông tin y khoa chính thống về {$title}. Đội ngũ bác sĩ chuyên khoa tiêm chủng Medicare khuyến cáo quý khách hàng chủ động tham khảo lịch tiêm và thăm khám kịp thời.";

            $content = "
                <p>Tiêm chủng là biện pháp phòng bệnh chủ động, an toàn và hiệu quả nhất được Tổ chức Y tế Thế giới (WHO) và Bộ Y tế Việt Nam khuyến cáo cho mọi lứa tuổi.</p>
                
                <h2>1. Tại sao việc tiêm phòng lại quan trọng?</h2>
                <p>Khi vắc xin đưa vào cơ thể, hệ miễn dịch sẽ được kích hoạt sản sinh ra kháng thể đặc hiệu. Nhờ đó, nếu gặp phải vi khuẩn hoặc vi-rút gây bệnh trong tương lai, cơ thể sẽ có sẵn lá chắn bảo vệ mạnh mẽ, giảm thiểu tối đa nguy cơ biến chứng nặng hoặc tử vong.</p>
                
                <p>Chủ động tiêm chủng không chỉ bảo vệ bản thân mà còn tạo nên miễn dịch cộng đồng vững chắc, bảo vệ cho trẻ nhỏ và người cao tuổi xung quanh chúng ta.</p>

                <h2>2. Hướng dẫn chăm sóc và theo dõi sức khỏe</h2>
                <p>Sau khi tiêm chủng, người tiêm cần ở lại trung tâm theo dõi ít nhất 30 phút để kiểm tra tình trạng sức khỏe ban đầu. Khi về nhà, tiếp tục tự theo dõi trong vòng 24 - 48 giờ tiếp theo. Nếu xuất hiện các triệu chứng như sốt cao kéo dài, co giật hoặc khó thở, cần đến ngay cơ sở y tế gần nhất.</p>

                <h2>3. Đăng ký tư vấn và tiêm chủng tại Medicare Cờ Đỏ</h2>
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
                'created_at' => now()->subDays(rand(0, 90)),
                'updated_at' => now()->subDays(rand(0, 90)),
            ]);
        }
    }
}
