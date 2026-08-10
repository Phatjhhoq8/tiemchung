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

class M32DashboardChallengerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $branchAdminA;
    protected User $branchAdminB;
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
            'name' => 'Super Admin M32 ' . $unique,
            'email' => 'super_m32_' . $unique . '@medicare.local',
            'username' => 'super_m32_' . $unique,
            'password' => Hash::make('Password123!'),
            'role' => 'super_admin',
            'is_active' => true,
            'status' => 'active',
            'center_id' => null,
        ]);

        $this->branchAdminA = User::create([
            'name' => 'Branch Admin A M32 ' . $unique,
            'email' => 'branch_m32_a_' . $unique . '@medicare.local',
            'username' => 'branch_m32_a_' . $unique,
            'password' => Hash::make('Password123!'),
            'role' => 'branch_admin',
            'is_active' => true,
            'status' => 'active',
            'center_id' => $this->centerA->id,
        ]);

        $this->branchAdminB = User::create([
            'name' => 'Branch Admin B M32 ' . $unique,
            'email' => 'branch_m32_b_' . $unique . '@medicare.local',
            'username' => 'branch_m32_b_' . $unique,
            'password' => Hash::make('Password123!'),
            'role' => 'branch_admin',
            'is_active' => true,
            'status' => 'active',
            'center_id' => $this->centerB->id,
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

    /**
     * Activity 1 & 4: Response times benchmark & general dashboard rendering
     */
    public function test_m32_dashboard_loads_and_passes_performance_benchmark(): void
    {
        $startTime = microtime(true);
        $iterations = 20;

        for ($i = 0; $i < $iterations; $i++) {
            $response = $this->actingAsAdmin($this->superAdmin)->get(route('admin.dashboard'));
            $response->assertStatus(200);
        }

        $durationMs = ((microtime(true) - $startTime) / $iterations) * 1000;
        
        // Assert average response time per dashboard render is under 150ms
        $this->assertLessThan(150, $durationMs, "Dashboard average response time was {$durationMs}ms, exceeding 150ms target.");
    }

    /**
     * Activity 2: Verify strict Medicare color palette compliance (#c8102e, #eaaa00, #004b8f)
     */
    public function test_m32_strict_medicare_color_palette_compliance(): void
    {
        $response = $this->actingAsAdmin($this->superAdmin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $content = $response->getContent();

        // 1. Primary Medicare Red (#c8102e)
        $this->assertStringContainsString('#c8102e', $content, 'Medicare Red (#c8102e) is missing from dashboard markup');

        // 2. Secondary Medicare Gold (#eaaa00)
        $this->assertStringContainsString('#eaaa00', $content, 'Medicare Gold (#eaaa00) is missing from dashboard markup');

        // 3. Accent Medicare Navy (#004b8f)
        $this->assertStringContainsString('#004b8f', $content, 'Medicare Navy (#004b8f) is missing from dashboard markup');
    }

    /**
     * Activity 3: Pure SVG implementation (no external JS chart libraries like Chart.js or ApexCharts)
     */
    public function test_m32_pure_svg_chart_rendering_no_external_js_libraries(): void
    {
        $response = $this->actingAsAdmin($this->superAdmin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $content = $response->getContent();

        // Assert SVG element tags are present
        $this->assertStringContainsString('<svg', $content);
        $this->assertStringContainsString('<polyline', $content);
        $this->assertStringContainsString('<path', $content);
        $this->assertStringContainsString('<circle', $content);
        $this->assertStringContainsString('<linearGradient', $content);

        // Assert NO external JS chart library scripts/invocations
        $this->assertStringNotContainsString('chart.js', strtolower($content));
        $this->assertStringNotContainsString('apexcharts', strtolower($content));
        $this->assertStringNotContainsString('highcharts', strtolower($content));
        $this->assertStringNotContainsString('d3.js', strtolower($content));
        $this->assertStringNotContainsString('new chart(', strtolower($content));
    }

    /**
     * Activity 4: Verify center scoping isolation for BranchAdmin vs SuperAdmin and anti-IDOR tampering
     */
    public function test_m32_center_scoping_isolation_and_parameter_tampering_defense(): void
    {
        // Clear test table records for isolation assertion
        ConsultationLead::query()->delete();
        InventoryLot::query()->delete();
        Registration::query()->delete();

        // Populate Center A data
        ConsultationLead::create([
            'name' => 'Lead Center A 1',
            'phone' => '0901000001',
            'status' => 'pending',
            'center_id' => $this->centerA->id,
        ]);
        ConsultationLead::create([
            'name' => 'Lead Center A 2',
            'phone' => '0901000002',
            'status' => 'new',
            'center_id' => $this->centerA->id,
        ]);

        $vaccine = Vaccine::first() ?? Vaccine::create(['name' => 'Vac M32', 'code' => 'VACM32']);
        InventoryLot::create([
            'vaccine_id' => $vaccine->id,
            'center_id' => $this->centerA->id,
            'lot_number' => 'LOT-M32-A1',
            'initial_quantity' => 100,
            'available_quantity' => 80,
            'reserved_quantity' => 20,
            'expires_at' => now()->addYear(),
            'status' => 'active',
        ]);

        Registration::create([
            'registration_code' => 'REG-M32-A1',
            'patient_name' => 'Patient Center A',
            'patient_phone' => '0901000001',
            'center_id' => $this->centerA->id,
            'center_name' => $this->centerA->name,
            'injection_date' => now()->toDateString(),
            'booking_status' => Registration::BOOKING_COMPLETED,
            'payment_status' => Registration::PAYMENT_PAID,
            'payment_method' => 'cash',
            'total_price' => 500000,
            'points_discount_amount' => 0,
        ]);

        // Populate Center B data
        ConsultationLead::create([
            'name' => 'Lead Center B 1',
            'phone' => '0902000001',
            'status' => 'pending',
            'center_id' => $this->centerB->id,
        ]);

        InventoryLot::create([
            'vaccine_id' => $vaccine->id,
            'center_id' => $this->centerB->id,
            'lot_number' => 'LOT-M32-B1',
            'initial_quantity' => 50,
            'available_quantity' => 50,
            'reserved_quantity' => 0,
            'expires_at' => now()->addYear(),
            'status' => 'active',
        ]);

        Registration::create([
            'registration_code' => 'REG-M32-B1',
            'patient_name' => 'Patient Center B',
            'patient_phone' => '0902000001',
            'center_id' => $this->centerB->id,
            'center_name' => $this->centerB->name,
            'injection_date' => now()->toDateString(),
            'booking_status' => Registration::BOOKING_COMPLETED,
            'payment_status' => Registration::PAYMENT_PAID,
            'payment_method' => 'cash',
            'total_price' => 400000,
            'points_discount_amount' => 0,
        ]);

        // 1. BranchAdmin A loads dashboard normally -> strictly Center A statistics
        $resBranchA = $this->actingAsAdmin($this->branchAdminA)->get(route('admin.dashboard'));
        $resBranchA->assertStatus(200);
        $this->assertEquals(2, $resBranchA->viewData('consultCount'));
        $this->assertEquals(100, $resBranchA->viewData('importedQuantity')); // 80 available + 20 reserved
        $this->assertEquals(1, $resBranchA->viewData('soldQuantity'));
        $this->assertEquals(1, $resBranchA->viewData('todayInjectionsCount'));

        // 2. Anti-IDOR Parameter Tampering: BranchAdmin A attempts to view Center B stats via ?center_id=CenterB
        $resTamper = $this->actingAsAdmin($this->branchAdminA)->get(route('admin.dashboard', ['center_id' => $this->centerB->id]));
        $resTamper->assertStatus(403);

        // 3. SuperAdmin loads dashboard without filter -> aggregated across ALL centers
        $resSuperAll = $this->actingAsAdmin($this->superAdmin)->get(route('admin.dashboard'));
        $resSuperAll->assertStatus(200);
        $this->assertEquals(3, $resSuperAll->viewData('consultCount'));
        $this->assertEquals(150, $resSuperAll->viewData('importedQuantity')); // 100 + 50
        $this->assertEquals(2, $resSuperAll->viewData('soldQuantity'));
        $this->assertEquals(2, $resSuperAll->viewData('todayInjectionsCount'));

        // 4. SuperAdmin explicitly selects Center B filter -> Center B statistics
        $resSuperB = $this->actingAsAdmin($this->superAdmin)->get(route('admin.dashboard', ['center_id' => $this->centerB->id]));
        $resSuperB->assertStatus(200);
        $this->assertEquals(1, $resSuperB->viewData('consultCount'));
        $this->assertEquals(50, $resSuperB->viewData('importedQuantity'));
        $this->assertEquals(1, $resSuperB->viewData('soldQuantity'));
        $this->assertEquals(1, $resSuperB->viewData('todayInjectionsCount'));
    }
}
