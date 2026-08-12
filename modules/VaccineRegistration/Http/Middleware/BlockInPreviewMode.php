<?php

namespace Modules\VaccineRegistration\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockInPreviewMode
{
    /**
     * Chặn các hành động sửa đổi dữ liệu (đặt tiêm, giỏ hàng, tư vấn) khi ở chế độ xem thử.
     */
    public function handle(Request $request, Closure $next)
    {
        $isPreview = $request->query('preview') == '1' || $request->header('X-Preview-Mode') == '1';
        
        $referer = $request->headers->get('referer');
        if ($referer && str_contains($referer, 'preview=1') && \Modules\VaccineRegistration\Support\AdminContext::user() !== null) {
            $isPreview = true;
        }

        if ($isPreview) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể thực hiện đặt tiêm hoặc gửi yêu cầu trong chế độ xem thử bản nháp!'
                ], 403);
            }

            return redirect()->back()->with('error', 'Không thể thực hiện đặt tiêm hoặc gửi yêu cầu trong chế độ xem thử bản nháp!');
        }

        return $next($request);
    }
}
