<?php
/**
 * Chức năng: VaccineServiceProvider nạp các cấu hình của module VaccineRegistration.
 * Lý do chỉnh sửa: Đăng ký thêm middleware admin.auth vào router để áp dụng bảo mật cho các route admin.
 */

namespace Modules\VaccineRegistration\Providers;

use Illuminate\Support\ServiceProvider;

class VaccineServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Load routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // 2. Load views (sử dụng namespace 'vaccine')
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'vaccine');

        // 3. Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // 4. Đăng ký middleware quản trị
        $this->app['router']->aliasMiddleware('admin.auth', \Modules\VaccineRegistration\Http\Middleware\AdminAuth::class);
    }
}
