<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settingsToUpdate = [
            'address' => 'Medicare Cờ Đỏ | Medicare Phong Điền | Medicare Trà Nóc | Medicare Vị Thanh',
            'address_draft' => 'Medicare Cờ Đỏ | Medicare Phong Điền | Medicare Trà Nóc | Medicare Vị Thanh',
            'about_story_desc' => 'Được thành lập từ năm 2016, Medicare bắt đầu với sứ mệnh mang dịch vụ tiêm chủng an toàn, chất lượng cao và chi phí hợp lý đến gần hơn với người dân tại Cần Thơ và Hậu Giang. Trải qua gần 10 năm phát triển, chúng tôi tự hào trở thành điểm tựa sức khỏe đáng tin cậy cho hàng chục ngàn gia đình, liên tục cải tiến chất lượng và nâng cao dịch vụ chăm sóc y tế.',
            'about_story_desc_draft' => 'Được thành lập từ năm 2016, Medicare bắt đầu với sứ mệnh mang dịch vụ tiêm chủng an toàn, chất lượng cao và chi phí hợp lý đến gần hơn với người dân tại Cần Thơ và Hậu Giang. Trải qua gần 10 năm phát triển, chúng tôi tự hào trở thành điểm tựa sức khỏe đáng tin cậy cho hàng chục ngàn gia đình, liên tục cải tiến chất lượng và nâng cao dịch vụ chăm sóc y tế.',
            'about_val5_desc' => 'Cung cấp vaccine với mức giá bình ổn, hỗ trợ tối đa người dân tiếp cận với y tế chất lượng cao.',
            'about_val5_desc_draft' => 'Cung cấp vaccine với mức giá bình ổn, hỗ trợ tối đa người dân tiếp cận với y tế chất lượng cao.',
            'about_stat_branches' => '04',
            'about_stat_branches_draft' => '04',
        ];

        foreach ($settingsToUpdate as $key => $value) {
            DB::table('settings')
                ->updateOrInsert(
                    ['key' => $key],
                    [
                        'value' => $value,
                        'updated_at' => now()
                    ]
                );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for static content updates
    }
};
