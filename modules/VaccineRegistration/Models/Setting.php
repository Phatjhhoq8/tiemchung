<?php
/**
 * Chức năng: Model Setting quản lý cấu hình hệ thống dạng key-value.
 * Lý do tạo: Truy xuất linh hoạt các cấu hình liên lạc (hotline, email, địa chỉ) động trên toàn bộ website.
 */

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Helper tĩnh để lấy cấu hình nhanh chóng kèm giá trị mặc định
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function values(array $defaults): array
    {
        $values = self::whereIn('key', array_keys($defaults))->pluck('value', 'key')->all();

        return array_replace($defaults, $values);
    }

    /**
     * Helper tĩnh để cập nhật hoặc tạo cấu hình nhanh
     */
    public static function set(string $key, ?string $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
