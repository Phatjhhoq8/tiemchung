<?php

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaccineRegimen extends Model
{
    protected $fillable = [
        'vaccine_id',
        'age_group',
        'doses',
        'price',
        'sale_price',
        'schedule_description',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'integer',
        'sale_price' => 'integer',
        'doses' => 'integer',
        'sort_order' => 'integer',
    ];

    public function vaccine(): BelongsTo
    {
        return $this->belongsTo(Vaccine::class);
    }
}
