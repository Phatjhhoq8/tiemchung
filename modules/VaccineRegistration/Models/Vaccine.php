<?php
/**
 * Chức năng: Model Vaccine quản lý thông tin các loại vắc xin/gói vắc xin.
 * Lý do tạo/chỉnh sửa: Bổ sung các cột type (loại vắc xin), doses (số mũi tiêm) và các scope hỗ trợ truy vấn nhanh.
 */

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vaccine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'type', // 'single' (lẻ) hoặc 'package' (gói)
        'doses', // số mũi tiêm
        'description',
        'disease_prevention',
        'age_group',
        'origin',
        'image',
    ];

    /**
     * Scope lọc vắc xin lẻ
     */
    public function scopeSingle($query)
    {
        return $query->where('type', 'single');
    }

    /**
     * Scope lọc gói vắc xin
     */
    public function scopePackage($query)
    {
        return $query->where('type', 'package');
    }

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
