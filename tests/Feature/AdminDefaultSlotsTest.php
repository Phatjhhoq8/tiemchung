<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\DefaultSlot;
use Modules\VaccineRegistration\Models\Schedule;
use Modules\VaccineRegistration\Models\Slot;
use Tests\TestCase;

class AdminDefaultSlotsTest extends TestCase
{
    use DatabaseTransactions;

    private Center $center;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $suffix = Str::random(8);
        $this->center = Center::create([
            'name' => 'Chi nhánh Test ' . $suffix,
            'slug' => 'cn-test-' . strtolower($suffix),
            'address' => 'Địa chỉ Test',
            'phone' => '0912111222',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin Test ' . $suffix,
            'username' => 'test_admin_' . strtolower($suffix),
            'email' => 'test_admin_' . strtolower($suffix) . '@example.test',
            'password' => bcrypt('Password123!'),
            'role' => 'super_admin',
            'is_active' => true,
            'status' => 'active',
        ]);
    }

    private function actingAsAdmin()
    {
        return $this->actingAs($this->admin)->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_role' => $this->admin->role,
        ]);
    }

    /**
     * Test admin can update default slots configuration.
     */
    public function test_admin_can_update_default_slots(): void
    {
        $response = $this->actingAsAdmin()->post(route('admin.default-slots.update'), [
            'center_id' => $this->center->id,
            'day_of_week' => 1, // Thứ 2
            'slots' => [
                ['start_at' => '08:00', 'end_at' => '09:00', 'capacity' => 12, 'is_active' => 1],
                ['start_at' => '14:00', 'end_at' => '15:00', 'capacity' => 15, 'is_active' => 1],
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('default_slots', [
            'center_id' => $this->center->id,
            'day_of_week' => 1,
            'start_at' => '08:00',
            'capacity' => 12
        ]);
    }

    /**
     * Test dynamic schedule auto-generation from default templates.
     */
    public function test_schedule_auto_generation_from_defaults(): void
    {
        // 1. Create a default slot config for Monday
        DefaultSlot::create([
            'center_id' => $this->center->id,
            'day_of_week' => 1, // Thứ 2
            'start_at' => '09:30',
            'end_at' => '10:30',
            'capacity' => 10,
            'is_active' => true
        ]);

        // Find next Monday
        $nextMonday = today()->next(\Carbon\Carbon::MONDAY);

        // Verify schedule does not exist
        $this->assertDatabaseMissing('schedules', [
            'center_id' => $this->center->id,
            'date' => $nextMonday->toDateString()
        ]);

        // 2. Call the generator
        Schedule::generateFromDefaults($this->center->id, $nextMonday->toDateString(), $nextMonday->toDateString());

        // 3. Verify schedule and slot are generated
        $this->assertDatabaseHas('schedules', [
            'center_id' => $this->center->id,
            'date' => $nextMonday->toDateString()
        ]);

        $schedule = Schedule::where([
            'center_id' => $this->center->id,
            'date' => $nextMonday->toDateString()
        ])->first();

        $this->assertNotNull($schedule);
        $this->assertDatabaseHas('slots', [
            'schedule_id' => $schedule->id,
            'start_at' => '09:30',
            'capacity' => 10
        ]);
    }

    /**
     * Test that generateFromDefaults does not overwrite existing custom schedule.
     */
    public function test_generate_does_not_overwrite_existing_schedule(): void
    {
        $nextMonday = today()->next(\Carbon\Carbon::MONDAY);

        // Pre-create custom schedule
        $customSchedule = Schedule::create([
            'center_id' => $this->center->id,
            'date' => $nextMonday->toDateString(),
            'is_active' => false, // Custom status
            'note' => 'Custom schedule note'
        ]);

        // Pre-create custom slot
        $customSlot = Slot::create([
            'schedule_id' => $customSchedule->id,
            'start_at' => '11:00',
            'end_at' => '12:00',
            'capacity' => 20,
            'reserved_count' => 0,
            'is_active' => true
        ]);

        // Configure default slots
        DefaultSlot::create([
            'center_id' => $this->center->id,
            'day_of_week' => 1,
            'start_at' => '09:00',
            'end_at' => '10:00',
            'capacity' => 10,
            'is_active' => true
        ]);

        // Run generator
        Schedule::generateFromDefaults($this->center->id, $nextMonday->toDateString(), $nextMonday->toDateString());

        // Verify schedule is NOT overwritten (remains inactive and keeps custom slot)
        $schedule = Schedule::findOrFail($customSchedule->id);
        $this->assertFalse($schedule->is_active);
        $this->assertEquals('Custom schedule note', $schedule->note);

        // Should not have the default slot 09:00-10:00
        $this->assertDatabaseMissing('slots', [
            'schedule_id' => $schedule->id,
            'start_at' => '09:00'
        ]);
    }

    /**
     * Test admin can delete a schedule.
     */
    public function test_admin_can_delete_schedule(): void
    {
        $schedule = Schedule::create([
            'center_id' => $this->center->id,
            'date' => today()->addDays(5)->toDateString(),
            'is_active' => true
        ]);

        $response = $this->actingAsAdmin()->delete(route('admin.schedules.destroy', $schedule->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('schedules', ['id' => $schedule->id]);
    }
}
