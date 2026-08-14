<?php

use Illuminate\Database\Migrations\Migration;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Vaccine;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Delete all existing centers and center vaccines
        CenterVaccine::query()->delete();
        Center::query()->delete();

        // 2. Insert real centers
        $centers = [
            [
                'name' => 'Medicare Cờ Đỏ',
                'slug' => 'medicare-co-do',
                'address' => 'Ấp Thới Bình, Xã Cờ Đỏ, Huyện Cờ Đỏ, TP. Cần Thơ (Cổng Bệnh Viện Quân Dân Y TP. Cần Thơ)',
                'phone' => '0938 603 839',
                'zalo_phone' => '0938 603 839',
                'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3928.751677322967!2d105.4327456!3d10.0864561!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a099002385f45d%3A0x794ab6e6dab32c92!2zUGjDsm5nIHRpw6ptIGNow7puZyB2YWNjaW4gTWVkaWNhcmUgQ-G7nSDEkOG7jw!5e0!3m2!1svi!2s',
                'working_hours' => '7:00 – 17:00 (Tất cả các ngày trong tuần kể cả Thứ 7, Chủ Nhật & Ngày Lễ)',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Medicare Phong Điền',
                'slug' => 'medicare-phong-dien',
                'address' => 'Ấp Nhơn Lộc 1, Xã Phong Điền, Huyện Phong Điền, TP. Cần Thơ (Phòng khám Đa Khoa Nhơn Ái)',
                'phone' => '0923331233',
                'zalo_phone' => '0923331233',
                'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3929.8660855219965!2d105.6666113!3d9.9947996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a08f00316d3267%3A0x8c35692cf5b2a45!2zUGjDsm5nIHRpw6ptIGNow7puZyBWYWNjaW4gTWVkaWNhcmUgUGhvbmcgxJBp4buBbg!5e0!3m2!1svi!2s',
                'working_hours' => '7:00 – 17:00 (Tất cả các ngày trong tuần kể cả Thứ 7, Chủ Nhật & Ngày Lễ)',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Medicare Trà Nóc',
                'slug' => 'medicare-tra-noc',
                'address' => '97 Đường Lê Hồng Phong, Phường Thới An Đông, Quận Bình Thủy, TP. Cần Thơ',
                'phone' => '0921 331 233',
                'zalo_phone' => '0921331233',
                'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3928.34960520286!2d105.7077651!3d10.0751336!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a087004a1f2cf1%3A0xdb6f978053d1b9a1!2zUGjDsm5nIHRpw6ptIGNow7puZyBWYWNjaW4gTWVkaWNhcmUgVHLDoCBOw7Nj!5e0!3m2!1svi!2s',
                'working_hours' => '7:00 – 17:00 (Tất cả các ngày trong tuần kể cả Thứ 7, Chủ Nhật & Ngày Lễ)',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Medicare Vị Thanh',
                'slug' => 'medicare-vi-thanh',
                'address' => '615B, Đường Trần Hưng Đạo, KV 10, Phường III, TP. Vị Thanh, Tỉnh Hậu Giang (kế CDC Hậu Giang cũ)',
                'phone' => '0921 989 798',
                'zalo_phone' => '0921989798',
                'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3932.2285191834273!2d105.464303!3d9.7850027!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a0f30501a35bb9%3A0xe5a3c61d56e0766c!2zNjE1QiDEkMaw4budbmcgVHLhuqduIEh1bmcgxJDhuqFvLCBQaMaw4budbmcgMywgVsG7iyBUaGFuaCwgSOG6rXUgR2lhbmc!5e0!3m2!1svi!2s',
                'working_hours' => '7:00 – 17:00 (Tất cả các ngày trong tuần kể cả Thứ 7, Chủ Nhật & Ngày Lễ)',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($centers as $center) {
            $savedCenter = Center::create($center);

            // Seed branch vaccine pricing
            Vaccine::query()->get()->each(function (Vaccine $vaccine) use ($savedCenter) {
                $multiplier = 1 + (max(0, (int) $savedCenter->sort_order - 1) * 0.01);
                $branchPrice = (int) (round(($vaccine->price * $multiplier) / 1000) * 1000);
                $branchSalePrice = $vaccine->sale_price
                    ? (int) (round(($vaccine->sale_price * $multiplier) / 1000) * 1000)
                    : null;

                CenterVaccine::firstOrCreate(
                    ['center_id' => $savedCenter->id, 'vaccine_id' => $vaccine->id],
                    [
                        'price' => $branchPrice,
                        'sale_price' => $branchSalePrice,
                        'stock_quantity' => 0,
                        'stock_status' => $vaccine->stock_status ?? 'available',
                        'is_active' => true,
                        'is_featured' => (bool) $vaccine->is_featured,
                        'sort_order' => (int) $vaccine->sort_order,
                    ]
                );
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rolling back of production data updates to avoid losing new records
    }
};
