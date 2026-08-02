<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\VaccineRegistration\Models\Article;
use Modules\VaccineRegistration\Models\Banner;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Customer;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Vaccine;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuditLogsAndResourceStatusTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $branchAdmin;
    protected Center $center;

    protected function setUp(): void
    {
        parent::setUp();

        $this->center = Center::create([
            'name' => 'Medicare Chi Nhánh Test M5',
            'slug' => 'medicare-chi-nhanh-test-m5-' . uniqid(),
            'address' => '123 Đường Test, Quận 1',
            'phone' => '0901234567',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Super Admin M5',
            'username' => 'superadmin_m5_' . uniqid(),
            'email' => 'superadmin_m5_' . uniqid() . '@medicare.vn',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
            'is_active' => true,
            'status' => 'active',
        ]);

        $this->branchAdmin = User::create([
            'name' => 'Branch Admin M5',
            'username' => 'branchadmin_m5_' . uniqid(),
            'email' => 'branchadmin_m5_' . uniqid() . '@medicare.vn',
            'password' => bcrypt('password123'),
            'role' => 'branch_admin',
            'center_id' => $this->center->id,
            'is_active' => true,
            'status' => 'active',
        ]);
    }

    protected function loginAs(User $user)
    {
        return $this->actingAs($user)->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $user->id,
            'admin_role' => $user->role,
            'admin_center_id' => $user->center_id,
        ]);
    }

    #[Test]
    public function test_audit_logger_creates_audit_log_entry()
    {
        $this->loginAs($this->superAdmin);

        $log = AuditLogger::log(
            action: 'price_update',
            resourceType: 'vaccine',
            resourceId: 1,
            oldValues: ['price' => 500000],
            newValues: ['price' => 550000],
            centerId: $this->center->id
        );

        $this->assertDatabaseHas('audit_logs', [
            'id' => $log->id,
            'action' => 'price_update',
            'resource_type' => 'vaccine',
            'resource_id' => '1',
            'actor_id' => $this->superAdmin->id,
            'center_id' => $this->center->id,
        ]);

        $this->assertEquals(['price' => 500000], $log->fresh()->old_values);
        $this->assertEquals(['price' => 550000], $log->fresh()->new_values);
    }

    #[Test]
    public function test_audit_log_generated_on_vaccine_price_update()
    {
        $vaccine = Vaccine::create([
            'name' => 'Vắc xin Cúm Test M5',
            'price' => 300000,
            'sale_price' => 280000,
            'type' => 'single',
            'doses' => 1,
            'stock_status' => 'available',
            'disease_prevention' => 'Phòng bệnh cúm mùa',
            'age_group' => 'Trẻ em và người lớn',
            'origin' => 'Pháp',
            'is_active' => true,
        ]);

        $response = $this->loginAs($this->superAdmin)->put(route('admin.vaccines.update', $vaccine->id), [
            'name' => 'Vắc xin Cúm Test M5',
            'center_id' => $this->center->id,
            'price' => 350000,
            'sale_price' => 320000,
            'type' => 'single',
            'doses' => 1,
            'stock_status' => 'available',
            'disease_prevention' => 'Phòng bệnh cúm mùa',
            'age_group' => 'Trẻ em và người lớn',
            'origin' => 'Pháp',
        ]);

        $response->assertRedirect(route('admin.vaccines.index', ['center_id' => $this->center->id]));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'price_update',
            'resource_type' => 'vaccine',
            'resource_id' => (string) $vaccine->id,
        ]);
    }

    #[Test]
    public function test_audit_log_generated_on_stock_update()
    {
        $vaccine = Vaccine::create([
            'name' => 'Vắc xin HPV Test M5',
            'price' => 1200000,
            'type' => 'single',
            'doses' => 3,
            'stock_status' => 'available',
            'disease_prevention' => 'Phòng ung thư cổ tử cung',
            'age_group' => '9-26 tuổi',
            'origin' => 'Mỹ',
            'is_active' => true,
        ]);

        $response = $this->loginAs($this->branchAdmin)->post(route('admin.stock.store'), [
            'center_id' => $this->center->id,
            'vaccine_id' => $vaccine->id,
            'type' => 'import',
            'quantity' => 10,
            'unit_price' => 1200000,
            'note' => 'Nhập hàng đợt 1',
        ]);

        $response->assertRedirect(route('admin.stock.index', ['center_id' => $this->center->id]));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'stock_update',
            'resource_type' => 'stock',
        ]);
    }

    #[Test]
    public function test_audit_log_generated_on_manual_settlement_and_refund()
    {
        $vaccine = Vaccine::create([
            'name' => 'Vắc xin Phế Cầu Test M5',
            'price' => 1000000,
            'type' => 'single',
            'doses' => 1,
            'stock_status' => 'available',
            'disease_prevention' => 'Phòng phế cầu',
            'age_group' => 'Trẻ em',
            'origin' => 'Bỉ',
            'is_active' => true,
        ]);

        CenterVaccine::create([
            'center_id' => $this->center->id,
            'vaccine_id' => $vaccine->id,
            'price' => 1000000,
            'stock_quantity' => 5,
            'stock_status' => 'available',
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'name' => 'Nguyễn Văn Test',
            'phone' => '+849' . str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT),
        ]);
        $registration = Registration::create([
            'registration_code' => 'TESTREG' . rand(1000, 9999),
            'customer_id' => $customer->id,
            'patient_name' => 'Nguyễn Văn Test',
            'patient_dob' => '1995-05-15',
            'patient_gender' => 'Nam',
            'patient_phone' => '0988777666',
            'patient_address' => 'Hà Nội',
            'center_id' => $this->center->id,
            'center_name' => $this->center->name,
            'injection_date' => now()->addDays(2)->toDateString(),
            'status' => 'pending',
            'booking_status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 1000000,
        ]);

        $registration->vaccines()->attach($vaccine->id, ['price' => 1000000, 'quantity' => 1]);

        $response1 = $this->loginAs($this->branchAdmin)->post(route('admin.registrations.settle', $registration->id), [
            'redeem_points' => 0,
        ]);
        $response1->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'registration_settled',
            'resource_type' => 'registration',
            'resource_id' => (string) $registration->id,
        ]);

        $response2 = $this->loginAs($this->branchAdmin)->post(route('admin.registrations.refund', $registration->id));
        $response2->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'refund_issued',
            'resource_type' => 'registration',
            'resource_id' => (string) $registration->id,
        ]);
    }

    #[Test]
    public function test_soft_deactivation_on_vaccine()
    {
        $vaccine = Vaccine::create([
            'name' => 'Vắc xin Soft Delete Test M5',
            'price' => 500000,
            'type' => 'single',
            'doses' => 1,
            'stock_status' => 'available',
            'disease_prevention' => 'Test',
            'age_group' => 'Tất cả',
            'origin' => 'Việt Nam',
            'is_active' => true,
        ]);

        $response = $this->loginAs($this->superAdmin)->delete(route('admin.vaccines.destroy', $vaccine->id));
        $response->assertRedirect(route('admin.vaccines.index'));

        // DB record exists, is_active is false
        $this->assertDatabaseHas('vaccines', [
            'id' => $vaccine->id,
            'is_active' => false,
        ]);

        // Test direct model delete() call
        $vaccine2 = Vaccine::create([
            'name' => 'Vắc xin Soft Delete Test 2 M5',
            'price' => 500000,
            'type' => 'single',
            'doses' => 1,
            'stock_status' => 'available',
            'disease_prevention' => 'Test',
            'age_group' => 'Tất cả',
            'origin' => 'Việt Nam',
            'is_active' => true,
        ]);
        $vaccine2->delete();

        $this->assertDatabaseHas('vaccines', [
            'id' => $vaccine2->id,
            'is_active' => false,
        ]);
    }

    #[Test]
    public function test_soft_deactivation_on_center()
    {
        $center = Center::create([
            'name' => 'Chi Nhánh Delete Test M5',
            'slug' => 'chi-nhanh-delete-test-' . uniqid(),
            'address' => '456 Đường Test',
            'phone' => '0912345678',
            'is_active' => true,
        ]);

        $response = $this->loginAs($this->superAdmin)->delete(route('admin.centers.destroy', $center->id));
        $response->assertRedirect(route('admin.centers.index'));

        $this->assertDatabaseHas('centers', [
            'id' => $center->id,
            'is_active' => false,
        ]);
    }

    #[Test]
    public function test_soft_deactivation_on_user()
    {
        $user = User::create([
            'name' => 'User Delete Test M5',
            'username' => 'user_delete_test_' . uniqid(),
            'email' => 'user_delete_test_' . uniqid() . '@medicare.vn',
            'password' => bcrypt('password123'),
            'role' => 'branch_admin',
            'center_id' => $this->center->id,
            'is_active' => true,
            'status' => 'active',
        ]);

        $response = $this->loginAs($this->superAdmin)->delete(route('admin.users.destroy', $user->id));
        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_active' => false,
            'status' => 'inactive',
        ]);
    }

    #[Test]
    public function test_soft_deactivation_on_banner()
    {
        $banner = Banner::create([
            'title' => 'Banner Delete Test M5',
            'subtitle' => 'Subtitle Test',
            'image_url' => '/images/banners/test.jpg',
            'is_active' => true,
        ]);

        $response = $this->loginAs($this->superAdmin)->delete(route('admin.banners.destroy', $banner->id));
        $response->assertRedirect(route('admin.banners.index'));

        $this->assertDatabaseHas('banners', [
            'id' => $banner->id,
            'is_active' => false,
        ]);
    }

    #[Test]
    public function test_soft_deactivation_on_article()
    {
        $article = Article::create([
            'title' => 'Bài viết Delete Test M5',
            'slug' => 'bai-viet-delete-test-' . uniqid(),
            'summary' => 'Tóm tắt bài viết',
            'content' => 'Nội dung bài viết',
            'category' => 'Khuyến cáo Y tế',
            'is_published' => true,
            'is_active' => true,
        ]);

        $response = $this->loginAs($this->superAdmin)->delete(route('admin.articles.destroy', $article->id));
        $response->assertRedirect(route('admin.articles.index'));

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'is_active' => false,
            'is_published' => false,
        ]);
    }
}
