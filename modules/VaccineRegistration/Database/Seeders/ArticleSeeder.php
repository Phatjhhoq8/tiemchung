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
                'title' => 'Vì Sao Trẻ Học Đường Rất Dễ Mắc Các Bệnh Truyền Nhiễm Khi Vào Mùa?',
                'slug' => Str::slug('Vì Sao Trẻ Học Đường Rất Dễ Mắc Các Bệnh Truyền Nhiễm Khi Vào Mùa'),
                'summary' => 'Môi trường học đường sinh hoạt tập trung là điều kiện thuận lợi để các bệnh đường hô hấp, tay chân miệng và cúm mùa lây lan nhanh chóng giữa các bé.',
                'content' => "Trẻ em khi bắt đầu đi học có tần suất tiếp xúc cao với các bạn cùng lớp. Hệ miễn dịch chưa hoàn thiện khiến trẻ dễ mắc các bệnh truyền nhiễm qua đường hô hấp hoặc tiếp xúc trực tiếp.\n\nCác bệnh dễ bùng phát ở lứa tuổi mầm non và tiểu học bao gồm:\n- Cúm mùa & các bệnh nhiễm trùng đường hô hấp trên.\n- Tay chân miệng và tiêu chảy do Rota virus.\n- Thủy đậu, Sởi, Quai bị, Rubella.\n\nChủ động tiêm vắc xin đầy đủ và đúng lịch trước năm học mới là lá chắn an toàn nhất giúp bảo vệ sức khỏe cho trẻ, đảm bảo quá trình học tập không bị gián đoạn.",
                'image' => 'prevenar13.jpg',
                'category' => 'Bệnh Truyền Nhiễm',
                'is_published' => true,
                'is_featured' => true,
                'views' => 1560,
            ],
            [
                'title' => 'Trước Khi Vào Lớp 1, Cha Mẹ Đừng Quên Rà Soát Lịch Tiêm Vắc Xin Cho Con',
                'slug' => Str::slug('Trước Khi Vào Lớp 1 Cha Mẹ Đừng Quên Rà Soát Lịch Tiêm Vắc Xin Cho Con'),
                'summary' => 'Nhập học mầm non và tiểu học là cột mốc lớn, trẻ cần được hoàn thành các mũi tiêm nhắc phế cầu, cúm, sởi - quai bị - rubella và thủy đậu.',
                'content' => "Các mũi tiêm chủng lúc sơ sinh có thể giảm dần lượng kháng thể bảo vệ theo thời gian. Mốc 4-6 tuổi trước khi bước vào lớp 1 là thời điểm vàng để thực hiện các mũi tiêm nhắc lại quan trọng.\n\nDanh mục vắc xin cần rà soát cho trẻ mầm non & chuẩn bị vào lớp 1:\n1. Vắc xin Sởi - Quai bị - Rubella (Mũi 2 nhắc lại).\n2. Vắc xin Thủy đậu (Varilrix / Varivax).\n3. Vắc xin Cúm mùa (Tiêm nhắc hàng năm).\n4. Vắc xin Phế cầu khuẩn (Prevenar 13).\n5. Vắc xin Bạch hầu - Ho gà - Uốn ván - Bại liệt (Boostrix / Tetraxim).\n\nQuý phụ huynh hãy mang theo sổ tiêm chủng của bé đến trung tâm Medicare để được bác sĩ kiểm tra và tư vấn phác đồ tiêm bổ sung kịp thời.",
                'image' => 'priorix.png',
                'category' => 'Khuyến cáo Y tế',
                'is_published' => true,
                'is_featured' => true,
                'views' => 1890,
            ],
            [
                'title' => 'Tại Sao Nên Tiêm Vắc Xin Cúm Mùa Hàng Năm Cho Cả Gia Đình?',
                'slug' => Str::slug('Tại Sao Nên Tiêm Vắc Xin Cúm Mùa Hàng Năm Cho Cả Gia Đình'),
                'summary' => 'Virus cúm biến đổi liên tục qua mỗi năm. Việc tiêm nhắc lại vắc xin cúm tứ giá Vaxigrip Tetra / Influvac Tetra giúp bảo vệ đường hô hấp tối ưu cho trẻ em và người lớn.',
                'content' => "Bệnh cúm mùa là bệnh nhiễm trùng đường hô hấp cấp tính do virus cúm gây ra. Virus cúm có khả năng biến đổi chủng liên tục hàng năm, vì vậy Tổ chức Y tế Thế giới (WHO) khuyến cáo mọi đối tượng từ 6 tháng tuổi trở lên nên tiêm vắc xin phòng cúm nhắc lại mỗi năm một lần.\n\nLợi ích của việc tiêm vắc xin cúm tứ giá Vaxigrip Tetra / Influvac Tetra:\n- Giảm đến 80% nguy cơ mắc cúm và giảm đáng kể mức độ nặng của bệnh.\n- Bảo vệ người cao tuổi và người có bệnh nền (tim mạch, tiểu đường, phổi mạn tính).\n- Phòng ngừa biến chứng nguy hiểm như viêm phổi, suy hô hấp.\n\nTrung tâm Medicare luôn sẵn sàng vắc xin cúm tứ giá thế hệ mới nhất với quy trình bảo lưu lạnh GSP tiêu chuẩn quốc tế.",
                'image' => 'vaxigrip.jpg',
                'category' => 'Tin nóng trong ngày',
                'is_published' => true,
                'is_featured' => true,
                'views' => 2420,
            ],
            [
                'title' => 'Đã Có Vắc Xin Sốt Xuất Huyết Qdenga: Hướng Dẫn Chi Tiết Độ Tuổi & Lịch Tiêm',
                'slug' => Str::slug('Đã Có Vắc Xin Sốt Xuất Huyết Qdenga Hướng Dẫn Chi Tiết Độ Tuổi Lịch Tiêm'),
                'summary' => 'Lần đầu tiên Việt Nam đưa vào sử dụng vắc xin phòng bệnh Sốt xuất huyết Qdenga (Nhật Bản). Tìm hiểu phác đồ 2 mũi tiêm chuẩn y khoa tại Medicare Cờ Đỏ.',
                'content' => "Vắc xin Qdenga do hãng dược phẩm Takeda (Nhật Bản) nghiên cứu và phát triển, giúp phòng ngừa hiệu quả cả 4 tuýp huyết thanh virus Dengue (DEN-1, DEN-2, DEN-3, DEN-4).\n\nThông tin vắc xin Sốt xuất huyết Qdenga:\n- Độ tuổi chỉ định: Trẻ từ 4 tuổi trở lên và người lớn.\n- Phác đồ tiêm: Gồm 2 mũi tiêm cách nhau 3 tháng.\n- Hiệu quả phòng bệnh: Giảm nguy cơ mắc bệnh sốt xuất huyết lên đến 80.2% và giảm tỷ lệ nhập viện do biến chứng nặng tới 90.4%.\n- Không cần xét nghiệm máu trước khi tiêm (áp dụng cho cả người đã từng mắc hoặc chưa từng mắc sốt xuất huyết).\n\nLiên hệ hotline Medicare 0938 60 38 39 để đặt giữ liều vắc xin Sốt xuất huyết ngay hôm nay.",
                'image' => 'qdenga.jpg',
                'category' => 'Vắc Xin Mới',
                'is_published' => true,
                'is_featured' => true,
                'views' => 3180,
            ],
            [
                'title' => 'Lịch Tiêm Chủng Mới Nhất Dành Cho Trẻ Từ 0 Đến 24 Tháng Tuổi',
                'slug' => Str::slug('Lịch Tiêm Chủng Mới Nhất Dành Cho Trẻ Từ 0 Đến 24 Tháng Tuổi'),
                'summary' => 'Tổng hợp đầy đủ các mũi tiêm bắt buộc và khuyến cáo quan trọng giúp trẻ xây dựng hệ miễn dịch vững chắc trong những năm đầu đời.',
                'content' => "Trong 24 tháng đầu đời, hệ miễn dịch của trẻ còn rất non nớt. Việc tiêm chủng đầy đủ và đúng mốc thời gian đóng vai trò quyết định phòng tránh các bệnh truyền nhiễm nguy hiểm ở trẻ nhỏ.\n\nMốc tiêm chủng trọng tâm 0 - 24 tháng tuổi:\n- Sơ sinh: Tiêm vắc xin Lao (BCG) & Viêm gan B mũi sơ sinh.\n- 2, 3, 4 tháng: Tiêm vắc xin 6 trong 1 (Hexaxim / Infanrix Hexa), uống vắc xin Rota (Rotarix / Rotavin), tiêm vắc xin Phế cầu (Prevenar 13).\n- 9 - 12 tháng: Tiêm vắc xin Sởi, Viêm não Nhật Bản (Imojev), Sốt xuất huyết (Qdenga từ 4 tuổi).\n- 12 - 24 tháng: Tiêm nhắc 6in1, Phế cầu, Thủy đậu, Sởi - Quai bị - Rubella.\n\nBố mẹ có thể tra cứu gói vắc xin trọn gói cho bé tại Medicare để tiết kiệm thời gian và đảm bảo không bỏ lỡ mũi tiêm nào.",
                'image' => 'hexaxim.jpg',
                'category' => 'Chăm Sóc Bé',
                'is_published' => true,
                'is_featured' => true,
                'views' => 1980,
            ],
            [
                'title' => 'Phòng Ngừa Bệnh Zona Thần Kinh Với Vắc Xin Thế Hệ Mới Shingrix',
                'slug' => Str::slug('Phòng Ngừa Bệnh Zona Thần Kinh Với Vắc Xin Thế Hệ Mới Shingrix'),
                'summary' => 'Bệnh Zona thần kinh gây đau đớn dữ dội và biến chứng đau thần kinh sau Zona kéo dài. Vắc xin Shingrix đem lại hiệu quả bảo vệ lên tới 97%.',
                'content' => "Zona thần kinh (giời ăn) do virus Varicella-Zoster tái hoạt động sau khi cơ thể từng mắc thủy đậu. Bệnh gây ra các bọng nước gây đau rát dữ dội dọc theo đường dây thần kinh và có thể để lại biến chứng đau thần kinh kéo dài hàng tháng, hàng năm (PHN).\n\nVắc xin Shingrix (Hãng GSK - Bỉ):\n- Chỉ định cho người từ 50 tuổi trở lên và người 18 tuổi trở lên có nguy cơ cao suy giảm miễn dịch.\n- Phác đồ 2 mũi tiêm (mũi 2 cách mũi 1 từ 2 đến 6 tháng).\n- Hiệu quả bảo vệ vượt trội trên 97% ở người lớn tuổi.",
                'image' => 'shingrix.jpg',
                'category' => 'Vắc Xin Mới',
                'is_published' => true,
                'is_featured' => true,
                'views' => 1750,
            ],
            [
                'title' => 'Vắc Xin Gardasil 9: Bảo Vệ Toàn Diện Khỏi 9 Tuýp Virus HPV Nổi Bật',
                'slug' => Str::slug('Vắc Xin Gardasil 9 Bảo Vệ Toàn Diện Khỏi 9 Tuýp Virus HPV Nổi Bật'),
                'summary' => 'Giải pháp phòng ngừa ung thư cổ tử cung, ung thư hậu môn, âm đạo, vòm họng và mụn cóc sinh dục hiệu quả cho cả nam và nữ từ 9 đến 45 tuổi.',
                'content' => "Virus HPV là nguyên nhân hàng đầu gây ung thư cổ tử cung ở nữ giới và các bệnh lý ung thư sinh dục, vòm họng ở cả nam và nữ. Vắc xin Gardasil 9 phòng ngừa 9 tuýp virus HPV nguy hiểm nhất (6, 11, 16, 18, 31, 33, 45, 52, 58).\n\nVì sao nam giới cũng cần tiêm Gardasil 9?\n- Nam giới có tỷ lệ thải trừ HPV kém hơn nữ giới.\n- Phòng ngừa ung thư hậu môn, ung thư dương vật, ung thư hầu họng và sùi mào gà.\n- Ngăn chặn lây nhiễm HPV cho bạn đời.\n\nĐộ tuổi tiêm chủng: Từ 9 đến 45 tuổi cho cả nam và nữ.",
                'image' => 'gardasil9.jpg',
                'category' => 'Tin nóng trong ngày',
                'is_published' => true,
                'is_featured' => true,
                'views' => 2890,
            ],
            [
                'title' => 'Tiêm Phòng Thủy Đậu - Giải Pháp An Toàn Tránh Biến Chứng Sẹo & Viêm Phổi',
                'slug' => Str::slug('Tiêm Phòng Thủy Đậu Giải Pháp An Toàn Tránh Biến Chứng Sẹo Viêm Phổi'),
                'summary' => 'Thủy đậu là bệnh lây nhiễm mạnh qua đường hô hấp. Tiêm vắc xin Varilrix giúp phòng bệnh hiệu quả và hạn chế nguy cơ để lại sẹo lõm.',
                'content' => "Thủy đậu (trái rạ) bùng phát mạnh vào mùa đông xuân. Bệnh lây lan rất nhanh trong trường học và gia đình qua giọt bắn đường hô hấp hoặc tiếp xúc với nốt mỏng.\n\nCác biến chứng nguy hiểm của bệnh Thủy đậu:\n- Nhiễm trùng da, để lại sẹo sâu thẩm mỹ.\n- Viêm phổi, viêm màng não do thủy đậu.\n- Tiêm vắc xin Varilrix (Bỉ) / Varivax (Mỹ) với 2 mũi tiêm giúp phòng bệnh đến 98%.",
                'image' => 'varilrix.jpg',
                'category' => 'Chăm Sóc Bé',
                'is_published' => true,
                'is_featured' => true,
                'views' => 1430,
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

