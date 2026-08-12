<?php

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';

    protected $fillable = [
        'identity_card',
        'full_name',
        'dob',
        'gender',
        'phone',
        'address',
        'medical_history',
        'is_active',
    ];

    protected $casts = [
        'dob' => 'date',
        'is_active' => 'boolean',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function administeredDoses()
    {
        return $this->hasMany(AdministeredDose::class);
    }

    /**
     * Find existing patient by identity_card or phone, or create a new one.
     */
    public static function findOrCreateCentralized(array $data): self
    {
        $query = static::query();

        // 1. Tìm theo số định danh (identity_card) nếu có
        if (!empty($data['identity_card'])) {
            $patient = $query->where('identity_card', $data['identity_card'])->first();
            if ($patient) {
                return $patient;
            }
        }

        // 2. Tìm theo tổ hợp: SĐT + Họ tên + Ngày sinh (để phân biệt các thành viên gia đình dùng chung SĐT)
        if (!empty($data['phone'])) {
            $name = $data['full_name'] ?? $data['patient_name'] ?? '';
            $dob = $data['dob'] ?? $data['patient_dob'] ?? null;
            
            $patient = static::where('phone', $data['phone'])
                ->where('full_name', trim($name))
                ->when($dob, fn($q) => $q->whereDate('dob', $dob))
                ->first();
                
            if ($patient) {
                return $patient;
            }
        }

        // 3. Tạo mới nếu không tìm thấy
        return static::create([
            'identity_card' => $data['identity_card'] ?? null,
            'full_name' => $data['full_name'] ?? $data['patient_name'] ?? 'Chưa rõ',
            'dob' => $data['dob'] ?? $data['patient_dob'] ?? now()->toDateString(),
            'gender' => $data['gender'] ?? $data['patient_gender'] ?? 'Khác',
            'phone' => $data['phone'] ?? $data['patient_phone'] ?? '',
            'address' => $data['address'] ?? $data['patient_address'] ?? null,
            'medical_history' => $data['medical_history'] ?? null,
            'is_active' => true,
        ]);
    }
}
