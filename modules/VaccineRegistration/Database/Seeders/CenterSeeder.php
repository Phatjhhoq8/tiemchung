<?php
/**
 * Chức năng: CenterSeeder nạp danh sách 2 chi nhánh trung tâm tiêm chủng chính thức thuộc hệ thống Medicare.
 * Lý do chỉnh sửa: Quản lý 2 chi nhánh hoạt động thực tế theo đúng yêu cầu hệ thống Medicare.
 */

namespace Modules\VaccineRegistration\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Vaccine;

class CenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $centers = [
            [
                'name' => 'Medicare Cờ Đỏ',
                'slug' => 'medicare-co-do',
                'legacy_names' => ['Medicare Cờ Đỏ (Chi nhánh 1)'],
                'address' => 'Cổng Bệnh viện Quân Dân Y TP Cần Thơ, Ấp Thới Bình, Xã Cờ Đỏ, TP. Cần Thơ',
                'phone' => '0938 60 38 39',
                'zalo_phone' => '0938 60 38 39',
                'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3929.569472382601!2d105.4746028!3d10.052327!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a09d3da5ea6e0b%3A0xc3fde93a9fa93e22!2zQuG7h25oIHZp4buHbiBRdcOibiBEw6JuIFkgQ-G6p24gVGjGoQ!5e0!3m2!1svi!2s!4v1717387200000!5m2!1svi!2s',
                'working_hours' => '7:00 – 17:00 (Tất cả các ngày trong tuần kể cả Thứ 7, Chủ Nhật & Ngày Lễ)',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Medicare Thới Lai',
                'slug' => 'medicare-thoi-lai',
                'legacy_names' => ['Medicare Thới Lai (Chi nhánh 2)'],
                'address' => 'Thị trấn Thới Lai, Huyện Thới Lai, TP. Cần Thơ',
                'phone' => '0932 477 184',
                'zalo_phone' => '0932 477 184',
                'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3929.289295557766!2d105.5786524!3d10.1009873!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a08e1a8bb2081f%3A0xc545367b60098df6!2zVGjhu4sgdHLhuqVuIFRo4bubaSBMYWksIFRo4bubaSBMYWksIEPhuqduIFRoxqEsIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1717387200000!5m2!1svi!2s',
                'working_hours' => '7:00 – 17:00 (Tất cả các ngày trong tuần kể cả Thứ 7, Chủ Nhật & Ngày Lễ)',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Medicare Phong Điền',
                'slug' => 'medicare-phong-dien',
                'address' => 'Ấp Nhơn Lộc 1, Xã Phong Điền, TP Cần Thơ (Phòng khám Đa Khoa Nhơn Ái)',
                'phone' => '0923331233',
                'zalo_phone' => '0923331233',
                'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3929.81665485434!2d105.6791223!3d9.9989914!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a08823b123cfc3%3A0x89e0ee2513f568f6!2zUGjDsm5nIEtow6FtIMSQYSBLYWjhu49hIE5oxqFuIMCBSQ!5e0!3m2!1svi!2s!4v1717387200000!5m2!1svi!2s',
                'working_hours' => '7:00 – 17:00 (Tất cả các ngày trong tuần kể cả Thứ 7, Chủ Nhật & Ngày Lễ)',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Medicare Trà Nóc',
                'slug' => 'medicare-tra-noc',
                'address' => 'Trà Nóc, TP Cần Thơ',
                'phone' => '092 1331233',
                'zalo_phone' => '092 1331233',
                'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3928.32832812345!2d105.7198765!3d10.0765432!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a087e5b6154321%3A0x9d4b53ea24a66a12!2zVHLDoCBOw7NjLCBCw6xuaCBUaOG7p3ksIEPhuqduIFRoxqEsIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1717387200000!5m2!1svi!2s',
                'working_hours' => '7:00 – 17:00 (Tất cả các ngày trong tuần kể cả Thứ 7, Chủ Nhật & Ngày Lễ)',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($centers as $center) {
            $center['slug'] = $center['slug'] ?? Str::slug($center['name']);
            $legacyNames = $center['legacy_names'] ?? [];
            unset($center['legacy_names']);

            $savedCenter = Center::where('slug', $center['slug'])->first()
                ?: Center::whereIn('name', $legacyNames)->first();

            if ($savedCenter) {
                $savedCenter->update($center);
            } else {
                $savedCenter = Center::create($center);
            }

            Vaccine::query()->get()->each(function (Vaccine $vaccine) use ($savedCenter) {
                CenterVaccine::firstOrCreate(
                    ['center_id' => $savedCenter->id, 'vaccine_id' => $vaccine->id],
                    [
                        'price' => $vaccine->price,
                        'sale_price' => $vaccine->sale_price,
                        'stock_quantity' => 0,
                        'stock_status' => $vaccine->stock_status ?? 'available',
                        'is_active' => true,
                        'is_featured' => (bool) $vaccine->is_featured,
                        'sort_order' => (int) $vaccine->sort_order,
                    ]
                );
            });
        }

        CenterVaccine::whereNotIn('center_id', Center::pluck('id'))->delete();
    }
}
