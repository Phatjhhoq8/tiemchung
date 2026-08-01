<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Models\InventoryLot;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Patient;
use Modules\VaccineRegistration\Models\AdministeredDose;
use Illuminate\Support\Str;

class PatientVaccinationWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private Center $center;
    private Vaccine $vaccine;
    private InventoryLot $lot;

    protected function setUp(): void
    {
        parent::setUp();

        $unique = Str::random(6);

        $this->center = Center::create([
            'name' => 'Trung tâm Test M9 ' . $unique,
            'address' => '456 Đường M9, Q1',
            'phone' => '090' . rand(1000000, 9999999),
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin Test M9 ' . $unique,
            'username' => 'admin_m9_' . $unique,
            'email' => 'admin_m9_' . $unique . '@medicare.vn',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
            'center_id' => $this->center->id,
            'is_active' => true,
            'status' => 'active',
        ]);

        $this->vaccine = Vaccine::create([
            'name' => 'Vắc xin M9 Test ' . $unique,
            'price' => 500000,
            'sale_price' => null,
            'type' => 'single',
            'doses' => 1,
            'stock_status' => 'available',
            'category' => 'Phế cầu',
            'disease_prevention' => 'Phế cầu khuẩn',
            'age_group' => 'Mọi lứa tuổi',
            'origin' => 'Bỉ',
            'description' => 'Vắc xin phòng phế cầu khuẩn',
            'is_active' => true,
        ]);

        $this->lot = InventoryLot::create([
            'vaccine_id' => $this->vaccine->id,
            'center_id' => $this->center->id,
            'lot_number' => 'LOT-M9-' . $unique,
            'initial_quantity' => 100,
            'available_quantity' => 100,
            'reserved_quantity' => 0,
            'expires_at' => now()->addYear(),
            'status' => 'active',
        ]);
    }

    private function loginAsAdmin(): self
    {
        return $this->actingAs($this->admin)->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_role' => $this->admin->role,
            'admin_center_id' => $this->admin->center_id,
        ]);
    }

    /**
     * Requirement 1: Centralized patient profile management without duplicate records.
     */
    public function test_centralized_patient_profile_management_without_duplicate_records(): void
    {
        $this->loginAsAdmin();

        $phone = '099' . rand(1000000, 9999999);
        $identityCard = '123' . rand(10000000, 99999999);

        // 1. Create registration 1 for patient
        $reg1 = Registration::create([
            'registration_code' => 'REG-M9-001-' . Str::random(4),
            'patient_name' => 'Nguyễn Văn M9',
            'patient_dob' => '1990-05-15',
            'patient_gender' => 'Nam',
            'patient_phone' => $phone,
            'patient_address' => '123 HCM',
            'center_id' => $this->center->id,
            'center_name' => $this->center->name,
            'injection_date' => now()->toDateString(),
            'status' => 'pending',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 500000,
        ]);

        // Trigger check-in which links/creates patient profile
        $reg1->checkIn();

        $patient1 = $reg1->patient;
        $this->assertNotNull($patient1);
        $this->assertEquals($phone, $patient1->phone);

        // Update identity card for patient1
        $patient1->update(['identity_card' => $identityCard]);

        // 2. Create registration 2 for SAME patient (matching phone / identity card)
        $reg2 = Registration::create([
            'registration_code' => 'REG-M9-002-' . Str::random(4),
            'patient_name' => 'Nguyễn Văn M9',
            'patient_dob' => '1990-05-15',
            'patient_gender' => 'Nam',
            'patient_phone' => $phone,
            'patient_address' => '123 HCM',
            'center_id' => $this->center->id,
            'center_name' => $this->center->name,
            'injection_date' => now()->addDay()->toDateString(),
            'status' => 'pending',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 500000,
        ]);

        $reg2->checkIn();

        // 3. Verify no duplicate patient record was created
        $this->assertEquals($patient1->id, $reg2->patient_id);
        $this->assertEquals(1, Patient::where('phone', $phone)->count());
    }

    /**
     * Requirement 2: Step 1 Check-in updates registration status to checked_in.
     */
    public function test_step1_check_in_updates_registration_status_to_checked_in(): void
    {
        $this->loginAsAdmin();

        $registration = Registration::create([
            'registration_code' => 'REG-M9-CHECKIN-' . Str::random(4),
            'patient_name' => 'Trần Thị B',
            'patient_dob' => '1995-08-20',
            'patient_gender' => 'Nữ',
            'patient_phone' => '0911223344',
            'patient_address' => 'Hà Nội',
            'center_id' => $this->center->id,
            'center_name' => $this->center->name,
            'injection_date' => now()->toDateString(),
            'status' => 'pending',
            'payment_method' => 'Chuyển khoản',
            'total_price' => 500000,
        ]);

        $response = $this->postJson("/admin/registrations/{$registration->id}/check-in");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $registration->id,
                'status' => 'checked_in',
            ]
        ]);

        $registration->refresh();
        $this->assertEquals('checked_in', $registration->status);
        $this->assertNotNull($registration->patient_id);
    }

    /**
     * Requirement 3: Step 2 Screening logic (eligible permits execution; deferred / contraindicated blocks administration).
     */
    public function test_step2_screening_logic_permits_eligible_and_blocks_deferred_or_contraindicated(): void
    {
        $this->loginAsAdmin();

        // 1. Deferred registration test
        $regDeferred = Registration::create([
            'registration_code' => 'REG-M9-DEF-' . Str::random(4),
            'patient_name' => 'Lê Văn Ho',
            'patient_dob' => '1988-12-12',
            'patient_gender' => 'Nam',
            'patient_phone' => '0933445566',
            'patient_address' => 'Đà Nẵng',
            'center_id' => $this->center->id,
            'center_name' => $this->center->name,
            'injection_date' => now()->toDateString(),
            'status' => 'checked_in',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 500000,
        ]);
        $regDeferred->checkIn();

        // Record screening as 'deferred' (bệnh nhân sốt cao, hoãn tiêm)
        $screeningResp = $this->postJson("/admin/registrations/{$regDeferred->id}/screening", [
            'screening_status' => 'deferred',
            'screening_notes' => 'Bệnh nhân đang sốt 38.5°C, tạm hoãn tiêm 3 ngày',
        ]);
        $screeningResp->assertStatus(200);
        $regDeferred->refresh();
        $this->assertEquals('deferred', $regDeferred->screening_status);

        // Attempt administration -> Must fail with 422
        $administerResp = $this->postJson("/admin/registrations/{$regDeferred->id}/administer", [
            'vaccine_id' => $this->vaccine->id,
            'inventory_lot_id' => $this->lot->id,
        ]);
        $administerResp->assertStatus(422);
        $this->assertDatabaseMissing('administered_doses', [
            'registration_id' => $regDeferred->id,
        ]);

        // 2. Contraindicated registration test
        $regContra = Registration::create([
            'registration_code' => 'REG-M9-CONTRA-' . Str::random(4),
            'patient_name' => 'Phạm Thị Dị Ứng',
            'patient_dob' => '1992-03-10',
            'patient_gender' => 'Nữ',
            'patient_phone' => '0977889900',
            'patient_address' => 'Hải Phòng',
            'center_id' => $this->center->id,
            'center_name' => $this->center->name,
            'injection_date' => now()->toDateString(),
            'status' => 'checked_in',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 500000,
        ]);
        $regContra->checkIn();

        $this->postJson("/admin/registrations/{$regContra->id}/screening", [
            'screening_status' => 'contraindicated',
            'screening_notes' => 'Tiền sử phản vệ độ 3 với thành phần vắc xin, chống chỉ định tiêm',
        ])->assertStatus(200);

        $administerResp2 = $this->postJson("/admin/registrations/{$regContra->id}/administer", [
            'vaccine_id' => $this->vaccine->id,
            'inventory_lot_id' => $this->lot->id,
        ]);
        $administerResp2->assertStatus(422);
        $this->assertDatabaseMissing('administered_doses', [
            'registration_id' => $regContra->id,
        ]);
    }

    /**
     * Requirement 4: Step 3 Vaccination execution creates AdministeredDose with vaccinator ID, lot number, and observation timestamp.
     */
    public function test_step3_vaccination_execution_creates_administered_dose(): void
    {
        $this->loginAsAdmin();

        $registration = Registration::create([
            'registration_code' => 'REG-M9-ADMINISTER-' . Str::random(4),
            'patient_name' => 'Hoàng Văn Tiêm',
            'patient_dob' => '1998-07-07',
            'patient_gender' => 'Nam',
            'patient_phone' => '0944556677',
            'patient_address' => 'Cần Thơ',
            'center_id' => $this->center->id,
            'center_name' => $this->center->name,
            'injection_date' => now()->toDateString(),
            'status' => 'checked_in',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 500000,
        ]);
        $registration->checkIn();

        // Screening: eligible
        $this->postJson("/admin/registrations/{$registration->id}/screening", [
            'screening_status' => 'eligible',
            'screening_notes' => 'Đủ điều kiện sức khỏe tiêm chủng',
        ])->assertStatus(200);

        // Step 3: Administer vaccine
        $response = $this->postJson("/admin/registrations/{$registration->id}/administer", [
            'vaccine_id' => $this->vaccine->id,
            'inventory_lot_id' => $this->lot->id,
            'observation_minutes' => 30,
            'observation_notes' => 'Bệnh nhân tỉnh táo, không có phản ứng bất thường sau 30 phút theo dõi',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Registration status updated to completed
        $registration->refresh();
        $this->assertEquals('completed', $registration->status);

        // AdministeredDose record created with required details
        $dose = AdministeredDose::where('registration_id', $registration->id)->first();
        $this->assertNotNull($dose);
        $this->assertEquals($registration->patient_id, $dose->patient_id);
        $this->assertEquals($this->vaccine->id, $dose->vaccine_id);
        $this->assertEquals($this->lot->id, $dose->inventory_lot_id);
        $this->assertEquals($this->admin->id, $dose->administered_by);
        $this->assertEquals('eligible', $dose->screening_status);
        $this->assertNotNull($dose->administered_at);
        $this->assertNotNull($dose->observation_ended_at);
        $this->assertEquals('completed', $dose->status);
    }
}
