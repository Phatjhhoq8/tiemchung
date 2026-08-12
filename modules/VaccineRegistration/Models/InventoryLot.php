<?php

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryLot extends Model
{
    use HasFactory;

    protected $table = 'inventory_lots';

    protected $fillable = [
        'vaccine_id',
        'center_id',
        'lot_number',
        'initial_quantity',
        'available_quantity',
        'reserved_quantity',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'initial_quantity' => 'integer',
        'available_quantity' => 'integer',
        'reserved_quantity' => 'integer',
    ];

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class);
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    protected static function booted()
    {
        $syncCenterVaccine = function (InventoryLot $lot) {
            $centerVaccine = CenterVaccine::where('center_id', $lot->center_id)
                ->where('vaccine_id', $lot->vaccine_id)
                ->first();

            if ($centerVaccine) {
                $totalAvailable = (int) static::where('center_id', $lot->center_id)
                    ->where('vaccine_id', $lot->vaccine_id)
                    ->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    })
                    ->sum('available_quantity');

                $status = $totalAvailable > 5 ? 'available' : ($totalAvailable > 0 ? 'limited' : 'out_of_stock');
                $centerVaccine->updateQuietly([
                    'stock_quantity' => $totalAvailable,
                    'stock_status' => $status,
                ]);
            }
        };

        static::saved($syncCenterVaccine);
        static::deleted($syncCenterVaccine);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'inventory_lot_id');
    }
}
