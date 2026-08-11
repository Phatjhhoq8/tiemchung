<?php
/**
 * Chức năng: SettingSeeder nạp cấu hình hệ thống thương hiệu Medicare quản lý chuỗi nhiều chi nhánh.
 * Lý do chỉnh sửa: Thay một địa chỉ trụ sở chính thành danh sách các chi nhánh trong hệ thống.
 */

namespace Modules\VaccineRegistration\Database\Seeders;

use Database\Seeders\Concerns\PreventsProductionSeeding;
use Illuminate\Database\Seeder;
use Modules\VaccineRegistration\Models\Setting;

class SettingSeeder extends Seeder
{
    use PreventsProductionSeeding;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->assertSafeSeedingTarget();

        $settings = [
            'site_name' => 'Medicare',
            'brand_title' => 'Hệ Thống Tiêm Chủng Medicare',
            'hotline' => '0938 60 38 39',
            'hotline_2' => '0932 477 184',
            'email' => 'cskh@medicare.vn',
            'address' => 'Chi nhánh 1: Cờ Đỏ (Cổng BV Quân Dân Y TP Cần Thơ) | Chi nhánh 2: Thới Lai (Thị trấn Thới Lai, TP Cần Thơ)',
            'working_hours' => 'Tất cả các ngày trong tuần (Từ 7:00 - 17:00 kể cả Chủ Nhật và ngày Lễ)',
            'footer_text' => '© 2026 Medicare - Hệ Thống Tiêm Chủng Vắc Xin Trẻ Em và Người Lớn với chuỗi chi nhánh phục vụ tận tâm.',
            'about_hero_title' => 'Giới Thiệu Hệ Thống Tiêm Chủng Medicare',
            'about_hero_desc' => 'Đơn vị y tế uy tín hàng đầu cung cấp giải pháp phòng bệnh toàn diện bằng vắc xin chất lượng cao cho trẻ em và người lớn tại Cần Thơ.',
            'about_story_title' => 'Hành trình Bảo vệ Sức khỏe Cộng đồng',
            'about_story_desc' => 'Được thành lập từ năm 2016, Medicare bắt đầu với sứ mệnh mang dịch vụ tiêm chủng an toàn, chất lượng cao và chi phí hợp lý đến gần hơn với người dân tại các huyện ngoại thành Cần Thơ như Cờ Đỏ và Thới Lai. Trải qua gần 10 năm phát triển, chúng tôi tự hào trở thành điểm tựa sức khỏe đáng tin cậy cho hàng chục ngàn gia đình, liên tục cải tiến chất lượng và nâng cao dịch vụ chăm sóc y tế.',
            'about_stat_exp' => '10+',
            'about_stat_exp_lbl' => 'Năm Kinh Nghiệm',
            'about_stat_clients' => '50,000+',
            'about_stat_clients_lbl' => 'Khách Hàng Hài Lòng',
            'about_stat_branches' => '02',
            'about_stat_branches_lbl' => 'Trung Tâm Tiêm Chủng',
            'about_mission_title' => 'Sứ Mệnh Của Chúng Tôi',
            'about_mission_desc' => 'Mang lại dịch vụ tiêm chủng an toàn tuyệt đối, vaccine chính hãng chất lượng cao với chi phí hợp lý cho mọi gia đình. Giúp cộng đồng chủ động phòng bệnh truyền nhiễm nguy hiểm.',
            'about_vision_title' => 'Tầm Nhìn Phát Triển',
            'about_vision_desc' => 'Trở thành hệ thống tiêm chủng dịch vụ uy tín hàng đầu Cần Thơ và Đồng bằng sông Cửu Long, không ngừng cải tiến trang thiết bị và ứng dụng sổ tiêm điện tử thông minh.',
            'about_values_desc' => 'Mọi hoạt động y tế của hệ thống tiêm chủng Medicare đều tuân thủ các chuẩn mực chất lượng khắt khe nhất để bảo vệ an toàn cho sức khỏe gia đình bạn.',
            'about_val1_icon' => 'shield-check',
            'about_val1_title' => 'An Toàn Vượt Trội',
            'about_val1_desc' => 'Quy trình tiêm chủng an toàn 5 bước chuẩn Bộ Y tế, 100% bác sĩ khám sàng lọc cẩn thận trước tiêm và theo dõi chặt chẽ sau tiêm.',
            'about_val2_icon' => 'award',
            'about_val2_title' => 'Uy Tín Hàng Đầu',
            'about_val2_desc' => 'Cam kết vắc xin nhập khẩu chính hãng từ các tập đoàn dược phẩm lớn trên thế giới như GSK, MSD, Sanofi Pasteur, Pfizer.',
            'about_val3_icon' => 'heart',
            'about_val3_title' => 'Tận Tâm Phục Vụ',
            'about_val3_desc' => 'Đội ngũ y bác sĩ, điều dưỡng ân cần, thấu hiểu tâm lý trẻ em và người lớn, tạo cảm giác thân thiện, nhẹ nhàng khi tiêm.',
            'about_val4_icon' => 'snowflake',
            'about_val4_title' => 'Hệ Thống Lạnh GSP',
            'about_val4_desc' => 'Hệ thống kho lạnh và tủ bảo quản vắc xin đạt chuẩn GSP nghiêm ngặt từ 2 - 8°C giúp giữ trọn vẹn chất lượng và hiệu quả.',
            'about_val5_icon' => 'scale',
            'about_val5_title' => 'Trách Nhiệm Xã Hội',
            'about_val5_desc' => 'Cung cấp vaccine với mức giá bình ổn, hỗ trợ tối đa người dân tại khu vực Cờ Đỏ & Thới Lai tiếp cận với y tế chất lượng cao.',
            'about_val6_icon' => 'database',
            'about_val6_title' => 'Sổ Tiêm Điện Tử',
            'about_val6_desc' => 'Quản lý lịch sử tiêm chủng đồng bộ trên hệ thống, tự động nhắn tin nhắc lịch tiêm chủng định kỳ cho trẻ đúng hẹn.',
            'about_member1_name' => 'ThS. BS. Nguyễn Minh Đức',
            'about_member1_role' => 'Giám Đốc Y Khoa',
            'about_member1_avatar' => 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=256&q=80',
            'about_team_members' => json_encode([
                [
                    'name' => 'ThS. BS. Nguyễn Minh Đức',
                    'role' => 'Giám đốc chuyên môn tiêm chủng',
                    'avatar' => '/images/avt_pktn.png',
                    'zalo' => '0938603839',
                ],
                [
                    'name' => 'BS. Trần Thị Thanh Trúc',
                    'role' => 'Bác sĩ khám sàng lọc trước tiêm',
                    'avatar' => '/images/avt_pkpđ.png',
                    'zalo' => '0932477184',
                ],
                [
                    'name' => 'CNĐD. Lê Hoàng Phúc',
                    'role' => 'Điều dưỡng trưởng phòng tiêm',
                    'avatar' => '/images/logo.png',
                    'zalo' => '0909001122',
                ],
            ], JSON_UNESCAPED_UNICODE),
            'loyalty_settings' => json_encode([
                'enabled' => true,
                'vnd_per_earned_point' => 10000,
                'min_order_value_to_earn' => 0,
                'min_order_value_to_redeem' => 0,
                'point_expiry_months' => 0,
                'redeem_value_type' => 'vnd',
                'redeem_vnd_per_point' => 100,
                'redeem_percent_bps_per_point' => 10,
                'max_redeem_percent' => 50,
                'max_redeem_amount' => null,
                'birthday_multiplier' => 1.0,
                'tiers' => [
                    ['name' => 'Bạc', 'min_points' => 100, 'multiplier' => 1.1],
                    ['name' => 'Vàng', 'min_points' => 500, 'multiplier' => 1.2],
                    ['name' => 'Kim Cương', 'min_points' => 1000, 'multiplier' => 1.5]
                ],
                'campaigns' => []
            ], JSON_UNESCAPED_UNICODE),
        ];

        foreach ($settings as $key => $value) {
            if ($key === 'loyalty_settings') {
                Setting::firstOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            } else {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }
    }
}
