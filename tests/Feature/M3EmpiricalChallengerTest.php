<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Vaccine;
use Tests\TestCase;

class M3EmpiricalChallengerTest extends TestCase
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
            'challenger_m3_super',
            'challenger_m3_branch_a',
            'challenger_m3_branch_b',
        ])->orWhereIn('email', [
            'challenger_m3_super@medicare.local',
            'challenger_m3_branch_a@medicare.local',
            'challenger_m3_branch_b@medicare.local',
        ])->delete();

        if (Center::count() < 2) {
            $this->seed(DatabaseSeeder::class);
        }

        $centers = Center::take(2)->get();
        $this->centerA = $centers[0];
        $this->centerB = $centers[1];

        // Create Super Admin
        $this->superAdmin = User::firstOrCreate(
            ['email' => 'challenger_m3_super@medicare.local'],
            [
                'name' => 'Challenger Super Admin',
                'username' => 'challenger_m3_super',
                'password' => Hash::make('Password123!'),
                'role' => 'super_admin',
                'is_active' => true,
                'status' => 'active',
            ]
        );

        // Create Branch Admin A
        $this->branchAdminA = User::firstOrCreate(
            ['email' => 'challenger_m3_branch_a@medicare.local'],
            [
                'name' => 'Challenger Branch Admin A',
                'username' => 'challenger_m3_branch_a',
                'password' => Hash::make('Password123!'),
                'role' => 'branch_admin',
                'center_id' => $this->centerA->id,
                'is_active' => true,
                'status' => 'active',
            ]
        );

        // Create Branch Admin B
        $this->branchAdminB = User::firstOrCreate(
            ['email' => 'challenger_m3_branch_b@medicare.local'],
            [
                'name' => 'Challenger Branch Admin B',
                'username' => 'challenger_m3_branch_b',
                'password' => Hash::make('Password123!'),
                'role' => 'branch_admin',
                'center_id' => $this->centerB->id,
                'is_active' => true,
                'status' => 'active',
            ]
        );

        // Create master vaccine
        $this->vaccine = Vaccine::create([
            'name' => 'Challenger Master Vaccine',
            'disease_prevention' => 'Phòng bệnh cúm mùa',
            'age_group' => 'Từ 6 tháng tuổi',
            'origin' => 'Pháp',
            'doses' => 1,
            'price' => 350000,
            'sale_price' => 320000,
            'stock_status' => 'available',
            'category' => 'Bệnh hô hấp',
            'description' => 'Mô tả vắc xin challenger test',
            'manufacturer' => 'Sanofi Pasteur',
            'dosage' => '0.5ml',
            'is_featured' => true,
        ]);

        CenterVaccine::create([
            'center_id' => $this->centerA->id,
            'vaccine_id' => $this->vaccine->id,
            'price' => 350000,
            'sale_price' => 320000,
            'stock_status' => 'available',
            'stock_quantity' => 20,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        CenterVaccine::create([
            'center_id' => $this->centerB->id,
            'vaccine_id' => $this->vaccine->id,
            'price' => 360000,
            'sale_price' => 330000,
            'stock_status' => 'available',
            'stock_quantity' => 15,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 2,
        ]);

        // Registrations
        $this->registrationA = Registration::create([
            'registration_code' => 'REG-CHALLENGE-A-' . uniqid(),
            'patient_name' => 'Patient Center A',
            'patient_phone' => '0911111111',
            'patient_dob' => '1990-01-01',
            'patient_gender' => 'Nam',
            'patient_address' => 'Chi nhánh A',
            'center_id' => $this->centerA->id,
            'center_name' => $this->centerA->name,
            'injection_date' => now()->addDays(2)->toDateString(),
            'payment_method' => 'Tiền mặt',
            'total_price' => 350000,
            'status' => 'Chờ thanh toán',
        ]);

        $this->registrationB = Registration::create([
            'registration_code' => 'REG-CHALLENGE-B-' . uniqid(),
            'patient_name' => 'Patient Center B',
            'patient_phone' => '0922222222',
            'patient_dob' => '1992-02-02',
            'patient_gender' => 'Nữ',
            'patient_address' => 'Chi nhánh B',
            'center_id' => $this->centerB->id,
            'center_name' => $this->centerB->name,
            'injection_date' => now()->addDays(3)->toDateString(),
            'payment_method' => 'Chuyển khoản',
            'total_price' => 360000,
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

    /**
     * TASK 1: IDOR Cross-Branch Attack Testing
     * Verify Branch Admin A cannot view, edit, status update, schedule view, CSV export, or modify stock/vaccines belonging to Branch B.
     */
    public function test_task1_idor_cross_branch_attack_returns_403(): void
    {
        // 1.1 View registrations list filtered for Branch B as Branch Admin A
        $this->actingAsAdmin($this->branchAdminA)
            ->get(route('admin.registrations.index', ['center_id' => $this->centerB->id]))
            ->assertStatus(403);

        // 1.2 View specific registration of Branch B as Branch Admin A
        $this->actingAsAdmin($this->branchAdminA)
            ->get(route('admin.registrations.show', $this->registrationB->id))
            ->assertStatus(403);

        // 1.3 Update status of registration of Branch B as Branch Admin A
        $this->actingAsAdmin($this->branchAdminA)
            ->patch(route('admin.registrations.status', $this->registrationB->id), ['status' => 'Đã thanh toán'])
            ->assertStatus(403);

        // 1.4 View schedule for Branch B as Branch Admin A
        $this->actingAsAdmin($this->branchAdminA)
            ->get(route('admin.schedule', ['center_id' => $this->centerB->id]))
            ->assertStatus(403);

        // 1.5 Export CSV registrations for Branch B as Branch Admin A
        $this->actingAsAdmin($this->branchAdminA)
            ->get(route('admin.registrations.export.csv', ['center_id' => $this->centerB->id]))
            ->assertStatus(403);

        // 1.6 View vaccine list filtered for Branch B as Branch Admin A
        $this->actingAsAdmin($this->branchAdminA)
            ->get(route('admin.vaccines.index', ['center_id' => $this->centerB->id]))
            ->assertStatus(403);

        // 1.7 Edit vaccine local settings for Branch B as Branch Admin A
        $this->actingAsAdmin($this->branchAdminA)
            ->get(route('admin.vaccines.edit', ['vaccine' => $this->vaccine->id, 'center_id' => $this->centerB->id]))
            ->assertStatus(403);

        // 1.8 Update vaccine local settings targeting Branch B as Branch Admin A
        $this->actingAsAdmin($this->branchAdminA)
            ->put(route('admin.vaccines.update', $this->vaccine->id), [
                'name' => $this->vaccine->name,
                'origin' => $this->vaccine->origin,
                'category' => $this->vaccine->category,
                'disease_prevention' => $this->vaccine->disease_prevention,
                'age_group' => $this->vaccine->age_group,
                'doses' => $this->vaccine->doses,
                'price' => 999999,
                'stock_status' => 'available',
                'center_id' => $this->centerB->id,
            ])
            ->assertStatus(403);

        // 1.9 Toggle featured state for Branch B as Branch Admin A
        $this->actingAsAdmin($this->branchAdminA)
            ->post(route('admin.vaccines.toggle-featured', ['id' => $this->vaccine->id, 'center_id' => $this->centerB->id]))
            ->assertStatus(403);

        // 1.10 Verify Branch Admin A CAN access own branch resources
        $this->actingAsAdmin($this->branchAdminA)
            ->get(route('admin.registrations.show', $this->registrationA->id))
            ->assertStatus(200);
    }

    /**
     * TASK 2: Master Catalog Protection Stress Testing
     * Verify Branch Admin attempting to modify master catalog fields returns 403 Forbidden.
     */
    public function test_task2_master_catalog_protection_returns_403(): void
    {
        $basePayload = [
            'name' => $this->vaccine->name,
            'origin' => $this->vaccine->origin,
            'category' => $this->vaccine->category,
            'disease_prevention' => $this->vaccine->disease_prevention,
            'age_group' => $this->vaccine->age_group,
            'doses' => $this->vaccine->doses,
            'manufacturer' => $this->vaccine->manufacturer,
            'dosage' => $this->vaccine->dosage,
            'description' => $this->vaccine->description,
            'price' => 400000,
            'sale_price' => 380000,
            'stock_status' => 'available',
            'center_id' => $this->centerA->id,
        ];

        // 2.1 Attempt modifying 'name'
        $this->actingAsAdmin($this->branchAdminA)
            ->put(route('admin.vaccines.update', $this->vaccine->id), array_merge($basePayload, ['name' => 'Hacked Vaccine Name']))
            ->assertStatus(403);

        // 2.2 Attempt modifying 'origin'
        $this->actingAsAdmin($this->branchAdminA)
            ->put(route('admin.vaccines.update', $this->vaccine->id), array_merge($basePayload, ['origin' => 'Đức']))
            ->assertStatus(403);

        // 2.3 Attempt modifying 'category'
        $this->actingAsAdmin($this->branchAdminA)
            ->put(route('admin.vaccines.update', $this->vaccine->id), array_merge($basePayload, ['category' => 'Bệnh Tiêu Hoá']))
            ->assertStatus(403);

        // 2.4 Attempt modifying 'disease_prevention'
        $this->actingAsAdmin($this->branchAdminA)
            ->put(route('admin.vaccines.update', $this->vaccine->id), array_merge($basePayload, ['disease_prevention' => 'Phòng bệnh tiêu chảy']))
            ->assertStatus(403);

        // 2.5 Attempt modifying 'doses'
        $this->actingAsAdmin($this->branchAdminA)
            ->put(route('admin.vaccines.update', $this->vaccine->id), array_merge($basePayload, ['doses' => 3]))
            ->assertStatus(403);

        // 2.6 Attempt modifying 'age_group'
        $this->actingAsAdmin($this->branchAdminA)
            ->put(route('admin.vaccines.update', $this->vaccine->id), array_merge($basePayload, ['age_group' => 'Người lớn']))
            ->assertStatus(403);

        // 2.7 Attempt modifying 'manufacturer'
        $this->actingAsAdmin($this->branchAdminA)
            ->put(route('admin.vaccines.update', $this->vaccine->id), array_merge($basePayload, ['manufacturer' => 'Pfizer']))
            ->assertStatus(403);

        // 2.8 Attempt modifying 'dosage'
        $this->actingAsAdmin($this->branchAdminA)
            ->put(route('admin.vaccines.update', $this->vaccine->id), array_merge($basePayload, ['dosage' => '1.0ml']))
            ->assertStatus(403);

        // 2.9 Attempt uploading image_file
        $fakeFile = UploadedFile::fake()->create('hacked.jpg', 10, 'image/jpeg');
        $this->actingAsAdmin($this->branchAdminA)
            ->put(route('admin.vaccines.update', $this->vaccine->id), array_merge($basePayload, ['image_file' => $fakeFile]))
            ->assertStatus(403);

        // 2.10 Attempt master vaccine store (creation) as Branch Admin
        $this->actingAsAdmin($this->branchAdminA)
            ->post(route('admin.vaccines.store'), array_merge($basePayload, ['name' => 'Brand New Vaccine']))
            ->assertStatus(403);

        // 2.11 Attempt master vaccine destroy (deletion) as Branch Admin
        $this->actingAsAdmin($this->branchAdminA)
            ->delete(route('admin.vaccines.destroy', $this->vaccine->id))
            ->assertStatus(403);

        // 2.12 Confirm Branch Admin CAN edit branch local fields (price, sale_price, stock_status, is_featured, sort_order)
        $validBranchUpdate = $this->actingAsAdmin($this->branchAdminA)
            ->put(route('admin.vaccines.update', $this->vaccine->id), $basePayload);
        $validBranchUpdate->assertRedirect();

        $this->assertDatabaseHas('center_vaccines', [
            'center_id' => $this->centerA->id,
            'vaccine_id' => $this->vaccine->id,
            'price' => 400000,
            'sale_price' => 380000,
            'stock_status' => 'available',
        ]);
    }

    /**
     * TASK 3: Super Admin Privilege Verification
     * Verify Super Admin can manage master catalog items, toggle featured states, and access all centers without 403 errors.
     */
    public function test_task3_super_admin_privileges_succeed(): void
    {
        // 3.1 Super Admin accesses master vaccine create page
        $this->actingAsAdmin($this->superAdmin)
            ->get(route('admin.vaccines.create'))
            ->assertStatus(200);

        // 3.2 Super Admin creates a new master catalog vaccine
        $storeResponse = $this->actingAsAdmin($this->superAdmin)
            ->post(route('admin.vaccines.store'), [
                'name' => 'Master Vaccine by Super Admin',
                'disease_prevention' => 'Phòng Viêm gan B',
                'age_group' => 'Trẻ sơ sinh',
                'origin' => 'Hàn Quốc',
                'doses' => 3,
                'price' => 250000,
                'sale_price' => 220000,
                'stock_status' => 'available',
                'category' => 'Bệnh truyền nhiễm',
                'center_id' => $this->centerA->id,
            ]);
        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('vaccines', ['name' => 'Master Vaccine by Super Admin']);

        // 3.3 Super Admin modifies master catalog fields
        $updateResponse = $this->actingAsAdmin($this->superAdmin)
            ->put(route('admin.vaccines.update', $this->vaccine->id), [
                'name' => 'Updated Challenger Master Vaccine',
                'origin' => 'Nhật Bản',
                'category' => 'Bệnh hô hấp nâng cao',
                'disease_prevention' => 'Phòng cúm mùa A/B',
                'age_group' => 'Từ 6 tháng trở lên',
                'doses' => 1,
                'price' => 380000,
                'stock_status' => 'available',
                'center_id' => $this->centerB->id,
            ]);
        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('vaccines', [
            'id' => $this->vaccine->id,
            'name' => 'Updated Challenger Master Vaccine',
            'origin' => 'Nhật Bản',
        ]);

        // 3.4 Super Admin toggles featured status for any center
        $toggleResponseA = $this->actingAsAdmin($this->superAdmin)
            ->post(route('admin.vaccines.toggle-featured', ['id' => $this->vaccine->id, 'center_id' => $this->centerA->id]));
        $toggleResponseA->assertStatus(302);

        $toggleResponseB = $this->actingAsAdmin($this->superAdmin)
            ->post(route('admin.vaccines.toggle-featured', ['id' => $this->vaccine->id, 'center_id' => $this->centerB->id]));
        $toggleResponseB->assertStatus(302);

        // 3.5 Super Admin accesses all center administration routes
        $this->actingAsAdmin($this->superAdmin)
            ->get(route('admin.centers.index'))
            ->assertStatus(200);

        $this->actingAsAdmin($this->superAdmin)
            ->get(route('admin.centers.create'))
            ->assertStatus(200);

        // 3.6 Super Admin accesses registrations for any center
        $this->actingAsAdmin($this->superAdmin)
            ->get(route('admin.registrations.index', ['center_id' => $this->centerA->id]))
            ->assertStatus(200);

        $this->actingAsAdmin($this->superAdmin)
            ->get(route('admin.registrations.index', ['center_id' => $this->centerB->id]))
            ->assertStatus(200);

    }

    /**
     * TASK 4: Unauthorized Endpoint Testing
     * Attempt to access AdminCenterController, AdminBannerController, AdminArticleController endpoints as branch_admin. Confirm 403 Forbidden.
     */
    public function test_task4_unauthorized_endpoints_return_403_for_branch_admin(): void
    {
        // 4.1 AdminCenterController endpoints
        $this->actingAsAdmin($this->branchAdminA)->get(route('admin.centers.index'))->assertStatus(403);
        $this->actingAsAdmin($this->branchAdminA)->get(route('admin.centers.create'))->assertStatus(403);
        $this->actingAsAdmin($this->branchAdminA)->post(route('admin.centers.store'), [
            'name' => 'Unauthorized Center',
            'address' => '123 Fake St',
            'is_active' => 1,
        ])->assertStatus(403);
        $this->actingAsAdmin($this->branchAdminA)->get(route('admin.centers.edit', $this->centerA->id))->assertStatus(403);
        $this->actingAsAdmin($this->branchAdminA)->put(route('admin.centers.update', $this->centerA->id), [
            'name' => 'Updated Center Name',
            'address' => '456 Fake St',
            'is_active' => 1,
        ])->assertStatus(403);
        $this->actingAsAdmin($this->branchAdminA)->delete(route('admin.centers.destroy', $this->centerA->id))->assertStatus(403);

        // 4.2 AdminBannerController endpoints
        $this->actingAsAdmin($this->branchAdminA)->get(route('admin.banners.index'))->assertStatus(403);
        $this->actingAsAdmin($this->branchAdminA)->get(route('admin.banners.create'))->assertStatus(403);
        $this->actingAsAdmin($this->branchAdminA)->post(route('admin.banners.store'), [
            'title' => 'Unauthorized Banner',
        ])->assertStatus(403);

        // 4.3 AdminArticleController endpoints
        $this->actingAsAdmin($this->branchAdminA)->get(route('admin.articles.index'))->assertStatus(403);
        $this->actingAsAdmin($this->branchAdminA)->get(route('admin.articles.create'))->assertStatus(403);
        $this->actingAsAdmin($this->branchAdminA)->post(route('admin.articles.store'), [
            'title' => 'Unauthorized Article',
            'content' => 'Test content',
        ])->assertStatus(403);

        // 4.4 Additional super-admin protected endpoints (AdminUserController, AdminSettingController)
        $this->actingAsAdmin($this->branchAdminA)->get(route('admin.users.index'))->assertStatus(403);
        $this->actingAsAdmin($this->branchAdminA)->get(route('admin.settings.index'))->assertStatus(403);
    }
}
