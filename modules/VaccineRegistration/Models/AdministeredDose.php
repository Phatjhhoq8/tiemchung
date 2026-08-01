<?php

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class AdministeredDose extends Model
{
    use HasFactory;

    protected $table = 'administered_doses';

    protected $fillable = [
        'registration_id',
        'patient_id',
        'vaccine_id',
        'inventory_lot_id',
        'center_id',
        'administered_by',
        'administered_at',
        'screening_status',
        'screening_notes',
        'observation_notes',
        'observation_ended_at',
        'status',
    ];

    protected $casts = [
        'administered_at' => 'datetime',
        'observation_ended_at' => 'datetime',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class);
    }

    public function inventoryLot()
    {
        return $this->belongsTo(InventoryLot::class, 'inventory_lot_id');
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function administrator()
    {
        return $this->belongsTo(User::class, 'administered_by');
    }
}
