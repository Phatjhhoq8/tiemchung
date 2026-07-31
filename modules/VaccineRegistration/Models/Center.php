<?php
/**
 * Chức năng: Model Center quản lý thông tin các trung tâm tiêm chủng Medicare Cờ Đỏ.
 * Lý do tạo: Cấu hình dữ liệu động cho danh sách trung tâm phục vụ việc lựa chọn địa điểm tiêm của khách hàng.
 */

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Center extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'address',
        'phone',
        'zalo_phone',
        'map_url',
        'working_hours',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope lọc trung tâm đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function centerVaccines()
    {
        return $this->hasMany(CenterVaccine::class);
    }
}
