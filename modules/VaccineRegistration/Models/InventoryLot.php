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

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'inventory_lot_id');
    }
}
