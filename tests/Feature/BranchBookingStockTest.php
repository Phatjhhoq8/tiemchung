<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\RegistrationPaymentService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Customer;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Schedule;
use Modules\VaccineRegistration\Models\Slot;
use Modules\VaccineRegistration\Models\Vaccine;
use Tests\TestCase;

class BranchBookingStockTest extends TestCase
{
    use DatabaseTransactions;

    private Center $centerA;
    private Center $centerB;
    private Vaccine $vaccine;
    private Slot $slotA;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $suffix = Str::lower(Str::random(8));
        $this->centerA = $this->center('Stock A ' . $suffix, 'stock-a-' . $suffix);
        $this->centerB = $this->center('Stock B ' . $suffix, 'stock-b-' . $suffix);
        $this->vaccine = Vaccine::create([
            'name' => 'Stock vaccine ' . $suffix,
            'price' => 100000,
            'doses' => 1,
            'stock_status' => 'available',
            'disease_prevention' => 'Cúm',
            'age_group' => 'Mọi độ tuổi',
            'origin' => 'Việt Nam',
            'is_active' => true,
        ]);
        foreach ([$this->centerA, $this->centerB] as $center) {
            CenterVaccine::create([
                'center_id' => $center->id,
                'vaccine_id' => $this->vaccine->id,
                'price' => 100000,
                'stock_quantity' => 10,
                'stock_status' => 'out_of_stock',
                'is_active' => true,
            ]);
        }
        $this->slotA = $this->slot($this->centerA);
        $this->admin = User::create([
            'name' => 'Stock admin ' . $suffix,
            'username' => 'stock_' . $suffix,
            'email' => 'stock_' . $suffix . '@example.test',
            'password' => bcrypt('Password123!'),
            'role' => 'branch_admin',
            'center_id' => $this->centerA->id,
            'is_active' => true,
            'status' => 'active',
        ]);
    }

    public function test_public_booking_decrements_aggregate_demand_and_only_current_branch(): void
    {
        $this->publicBook([
            $this->patient('Người 1', '0911111111'),
            $this->patient('Người 2', '0922222222'),
            $this->patient('Người 3', '0933333333'),
        ], 'aggregate')->assertRedirect(route('register.success'));

        $this->assertSame(7, $this->stock($this->centerA)->stock_quantity);
        $this->assertSame('available', $this->stock($this->centerA)->stock_status);
        $this->assertSame(10, $this->stock($this->centerB)->stock_quantity);
        $this->assertSame(3, Registration::where('center_id', $this->centerA->id)->count());
        $this->assertSame(3, (int) Registration::where('center_id', $this->centerA->id)->get()->sum(
            fn (Registration $registration) => $registration->vaccines()->sum('stock_committed_quantity')
        ));
    }

    public function test_insufficient_or_zero_stock_rolls_back_registration_slot_and_stock(): void
    {
        $stock = $this->stock($this->centerA);
        $stock->update(['stock_quantity' => 1, 'stock_status' => 'available']);

        $this->publicBook([
            $this->patient('Người 1', '0911111111'),
            $this->patient('Người 2', '0922222222'),
        ], 'insufficient')->assertSessionHasErrors('vaccine_ids');

        $this->assertSame(1, $stock->fresh()->stock_quantity);
        $this->assertSame('limited', $stock->fresh()->stock_status);
        $this->assertSame(0, $this->slotA->fresh()->reserved_count);
        $this->assertSame(0, Registration::where('center_id', $this->centerA->id)->count());

        $stock->update(['stock_quantity' => 0, 'stock_status' => 'available']);
        $this->publicBook([$this->patient('Người 3', '0933333333')], 'zero')->assertSessionHasErrors('vaccine_ids');
        $this->assertSame('out_of_stock', $stock->fresh()->stock_status);
    }

    public function test_counter_quantity_is_committed_and_repeated_unpaid_cancellation_restores_once(): void
    {
        $response = $this->asAdmin()->post(route('admin.registrations.store'), [
            'center_id' => $this->centerA->id,
            'account_name' => 'Chủ tài khoản',
            'account_phone' => '0912345678',
            'patient_name' => 'Người tiêm',
            'patient_phone' => '0987654321',
            'slot_id' => $this->slotA->id,
            'vaccine_ids' => [$this->vaccine->id],
            'quantities' => [$this->vaccine->id => 4],
            'booking_status' => 'confirmed',
            'idempotency_key' => 'counter-' . Str::uuid(),
        ]);
        $registration = Registration::latest('id')->firstOrFail();
        $response->assertRedirect(route('admin.registrations.show', $registration));
        $this->assertSame(6, $this->stock($this->centerA)->stock_quantity);
        $this->assertSame(4, (int) $registration->vaccines()->first()->pivot->stock_committed_quantity);

        $service = app(RegistrationPaymentService::class);
        $service->cancelUnpaid($registration->id, $this->admin);
        $service->cancelUnpaid($registration->id, $this->admin);
        $this->assertSame(10, $this->stock($this->centerA)->stock_quantity);
        $this->assertSame(0, (int) $registration->vaccines()->first()->pivot->stock_committed_quantity);
    }

    public function test_refund_and_no_show_restore_exactly_once_while_legacy_rows_never_inflate(): void
    {
        $this->publicBook([$this->patient('Hoàn tiền', '0911111111')], 'refund');
        $refund = Registration::latest('id')->firstOrFail();
        $payments = app(RegistrationPaymentService::class);
        $payments->settle($refund->id, 0, $this->admin);
        $payments->refund($refund->id, $this->admin);
        $payments->refund($refund->id, $this->admin);
        $this->assertSame(10, $this->stock($this->centerA)->stock_quantity);

        $this->publicBook([$this->patient('Không đến', '0922222222')], 'no-show');
        $noShow = Registration::latest('id')->firstOrFail();
        $payments->markNoShow($noShow->id, $this->admin);
        $payments->markNoShow($noShow->id, $this->admin);
        $this->assertSame(10, $this->stock($this->centerA)->stock_quantity);

        $legacy = $this->legacyRegistration();
        $payments->cancelUnpaid($legacy->id, $this->admin);
        $this->assertSame(10, $this->stock($this->centerA)->stock_quantity);
    }

    private function publicBook(array $patients, string $key)
    {
        return $this->withSession(['selected_center_id' => $this->centerA->id])->post(route('register.post'), [
            'patients' => $patients,
            'account_name' => 'Chủ tài khoản',
            'account_phone' => '0901234567',
            'slot_id' => $this->slotA->id,
            'idempotency_key' => 'stock-' . $key . '-' . Str::random(8),
        ]);
    }

    private function patient(string $name, string $phone): array
    {
        return ['name' => $name, 'phone' => $phone, 'dob' => '2000-01-01', 'gender' => 'Khác', 'address' => 'Cần Thơ', 'vaccine_ids' => [$this->vaccine->id]];
    }

    private function legacyRegistration(): Registration
    {
        $customer = Customer::findOrCreateByPhone('+84901234568', 'Legacy');
        $registration = Registration::create([
            'registration_code' => 'LEGACY-' . Str::random(8),
            'customer_id' => $customer->id,
            'patient_name' => 'Legacy',
            'patient_phone' => '+84901234568',
            'center_id' => $this->centerA->id,
            'center_name' => $this->centerA->name,
            'injection_date' => today()->addDays(3),
            'status' => 'pending',
            'booking_status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 100000,
        ]);
        $registration->vaccines()->attach($this->vaccine->id, ['price' => 100000, 'quantity' => 1]);

        return $registration;
    }

    private function center(string $name, string $slug): Center
    {
        return Center::create(['name' => $name, 'slug' => $slug, 'address' => 'Cần Thơ', 'phone' => '0912345678', 'is_active' => true]);
    }

    private function slot(Center $center): Slot
    {
        $schedule = Schedule::create(['center_id' => $center->id, 'date' => today()->addDays(3), 'is_active' => true]);
        return Slot::create(['schedule_id' => $schedule->id, 'start_at' => '08:00', 'end_at' => '09:00', 'capacity' => 20, 'reserved_count' => 0, 'is_active' => true]);
    }

    private function stock(Center $center): CenterVaccine
    {
        return CenterVaccine::where('center_id', $center->id)->where('vaccine_id', $this->vaccine->id)->firstOrFail();
    }

    private function asAdmin()
    {
        return $this->actingAs($this->admin)->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_role' => $this->admin->role,
            'admin_center_id' => $this->admin->center_id,
        ]);
    }
}
