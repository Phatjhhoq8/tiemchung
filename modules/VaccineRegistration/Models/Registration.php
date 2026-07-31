<?php

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_code',
        'patient_name',
        'patient_dob',
        'patient_gender',
        'patient_phone',
        'patient_address',
        'guardian_name',
        'guardian_phone',
        'center_id',
        'center_name',
        'injection_date',
        'status',
        'payment_method',
        'total_price',
        'slot_id',
    ];

    /**
     * Get the vaccines registered for injection.
     */
    public function vaccines()
    {
        return $this->belongsToMany(Vaccine::class, 'registration_vaccines')
                    ->withPivot(['quantity', 'price', 'sale_price'])
                    ->withTimestamps();
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }
}
