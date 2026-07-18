<?php
/**
 * Chức năng: AdminDashboardController hiển thị trang thống kê tổng quan của Admin.
 * Lý do tạo: Tải động các số liệu thống kê doanh thu, đơn hàng tiêm chủng từ cơ sở dữ liệu MySQL.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Models\Center;

class AdminDashboardController extends Controller
{
    /**
     * Hiển thị bảng điều khiển thống kê.
     */
    public function index()
    {
        // 1. Thống kê tổng số đơn đăng ký
        $totalRegistrations = Registration::count();

        // 2. Thống kê tổng doanh thu dự kiến (tổng tiền các đơn đăng ký)
        $totalRevenue = Registration::where('status', '!=', 'Đã hủy')->sum('total_price');

        // 3. Số đơn chờ thanh toán
        $pendingCount = Registration::whereIn('status', ['pending', 'Chờ thanh toán'])->count();

        // 4. Số đơn đã hoàn tất thanh toán / tiêm xong
        $completedCount = Registration::whereIn('status', ['Đã thanh toán', 'Đã tiêm'])->count();

        // 5. Lấy 8 đơn đăng ký mới nhất
        $recentRegistrations = Registration::latest()->take(8)->get();

        // 6. Số lượng vắc xin và trung tâm hoạt động
        $vaccinesCount = Vaccine::count();
        $centersCount = Center::active()->count();

        return view('vaccine::admin.dashboard', compact(
            'totalRegistrations',
            'totalRevenue',
            'pendingCount',
            'completedCount',
            'recentRegistrations',
            'vaccinesCount',
            'centersCount'
        ));
    }
}
