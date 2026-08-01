<?php

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_code',
        'patient_id',
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
        'screening_status',
        'screening_notes',
    ];

    /**
     * Get the centralized patient.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get administered doses for this registration.
     */
    public function administeredDoses()
    {
        return $this->hasMany(AdministeredDose::class);
    }

    /**
     * Get the vaccines registered for injection.
     */
    public function vaccines()
    {
        return $this->belongsToMany(Vaccine::class, 'registration_vaccines')
                    ->withPivot(['id', 'quantity', 'price', 'sale_price', 'inventory_lot_id'])
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

    /**
     * Step 1: Check-in patient for registration.
     */
    public function checkIn(): self
    {
        if (!$this->patient_id) {
            $patient = Patient::findOrCreateCentralized([
                'full_name' => $this->patient_name,
                'dob' => $this->patient_dob,
                'gender' => $this->patient_gender,
                'phone' => $this->patient_phone,
                'address' => $this->patient_address,
            ]);
            $this->patient_id = $patient->id;
        }

        $this->status = 'checked_in';
        $this->save();

        return $this;
    }

    /**
     * Step 2: Screening assessment.
     */
    public function screening(string $status, ?string $notes = null): self
    {
        if (!in_array($status, ['eligible', 'deferred', 'contraindicated'])) {
            throw new \InvalidArgumentException("Invalid screening status: {$status}");
        }

        $this->screening_status = $status;
        $this->screening_notes = $notes;
        $this->save();

        return $this;
    }

    /**
     * Step 3: Vaccine Administration execution.
     */
    public function administer(?int $vaccinatorId = null, ?int $vaccineId = null, ?int $inventoryLotId = null, int $observationMinutes = 30, ?string $observationNotes = null): AdministeredDose
    {
        if ($this->screening_status !== 'eligible') {
            throw new \RuntimeException("Cannot administer vaccine. Screening status is '{$this->screening_status}' (must be 'eligible').");
        }

        if (!$this->patient_id) {
            $this->checkIn();
        }

        if (!$vaccineId) {
            $firstVaccine = $this->vaccines()->first();
            $vaccineId = $firstVaccine ? $firstVaccine->id : null;
            if (!$inventoryLotId && $firstVaccine && isset($firstVaccine->pivot->inventory_lot_id)) {
                $inventoryLotId = $firstVaccine->pivot->inventory_lot_id;
            }
        }

        $dose = AdministeredDose::create([
            'registration_id' => $this->id,
            'patient_id' => $this->patient_id,
            'vaccine_id' => $vaccineId,
            'inventory_lot_id' => $inventoryLotId,
            'center_id' => $this->center_id,
            'administered_by' => $vaccinatorId ?? auth()->id(),
            'administered_at' => now(),
            'screening_status' => $this->screening_status,
            'screening_notes' => $this->screening_notes,
            'observation_notes' => $observationNotes,
            'observation_ended_at' => now()->addMinutes($observationMinutes),
            'status' => 'completed',
        ]);

        $this->status = 'completed';
        $this->save();

        return $dose;
    }
}

