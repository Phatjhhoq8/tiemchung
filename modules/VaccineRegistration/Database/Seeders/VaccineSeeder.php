<?php

namespace Modules\VaccineRegistration\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\VaccineRegistration\Models\Vaccine;

class VaccineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vaccines = [
            [
                'name' => 'Infanrix Hexa (Bỉ)',
                'price' => 1045000,
                'description' => 'Vắc xin 6 trong 1 phòng ngừa 6 bệnh: Bạch hầu, ho gà, uốn ván, bại liệt, viêm gan B và các bệnh xâm lấn do Haemophilus influenzae týp b (Hib).',
                'disease_prevention' => 'Bạch hầu, ho gà, uốn ván, bại liệt, viêm gan B, Hib',
                'age_group' => 'Trẻ từ 2 tháng đến 2 tuổi',
                'origin' => 'GlaxoSmithKline (Bỉ)',
                'image' => 'infanrix.jpg',
            ],
            [
                'name' => 'Hexaxim (Pháp)',
                'price' => 1045000,
                'description' => 'Vắc xin 6 trong 1 thế hệ mới dạng pha sẵn tiện dụng, giúp giảm thời gian tiêm chủng và hạn chế sai sót trong quá trình pha hoàn nguyên.',
                'disease_prevention' => 'Bạch hầu, ho gà, uốn ván, bại liệt, viêm gan B, Hib',
                'age_group' => 'Trẻ từ 2 tháng đến 2 tuổi',
                'origin' => 'Sanofi Pasteur (Pháp)',
                'image' => 'hexaxim.jpg',
            ],
            [
                'name' => 'Gardasil 9 (Mỹ)',
                'price' => 2950000,
                'description' => 'Vắc xin phòng ngừa 9 chủng virus HPV gây ung thư cổ tử cung, ung thư âm hộ, ung thư hậu môn, mụn cóc sinh dục ở cả nam và nữ.',
                'disease_prevention' => 'Ung thư cổ tử cung, sùi mào gà, HPV',
                'age_group' => 'Trẻ em và người lớn từ 9 đến 45 tuổi',
                'origin' => 'MSD (Mỹ)',
                'image' => 'gardasil9.jpg',
            ],
            [
                'name' => 'Prevenar 13 (Bỉ)',
                'price' => 1290000,
                'description' => 'Vắc xin phế cầu 13 phòng các bệnh viêm phổi, viêm màng não, viêm tai giữa và nhiễm trùng huyết do vi khuẩn phế cầu gây ra.',
                'disease_prevention' => 'Viêm phổi, viêm màng não, phế cầu khuẩn',
                'age_group' => 'Trẻ từ 6 tuần tuổi và người lớn',
                'origin' => 'Pfizer (Bỉ)',
                'image' => 'prevenar13.jpg',
            ],
            [
                'name' => 'Synflorix (Bỉ)',
                'price' => 1045000,
                'description' => 'Vắc xin phòng các bệnh do phế cầu khuẩn (10 chủng) gây ra cho trẻ nhỏ như viêm phổi, viêm màng não và viêm tai giữa cấp.',
                'disease_prevention' => 'Viêm phổi, viêm màng não, viêm tai giữa',
                'age_group' => 'Trẻ từ 6 tuần tuổi đến 5 tuổi',
                'origin' => 'GlaxoSmithKline (Bỉ)',
                'image' => 'synflorix.jpg',
            ],
            [
                'name' => 'Vaxigrip Tetra (Pháp)',
                'price' => 356000,
                'description' => 'Vắc xin cúm tứ giá thế hệ mới, chứa 4 chủng virus cúm giúp bảo vệ rộng hơn và hiệu quả hơn trước dịch cúm hàng năm.',
                'disease_prevention' => 'Cúm mùa (4 chủng)',
                'age_group' => 'Trẻ từ 6 tháng tuổi và người lớn',
                'origin' => 'Sanofi Pasteur (Pháp)',
                'image' => 'vaxigrip.jpg',
            ],
            [
                'name' => 'Rotarix (Bỉ)',
                'price' => 825000,
                'description' => 'Vắc xin dạng uống phòng ngừa viêm dạ dày ruột cấp do Rotavirus gây ra (bệnh tiêu chảy cấp nguy hiểm ở trẻ nhỏ).',
                'disease_prevention' => 'Tiêu chảy cấp do Rota virus',
                'age_group' => 'Trẻ từ 6 tuần tuổi đến 6 tháng tuổi',
                'origin' => 'GlaxoSmithKline (Bỉ)',
                'image' => 'rotarix.jpg',
            ],
            [
                'name' => 'Menactra (Mỹ)',
                'price' => 1290000,
                'description' => 'Vắc xin phòng bệnh viêm màng não, nhiễm trùng huyết và các thể bệnh xâm lấn khác do não mô cầu khuẩn các tuýp A, C, Y, W-135 gây ra.',
                'disease_prevention' => 'Viêm màng não do não mô cầu ACYW',
                'age_group' => 'Trẻ từ 9 tháng tuổi đến người lớn 55 tuổi',
                'origin' => 'Sanofi Pasteur (Mỹ)',
                'image' => 'menactra.jpg',
            ],
            [
                'name' => 'Verorab (Pháp)',
                'price' => 355000,
                'description' => 'Vắc xin phòng bệnh dại tế bào Vero tinh chế, dùng phòng ngừa bệnh dại cho người tiếp xúc với động vật nghi dại hoặc tiêm ngừa chủ động.',
                'disease_prevention' => 'Bệnh dại',
                'age_group' => 'Mọi lứa tuổi',
                'origin' => 'Sanofi Pasteur (Pháp)',
                'image' => 'verorab.jpg',
            ],
            [
                'name' => 'Mengoc BC (Cuba)',
                'price' => 295000,
                'description' => 'Vắc xin phòng bệnh viêm màng não mũ do não mô cầu khuẩn nhóm B và C gây ra, có hiệu quả phòng ngừa cao và an toàn.',
                'disease_prevention' => 'Viêm màng não do não mô cầu BC',
                'age_group' => 'Trẻ từ 3 tháng tuổi đến người lớn 45 tuổi',
                'origin' => 'Finlay Institute (Cuba)',
                'image' => 'mengoc.jpg',
            ],
            [
                'name' => 'Boostrix (Bỉ)',
                'price' => 745000,
                'description' => 'Vắc xin nhắc lại phòng 3 bệnh Bạch hầu - Uốn ván - Ho gà cho trẻ em từ 4 tuổi trở lên và người lớn, đặc biệt phụ nữ mang thai.',
                'disease_prevention' => 'Bạch hầu, ho gà, uốn ván (nhắc lại)',
                'age_group' => 'Trẻ từ 4 tuổi và người lớn',
                'origin' => 'GlaxoSmithKline (Bỉ)',
                'image' => 'boostrix.jpg',
            ],
            [
                'name' => 'Varilrix (Bỉ)',
                'price' => 945000,
                'description' => 'Vắc xin phòng bệnh thủy đậu (trái rạ) hiệu quả cao, giúp tránh các biến chứng nguy hiểm của thủy đậu như viêm màng não, viêm phổi.',
                'disease_prevention' => 'Bệnh thủy đậu (trái rạ)',
                'age_group' => 'Trẻ từ 9 tháng tuổi và người lớn',
                'origin' => 'GlaxoSmithKline (Bỉ)',
                'image' => 'varilrix.jpg',
            ]
        ];

        foreach ($vaccines as $vaccine) {
            Vaccine::create($vaccine);
        }
    }
}
