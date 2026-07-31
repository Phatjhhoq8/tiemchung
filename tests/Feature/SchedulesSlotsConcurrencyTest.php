<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Schedule;
use Modules\VaccineRegistration\Models\Slot;
use Modules\VaccineRegistration\Models\Vaccine;
use Tests\TestCase;

class SchedulesSlotsConcurrencyTest extends TestCase
{
    use DatabaseTransactions;

    protected Center $center;
    protected Vaccine $vaccine;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->center = Center::firstOrCreate(
            ['name' => 'Trung tâm Test MCD Schedule'],
            [
                'code' => 'MCD-TEST-SLOT',
                'address' => '456 Đường Lịch Hẹn, Ninh Kiều',
                'phone' => '02923888999',
                'email' => 'schedulecenter@medicare.local',
                'status' => 'active',
                'is_active' => true,
            ]
        );

        $this->vaccine = Vaccine::firstOrCreate(
            ['name' => 'Vắc xin Test Slot'],
            [
                'price' => 450000,
                'sale_price' => null,
                'type' => 'single',
                'doses' => 1,
                'stock_status' => 'available',
                'category' => 'Cúm',
                'disease_prevention' => 'Cúm mùa',
                'age_group' => 'Mọi độ tuổi',
                'origin' => 'Pháp',
                'description' => 'Mô tả test slot',
                'is_active' => true,
            ]
        );

        CenterVaccine::firstOrCreate(
            [
                'center_id' => $this->center->id,
                'vaccine_id' => $this->vaccine->id,
            ],
            [
                'price' => 450000,
                'sale_price' => null,
                'stock_quantity' => 50,
                'stock_status' => 'available',
                'is_active' => true,
            ]
        );

        $this->admin = User::firstOrCreate(
            ['username' => 'admin_slot_tester'],
            [
                'name' => 'Admin Slot Tester',
                'email' => 'admin_slot_tester@medicare.local',
                'password' => bcrypt('password123'),
                'role' => 'super_admin',
                'status' => 'active',
            ]
        );
    }

    /**
     * Test 1: Creation of schedules and slots with specified capacity.
     */
    public function test_creation_of_schedules_and_slots_with_specified_capacity(): void
    {
        $this->actingAs($this->admin)->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_role' => $this->admin->role,
        ]);

        $payload = [
            'center_id' => $this->center->id,
            'date' => now()->addDays(5)->toDateString(),
            'note' => 'Lịch khám buổi sáng',
            'slots' => [
                [
                    'start_at' => '08:00',
                    'end_at' => '09:00',
                    'capacity' => 10,
                ],
                [
                    'start_at' => '09:00',
                    'end_at' => '10:00',
                    'capacity' => 15,
                ],
            ]
        ];

        $response = $this->postJson('/admin/schedules', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Tạo lịch làm việc thành công.',
            ]);

        $scheduleId = $response->json('schedule.id');

        $this->assertDatabaseHas('schedules', [
            'id' => $scheduleId,
            'center_id' => $this->center->id,
            'date' => $payload['date'],
            'note' => 'Lịch khám buổi sáng',
        ]);

        $this->assertDatabaseHas('slots', [
            'schedule_id' => $scheduleId,
            'start_at' => '08:00',
            'end_at' => '09:00',
            'capacity' => 10,
            'reserved_count' => 0,
        ]);

        $this->assertDatabaseHas('slots', [
            'schedule_id' => $scheduleId,
            'start_at' => '09:00',
            'end_at' => '10:00',
            'capacity' => 15,
            'reserved_count' => 0,
        ]);
    }

    /**
     * Test 2: Reservation of slot increments reserved_count.
     */
    public function test_reservation_of_slot_increments_reserved_count(): void
    {
        $schedule = Schedule::create([
            'center_id' => $this->center->id,
            'date' => now()->addDays(2)->toDateString(),
            'is_active' => true,
        ]);

        $slot = Slot::create([
            'schedule_id' => $schedule->id,
            'start_at' => '08:00',
            'end_at' => '09:00',
            'capacity' => 5,
            'reserved_count' => 0,
            'is_active' => true,
        ]);

        $payload = [
            'center_id' => $this->center->id,
            'injection_date' => $schedule->date->toDateString(),
            'slot_id' => $slot->id,
            'payment_method' => 'Tại trung tâm',
            'patients' => [
                [
                    'name' => 'Trần Văn Tiêm Slot',
                    'dob' => '1992-03-10',
                    'gender' => 'Nam',
                    'phone' => '0911223344',
                    'address' => '123 Đường 30/4, Cần Thơ',
                    'vaccine_ids' => [$this->vaccine->id],
                    'quantity' => 1,
                    'slot_id' => $slot->id,
                ]
            ]
        ];

        $response = $this->postJson('/register', $payload);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $slot->refresh();
        $this->assertEquals(1, $slot->reserved_count);

        $this->assertDatabaseHas('registrations', [
            'patient_phone' => '0911223344',
            'slot_id' => $slot->id,
            'center_id' => $this->center->id,
        ]);
    }

    /**
     * Test 3: Attempting to reserve a slot when reserved_count >= capacity is rejected (422/error) with zero overbooking.
     */
    public function test_attempting_to_reserve_slot_when_full_is_rejected_with_zero_overbooking(): void
    {
        $schedule = Schedule::create([
            'center_id' => $this->center->id,
            'date' => now()->addDays(3)->toDateString(),
            'is_active' => true,
        ]);

        $slot = Slot::create([
            'schedule_id' => $schedule->id,
            'start_at' => '10:00',
            'end_at' => '11:00',
            'capacity' => 2,
            'reserved_count' => 2, // Full capacity
            'is_active' => true,
        ]);

        $payload = [
            'center_id' => $this->center->id,
            'injection_date' => $schedule->date->toDateString(),
            'slot_id' => $slot->id,
            'payment_method' => 'Tại trung tâm',
            'patients' => [
                [
                    'name' => 'Nguyễn Quá Tải',
                    'dob' => '1998-11-20',
                    'gender' => 'Nữ',
                    'phone' => '0988776655',
                    'address' => '789 Mậu Thân, Cần Thơ',
                    'vaccine_ids' => [$this->vaccine->id],
                    'quantity' => 1,
                    'slot_id' => $slot->id,
                ]
            ]
        ];

        $response = $this->postJson('/register', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Khung giờ đã đầy công suất',
            ]);

        $slot->refresh();
        // Ensure reserved_count did not exceed capacity
        $this->assertEquals(2, $slot->reserved_count);

        $this->assertDatabaseMissing('registrations', [
            'patient_phone' => '0988776655',
        ]);
    }

    /**
     * Test 4: Simulated concurrent reservations with lockForUpdate ensuring exact capacity enforcement.
     */
    public function test_simulated_concurrent_reservations_with_lock_for_update(): void
    {
        $schedule = Schedule::create([
            'center_id' => $this->center->id,
            'date' => now()->addDays(4)->toDateString(),
            'is_active' => true,
        ]);

        $capacity = 3;
        $slot = Slot::create([
            'schedule_id' => $schedule->id,
            'start_at' => '14:00',
            'end_at' => '15:00',
            'capacity' => $capacity,
            'reserved_count' => 0,
            'is_active' => true,
        ]);

        $totalAttempts = 6;
        $successCount = 0;
        $rejectedCount = 0;

        for ($i = 1; $i <= $totalAttempts; $i++) {
            $payload = [
                'center_id' => $this->center->id,
                'injection_date' => $schedule->date->toDateString(),
                'slot_id' => $slot->id,
                'payment_method' => 'Tại trung tâm',
                'patients' => [
                    [
                        'name' => 'Bệnh Nhân Concurrent ' . $i,
                        'dob' => '1995-01-01',
                        'gender' => 'Nam',
                        'phone' => '090000000' . $i,
                        'address' => 'Địa chỉ test ' . $i,
                        'vaccine_ids' => [$this->vaccine->id],
                        'quantity' => 1,
                        'slot_id' => $slot->id,
                    ]
                ]
            ];

            $response = $this->postJson('/register', $payload);

            if ($response->status() === 200 && $response->json('success') === true) {
                $successCount++;
            } elseif ($response->status() === 422 && str_contains($response->json('message', ''), 'Khung giờ đã đầy công suất')) {
                $rejectedCount++;
            }
        }

        $slot->refresh();

        // Exactly capacity (3) reservations succeeded
        $this->assertEquals($capacity, $successCount);
        // Remaining (3) attempts were rejected
        $this->assertEquals($totalAttempts - $capacity, $rejectedCount);
        // Reserved count equals capacity exactly, no overbooking
        $this->assertEquals($capacity, $slot->reserved_count);
    }
}
