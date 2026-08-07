<?php
/**
 * Chức năng: AdminAuthController quản lý phiên đăng nhập và đăng xuất của Quản trị viên.
 * Lý do chỉnh sửa: Thêm bảo mật kiểm tra tài khoản bị khóa (isLocked), giới hạn số lần đăng nhập (RateLimiter),
 * ghi nhận đăng nhập sai/thành công và tự động khóa tài khoản sau 5 lần nhập sai mật khẩu.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    /**
     * Hiển thị trang đăng nhập.
     */
    public function showLogin()
    {
        if (session('admin_logged_in') === true) {
            return redirect()->route('admin.dashboard');
        }
        return view('vaccine::admin.login');
    }

    /**
     * Xử lý đăng nhập.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Tên đăng nhập không được để trống.',
            'password.required' => 'Mật khẩu không được để trống.',
        ]);

        // 1. Fetch User
        $user = User::where(function ($q) use ($credentials) {
                $q->where('username', $credentials['username'])
                    ->orWhere('email', $credentials['username']);
            })
            ->whereIn('role', ['super_admin', 'branch_admin'])
            ->first();

        // 2. Check if user account is locked
        if ($user && $user->isLocked()) {
            Log::warning('Security Event: Login attempted on locked admin account', [
                'user_id' => $user->id,
                'username' => $user->username,
                'locked_until' => $user->locked_until?->toDateTimeString(),
                'ip' => $request->ip(),
            ]);

            return back()->withErrors([
                'auth_failed' => 'Tài khoản tạm thời bị khóa do đăng nhập sai quá nhiều lần.',
            ])->withInput();
        }

        // 3. Rate Limiting Check (Brute-force protection)
        $throttleKey = 'admin_login:' . Str::lower($credentials['username']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            Log::warning('Admin login rate limit exceeded', [
                'username' => $credentials['username'],
                'ip' => $request->ip(),
                'available_in' => $seconds,
            ]);

            return back()->withErrors([
                'auth_failed' => "Bạn đã thử quá nhiều lần. Vui lòng thử lại sau {$seconds} giây.",
            ])->withInput();
        }

        // 4. Check inactive status
        if ($user && (!$user->is_active || $user->status === 'inactive')) {
            Log::warning('Security Event: Login attempted on inactive admin account', [
                'user_id' => $user->id,
                'username' => $user->username,
                'ip' => $request->ip(),
            ]);

            return back()->withErrors([
                'auth_failed' => 'Tên đăng nhập hoặc mật khẩu không chính xác.',
            ])->withInput();
        }

        // 5. Verify password
        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Success: clear rate limiter and record login
            RateLimiter::clear($throttleKey);
            $user->recordSuccessfulLogin();

            Log::info('Security Event: Admin login successful', [
                'user_id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'ip' => $request->ip(),
            ]);

            session()->put('admin_logged_in', true);
            session()->put('admin_user_id', $user->id);
            session()->put('admin_role', $user->role);
            session()->put('admin_center_id', $user->center_id);
            session()->put('admin_password_hash', md5($user->password));
            
            // Chống Session Fixation bằng cách regenerate session id
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard')->with('success', 'Chào mừng ' . $user->name . ' đã quay lại!');
        }

        // 6. Password failure or user not found
        RateLimiter::hit($throttleKey, 60);

        if ($user) {
            $user->recordFailedLogin(5, 15);

            if ($user->isLocked()) {
                Log::warning('Security Event: Admin account locked due to 5 consecutive failed login attempts', [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'locked_until' => $user->locked_until?->toDateTimeString(),
                    'ip' => $request->ip(),
                ]);

                return back()->withErrors([
                    'auth_failed' => 'Tài khoản tạm thời bị khóa do đăng nhập sai quá nhiều lần.',
                ])->withInput();
            }

            Log::warning('Security Event: Admin login failed (wrong password)', [
                'user_id' => $user->id,
                'username' => $user->username,
                'failed_login_count' => $user->failed_login_count,
                'ip' => $request->ip(),
            ]);
        } else {
            Log::warning('Security Event: Admin login failed (user not found)', [
                'username' => $credentials['username'],
                'ip' => $request->ip(),
            ]);
        }

        return back()->withErrors([
            'auth_failed' => 'Tên đăng nhập hoặc mật khẩu không chính xác.',
        ])->withInput();
    }

    /**
     * Xử lý đăng xuất.
     */
    public function logout(Request $request)
    {
        session()->forget(['admin_logged_in', 'admin_user_id', 'admin_role', 'admin_center_id']);
        
        // Hủy session hiện tại và khởi tạo lại token CSRF mới
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.show')->with('success', 'Đã đăng xuất tài khoản quản trị thành công.');
    }
}
