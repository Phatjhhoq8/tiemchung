<?php
/**
 * Chức năng: AdminAuthController quản lý phiên đăng nhập và đăng xuất của Quản trị viên.
 * Lý do tạo: Tách biệt logic xác thực admin ra khỏi các controller khác, đảm bảo tính bảo mật.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

        $envUser = env('ADMIN_USERNAME', 'admin');
        $envPass = env('ADMIN_PASSWORD', 'admin123');

        if ($credentials['username'] === $envUser && $credentials['password'] === $envPass) {
            // Đăng nhập thành công
            session()->put('admin_logged_in', true);
            
            // Chống Session Fixation bằng cách regenerate session id
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard')->with('success', 'Chào mừng Quản trị viên đã quay lại!');
        }

        return back()->withErrors(['auth_failed' => 'Tên đăng nhập hoặc mật khẩu không chính xác.'])->withInput();
    }

    /**
     * Xử lý đăng xuất.
     */
    public function logout(Request $request)
    {
        session()->forget('admin_logged_in');
        
        // Hủy session hiện tại và khởi tạo lại token CSRF mới
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.show')->with('success', 'Đã đăng xuất tài khoản quản trị thành công.');
    }
}
