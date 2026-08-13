<?php

namespace Modules\VaccineRegistration\Support;

class SiteContentRegistry
{
    /**
     * Danh sách tất cả các phân đoạn (sections) mặc định trên Trang Chủ.
     */
    public static $defaultSections = [
        'quick_booking' => 'Form Đăng ký nhanh',
        'centers' => 'Hệ thống Trung tâm',
        'recommendations' => 'Khuyến nghị Y khoa',
        'qdenga_promo' => 'Vắc-xin nổi bật',
        'featured_vaccines' => 'Danh mục vắc-xin',
        'safe_process' => 'Quy trình 5 bước',
        'services' => 'Dịch vụ chính',
        'testimonials' => 'Đánh giá khách hàng',
        'news' => 'Tin tức y khoa',
        'faq' => 'Câu hỏi thường gặp',
    ];

    /**
     * Lấy danh sách các trường được phép cấu hình (whitelist) và các thuộc tính liên quan.
     */
    public static function getFields(): array
    {
        return [
            // --- HỆ THỐNG & KHUNG CHUNG (GLOBAL SHELL) ---
            'site_name' => [
                'type' => 'string',
                'default' => 'Medicare',
                'rules' => 'required|string|max:100',
            ],
            'brand_title' => [
                'type' => 'string',
                'default' => 'Hệ Thống Tiêm Chủng Medicare',
                'rules' => 'required|string|max:255',
            ],
            'hotline' => [
                'type' => 'string',
                'default' => '0938 60 38 39',
                'rules' => 'required|string|max:50',
            ],
            'hotline_2' => [
                'type' => 'string',
                'default' => '0932 477 184',
                'rules' => 'nullable|string|max:50',
            ],
            'email' => [
                'type' => 'string',
                'default' => 'cskh@medicare.vn',
                'rules' => 'required|email|max:100',
            ],
            'address' => [
                'type' => 'string',
                'default' => 'Chi nhánh 1: Cờ Đỏ (Cổng BV Quân Dân Y Cần Thơ) | Chi nhánh 2: Thới Lai (Thị trấn Thới Lai)',
                'rules' => 'required|string|max:500',
            ],
            'working_hours' => [
                'type' => 'string',
                'default' => 'Tất cả các ngày trong tuần (Từ 7:00 - 17:00 kể cả Chủ Nhật và ngày Lễ)',
                'rules' => 'required|string|max:255',
            ],
            'footer_text' => [
                'type' => 'string',
                'default' => '© 2026 Medicare - Hệ Thống Tiêm Chủng Vắc Xin Trẻ Em và Người Lớn phục vụ tận tâm.',
                'rules' => 'required|string|max:500',
            ],
            'footer_company_name' => [
                'type' => 'string',
                'default' => 'CÔNG TY CỔ PHẦN VẮC XIN MEDICARE',
                'rules' => 'required|string|max:255',
            ],
            'footer_sub_title' => [
                'type' => 'string',
                'default' => 'HỆ THỐNG TRUNG TÂM TIÊM CHỦNG VẮC XIN CHO TRẺ EM & NGƯỜI LỚN AN TOÀN – UY TÍN – CHẤT LƯỢNG HÀNG ĐẦU VIỆT NAM',
                'rules' => 'required|string|max:500',
            ],
            'footer_content_manager' => [
                'type' => 'string',
                'default' => 'Chịu trách nhiệm nội dung: Ban Giám Đốc HỆ THỐNG TIÊM CHỦNG MEDICARE',
                'rules' => 'required|string|max:255',
            ],
            'footer_info_lines' => [
                'type' => 'json',
                'default' => '[{"icon":"shield-check","text":"Giấy chứng nhận ĐKKD số 0107631488 do Sở KH&ĐT TP. Cần Thơ cấp ngày 11/11/2016"},{"icon":"building","text":"Trụ sở: Cổng Bệnh viện Quân Dân Y TP Cần Thơ, Ấp Thới Thuận, Xã Cờ Đỏ, TP. Cần Thơ"},{"icon":"mail","text":"Email liên hệ: cskh@medicare.vn"}]',
                'rules' => 'required|json',
            ],

            // --- TRANG CHỦ (HOME PAGE) ---
            // 4 Ô tiện ích nhanh
            'quick_t1_title' => ['type' => 'string', 'default' => 'Đặt Mua Vắc Xin', 'rules' => 'required|string|max:100'],
            'quick_t1_sub' => ['type' => 'string', 'default' => 'Chọn mua vắc xin online nhanh chóng', 'rules' => 'nullable|string|max:255'],
            'quick_t2_title' => ['type' => 'string', 'default' => 'Đăng Ký Tiêm', 'rules' => 'required|string|max:100'],
            'quick_t2_sub' => ['type' => 'string', 'default' => 'Đặt hẹn tiêm chủng cho bé & gia đình', 'rules' => 'nullable|string|max:255'],
            'quick_t3_title' => ['type' => 'string', 'default' => 'Bảng Giá Vắc Xin', 'rules' => 'required|string|max:100'],
            'quick_t3_sub' => ['type' => 'string', 'default' => 'Tra cứu bảng giá vắc xin mới nhất', 'rules' => 'nullable|string|max:255'],
            'quick_t4_title' => ['type' => 'string', 'default' => 'Hệ Thống Trung Tâm', 'rules' => 'required|string|max:100'],
            'quick_t4_sub' => ['type' => 'string', 'default' => 'Tìm kiếm chi nhánh gần bạn nhất', 'rules' => 'nullable|string|max:255'],

            // Khung Quy trình 5 bước
            'home_safe_process_title' => ['type' => 'string', 'default' => 'Quy Trình Tiêm Chủng An Toàn', 'rules' => 'required|string|max:255'],
            'home_safe_process_desc' => ['type' => 'string', 'default' => 'Medicare áp dụng quy trình tiêm chủng an toàn nghiêm ngặt chuẩn Bộ Y tế để bảo vệ sức khỏe của bạn.', 'rules' => 'nullable|string|max:500'],
            'home_safe_process' => [
                'type' => 'json',
                'default' => '[{"step":"1","title":"Khám Sàng Lọc","desc":"100% khách hàng được bác sĩ chuyên khoa khám sàng lọc chỉ định tiêm chủng phù hợp."},{"step":"2","title":"Tư Vấn & Chỉ Định","desc":"Bác sĩ giải thích tác dụng, phản ứng phụ và thống nhất vắc xin trước khi tiêm."},{"step":"3","title":"Thực Hiện Tiêm","desc":"Điều dưỡng đối chiếu thông tin vắc xin và thực hiện tiêm chủng nhẹ nhàng, đúng kỹ thuật."},{"step":"4","title":"Theo Dõi Sau Tiêm","desc":"Theo dõi sức khỏe tại khu vực sảnh chờ 30 phút để phát hiện sớm phản ứng bất thường."},{"step":"5","title":"Kiểm Tra & Ra Về","desc":"Bác sĩ kiểm tra lại vết tiêm, tư vấn cách theo dõi chăm sóc tại nhà trước khi về."}]',
                'rules' => 'required|json',
            ],

            // Ý kiến khách hàng (Testimonials)
            'home_testimonials' => [
                'type' => 'json',
                'default' => '[{"name":"Chị Nguyễn Thảo Vy","role":"Phụ huynh bé Min (3 tháng)","content":"Phòng tiêm sạch sẽ, y bác sĩ rất nhẹ nhàng và chu đáo. Bé nhà mình tiêm về không hề bị sốt. Rất an tâm!","avatar":"/images/logo.png"},{"name":"Anh Trần Quốc Bảo","role":"Khách hàng tại Cờ Đỏ","content":"Quy trình tiêm chủng chuyên nghiệp, đặt lịch online trước đến là được vào khám ngay không cần chờ đợi.","avatar":"/images/logo.png"}]',
                'rules' => 'required|json',
            ],

            // Hỏi đáp FAQs
            'home_faq_title' => ['type' => 'string', 'default' => 'Câu Hỏi Thường Gặp', 'rules' => 'required|string|max:255'],
            'home_faq_desc' => ['type' => 'string', 'default' => 'Giải đáp những thắc mắc phổ biến nhất về dịch vụ tiêm chủng tại Medicare.', 'rules' => 'nullable|string|max:500'],
            'home_faqs' => [
                'type' => 'json',
                'default' => '[{"q":"Trẻ bao nhiêu tháng tuổi bắt đầu tiêm chủng?","a":"Trẻ sơ sinh nên được tiêm mũi đầu tiên (Viêm Gan B) ngay trong 24 giờ sau sinh. Từ 2 tháng tuổi, bé sẽ bắt đầu các mũi tiêm cơ bản như 6in1, Rotavirus, Phế cầu."},{"q":"Tiêm vắc xin có tác dụng phụ không?","a":"Phản ứng nhẹ như sưng đỏ tại chỗ tiêm, sốt nhẹ trong 1-2 ngày là bình thường. Tại Medicare, bé được theo dõi 30 phút sau tiêm và được hướng dẫn chi tiết cách chăm sóc tại nhà."},{"q":"Giá vắc xin có bao gồm phí khám sàng lọc không?","a":"Phí khám sàng lọc trước tiêm tại Medicare là hoàn toàn MIỄN PHÍ. Giá niêm yết trên website đã bao gồm trọn gói dịch vụ."}]',
                'rules' => 'required|json',
            ],

            // --- TRANG GIỚI THIỆU (ABOUT PAGE) ---
            'about_hero_title' => ['type' => 'string', 'default' => 'Giới Thiệu Hệ Thống Tiêm Chủng Medicare', 'rules' => 'required|string|max:255'],
            'about_hero_desc' => ['type' => 'string', 'default' => 'Đơn vị y tế uy tín hàng đầu cung cấp giải pháp phòng bệnh toàn diện bằng vắc xin chất lượng cao cho trẻ em và người lớn tại Cần Thơ.', 'rules' => 'required|string|max:500'],
            'about_story_title' => ['type' => 'string', 'default' => 'Hành trình Bảo vệ Sức khỏe Cộng đồng', 'rules' => 'required|string|max:255'],
            'about_story_desc' => ['type' => 'string', 'default' => 'Được thành lập từ năm 2016, Medicare bắt đầu với sứ mệnh mang dịch vụ tiêm chủng an toàn, chất lượng cao và chi phí hợp lý đến gần hơn với người dân tại các huyện ngoại thành Cần Thơ như Cờ Đỏ và Thới Lai. Trải qua gần 10 năm phát triển, chúng tôi tự hào trở thành điểm tựa sức khỏe đáng tin cậy cho hàng chục ngàn gia đình, liên tục cải tiến chất lượng và nâng cao dịch vụ chăm sóc y tế.', 'rules' => 'required|string|max:2000'],
            'about_stat_exp' => ['type' => 'string', 'default' => '10+', 'rules' => 'required|string|max:50'],
            'about_stat_exp_lbl' => ['type' => 'string', 'default' => 'Năm Kinh Nghiệm', 'rules' => 'required|string|max:100'],
            'about_stat_clients' => ['type' => 'string', 'default' => '50,000+', 'rules' => 'required|string|max:50'],
            'about_stat_clients_lbl' => ['type' => 'string', 'default' => 'Khách Hàng Hài Lòng', 'rules' => 'required|string|max:100'],
            'about_stat_branches' => ['type' => 'string', 'default' => '02', 'rules' => 'required|string|max:50'],
            'about_stat_branches_lbl' => ['type' => 'string', 'default' => 'Trung Tâm Tiêm Chủng', 'rules' => 'required|string|max:100'],
            'about_mission_title' => ['type' => 'string', 'default' => 'Sứ Mệnh Của Chúng Tôi', 'rules' => 'required|string|max:255'],
            'about_mission_desc' => ['type' => 'string', 'default' => 'Mang lại dịch vụ tiêm chủng an toàn tuyệt đối, vaccine chính hãng chất lượng cao với chi phí hợp lý cho mọi gia đình. Giúp cộng đồng chủ động phòng bệnh truyền nhiễm nguy hiểm.', 'rules' => 'required|string|max:1000'],
            'about_vision_title' => ['type' => 'string', 'default' => 'Tầm Nhìn Phát Triển', 'rules' => 'required|string|max:255'],
            'about_vision_desc' => ['type' => 'string', 'default' => 'Trở thành hệ thống tiêm chủng dịch vụ uy tín hàng đầu Cần Thơ và Đồng bằng sông Cửu Long, không ngừng cải tiến trang thiết bị và ứng dụng sổ tiêm điện tử thông minh.', 'rules' => 'required|string|max:1000'],
            'about_values_desc' => ['type' => 'string', 'default' => 'Mọi hoạt động y tế của hệ thống tiêm chủng Medicare đều tuân thủ các chuẩn mực chất lượng khắt khe nhất để bảo vệ an toàn cho sức khỏe gia đình bạn.', 'rules' => 'required|string|max:1000'],
            
            // Sáu Giá Trị Cốt Lõi Vàng
            'about_val1_icon' => ['type' => 'string', 'default' => 'shield-check', 'rules' => 'required|string|max:50'],
            'about_val1_title' => ['type' => 'string', 'default' => 'An Toàn Vượt Trội', 'rules' => 'required|string|max:100'],
            'about_val1_desc' => ['type' => 'string', 'default' => 'Quy trình tiêm chủng an toàn 5 bước chuẩn Bộ Y tế, 100% bác sĩ khám sàng lọc cẩn thận trước tiêm và theo dõi chặt chẽ sau tiêm.', 'rules' => 'required|string|max:255'],
            
            'about_val2_icon' => ['type' => 'string', 'default' => 'award', 'rules' => 'required|string|max:50'],
            'about_val2_title' => ['type' => 'string', 'default' => 'Uy Tín Hàng Đầu', 'rules' => 'required|string|max:100'],
            'about_val2_desc' => ['type' => 'string', 'default' => 'Cam kết vắc xin nhập khẩu chính hãng từ các tập đoàn dược phẩm lớn trên thế giới như GSK, MSD, Sanofi Pasteur, Pfizer.', 'rules' => 'required|string|max:255'],
            
            'about_val3_icon' => ['type' => 'string', 'default' => 'heart', 'rules' => 'required|string|max:50'],
            'about_val3_title' => ['type' => 'string', 'default' => 'Tận Tâm Phục Vụ', 'rules' => 'required|string|max:100'],
            'about_val3_desc' => ['type' => 'string', 'default' => 'Đội ngũ y bác sĩ, điều dưỡng ân cần, thấu hiểu tâm lý trẻ em và người lớn, tạo cảm giác thân thiện, nhẹ nhàng khi tiêm.', 'rules' => 'required|string|max:255'],
            
            'about_val4_icon' => ['type' => 'string', 'default' => 'snowflake', 'rules' => 'required|string|max:50'],
            'about_val4_title' => ['type' => 'string', 'default' => 'Hệ Thống Lạnh GSP', 'rules' => 'required|string|max:100'],
            'about_val4_desc' => ['type' => 'string', 'default' => 'Hệ thống kho lạnh và tủ bảo quản vắc xin đạt chuẩn GSP nghiêm ngặt từ 2 - 8°C giúp giữ trọn vẹn chất lượng và hiệu quả.', 'rules' => 'required|string|max:255'],
            
            'about_val5_icon' => ['type' => 'string', 'default' => 'scale', 'rules' => 'required|string|max:50'],
            'about_val5_title' => ['type' => 'string', 'default' => 'Trách Nhiệm Xã Hội', 'rules' => 'required|string|max:100'],
            'about_val5_desc' => ['type' => 'string', 'default' => 'Cung cấp vaccine với mức giá bình ổn, hỗ trợ tối đa người dân tại khu vực Cờ Đỏ & Thới Lai tiếp cận với y tế chất lượng cao.', 'rules' => 'required|string|max:255'],
            
            'about_val6_icon' => ['type' => 'string', 'default' => 'database', 'rules' => 'required|string|max:50'],
            'about_val6_title' => ['type' => 'string', 'default' => 'Sổ Tiêm Điện Tử', 'rules' => 'required|string|max:100'],
            'about_val6_desc' => ['type' => 'string', 'default' => 'Quản lý lịch sử tiêm chủng đồng bộ trên hệ thống, tự động nhắn tin nhắc lịch tiêm chủng định kỳ cho trẻ đúng hẹn.', 'rules' => 'required|string|max:255'],
            
            // Đội ngũ chuyên gia
            'about_team_members' => [
                'type' => 'json',
                'default' => '[{"name":"ThS. BS. Nguyễn Minh Đức","role":"Giám đốc chuyên môn tiêm chủng","avatar":"/images/avt_pktn.png","zalo":"0938603839"},{"name":"BS. Trần Thị Thanh Trúc","role":"Bác sĩ khám sàng lọc trước tiêm","avatar":"/images/avt_pkpđ.png","zalo":"0932477184"},{"name":"CNĐD. Lê Hoàng Phúc","role":"Điều dưỡng trưởng phòng tiêm","avatar":"/images/logo.png","zalo":"0909001122"}]',
                'rules' => 'required|json',
            ],

            // --- TRANG DỊCH VỤ (SERVICES PAGE) ---
            'services_hero_title' => ['type' => 'string', 'default' => 'Dịch Vụ Tiêm Chủng Toàn Diện', 'rules' => 'required|string|max:255'],
            'services_hero_desc' => ['type' => 'string', 'default' => 'Cung cấp đầy đủ gói tiêm vắc xin cho Trẻ em, Người lớn, Phụ nữ chuẩn bị mang thai và Tiêm chủng lưu động doanh nghiệp với chuẩn an toàn y tế tốt nhất.', 'rules' => 'required|string|max:500'],
            'services_list' => [
                'type' => 'json',
                'default' => '[{"title":"Tiêm vắc xin lẻ","desc":"Cung cấp đầy đủ các loại vắc xin lẻ chất lượng cao cho trẻ em và người lớn. Khách hàng lựa chọn tiêm theo nhu cầu sau khi khám sàng lọc.","icon":"syringe"},{"title":"Gói vắc xin toàn diện","desc":"Thiết kế sẵn các gói vắc xin theo từng độ tuổi (0-6 tháng, 0-12 tháng, phụ nữ mang thai) giúp bảo vệ toàn diện, giữ giá và giữ đủ vắc xin trong cả liệu trình.","icon":"package"},{"title":"Tiêm chủng theo yêu cầu","desc":"Dịch vụ đặt trước vắc xin khan hiếm, bảo quản và giữ riêng vắc xin cho bé của bạn theo đúng thời hạn lịch hẹn.","icon":"calendar-check"},{"title":"Tiêm chủng lưu động","desc":"Hỗ trợ các cơ quan, trường học, doanh nghiệp thực hiện tiêm chủng tập trung cho học sinh, nhân viên tại thực địa an toàn và nhanh chóng.","icon":"truck"}]',
                'rules' => 'required|json',
            ],
            'services_promos' => [
                'type' => 'json',
                'default' => '[{"title":"Miễn phí khám sàng lọc","desc":"100% khách hàng đến Medicare đều được bác sĩ khám sàng lọc và tư vấn kỹ lưỡng hoàn toàn miễn phí."},{"title":"Ưu đãi mua theo gói","desc":"Tiết kiệm lên tới 5 - 10% chi phí so với mua lẻ, hỗ trợ trả góp lãi suất 0% giúp giảm nhẹ gánh nặng tài chính."}]',
                'rules' => 'required|json',
            ],
            'services_commitments' => [
                'type' => 'json',
                'default' => '[{"title":"100% Vắc xin chính hãng","desc":"Vắc xin được nhập khẩu trực tiếp từ các hãng sản xuất dược phẩm uy tín toàn cầu (GSK, Sanofi Pasteur, MSD, Pfizer...)"},{"title":"Bảo quản lạnh GSP","desc":"Hệ thống kho lạnh trung tâm đạt chuẩn bảo quản nghiêm ngặt từ 2 - 8°C với hệ thống giám sát nhiệt độ tự động 24/7."},{"title":"Đội ngũ tận tâm chuyên nghiệp","desc":"Y bác sĩ có chứng chỉ an toàn tiêm chủng, điều dưỡng thao tác tiêm nhẹ nhàng, thấu hiểu tâm lý trẻ em."}]',
                'rules' => 'required|json',
            ],

            // --- TRANG LIÊN HỆ (CONTACT PAGE) ---
            'contact_hero_title' => ['type' => 'string', 'default' => 'Liên Hệ & Chi Nhánh Medicare', 'rules' => 'required|string|max:255'],
            'contact_hero_desc' => ['type' => 'string', 'default' => 'Quý khách vui lòng liên hệ hotline chi nhánh gần nhất để đặt lịch tiêm chủng, tư vấn vắc xin hoặc nhận hỗ trợ chăm sóc y tế.', 'rules' => 'required|string|max:500'],
        ];
    }
}
