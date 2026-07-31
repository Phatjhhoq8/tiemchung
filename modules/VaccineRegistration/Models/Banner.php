<?php
/**
 * Chức năng: Model Banner quản lý slider trang chủ Medicare Cờ Đỏ.
 * Lý do tạo: Cho phép quản lý và hiển thị danh sách ảnh/nội dung quảng bá động trên trang chủ.
 */

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image_url',
        'link_url',
        'sort_order',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Banner $banner) {
            $banner->is_active = false;
            $banner->save();
            return false; // Prevent hard deletion
        });
    }

    /**
     * Scope lọc banner đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope sắp xếp thứ tự hiển thị
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
