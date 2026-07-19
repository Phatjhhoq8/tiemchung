<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Vaccine;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

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
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('vaccines', 15);
        $this->assertDatabaseCount('centers', 6);
        $this->assertDatabaseCount('settings', 6);
        $this->assertDatabaseCount('banners', 3);
        $this->assertDatabaseHas('vaccines', [
            'name' => 'Qdenga (Nhật Bản)',
            'type' => 'single',
            'doses' => 2,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Đăng Ký Tiêm Chủng Trực Tuyến Dễ Dàng');

        $this->get('/vaccines?type=package')
            ->assertOk()
            ->assertSee('Gói Vắc Xin Người Cao Tuổi & Bệnh Nền');
    }

    public function test_existing_registration_flow_persists_the_selected_seeded_vaccine(): void
    {
        $this->seed(DatabaseSeeder::class);
        $vaccine = Vaccine::where('name', 'Qdenga (Nhật Bản)')->firstOrFail();

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
            'patient_name' => 'Nguyen Van A',
            'patient_dob' => now()->subYears(25)->toDateString(),
            'patient_gender' => 'Nam',
            'patient_phone' => '0912345678',
            'patient_address' => 'Can Tho',
            'center_name' => 'Medicare Cờ Đỏ (Trụ sở chính)',
            'injection_date' => now()->addDay()->toDateString(),
            'payment_method' => 'QR',
        ]);

        $response->assertRedirect(route('register.success'));
        $response->assertSessionMissing('cart');

        $registration = Registration::firstOrFail();
        $this->assertSame('Đã thanh toán', $registration->status);
        $this->assertSame($vaccine->price, $registration->total_price);
        $this->assertDatabaseHas('registration_vaccines', [
            'registration_id' => $registration->id,
            'vaccine_id' => $vaccine->id,
            'price' => $vaccine->price,
        ]);

        $this->get('/success')->assertOk()->assertSee($registration->registration_code);
    }
}
