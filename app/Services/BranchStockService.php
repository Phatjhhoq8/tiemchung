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
            $vaccine = \Modules\VaccineRegistration\Models\Vaccine::where('id', $vaccineId)->where('is_active', true)->first();
            if (!$vaccine) {
                throw ValidationException::withMessages([
                    'vaccine_ids' => 'Vắc xin không tồn tại hoặc đã tạm dừng hoạt động.',
                ]);
            }
        }

        // Do not decrement stock during booking commit anymore.
        // Tồn kho chỉ được trừ khi hoàn tất tiêm chủng thực tế.

        return $rows;
    }

    /** Restore only quantities committed by this booking implementation, once. */
    public function restore(Registration $registration): void
    {
        // Do not restore stock since we did not decrement on booking.
        // Clear the committed quantity trace.
        DB::table('registration_vaccines')
            ->where('registration_id', $registration->id)
            ->update([
                'stock_committed_quantity' => 0,
                'updated_at' => now(),
            ]);
    }
}
