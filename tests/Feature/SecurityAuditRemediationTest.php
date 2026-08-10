<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\RegistrationPaymentService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Schedule;
use Modules\VaccineRegistration\Models\Slot;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Models\Customer;
use Modules\VaccineRegistration\Models\PointTransaction;
use Modules\VaccineRegistration\Models\InventoryLot;
use Modules\VaccineRegistration\Models\StockMovement;
use Tests\TestCase;

class SecurityAuditRemediationTest extends TestCase
{
    use DatabaseTransactions;

    private Center $centerA;
    private Center $centerB;
    private Vaccine $vaccine;
    private User $superAdmin;
    private User $branchAdminA;
    private Slot $slotA;
    private Slot $slotB;

    protected function setUp(): void
    {
        parent::setUp();

        $suffix = Str::random(8);

        $this->centerA = Center::create([
            'name' => 'Trung tâm Test A ' . $suffix,
            'slug' => 'test-a-' . strtolower($suffix),
            'address' => 'Địa chỉ A',
            'phone' => '0912345671',
            'is_active' => true,
        ]);

        $this->centerB = Center::create([
            'name' => 'Trung tâm Test B ' . $suffix,
            'slug' => 'test-b-' . strtolower($suffix),
            'address' => 'Địa chỉ B',
            'phone' => '0912345672',
            'is_active' => true,
        ]);

        $this->vaccine = Vaccine::create([
            'name' => 'Vắc xin Test ' . $suffix,
            'price' => 150000,
            'doses' => 1,
            'stock_status' => 'available',
            'disease_prevention' => 'Cúm',
            'age_group' => 'Mọi độ tuổi',
            'origin' => 'Việt Nam',
            'is_active' => true,
        ]);

        foreach ([$this->centerA, $this->centerB] as $center) {
            CenterVaccine::create([
                'center_id' => $center->id,
                'vaccine_id' => $this->vaccine->id,
                'price' => 150000,
                'stock_status' => 'available',
                'stock_quantity' => 100,
                'is_active' => true,
            ]);
        }

        $this->superAdmin = User::create([
            'name' => 'Super Admin ' . $suffix,
            'username' => 'super_admin_' . strtolower($suffix),
            'email' => 'super_' . strtolower($suffix) . '@example.test',
            'password' => bcrypt('AdminPassword123!'),
            'role' => 'super_admin',
            'is_active' => true,
            'status' => 'active',
        ]);

        $this->branchAdminA = User::create([
            'name' => 'Branch Admin A ' . $suffix,
            'username' => 'branch_admin_a_' . strtolower($suffix),
            'email' => 'branch_a_' . strtolower($suffix) . '@example.test',
            'password' => bcrypt('BranchPassword123!'),
            'role' => 'branch_admin',
            'center_id' => $this->centerA->id,
            'is_active' => true,
            'status' => 'active',
        ]);

        $scheduleA = Schedule::create([
            'center_id' => $this->centerA->id,
            'date' => today()->addDays(5)->toDateString(),
            'is_active' => true,
        ]);

        $this->slotA = Slot::create([
            'schedule_id' => $scheduleA->id,
            'start_at' => '08:00',
            'end_at' => '09:00',
            'capacity' => 10,
            'reserved_count' => 0,
            'is_active' => true,
        ]);

        $scheduleB = Schedule::create([
            'center_id' => $this->centerB->id,
            'date' => today()->addDays(5)->toDateString(),
            'is_active' => true,
        ]);

        $this->slotB = Slot::create([
            'schedule_id' => $scheduleB->id,
            'start_at' => '08:00',
            'end_at' => '09:00',
            'capacity' => 10,
            'reserved_count' => 0,
            'is_active' => true,
        ]);
    }

    private function actingAsAdmin(User $user)
    {
        return $this->actingAs($user)->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $user->id,
            'admin_role' => $user->role,
            'admin_center_id' => $user->center_id,
            'admin_password_hash' => md5($user->password),
        ]);
    }

    /**
     * VAC-001: Tra cứu lịch hẹn che giấu PII khi chỉ dùng SĐT và mở khoá khi có mã đặt lịch.
     */
    public function test_vac_001_booking_lookup_masks_pii_when_no_code(): void
    {
        $customer = Customer::create(['name' => 'Nguyễn Văn Anh', 'phone' => '+84912345600']);
        $registration = Registration::create([
            'registration_code' => 'MCD-ABCDEFGH-1',
            'customer_id' => $customer->id,
            'patient_name' => 'Nguyễn Văn Anh',
            'patient_phone' => '+84912345600',
            'patient_dob' => '2000-01-01',
            'patient_gender' => 'Nam',
            'center_id' => $this->centerA->id,
            'center_name' => $this->centerA->name,
            'injection_date' => today()->addDays(5)->toDateString(),
            'slot_id' => $this->slotA->id,
            'status' => 'pending',
            'booking_status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 150000,
        ]);

        // Tra cứu chỉ bằng SĐT
        $response = $this->post(route('booking.lookup.submit'), [
            'phone' => '0912345600',
        ]);

        $response->assertStatus(200);
        $response->assertViewHas('registrations', function ($regs) use ($registration) {
            $reg = $regs->first();
            return $reg->is_masked === true 
                && $reg->display_name === 'Nguyễn * Anh' 
                && $reg->display_code === 'MCD-***-1'
                && $reg->display_price === '*** đ';
        });

        // Tra cứu bằng SĐT + Mã đặt lịch khớp
        $responseWithCode = $this->post(route('booking.lookup.submit'), [
            'phone' => '0912345600',
            'registration_code' => 'MCD-ABCDEFGH-1',
        ]);

        $responseWithCode->assertStatus(200);
        $responseWithCode->assertViewHas('registrations', function ($regs) use ($registration) {
            $reg = $regs->first();
            return $reg->is_masked === false 
                && $reg->display_name === 'Nguyễn Văn Anh' 
                && $reg->display_code === 'MCD-ABCDEFGH-1'
                && str_contains($reg->display_price, '150.000');
        });
    }

    /**
     * VAC-002: Thêm validate patients tối đa 5 người.
     */
    public function test_vac_002_post_register_limits_max_patients(): void
    {
        $patients = [];
        for ($i = 0; $i < 6; $i++) {
            $patients[] = [
                'name' => 'Bệnh nhân ' . $i,
                'phone' => '091234570' . $i,
                'dob' => '2000-01-01',
                'gender' => 'Nam',
                'address' => 'Địa chỉ ' . $i,
                'vaccine_ids' => [$this->vaccine->id],
            ];
        }

        $response = $this->withSession(['selected_center_id' => $this->centerA->id])
            ->post(route('register.post'), [
                'patients' => $patients,
                'slot_id' => $this->slotA->id,
            ]);

        $response->assertSessionHasErrors('patients');
    }

    /**
     * VAC-003: Xử lý idempotency đặt lịch bị lỗi đuôi _0.
     */
    public function test_vac_003_post_register_handles_idempotency_with_suffix_0(): void
    {
        $idempotencyKey = 'key-' . Str::random(12);

        $patients = [
            [
                'name' => 'Bệnh nhân Test Idem',
                'phone' => '0912345800',
                'dob' => '2000-01-01',
                'gender' => 'Nam',
                'address' => 'Địa chỉ Test',
                'vaccine_ids' => [$this->vaccine->id],
            ]
        ];

        // Lần gửi đầu tiên: tạo mới lịch
        $response1 = $this->withSession(['selected_center_id' => $this->centerA->id])
            ->post(route('register.post'), [
                'patients' => $patients,
                'slot_id' => $this->slotA->id,
                'idempotency_key' => $idempotencyKey,
            ]);

        $response1->assertRedirect();
        
        $this->assertDatabaseHas('registrations', [
            'idempotency_key' => $idempotencyKey . '_0',
            'patient_name' => 'Bệnh nhân Test Idem',
        ]);

        // Lần gửi thứ 2 (retry): phát hiện idempotency và trả về kết quả cũ
        $response2 = $this->withSession(['selected_center_id' => $this->centerA->id])
            ->post(route('register.post'), [
                'patients' => $patients,
                'slot_id' => $this->slotA->id,
                'idempotency_key' => $idempotencyKey,
            ]);

        $response2->assertRedirect(route('register.success'));
    }

    /**
     * VAC-006: Ngăn chặn thanh toán (settle) đơn đã hủy.
     */
    public function test_vac_006_cannot_settle_cancelled_registration(): void
    {
        $customer = Customer::create(['name' => 'Khách hủy', 'phone' => '+84912345900']);
        $registration = Registration::create([
            'registration_code' => 'MCD-' . strtoupper(Str::random(8)) . '-1',
            'customer_id' => $customer->id,
            'patient_name' => 'Bệnh nhân hủy',
            'patient_phone' => '+84912345900',
            'center_id' => $this->centerA->id,
            'center_name' => $this->centerA->name,
            'injection_date' => today()->addDays(5)->toDateString(),
            'booking_status' => 'cancelled',
            'payment_status' => 'unpaid',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 150000,
        ]);

        $service = new RegistrationPaymentService();

        $this->expectException(ValidationException::class);
        $service->settle($registration->id, 0, $this->superAdmin);
    }

    /**
     * VAC-007: Lọc point transactions theo center đối với Branch Admin.
     */
    public function test_vac_007_point_transactions_scoped_to_center_for_branch_admin(): void
    {
        $customer = Customer::create(['name' => 'Khách tích điểm', 'phone' => '+84912345111']);

        // Phải tạo 1 registration thuộc center A cho khách hàng này để BranchAdmin của center A có quyền truy cập thông tin
        Registration::create([
            'registration_code' => 'MCD-' . strtoupper(Str::random(8)) . '-1',
            'customer_id' => $customer->id,
            'patient_name' => 'Khách tích điểm',
            'patient_phone' => '+84912345111',
            'center_id' => $this->centerA->id,
            'center_name' => $this->centerA->name,
            'injection_date' => today()->addDays(5)->toDateString(),
            'booking_status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 150000,
        ]);

        // Giao dịch điểm ở Center A
        PointTransaction::create([
            'customer_id' => $customer->id,
            'center_id' => $this->centerA->id,
            'type' => 'earn',
            'points' => 10,
            'source_key' => 'earn-a-' . Str::random(8),
        ]);

        // Giao dịch điểm ở Center B
        PointTransaction::create([
            'customer_id' => $customer->id,
            'center_id' => $this->centerB->id,
            'type' => 'earn',
            'points' => 15,
            'source_key' => 'earn-b-' . Str::random(8),
        ]);

        // Xem khách hàng với quyền BranchAdmin của Center A
        $response = $this->actingAsAdmin($this->branchAdminA)
            ->get(route('admin.customers.show', $customer->id));

        $response->assertStatus(200);
        $response->assertViewHas('transactions', function ($txs) {
            // Chỉ thấy giao dịch tại Center A (1 giao dịch chi tiết)
            return $txs->count() === 1 && $txs->first()->center_id === $this->centerA->id;
        });
    }

    /**
     * VAC-008: Thống nhất thông báo lỗi đăng nhập.
     */
    public function test_vac_008_login_error_messages_are_unified(): void
    {
        // 1. Tài khoản không tồn tại
        $response1 = $this->post(route('admin.login'), [
            'username' => 'nonexistent_admin',
            'password' => 'WrongPassword!',
        ]);
        $response1->assertSessionHasErrors(['auth_failed' => 'Tên đăng nhập hoặc mật khẩu không chính xác.']);

        // 2. Tài khoản inactive
        $inactiveUser = User::create([
            'name' => 'Inactive User',
            'username' => 'inactive_admin',
            'email' => 'inactive@example.test',
            'password' => bcrypt('AdminPassword123!'),
            'role' => 'branch_admin',
            'center_id' => $this->centerA->id,
            'is_active' => false,
            'status' => 'inactive',
        ]);

        $response2 = $this->post(route('admin.login'), [
            'username' => 'inactive_admin',
            'password' => 'AdminPassword123!',
        ]);
        $response2->assertSessionHasErrors(['auth_failed' => 'Tên đăng nhập hoặc mật khẩu không chính xác.']);
    }

    /**
     * VAC-009: Thu hồi session cũ khi mật khẩu thay đổi.
     */
    public function test_vac_009_middleware_invalidates_session_on_password_change(): void
    {
        $user = User::create([
            'name' => 'User Đổi Pass',
            'username' => 'user_doi_pass',
            'email' => 'doi_pass@example.test',
            'password' => bcrypt('OldPassword123!'),
            'role' => 'super_admin',
            'is_active' => true,
            'status' => 'active',
        ]);

        // Đăng nhập thành công
        $session = [
            'admin_logged_in' => true,
            'admin_user_id' => $user->id,
            'admin_role' => $user->role,
            'admin_password_hash' => md5($user->password),
        ];

        // Gửi request khi mật khẩu chưa đổi -> Phải truy cập thành công
        $responseBefore = $this->withSession($session)
            ->get(route('admin.dashboard'));
        $responseBefore->assertStatus(200);

        // Đổi mật khẩu trong database
        $user->update(['password' => bcrypt('NewPassword123!')]);

        // Gửi request với session cũ -> Bị middleware đẩy ra trang login
        $responseAfter = $this->withSession($session)
            ->get(route('admin.dashboard'));
        $responseAfter->assertRedirect(route('admin.login.show'));
    }

    /**
     * VAC-010: Ngăn chặn Admin cập nhật reserved_count trực tiếp.
     */
    public function test_vac_010_cannot_update_reserved_count_directly(): void
    {
        $response = $this->actingAsAdmin($this->superAdmin)
            ->put(route('admin.slots.update', $this->slotA->id), [
                'capacity' => 15,
                'reserved_count' => 8, // Thử gửi trị mới phá hoại
            ]);

        $response->assertRedirect();
        
        // Trong DB, reserved_count vẫn phải là 0
        $this->assertSame(0, $this->slotA->fresh()->reserved_count);
    }

    /**
     * VAC-011: Sửa available_quantity của lô vắc xin tự động tạo StockMovement.
     */
    public function test_vac_011_updating_lot_quantity_creates_stock_movement(): void
    {
        $lot = InventoryLot::create([
            'vaccine_id' => $this->vaccine->id,
            'center_id' => $this->centerA->id,
            'lot_number' => 'LOT-001',
            'initial_quantity' => 10,
            'available_quantity' => 10,
            'reserved_quantity' => 0,
            'expires_at' => today()->addYear(),
            'status' => 'active',
        ]);

        $response = $this->actingAsAdmin($this->superAdmin)
            ->put(route('admin.inventory-lots.update', $lot->id), [
                'available_quantity' => 15, // Tăng thêm 5
            ]);

        $response->assertRedirect();
        
        $this->assertSame(15, $lot->fresh()->available_quantity);
        $this->assertDatabaseHas('stock_movements', [
            'inventory_lot_id' => $lot->id,
            'type' => 'adjustment',
            'quantity' => 5,
        ]);
    }

    /**
     * VAC-019 & VAC-020: Check-in/Screening/Administer y tế bị scope và trừ tồn kho lô vắc xin.
     */
    public function test_vac_019_and_vac_020_workflow_branch_isolation_and_lot_deduction(): void
    {
        $customer = Customer::create(['name' => 'Bệnh nhân tiêm', 'phone' => '+84912345222']);
        
        // Lịch hẹn tại Center B
        $registrationB = Registration::create([
            'registration_code' => 'MCD-' . strtoupper(Str::random(8)) . '-1',
            'customer_id' => $customer->id,
            'patient_name' => 'Bệnh nhân B',
            'patient_phone' => '+84912345222',
            'center_id' => $this->centerB->id,
            'center_name' => $this->centerB->name,
            'injection_date' => today()->addDays(5)->toDateString(),
            'slot_id' => $this->slotB->id,
            'status' => 'pending',
            'booking_status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 150000,
        ]);
        $registrationB->vaccines()->attach([$this->vaccine->id => ['price' => 150000, 'quantity' => 1]]);

        // Lịch hẹn tại Center A (chi nhánh của branchAdminA)
        $registrationA = Registration::create([
            'registration_code' => 'MCD-' . strtoupper(Str::random(8)) . '-2',
            'customer_id' => $customer->id,
            'patient_name' => 'Bệnh nhân A',
            'patient_phone' => '+84912345222',
            'center_id' => $this->centerA->id,
            'center_name' => $this->centerA->name,
            'injection_date' => today()->addDays(5)->toDateString(),
            'slot_id' => $this->slotA->id,
            'status' => 'pending',
            'booking_status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 150000,
        ]);
        $registrationA->vaccines()->attach([$this->vaccine->id => ['price' => 150000, 'quantity' => 1]]);

        // 1. Branch Admin A cố check-in lịch của Center B -> Bị chặn 403
        $responseCheckinForbidden = $this->actingAsAdmin($this->branchAdminA)
            ->post(route('admin.registrations.check-in', $registrationB->id));
        $responseCheckinForbidden->assertStatus(403);

        // 2. Thao tác đúng chi nhánh A: Check-in -> Screening (Eligible) -> Administer
        // Check-in
        $responseCheckinOk = $this->actingAsAdmin($this->branchAdminA)
            ->post(route('admin.registrations.check-in', $registrationA->id));
        $responseCheckinOk->assertRedirect();
        $this->assertSame('checked_in', $registrationA->fresh()->status);

        // Khám sàng lọc thành Eligible
        $responseScreening = $this->actingAsAdmin($this->branchAdminA)
            ->post(route('admin.registrations.screening', $registrationA->id), [
                'screening_status' => 'eligible',
                'screening_notes' => 'Đủ điều kiện tiêm',
            ]);
        $responseScreening->assertRedirect();
        $this->assertSame('eligible', $registrationA->fresh()->screening_status);

        // Tạo lô vắc xin tại chi nhánh A
        $lot = InventoryLot::create([
            'vaccine_id' => $this->vaccine->id,
            'center_id' => $this->centerA->id,
            'lot_number' => 'LOT-A-01',
            'initial_quantity' => 5,
            'available_quantity' => 5,
            'reserved_quantity' => 0,
            'expires_at' => today()->addYear(),
            'status' => 'active',
        ]);

        // Thực hiện tiêm vắc xin (Administer)
        $responseAdminister = $this->actingAsAdmin($this->branchAdminA)
            ->post(route('admin.registrations.administer', $registrationA->id), [
                'vaccine_id' => $this->vaccine->id,
                'inventory_lot_id' => $lot->id,
            ]);

        $responseAdminister->assertRedirect();
        
        $registrationA = $registrationA->fresh();
        $this->assertSame('completed', $registrationA->status);

        // Lô vắc xin tại chi nhánh phải bị trừ 1 khả dụng
        $this->assertSame(4, $lot->fresh()->available_quantity);

        // Phải xuất hiện stock movement của việc xuất kho
        $this->assertDatabaseHas('stock_movements', [
            'inventory_lot_id' => $lot->id,
            'type' => 'export',
            'quantity' => 1,
        ]);
    }
}
