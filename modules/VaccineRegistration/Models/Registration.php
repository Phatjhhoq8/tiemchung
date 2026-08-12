<?php

namespace Modules\VaccineRegistration\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\VaccineRegistration\Support\PhoneNormalizer;

class Registration extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (Registration $registration) {
            // 1. Tự động đồng bộ tài khoản tích điểm (Customer)
            if (!$registration->customer_id && $registration->patient_phone) {
                $phone = PhoneNormalizer::normalize($registration->patient_phone);
                if (!$phone) {
                    $phone = $registration->patient_phone;
                }
                
                $customer = Customer::findOrCreateByPhone(
                    $phone,
                    $registration->patient_name ?? 'Khách hàng'
                );
                $registration->customer_id = $customer->id;
            }

            // 2. Tự động đồng bộ hồ sơ bệnh nhân (Patient)
            if (!$registration->patient_id && $registration->patient_phone) {
                $patient = Patient::findOrCreateCentralized([
                    'full_name' => $registration->patient_name,
                    'dob' => $registration->patient_dob,
                    'gender' => $registration->patient_gender,
                    'phone' => $registration->patient_phone,
                    'address' => $registration->patient_address,
                ]);
                $registration->patient_id = $patient->id;
            }
        });
    }

    public const BOOKING_PENDING = 'pending';

    public const BOOKING_CONFIRMED = 'confirmed';

    public const BOOKING_COMPLETED = 'completed';

    public const BOOKING_CANCELLED = 'cancelled';

    public const BOOKING_NO_SHOW = 'no_show';

    public const PAYMENT_UNPAID = 'unpaid';

    public const PAYMENT_PAID = 'paid';

    public const PAYMENT_REFUNDED = 'refunded';

    protected $fillable = [
        'registration_code',
        'customer_id',
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
        'booking_status',
        'payment_status',
        'idempotency_key',
        'payment_method',
        'total_price',
        'points_discount_amount',
        'paid_at',
        'refunded_at',
        'settled_by',
        'slot_id',
        'screening_status',
        'screening_notes',
    ];

    protected $casts = [
        'patient_dob' => 'date',
        'injection_date' => 'date',
        'total_price' => 'integer',
        'points_discount_amount' => 'integer',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

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
    public function administeredDoses(): HasMany
    {
        return $this->hasMany(AdministeredDose::class);
    }

    /**
     * Get the vaccines registered for injection.
     */
    public function vaccines()
    {
        return $this->belongsToMany(Vaccine::class, 'registration_vaccines')
            ->withPivot(['id', 'quantity', 'stock_committed_quantity', 'price', 'sale_price', 'inventory_lot_id'])
            ->withTimestamps();
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    public function settledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'settled_by');
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function netPaidAmount(): int
    {
        return max(0, (int) $this->total_price - (int) $this->points_discount_amount);
    }

    public function bookingStatusLabel(): string
    {
        return match ($this->booking_status) {
            self::BOOKING_CONFIRMED => 'Đã xác nhận',
            self::BOOKING_COMPLETED => 'Đã hoàn tất',
            self::BOOKING_CANCELLED => 'Đã hủy',
            self::BOOKING_NO_SHOW => 'Không đến',
            default => 'Chờ xác nhận',
        };
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_PAID => 'Đã thanh toán',
            self::PAYMENT_REFUNDED => 'Đã hoàn tiền',
            default => 'Chưa thanh toán',
        };
    }

    /**
     * Step 1: Check-in patient for registration.
     */
    public function checkIn(): self
    {
        if (! $this->patient_id) {
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
        if (! in_array($status, ['eligible', 'deferred', 'contraindicated'])) {
            throw new \InvalidArgumentException('Trạng thái khám sàng lọc không hợp lệ.');
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
            throw new \RuntimeException('Không thể thực hiện tiêm chủng khi bệnh nhân chưa đủ điều kiện tiêm.');
        }

        if (! $this->patient_id) {
            $this->checkIn();
        }

        if (! $vaccineId) {
            $firstVaccine = $this->vaccines()->first();
            $vaccineId = $firstVaccine ? $firstVaccine->id : null;
            if (! $inventoryLotId && $firstVaccine && isset($firstVaccine->pivot->inventory_lot_id)) {
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
