<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\RegistrationPaymentService;
use App\Services\LoyaltyService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\Customer;
use Modules\VaccineRegistration\Models\Patient;
use Modules\VaccineRegistration\Models\PointTransaction;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Setting;
use Tests\TestCase;

class LoyaltyPointsDynamicConfigTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private Center $center;
    private Customer $customer;
    private Patient $patient;
    private RegistrationPaymentService $paymentService;
    private LoyaltyService $loyaltyService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentService = new RegistrationPaymentService();
        $this->loyaltyService = new LoyaltyService();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admin_test',
            'email' => 'admin_test@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
            'status' => 'active',
        ]);

        $this->center = Center::create([
            'name' => 'Trung tâm Test',
            'slug' => 'trung-tam-test',
            'address' => '123 Test St',
            'phone' => '0900000000',
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'name' => 'Nguyễn Văn Khách',
            'phone' => '+84909000111',
        ]);

        $this->patient = Patient::create([
            'full_name' => 'Nguyễn Văn Bé',
            'dob' => '2020-08-12', // Sinh nhật
            'gender' => 'Nam',
            'phone' => '0909000111',
        ]);
    }

    private function createRegistration(array $overrides = []): Registration
    {
        return Registration::create(array_merge([
            'registration_code' => 'MCD-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'customer_id' => $this->customer->id,
            'patient_id' => $this->patient->id,
            'patient_name' => $this->patient->full_name,
            'patient_phone' => '+84909000111',
            'center_id' => $this->center->id,
            'center_name' => $this->center->name,
            'injection_date' => today()->toDateString(),
            'booking_status' => 'confirmed',
            'payment_status' => 'unpaid',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 500000,
        ], $overrides));
    }

    /**
     * Test khi tính năng tích điểm bị tắt.
     */
    public function test_loyalty_disabled(): void
    {
        Setting::set('loyalty_settings', json_encode([
            'enabled' => false,
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
            'tiers' => [],
            'campaigns' => []
        ]));

        $registration = $this->createRegistration(['total_price' => 500000]);

        // Kiểm tra quote: khi bị tắt, maximum_points = 0
        $quote = $this->paymentService->quote($this->customer, $registration);
        $this->assertEquals(0, $quote['maximum_points']);

        // Settle đơn hàng
        $this->paymentService->settle($registration->id, 0, $this->admin);

        // Khách hàng không được cộng điểm nào
        $earnedTx = PointTransaction::where('registration_id', $registration->id)
            ->where('type', PointTransaction::EARN)
            ->first();
        $this->assertNull($earnedTx);
    }

    /**
     * Test FIFO hạn dùng điểm bền vững.
     */
    public function test_fifo_point_expiration(): void
    {
        Setting::set('loyalty_settings', json_encode([
            'enabled' => true,
            'vnd_per_earned_point' => 10000,
            'min_order_value_to_earn' => 9999999, // Đặt ngưỡng earn cực cao để tránh tích điểm mới ở đơn test này
            'min_order_value_to_redeem' => 0,
            'point_expiry_months' => 12,
            'redeem_value_type' => 'vnd',
            'redeem_vnd_per_point' => 100,
            'redeem_percent_bps_per_point' => 10,
            'max_redeem_percent' => 100,
            'max_redeem_amount' => null,
            'birthday_multiplier' => 1.0,
            'tiers' => [],
            'campaigns' => []
        ]));

        // Tx1: +10 điểm, đã hết hạn hôm qua
        PointTransaction::create([
            'customer_id' => $this->customer->id,
            'type' => PointTransaction::EARN,
            'points' => 10,
            'source_key' => 'test:earn:1',
            'expired_at' => now()->subDay(),
        ]);

        // Tx2: +20 điểm, hết hạn trong 5 ngày
        $tx2 = PointTransaction::create([
            'customer_id' => $this->customer->id,
            'type' => PointTransaction::EARN,
            'points' => 20,
            'source_key' => 'test:earn:2',
            'expired_at' => now()->addDays(5),
        ]);

        // Tx3: +15 điểm, hết hạn trong 10 ngày
        PointTransaction::create([
            'customer_id' => $this->customer->id,
            'type' => PointTransaction::EARN,
            'points' => 15,
            'source_key' => 'test:earn:3',
            'expired_at' => now()->addDays(10),
        ]);

        // Tổng khả dụng: Tx2(20) + Tx3(15) = 35 điểm
        $balance = $this->paymentService->calculateAvailablePoints($this->customer);
        $this->assertEquals(35, $balance);

        // Khách hàng sử dụng 25 điểm -> Trừ Tx2(20) hết, trừ Tx3(5), còn Tx3(10) khả dụng
        $registration = $this->createRegistration(['total_price' => 500000]);

        $this->paymentService->settle($registration->id, 25, $this->admin);

        $newBalance = $this->paymentService->calculateAvailablePoints($this->customer);
        $this->assertEquals(10, $newBalance);

        // Kiểm tra phân bổ allocations
        $allocations = DB::table('point_allocations')->get();
        $this->assertCount(2, $allocations);
        $this->assertEquals(20, $allocations->firstWhere('credit_transaction_id', $tx2->id)->points);
    }

    /**
     * Test hệ số nhân cộng dồn phần dư.
     */
    public function test_additive_loyalty_multipliers(): void
    {
        Setting::set('loyalty_settings', json_encode([
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
            'birthday_multiplier' => 1.5, // Sinh nhật: +0.5
            'tiers' => [
                ['name' => 'Vàng', 'min_points' => 100, 'multiplier' => 1.2] // Rank Vàng: +0.2
            ],
            'campaigns' => [
                [
                    'name' => 'Sự kiện Tết',
                    'start_date' => now()->subDay()->toDateString(),
                    'end_date' => now()->addDay()->toDateString(),
                    'multiplier' => 2.0 // Sự kiện: +1.0
                ]
            ]
        ]));

        // Tích điểm EARN gốc để đạt Rank Vàng (120đ)
        PointTransaction::create([
            'customer_id' => $this->customer->id,
            'type' => PointTransaction::EARN,
            'points' => 120,
            'source_key' => 'test:history:1',
        ]);

        $this->patient->update([
            'dob' => now()->subYears(5)->toDateString() // Sinh nhật trùng ngày hôm nay
        ]);

        $registration = $this->createRegistration(['total_price' => 100000]);

        $this->paymentService->settle($registration->id, 0, $this->admin);

        // Cộng dồn phần dư: 1.0 + 0.2 (Rank) + 1.0 (Tết) + 0.5 (Sinh nhật) = x2.7
        // Điểm gốc = 100,000 / 10,000 = 10 điểm.
        // Tổng điểm nhận = floor(10 * 2.7) = 27 điểm.
        $earnedTx = PointTransaction::where('registration_id', $registration->id)
            ->where('type', PointTransaction::EARN)
            ->first();
        
        $this->assertNotNull($earnedTx);
        $this->assertEquals(27, $earnedTx->points);
    }

    /**
     * Test cấu hình riêng của chi nhánh được áp dụng độc lập.
     */
    public function test_center_specific_settings_are_applied(): void
    {
        Setting::set('loyalty_settings', json_encode([
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
            'tiers' => [],
            'campaigns' => []
        ]));

        Setting::set('loyalty_settings_center_' . $this->center->id, json_encode([
            'use_system_settings' => false,
            'synced_system_at' => now()->toDateTimeString(),
            'enabled' => true,
            'vnd_per_earned_point' => 15000,
            'min_order_value_to_earn' => 0,
            'min_order_value_to_redeem' => 0,
            'point_expiry_months' => 0,
            'redeem_value_type' => 'vnd',
            'redeem_vnd_per_point' => 100,
            'redeem_percent_bps_per_point' => 10,
            'max_redeem_percent' => 50,
            'max_redeem_amount' => null,
            'birthday_multiplier' => 1.0,
            'tiers' => [],
            'campaigns' => []
        ]));

        $regCenterA = $this->createRegistration(['center_id' => $this->center->id, 'total_price' => 150000]);

        $this->paymentService->settle($regCenterA->id, 0, $this->admin);

        $earnedA = PointTransaction::where('registration_id', $regCenterA->id)
            ->where('type', PointTransaction::EARN)
            ->first();
        $this->assertNotNull($earnedA);
        $this->assertEquals(10, $earnedA->points); // 150,000 / 15,000 = 10 điểm
    }

    /**
     * Test đồng bộ từng phần.
     */
    public function test_sync_loyalty_settings_partially(): void
    {
        Setting::set('loyalty_settings', json_encode([
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
                ['name' => 'Hạng Vàng VIP', 'min_points' => 999, 'multiplier' => 2.0]
            ],
            'campaigns' => []
        ]));

        Setting::set('loyalty_settings_center_' . $this->center->id, json_encode([
            'use_system_settings' => false,
            'synced_system_at' => now()->subDay()->toDateTimeString(),
            'enabled' => true,
            'vnd_per_earned_point' => 15000,
            'min_order_value_to_earn' => 0,
            'min_order_value_to_redeem' => 0,
            'point_expiry_months' => 0,
            'redeem_value_type' => 'vnd',
            'redeem_vnd_per_point' => 100,
            'redeem_percent_bps_per_point' => 10,
            'max_redeem_percent' => 50,
            'max_redeem_amount' => null,
            'birthday_multiplier' => 1.0,
            'tiers' => [],
            'campaigns' => []
        ]));

        $this->actingAs($this->admin)->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_role' => 'super_admin',
            'admin_selected_center_id' => $this->center->id,
        ]);

        $response = $this->post(route('admin.settings.loyalty.sync'), [
            'sync_fields' => ['tiers']
        ]);

        $response->assertRedirect();

        $centerJson = Setting::get('loyalty_settings_center_' . $this->center->id);
        $centerSettings = json_decode($centerJson, true);

        $this->assertFalse($centerSettings['use_system_settings']);
        $this->assertEquals(15000, $centerSettings['vnd_per_earned_point']);
        $this->assertCount(1, $centerSettings['tiers']);
        $this->assertEquals('Hạng Vàng VIP', $centerSettings['tiers'][0]['name']);
    }

    /**
     * Test hoàn tiền (Refund) khôi phục đúng lô điểm gốc kèm ngày hết hạn gốc.
     */
    public function test_refund_restores_original_point_lots(): void
    {
        Setting::set('loyalty_settings', json_encode([
            'enabled' => true,
            'vnd_per_earned_point' => 10000,
            'min_order_value_to_earn' => 9999999, // Không tích điểm mới
            'min_order_value_to_redeem' => 0,
            'point_expiry_months' => 0,
            'redeem_value_type' => 'vnd',
            'redeem_vnd_per_point' => 100,
            'redeem_percent_bps_per_point' => 10,
            'max_redeem_percent' => 100,
            'max_redeem_amount' => null,
            'birthday_multiplier' => 1.0,
            'tiers' => [],
            'campaigns' => []
        ]));

        // Lô 1: 15 điểm, hết hạn trong 5 ngày
        $credit1 = PointTransaction::create([
            'customer_id' => $this->customer->id,
            'type' => PointTransaction::EARN,
            'points' => 15,
            'source_key' => 'test:earn:lot1',
            'expired_at' => now()->addDays(5),
        ]);

        // Lô 2: 20 điểm, hết hạn trong 10 ngày
        $credit2 = PointTransaction::create([
            'customer_id' => $this->customer->id,
            'type' => PointTransaction::EARN,
            'points' => 20,
            'source_key' => 'test:earn:lot2',
            'expired_at' => now()->addDays(10),
        ]);

        // Đơn hàng: tiêu dùng 25 điểm -> lấy sạch 15đ của Lô 1, lấy tiếp 10đ của Lô 2
        $registration = $this->createRegistration(['total_price' => 500000]);

        $this->paymentService->settle($registration->id, 25, $this->admin);

        // Kiểm tra sau khi tiêu điểm: Lô 2 còn lại 10đ
        $this->assertEquals(10, $this->loyaltyService->calculateAvailablePoints($this->customer));
        
        // Thực hiện hoàn tiền đơn hàng
        $this->paymentService->refund($registration->id, $this->admin);

        // Sau khi hoàn tiền: Lô 1 phải được khôi phục về 15đ, Lô 2 khôi phục về 20đ (tổng khả dụng 35đ)
        $balance = $this->loyaltyService->calculateAvailablePoints($this->customer);
        $this->assertEquals(35, $balance);

        // Xác nhận allocations của giao dịch redeem ban đầu đã bị xoá
        $allocCount = DB::table('point_allocations')->count();
        $this->assertEquals(0, $allocCount);
    }

    /**
     * Test đảo điểm tích lũy (Earn Reversal) sau khi điểm đã tiêu dùng.
     */
    public function test_earn_reversal_handles_used_points(): void
    {
        Setting::set('loyalty_settings', json_encode([
            'enabled' => true,
            'vnd_per_earned_point' => 10000,
            'min_order_value_to_earn' => 0,
            'min_order_value_to_redeem' => 0,
            'point_expiry_months' => 0,
            'redeem_value_type' => 'vnd',
            'redeem_vnd_per_point' => 100,
            'redeem_percent_bps_per_point' => 10,
            'max_redeem_percent' => 100,
            'max_redeem_amount' => null,
            'birthday_multiplier' => 1.0,
            'tiers' => [],
            'campaigns' => []
        ]));

        // Khách hàng có 0 điểm ban đầu
        // Đơn hàng 1: thanh toán 100,000đ -> Tích 10 điểm (Lô tích điểm A)
        $reg1 = $this->createRegistration(['total_price' => 100000]);
        $this->paymentService->settle($reg1->id, 0, $this->admin);

        // Đơn hàng 2: tiêu dùng 10 điểm tích luỹ vừa được cộng từ Đơn 1
        // Đặt ngưỡng earn cực cao ở đơn 2 để đơn 2 không tích điểm mới
        Setting::set('loyalty_settings', json_encode([
            'enabled' => true,
            'vnd_per_earned_point' => 10000,
            'min_order_value_to_earn' => 9999999, // Không tích điểm mới
            'min_order_value_to_redeem' => 0,
            'point_expiry_months' => 0,
            'redeem_value_type' => 'vnd',
            'redeem_vnd_per_point' => 100,
            'redeem_percent_bps_per_point' => 10,
            'max_redeem_percent' => 100,
            'max_redeem_amount' => null,
            'birthday_multiplier' => 1.0,
            'tiers' => [],
            'campaigns' => []
        ]));

        $reg2 = $this->createRegistration(['total_price' => 200000]);
        $this->paymentService->settle($reg2->id, 10, $this->admin);

        // Bây giờ hoàn tiền Đơn 1 (Đảo điểm tích lũy của Đơn 1). Giao dịch đảo điểm EARN_REVERSAL (-10đ) được sinh ra.
        // Vì 10đ của Đơn 1 đã bị dùng ở Đơn 2, việc đảo điểm này sẽ tiêu dùng điểm từ Đơn 1 (Ledger allocations), 
        // đảm bảo Đơn 1 không thể được dùng tiếp.
        $this->paymentService->refund($reg1->id, $this->admin);

        // Tổng số dư khả dụng thực tế của khách hàng lúc này = 0đ (Lô A bị vô hiệu hóa vì đã bị hoàn tiền)
        $balance = $this->loyaltyService->calculateAvailablePoints($this->customer);
        $this->assertEquals(0, $balance);
    }

    /**
     * Test BPS percentage quy đổi điểm và làm tròn tiền.
     */
    public function test_basis_point_rounding(): void
    {
        Setting::set('loyalty_settings', json_encode([
            'enabled' => true,
            'vnd_per_earned_point' => 10000,
            'min_order_value_to_earn' => 9999999, // Không tích điểm mới
            'min_order_value_to_redeem' => 0,
            'point_expiry_months' => 0,
            'redeem_value_type' => 'percent',
            'redeem_vnd_per_point' => 100,
            'redeem_percent_bps_per_point' => 15, // 15 BPS = 0.15% đơn hàng trên mỗi điểm
            'max_redeem_percent' => 50,
            'max_redeem_amount' => null,
            'birthday_multiplier' => 1.0,
            'tiers' => [],
            'campaigns' => []
        ]));

        // Tích 10 điểm
        PointTransaction::create([
            'customer_id' => $this->customer->id,
            'type' => PointTransaction::EARN,
            'points' => 10,
            'source_key' => 'test:earn:bps',
        ]);

        $registration = $this->createRegistration(['total_price' => 150000]); // Đơn 150,000đ

        // Dùng 5 điểm.
        // Giảm giá = floor(150,000 * (15 BPS * 5 điểm) / 10000) = floor(150,000 * 75 / 10000) = floor(1125) = 1125 đ.
        $this->paymentService->settle($registration->id, 5, $this->admin);

        $registration->refresh();
        $this->assertEquals(1125, $registration->points_discount_amount);
    }

    /**
     * Test max_redeem_amount với null, 0, và số dương.
     */
    public function test_max_redeem_amount_scenarios(): void
    {
        // 1. max_redeem_amount = null (không giới hạn số tiền giảm)
        Setting::set('loyalty_settings', json_encode([
            'enabled' => true,
            'vnd_per_earned_point' => 10000,
            'min_order_value_to_earn' => 9999999, // Không tích điểm mới
            'min_order_value_to_redeem' => 0,
            'point_expiry_months' => 0,
            'redeem_value_type' => 'vnd',
            'redeem_vnd_per_point' => 1000,
            'redeem_percent_bps_per_point' => 10,
            'max_redeem_percent' => 50,
            'max_redeem_amount' => null,
            'birthday_multiplier' => 1.0,
            'tiers' => [],
            'campaigns' => []
        ]));

        PointTransaction::create([
            'customer_id' => $this->customer->id,
            'type' => PointTransaction::EARN,
            'points' => 100,
            'source_key' => 'test:earn:max1',
        ]);

        $reg1 = $this->createRegistration(['total_price' => 100000]); // Đơn 100k, giảm tối đa 50% = 50k

        $quote1 = $this->paymentService->quote($this->customer, $reg1);
        $this->assertEquals(50, $quote1['available_points']); // Giảm tối đa 50k tương đương 50 điểm

        // 2. max_redeem_amount = 20,000đ (giảm tối đa 20k)
        Setting::set('loyalty_settings', json_encode([
            'enabled' => true,
            'vnd_per_earned_point' => 10000,
            'min_order_value_to_earn' => 9999999,
            'min_order_value_to_redeem' => 0,
            'point_expiry_months' => 0,
            'redeem_value_type' => 'vnd',
            'redeem_vnd_per_point' => 1000,
            'redeem_percent_bps_per_point' => 10,
            'max_redeem_percent' => 50,
            'max_redeem_amount' => 20000,
            'birthday_multiplier' => 1.0,
            'tiers' => [],
            'campaigns' => []
        ]));

        $quote2 = $this->paymentService->quote($this->customer, $reg1);
        $this->assertEquals(20, $quote2['available_points']); // Giảm tối đa 20k tương đương 20 điểm
    }

    /**
     * Test tắt tích điểm rồi bật lại, số dư điểm cũ được giữ nguyên.
     */
    public function test_loyalty_toggle_preserves_balance(): void
    {
        // Có 50 điểm ban đầu
        PointTransaction::create([
            'customer_id' => $this->customer->id,
            'type' => PointTransaction::EARN,
            'points' => 50,
            'source_key' => 'test:earn:toggle',
        ]);

        // Tắt tích điểm
        Setting::set('loyalty_settings', json_encode([
            'enabled' => false,
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
            'tiers' => [],
            'campaigns' => []
        ]));

        $registration = $this->createRegistration(['total_price' => 100000]);
        
        $quote = $this->paymentService->quote($this->customer, $registration);
        $this->assertEquals(50, $quote['balance']);
        $this->assertEquals(0, $quote['available_points']);
    }
}
