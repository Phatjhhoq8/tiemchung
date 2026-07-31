<?php

namespace Modules\VaccineRegistration\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Support\AdminContext;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!AdminContext::isSuperAdmin()) {
            abort(403, 'Bạn không có quyền truy cập chức năng này.');
        }

        return $next($request);
    }
}
