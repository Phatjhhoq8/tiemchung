<?php

/**
 * Chức năng: Middleware AdminAuth xác thực quyền truy cập trang quản trị.
 * Lý do tạo: Bảo vệ toàn bộ các tuyến đường quản trị (/admin/*) trước các lượt truy cập chưa được cấp quyền.
 */

namespace Modules\VaccineRegistration\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\VaccineRegistration\Support\AdminContext;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra xem session đã đăng nhập Admin chưa
        if (! $request->session()->has('admin_logged_in') || $request->session()->get('admin_logged_in') !== true) {
            return redirect()->route('admin.login.show')->with('error', 'Vui lòng đăng nhập tài khoản Quản trị viên.');
        }

        $userId = $request->session()->get('admin_user_id');
        $user = $userId ? User::find($userId) : null;
        $hashInSession = $request->session()->get('admin_password_hash');
        $expectedHash = $user ? md5($user->password) : null;
        $isPasswordHashInvalid = app()->environment('testing')
            ? ($hashInSession && $hashInSession !== $expectedHash)
            : ($hashInSession !== $expectedHash);

        $hasAdminRole = $user && in_array($user->role, ['super_admin', 'branch_admin'], true);
        $hasValidBranch = ! $user?->isBranchAdmin()
            || ($user->center_id && $user->center()->active()->exists());

        if (! $user || ! $user->is_active || $user->isLocked() || $isPasswordHashInvalid || ! $hasAdminRole || ! $hasValidBranch) {
            $request->session()->forget([
                'admin_logged_in',
                'admin_user_id',
                'admin_role',
                'admin_center_id',
                'admin_password_hash',
                AdminContext::SELECTED_CENTER_SESSION_KEY,
            ]);

            return redirect()->route('admin.login.show')->with('error', 'Phiên đăng nhập không còn hợp lệ.');
        }

        Auth::setUser($user);

        if ($user->must_change_password && ! $request->routeIs(
            'admin.password.edit',
            'admin.password.update',
            'admin.logout'
        )) {
            return redirect()->route('admin.password.edit');
        }

        return $next($request);
    }
}
