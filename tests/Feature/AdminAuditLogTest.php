<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\VaccineRegistration\Models\Center;
use Tests\TestCase;

class AdminAuditLogTest extends TestCase
{
    use DatabaseTransactions;

    private User $superAdmin;

    private User $branchAdmin;

    private Center $center;

    protected function setUp(): void
    {
        parent::setUp();

        $unique = Str::lower(Str::random(8));
        $this->center = Center::create([
            'name' => 'Chi nhánh audit '.$unique,
            'slug' => 'chi-nhanh-audit-'.$unique,
            'address' => 'Địa chỉ kiểm thử',
            'phone' => '090'.random_int(1000000, 9999999),
            'is_active' => true,
        ]);
        $this->superAdmin = $this->createUser('super_admin', $unique);
        $this->branchAdmin = $this->createUser('branch_admin', $unique, $this->center->id);
    }

    public function test_only_super_admin_can_access_audit_log_pages_and_menu(): void
    {
        $log = $this->createLog(['actor_id' => $this->superAdmin->id]);

        $this->actingAsAdmin($this->superAdmin)
            ->get(route('admin.audit-logs.index'))
            ->assertOk()
            ->assertSee('Nhật Ký Hệ Thống')
            ->assertSee($log->action);
        $this->actingAsAdmin($this->superAdmin)
            ->get(route('admin.audit-logs.show', $log))
            ->assertOk();

        $this->actingAsAdmin($this->branchAdmin)->get(route('admin.audit-logs.index'))->assertForbidden();
        $this->actingAsAdmin($this->branchAdmin)->get(route('admin.audit-logs.show', $log))->assertForbidden();
        $this->actingAsAdmin($this->branchAdmin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Nhật Ký Hệ Thống');
    }

    public function test_index_filters_by_all_supported_fields(): void
    {
        $matching = $this->createLog([
            'actor_id' => $this->superAdmin->id,
            'center_id' => $this->center->id,
            'action' => 'audit_filter_match',
            'resource_type' => 'audit_test_resource',
            'resource_id' => 'MATCH-123',
        ]);
        $other = $this->createLog([
            'actor_id' => $this->branchAdmin->id,
            'center_id' => null,
            'action' => 'audit_filter_other',
            'resource_type' => 'other_resource',
            'resource_id' => 'OTHER-456',
        ]);

        $response = $this->actingAsAdmin($this->superAdmin)->get(route('admin.audit-logs.index', [
            'from_date' => now()->toDateString(),
            'to_date' => now()->toDateString(),
            'actor_id' => $this->superAdmin->id,
            'center_id' => $this->center->id,
            'action' => $matching->action,
            'resource_type' => $matching->resource_type,
            'resource_id' => $matching->resource_id,
        ]));

        $response->assertOk()->assertViewHas('auditLogs', function ($logs) use ($matching, $other) {
            $ids = collect($logs->items())->pluck('id');

            return $ids->contains($matching->id) && ! $ids->contains($other->id);
        });
    }

    public function test_show_renders_metadata_and_escapes_json_values(): void
    {
        $log = $this->createLog([
            'actor_id' => $this->superAdmin->id,
            'center_id' => $this->center->id,
            'old_values' => ['status' => 'pending'],
            'new_values' => ['unsafe' => '<script>alert("audit")</script>'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Audit test agent',
        ]);

        $this->actingAsAdmin($this->superAdmin)
            ->get(route('admin.audit-logs.show', $log))
            ->assertOk()
            ->assertSee('Audit log #'.$log->id)
            ->assertSee($this->center->name)
            ->assertSee('Audit test agent')
            ->assertSee('&lt;script&gt;alert(\&quot;audit\&quot;)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert("audit")</script>', false);
    }

    private function createUser(string $role, string $unique, ?int $centerId = null): User
    {
        return User::create([
            'name' => $role.' audit test',
            'username' => $role.'_audit_'.$unique,
            'email' => $role.'_audit_'.$unique.'@medicare.local',
            'password' => Hash::make('Password123!'),
            'role' => $role,
            'center_id' => $centerId,
            'is_active' => true,
            'status' => 'active',
        ]);
    }

    private function createLog(array $attributes = []): AuditLog
    {
        return AuditLog::create($attributes + [
            'action' => 'audit_test_action',
            'resource_type' => 'audit_test',
            'resource_id' => Str::uuid()->toString(),
            'old_values' => ['before' => true],
            'new_values' => ['after' => true],
            'ip_address' => '127.0.0.1',
        ]);
    }

    private function actingAsAdmin(User $user): self
    {
        return $this->actingAs($user)->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $user->id,
            'admin_role' => $user->role,
            'admin_center_id' => $user->center_id,
        ]);
    }
}
