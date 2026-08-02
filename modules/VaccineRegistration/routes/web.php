<?php
/**
 * Chức năng: Định nghĩa các tuyến đường (routes) của module VaccineRegistration.
 * Lý do chỉnh sửa: Phân tách rõ ràng các route client và nhóm route admin (được bảo vệ bởi middleware admin.auth).
 */

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Http\Controllers\HomeController;
use Modules\VaccineRegistration\Http\Controllers\VaccineController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminAuthController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminDashboardController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminVaccineController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminRegistrationController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminCenterController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminSettingController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminBannerController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminUserController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminStockController;
use Modules\VaccineRegistration\Http\Controllers\ConsultationLeadController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminConsultationLeadController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminScheduleController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminSlotController;
use Modules\VaccineRegistration\Http\Controllers\AdminArticleController;
use Modules\VaccineRegistration\Http\Controllers\ArticleController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminInventoryLotController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminPatientController;
use Modules\VaccineRegistration\Http\Controllers\Admin\VaccinationWorkflowController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminCustomerController;

Route::middleware('web')->group(function () {
    // --- Giao diện Khách hàng (Client Multi-Page SPA) ---
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/about', [HomeController::class, 'about'])->name('about');
    Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
    Route::post('/centers/select', [HomeController::class, 'selectCenter'])->name('centers.select');

    // Tin tức & Kiến thức y khoa
    Route::get('/news', [ArticleController::class, 'index'])->name('news.index');
    Route::get('/news/{slug}', [ArticleController::class, 'show'])->name('news.show');
    
    // Danh mục vắc xin & chi tiết
    Route::get('/vaccines', [VaccineController::class, 'index'])->name('vaccine.index');
    Route::get('/vaccines/disease/{disease}', [VaccineController::class, 'diseaseDetail'])->name('vaccine.disease');
    Route::post('/vaccines/disease/{disease}/consult', [VaccineController::class, 'postDiseaseConsult'])->middleware('throttle:10,1')->name('vaccine.disease.consult');
    Route::get('/vaccines/{id}', [VaccineController::class, 'show'])->name('vaccine.show');
    
    // Giỏ hàng
    Route::post('/cart/add', [VaccineController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove', [VaccineController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/clear', [VaccineController::class, 'clearCart'])->name('cart.clear');
    
    // Yêu cầu tư vấn (CRM Leads)
    Route::post('/consultations', [ConsultationLeadController::class, 'store'])->middleware('throttle:10,1')->name('consultations.store');
    Route::post('/leads', [ConsultationLeadController::class, 'store'])->middleware('throttle:10,1')->name('leads.store');
    
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
        Route::post('/context/center', function (Request $request) {
            $validated = $request->validate(['center_id' => 'required|exists:centers,id']);
            \Modules\VaccineRegistration\Support\AdminContext::setSelectedCenter((int) $validated['center_id']);

            return back()->with('success', 'Đã đổi ngữ cảnh chi nhánh quản trị.');
        })->name('context.center');

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Quản lý CRM Consultation Leads
        Route::get('/leads', [AdminConsultationLeadController::class, 'index'])->name('leads.index');
        Route::get('/leads/{id}', [AdminConsultationLeadController::class, 'show'])->name('leads.show');
        Route::patch('/leads/{id}/status', [AdminConsultationLeadController::class, 'updateStatus'])->name('leads.status');

        // Quản lý Vắc xin
        Route::get('/vaccin', function() { return redirect()->route('admin.vaccines.index'); });
        Route::post('/vaccines/{id}/toggle-featured', [AdminVaccineController::class, 'toggleFeatured'])->name('vaccines.toggle-featured');
        Route::resource('vaccines', AdminVaccineController::class)->except(['show']);

        // Quản lý Đăng ký tiêm chủng
        Route::get('/schedule', [AdminRegistrationController::class, 'schedule'])->name('schedule');
        Route::get('/registrations', [AdminRegistrationController::class, 'index'])->name('registrations.index');
        Route::get('/registrations/export/csv', [AdminRegistrationController::class, 'exportCsv'])->name('registrations.export.csv');
        Route::get('/registrations/{id}', [AdminRegistrationController::class, 'show'])->name('registrations.show');
        Route::patch('/registrations/{id}/status', [AdminRegistrationController::class, 'updateStatus'])->name('registrations.status');
        Route::post('/registrations/{id}/settle', [AdminRegistrationController::class, 'settle'])->name('registrations.settle');
        Route::post('/registrations/{id}/refund', [AdminRegistrationController::class, 'refund'])->name('registrations.refund');

        // Khách hàng household và lịch sử điểm.
        Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{id}', [AdminCustomerController::class, 'show'])->name('customers.show');
        Route::post('/customers/{id}/points/adjust', [AdminCustomerController::class, 'adjustPoints'])->name('customers.points.adjust');

        // Quản lý Lịch & Khung giờ (Schedules & Slots)
        Route::resource('schedules', AdminScheduleController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('/schedules/{schedule}/slots', [AdminScheduleController::class, 'storeSlot'])->name('schedules.slots.store');
        Route::resource('slots', AdminSlotController::class)->only(['store', 'update', 'destroy']);

        // Nhập/xuất/tồn kho theo chi nhánh
        Route::get('/stock', [AdminStockController::class, 'index'])->name('stock.index');
        Route::get('/stock/create', [AdminStockController::class, 'create'])->name('stock.create');
        Route::post('/stock', [AdminStockController::class, 'store'])->name('stock.store');

        // Quản lý lô vắc xin (Inventory Lots)
        Route::patch('/inventory-lots/{id}/status', [AdminInventoryLotController::class, 'updateStatus'])->name('inventory-lots.status');
        Route::resource('inventory-lots', AdminInventoryLotController::class)->except(['create', 'edit', 'show']);

        Route::middleware('super.admin')->group(function () {
            // Quản lý Trung tâm
            Route::resource('centers', AdminCenterController::class)->except(['show']);

            // Quản lý tài khoản chi nhánh
            Route::resource('users', AdminUserController::class)->except(['show']);

            // Quản lý Banner trang chủ
            Route::resource('banners', AdminBannerController::class)->except(['show']);

            // Quản lý Bài viết / Tin tức y tế (Mục 10)
            Route::post('/articles/upload-image', [AdminArticleController::class, 'uploadEditorImage'])->name('articles.upload-image');
            Route::resource('articles', AdminArticleController::class)->except(['show']);

            // Quản lý Cấu hình động (Settings)
            Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
            Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        });
    });
});
