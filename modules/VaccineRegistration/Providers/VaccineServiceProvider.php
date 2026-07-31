<?php
/**
 * Chức năng: VaccineServiceProvider nạp các cấu hình của module VaccineRegistration.
 * Lý do chỉnh sửa: Đăng ký thêm middleware admin.auth vào router để áp dụng bảo mật cho các route admin.
 */

namespace Modules\VaccineRegistration\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Modules\VaccineRegistration\Support\CenterContext;
use Modules\VaccineRegistration\Support\AdminContext;

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
        $this->app['router']->aliasMiddleware('super.admin', \Modules\VaccineRegistration\Http\Middleware\SuperAdminOnly::class);

        // 5. Đăng ký Policy phân quyền RBAC
        \Illuminate\Support\Facades\Gate::policy(\Modules\VaccineRegistration\Models\Vaccine::class, \Modules\VaccineRegistration\Policies\VaccinePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\Modules\VaccineRegistration\Models\CenterVaccine::class, \Modules\VaccineRegistration\Policies\CenterVaccinePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\Modules\VaccineRegistration\Models\Registration::class, \Modules\VaccineRegistration\Policies\RegistrationPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\Modules\VaccineRegistration\Models\Center::class, \Modules\VaccineRegistration\Policies\CenterPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\Modules\VaccineRegistration\Models\Banner::class, \Modules\VaccineRegistration\Policies\BannerPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\Modules\VaccineRegistration\Models\Article::class, \Modules\VaccineRegistration\Policies\ArticlePolicy::class);

        // 6. Đăng ký Artisan commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\CreateAdminCommand::class,
                \Modules\VaccineRegistration\Console\Commands\CreateAdminCommand::class,
            ]);
        }

        View::composer('vaccine::*', function ($view) {
            if (app()->runningInConsole()) {
                return;
            }

            $view->with('currentCenter', CenterContext::current());
            $view->with('activeCenters', CenterContext::activeCenters());
            $view->with('adminUser', AdminContext::user());
            $view->with('isSuperAdmin', AdminContext::isSuperAdmin());
        });
    }
}
