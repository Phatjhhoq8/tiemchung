<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $reviewedAt = '2026-08-10';
        $content = [
            'Hexaxim' => [
                'administration_route' => 'Tiêm bắp.',
                'detailed_schedule' => 'Từ 6 tuần tuổi: lịch cơ bản 2 hoặc 3 liều; khoảng cách và liều nhắc thực hiện theo hướng dẫn sản phẩm và chương trình tiêm chủng áp dụng.',
                'contraindications' => 'Quá mẫn với thành phần hoặc liều trước; bệnh não không rõ nguyên nhân sau vắc xin chứa ho gà.',
                'adverse_effects' => 'Đau, đỏ, sưng chỗ tiêm; sốt, quấy khóc, buồn ngủ hoặc giảm ăn.',
                'warnings' => 'Hoãn khi sốt hoặc nhiễm trùng cấp vừa đến nặng; cần sẵn sàng xử trí phản vệ.',
                'source_reference_url' => 'https://extranet.who.int/prequal/sites/default/files/vwa_vaccine/FVP-P-285_Hexaxim_1dose_SanofiW_PI-2025.pdf',
            ],
            'Rotarix' => [
                'administration_route' => 'Chỉ dùng đường uống; không tiêm.',
                'detailed_schedule' => 'Hai liều từ 6 tuần tuổi, cách nhau ít nhất 4 tuần; hoàn tất trước 24 tuần tuổi.',
                'contraindications' => 'Quá mẫn; tiền sử lồng ruột; dị tật tiêu hóa làm tăng nguy cơ lồng ruột; suy giảm miễn dịch kết hợp nặng.',
                'adverse_effects' => 'Có thể gặp tiêu chảy hoặc kích thích; cần theo dõi dấu hiệu lồng ruột.',
                'warnings' => 'Hoãn khi sốt cấp nặng, tiêu chảy hoặc nôn; rửa tay kỹ sau thay tã.',
                'source_reference_url' => 'https://extranet.who.int/prequal/sites/default/files/vwa_vaccine/fvp-p-62_rota-iquid_1dose_gsk_pi_tube-2024.pdf',
            ],
            'BCG' => [
                'administration_route' => 'Tiêm trong da; không tiêm dưới da hoặc vào mạch máu.',
                'detailed_schedule' => 'Một liều theo chương trình tiêm chủng và hướng dẫn sử dụng hiện hành; liều dùng phụ thuộc tuổi.',
                'contraindications' => 'Đã nhiễm lao; sốt hoặc bệnh cấp; suy giảm miễn dịch, bệnh ác tính hoặc điều trị ức chế miễn dịch.',
                'adverse_effects' => 'Có thể sốt nhẹ, nổi hạch hoặc áp-xe tại chỗ; nốt tiêm thường tiến triển thành sẹo nhỏ.',
                'warnings' => 'Phải tiêm đúng trong da; khám ngay nếu có dấu hiệu nhiễm BCG toàn thân hoặc viêm hạch mủ.',
                'source_reference_url' => 'https://dav.gov.vn/bcg-n1146.html',
            ],
            'Prevenar 13' => [
                'administration_route' => 'Tiêm bắp.',
                'detailed_schedule' => 'Số liều phụ thuộc tuổi, tiền sử tiêm và nguy cơ; trẻ nhỏ thường cần loạt cơ bản và liều nhắc.',
                'contraindications' => 'Quá mẫn với hoạt chất, tá dược hoặc giải độc tố bạch hầu.',
                'adverse_effects' => 'Đau, đỏ, sưng chỗ tiêm; sốt, quấy khóc, buồn ngủ hoặc giảm ăn.',
                'warnings' => 'Hoãn khi sốt cấp nặng; vắc xin không bảo vệ ngoài các týp huyết thanh có trong sản phẩm.',
                'source_reference_url' => 'https://www.ema.europa.eu/en/documents/product-information/prevenar-13-epar-product-information_en.pdf',
            ],
            'Prevenar 20' => [
                'administration_route' => 'Tiêm bắp.',
                'detailed_schedule' => 'Người lớn thường dùng một liều; lịch trẻ em phụ thuộc tuổi và tiền sử tiêm.',
                'contraindications' => 'Quá mẫn với hoạt chất, tá dược hoặc protein mang CRM197.',
                'adverse_effects' => 'Đau, đỏ, sưng chỗ tiêm; mệt, đau cơ hoặc đau đầu; trẻ nhỏ có thể sốt và quấy khóc.',
                'warnings' => 'Hoãn khi sốt cấp nặng; thận trọng khi có rối loạn đông máu hoặc suy giảm miễn dịch.',
                'source_reference_url' => 'https://www.ema.europa.eu/en/documents/product-information/prevenar-20-epar-product-information_en.pdf',
            ],
            'Influvac Tetra' => [
                'administration_route' => 'Tiêm bắp hoặc tiêm dưới da sâu.',
                'detailed_schedule' => 'Từ 6 tháng tuổi: một liều mỗi mùa cúm; trẻ dưới 9 tuổi chưa từng tiêm cúm cần liều thứ hai sau ít nhất 4 tuần.',
                'contraindications' => 'Quá mẫn với thành phần hoặc lượng tồn dư được nêu trong hướng dẫn sản phẩm; hoãn khi sốt hoặc nhiễm trùng cấp.',
                'adverse_effects' => 'Đau chỗ tiêm, mệt hoặc đau đầu; trẻ nhỏ có thể sốt, quấy khóc hoặc giảm ăn.',
                'warnings' => 'Không tiêm vào mạch máu; thận trọng khi giảm tiểu cầu, rối loạn đông máu hoặc suy giảm miễn dịch.',
                'source_reference_url' => 'https://extranet.who.int/prequal/sites/default/files/vwa_vaccine/FVP-P-460_InfluenzaQIV_1dose_Abbot_PI-NH2025-26.pdf',
            ],
            'Vaxigrip Tetra' => [
                'administration_route' => 'Tiêm bắp hoặc tiêm dưới da.',
                'detailed_schedule' => 'Tiêm hằng năm; trẻ 6 tháng đến 8 tuổi chưa từng tiêm cúm cần hai liều cách nhau ít nhất 4 tuần.',
                'contraindications' => 'Quá mẫn với thành phần hoặc lượng tồn dư được nêu trong hướng dẫn sản phẩm.',
                'adverse_effects' => 'Đau chỗ tiêm, đau đầu, đau cơ, khó chịu hoặc sốt.',
                'warnings' => 'Hoãn khi bệnh cấp hoặc sốt vừa đến cao; không tiêm vào mạch máu.',
                'source_reference_url' => 'https://extranet.who.int/prequal/sites/default/files/vwa_vaccine/FVP-P-238_Vaxigrip_TIV_SanofiW_PI-2025_2.pdf',
            ],
            'Qdenga' => [
                'administration_route' => 'Tiêm dưới da.',
                'detailed_schedule' => 'Từ 4 tuổi: hai liều, liều thứ hai sau 3 tháng.',
                'contraindications' => 'Quá mẫn; suy giảm miễn dịch; mang thai hoặc đang cho con bú.',
                'adverse_effects' => 'Đau hoặc đỏ chỗ tiêm, đau đầu, đau cơ, khó chịu hoặc sốt.',
                'warnings' => 'Đây là vắc xin sống; tiếp tục thực hiện biện pháp phòng muỗi và khám khi có dấu hiệu sốt xuất huyết.',
                'source_reference_url' => 'https://www.ema.europa.eu/en/documents/product-information/qdenga-epar-product-information_en.pdf',
            ],
            'Gardasil 9' => [
                'administration_route' => 'Tiêm bắp.',
                'detailed_schedule' => 'Bắt đầu 9-14 tuổi: hai liều; bắt đầu từ 15 tuổi hoặc suy giảm miễn dịch: ba liều. Khoảng cách thực hiện theo hướng dẫn sản phẩm.',
                'contraindications' => 'Quá mẫn với thành phần, nấm men hoặc liều trước.',
                'adverse_effects' => 'Đau, sưng hoặc đỏ chỗ tiêm; đau đầu; có thể sốt, buồn nôn hoặc ngất.',
                'warnings' => 'Theo dõi 15 phút sau tiêm; không điều trị nhiễm HPV đang có và không thay thế sàng lọc ung thư cổ tử cung.',
                'source_reference_url' => 'https://www.fda.gov/media/90064/download',
            ],
            'Shingrix' => [
                'administration_route' => 'Tiêm bắp vùng cơ delta.',
                'detailed_schedule' => 'Hai liều cách 2-6 tháng; người suy giảm miễn dịch có thể tiêm liều hai sau 1-2 tháng.',
                'contraindications' => 'Tiền sử phản ứng dị ứng nặng với thành phần hoặc liều trước.',
                'adverse_effects' => 'Đau, đỏ, sưng chỗ tiêm; đau cơ, mệt, đau đầu, rét run, sốt hoặc triệu chứng tiêu hóa.',
                'warnings' => 'Cần sẵn sàng xử trí phản vệ; sản phẩm không dùng để phòng thủy đậu nguyên phát.',
                'source_reference_url' => 'https://www.fda.gov/media/108597/download',
            ],
        ];

        foreach ($content as $name => $fields) {
            DB::table('vaccines')
                ->where('name', $name)
                ->whereNull('source_reference_url')
                ->update($fields + ['source_review_date' => $reviewedAt]);
        }
    }

    public function down(): void
    {
        // Keep administrator-managed content intact on rollback.
    }
};
