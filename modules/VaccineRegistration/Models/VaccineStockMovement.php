<?php

namespace Modules\VaccineRegistration\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class VaccineStockMovement extends Model
{
    protected $fillable = [
        'center_id',
        'vaccine_id',
        'type',
        'quantity',
        'unit_price',
        'note',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
