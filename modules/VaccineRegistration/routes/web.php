<?php

/**
 * Chức năng: Định nghĩa các tuyến đường (routes) của module VaccineRegistration.
 * Lý do chỉnh sửa: Phân tách rõ ràng các route client và nhóm route admin (được bảo vệ bởi middleware admin.auth).
 */

use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminAuditLogController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminAuthController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminBannerController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminCenterController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminConsultationLeadController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminCustomerController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminDashboardController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminDefaultSlotController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminInventoryLotController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminLiveEditorController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminPatientController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminRegistrationController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminScheduleController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminLoyaltySettingController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminSettingController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminSlotController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminUserController;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminVaccineController;
use Modules\VaccineRegistration\Http\Controllers\Admin\VaccinationWorkflowController;
use Modules\VaccineRegistration\Http\Controllers\AdminArticleController;
use Modules\VaccineRegistration\Http\Controllers\ArticleController;
use Modules\VaccineRegistration\Http\Controllers\ConsultationLeadController;
use Modules\VaccineRegistration\Http\Controllers\HomeController;
use Modules\VaccineRegistration\Http\Controllers\VaccineController;
use Modules\VaccineRegistration\Support\AdminContext;
use Modules\VaccineRegistration\Http\Middleware\BlockInPreviewMode;

Route::middleware('web')->group(function () {
    // --- Giao diện Khách hàng (Client Multi-Page SPA) ---
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/about', [HomeController::class, 'about'])->name('about');
    Route::get('/services', [HomeController::class, 'services'])->name('services');
    Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
    Route::post('/centers/select', [HomeController::class, 'selectCenter'])->name('centers.select');

    // Tin tức & Kiến thức y khoa
    Route::get('/news', [ArticleController::class, 'index'])->name('news.index');
    Route::get('/news/{slug}', [ArticleController::class, 'show'])->name('news.show');

    // Danh mục vắc xin & chi tiết
    Route::get('/vaccines', [VaccineController::class, 'index'])->name('vaccine.index');
    Route::get('/vaccines/disease/{disease}', [VaccineController::class, 'diseaseDetail'])->name('vaccine.disease');
    Route::post('/vaccines/disease/{disease}/consult', [VaccineController::class, 'postDiseaseConsult'])->middleware(['throttle:10,1', BlockInPreviewMode::class])->name('vaccine.disease.consult');
    Route::get('/vaccines/{id}', [VaccineController::class, 'show'])->name('vaccine.show');

    // Giỏ hàng
    Route::post('/cart/add', [VaccineController::class, 'addToCart'])->middleware(BlockInPreviewMode::class)->name('cart.add');
    Route::post('/cart/remove', [VaccineController::class, 'removeFromCart'])->middleware(BlockInPreviewMode::class)->name('cart.remove');
    Route::post('/cart/clear', [VaccineController::class, 'clearCart'])->middleware(BlockInPreviewMode::class)->name('cart.clear');

    // Yêu cầu tư vấn (CRM Leads)
    Route::post('/consultations', [ConsultationLeadController::class, 'store'])->middleware(['throttle:10,1', BlockInPreviewMode::class])->name('consultations.store');
    Route::post('/leads', [ConsultationLeadController::class, 'store'])->middleware(['throttle:10,1', BlockInPreviewMode::class])->name('leads.store');

    // Quy trình đăng ký tiêm
    Route::get('/register', [VaccineController::class, 'showRegister'])->name('register.show');
    Route::post('/register', [VaccineController::class, 'postRegister'])->middleware(['throttle:15,1', BlockInPreviewMode::class])->name('register.post');
    Route::get('/success', [VaccineController::class, 'showSuccess'])->name('register.success');
    Route::get('/tra-cuu-lich-hen', [VaccineController::class, 'showBookingLookup'])->name('booking.lookup');
    Route::post('/tra-cuu-lich-hen', [VaccineController::class, 'lookupBookingsByPhone'])
        ->middleware('throttle:10,1')
        ->name('booking.lookup.submit');

    // --- Quản trị viên (Admin Auth) ---
    Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login.show');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])
        ->middleware('throttle:5,1') // Rate limit đăng nhập tối đa 5 lần mỗi phút
        ->name('admin.login');
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])
        ->middleware('admin.auth')
        ->name('admin.logout');

    // Redirect /admin sang /admin/dashboard
    Route::get('/admin', function () {
        return redirect()->route('admin.dashboard');
    });

    // --- Trang Quản trị (Bảo mật qua admin.auth) ---
    Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/password/change', [AdminAuthController::class, 'editPassword'])->name('password.edit');
        Route::put('/password/change', [AdminAuthController::class, 'updatePassword'])->name('password.update');

        Route::post('/context/center', function (Request $request) {
            $validated = $request->validate(['center_id' => 'nullable|integer|exists:centers,id']);
            $centerId = isset($validated['center_id']) ? (int) $validated['center_id'] : null;
            $oldCenterId = AdminContext::selectedCenterId();
            $center = AdminContext::setSelectedCenter($centerId);
            AuditLogger::log(
                'admin.center_context_changed',
                'center_context',
                'current',
                ['center_id' => $oldCenterId],
                ['center_id' => $center?->id]
            );

            $previousUrl = url()->previous();
            $path = parse_url($previousUrl, PHP_URL_PATH) ?: '/admin/dashboard';
            if (! str_starts_with($path, '/admin')) {
                $path = '/admin/dashboard';
            }

            parse_str((string) parse_url($previousUrl, PHP_URL_QUERY), $query);
            unset($query['center_id']);
            $target = $path.($query ? '?'.http_build_query($query) : '');

            return redirect($target)->with(
                'success',
                $center ? 'Đã chuyển sang chi nhánh '.$center->name.'.' : 'Đang xem dữ liệu của tất cả chi nhánh.'
            );
        })->name('context.center');

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Quản lý CRM Consultation Leads
        Route::get('/leads', [AdminConsultationLeadController::class, 'index'])->name('leads.index');
        Route::get('/leads/{id}', [AdminConsultationLeadController::class, 'show'])->name('leads.show');
        Route::patch('/leads/{id}/status', [AdminConsultationLeadController::class, 'updateStatus'])->name('leads.status');

        // Quản lý Vắc xin & Nhóm bệnh
        Route::get('/vaccin', function () {
            return redirect()->route('admin.vaccines.index');
        });
        Route::get('/categories', [AdminVaccineController::class, 'categoriesIndex'])->name('vaccines.categories');
        Route::post('/categories/check-delete', [AdminVaccineController::class, 'checkCategoryDelete'])->name('categories.check-delete');
        Route::put('/categories/update', [AdminVaccineController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/destroy', [AdminVaccineController::class, 'destroyCategory'])->name('categories.destroy');
        Route::post('/categories/bulk-destroy', [AdminVaccineController::class, 'bulkDestroyCategories'])->name('categories.bulk-destroy');
        Route::post('/categories/store-ajax', [AdminVaccineController::class, 'storeCategoryAjax'])->name('categories.store-ajax');
        Route::post('/metadata/check-delete', [AdminVaccineController::class, 'checkMetadataDelete'])->name('metadata.check-delete');
        Route::put('/metadata/update', [AdminVaccineController::class, 'updateMetadata'])->name('metadata.update');
        Route::delete('/metadata/destroy', [AdminVaccineController::class, 'destroyMetadata'])->name('metadata.destroy');
        Route::post('/vaccines/bulk-destroy', [AdminVaccineController::class, 'bulkDestroy'])->name('vaccines.bulk-destroy');
        Route::post('/vaccines/{id}/toggle-featured', [AdminVaccineController::class, 'toggleFeatured'])->name('vaccines.toggle-featured');
        Route::get('/vaccines/{id}/branches-stock', [AdminVaccineController::class, 'branchesStock'])->name('vaccines.branches-stock');
        Route::get('/vaccines/{id}/center-data', [AdminVaccineController::class, 'getCenterData'])->name('vaccines.center-data');
        Route::resource('vaccines', AdminVaccineController::class)->except(['show']);

        // Quản lý Đăng ký tiêm chủng
        Route::get('/schedule', [AdminRegistrationController::class, 'schedule'])->name('schedule');
        Route::get('/registrations', [AdminRegistrationController::class, 'index'])->name('registrations.index');
        Route::get('/registrations/export/csv', [AdminRegistrationController::class, 'exportCsv'])->name('registrations.export.csv');
        Route::get('/registrations/create', [AdminRegistrationController::class, 'create'])->name('registrations.create');
        Route::post('/registrations', [AdminRegistrationController::class, 'store'])->name('registrations.store');
        Route::get('/registrations/{id}', [AdminRegistrationController::class, 'show'])->name('registrations.show');
        Route::patch('/registrations/{id}/status', [AdminRegistrationController::class, 'updateStatus'])->name('registrations.status');
        Route::post('/registrations/{id}/settle', [AdminRegistrationController::class, 'settle'])->name('registrations.settle');
        Route::post('/registrations/{id}/settle-group', [AdminRegistrationController::class, 'settleGroup'])->name('registrations.settle-group');
        Route::post('/registrations/{id}/refund', [AdminRegistrationController::class, 'refund'])->name('registrations.refund');
        Route::post('/registrations/{id}/reschedule', [AdminRegistrationController::class, 'reschedule'])->name('registrations.reschedule');

        // Quy trình tiêm chủng lâm sàng (3 bước)
        Route::post('/registrations/{id}/check-in', [VaccinationWorkflowController::class, 'checkIn'])->name('registrations.check-in');
        Route::post('/registrations/{id}/screening', [VaccinationWorkflowController::class, 'screening'])->name('registrations.screening');
        Route::post('/registrations/{id}/administer', [VaccinationWorkflowController::class, 'administer'])->name('registrations.administer');

        // Quản lý bệnh nhân
        Route::resource('patients', AdminPatientController::class)->except(['create', 'edit']);

        // Khách hàng household và lịch sử điểm.
        Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/lookup', [AdminCustomerController::class, 'lookup'])->name('customers.lookup');
        Route::get('/customers/{id}', [AdminCustomerController::class, 'show'])->name('customers.show');
        Route::post('/customers/{id}/points/adjust', [AdminCustomerController::class, 'adjustPoints'])->name('customers.points.adjust');

        // Quản lý Lịch & Khung giờ (Schedules & Slots)
        Route::get('/default-slots', [AdminDefaultSlotController::class, 'index'])->name('default-slots.index');
        Route::post('/default-slots/update', [AdminDefaultSlotController::class, 'update'])->name('default-slots.update');
        Route::post('/schedules/copy', [AdminScheduleController::class, 'copySchedule'])->name('schedules.copy');
        Route::post('/schedules/toggle-day', [AdminScheduleController::class, 'toggleDayStatus'])->name('schedules.toggle-day');
        Route::delete('/schedules/day', [AdminScheduleController::class, 'destroyDay'])->name('schedules.destroy-day');
        Route::resource('schedules', AdminScheduleController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('/schedules/{schedule}/slots', [AdminScheduleController::class, 'storeSlot'])->name('schedules.slots.store');
        Route::resource('slots', AdminSlotController::class)->only(['store', 'update', 'destroy']);

        // Quản lý lô vắc xin (Inventory Lots)
        Route::patch('/inventory-lots/{id}/status', [AdminInventoryLotController::class, 'updateStatus'])->name('inventory-lots.status');
        Route::resource('inventory-lots', AdminInventoryLotController::class)->except(['create', 'edit', 'show']);

        // Cấu hình tích điểm (Dành cho cả Super Admin và Branch Admin)
        Route::get('/settings/loyalty', [AdminLoyaltySettingController::class, 'index'])->name('settings.loyalty');
        Route::post('/settings/loyalty', [AdminLoyaltySettingController::class, 'update'])->name('settings.loyalty.update');
        Route::post('/settings/loyalty/sync', [AdminLoyaltySettingController::class, 'syncSettings'])->name('settings.loyalty.sync');
        Route::post('/settings/loyalty/reject-sync', [AdminLoyaltySettingController::class, 'rejectSync'])->name('settings.loyalty.reject-sync');

        Route::middleware('super.admin')->group(function () {
            // Trình Chỉnh Sửa Trực Quan Xem Trước (Visual Live Page Customizer)
            Route::get('/live-editor', [AdminLiveEditorController::class, 'index'])->name('live-editor');
            Route::post('/live-editor/banner', [AdminLiveEditorController::class, 'updateBanner'])->name('live-editor.banner');
            Route::post('/live-editor/vaccine', [AdminLiveEditorController::class, 'updateVaccine'])->name('live-editor.vaccine');
            Route::post('/live-editor/settings', [AdminLiveEditorController::class, 'updateSettings'])->name('live-editor.settings');
            Route::post('/live-editor/layout/save', [AdminLiveEditorController::class, 'saveLayoutConfig'])->name('live-editor.layout.save');
            Route::post('/live-editor/layout/publish', [AdminLiveEditorController::class, 'publishLayoutConfig'])->name('live-editor.layout.publish');
            Route::post('/live-editor/layout/reset', [AdminLiveEditorController::class, 'resetLayoutConfig'])->name('live-editor.layout.reset');

            // Quản lý Cấu hình động (Settings)
            Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
            Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

            // Tra cứu nhật ký hệ thống (chỉ đọc)
            Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');
            Route::get('/audit-logs/{auditLog}', [AdminAuditLogController::class, 'show'])->name('audit-logs.show');

            // Quản lý Trung tâm
            Route::post('/centers/{id}/toggle-status', [AdminCenterController::class, 'toggleStatus'])->name('centers.toggle-status');
            Route::post('/centers/bulk-destroy', [AdminCenterController::class, 'bulkDestroy'])->name('centers.bulk-destroy');
            Route::resource('centers', AdminCenterController::class)->except(['show']);

            // Quản lý tài khoản chi nhánh
            Route::resource('users', AdminUserController::class)->except(['show']);

            // Quản lý Banner trang chủ
            Route::post('/banners/{id}/toggle-status', [AdminBannerController::class, 'toggleStatus'])->name('banners.toggle-status');
            Route::post('/banners/bulk-destroy', [AdminBannerController::class, 'bulkDestroy'])->name('banners.bulk-destroy');
            Route::resource('banners', AdminBannerController::class)->except(['show']);

            // Quản lý Bài viết / Tin tức y tế (Mục 10)
            Route::post('/articles/upload-image', [AdminArticleController::class, 'uploadEditorImage'])->name('articles.upload-image');
            Route::post('/articles/{id}/toggle-status', [AdminArticleController::class, 'toggleStatus'])->name('articles.toggle-status');
            Route::post('/articles/bulk-destroy', [AdminArticleController::class, 'bulkDestroy'])->name('articles.bulk-destroy');
            Route::resource('articles', AdminArticleController::class)->except(['show']);
        });
    });
});
