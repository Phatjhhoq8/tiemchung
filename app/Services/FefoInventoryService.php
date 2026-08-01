<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Modules\VaccineRegistration\Models\InventoryLot;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\StockMovement;

class FefoInventoryService
{
    /**
     * Allocate and reserve stock for a registration based on FEFO (First Expired, First Out).
     */
    public function allocateAndReserve(Registration $registration): Registration
    {
        return DB::transaction(function () use ($registration) {
            $registration->load('vaccines');

            foreach ($registration->vaccines as $vaccine) {
                $qty = (int) ($vaccine->pivot->quantity ?? 1);
                if ($qty <= 0) {
                    $qty = 1;
                }

                $hasLots = InventoryLot::where('center_id', $registration->center_id)
                    ->where('vaccine_id', $vaccine->id)
                    ->exists();

                if (!$hasLots) {
                    continue;
                }

                // FEFO: Pick active non-expired lot with earliest expiration date
                $lot = InventoryLot::where('center_id', $registration->center_id)
                    ->where('vaccine_id', $vaccine->id)
                    ->where('status', 'active')
                    ->where('expires_at', '>', now())
                    ->where('available_quantity', '>=', $qty)
                    ->orderBy('expires_at', 'asc')
                    ->lockForUpdate()
                    ->first();

                if (!$lot) {
                    throw new \RuntimeException("Không đủ tồn kho vắc xin khả dụng (FEFO) cho vắc xin {$vaccine->name}");
                }

                // Reserve stock
                $lot->reserved_quantity += $qty;
                $lot->available_quantity -= $qty;
                $lot->save();

                // Update pivot table with inventory_lot_id
                if (!empty($vaccine->pivot->id)) {
                    DB::table('registration_vaccines')
                        ->where('id', $vaccine->pivot->id)
                        ->update(['inventory_lot_id' => $lot->id]);
                } else {
                    $registration->vaccines()->updateExistingPivot($vaccine->id, ['inventory_lot_id' => $lot->id]);
                }

                // Log reservation stock movement
                StockMovement::create([
                    'inventory_lot_id' => $lot->id,
                    'user_id' => auth()->check() ? auth()->id() : null,
                    'type' => 'reservation',
                    'quantity' => $qty,
                    'reference_type' => Registration::class,
                    'reference_id' => $registration->id,
                    'note' => "Dự trữ vắc xin theo FEFO cho đơn đăng ký #{$registration->registration_code}",
                ]);
            }

            $registration->unsetRelation('vaccines');
            return $registration;
        });
    }

    /**
     * Release reserved stock on order cancellation.
     */
    public function releaseStock(Registration $registration): void
    {
        DB::transaction(function () use ($registration) {
            $alreadyReleased = StockMovement::where('reference_type', Registration::class)
                ->where('reference_id', $registration->id)
                ->where('type', 'release')
                ->exists();

            if ($alreadyReleased) {
                return;
            }

            $registration->load('vaccines');

            foreach ($registration->vaccines as $vaccine) {
                $lotId = $vaccine->pivot->inventory_lot_id;
                $qty = (int) ($vaccine->pivot->quantity ?? 1);
                if ($qty <= 0) {
                    $qty = 1;
                }

                if (!$lotId) {
                    continue;
                }

                $lot = InventoryLot::where('id', $lotId)->lockForUpdate()->first();
                if ($lot) {
                    $lot->available_quantity += $qty;
                    $lot->reserved_quantity = max(0, $lot->reserved_quantity - $qty);
                    $lot->save();

                    StockMovement::create([
                        'inventory_lot_id' => $lot->id,
                        'user_id' => auth()->check() ? auth()->id() : null,
                        'type' => 'release',
                        'quantity' => $qty,
                        'reference_type' => Registration::class,
                        'reference_id' => $registration->id,
                        'note' => "Giải phóng tồn kho do hủy đơn đăng ký #{$registration->registration_code}",
                    ]);
                }
            }
        });
    }

    /**
     * Commit reserved quantity to permanent deduction on paid order.
     */
    public function commitDeduction(Registration $registration): void
    {
        DB::transaction(function () use ($registration) {
            $alreadyDeducted = StockMovement::where('reference_type', Registration::class)
                ->where('reference_id', $registration->id)
                ->where('type', 'deduction')
                ->exists();

            if ($alreadyDeducted) {
                return;
            }

            $registration->load('vaccines');

            foreach ($registration->vaccines as $vaccine) {
                $lotId = $vaccine->pivot->inventory_lot_id;
                $qty = (int) ($vaccine->pivot->quantity ?? 1);
                if ($qty <= 0) {
                    $qty = 1;
                }

                if (!$lotId) {
                    continue;
                }

                $lot = InventoryLot::where('id', $lotId)->lockForUpdate()->first();
                if ($lot) {
                    $lot->reserved_quantity = max(0, $lot->reserved_quantity - $qty);
                    $lot->save();

                    StockMovement::create([
                        'inventory_lot_id' => $lot->id,
                        'user_id' => auth()->check() ? auth()->id() : null,
                        'type' => 'deduction',
                        'quantity' => $qty,
                        'reference_type' => Registration::class,
                        'reference_id' => $registration->id,
                        'note' => "Khấu trừ tồn kho cho đơn đã thanh toán #{$registration->registration_code}",
                    ]);
                }
            }
        });
    }
}
