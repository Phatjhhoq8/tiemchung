<?php

/**
 * Chức năng: AdminSettingController quản lý cấu hình chung (Settings) của trang web.
 * Lý do tạo: Cho phép Quản trị viên thay đổi thông tin Hotline, địa chỉ, email và thông tin chân trang của Medicare Cờ Đỏ.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Setting;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminSettingController extends Controller
{
    public function __construct()
    {
        // Protected by route middleware 'super.admin' and explicit abort_unless checks
    }

    /**
     * Hiển thị danh sách cấu hình.
     */
    public function index()
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền xem cấu hình trang web.');
        $settings = [
            'site_name' => Setting::get('site_name', 'Medicare'),
            'hotline' => Setting::get('hotline', '0938 60 38 39'),
            'hotline_2' => Setting::get('hotline_2', '0932 477 184'),
            'email' => Setting::get('email', 'cskh@medicarecodo.vn'),
            'footer_text' => Setting::get('footer_text', '© 2026 Medicare. Hệ thống tiêm chủng uy tín.'),
        ];

        return view('vaccine::admin.settings.index', compact('settings'));
    }

    /**
     * Cập nhật các cấu hình.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'hotline' => 'required|string|max:50',
            'hotline_2' => 'nullable|string|max:50',
            'email' => 'required|email|max:255',
            'footer_text' => 'required|string|max:500',
        ], [
            'site_name.required' => 'Tên trang web không được để trống.',
            'hotline.required' => 'Số điện thoại đường dây nóng không được để trống.',
            'email.required' => 'Địa chỉ thư điện tử không được để trống.',
            'email.email' => 'Địa chỉ thư điện tử không đúng định dạng.',
            'footer_text.required' => 'Nội dung chân trang không được để trống.',
        ]);

        $oldValues = Setting::whereIn('key', array_keys($validated))->pluck('value', 'key')->all();
        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }
        AuditLogger::log('setting.updated', 'setting', 'site', $oldValues, $validated, resolveCenter: false);

        return redirect()->route('admin.settings.index')->with('success', 'Cập nhật cấu hình trang web thành công.');
    }
}
