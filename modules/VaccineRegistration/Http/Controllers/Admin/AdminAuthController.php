<?php
/**
 * Chức năng: AdminAuthController quản lý phiên đăng nhập và đăng xuất của Quản trị viên.
 * Lý do tạo: Tách biệt logic xác thực admin ra khỏi các controller khác, đảm bảo tính bảo mật.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        $this->ensureSuperAdminExists();

        $user = User::where(function ($q) use ($credentials) {
                $q->where('username', $credentials['username'])
                    ->orWhere('email', $credentials['username']);
            })
            ->whereIn('role', ['super_admin', 'branch_admin'])
            ->first();

        if ($user && $user->is_active && Hash::check($credentials['password'], $user->password)) {
            // Đăng nhập thành công
            session()->put('admin_logged_in', true);
            session()->put('admin_user_id', $user->id);
            session()->put('admin_role', $user->role);
            session()->put('admin_center_id', $user->center_id);
            
            // Chống Session Fixation bằng cách regenerate session id
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard')->with('success', 'Chào mừng ' . $user->name . ' đã quay lại!');
        }

        return back()->withErrors(['auth_failed' => 'Tên đăng nhập hoặc mật khẩu không chính xác.'])->withInput();
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

    private function ensureSuperAdminExists(): void
    {
        if (User::where('role', 'super_admin')->exists()) {
            return;
        }

        $username = env('ADMIN_USERNAME', 'admin');
        $password = env('ADMIN_PASSWORD', 'admin123');

        User::create([
            'name' => 'Admin Gốc',
            'username' => $username,
            'email' => $username . '@medicare.local',
            'password' => $password,
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }
}
