<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Support\AdminContext;
use Tests\TestCase;

class AdminRootGlobalBranchContextTest extends TestCase
{
    use DatabaseTransactions;

    private User $superAdmin;
    private User $branchAdmin;
    private Center $centerA;
    private Center $centerB;
    private Vaccine $vaccine;

    protected function setUp(): void
    {
        parent::setUp();

        if (Center::active()->count() < 2) {
            $this->seed(DatabaseSeeder::class);
        }

        [$this->centerA, $this->centerB] = Center::active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->take(2)
            ->get()
            ->all();

        $unique = Str::lower(Str::random(8));
        $this->superAdmin = User::create([
            'name' => 'Admin gốc kiểm thử',
            'username' => 'root_context_' . $unique,
            'email' => 'root_context_' . $unique . '@medicare.local',
            'password' => Hash::make('Password123!'),
            'role' => 'super_admin',
            'is_active' => true,
            'status' => 'active',
        ]);
        $this->branchAdmin = User::create([
            'name' => 'Admin chi nhánh kiểm thử',
            'username' => 'branch_context_' . $unique,
            'email' => 'branch_context_' . $unique . '@medicare.local',
            'password' => Hash::make('Password123!'),
            'role' => 'branch_admin',
            'center_id' => $this->centerA->id,
            'is_active' => true,
            'status' => 'active',
        ]);

        $this->vaccine = Vaccine::create([
            'name' => 'Vắc xin lọc tồn ' . $unique,
            'disease_prevention' => 'Phòng bệnh kiểm thử',
            'age_group' => 'Mọi độ tuổi',
            'origin' => 'Việt Nam',
            'doses' => 1,
            'price' => 100000,
            'stock_status' => 'available',
            'is_active' => true,
        ]);

        CenterVaccine::create([
            'center_id' => $this->centerA->id,
            'vaccine_id' => $this->vaccine->id,
            'price' => 100000,
            'stock_quantity' => 12,
            'stock_status' => 'available',
            'is_active' => true,
        ]);
        CenterVaccine::create([
            'center_id' => $this->centerB->id,
            'vaccine_id' => $this->vaccine->id,
            'price' => 110000,
            'stock_quantity' => 3,
            'stock_status' => 'limited',
            'is_active' => true,
        ]);
    }

    public function test_root_sees_every_branch_and_full_vaccine_actions_by_default(): void
    {
        $response = $this->actingAsAdmin($this->superAdmin)->get(route('admin.vaccines.index', [
            'search' => $this->vaccine->name,
        ]));

        $response->assertOk()
            ->assertViewHas('selectedCenterId', null)
            ->assertSee('Tất cả chi nhánh')
            ->assertSee('Số lượng từ')
            ->assertSee('Số lượng đến')
            ->assertSee('Thêm Vắc xin Mới')
            ->assertSee('Sửa')
            ->assertSee('Xóa')
            ->assertDontSee('Chỉ xem');

        $response->assertViewHas('vaccines', function ($vaccines) {
            $rows = collect($vaccines->items())->where('id', $this->vaccine->id);

            return $rows->count() === 2
                && $rows->pluck('center_id')->map(fn ($id) => (int) $id)->sort()->values()->all()
                    === collect([$this->centerA->id, $this->centerB->id])->sort()->values()->all();
        });
    }

    public function test_vaccine_quantity_range_filters_each_branch_row(): void
    {
        $minimumResponse = $this->actingAsAdmin($this->superAdmin)->get(route('admin.vaccines.index', [
            'search' => $this->vaccine->name,
            'min_quantity' => 10,
        ]));

        $minimumResponse->assertOk()->assertViewHas('vaccines', function ($vaccines) {
            $rows = collect($vaccines->items());

            return $rows->count() === 1
                && (int) $rows->first()->center_id === $this->centerA->id
                && (int) $rows->first()->stock_quantity === 12;
        });

        $maximumResponse = $this->actingAsAdmin($this->superAdmin)->get(route('admin.vaccines.index', [
            'search' => $this->vaccine->name,
            'max_quantity' => 3,
        ]));

        $maximumResponse->assertOk()->assertViewHas('vaccines', function ($vaccines) {
            $rows = collect($vaccines->items());

            return $rows->count() === 1
                && (int) $rows->first()->center_id === $this->centerB->id
                && (int) $rows->first()->stock_quantity === 3;
        });
    }

    public function test_root_all_view_includes_vaccine_temporarily_disabled_at_a_branch(): void
    {
        CenterVaccine::where('center_id', $this->centerB->id)
            ->where('vaccine_id', $this->vaccine->id)
            ->update(['is_active' => false]);

        $response = $this->actingAsAdmin($this->superAdmin)->get(route('admin.vaccines.index', [
            'search' => $this->vaccine->name,
        ]));

        $response->assertOk()
            ->assertSee($this->centerB->name)
            ->assertSee('Tạm ngưng')
            ->assertViewHas('vaccines', function ($vaccines) {
                $row = collect($vaccines->items())
                    ->first(fn ($item) => (int) $item->center_id === $this->centerB->id);

                return $row && !(bool) $row->center_is_active;
            });

        $payload = [
            'name' => $this->vaccine->name,
            'center_id' => $this->centerB->id,
            'price' => 115000,
            'doses' => $this->vaccine->doses,
            'stock_status' => 'limited',
            'disease_prevention' => $this->vaccine->disease_prevention,
            'age_group' => $this->vaccine->age_group,
            'origin' => $this->vaccine->origin,
        ];
        $this->actingAsAdmin($this->superAdmin)
            ->put(route('admin.vaccines.update', $this->vaccine->id), $payload)
            ->assertRedirect();
        $this->assertDatabaseHas('center_vaccines', [
            'center_id' => $this->centerB->id,
            'vaccine_id' => $this->vaccine->id,
            'is_active' => false,
        ]);

        $this->actingAsAdmin($this->superAdmin)
            ->put(route('admin.vaccines.update', $this->vaccine->id), $payload + ['center_is_active' => 1])
            ->assertRedirect();
        $this->assertDatabaseHas('center_vaccines', [
            'center_id' => $this->centerB->id,
            'vaccine_id' => $this->vaccine->id,
            'is_active' => true,
        ]);
    }

    public function test_global_selector_persists_specific_branch_and_can_return_to_all(): void
    {
        $this->actingAsAdmin($this->superAdmin)
            ->from(route('admin.dashboard'))
            ->post(route('admin.context.center'), ['center_id' => $this->centerB->id])
            ->assertRedirect()
            ->assertSessionHas(AdminContext::SELECTED_CENTER_SESSION_KEY, $this->centerB->id);

        $this->actingAsAdmin($this->superAdmin)
            ->withSession([AdminContext::SELECTED_CENTER_SESSION_KEY => $this->centerB->id])
            ->get(route('admin.vaccines.index'))
            ->assertOk()
            ->assertViewHas('selectedCenterId', $this->centerB->id);

        $this->actingAsAdmin($this->superAdmin)
            ->withSession([AdminContext::SELECTED_CENTER_SESSION_KEY => $this->centerB->id])
            ->from(route('admin.vaccines.index'))
            ->post(route('admin.context.center'), ['center_id' => ''])
            ->assertRedirect()
            ->assertSessionMissing(AdminContext::SELECTED_CENTER_SESSION_KEY);
    }

    public function test_root_login_resets_branch_context_to_all(): void
    {
        $this->withSession([AdminContext::SELECTED_CENTER_SESSION_KEY => $this->centerA->id])
            ->post(route('admin.login'), [
                'username' => $this->superAdmin->username,
                'password' => 'Password123!',
            ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionMissing(AdminContext::SELECTED_CENTER_SESSION_KEY);
    }

    public function test_stock_management_is_not_available_in_admin(): void
    {
        $this->actingAsAdmin($this->superAdmin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Nhập / Xuất Kho');

        $this->actingAsAdmin($this->superAdmin)->get('/admin/stock')->assertNotFound();
        $this->actingAsAdmin($this->superAdmin)->get('/admin/stock/create')->assertNotFound();
        $this->actingAsAdmin($this->superAdmin)->post('/admin/stock')->assertNotFound();
    }

    public function test_all_branch_stock_endpoint_is_reserved_for_root(): void
    {
        $this->actingAsAdmin($this->superAdmin)
            ->getJson(route('admin.vaccines.branches-stock', $this->vaccine->id))
            ->assertOk()
            ->assertJsonCount(Center::active()->count(), 'branches');

        $this->actingAsAdmin($this->branchAdmin)
            ->getJson(route('admin.vaccines.branches-stock', $this->vaccine->id))
            ->assertForbidden();
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
