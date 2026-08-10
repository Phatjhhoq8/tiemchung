<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Modules\VaccineRegistration\Models\Center;
use Tests\TestCase;

class AdminAccountSecurityTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        User::whereIn('username', [
            'superadmin_test',
            'branchadmin_test',
            'branchadmin_invalid',
            'locktest',
            'loginfailadmin',
            'successadmin',
        ])->orWhereIn('email', [
            'superadmin_test@medicare.local',
            'branchadmin_test@medicare.local',
            'branchadmin_invalid@medicare.local',
            'locktest@medicare.local',
            'loginfailadmin@medicare.local',
            'successadmin@medicare.local',
        ])->delete();
    }

    public function test_admin_create_artisan_command_creates_super_admin_successfully(): void
    {
        $this->artisan('admin:create', [
            '--name' => 'Super Admin Test',
            '--username' => 'superadmin_test',
            '--email' => 'superadmin_test@medicare.local',
            '--password' => 'SecurePass123!',
            '--role' => 'super_admin',
            '--no-interaction' => true,
        ])->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'username' => 'superadmin_test',
            'email' => 'superadmin_test@medicare.local',
            'role' => 'super_admin',
            'status' => 'active',
            'center_id' => null,
            'must_change_password' => true,
            'password_changed_at' => null,
        ]);
    }

    public function test_admin_create_artisan_command_creates_branch_admin_with_valid_center(): void
    {
        $this->seed(DatabaseSeeder::class);
        $center = Center::firstOrFail();

        $this->artisan('admin:create', [
            '--name' => 'Branch Admin Test',
            '--username' => 'branchadmin_test',
            '--email' => 'branchadmin_test@medicare.local',
            '--password' => 'SecurePass123!',
            '--role' => 'branch_admin',
            '--center_id' => $center->id,
            '--no-interaction' => true,
        ])->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'username' => 'branchadmin_test',
            'email' => 'branchadmin_test@medicare.local',
            'role' => 'branch_admin',
            'center_id' => $center->id,
            'must_change_password' => true,
            'password_changed_at' => null,
        ]);
    }

    public function test_admin_create_artisan_command_fails_for_invalid_center(): void
    {
        $this->artisan('admin:create', [
            '--name' => 'Branch Admin Invalid',
            '--username' => 'branchadmin_invalid',
            '--email' => 'branchadmin_invalid@medicare.local',
            '--password' => 'SecurePass123!',
            '--role' => 'branch_admin',
            '--center_id' => 99999,
            '--no-interaction' => true,
        ])->assertExitCode(1);

        $this->assertDatabaseMissing('users', [
            'username' => 'branchadmin_invalid',
        ]);
    }

    public function test_database_seeder_does_not_auto_create_default_admin(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseMissing('users', [
            'username' => 'admin',
            'email' => 'admin@medicare.local',
        ]);
    }

    public function test_user_model_account_locking_helpers(): void
    {
        $user = User::create([
            'name' => 'Lock Test User',
            'username' => 'locktest',
            'email' => 'locktest@medicare.local',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'status' => 'active',
            'failed_login_count' => 0,
        ]);

        $this->assertFalse($user->isLocked());

        // Simulate 4 failed attempts
        for ($i = 1; $i <= 4; $i++) {
            $user->recordFailedLogin(5, 15);
            $this->assertFalse($user->isLocked());
            $this->assertEquals($i, $user->failed_login_count);
        }

        // 5th failed attempt triggers lock
        $user->recordFailedLogin(5, 15);
        $this->assertTrue($user->isLocked());
        $this->assertNotNull($user->locked_until);
        $this->assertTrue($user->locked_until->isFuture());

        // Record successful login clears lock
        $user->recordSuccessfulLogin();
        $this->assertFalse($user->isLocked());
        $this->assertEquals(0, $user->failed_login_count);
        $this->assertNull($user->locked_until);
        $this->assertNotNull($user->last_login_at);
    }

    public function test_admin_login_locks_account_after_5_failed_attempts(): void
    {
        $user = User::create([
            'name' => 'Login Fail Admin',
            'username' => 'loginfailadmin',
            'email' => 'loginfailadmin@medicare.local',
            'password' => Hash::make('CorrectPassword123!'),
            'role' => 'super_admin',
            'is_active' => true,
            'status' => 'active',
            'failed_login_count' => 0,
        ]);

        // Fail 4 times
        for ($i = 0; $i < 4; $i++) {
            $response = $this->post('/admin/login', [
                'username' => 'loginfailadmin',
                'password' => 'WrongPassword!',
            ]);
            $response->assertSessionHasErrors('auth_failed');
        }

        $user->refresh();
        $this->assertEquals(4, $user->failed_login_count);
        $this->assertFalse($user->isLocked());

        // 5th failed attempt locks the account
        $response5 = $this->post('/admin/login', [
            'username' => 'loginfailadmin',
            'password' => 'WrongPassword!',
        ]);
        $response5->assertSessionHasErrors(['auth_failed' => 'Tên đăng nhập hoặc mật khẩu không chính xác.']);

        $user->refresh();
        $this->assertTrue($user->isLocked());

        // Clear throttle rate limiter for IP to test account-level lock check with correct password
        \Illuminate\Support\Facades\RateLimiter::clear('admin_login:loginfailadmin|127.0.0.1');

        $responseLocked = $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.2'])->post('/admin/login', [
            'username' => 'loginfailadmin',
            'password' => 'CorrectPassword123!',
        ]);
        $responseLocked->assertSessionHasErrors(['auth_failed' => 'Tên đăng nhập hoặc mật khẩu không chính xác.']);
    }

    public function test_admin_login_success_resets_failed_count_and_redirects(): void
    {
        $user = User::create([
            'name' => 'Success Admin',
            'username' => 'successadmin',
            'email' => 'successadmin@medicare.local',
            'password' => Hash::make('ValidPass123!'),
            'role' => 'super_admin',
            'is_active' => true,
            'status' => 'active',
            'failed_login_count' => 2,
        ]);

        $response = $this->post('/admin/login', [
            'username' => 'successadmin',
            'password' => 'ValidPass123!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('admin_logged_in', true);
        $response->assertSessionHas('admin_user_id', $user->id);

        $user->refresh();
        $this->assertEquals(0, $user->failed_login_count);
        $this->assertNotNull($user->last_login_at);
    }
}
