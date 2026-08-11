<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\Vaccine;
use Tests\TestCase;

class AdminCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected Center $center;

    protected function setUp(): void
    {
        parent::setUp();

        $this->center = Center::create([
            'name' => 'Trung Tâm Medicare Test',
            'code' => 'CENTER_TEST',
            'address' => '123 Đường Test',
            'phone' => '0901234567',
            'is_active' => true,
        ]);

        $this->superAdmin = User::create([
            'username' => 'superadmin_test',
            'email' => 'superadmin_test@medicare.local',
            'password' => bcrypt('password123'),
            'name' => 'Super Admin Test',
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_check_category_delete_with_and_without_vaccines(): void
    {
        $vaccine = Vaccine::create([
            'name' => 'Vắc Xin Cúm Test',
            'category' => 'Cúm Mùa Special',
            'disease_prevention' => 'Phòng cúm',
            'age_group' => 'Trẻ em và Người lớn',
            'origin' => 'Pháp',
            'doses' => 1,
            'price' => 350000,
        ]);

        $responseWithVaccines = $this->actingAsAdmin($this->superAdmin)
            ->postJson(route('admin.categories.check-delete'), [
                'category' => 'Cúm Mùa Special',
            ]);

        $responseWithVaccines->assertOk()
            ->assertJson([
                'category' => 'Cúm Mùa Special',
                'has_vaccines' => true,
                'vaccine_count' => 1,
            ]);

        $responseEmpty = $this->actingAsAdmin($this->superAdmin)
            ->postJson(route('admin.categories.check-delete'), [
                'category' => 'Nhóm Bệnh Rỗng',
            ]);

        $responseEmpty->assertOk()
            ->assertJson([
                'category' => 'Nhóm Bệnh Rỗng',
                'has_vaccines' => false,
                'vaccine_count' => 0,
            ]);
    }

    public function test_admin_can_update_category_name(): void
    {
        Vaccine::create([
            'name' => 'Vắc Xin A',
            'category' => 'Tên Cũ',
            'disease_prevention' => 'Phòng bệnh',
            'age_group' => 'Mọi lứa tuổi',
            'origin' => 'Mỹ',
            'doses' => 1,
            'price' => 200000,
        ]);

        $response = $this->actingAsAdmin($this->superAdmin)
            ->putJson(route('admin.categories.update'), [
                'old_name' => 'Tên Cũ',
                'new_name' => 'Tên Mới',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('vaccines', ['category' => 'Tên Mới']);
        $this->assertDatabaseMissing('vaccines', ['category' => 'Tên Cũ']);
    }

    public function test_admin_can_delete_category(): void
    {
        Vaccine::create([
            'name' => 'Vắc Xin B',
            'category' => 'Nhóm Cần Xóa',
            'disease_prevention' => 'Phòng bệnh',
            'age_group' => 'Mọi lứa tuổi',
            'origin' => 'Đức',
            'doses' => 1,
            'price' => 200000,
        ]);

        $response = $this->actingAsAdmin($this->superAdmin)
            ->deleteJson(route('admin.categories.destroy'), [
                'category' => 'Nhóm Cần Xóa',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('vaccines', ['name' => 'Vắc Xin B', 'category' => null]);
    }

    protected function actingAsAdmin(User $admin): static
    {
        return $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $admin->id,
            'admin_role' => $admin->role,
            'admin_name' => $admin->name,
            'admin_username' => $admin->username,
            'admin_password_hash' => md5($admin->password),
        ]);
    }
}
