<?php
/**
 * Chức năng: Middleware AdminAuth xác thực quyền truy cập trang quản trị.
 * Lý do tạo: Bảo vệ toàn bộ các tuyến đường quản trị (/admin/*) trước các lượt truy cập chưa được cấp quyền.
 */

namespace Modules\VaccineRegistration\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra xem session đã đăng nhập Admin chưa
        if (!$request->session()->has('admin_logged_in') || $request->session()->get('admin_logged_in') !== true) {
            return redirect()->route('admin.login.show')->with('error', 'Vui lòng đăng nhập tài khoản Quản trị viên.');
        }

        return $next($request);
    }
}
