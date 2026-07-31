<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
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
            'centers',
            'settings',
            'banners',
            'sessions',
            'cache',
            'jobs',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing {$table} table.");
        }

        $this->assertTrue(Schema::hasColumns('vaccines', ['type', 'doses']));
    }

    public function test_seed_data_is_available_to_the_existing_public_pages_and_can_be_reseeded(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThanOrEqual(15, Vaccine::count());
        $this->assertGreaterThanOrEqual(2, \Modules\VaccineRegistration\Models\Center::count());
        $this->assertGreaterThanOrEqual(6, \Modules\VaccineRegistration\Models\Setting::count());
        $this->assertGreaterThanOrEqual(3, \Modules\VaccineRegistration\Models\Banner::count());

        $this->get('/')
            ->assertOk()
            ->assertSee('Medicare Cờ Đỏ');

        $this->get('/vaccines?type=package')
            ->assertOk();
    }

    public function test_existing_registration_flow_persists_the_selected_seeded_vaccine(): void
    {
        $this->seed(DatabaseSeeder::class);
        $vaccine = Vaccine::where('name', 'like', '%Hexaxim%')->orWhere('name', 'like', '%Qdenga%')->first() ?: Vaccine::firstOrFail();
        $center = \Modules\VaccineRegistration\Models\Center::whereHas('centerVaccines', function ($q) use ($vaccine) {
            $q->where('vaccine_id', $vaccine->id)->where('is_active', true);
        })->firstOrFail();

        $response = $this->withSession([
            'cart' => [
                $vaccine->id => [
                    'name' => $vaccine->name,
                    'price' => $vaccine->price,
                    'type' => $vaccine->type,
                    'doses' => $vaccine->doses,
                    'disease_prevention' => $vaccine->disease_prevention,
                    'origin' => $vaccine->origin,
                    'image' => $vaccine->image,
                ],
            ],
        ])->post('/register', [
            'patients' => [
                [
                    'name' => 'Nguyen Van A',
                    'dob' => now()->subYears(25)->format('Y-m-d'),
                    'gender' => 'Nam',
                    'phone' => '0912345678',
                    'address' => 'Can Tho',
                    'vaccine_ids' => [$vaccine->id],
                ]
            ],
            'center_id' => $center->id,
            'injection_date' => now()->addDay()->format('Y-m-d'),
            'payment_method' => 'QR',
        ]);

        $response->assertRedirect(route('register.success'));
        $response->assertSessionMissing('cart');

        $registration = Registration::latest()->firstOrFail();
        $this->assertSame('Chờ thanh toán', $registration->status);
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
}
