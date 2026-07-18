<?php
/**
 * Chức năng: Định nghĩa các tuyến đường (routes) của module VaccineRegistration.
 * Lý do chỉnh sửa: Phân tách rõ ràng các route client và nhóm route admin (được bảo vệ bởi middleware admin.auth).
 */

use Illuminate\Support\Facades\Route;
use Modules\VaccineRegistration\Http\Controllers\HomeController;
use Modules\VaccineRegistration\Http\Controllers\VaccineController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminAuthController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminDashboardController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminVaccineController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminRegistrationController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminCenterController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminSettingController;

Route::middleware('web')->group(function () {
    // --- Giao diện Khách hàng (Client) ---
    Route::get('/', [HomeController::class, 'index'])->name('home');
    
    // Danh mục vắc xin & chi tiết
    Route::get('/vaccines', [VaccineController::class, 'index'])->name('vaccine.index');
    Route::get('/vaccines/{id}', [VaccineController::class, 'show'])->name('vaccine.show');
    
    // Giỏ hàng
    Route::post('/cart/add', [VaccineController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove', [VaccineController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/clear', [VaccineController::class, 'clearCart'])->name('cart.clear');
    
    // Quy trình đăng ký tiêm
    Route::get('/register', [VaccineController::class, 'showRegister'])->name('register.show');
    Route::post('/register', [VaccineController::class, 'postRegister'])->name('register.post');
    Route::get('/success', [VaccineController::class, 'showSuccess'])->name('register.success');

    // --- Quản trị viên (Admin Auth) ---
    Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login.show');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])
        ->middleware('throttle:5,1') // Rate limit đăng nhập tối đa 5 lần mỗi phút
        ->name('admin.login');
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    // Redirect /admin sang /admin/dashboard
    Route::get('/admin', function () {
        return redirect()->route('admin.dashboard');
    });

    // --- Trang Quản trị (Bảo mật qua admin.auth) ---
    Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Quản lý Vắc xin
        Route::resource('vaccines', AdminVaccineController::class)->except(['show']);

        // Quản lý Đăng ký tiêm chủng
        Route::get('/registrations', [AdminRegistrationController::class, 'index'])->name('registrations.index');
        Route::get('/registrations/{id}', [AdminRegistrationController::class, 'show'])->name('registrations.show');
        Route::patch('/registrations/{id}/status', [AdminRegistrationController::class, 'updateStatus'])->name('registrations.status');

        // Quản lý Trung tâm
        Route::resource('centers', AdminCenterController::class)->except(['show']);

        // Quản lý Cấu hình động (Settings)
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });
});
