<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Vaccine;
use Tests\TestCase;

class RbacMultiBranchTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $branchAdminA;
    protected User $branchAdminB;
    protected Center $centerA;
    protected Center $centerB;
    protected Vaccine $vaccine;
    protected Registration $registrationA;
    protected Registration $registrationB;

    protected function setUp(): void
    {
        parent::setUp();

        User::whereIn('username', [
            'superadmin_rbac_test',
            'branchadmin_a_rbac_test',
            'branchadmin_b_rbac_test',
        ])->orWhereIn('email', [
            'superadmin_rbac_test@medicare.local',
            'branchadmin_a_rbac_test@medicare.local',
            'branchadmin_b_rbac_test@medicare.local',
        ])->delete();

        // Seed basic centers if missing
        if (Center::count() < 2) {
            $this->seed(DatabaseSeeder::class);
        }

        $centers = Center::take(2)->get();
        $this->centerA = $centers[0];
        $this->centerB = $centers[1];

        $unique = Str::random(6);

        // Create Super Admin
        $this->superAdmin = User::create([
            'name' => 'Super Admin Test ' . $unique,
            'email' => 'superadmin_rbac_' . $unique . '@medicare.local',
            'username' => 'superadmin_rbac_' . $unique,
            'password' => Hash::make('Password123!'),
            'role' => 'super_admin',
            'is_active' => true,
            'status' => 'active',
        ]);

        // Create Branch Admin for Center A
        $this->branchAdminA = User::create([
            'name' => 'Branch Admin A ' . $unique,
            'email' => 'branchadmin_a_rbac_' . $unique . '@medicare.local',
            'username' => 'branchadmin_a_rbac_' . $unique,
            'password' => Hash::make('Password123!'),
            'role' => 'branch_admin',
            'center_id' => $this->centerA->id,
            'is_active' => true,
            'status' => 'active',
        ]);

        // Create Branch Admin for Center B
        $this->branchAdminB = User::create([
            'name' => 'Branch Admin B ' . $unique,
            'email' => 'branchadmin_b_rbac_' . $unique . '@medicare.local',
            'username' => 'branchadmin_b_rbac_' . $unique,
            'password' => Hash::make('Password123!'),
            'role' => 'branch_admin',
            'center_id' => $this->centerB->id,
            'is_active' => true,
            'status' => 'active',
        ]);

        // Create a test master vaccine
        $this->vaccine = Vaccine::create([
            'name' => 'Test Master Vaccine 1',
            'type' => 'single',
            'disease_prevention' => 'Phòng Bệnh Test',
            'age_group' => 'Từ 2 tháng tuổi',
            'origin' => 'Bỉ',
            'doses' => 1,
            'price' => 500000,
            'sale_price' => 450000,
            'stock_status' => 'available',
            'category' => 'Bệnh hô hấp',
            'description' => 'Mô tả vắc xin test',
            'is_featured' => true,
        ]);

        // Attach center vaccines
        CenterVaccine::create([
            'center_id' => $this->centerA->id,
            'vaccine_id' => $this->vaccine->id,
            'price' => 500000,
            'sale_price' => 450000,
            'stock_status' => 'available',
            'stock_quantity' => 10,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        CenterVaccine::create([
            'center_id' => $this->centerB->id,
            'vaccine_id' => $this->vaccine->id,
            'price' => 520000,
            'sale_price' => 480000,
            'stock_status' => 'available',
            'stock_quantity' => 5,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 2,
        ]);

        // Create registrations for Center A and Center B
        $this->registrationA = Registration::create([
            'registration_code' => 'REG-TEST-A-' . uniqid(),
            'patient_name' => 'Patient Center A',
            'patient_phone' => '0901111111',
            'patient_dob' => '1990-01-01',
            'patient_gender' => 'Nam',
            'patient_address' => 'Địa chỉ Test A',
            'center_id' => $this->centerA->id,
            'center_name' => $this->centerA->name,
            'injection_date' => now()->addDays(2)->toDateString(),
            'payment_method' => 'Tiền mặt',
            'total_price' => 500000,
            'status' => 'Chờ thanh toán',
        ]);

        $this->registrationB = Registration::create([
            'registration_code' => 'REG-TEST-B-' . uniqid(),
            'patient_name' => 'Patient Center B',
            'patient_phone' => '0902222222',
            'patient_dob' => '1992-02-02',
            'patient_gender' => 'Nữ',
            'patient_address' => 'Địa chỉ Test B',
            'center_id' => $this->centerB->id,
            'center_name' => $this->centerB->name,
            'injection_date' => now()->addDays(3)->toDateString(),
            'payment_method' => 'Chuyển khoản',
            'total_price' => 520000,
            'status' => 'Chờ thanh toán',
        ]);
    }

    protected function actingAsAdmin(User $user)
    {
        return $this->actingAs($user)->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $user->id,
            'admin_role' => $user->role,
            'admin_center_id' => $user->center_id,
        ]);
    }

    /** Test 1: Super Admin can CRUD master catalog */
    public function test_super_admin_has_full_crud_over_master_vaccine_catalog(): void
    {
        $response = $this->actingAsAdmin($this->superAdmin)->post(route('admin.vaccines.store'), [
            'name' => 'New Master Vaccine by SuperAdmin',
            'type' => 'single',
            'disease_prevention' => 'Phòng Bệnh Mới',
            'age_group' => 'Từ 1 tuổi',
            'origin' => 'Pháp',
            'doses' => 2,
            'price' => 600000,
            'sale_price' => 550000,
            'stock_status' => 'available',
            'center_id' => $this->centerA->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vaccines', [
            'name' => 'New Master Vaccine by SuperAdmin',
            'origin' => 'Pháp',
        ]);
    }

    /** Test 2: Branch Admin CANNOT create master catalog vaccine */
    public function test_branch_admin_cannot_create_master_vaccine(): void
    {
        $response = $this->actingAsAdmin($this->branchAdminA)->post(route('admin.vaccines.store'), [
            'name' => 'Branch Admin Attempted Vaccine',
            'type' => 'single',
            'disease_prevention' => 'Phòng Bệnh Fake',
            'age_group' => 'Từ 1 tuổi',
            'origin' => 'Mỹ',
            'doses' => 1,
            'price' => 300000,
            'stock_status' => 'available',
            'center_id' => $this->centerA->id,
        ]);

        $response->assertStatus(403);
    }

    /** Test 3: Branch Admin CANNOT delete master catalog vaccine */
    public function test_branch_admin_cannot_delete_master_vaccine(): void
    {
        $response = $this->actingAsAdmin($this->branchAdminA)->delete(route('admin.vaccines.destroy', $this->vaccine->id));

        $response->assertStatus(403);
    }

    /** Test 4: Branch Admin can edit branch-specific local settings for own center */
    public function test_branch_admin_can_update_local_branch_settings(): void
    {
        $response = $this->actingAsAdmin($this->branchAdminA)->put(route('admin.vaccines.update', $this->vaccine->id), [
            'name' => $this->vaccine->name,
            'origin' => $this->vaccine->origin,
            'category' => $this->vaccine->category,
            'disease_prevention' => $this->vaccine->disease_prevention,
            'age_group' => $this->vaccine->age_group,
            'type' => $this->vaccine->type,
            'doses' => $this->vaccine->doses,
            'price' => 590000,
            'sale_price' => 540000,
            'stock_status' => 'limited',
            'center_id' => $this->centerA->id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('center_vaccines', [
            'center_id' => $this->centerA->id,
            'vaccine_id' => $this->vaccine->id,
            'price' => 590000,
            'sale_price' => 540000,
            'stock_status' => 'limited',
        ]);
    }

    /** Test 5: Branch Admin attempting to modify master catalog fields returns 403 Forbidden */
    public function test_branch_admin_modifying_master_catalog_fields_returns_403(): void
    {
        $response = $this->actingAsAdmin($this->branchAdminA)->put(route('admin.vaccines.update', $this->vaccine->id), [
            'name' => 'Modified Name by Branch Admin',
            'origin' => $this->vaccine->origin,
            'category' => $this->vaccine->category,
            'disease_prevention' => $this->vaccine->disease_prevention,
            'age_group' => $this->vaccine->age_group,
            'type' => $this->vaccine->type,
            'doses' => $this->vaccine->doses,
            'price' => 500000,
            'stock_status' => 'available',
            'center_id' => $this->centerA->id,
        ]);

        $response->assertStatus(403);
    }

    /** Test 6: Cross-Branch Protection (Anti-IDOR) for Registrations */
    public function test_branch_admin_accessing_cross_branch_registration_returns_403(): void
    {
        // Branch Admin A accessing Registration of Branch B
        $responseView = $this->actingAsAdmin($this->branchAdminA)->get(route('admin.registrations.show', $this->registrationB->id));
        $responseView->assertStatus(403);

        // Branch Admin A updating status of Registration of Branch B
        $responseUpdate = $this->actingAsAdmin($this->branchAdminA)->patch(route('admin.registrations.status', $this->registrationB->id), [
            'status' => 'Đã thanh toán',
        ]);
        $responseUpdate->assertStatus(403);
    }

    /** Test 7: Branch Admin accessing own branch registration succeeds */
    public function test_branch_admin_accessing_own_branch_registration_succeeds(): void
    {
        $responseView = $this->actingAsAdmin($this->branchAdminA)->get(route('admin.registrations.show', $this->registrationA->id));
        $responseView->assertStatus(200);
    }

    /** Test 8: Cross-Branch Protection (Anti-IDOR) for Vaccine Local Settings */
    public function test_branch_admin_updating_vaccine_for_other_branch_returns_403(): void
    {
        $response = $this->actingAsAdmin($this->branchAdminA)->put(route('admin.vaccines.update', $this->vaccine->id), [
            'name' => $this->vaccine->name,
            'origin' => $this->vaccine->origin,
            'category' => $this->vaccine->category,
            'disease_prevention' => $this->vaccine->disease_prevention,
            'age_group' => $this->vaccine->age_group,
            'type' => $this->vaccine->type,
            'doses' => $this->vaccine->doses,
            'price' => 999999,
            'stock_status' => 'available',
            'center_id' => $this->centerB->id, // Trying to update Center B
        ]);

        $response->assertStatus(403);
    }

    /** Test 9: Authorization Hole Fixes - Branch Admin blocked from super_admin resources */
    public function test_branch_admin_blocked_from_centers_banners_articles(): void
    {
        $this->actingAsAdmin($this->branchAdminA)->get(route('admin.centers.index'))->assertStatus(403);
        $this->actingAsAdmin($this->branchAdminA)->get(route('admin.banners.index'))->assertStatus(403);
        $this->actingAsAdmin($this->branchAdminA)->get(route('admin.articles.index'))->assertStatus(403);
    }

    /** Test 10: toggleFeatured permission check works for Super Admin & Branch Admin */
    public function test_toggle_featured_permission_check(): void
    {
        // Branch Admin A toggle for center A -> 302 redirect back with success
        $responseBranch = $this->actingAsAdmin($this->branchAdminA)->post(route('admin.vaccines.toggle-featured', [
            'id' => $this->vaccine->id,
            'center_id' => $this->centerA->id,
        ]));
        $responseBranch->assertStatus(302);

        // Branch Admin A toggle for center B -> 403 Forbidden
        $responseCrossBranch = $this->actingAsAdmin($this->branchAdminA)->post(route('admin.vaccines.toggle-featured', [
            'id' => $this->vaccine->id,
            'center_id' => $this->centerB->id,
        ]));
        $responseCrossBranch->assertStatus(403);

        // Super Admin toggle -> 302 redirect back with success
        $responseSuper = $this->actingAsAdmin($this->superAdmin)->post(route('admin.vaccines.toggle-featured', [
            'id' => $this->vaccine->id,
            'center_id' => $this->centerB->id,
        ]));
        $responseSuper->assertStatus(302);
    }
}
