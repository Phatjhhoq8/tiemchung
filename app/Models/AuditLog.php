<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\VaccineRegistration\Models\Center;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'actor_id',
        'center_id',
        'action',
        'resource_type',
        'resource_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    protected static function booted(): void
    {
        static::updating(fn () => throw new \LogicException('Audit logs cannot be updated.'));
        static::deleting(fn () => throw new \LogicException('Audit logs cannot be deleted.'));
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function center()
    {
        return $this->belongsTo(Center::class, 'center_id');
    }

    /**
     * Danh sách nhãn tiếng Việt hoàn chỉnh cho các hành động (Dành cho người Non-Tech)
     */
    public static function actionLabels(): array
    {
        return [
            // Trung tâm & Chi nhánh
            'center.activated' => 'Kích hoạt chi nhánh',
            'center.deactivated' => 'Tạm ngưng chi nhánh',
            'center.created' => 'Thêm chi nhánh mới',
            'center.updated' => 'Cập nhật chi nhánh',
            'center.deleted' => 'Xóa chi nhánh',
            'admin.center_context_changed' => 'Đổi chi nhánh làm việc',

            // Đăng nhập / Xác thực
            'auth.login_succeeded' => 'Đăng nhập thành công',
            'auth.login_failed' => 'Đăng nhập thất bại',
            'auth.login' => 'Đăng nhập hệ thống',
            'auth.logout' => 'Đăng xuất',

            // Quy trình tiêm chủng
            'vaccination.checked_in' => 'Check-in tiếp đón',
            'vaccination.screened' => 'Khám sàng lọc',
            'vaccination.administered' => 'Xác nhận đã tiêm',

            // Đơn đăng ký & Lịch hẹn
            'registration.created' => 'Tạo lịch tiêm mới',
            'registration.updated' => 'Cập nhật lịch tiêm',
            'registration.status_updated' => 'Đổi trạng thái lịch tiêm',
            'registration.deleted' => 'Xóa lịch tiêm',
            'registration_settled' => 'Xác nhận thanh toán',
            'registration.exported' => 'Xuất file Excel/CSV lịch tiêm',

            // Bệnh nhân & Khách hàng
            'patient.created' => 'Tạo hồ sơ bệnh nhân',
            'patient.updated' => 'Cập nhật hồ sơ bệnh nhân',
            'patient.deleted' => 'Xóa hồ sơ bệnh nhân',

            // Vắc xin
            'vaccine.created' => 'Thêm vắc xin',
            'vaccine.updated' => 'Cập nhật vắc xin',
            'vaccine.deleted' => 'Xóa vắc xin',
            'vaccine.featured_changed' => 'Đổi trạng thái vắc xin nổi bật',
            'price_update' => 'Cập nhật giá vắc xin',

            // Cấu hình & Người dùng
            'loyalty_settings.center_reset_to_system' => 'Khôi phục tích điểm mặc định',
            'user.created' => 'Tạo tài khoản quản trị',
            'user.updated' => 'Cập nhật tài khoản',
            'user.deleted' => 'Xóa tài khoản',
            'admin_user.created' => 'Tạo tài khoản quản trị',
            'admin_user.updated' => 'Cập nhật tài khoản quản trị',
            'admin_user.deleted' => 'Xóa tài khoản quản trị',
            'auth.password_changed' => 'Đổi mật khẩu tài khoản',
            'publish_settings' => 'Xuất bản cấu hình trang web',

            // Nội dung
            'article.created' => 'Đăng bài viết mới',
            'article.updated' => 'Cập nhật bài viết',
            'article.deleted' => 'Xóa bài viết',
            'banner.created' => 'Thêm biểu ngữ',
            'banner.updated' => 'Cập nhật biểu ngữ',
            'banner.deleted' => 'Xóa biểu ngữ',
        ];
    }

    /**
     * Danh sách nhãn tiếng Việt cho các loại tài nguyên
     */
    public static function resourceTypeLabels(): array
    {
        return [
            'center' => 'Chi nhánh',
            'center_context' => 'Chi nhánh làm việc',
            'patient' => 'Hồ sơ bệnh nhân',
            'registration' => 'Lịch tiêm chủng',
            'registration_export' => 'Xuất danh sách',
            'vaccine' => 'Vắc xin',
            'admin_user' => 'Tài khoản quản trị',
            'user' => 'Người dùng',
            'setting' => 'Cấu hình hệ thống',
            'article' => 'Bài viết tin tức',
            'banner' => 'Biểu ngữ banner',
        ];
    }

    /**
     * Nhãn tiếng Việt hiển thị của hành động
     */
    public function getActionLabelAttribute(): string
    {
        return self::actionLabels()[$this->action] ?? $this->action;
    }

    /**
     * Nhãn tiếng Việt hiển thị của loại tài nguyên
     */
    public function getResourceTypeLabelAttribute(): string
    {
        return self::resourceTypeLabels()[$this->resource_type] ?? $this->resource_type;
    }

    /**
     * Tên hiển thị chi tiết, thân thiện cho người dùng Non-Tech
     */
    public function getResourceDisplayNameAttribute(): string
    {
        // 1. Nếu là chuyển ngữ cảnh chi nhánh làm việc
        if ($this->resource_type === 'center_context' || $this->action === 'admin.center_context_changed') {
            $targetCenterId = $this->new_values['center_id'] ?? null;
            if ($targetCenterId) {
                $centerName = Center::where('id', $targetCenterId)->value('name');
                return $centerName ? "Chi nhánh: {$centerName}" : "Chi nhánh (#{$targetCenterId})";
            }
            return 'Xem: Toàn hệ thống';
        }

        // 2. Nếu là xuất file dữ liệu
        if ($this->resource_type === 'registration_export' || $this->action === 'registration.exported') {
            $count = $this->new_values['record_count'] ?? null;
            return $count !== null ? "Xuất {$count} lịch tiêm" : "Xuất danh sách lịch tiêm";
        }

        // 3. Chi nhánh
        if ($this->resource_type === 'center') {
            $centerName = Center::where('id', $this->resource_id)->value('name');
            return $centerName ? "Chi nhánh: {$centerName}" : "Chi nhánh (#{$this->resource_id})";
        }

        // 4. Vắc xin
        if ($this->resource_type === 'vaccine') {
            $vacName = \Modules\VaccineRegistration\Models\Vaccine::where('id', $this->resource_id)->value('name');
            return $vacName ? "Vắc xin: {$vacName}" : "Vắc xin (#{$this->resource_id})";
        }

        // 5. Hồ sơ bệnh nhân
        if ($this->resource_type === 'patient') {
            $patientName = \Modules\VaccineRegistration\Models\Patient::where('id', $this->resource_id)->value('full_name');
            return $patientName ? "Bệnh nhân: {$patientName} (#{$this->resource_id})" : "Bệnh nhân (#{$this->resource_id})";
        }

        // 6. Lịch hẹn tiêm / Đơn đăng ký
        if ($this->resource_type === 'registration') {
            $regCode = \Modules\VaccineRegistration\Models\Registration::where('id', $this->resource_id)->value('registration_code');
            return $regCode ? "Lịch tiêm: #{$regCode}" : "Lịch tiêm (#{$this->resource_id})";
        }

        // 7. Tài khoản
        if (in_array($this->resource_type, ['user', 'admin_user'])) {
            $userName = User::where('id', $this->resource_id)->value('name');
            return $userName ? "Tài khoản: {$userName}" : "Tài khoản (#{$this->resource_id})";
        }

        // 8. Bài viết
        if ($this->resource_type === 'article') {
            $articleTitle = \Modules\VaccineRegistration\Models\Article::where('id', $this->resource_id)->value('title');
            return $articleTitle ? "Bài viết: {$articleTitle}" : "Bài viết (#{$this->resource_id})";
        }

        // 9. Biểu ngữ
        if ($this->resource_type === 'banner') {
            $bannerTitle = \Modules\VaccineRegistration\Models\Banner::where('id', $this->resource_id)->value('title');
            return $bannerTitle ? "Banner: {$bannerTitle}" : "Banner (#{$this->resource_id})";
        }

        // Mặc định
        $typeLabel = $this->resource_type_label;
        if ($this->resource_id && $this->resource_id !== 'current') {
            return "{$typeLabel} (#{$this->resource_id})";
        }
        return $typeLabel;
    }

    /**
     * Class CSS màu sắc cho badge
     */
    public function getActionBadgeClassAttribute(): string
    {
        if (str_contains($this->action, 'created') || str_contains($this->action, 'activated') || str_contains($this->action, 'login_succeeded') || str_contains($this->action, 'administered') || str_contains($this->action, 'settled')) {
            return 'badge-modern-success';
        }
        if (str_contains($this->action, 'deleted') || str_contains($this->action, 'deactivated') || str_contains($this->action, 'failed')) {
            return 'badge-modern-danger';
        }
        if (str_contains($this->action, 'exported') || str_contains($this->action, 'featured') || str_contains($this->action, 'screened')) {
            return 'badge-modern-warning';
        }
        return 'badge-modern-info';
    }
}
