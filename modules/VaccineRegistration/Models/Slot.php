<?php

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Slot extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'start_at',
        'end_at',
        'capacity',
        'reserved_count',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'reserved_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}
