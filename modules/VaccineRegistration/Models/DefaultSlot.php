<?php

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DefaultSlot extends Model
{
    use HasFactory;

    protected $table = 'default_slots';

    protected $fillable = [
        'center_id',
        'day_of_week',
        'start_at',
        'end_at',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
}
