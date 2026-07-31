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
use Modules\VaccineRegistration\Models\VaccineStockMovement;
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
        $stockQuery = VaccineStockMovement::query();
        $productQuery = CenterVaccine::query();

        if ($selectedCenterId) {
            $registrationQuery->where('center_id', $selectedCenterId);
            $stockQuery->where('center_id', $selectedCenterId);
            $productQuery->where('center_id', $selectedCenterId);
        }

        // 1. Thống kê tổng số đơn đăng ký
        $totalRegistrations = (clone $registrationQuery)->count();

        // 2. Thống kê tổng doanh thu dự kiến (tổng tiền các đơn đăng ký)
        $totalRevenue = (clone $registrationQuery)->where('status', '!=', 'Đã hủy')->sum('total_price');

        // 3. Số đơn chờ thanh toán
        $pendingCount = (clone $registrationQuery)->whereIn('status', ['pending', 'Chờ thanh toán'])->count();

        // 4. Số đơn đã hoàn tất thanh toán / tiêm xong
        $completedCount = (clone $registrationQuery)->whereIn('status', ['Đã thanh toán', 'Đã tiêm'])->count();

        $consultCount = (clone $registrationQuery)->whereIn('status', ['Chờ tư vấn', 'Đã tư vấn'])->count();

        $importedQuantity = (clone $stockQuery)->where('type', 'import')->sum('quantity');
        $soldQuantity = (clone $stockQuery)->where('type', 'sale')->sum('quantity');

        // 5. Lấy 8 đơn đăng ký mới nhất
        $recentRegistrations = (clone $registrationQuery)->latest()->take(8)->get();

        // 6. Số lượng vắc xin và trung tâm hoạt động
        $vaccinesCount = (clone $productQuery)->where('is_active', true)->count();
        $centersCount = Center::active()->count();

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
