<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Registration;

class BranchStockService
{
    public static function statusFor(int $quantity): string
    {
        return match (true) {
            $quantity === 0 => 'out_of_stock',
            $quantity <= 5 => 'limited',
            default => 'available',
        };
    }

    /**
     * Lock and commit aggregate demand for one branch. Must run inside the booking transaction.
     */
    public function commit(int $centerId, array $demand): Collection
    {
        $demand = collect($demand)
            ->mapWithKeys(fn ($quantity, $vaccineId) => [(int) $vaccineId => (int) $quantity])
            ->filter(fn (int $quantity) => $quantity > 0)
            ->sortKeys();

        $rows = CenterVaccine::query()
            ->with('vaccine')
            ->where('center_id', $centerId)
            ->whereIn('vaccine_id', $demand->keys())
            ->orderBy('vaccine_id')
            ->lockForUpdate()
            ->get()
            ->keyBy('vaccine_id');

        foreach ($demand as $vaccineId => $quantity) {
            $row = $rows->get($vaccineId);
            if (!$row || !$row->is_active || !$row->vaccine?->is_active || $row->stock_quantity < $quantity) {
                throw ValidationException::withMessages([
                    'vaccine_ids' => 'Một hoặc nhiều vắc xin không đủ tồn kho tại chi nhánh này.',
                ]);
            }
        }

        foreach ($demand as $vaccineId => $quantity) {
            $row = $rows->get($vaccineId);
            $remaining = $row->stock_quantity - $quantity;
            $row->update([
                'stock_quantity' => $remaining,
                'stock_status' => self::statusFor($remaining),
            ]);
        }

        return $rows;
    }

    /** Restore only quantities committed by this booking implementation, once. */
    public function restore(Registration $registration): void
    {
        $items = DB::table('registration_vaccines')
            ->where('registration_id', $registration->id)
            ->where('stock_committed_quantity', '>', 0)
            ->orderBy('vaccine_id')
            ->lockForUpdate()
            ->get();

        if ($items->isEmpty()) {
            return;
        }

        $rows = CenterVaccine::query()
            ->where('center_id', $registration->center_id)
            ->whereIn('vaccine_id', $items->pluck('vaccine_id'))
            ->orderBy('vaccine_id')
            ->lockForUpdate()
            ->get()
            ->keyBy('vaccine_id');

        foreach ($items as $item) {
            $row = $rows->get($item->vaccine_id);
            if ($row) {
                $quantity = $row->stock_quantity + (int) $item->stock_committed_quantity;
                $row->update([
                    'stock_quantity' => $quantity,
                    'stock_status' => self::statusFor($quantity),
                ]);
            }

            DB::table('registration_vaccines')->where('id', $item->id)->update([
                'stock_committed_quantity' => 0,
                'updated_at' => now(),
            ]);
        }
    }
}
