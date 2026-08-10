<?php
/**
 * Chức năng: AdminDashboardController hiển thị trang thống kê tổng quan của Admin.
 * Lý do tạo: Tải động các số liệu thống kê doanh thu, đơn hàng tiêm chủng từ cơ sở dữ liệu MySQL.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\ConsultationLead;
use Modules\VaccineRegistration\Models\InventoryLot;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminDashboardController extends Controller
{
    /**
     * Hiển thị bảng điều khiển thống kê.
     */
    public function index(Request $request)
    {
        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get();
        $selectedCenterId = AdminContext::resolveListCenterId($request);

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

        $totalRegistrations = (int) ($stats->total_registrations ?? 0);
        $totalRevenue = (int) ($stats->total_revenue ?? 0);
        $pendingCount = (int) ($stats->pending_count ?? 0);
        $completedCount = (int) ($stats->completed_count ?? 0);

        // R1: Dynamic Metrics
        $consultLeadQuery = ConsultationLead::query();
        if ($selectedCenterId) {
            $consultLeadQuery->where('center_id', $selectedCenterId);
        }
        $consultCount = (int) $consultLeadQuery->whereIn('status', ['pending', 'new'])->count();

        $inventoryQuery = InventoryLot::query();
        if ($selectedCenterId) {
            $inventoryQuery->where('center_id', $selectedCenterId);
        }
        $importedQuantity = (int) $inventoryQuery->sum(DB::raw('available_quantity + reserved_quantity'));

        $soldQuantity = (int) (clone $registrationQuery)->where('booking_status', Registration::BOOKING_COMPLETED)->count();

        // R2: Today's Injections Widget
        $todayInjectionsCount = (int) (clone $registrationQuery)
            ->whereDate('injection_date', now()->toDateString())
            ->count();

        // R3: Trends Data (7-day daily & 6-month monthly)
        $sevenDaysAgo = now()->subDays(6)->startOfDay();
        $dailyGrouped = (clone $registrationQuery)
            ->where('created_at', '>=', $sevenDaysAgo)
            ->selectRaw("DATE(created_at) as reg_date, COUNT(*) as reg_count, COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_price - points_discount_amount ELSE 0 END), 0) as rev_sum")
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->keyBy('reg_date');

        $dailyTrends = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $dateKey = $d->toDateString();
            $row = $dailyGrouped->get($dateKey);
            $dailyTrends[] = [
                'date' => $dateKey,
                'label' => $d->format('d/m'),
                'revenue' => $row ? (int) $row->rev_sum : 0,
                'registrations' => $row ? (int) $row->reg_count : 0,
            ];
        }

        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();
        $monthlyGrouped = (clone $registrationQuery)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COUNT(*) as reg_count, COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_price - points_discount_amount ELSE 0 END), 0) as rev_sum")
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->get()
            ->keyBy('month_key');

        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $monthKey = $m->format('Y-m');
            $row = $monthlyGrouped->get($monthKey);
            $monthlyTrends[] = [
                'month' => $monthKey,
                'label' => 'Tháng ' . $m->format('m/Y'),
                'short_label' => 'T' . $m->format('m'),
                'revenue' => $row ? (int) $row->rev_sum : 0,
                'registrations' => $row ? (int) $row->reg_count : 0,
            ];
        }

        $recentRegistrations = (clone $registrationQuery)->latest()->take(8)->get();
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
            'todayInjectionsCount',
            'dailyTrends',
            'monthlyTrends',
            'centers',
            'selectedCenterId'
        ));
    }
}

