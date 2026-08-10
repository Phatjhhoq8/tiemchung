<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Vaccine;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_mysql_migrations_create_the_vaccine_registration_schema(): void
    {
        foreach ([
            'vaccines',
            'registrations',
            'registration_vaccines',
            'customers',
            'point_transactions',
            'centers',
            'settings',
            'banners',
            'sessions',
            'cache',
            'jobs',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing {$table} table.");
        }

        foreach ([
            'doses',
            'administration_route',
            'detailed_schedule',
            'contraindications',
            'adverse_effects',
            'warnings',
            'source_reference_url',
            'source_review_date',
        ] as $column) {
            $this->assertTrue(Schema::hasColumn('vaccines', $column), "Missing vaccines.{$column} column.");
        }
        $this->assertFalse(Schema::hasColumn('vaccines', 'type'));
    }

    public function test_general_seed_data_does_not_insert_vaccines(): void
    {
        $initialVaccineCount = Vaccine::count();
        $this->seed(DatabaseSeeder::class);

        $this->assertSame($initialVaccineCount, Vaccine::count());
        $this->assertGreaterThanOrEqual(2, \Modules\VaccineRegistration\Models\Center::count());
        $this->assertGreaterThanOrEqual(6, \Modules\VaccineRegistration\Models\Setting::count());
        $this->assertGreaterThanOrEqual(3, \Modules\VaccineRegistration\Models\Banner::count());

        $this->get('/')
            ->assertOk()
            ->assertSee('Medicare Cờ Đỏ');

        $this->get('/vaccines')->assertOk();
    }

    public function test_database_seeder_does_not_reactivate_a_deleted_vaccine(): void
    {
        $vaccine = $this->createVaccine();
        $vaccine->delete();

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('vaccines', [
            'name' => 'Vaccine kiểm thử',
            'is_active' => false,
        ]);
    }

    public function test_existing_registration_flow_persists_the_selected_seeded_vaccine(): void
    {
        $this->seed(DatabaseSeeder::class);
        [$vaccine, $center] = $this->createBranchVaccine();

        $slot = \Modules\VaccineRegistration\Models\Slot::whereHas('schedule', function ($query) use ($center) {
            $query->where('center_id', $center->id)->whereDate('date', '>=', today())->where('is_active', true);
        })->where('is_active', true)->whereColumn('reserved_count', '<', 'capacity')->firstOrFail();

        $response = $this->withSession([
            'selected_center_id' => $center->id,
            'cart' => [
                $vaccine->id => [
                    'name' => $vaccine->name,
                    'price' => $vaccine->price,
                    'doses' => $vaccine->doses,
                    'disease_prevention' => $vaccine->disease_prevention,
                    'origin' => $vaccine->origin,
                    'image' => $vaccine->image,
                ],
            ],
        ])->post('/register', [
            'patient_name' => 'Nguyen Van A',
            'patient_phone' => '0912345678',
            'slot_id' => $slot->id,
            'vaccine_ids' => [$vaccine->id],
            'idempotency_key' => 'example-booking-' . uniqid(),
        ]);

        $response->assertRedirect(route('register.success'));
        $response->assertSessionMissing('cart');

        $registration = Registration::where('patient_phone', '+84912345678')->latest()->firstOrFail();
        $this->assertSame('pending', $registration->booking_status);
        $this->assertSame('unpaid', $registration->payment_status);
        $this->assertDatabaseHas('registration_vaccines', [
            'registration_id' => $registration->id,
            'vaccine_id' => $vaccine->id,
        ]);

        $this->get('/success')->assertOk()->assertSee($registration->registration_code);
    }

    public function test_disease_details_page_renders_successfully(): void
    {
        $this->seed(DatabaseSeeder::class);
        $disease = 'Cúm';
        $response = $this->get('/vaccines/disease/' . urlencode($disease));
        $response->assertOk();
    }

    public function test_vaccine_detail_uses_verified_product_fields_without_influenza_defaults(): void
    {
        $this->seed(DatabaseSeeder::class);
        [$vaccine, $center] = $this->createBranchVaccine();

        $vaccine->update([
            'administration_route' => 'Đường dùng đã xác minh',
            'detailed_schedule' => 'Lịch tiêm đã xác minh',
            'contraindications' => 'Chống chỉ định đã xác minh',
            'adverse_effects' => 'Phản ứng đã xác minh',
            'warnings' => 'Cảnh báo đã xác minh',
            'source_reference_url' => 'https://example.test/verified-source',
            'source_review_date' => '2026-08-10',
        ]);

        $response = $this->withSession(['selected_center_id' => $center->id])
            ->get(route('vaccine.show', $vaccine->id));

        $response->assertOk()
            ->assertSee('Đường dùng đã xác minh')
            ->assertSee('Lịch tiêm đã xác minh')
            ->assertSee('Chống chỉ định đã xác minh')
            ->assertSee('Phản ứng đã xác minh')
            ->assertSee('Cảnh báo đã xác minh')
            ->assertSee('https://example.test/verified-source', false)
            ->assertSee(route('vaccine.index', ['disease' => $vaccine->disease_prevention]), false)
            ->assertDontSee('dòng vắc xin cúm tam giá')
            ->assertDontSee('B/Yamagata')
            ->assertDontSee('an toàn sử dụng cho thai phụ');
    }

    public function test_submitting_consultation_form_creates_consultation_lead_record(): void
    {
        $this->seed(DatabaseSeeder::class);
        
        $response = $this->post('/vaccines/disease/C%C3%BAm/consult', [
            'customerName' => 'Bệnh nhân test tư vấn',
            'customerPhone' => '0987654321',
            'centerName' => 'Medicare Cờ Đỏ (Trụ sở chính)',
            'consultType' => 'online',
            'customerNote' => 'Cần tư vấn cúm cho người già'
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'lead_id'
        ]);

        $this->assertDatabaseHas('consultation_leads', [
            'name' => 'Bệnh nhân test tư vấn',
            'phone' => '0987654321',
        ]);
    }

    public function test_admin_schedule_page_displays_appointments(): void
    {
        $this->seed(DatabaseSeeder::class);

        Registration::where('registration_code', 'MCD-TEST123')->delete();
        \App\Models\User::where('username', 'adminscheduletest')->delete();

        // Tạo một lịch hẹn tiêm chủng giả lập
        $registration = Registration::create([
            'registration_code' => 'MCD-TEST123',
            'patient_name' => 'Bệnh nhân kiểm thử lịch',
            'patient_dob' => '1995-05-15',
            'patient_gender' => 'Nữ',
            'patient_phone' => '0912345678',
            'patient_address' => '123 Đường Test, Cần Thơ',
            'center_name' => 'Medicare Cờ Đỏ (Trụ sở chính)',
            'injection_date' => now()->startOfWeek()->toDateString(), // Đặt ngày tiêm vào đầu tuần hiện tại
            'status' => 'Chờ thanh toán',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 150000,
        ]);

        $admin = \App\Models\User::create([
            'name' => 'Admin Schedule Test',
            'username' => 'adminscheduletest',
            'email' => 'adminscheduletest@medicare.local',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'super_admin',
            'is_active' => true,
            'status' => 'active',
        ]);

        // Truy cập với quyền Admin
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $admin->id,
            'admin_role' => $admin->role,
        ])->get('/admin/schedule');

        $response->assertOk();
        $response->assertSee('Bệnh nhân kiểm thử lịch');
        $response->assertSee('MCD-TEST123');
    }

    private function createVaccine(): Vaccine
    {
        return Vaccine::create([
            'name' => 'Vaccine kiểm thử',
            'price' => 150000,
            'sale_price' => 150000,
            'doses' => 1,
            'stock_status' => 'available',
            'disease_prevention' => 'Bệnh kiểm thử',
            'age_group' => 'Mọi độ tuổi phù hợp',
            'origin' => 'Việt Nam',
            'description' => 'Nội dung vaccine do quản trị viên nhập.',
            'is_active' => true,
        ]);
    }

    private function createBranchVaccine(): array
    {
        $vaccine = $this->createVaccine();
        $center = Center::active()->firstOrFail();

        CenterVaccine::create([
            'center_id' => $center->id,
            'vaccine_id' => $vaccine->id,
            'price' => $vaccine->price,
            'sale_price' => $vaccine->sale_price,
            'stock_quantity' => 5,
            'stock_status' => 'limited',
            'is_active' => true,
        ]);

        return [$vaccine, $center];
    }
}
