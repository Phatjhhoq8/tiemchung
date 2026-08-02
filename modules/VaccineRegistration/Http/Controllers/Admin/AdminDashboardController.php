<?php
/**
 * Chức năng: AdminDashboardController hiển thị trang thống kê tổng quan của Admin.
 * Lý do tạo: Tải động các số liệu thống kê doanh thu, đơn hàng tiêm chủng từ cơ sở dữ liệu MySQL.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminDashboardController extends Controller
{
    /**
     * Hiển thị bảng điều khiển thống kê.
     */
    public function index(Request $request)
    {
        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get();
        $selectedCenterId = AdminContext::isBranchAdmin()
            ? AdminContext::centerId()
            : ($request->filled('center_id') ? (int) $request->input('center_id') : null);

        $registrationQuery = Registration::query();
        $productQuery = CenterVaccine::query();

        if ($selectedCenterId) {
            $registrationQuery->where('center_id', $selectedCenterId);
            $productQuery->where('center_id', $selectedCenterId);
        }

        $stats = (clone $registrationQuery)->selectRaw("
            COUNT(*) as total_registrations,
            COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_price - points_discount_amount ELSE 0 END), 0) as total_revenue,
            COALESCE(SUM(CASE WHEN payment_status = 'unpaid' AND booking_status != 'cancelled' THEN 1 ELSE 0 END), 0) as pending_count,
            COALESCE(SUM(CASE WHEN booking_status = 'completed' THEN 1 ELSE 0 END), 0) as completed_count
        ")->first();

        $totalRegistrations = (int) $stats->total_registrations;
        $totalRevenue = (int) $stats->total_revenue;
        $pendingCount = (int) $stats->pending_count;
        $completedCount = (int) $stats->completed_count;
        $consultCount = 0;
        $importedQuantity = 0;
        $soldQuantity = 0;

        // 5. Lấy 8 đơn đăng ký mới nhất
        $recentRegistrations = (clone $registrationQuery)->latest()->take(8)->get();

        // 6. Số lượng vắc xin và trung tâm hoạt động
        $vaccinesCount = (clone $productQuery)->where('is_active', true)->count();
        $centersCount = $centers->count();

        return view('vaccine::admin.dashboard', compact(
            'totalRegistrations',
            'totalRevenue',
            'pendingCount',
            'completedCount',
            'recentRegistrations',
            'vaccinesCount',
            'centersCount',
            'consultCount',
            'importedQuantity',
            'soldQuantity',
            'centers',
            'selectedCenterId'
        ));
    }
}
