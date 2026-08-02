<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
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

    private Center $center;
    private Center $otherCenter;
    private Vaccine $vaccine;
    private User $admin;
    private string $idempotencyPrefix;

    protected function setUp(): void
    {
        parent::setUp();

        $suffix = Str::random(8);
        $this->idempotencyPrefix = 'slot-' . strtolower(Str::random(12));
        $this->center = Center::create([
            'name' => 'Trung tâm slot ' . $suffix,
            'slug' => 'slot-' . strtolower($suffix),
            'address' => 'Địa chỉ A',
            'phone' => '0912345678',
            'is_active' => true,
        ]);
        $this->otherCenter = Center::create([
            'name' => 'Trung tâm slot B ' . $suffix,
            'slug' => 'slot-b-' . strtolower($suffix),
            'address' => 'Địa chỉ B',
            'phone' => '0987654321',
            'is_active' => true,
        ]);
        $this->vaccine = Vaccine::create([
            'name' => 'Vắc xin slot ' . $suffix,
            'price' => 100000,
            'type' => 'single',
            'doses' => 1,
            'stock_status' => 'available',
            'disease_prevention' => 'Cúm',
            'age_group' => 'Mọi độ tuổi',
            'origin' => 'Việt Nam',
            'is_active' => true,
        ]);
        foreach ([$this->center, $this->otherCenter] as $center) {
            CenterVaccine::create([
                'center_id' => $center->id,
                'vaccine_id' => $this->vaccine->id,
                'price' => $center->id === $this->center->id ? 100000 : 120000,
                'stock_status' => 'available',
                'stock_quantity' => 20,
                'is_active' => true,
            ]);
        }
        $this->admin = User::create([
            'name' => 'Admin slot ' . $suffix,
            'username' => 'slot_admin_' . strtolower($suffix),
            'email' => 'slot_admin_' . strtolower($suffix) . '@example.test',
            'password' => bcrypt('Password123!'),
            'role' => 'super_admin',
            'is_active' => true,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_create_one_schedule_with_multiple_slots(): void
    {
        $response = $this->actingAsAdmin()->postJson(route('admin.schedules.store'), [
            'center_id' => $this->center->id,
            'date' => today()->addDays(2)->toDateString(),
            'slots' => [
                ['start_at' => '08:00', 'end_at' => '09:00', 'capacity' => 5],
                ['start_at' => '09:30', 'end_at' => '10:30', 'capacity' => 6],
            ],
        ]);

        $response->assertCreated()->assertJsonPath('success', true);
        $this->assertDatabaseHas('schedules', ['center_id' => $this->center->id, 'date' => today()->addDays(2)->toDateString()]);
        $this->assertDatabaseHas('slots', ['start_at' => '08:00', 'end_at' => '09:00', 'capacity' => 5]);
    }

    public function test_booking_uses_current_branch_slot_and_branch_price(): void
    {
        $slot = $this->slotFor($this->center, 3);

        $response = $this->book($this->center, $slot, 'Nguyễn Văn A', '0912345678', 'booking-a');
        $response->assertRedirect(route('register.success'));

        $registration = Registration::where('idempotency_key', $this->idempotencyKey('booking-a'))->firstOrFail();
        $this->assertSame($this->center->id, $registration->center_id);
        $this->assertSame('+84912345678', $registration->patient_phone);
        $this->assertSame(100000, $registration->total_price);
        $this->assertSame('pending', $registration->booking_status);
        $this->assertSame('unpaid', $registration->payment_status);
        $this->assertSame(1, $slot->fresh()->reserved_count);
        $this->assertDatabaseHas('registration_vaccines', [
            'registration_id' => $registration->id,
            'vaccine_id' => $this->vaccine->id,
            'price' => 100000,
            'quantity' => 1,
        ]);
    }

    public function test_booking_rejects_slot_of_another_branch_and_full_slot(): void
    {
        $otherSlot = $this->slotFor($this->otherCenter, 1);
        $wrongBranch = $this->book($this->center, $otherSlot, 'Nguyễn Văn B', '0912345679', 'booking-wrong');
        $wrongBranch->assertSessionHasErrors('slot_id');
        $this->assertSame(0, $otherSlot->fresh()->reserved_count);

        $fullSlot = $this->slotFor($this->center, 1);
        $fullSlot->update(['reserved_count' => 1]);
        $full = $this->book($this->center, $fullSlot, 'Nguyễn Văn C', '0912345680', 'booking-full');
        $full->assertSessionHasErrors('slot_id');
        $this->assertSame(1, $fullSlot->fresh()->reserved_count);
    }

    public function test_sequential_attempts_never_exceed_slot_capacity(): void
    {
        $slot = $this->slotFor($this->center, 2);
        $this->book($this->center, $slot, 'Khách 1', '0912345681', 'capacity-1')->assertRedirect();
        $this->book($this->center, $slot, 'Khách 2', '0912345682', 'capacity-2')->assertRedirect();
        $this->book($this->center, $slot, 'Khách 3', '0912345683', 'capacity-3')->assertSessionHasErrors('slot_id');

        $this->assertSame(2, $slot->fresh()->reserved_count);
        $this->assertSame(2, Registration::whereIn('idempotency_key', [
            $this->idempotencyKey('capacity-1'),
            $this->idempotencyKey('capacity-2'),
            $this->idempotencyKey('capacity-3'),
        ])->count());
    }

    private function slotFor(Center $center, int $capacity): Slot
    {
        $schedule = Schedule::create([
            'center_id' => $center->id,
            'date' => today()->addDays(3)->toDateString(),
            'is_active' => true,
        ]);

        return Slot::create([
            'schedule_id' => $schedule->id,
            'start_at' => '08:00',
            'end_at' => '09:00',
            'capacity' => $capacity,
            'reserved_count' => 0,
            'is_active' => true,
        ]);
    }

    private function book(Center $center, Slot $slot, string $name, string $phone, string $key)
    {
        return $this->withSession(['selected_center_id' => $center->id])->post(route('register.post'), [
            'patient_name' => $name,
            'patient_phone' => $phone,
            'slot_id' => $slot->id,
            'vaccine_ids' => [$this->vaccine->id],
            'idempotency_key' => $this->idempotencyKey($key),
        ]);
    }

    private function idempotencyKey(string $key): string
    {
        return $this->idempotencyPrefix . '-' . $key;
    }

    private function actingAsAdmin()
    {
        return $this->actingAs($this->admin)->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_role' => $this->admin->role,
        ]);
    }
}
