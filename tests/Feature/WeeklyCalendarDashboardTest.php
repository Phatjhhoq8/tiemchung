<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Schedule;
use Modules\VaccineRegistration\Models\Slot;
use Tests\TestCase;

class WeeklyCalendarDashboardTest extends TestCase
{
    use DatabaseTransactions;

    private Center $center;
    private User $superAdmin;
    private User $branchAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $suffix = Str::random(8);
        $this->center = Center::create([
            'name' => 'Chi nhánh Test Grid ' . $suffix,
            'slug' => 'cn-grid-' . strtolower($suffix),
            'address' => '123 Đường Test',
            'phone' => '0988777666',
            'is_active' => true,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Super Admin ' . $suffix,
            'username' => 'sa_grid_' . strtolower($suffix),
            'email' => 'sa_grid_' . strtolower($suffix) . '@example.test',
            'password' => bcrypt('Password123!'),
            'role' => 'super_admin',
            'is_active' => true,
            'status' => 'active',
        ]);

        $this->branchAdmin = User::create([
            'name' => 'Branch Admin ' . $suffix,
            'username' => 'ba_grid_' . strtolower($suffix),
            'email' => 'ba_grid_' . strtolower($suffix) . '@example.test',
            'password' => bcrypt('Password123!'),
            'role' => 'branch_admin',
            'center_id' => $this->center->id,
            'is_active' => true,
            'status' => 'active',
        ]);
    }

    private function actingAsSuperAdmin()
    {
        return $this->actingAs($this->superAdmin)->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->superAdmin->id,
            'admin_role' => $this->superAdmin->role,
        ]);
    }

    private function actingAsBranchAdmin()
    {
        return $this->actingAs($this->branchAdmin)->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->branchAdmin->id,
            'admin_role' => $this->branchAdmin->role,
        ]);
    }

    /**
     * Test weekly schedule grid index returns 7 days of selected week.
     */
    public function test_weekly_schedule_grid_index_returns_7_days_of_selected_week(): void
    {
        $response = $this->actingAsSuperAdmin()->get(route('admin.schedules.index', [
            'center_id' => $this->center->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('weekGrid');

        $weekGrid = $response->viewData('weekGrid');
        $this->assertCount(7, $weekGrid);
    }

    /**
     * Test week navigation filtering with date parameter.
     */
    public function test_week_navigation_filtering(): void
    {
        $targetDate = '2026-08-17'; // A Monday
        $response = $this->actingAsSuperAdmin()->getJson(route('admin.schedules.index', [
            'date' => $targetDate,
            'center_id' => $this->center->id,
        ]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'week_start' => '2026-08-17',
                'selected_center_id' => $this->center->id,
            ]
        ]);
        $response->assertJsonCount(7, 'data.week_grid');
    }

    /**
     * Test slot CRUD AJAX endpoints.
     */
    public function test_slot_crud_ajax_endpoints(): void
    {
        $monday = now()->startOfWeek()->toDateString();
        $schedule = Schedule::create([
            'center_id' => $this->center->id,
            'date' => $monday,
            'is_active' => true,
        ]);

        // 1. Create slot
        $createResp = $this->actingAsSuperAdmin()->postJson(route('admin.slots.store'), [
            'schedule_id' => $schedule->id,
            'start_at' => '08:00',
            'end_at' => '09:00',
            'capacity' => 10,
            'is_active' => 1,
        ]);

        $createResp->assertStatus(201);
        $createResp->assertJson(['success' => true]);
        $slotId = $createResp->json('slot.id');

        $this->assertDatabaseHas('slots', [
            'id' => $slotId,
            'start_at' => '08:00',
            'capacity' => 10,
        ]);

        // 2. Update slot
        $updateResp = $this->actingAsSuperAdmin()->putJson(route('admin.slots.update', $slotId), [
            'capacity' => 15,
            'is_active' => 1,
        ]);

        $updateResp->assertStatus(200);
        $updateResp->assertJson(['success' => true]);
        $this->assertDatabaseHas('slots', [
            'id' => $slotId,
            'capacity' => 15,
        ]);

        // 3. Delete slot
        $deleteResp = $this->actingAsSuperAdmin()->deleteJson(route('admin.slots.destroy', $slotId));

        $deleteResp->assertStatus(200);
        $deleteResp->assertJson(['success' => true]);
        $this->assertDatabaseMissing('slots', ['id' => $slotId]);
    }

    /**
     * Test day toggle status and day schedule deletion.
     */
    public function test_day_toggle_status_and_day_schedule_deletion(): void
    {
        $monday = now()->startOfWeek()->toDateString();

        // 1. Toggle Day Status to OFF
        $toggleResp = $this->actingAsSuperAdmin()->postJson(route('admin.schedules.toggle-day'), [
            'center_id' => $this->center->id,
            'date' => $monday,
            'is_active' => 0,
        ]);

        $toggleResp->assertStatus(200);
        $toggleResp->assertJson([
            'success' => true,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('schedules', [
            'center_id' => $this->center->id,
            'date' => $monday,
            'is_active' => false,
        ]);

        // 2. Delete Day Schedule
        $deleteDayResp = $this->actingAsSuperAdmin()->deleteJson(route('admin.schedules.destroy-day'), [
            'center_id' => $this->center->id,
            'date' => $monday,
        ]);

        $deleteDayResp->assertStatus(200);
        $deleteDayResp->assertJson(['success' => true]);

        $this->assertDatabaseMissing('schedules', [
            'center_id' => $this->center->id,
            'date' => $monday,
        ]);
    }

    /**
     * Test copy schedule from source day to target days when target days have zero bookings.
     */
    public function test_copy_schedule_from_source_day_to_target_days_success_when_reserved_count_zero(): void
    {
        $weekStart = now()->startOfWeek();
        $sourceDate = $weekStart->toDateString();
        $targetDate1 = $weekStart->copy()->addDay()->toDateString(); // Tuesday
        $targetDate2 = $weekStart->copy()->addDays(2)->toDateString(); // Wednesday

        $sourceSchedule = Schedule::create([
            'center_id' => $this->center->id,
            'date' => $sourceDate,
            'is_active' => true,
            'note' => 'Source note',
        ]);

        Slot::create([
            'schedule_id' => $sourceSchedule->id,
            'start_at' => '08:00',
            'end_at' => '09:00',
            'capacity' => 10,
            'reserved_count' => 0,
            'is_active' => true,
        ]);

        Slot::create([
            'schedule_id' => $sourceSchedule->id,
            'start_at' => '09:00',
            'end_at' => '10:00',
            'capacity' => 12,
            'reserved_count' => 0,
            'is_active' => true,
        ]);

        $response = $this->actingAsSuperAdmin()->postJson(route('admin.schedules.copy'), [
            'center_id' => $this->center->id,
            'source_date' => $sourceDate,
            'target_dates' => [$targetDate1, $targetDate2],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Assert target schedules created with cloned slots
        $targetSchedule1 = Schedule::where(['center_id' => $this->center->id, 'date' => $targetDate1])->first();
        $this->assertNotNull($targetSchedule1);
        $this->assertCount(2, $targetSchedule1->slots);

        $targetSchedule2 = Schedule::where(['center_id' => $this->center->id, 'date' => $targetDate2])->first();
        $this->assertNotNull($targetSchedule2);
        $this->assertCount(2, $targetSchedule2->slots);
    }

    /**
     * Test copy schedule BLOCKED with 422 validation response when target day has reserved_count > 0.
     */
    public function test_copy_schedule_blocked_with_422_when_target_day_has_reserved_count_greater_than_zero(): void
    {
        $weekStart = now()->startOfWeek();
        $sourceDate = $weekStart->toDateString();
        $targetDate = $weekStart->copy()->addDay()->toDateString(); // Tuesday

        $sourceSchedule = Schedule::create([
            'center_id' => $this->center->id,
            'date' => $sourceDate,
            'is_active' => true,
        ]);

        Slot::create([
            'schedule_id' => $sourceSchedule->id,
            'start_at' => '08:00',
            'end_at' => '09:00',
            'capacity' => 10,
            'reserved_count' => 0,
            'is_active' => true,
        ]);

        // Target schedule has slot with reserved_count = 2
        $targetSchedule = Schedule::create([
            'center_id' => $this->center->id,
            'date' => $targetDate,
            'is_active' => true,
        ]);

        Slot::create([
            'schedule_id' => $targetSchedule->id,
            'start_at' => '08:00',
            'end_at' => '09:00',
            'capacity' => 10,
            'reserved_count' => 2, // Booked!
            'is_active' => true,
        ]);

        $response = $this->actingAsSuperAdmin()->postJson(route('admin.schedules.copy'), [
            'center_id' => $this->center->id,
            'source_date' => $sourceDate,
            'target_dates' => [$targetDate],
        ]);

        $response->assertStatus(422);

        $formattedTargetDate = Carbon::parse($targetDate)->format('d/m/Y');
        $response->assertJsonFragment([
            'target_dates' => ["Không thể sao chép đè lịch ngày {$formattedTargetDate} vì đã có 2 lượt đặt tiêm!"]
        ]);
    }

    /**
     * Test copy schedule with multiple target dates where 1 target date has existing bookings (transaction rollback).
     */
    public function test_copy_schedule_multiple_targets_where_one_target_has_bookings_blocks_all(): void
    {
        $weekStart = now()->startOfWeek();
        $sourceDate = $weekStart->toDateString();
        $targetDateUnbooked = $weekStart->copy()->addDay()->toDateString();
        $targetDateBooked = $weekStart->copy()->addDays(2)->toDateString();

        $sourceSchedule = Schedule::create([
            'center_id' => $this->center->id,
            'date' => $sourceDate,
            'is_active' => true,
        ]);

        Slot::create([
            'schedule_id' => $sourceSchedule->id,
            'start_at' => '08:00',
            'end_at' => '09:00',
            'capacity' => 10,
            'reserved_count' => 0,
            'is_active' => true,
        ]);

        // Target Date 2 has existing bookings
        $targetBookedSchedule = Schedule::create([
            'center_id' => $this->center->id,
            'date' => $targetDateBooked,
            'is_active' => true,
        ]);

        Slot::create([
            'schedule_id' => $targetBookedSchedule->id,
            'start_at' => '08:00',
            'end_at' => '09:00',
            'capacity' => 10,
            'reserved_count' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAsSuperAdmin()->postJson(route('admin.schedules.copy'), [
            'center_id' => $this->center->id,
            'source_date' => $sourceDate,
            'target_dates' => [$targetDateUnbooked, $targetDateBooked],
        ]);

        $response->assertStatus(422);

        // Ensure targetDateUnbooked schedule was NOT created/copied due to validation failure
        $this->assertDatabaseMissing('schedules', [
            'center_id' => $this->center->id,
            'date' => $targetDateUnbooked,
        ]);
    }

    /**
     * Test copy schedule blocked when target slot has reserved_count=0 but has linked Registration records.
     */
    public function test_copy_schedule_blocked_when_target_has_linked_registration_records(): void
    {
        $weekStart = now()->startOfWeek();
        $sourceDate = $weekStart->toDateString();
        $targetDate = $weekStart->copy()->addDay()->toDateString();

        $sourceSchedule = Schedule::create([
            'center_id' => $this->center->id,
            'date' => $sourceDate,
            'is_active' => true,
        ]);

        Slot::create([
            'schedule_id' => $sourceSchedule->id,
            'start_at' => '08:00',
            'end_at' => '09:00',
            'capacity' => 10,
            'reserved_count' => 0,
            'is_active' => true,
        ]);

        $targetSchedule = Schedule::create([
            'center_id' => $this->center->id,
            'date' => $targetDate,
            'is_active' => true,
        ]);

        $targetSlot = Slot::create([
            'schedule_id' => $targetSchedule->id,
            'start_at' => '08:00',
            'end_at' => '09:00',
            'capacity' => 10,
            'reserved_count' => 0, // reserved_count is 0, but linked registration exists
            'is_active' => true,
        ]);

        Registration::create([
            'registration_code' => 'REG-TEST-' . Str::random(5),
            'customer_id' => null,
            'patient_name' => 'Nguyễn Văn Test',
            'patient_phone' => '0912345678',
            'center_id' => $this->center->id,
            'center_name' => $this->center->name,
            'injection_date' => $targetDate,
            'status' => 'pending',
            'booking_status' => 'confirmed',
            'payment_status' => 'unpaid',
            'payment_method' => 'cash',
            'total_price' => 500000,
            'slot_id' => $targetSlot->id,
        ]);

        $response = $this->actingAsSuperAdmin()->postJson(route('admin.schedules.copy'), [
            'center_id' => $this->center->id,
            'source_date' => $sourceDate,
            'target_dates' => [$targetDate],
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test cross-month and cross-year week navigation date queries.
     */
    public function test_cross_month_and_cross_year_week_navigation_queries(): void
    {
        // 1. Cross-year query (2026-12-31 is Thursday)
        $yearEndResponse = $this->actingAsSuperAdmin()->getJson(route('admin.schedules.index', [
            'date' => '2026-12-31',
            'center_id' => $this->center->id,
        ]));

        $yearEndResponse->assertStatus(200);
        $yearEndResponse->assertJson([
            'success' => true,
            'data' => [
                'week_start' => '2026-12-28', // Monday
                'week_end' => '2027-01-03',   // Sunday cross-year
            ]
        ]);
        $yearEndResponse->assertJsonCount(7, 'data.week_grid');

        // 2. Cross-month query (2026-08-31 is Monday)
        $monthEndResponse = $this->actingAsSuperAdmin()->getJson(route('admin.schedules.index', [
            'date' => '2026-08-31',
            'center_id' => $this->center->id,
        ]));

        $monthEndResponse->assertStatus(200);
        $monthEndResponse->assertJson([
            'success' => true,
            'data' => [
                'week_start' => '2026-08-31', // Monday
                'week_end' => '2026-09-06',   // Sunday cross-month
            ]
        ]);
        $monthEndResponse->assertJsonCount(7, 'data.week_grid');
    }

    /**
     * Test Branch Admin scope checks (403 on cross-branch access across all schedule endpoints).
     */
    public function test_branch_admin_scope_checks_returns_403_on_cross_branch_access(): void
    {
        $otherCenter = Center::create([
            'name' => 'Chi nhánh Khác ' . Str::random(4),
            'slug' => 'cn-khac-' . Str::random(5),
            'address' => '456 Đường Khác',
            'phone' => '0911222333',
            'is_active' => true,
        ]);

        $monday = now()->startOfWeek()->toDateString();

        // 1. Attempt GET index for other center
        $getIndexResp = $this->actingAsBranchAdmin()->getJson(route('admin.schedules.index', [
            'center_id' => $otherCenter->id,
        ]));
        $getIndexResp->assertStatus(403);

        // 2. Attempt POST copy schedule for other center
        $copyResp = $this->actingAsBranchAdmin()->postJson(route('admin.schedules.copy'), [
            'center_id' => $otherCenter->id,
            'source_date' => $monday,
            'target_dates' => [now()->startOfWeek()->addDay()->toDateString()],
        ]);
        $copyResp->assertStatus(403);

        // 3. Attempt POST toggle day status for other center
        $toggleResp = $this->actingAsBranchAdmin()->postJson(route('admin.schedules.toggle-day'), [
            'center_id' => $otherCenter->id,
            'date' => $monday,
            'is_active' => 0,
        ]);
        $toggleResp->assertStatus(403);

        // 4. Attempt DELETE day schedule for other center
        $destroyDayResp = $this->actingAsBranchAdmin()->deleteJson(route('admin.schedules.destroy-day'), [
            'center_id' => $otherCenter->id,
            'date' => $monday,
        ]);
        $destroyDayResp->assertStatus(403);
    }

    /**
     * Test destroy day schedule blocked with 422 when reserved_count > 0.
     */
    public function test_destroy_day_blocked_with_422_when_reserved_count_greater_than_zero(): void
    {
        $monday = now()->startOfWeek()->toDateString();
        $schedule = Schedule::create([
            'center_id' => $this->center->id,
            'date' => $monday,
            'is_active' => true,
        ]);

        Slot::create([
            'schedule_id' => $schedule->id,
            'start_at' => '08:00',
            'end_at' => '09:00',
            'capacity' => 10,
            'reserved_count' => 1,
            'is_active' => true,
        ]);

        $deleteResp = $this->actingAsSuperAdmin()->deleteJson(route('admin.schedules.destroy-day'), [
            'center_id' => $this->center->id,
            'date' => $monday,
        ]);

        $deleteResp->assertStatus(422);
        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
        ]);
    }
}
