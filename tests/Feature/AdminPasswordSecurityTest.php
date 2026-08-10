<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\VaccineRegistration\Models\Center;
use Tests\TestCase;

class AdminPasswordSecurityTest extends TestCase
{
    use DatabaseTransactions;

    private const CURRENT_PASSWORD = 'Current#Pass123';

    private function createAdmin(array $attributes = []): User
    {
        $suffix = Str::lower(Str::random(10));

        return User::create(array_merge([
            'name' => 'Admin Password Test',
            'username' => 'password_admin_'.$suffix,
            'email' => 'password_admin_'.$suffix.'@example.test',
            'password' => Hash::make(self::CURRENT_PASSWORD),
            'role' => 'super_admin',
            'is_active' => true,
            'status' => 'active',
            'must_change_password' => false,
            'password_changed_at' => now()->subDay(),
        ], $attributes));
    }

    private function adminSession(User $user): array
    {
        return [
            'admin_logged_in' => true,
            'admin_user_id' => $user->id,
            'admin_role' => $user->role,
            'admin_center_id' => $user->center_id,
            'admin_password_hash' => md5($user->password),
        ];
    }

    public function test_web_created_account_and_admin_reset_use_temporary_password_state(): void
    {
        $superAdmin = $this->createAdmin();
        $center = Center::create([
            'name' => 'Trung tâm Password '.Str::random(8),
            'slug' => 'password-'.Str::lower(Str::random(10)),
            'address' => 'Địa chỉ kiểm thử',
            'phone' => '090'.random_int(1000000, 9999999),
            'is_active' => true,
        ]);
        $username = 'temporary_'.Str::lower(Str::random(10));

        $this->actingAs($superAdmin)
            ->withSession($this->adminSession($superAdmin))
            ->post(route('admin.users.store'), [
                'name' => 'Temporary Admin',
                'username' => $username,
                'email' => $username.'@example.test',
                'password' => 'Temporary#123A',
                'role' => 'branch_admin',
                'center_id' => $center->id,
                'is_active' => '1',
            ])->assertRedirect(route('admin.users.index'));

        $created = User::where('username', $username)->firstOrFail();
        $this->assertTrue($created->must_change_password);
        $this->assertNull($created->password_changed_at);

        $created->forceFill([
            'must_change_password' => false,
            'password_changed_at' => now()->subDay(),
        ])->save();

        $this->actingAs($superAdmin)
            ->withSession($this->adminSession($superAdmin))
            ->put(route('admin.users.update', $created), [
                'name' => $created->name,
                'username' => $created->username,
                'email' => $created->email,
                'password' => 'ResetPassword#456A',
                'role' => $created->role,
                'center_id' => $center->id,
                'is_active' => '1',
            ])->assertRedirect(route('admin.users.index'));

        $this->assertTrue($created->fresh()->must_change_password);
        $this->assertNull($created->fresh()->password_changed_at);
    }

    public function test_login_and_middleware_force_temporary_password_change(): void
    {
        $user = $this->createAdmin([
            'must_change_password' => true,
            'password_changed_at' => null,
        ]);

        $this->post(route('admin.login'), [
            'username' => $user->username,
            'password' => self::CURRENT_PASSWORD,
        ])->assertRedirect(route('admin.password.edit'));

        $this->withSession($this->adminSession($user))
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.password.edit'));

        $this->withSession($this->adminSession($user))
            ->get(route('admin.password.edit'))
            ->assertOk();
    }

    public function test_wrong_current_weak_and_reused_passwords_are_rejected(): void
    {
        $user = $this->createAdmin(['must_change_password' => true, 'password_changed_at' => null]);
        $session = $this->adminSession($user);

        $this->withSession($session)->put(route('admin.password.update'), [
            'current_password' => 'Wrong#Password123',
            'password' => 'ValidNew#Pass456',
            'password_confirmation' => 'ValidNew#Pass456',
        ])->assertSessionHasErrors('current_password');

        $this->withSession($session)->put(route('admin.password.update'), [
            'current_password' => self::CURRENT_PASSWORD,
            'password' => 'weakpassword',
            'password_confirmation' => 'weakpassword',
        ])->assertSessionHasErrors('password');

        $this->withSession($session)->put(route('admin.password.update'), [
            'current_password' => self::CURRENT_PASSWORD,
            'password' => self::CURRENT_PASSWORD,
            'password_confirmation' => self::CURRENT_PASSWORD,
        ])->assertSessionHasErrors('password');

        $this->assertTrue($user->fresh()->must_change_password);
        $this->assertTrue(Hash::check(self::CURRENT_PASSWORD, $user->fresh()->password));
    }

    public function test_valid_password_change_clears_flag_sets_timestamp_and_updates_session_hash(): void
    {
        $user = $this->createAdmin(['must_change_password' => true, 'password_changed_at' => null]);
        $newPassword = 'ValidNew#Pass456';

        $response = $this->withSession($this->adminSession($user))
            ->put(route('admin.password.update'), [
                'current_password' => self::CURRENT_PASSWORD,
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ]);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success', 'Đổi mật khẩu thành công.');

        $user->refresh();
        $this->assertFalse($user->must_change_password);
        $this->assertNotNull($user->password_changed_at);
        $this->assertTrue(Hash::check($newPassword, $user->password));
        $response->assertSessionHas('admin_password_hash', md5($user->password));

        $this->withSession($this->adminSession($user))
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_existing_normal_account_still_logs_in_to_dashboard(): void
    {
        $user = $this->createAdmin();

        $this->post(route('admin.login'), [
            'username' => $user->username,
            'password' => self::CURRENT_PASSWORD,
        ])->assertRedirect(route('admin.dashboard'));
    }
}
