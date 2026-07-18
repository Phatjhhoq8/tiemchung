<?php

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vaccine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'disease_prevention',
        'age_group',
        'origin',
        'image',
    ];

    /**
     * Get the registrations associated with the vaccine.
     */
    public function registrations()
    {
        return $this->belongsToMany(Registration::class, 'registration_vaccines')
                    ->withPivot('price')
                    ->withTimestamps();
    }
}
