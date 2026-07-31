<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\ConsultationLead;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Vaccine;
use Tests\TestCase;

class CrmLeadsAndRegistrationIdempotencyTest extends TestCase
{
    use DatabaseTransactions;

    protected Center $center;
    protected Vaccine $vaccine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->center = Center::firstOrCreate(
            ['name' => 'Trung tâm Test MCD'],
            [
                'code' => 'MCD-TEST',
                'address' => '123 Đường Test, Quận 1',
                'phone' => '02812345678',
                'email' => 'testcenter@medicare.local',
                'status' => 'active',
                'is_active' => true,
            ]
        );

        $this->vaccine = Vaccine::firstOrCreate(
            ['name' => 'Vắc xin Test Idempotency'],
            [
                'price' => 500000,
                'sale_price' => null,
                'type' => 'single',
                'doses' => 1,
                'stock_status' => 'available',
                'category' => 'Cúm',
                'disease_prevention' => 'Cúm mùa',
                'age_group' => 'Trẻ em và người lớn',
                'origin' => 'Việt Nam',
                'description' => 'Mô tả vắc xin test',
                'is_active' => true,
            ]
        );

        CenterVaccine::firstOrCreate(
            [
                'center_id' => $this->center->id,
                'vaccine_id' => $this->vaccine->id,
            ],
            [
                'price' => 500000,
                'sale_price' => null,
                'stock_quantity' => 100,
                'stock_status' => 'available',
                'is_active' => true,
            ]
        );
    }

    /**
     * Test 1: Public lead submission creates consultation_leads record and NO registrations record.
     */
    public function test_public_lead_submission_creates_consultation_lead_and_no_registration(): void
    {
        $initialLeadsCount = ConsultationLead::count();
        $initialRegistrationsCount = Registration::count();

        // 1. Direct consultation endpoint
        $response1 = $this->postJson('/consultations', [
            'name' => 'Nguyễn Văn Lead Test',
            'phone' => '0912345678',
            'source' => 'Website Public Form',
            'note' => 'Cần tư vấn vắc xin Cúm cho người lớn',
            'center_id' => $this->center->id,
        ]);

        $response1->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('consultation_leads', [
            'name' => 'Nguyễn Văn Lead Test',
            'phone' => '0912345678',
            'source' => 'Website Public Form',
            'center_id' => $this->center->id,
        ]);

        // 2. Disease consultation endpoint
        $response2 = $this->postJson('/vaccines/disease/hpv/consult', [
            'consultType' => 'online',
            'customerName' => 'Trần Thị Lead HPV',
            'customerPhone' => '0987654321',
            'customerNote' => 'Tư vấn phác đồ HPV',
        ]);

        $response2->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('consultation_leads', [
            'name' => 'Trần Thị Lead HPV',
            'phone' => '0987654321',
        ]);

        // Verify NO registrations records were created by lead submissions
        $this->assertEquals($initialLeadsCount + 2, ConsultationLead::count());
        $this->assertEquals($initialRegistrationsCount, Registration::count());
    }

    /**
     * Test 2: Registration creation correctly calculates and stores quantity and price in registration_vaccines pivot.
     */
    public function test_registration_creation_calculates_and_stores_quantity_and_price_in_pivot(): void
    {
        $payload = [
            'center_id' => $this->center->id,
            'injection_date' => now()->addDays(2)->toDateString(),
            'payment_method' => 'Tại trung tâm',
            'patients' => [
                [
                    'name' => 'Lê Văn Tiêm Test',
                    'dob' => '1995-05-15',
                    'gender' => 'Nam',
                    'phone' => '0933112233',
                    'address' => '456 Lê Duẩn, Cần Thơ',
                    'vaccine_ids' => [$this->vaccine->id],
                    'quantity' => 2,
                ]
            ]
        ];

        $response = $this->postJson('/register', $payload);
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $registration = Registration::where('patient_phone', '0933112233')->latest()->firstOrFail();

        // Verify registration total price
        $this->assertEquals(1000000, $registration->total_price);

        // Verify pivot relationship and attributes
        $registeredVaccines = $registration->vaccines;
        $this->assertCount(1, $registeredVaccines);

        $pivot = $registeredVaccines->first()->pivot;
        $this->assertEquals(2, $pivot->quantity);
        $this->assertEquals(500000, $pivot->price);
    }

    /**
     * Test 3: Duplicate registration request with identical idempotency_key returns existing registration without creating a second record in DB.
     */
    public function test_duplicate_registration_request_with_identical_idempotency_key_returns_existing_registration_without_second_record(): void
    {
        $idempotencyKey = 'idempotent-test-key-' . uniqid();

        $payload = [
            'idempotency_key' => $idempotencyKey,
            'center_id' => $this->center->id,
            'injection_date' => now()->addDays(3)->toDateString(),
            'payment_method' => 'Tại trung tâm',
            'patients' => [
                [
                    'name' => 'Phạm Thị Trùng Lặp',
                    'dob' => '1990-08-20',
                    'gender' => 'Nữ',
                    'phone' => '0977889900',
                    'address' => '789 Nguyễn Trãi, TP HCM',
                    'vaccine_ids' => [$this->vaccine->id],
                    'quantity' => 1,
                ]
            ]
        ];

        $initialRegistrationsCount = Registration::count();

        // First request with idempotency key
        $response1 = $this->postJson('/register', $payload, [
            'Idempotency-Key' => $idempotencyKey,
        ]);

        $response1->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertEquals($initialRegistrationsCount + 1, Registration::count());
        $registrationCode1 = $response1->json('registration_codes.0');

        // Second request with IDENTICAL idempotency key
        $response2 = $this->postJson('/register', $payload, [
            'Idempotency-Key' => $idempotencyKey,
        ]);

        $response2->assertStatus(200)
            ->assertJson(['success' => true]);

        $registrationCode2 = $response2->json('registration_codes.0');

        // Must return identical response
        $this->assertEquals($registrationCode1, $registrationCode2);

        // Database count MUST NOT increase
        $this->assertEquals($initialRegistrationsCount + 1, Registration::count());
    }

    /**
     * Test 4: Admin can view and update consultation lead status.
     */
    public function test_admin_can_view_and_update_status_of_consultation_lead(): void
    {
        $lead = ConsultationLead::create([
            'name' => 'Nguyễn Admin Test Lead',
            'phone' => '0944556677',
            'source' => 'Website',
            'status' => 'new',
            'note' => 'Tư vấn phác đồ',
            'center_id' => $this->center->id,
        ]);

        $admin = User::firstOrCreate(
            ['username' => 'admin_lead_tester'],
            [
                'name' => 'Admin Tester',
                'email' => 'admin_lead_tester@medicare.local',
                'password' => bcrypt('password123'),
                'role' => 'super_admin',
                'status' => 'active',
            ]
        );

        $this->actingAs($admin)->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $admin->id,
            'admin_role' => $admin->role,
        ]);

        // View index
        $indexResponse = $this->get('/admin/leads');
        $indexResponse->assertStatus(200);

        // Update status
        $statusResponse = $this->patchJson('/admin/leads/' . $lead->id . '/status', [
            'status' => 'contacted',
            'note' => 'Đã gọi điện hỗ trợ tư vấn cho khách',
        ]);

        $statusResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('consultation_leads', [
            'id' => $lead->id,
            'status' => 'contacted',
            'note' => 'Đã gọi điện hỗ trợ tư vấn cho khách',
        ]);
    }
}
