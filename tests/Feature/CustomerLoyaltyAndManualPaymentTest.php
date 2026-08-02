<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Customer;
use Modules\VaccineRegistration\Models\PointTransaction;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Schedule;
use Modules\VaccineRegistration\Models\Slot;
use Modules\VaccineRegistration\Models\Vaccine;
use Tests\TestCase;

class CustomerLoyaltyAndManualPaymentTest extends TestCase
{
    use DatabaseTransactions;

    private Center $centerA;
    private Center $centerB;
    private Vaccine $vaccine;
    private User $branchAdminA;
    private User $branchAdminB;
    private string $householdPhone;
    private string $keyPrefix;

    protected function setUp(): void
    {
        parent::setUp();

        $suffix = Str::lower(Str::random(8));
        $this->householdPhone = '09' . str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        $this->keyPrefix = 'loyalty-' . $suffix . '-';
        $this->centerA = Center::create([
            'name' => 'Loyalty A ' . $suffix,
            'slug' => 'loyalty-a-' . $suffix,
            'address' => 'Địa chỉ A',
            'phone' => '0911111111',
            'is_active' => true,
        ]);
        $this->centerB = Center::create([
            'name' => 'Loyalty B ' . $suffix,
            'slug' => 'loyalty-b-' . $suffix,
            'address' => 'Địa chỉ B',
            'phone' => '0922222222',
            'is_active' => true,
        ]);
        $this->vaccine = Vaccine::create([
            'name' => 'Vắc xin loyalty ' . $suffix,
            'price' => 100000,
            'type' => 'single',
            'doses' => 1,
            'stock_status' => 'available',
            'disease_prevention' => 'Cúm',
            'age_group' => 'Mọi độ tuổi',
            'origin' => 'Việt Nam',
            'is_active' => true,
        ]);
        CenterVaccine::create([
            'center_id' => $this->centerA->id,
            'vaccine_id' => $this->vaccine->id,
            'price' => 100000,
            'stock_status' => 'available',
            'stock_quantity' => 20,
            'is_active' => true,
        ]);
        CenterVaccine::create([
            'center_id' => $this->centerB->id,
            'vaccine_id' => $this->vaccine->id,
            'price' => 120000,
            'stock_status' => 'available',
            'stock_quantity' => 20,
            'is_active' => true,
        ]);
        $this->branchAdminA = $this->adminFor($this->centerA, $suffix . 'a');
        $this->branchAdminB = $this->adminFor($this->centerB, $suffix . 'b');
    }

    public function test_household_phone_is_normalized_and_reused_for_purchase_history(): void
    {
        $first = $this->book($this->centerA, 'Nguyễn Văn A', $this->householdPhone, $this->keyPrefix . 'household-one');
        $second = $this->book($this->centerA, 'Nguyễn Văn B', '+84' . substr($this->householdPhone, 1), $this->keyPrefix . 'household-two');

        $customer = Customer::where('phone', '+84' . substr($this->householdPhone, 1))->firstOrFail();
        $this->assertSame('Nguyễn Văn A', $customer->name);
        $this->assertSame(2, $customer->registrations()->count());
        $this->assertSame($customer->id, $first->customer_id);
        $this->assertSame($customer->id, $second->customer_id);
    }

    public function test_manual_settlement_redeems_up_to_balance_and_earns_points_once(): void
    {
        $first = $this->book($this->centerA, 'Nguyễn Văn A', $this->householdPhone, $this->keyPrefix . 'settle-one');
        $this->asAdmin($this->branchAdminA)
            ->post(route('admin.registrations.settle', $first), ['redeem_points' => 0])
            ->assertSessionHas('success');

        $customer = $first->customer()->firstOrFail();
        $this->assertSame(10, $customer->pointBalance());
        $this->assertDatabaseHas('point_transactions', [
            'registration_id' => $first->id,
            'type' => PointTransaction::EARN,
            'points' => 10,
        ]);

        $second = $this->book($this->centerA, 'Nguyễn Văn A', '+84' . substr($this->householdPhone, 1), $this->keyPrefix . 'settle-two');
        $this->asAdmin($this->branchAdminA)
            ->post(route('admin.registrations.settle', $second), ['redeem_points' => 10])
            ->assertSessionHas('success');

        $second->refresh();
        $this->assertSame(1000, $second->points_discount_amount);
        $this->assertSame(99000, $second->netPaidAmount());
        $this->assertSame(9, PointTransaction::where('registration_id', $second->id)->where('type', PointTransaction::EARN)->value('points'));
        $this->assertSame(-10, PointTransaction::where('registration_id', $second->id)->where('type', PointTransaction::REDEEM)->value('points'));

        $this->asAdmin($this->branchAdminA)
            ->post(route('admin.registrations.settle', $second), ['redeem_points' => 10])
            ->assertSessionHas('success');
        $this->assertSame(2, PointTransaction::where('registration_id', $second->id)->count());
    }

    public function test_full_refund_reverses_points_and_releases_slot_once(): void
    {
        $registration = $this->book($this->centerA, 'Nguyễn Văn A', $this->householdPhone, $this->keyPrefix . 'refund-one');
        $slot = $registration->slot()->firstOrFail();
        $this->asAdmin($this->branchAdminA)->post(route('admin.registrations.settle', $registration), ['redeem_points' => 0]);

        $this->asAdmin($this->branchAdminA)->post(route('admin.registrations.refund', $registration))->assertSessionHas('success');
        $registration->refresh();
        $customer = $registration->customer()->firstOrFail();

        $this->assertSame(Registration::PAYMENT_REFUNDED, $registration->payment_status);
        $this->assertSame(Registration::BOOKING_CANCELLED, $registration->booking_status);
        $this->assertSame(0, $customer->pointBalance());
        $this->assertSame(0, $slot->fresh()->reserved_count);
        $this->assertSame(2, PointTransaction::where('registration_id', $registration->id)->count());

        $this->asAdmin($this->branchAdminA)->post(route('admin.registrations.refund', $registration))->assertSessionHas('success');
        $this->assertSame(2, PointTransaction::where('registration_id', $registration->id)->count());
        $this->assertSame(0, $slot->fresh()->reserved_count);
    }

    public function test_branch_admin_cannot_settle_other_branch_registration_and_online_routes_are_missing(): void
    {
        $registration = $this->book($this->centerA, 'Nguyễn Văn A', $this->householdPhone, $this->keyPrefix . 'cross-branch');

        $this->asAdmin($this->branchAdminB)
            ->post(route('admin.registrations.settle', $registration), ['redeem_points' => 0])
            ->assertForbidden();
        $this->postJson('/api/webhooks/payment')->assertNotFound();
        $this->get('/payment/return')->assertNotFound();
    }

    private function book(Center $center, string $name, string $phone, string $key): Registration
    {
        $schedule = Schedule::firstOrCreate([
            'center_id' => $center->id,
            'date' => today()->addDays(5)->toDateString(),
        ], [
            'is_active' => true,
        ]);
        $slot = Slot::firstOrCreate([
            'schedule_id' => $schedule->id,
            'start_at' => '08:00',
            'end_at' => '09:00',
        ], [
            'capacity' => 20,
            'reserved_count' => 0,
            'is_active' => true,
        ]);

        $this->withSession(['selected_center_id' => $center->id])->post(route('register.post'), [
            'patient_name' => $name,
            'patient_phone' => $phone,
            'slot_id' => $slot->id,
            'vaccine_ids' => [$this->vaccine->id],
            'idempotency_key' => $key,
        ])->assertRedirect(route('register.success'));

        return Registration::where('idempotency_key', $key)->firstOrFail();
    }

    private function adminFor(Center $center, string $suffix): User
    {
        return User::create([
            'name' => 'Branch ' . $suffix,
            'username' => 'branch_' . $suffix,
            'email' => 'branch_' . $suffix . '@example.test',
            'password' => bcrypt('Password123!'),
            'role' => 'branch_admin',
            'center_id' => $center->id,
            'is_active' => true,
            'status' => 'active',
        ]);
    }

    private function asAdmin(User $admin)
    {
        return $this->actingAs($admin)->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $admin->id,
            'admin_role' => $admin->role,
            'admin_center_id' => $admin->center_id,
        ]);
    }
}
