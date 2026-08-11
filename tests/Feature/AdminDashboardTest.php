<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\ConsultationLead;
use Modules\VaccineRegistration\Models\InventoryLot;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Vaccine;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $branchAdminA;
    protected Center $centerA;
    protected Center $centerB;

    protected function setUp(): void
    {
        parent::setUp();

        if (Center::active()->count() < 2) {
            $this->seed(DatabaseSeeder::class);
        }

        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->take(2)->get();
        $this->centerA = $centers[0];
        $this->centerB = $centers[1];

        $unique = Str::lower(Str::random(6));

        $this->superAdmin = User::create([
            'name' => 'Super Admin Test ' . $unique,
            'email' => 'super_dash_' . $unique . '@medicare.local',
            'username' => 'super_dash_' . $unique,
            'password' => Hash::make('Password123!'),
            'role' => 'super_admin',
            'is_active' => true,
            'status' => 'active',
            'center_id' => null,
        ]);

        $this->branchAdminA = User::create([
            'name' => 'Branch Admin A ' . $unique,
            'email' => 'branch_dash_a_' . $unique . '@medicare.local',
            'username' => 'branch_dash_a_' . $unique,
            'password' => Hash::make('Password123!'),
            'role' => 'branch_admin',
            'is_active' => true,
            'status' => 'active',
            'center_id' => $this->centerA->id,
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

    public function test_admin_dashboard_loads_for_super_admin_and_branch_admin(): void
    {
        $responseSuper = $this->actingAsAdmin($this->superAdmin)
            ->get(route('admin.dashboard'));
        $responseSuper->assertStatus(200);
        $responseSuper->assertViewIs('vaccine::admin.dashboard');
        $responseSuper->assertViewHasAll([
            'totalRegistrations',
            'totalRevenue',
            'pendingCount',
            'completedCount',
            'consultCount',
            'importedQuantity',
            'soldQuantity',
            'todayInjectionsCount',
            'dailyTrends',
            'monthlyTrends',
        ]);

        $responseBranch = $this->actingAsAdmin($this->branchAdminA)
            ->get(route('admin.dashboard'));
        $responseBranch->assertStatus(200);
        $responseBranch->assertViewIs('vaccine::admin.dashboard');
    }

    public function test_dynamic_statistics_match_db_and_filter_properly_by_center_id(): void
    {
        // Clean existing test table records for deterministic count assertions
        ConsultationLead::query()->delete();
        InventoryLot::query()->delete();
        Registration::query()->delete();

        // Setup Consultation Leads
        ConsultationLead::create([
            'name' => 'Lead A1',
            'phone' => '0901111111',
            'status' => 'pending',
            'center_id' => $this->centerA->id,
        ]);
        ConsultationLead::create([
            'name' => 'Lead A2',
            'phone' => '0901111112',
            'status' => 'new',
            'center_id' => $this->centerA->id,
        ]);
        ConsultationLead::create([
            'name' => 'Lead A3 Done',
            'phone' => '0901111113',
            'status' => 'contacted', // processed - should NOT be counted
            'center_id' => $this->centerA->id,
        ]);
        ConsultationLead::create([
            'name' => 'Lead B1',
            'phone' => '0902222221',
            'status' => 'pending',
            'center_id' => $this->centerB->id,
        ]);

        // Setup Inventory Lots
        $vaccine = Vaccine::first() ?? Vaccine::create(['name' => 'Test Vac', 'price' => 100000, 'disease_prevention' => 'Test', 'origin' => 'Mỹ', 'age_group' => 'Mọi lứa tuổi', 'doses' => 1]);
        InventoryLot::create([
            'vaccine_id' => $vaccine->id,
            'center_id' => $this->centerA->id,
            'lot_number' => 'LOT-A1-' . Str::random(4),
            'initial_quantity' => 15,
            'available_quantity' => 10,
            'reserved_quantity' => 5,
            'expires_at' => now()->addYear(),
            'status' => 'active',
        ]);
        InventoryLot::create([
            'vaccine_id' => $vaccine->id,
            'center_id' => $this->centerB->id,
            'lot_number' => 'LOT-B1-' . Str::random(4),
            'initial_quantity' => 20,
            'available_quantity' => 20,
            'reserved_quantity' => 0,
            'expires_at' => now()->addYear(),
            'status' => 'active',
        ]);

        // Setup Registrations
        Registration::create([
            'registration_code' => 'REG-A1-' . Str::random(4),
            'patient_name' => 'Patient A1',
            'patient_phone' => '0901111111',
            'center_id' => $this->centerA->id,
            'center_name' => $this->centerA->name,
            'injection_date' => now()->toDateString(),
            'booking_status' => Registration::BOOKING_COMPLETED,
            'payment_status' => Registration::PAYMENT_PAID,
            'payment_method' => 'cash',
            'total_price' => 500000,
            'points_discount_amount' => 0,
        ]);
        Registration::create([
            'registration_code' => 'REG-A2-' . Str::random(4),
            'patient_name' => 'Patient A2',
            'patient_phone' => '0901111112',
            'center_id' => $this->centerA->id,
            'center_name' => $this->centerA->name,
            'injection_date' => now()->toDateString(),
            'booking_status' => Registration::BOOKING_COMPLETED,
            'payment_status' => Registration::PAYMENT_PAID,
            'payment_method' => 'cash',
            'total_price' => 300000,
            'points_discount_amount' => 0,
        ]);
        Registration::create([
            'registration_code' => 'REG-B1-' . Str::random(4),
            'patient_name' => 'Patient B1',
            'patient_phone' => '0902222221',
            'center_id' => $this->centerB->id,
            'center_name' => $this->centerB->name,
            'injection_date' => now()->toDateString(),
            'booking_status' => Registration::BOOKING_COMPLETED,
            'payment_status' => Registration::PAYMENT_PAID,
            'payment_method' => 'cash',
            'total_price' => 400000,
            'points_discount_amount' => 0,
        ]);

        // SuperAdmin - All Centers (no filter)
        $response = $this->actingAsAdmin($this->superAdmin)
            ->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $consultCount = $response->viewData('consultCount');
        $importedQuantity = $response->viewData('importedQuantity');
        $soldQuantity = $response->viewData('soldQuantity');

        $this->assertGreaterThanOrEqual(3, $consultCount);
        $this->assertGreaterThanOrEqual(35, $importedQuantity);
        $this->assertGreaterThanOrEqual(3, $soldQuantity);

        // SuperAdmin - Filter Center A
        $responseFilterA = $this->actingAsAdmin($this->superAdmin)
            ->get(route('admin.dashboard', ['center_id' => $this->centerA->id]));
        $responseFilterA->assertStatus(200);

        $this->assertEquals(2, $responseFilterA->viewData('consultCount'));
        $this->assertEquals(15, $responseFilterA->viewData('importedQuantity'));
        $this->assertEquals(2, $responseFilterA->viewData('soldQuantity'));

        // BranchAdmin A - Automatically scoped to Center A
        $responseBranchA = $this->actingAsAdmin($this->branchAdminA)
            ->get(route('admin.dashboard'));
        $responseBranchA->assertStatus(200);

        $this->assertEquals(2, $responseBranchA->viewData('consultCount'));
        $this->assertEquals(15, $responseBranchA->viewData('importedQuantity'));
        $this->assertEquals(2, $responseBranchA->viewData('soldQuantity'));
    }

    public function test_todays_injections_widget_shows_correct_count_for_todays_date(): void
    {
        Registration::query()->delete();

        $todayStr = now()->toDateString();
        $yesterdayStr = now()->subDay()->toDateString();

        Registration::create([
            'registration_code' => 'TODAY-1-' . Str::random(4),
            'patient_name' => 'Today Patient 1',
            'patient_phone' => '0903333331',
            'center_id' => $this->centerA->id,
            'center_name' => $this->centerA->name,
            'injection_date' => $todayStr,
            'booking_status' => Registration::BOOKING_CONFIRMED,
            'payment_status' => Registration::PAYMENT_UNPAID,
            'payment_method' => 'cash',
            'total_price' => 200000,
        ]);

        Registration::create([
            'registration_code' => 'TODAY-2-' . Str::random(4),
            'patient_name' => 'Today Patient 2',
            'patient_phone' => '0903333332',
            'center_id' => $this->centerA->id,
            'center_name' => $this->centerA->name,
            'injection_date' => $todayStr,
            'booking_status' => Registration::BOOKING_COMPLETED,
            'payment_status' => Registration::PAYMENT_PAID,
            'payment_method' => 'vnpay',
            'total_price' => 350000,
        ]);

        Registration::create([
            'registration_code' => 'YESTERDAY-1-' . Str::random(4),
            'patient_name' => 'Yesterday Patient 1',
            'patient_phone' => '0903333333',
            'center_id' => $this->centerA->id,
            'center_name' => $this->centerA->name,
            'injection_date' => $yesterdayStr,
            'booking_status' => Registration::BOOKING_CONFIRMED,
            'payment_status' => Registration::PAYMENT_UNPAID,
            'payment_method' => 'cash',
            'total_price' => 200000,
        ]);

        $responseBranchA = $this->actingAsAdmin($this->branchAdminA)
            ->get(route('admin.dashboard'));
        $responseBranchA->assertStatus(200);

        $todayCount = $responseBranchA->viewData('todayInjectionsCount');
        $this->assertEquals(2, $todayCount);
    }

    public function test_svg_chart_structure_renders_correctly(): void
    {
        $response = $this->actingAsAdmin($this->superAdmin)
            ->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $content = $response->getContent();

        $this->assertStringContainsString('<svg', $content);
        $this->assertStringContainsString('<polyline', $content);
        $this->assertStringContainsString('<path', $content);
        $this->assertStringContainsString('<circle', $content);

        $this->assertStringContainsString('#c8102e', $content);
        $this->assertStringContainsString('#004b8f', $content);
        $this->assertStringContainsString('#eaaa00', $content);
    }
}
