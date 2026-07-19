<?php
/**
 * Chức năng: VaccineSeeder nạp danh mục vắc xin lẻ và gói vắc xin mẫu.
 * Lý do tạo/chỉnh sửa: Nạp đầy đủ các vắc xin bổ sung từ tài liệu (Qdenga, Shingrix, Gardasil 4) và các gói vắc xin động.
 */

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
            // --- VẮC XIN LẺ ---
            [
                'name' => 'Qdenga (Nhật Bản)',
                'price' => 1250000,
                'type' => 'single',
                'doses' => 2,
                'description' => 'Vắc xin phòng bệnh Sốt xuất huyết Dengue do cả 4 tuýp huyết thanh virus gây ra. Giảm nguy cơ nhiễm bệnh lên đến 80% và giảm tỷ lệ nhập viện do sốt xuất huyết đến 90%.',
                'disease_prevention' => 'Sốt xuất huyết Dengue',
                'age_group' => 'Trẻ từ 4 tuổi và người lớn',
                'origin' => 'Takeda (Nhật Bản)',
                'image' => 'qdenga.jpg',
            ],
            [
                'name' => 'Gardasil 9 (Mỹ)',
                'price' => 2950000,
                'type' => 'single',
                'doses' => 3,
                'description' => 'Vắc xin phòng ngừa 9 chủng virus HPV (6, 11, 16, 18, 31, 33, 45, 52 và 58) gây ung thư cổ tử cung, ung thư âm hộ, âm đạo, hậu môn và sùi mào gà sinh dục ở cả nam và nữ.',
                'disease_prevention' => 'Ung thư cổ tử cung, sùi mào gà, HPV',
                'age_group' => 'Trẻ em và người lớn từ 9 đến 45 tuổi',
                'origin' => 'MSD (Mỹ)',
                'image' => 'gardasil9.jpg',
            ],
            [
                'name' => 'Gardasil (Mỹ)',
                'price' => 1790000,
                'type' => 'single',
                'doses' => 3,
                'description' => 'Vắc xin phòng ngừa 4 tuýp virus HPV (6, 11, 16 và 18) gây sùi mào gà sinh dục, ung thư cổ tử cung ở nữ giới.',
                'disease_prevention' => 'Ung thư cổ tử cung, sùi mào gà do HPV',
                'age_group' => 'Người từ 9 đến 26 tuổi',
                'origin' => 'MSD (Mỹ)',
                'image' => 'gardasil.jpg',
            ],
            [
                'name' => 'Shingrix (Bỉ)',
                'price' => 3590000,
                'type' => 'single',
                'doses' => 2,
                'description' => 'Vắc xin tái tổ hợp phòng bệnh Zona thần kinh (giời leo) và biến chứng đau dây thần kinh sau Zona kéo dài. Hiệu quả bảo vệ vượt trội trên 90%.',
                'disease_prevention' => 'Bệnh Zona thần kinh (giời leo)',
                'age_group' => 'Người lớn từ 50 tuổi trở lên hoặc người từ 18 tuổi có nguy cơ cao',
                'origin' => 'GlaxoSmithKline (Bỉ)',
                'image' => 'shingrix.jpg',
            ],
            [
                'name' => 'Infanrix Hexa (Bỉ)',
                'price' => 1045000,
                'type' => 'single',
                'doses' => 3,
                'description' => 'Vắc xin 6 trong 1 phòng ngừa 6 bệnh truyền nhiễm nguy hiểm đầu đời: Bạch hầu, ho gà, uốn ván, bại liệt, viêm gan B và viêm màng não/viêm phổi do Hib.',
                'disease_prevention' => 'Bạch hầu, ho gà, uốn ván, bại liệt, viêm gan B, Hib',
                'age_group' => 'Trẻ từ 2 tháng đến 2 tuổi',
                'origin' => 'GlaxoSmithKline (Bỉ)',
                'image' => 'infanrix.jpg',
            ],
            [
                'name' => 'Hexaxim (Pháp)',
                'price' => 1045000,
                'type' => 'single',
                'doses' => 3,
                'description' => 'Vắc xin 6 trong 1 dạng pha sẵn tiện dụng, giúp giảm thời gian tiêm chủng, hạn chế sai sót và tạo sự thoải mái tối đa cho bé yêu.',
                'disease_prevention' => 'Bạch hầu, ho gà, uốn ván, bại liệt, viêm gan B, Hib',
                'age_group' => 'Trẻ từ 2 tháng đến 2 tuổi',
                'origin' => 'Sanofi Pasteur (Pháp)',
                'image' => 'hexaxim.jpg',
            ],
            [
                'name' => 'Prevenar 13 (Bỉ)',
                'price' => 1290000,
                'type' => 'single',
                'doses' => 1,
                'description' => 'Vắc xin phế cầu khuẩn phòng các bệnh viêm phổi nặng, viêm màng não nguy hiểm, viêm tai giữa cấp và nhiễm trùng huyết do phế cầu gây ra.',
                'disease_prevention' => 'Viêm phổi, viêm màng não do phế cầu khuẩn',
                'age_group' => 'Trẻ từ 6 tuần tuổi và người lớn',
                'origin' => 'Pfizer (Bỉ)',
                'image' => 'prevenar13.jpg',
            ],
            [
                'name' => 'Vaxigrip Tetra (Pháp)',
                'price' => 356000,
                'type' => 'single',
                'doses' => 1,
                'description' => 'Vắc xin cúm tứ giá chứa 4 chủng virus cúm mùa mới nhất hàng năm theo khuyến cáo WHO, giúp bảo vệ toàn diện hệ hô hấp của người cao tuổi và trẻ nhỏ.',
                'disease_prevention' => 'Cúm mùa (4 chủng)',
                'age_group' => 'Trẻ từ 6 tháng tuổi và người lớn',
                'origin' => 'Sanofi Pasteur (Pháp)',
                'image' => 'vaxigrip.jpg',
            ],
            [
                'name' => 'Rotarix (Bỉ)',
                'price' => 825000,
                'type' => 'single',
                'doses' => 2,
                'description' => 'Vắc xin dạng uống phòng ngừa viêm dạ dày ruột cấp do Rotavirus gây ra - nguyên nhân hàng đầu gây tiêu chảy cấp mất nước nguy kịch ở trẻ sơ sinh.',
                'disease_prevention' => 'Tiêu chảy cấp do Rotavirus',
                'age_group' => 'Trẻ từ 6 tuần tuổi đến 6 tháng tuổi',
                'origin' => 'GlaxoSmithKline (Bỉ)',
                'image' => 'rotarix.jpg',
            ],
            [
                'name' => 'Menactra (Mỹ)',
                'price' => 1290000,
                'type' => 'single',
                'doses' => 1,
                'description' => 'Vắc xin phòng bệnh viêm màng não và nhiễm trùng huyết tối cấp do não mô cầu khuẩn các nhóm huyết thanh A, C, Y, W-135 gây ra.',
                'disease_prevention' => 'Viêm màng não do não mô cầu ACYW',
                'age_group' => 'Trẻ từ 9 tháng tuổi đến người lớn 55 tuổi',
                'origin' => 'Sanofi Pasteur (Mỹ)',
                'image' => 'menactra.jpg',
            ],
            [
                'name' => 'Boostrix (Bỉ)',
                'price' => 745000,
                'type' => 'single',
                'doses' => 1,
                'description' => 'Vắc xin tiêm nhắc lại phòng 3 bệnh Bạch hầu - Ho gà - Uốn ván giúp củng cố hệ miễn dịch cho trẻ từ 4 tuổi và người trưởng thành.',
                'disease_prevention' => 'Bạch hầu, ho gà, uốn ván (tiêm nhắc)',
                'age_group' => 'Trẻ từ 4 tuổi và người lớn',
                'origin' => 'GlaxoSmithKline (Bỉ)',
                'image' => 'boostrix.jpg',
            ],
            [
                'name' => 'Varilrix (Bỉ)',
                'price' => 945000,
                'type' => 'single',
                'doses' => 2,
                'description' => 'Vắc xin phòng bệnh thủy đậu (trái rạ), phòng ngừa hiệu quả các biến chứng nhiễm trùng da, viêm phổi và viêm não do virus thủy đậu gây ra.',
                'disease_prevention' => 'Bệnh thủy đậu (trái rạ)',
                'age_group' => 'Trẻ từ 9 tháng tuổi và người lớn',
                'origin' => 'GlaxoSmithKline (Bỉ)',
                'image' => 'varilrix.jpg',
            ],

            // --- GÓI VẮC XIN ---
            [
                'name' => 'Gói Vắc Xin Người Cao Tuổi & Bệnh Nền',
                'price' => 5200000,
                'type' => 'package',
                'doses' => 4,
                'description' => 'Gói vắc xin thiết yếu khuyến nghị cho người cao tuổi và người có bệnh nền để bảo vệ hệ thần kinh và hô hấp. Gồm 1 mũi Phế cầu Prevenar 13, 1 mũi Cúm Vaxigrip và 2 mũi Zona Shingrix.',
                'disease_prevention' => 'Cúm mùa, phế cầu khuẩn (viêm phổi), Zona thần kinh',
                'age_group' => 'Người lớn từ 50 tuổi trở lên',
                'origin' => 'Medicare (Nhập khẩu)',
                'image' => 'goi_nguoicaotuoi.jpg',
            ],
            [
                'name' => 'Gói Vắc Xin Toàn Diện Cho Trẻ Em (0 - 2 tuổi)',
                'price' => 16500000,
                'type' => 'package',
                'doses' => 12,
                'description' => 'Gói tiêm trọn gói bảo vệ bé yêu vững chắc trong 2 năm đầu đời. Cam kết giữ đầy đủ tất cả vắc xin (6in1, Rota, Phế cầu, Thủy đậu...) theo đúng phác đồ tiêm.',
                'disease_prevention' => 'Bạch hầu, ho gà, uốn ván, bại liệt, viêm gan B, Hib, phế cầu, tiêu chảy Rota, thủy đậu',
                'age_group' => 'Trẻ em dưới 2 tuổi',
                'origin' => 'Medicare (Nhập khẩu)',
                'image' => 'goi_treem.jpg',
            ],
            [
                'name' => 'Gói Vắc Xin Tiền Hôn Nhân & Sức Khỏe Sinh Sản',
                'price' => 4500000,
                'type' => 'package',
                'doses' => 6,
                'description' => 'Gói vắc xin bảo vệ cặp đôi trước khi kết hôn và chuẩn bị mang thai. Gồm vắc xin ngừa HPV (sùi mào gà/ung thư CTC), viêm gan B, và sởi - quai bị - rubella.',
                'disease_prevention' => 'HPV (sùi mào gà, ung thư CTC), viêm gan B, sởi, quai bị, rubella',
                'age_group' => 'Nam và nữ từ 9 đến 26 tuổi',
                'origin' => 'Medicare (Nhập khẩu)',
                'image' => 'goi_tienhonnhan.jpg',
            ]
        ];

        foreach ($vaccines as $vaccine) {
            Vaccine::updateOrCreate(['name' => $vaccine['name']], $vaccine);
        }
    }
}
