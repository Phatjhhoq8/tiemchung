<?php

namespace Modules\VaccineRegistration\Models;

use App\Services\BranchStockService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CenterVaccine extends Model
{
    protected static function booted(): void
    {
        static::saving(function (CenterVaccine $centerVaccine) {
            $centerVaccine->stock_status = BranchStockService::statusFor(max(0, (int) $centerVaccine->stock_quantity));
        });
    }
    protected $fillable = [
        'center_id',
        'vaccine_id',
        'price',
        'sale_price',
        'stock_quantity',
        'stock_status',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'integer',
        'sale_price' => 'integer',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    public function vaccine(): BelongsTo
    {
        return $this->belongsTo(Vaccine::class);
    }

    public function hasSalePrice(): bool
    {
        return !empty($this->sale_price) && $this->sale_price < $this->price;
    }
}
