<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\ConsultationLead;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Schedule;
use Modules\VaccineRegistration\Models\Slot;
use Modules\VaccineRegistration\Models\Vaccine;
use Tests\TestCase;

class CrmLeadsAndRegistrationIdempotencyTest extends TestCase
{
    use DatabaseTransactions;

    private Center $center;
    private Vaccine $vaccine;
    private Slot $slot;

    protected function setUp(): void
    {
        parent::setUp();
        $suffix = Str::lower(Str::random(8));
        $this->center = Center::create([
            'name' => 'CRM center ' . $suffix,
            'slug' => 'crm-center-' . $suffix,
            'address' => 'Địa chỉ CRM',
            'phone' => '0933333333',
            'is_active' => true,
        ]);
        $this->vaccine = Vaccine::create([
            'name' => 'CRM vaccine ' . $suffix,
            'price' => 150000,
            'doses' => 1,
            'stock_status' => 'available',
            'disease_prevention' => 'Cúm',
            'age_group' => 'Mọi độ tuổi',
            'origin' => 'Việt Nam',
            'is_active' => true,
        ]);
        CenterVaccine::create([
            'center_id' => $this->center->id,
            'vaccine_id' => $this->vaccine->id,
            'price' => 150000,
            'stock_status' => 'available',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);
        $schedule = Schedule::create([
            'center_id' => $this->center->id,
            'date' => today()->addDays(2)->toDateString(),
            'is_active' => true,
        ]);
        $this->slot = Slot::create([
            'schedule_id' => $schedule->id,
            'start_at' => '08:00',
            'end_at' => '09:00',
            'capacity' => 5,
            'reserved_count' => 0,
            'is_active' => true,
        ]);
    }

    public function test_public_lead_is_independent_from_booking_and_status_cannot_be_forged(): void
    {
        $before = Registration::count();
        $this->postJson(route('consultations.store'), [
            'name' => 'Người cần tư vấn',
            'phone' => '0912345678',
            'source' => 'Website',
            'status' => 'closed',
            'center_id' => $this->center->id,
        ])->assertCreated();

        $lead = ConsultationLead::latest('id')->firstOrFail();
        $this->assertSame('new', $lead->status);
        $this->assertSame($before, Registration::count());
    }

    public function test_same_idempotency_key_creates_one_booking_and_one_slot_reservation(): void
    {
        $payload = [
            'patient_name' => 'Khách chống trùng',
            'patient_phone' => '0912345678',
            'slot_id' => $this->slot->id,
            'vaccine_ids' => [$this->vaccine->id],
            'idempotency_key' => 'same-key-' . Str::random(16),
        ];

        $this->withSession(['selected_center_id' => $this->center->id])->post(route('register.post'), $payload)->assertRedirect();
        $this->withSession(['selected_center_id' => $this->center->id])->post(route('register.post'), $payload)->assertRedirect();

        $this->assertSame(1, Registration::where('idempotency_key', $payload['idempotency_key'])->count());
        $this->assertSame(1, $this->slot->fresh()->reserved_count);
        $this->assertSame(9, CenterVaccine::where('center_id', $this->center->id)->where('vaccine_id', $this->vaccine->id)->value('stock_quantity'));
    }
}
