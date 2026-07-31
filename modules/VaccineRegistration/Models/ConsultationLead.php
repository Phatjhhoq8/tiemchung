<?php

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConsultationLead extends Model
{
    use HasFactory;

    protected $table = 'consultation_leads';

    protected $fillable = [
        'name',
        'phone',
        'source',
        'status',
        'note',
        'center_id',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
}
