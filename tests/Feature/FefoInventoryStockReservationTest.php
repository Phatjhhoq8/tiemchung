<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\InventoryLot;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\StockMovement;
use Modules\VaccineRegistration\Models\Vaccine;
use App\Services\FefoInventoryService;
use Illuminate\Support\Str;
use Tests\TestCase;

class FefoInventoryStockReservationTest extends TestCase
{
    use DatabaseTransactions;

    private function createContext(): array
    {
        $unique = Str::random(6);

        $center = Center::create([
            'name' => 'Trung tâm Test FEFO ' . $unique,
            'address' => '123 Đường FEFO, Q1',
            'phone' => '090' . rand(1000000, 9999999),
            'is_active' => true,
        ]);

        $vaccine = Vaccine::create([
            'name' => 'Vắc xin FEFO Test ' . $unique,
            'price' => 350000,
            'sale_price' => null,
            'type' => 'single',
            'doses' => 1,
            'stock_status' => 'available',
            'category' => 'Cúm',
            'disease_prevention' => 'Cúm mùa',
            'age_group' => 'Mọi lứa tuổi',
            'origin' => 'Pháp',
            'description' => 'Mô tả test FEFO',
            'is_active' => true,
        ]);

        CenterVaccine::create([
            'center_id' => $center->id,
            'vaccine_id' => $vaccine->id,
            'price' => 350000,
            'stock_quantity' => 100,
            'stock_status' => 'available',
            'is_active' => true,
        ]);

        return [$center, $vaccine];
    }

    /**
     * Test 1: FEFO allocation picks lot with earliest expiration date.
     */
    public function test_fefo_allocation_picks_lot_with_earliest_expiration_date(): void
    {
        [$center, $vaccine] = $this->createContext();

        // Lot A: Expires in 10 days
        $lotA = InventoryLot::create([
            'vaccine_id' => $vaccine->id,
            'center_id' => $center->id,
            'lot_number' => 'LOT-A-' . Str::random(5),
            'initial_quantity' => 10,
            'available_quantity' => 10,
            'reserved_quantity' => 0,
            'expires_at' => now()->addDays(10),
            'status' => 'active',
        ]);

        // Lot B: Expires in 3 days (Earliest expiration date!)
        $lotB = InventoryLot::create([
            'vaccine_id' => $vaccine->id,
            'center_id' => $center->id,
            'lot_number' => 'LOT-B-' . Str::random(5),
            'initial_quantity' => 10,
            'available_quantity' => 10,
            'reserved_quantity' => 0,
            'expires_at' => now()->addDays(3),
            'status' => 'active',
        ]);

        // Lot C: Expires in 30 days
        $lotC = InventoryLot::create([
            'vaccine_id' => $vaccine->id,
            'center_id' => $center->id,
            'lot_number' => 'LOT-C-' . Str::random(5),
            'initial_quantity' => 10,
            'available_quantity' => 10,
            'reserved_quantity' => 0,
            'expires_at' => now()->addDays(30),
            'status' => 'active',
        ]);

        $registration = Registration::create([
            'registration_code' => 'TEST-FEFO-' . Str::random(8),
            'patient_name' => 'Nguyen Van A',
            'patient_dob' => '1995-01-01',
            'patient_gender' => 'Nam',
            'patient_phone' => '0901111222',
            'patient_address' => 'HCM',
            'center_id' => $center->id,
            'center_name' => $center->name,
            'injection_date' => now()->addDay()->toDateString(),
            'status' => 'Chờ thanh toán',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 350000,
        ]);

        $registration->vaccines()->attach($vaccine->id, [
            'price' => 350000,
            'quantity' => 1,
        ]);

        $service = app(FefoInventoryService::class);
        $service->allocateAndReserve($registration);

        $registration->load('vaccines');
        $pivotLotId = $registration->vaccines->first()->pivot->inventory_lot_id;

        // Assert Lot B (earliest expiration) was chosen
        $this->assertEquals($lotB->id, $pivotLotId);

        $lotB->refresh();
        $this->assertEquals(9, $lotB->available_quantity);
        $this->assertEquals(1, $lotB->reserved_quantity);

        // Assert stock movement recorded
        $this->assertDatabaseHas('stock_movements', [
            'inventory_lot_id' => $lotB->id,
            'type' => 'reservation',
            'quantity' => 1,
            'reference_id' => $registration->id,
        ]);
    }

    /**
     * Test 2: Recalled, quarantined, and expired lots are rejected.
     */
    public function test_recalled_quarantined_expired_lots_are_rejected(): void
    {
        [$center, $vaccine] = $this->createContext();

        // Recalled lot
        InventoryLot::create([
            'vaccine_id' => $vaccine->id,
            'center_id' => $center->id,
            'lot_number' => 'LOT-RECALLED-' . Str::random(5),
            'initial_quantity' => 10,
            'available_quantity' => 10,
            'reserved_quantity' => 0,
            'expires_at' => now()->addDays(20),
            'status' => 'recalled',
        ]);

        // Quarantined lot
        InventoryLot::create([
            'vaccine_id' => $vaccine->id,
            'center_id' => $center->id,
            'lot_number' => 'LOT-QUARANTINED-' . Str::random(5),
            'initial_quantity' => 10,
            'available_quantity' => 10,
            'reserved_quantity' => 0,
            'expires_at' => now()->addDays(20),
            'status' => 'quarantined',
        ]);

        // Expired lot
        InventoryLot::create([
            'vaccine_id' => $vaccine->id,
            'center_id' => $center->id,
            'lot_number' => 'LOT-EXPIRED-' . Str::random(5),
            'initial_quantity' => 10,
            'available_quantity' => 10,
            'reserved_quantity' => 0,
            'expires_at' => now()->subDay(),
            'status' => 'active',
        ]);

        $registration = Registration::create([
            'registration_code' => 'TEST-FEFO-' . Str::random(8),
            'patient_name' => 'Tran Thi B',
            'patient_dob' => '1998-05-05',
            'patient_gender' => 'Nữ',
            'patient_phone' => '0903333444',
            'patient_address' => 'HCM',
            'center_id' => $center->id,
            'center_name' => $center->name,
            'injection_date' => now()->addDay()->toDateString(),
            'status' => 'Chờ thanh toán',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 350000,
        ]);

        $registration->vaccines()->attach($vaccine->id, [
            'price' => 350000,
            'quantity' => 1,
        ]);

        $service = app(FefoInventoryService::class);

        // Expect exception because all available lots are rejected
        $this->expectException(\RuntimeException::class);
        $service->allocateAndReserve($registration);
    }

    /**
     * Test 3: Pending order reserves stock; cancellation releases stock back to available quantity.
     */
    public function test_pending_order_reserves_stock_and_cancellation_releases_stock(): void
    {
        [$center, $vaccine] = $this->createContext();

        $lot = InventoryLot::create([
            'vaccine_id' => $vaccine->id,
            'center_id' => $center->id,
            'lot_number' => 'LOT-RESERVE-RELEASE-' . Str::random(5),
            'initial_quantity' => 5,
            'available_quantity' => 5,
            'reserved_quantity' => 0,
            'expires_at' => now()->addDays(15),
            'status' => 'active',
        ]);

        $postData = [
            'center_id' => $center->id,
            'injection_date' => now()->addDay()->toDateString(),
            'payment_method' => 'Tại trung tâm',
            'patients' => [
                [
                    'name' => 'Le Van C',
                    'dob' => '2000-01-01',
                    'gender' => 'Nam',
                    'phone' => '0912345678',
                    'address' => 'HN',
                    'vaccine_ids' => [$vaccine->id],
                    'quantity' => 1,
                ]
            ]
        ];

        $response = $this->postJson('/register', $postData);
        $response->assertStatus(200);

        // Verify stock reserved
        $lot->refresh();
        $this->assertEquals(4, $lot->available_quantity);
        $this->assertEquals(1, $lot->reserved_quantity);

        $registration = Registration::where('patient_name', 'Le Van C')->latest()->first();

        // Cancel order
        $service = app(FefoInventoryService::class);
        $service->releaseStock($registration);

        // Verify stock released back
        $lot->refresh();
        $this->assertEquals(5, $lot->available_quantity);
        $this->assertEquals(0, $lot->reserved_quantity);

        // Assert release stock movement created
        $this->assertDatabaseHas('stock_movements', [
            'inventory_lot_id' => $lot->id,
            'type' => 'release',
            'quantity' => 1,
            'reference_id' => $registration->id,
        ]);
    }

    /**
     * Test 4: Paid order commits deduction from reserved quantity.
     */
    public function test_paid_order_commits_deduction(): void
    {
        [$center, $vaccine] = $this->createContext();

        $lot = InventoryLot::create([
            'vaccine_id' => $vaccine->id,
            'center_id' => $center->id,
            'lot_number' => 'LOT-PAID-DEDUCT-' . Str::random(5),
            'initial_quantity' => 10,
            'available_quantity' => 10,
            'reserved_quantity' => 0,
            'expires_at' => now()->addDays(20),
            'status' => 'active',
        ]);

        $registration = Registration::create([
            'registration_code' => 'TEST-FEFO-' . Str::random(8),
            'patient_name' => 'Pham Van D',
            'patient_dob' => '1992-02-02',
            'patient_gender' => 'Nam',
            'patient_phone' => '0905555666',
            'patient_address' => 'Da Nang',
            'center_id' => $center->id,
            'center_name' => $center->name,
            'injection_date' => now()->addDay()->toDateString(),
            'status' => 'Chờ thanh toán',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 350000,
        ]);

        $registration->vaccines()->attach($vaccine->id, [
            'price' => 350000,
            'quantity' => 1,
        ]);

        $service = app(FefoInventoryService::class);
        $service->allocateAndReserve($registration);

        $lot->refresh();
        $this->assertEquals(9, $lot->available_quantity);
        $this->assertEquals(1, $lot->reserved_quantity);

        // Commit deduction on payment
        $service->commitDeduction($registration);

        $lot->refresh();
        $this->assertEquals(9, $lot->available_quantity);
        $this->assertEquals(0, $lot->reserved_quantity);

        // Assert deduction movement logged
        $this->assertDatabaseHas('stock_movements', [
            'inventory_lot_id' => $lot->id,
            'type' => 'deduction',
            'quantity' => 1,
            'reference_id' => $registration->id,
        ]);
    }
}
