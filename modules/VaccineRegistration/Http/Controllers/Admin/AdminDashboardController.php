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

        $fromDateInput = $request->input('from_date');
        $toDateInput = $request->input('to_date');

        $registrationQuery = Registration::query();
        $productQuery = CenterVaccine::query();

        if ($selectedCenterId) {
            $registrationQuery->where('center_id', $selectedCenterId);
            $productQuery->where('center_id', $selectedCenterId);
        }

        $filteredRegistrationQuery = clone $registrationQuery;
        if ($fromDateInput) {
            $filteredRegistrationQuery->whereDate('created_at', '>=', $fromDateInput);
        }
        if ($toDateInput) {
            $filteredRegistrationQuery->whereDate('created_at', '<=', $toDateInput);
        }

        $stats = (clone $filteredRegistrationQuery)->selectRaw("
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
        if ($fromDateInput) {
            $consultLeadQuery->whereDate('created_at', '>=', $fromDateInput);
        }
        if ($toDateInput) {
            $consultLeadQuery->whereDate('created_at', '<=', $toDateInput);
        }
        $consultCount = (int) $consultLeadQuery->whereIn('status', ['pending', 'new'])->count();

        $inventoryQuery = InventoryLot::query();
        if ($selectedCenterId) {
            $inventoryQuery->where('center_id', $selectedCenterId);
        }
        $importedQuantity = (int) $inventoryQuery->sum(DB::raw('available_quantity + reserved_quantity'));

        $soldQuantity = (int) (clone $filteredRegistrationQuery)->where('booking_status', Registration::BOOKING_COMPLETED)->count();

        // R2: Target Date Calculation (Cutoff 20:30) & Vaccine Stock Shortage Analysis
        $now = now();
        $isAfterCutoff = $now->format('H:i') >= '20:30';
        $targetDate = $isAfterCutoff ? $now->copy()->addDay()->toDateString() : $now->toDateString();
        $targetDateLabel = $isAfterCutoff ? 'Ngày mai (' . $now->copy()->addDay()->format('d/m/Y') . ')' : 'Hôm nay (' . $now->format('d/m/Y') . ')';

        $todayInjectionsCount = (int) (clone $registrationQuery)
            ->whereDate('injection_date', now()->toDateString())
            ->count();

        $targetInjectionsQuery = (clone $registrationQuery)
            ->whereDate('injection_date', $targetDate)
            ->where('booking_status', '!=', Registration::BOOKING_CANCELLED);

        $targetInjectionsCount = (int) (clone $targetInjectionsQuery)->count();

        $targetRegistrations = (clone $targetInjectionsQuery)
            ->with(['vaccines', 'center'])
            ->get();

        // Calculate vaccine demands for target date per center & vaccine
        $demands = [];
        foreach ($targetRegistrations as $reg) {
            $cId = $reg->center_id;
            $cName = $reg->center?->name ?? ('Chi nhánh #' . $cId);
            foreach ($reg->vaccines as $vac) {
                $vId = $vac->id;
                $qty = max(1, (int) ($vac->pivot->quantity ?? 1));
                $key = $cId . '_' . $vId;
                if (!isset($demands[$key])) {
                    $demands[$key] = [
                        'center_id' => $cId,
                        'center_name' => $cName,
                        'vaccine_id' => $vId,
                        'vaccine_name' => $vac->name,
                        'disease_prevention' => $vac->disease_prevention,
                        'origin' => $vac->origin,
                        'required_quantity' => 0,
                    ];
                }
                $demands[$key]['required_quantity'] += $qty;
            }
        }

        // CenterVaccine stock quantity (simple warehouse mode)
        $centerVaccinesStock = CenterVaccine::query()
            ->where('is_active', true)
            ->when($selectedCenterId, fn($q) => $q->where('center_id', $selectedCenterId))
            ->get()
            ->keyBy(fn($row) => $row->center_id . '_' . $row->vaccine_id);

        $shortageAlerts = [];
        $sufficientVaccines = [];
        foreach ($demands as $key => $item) {
            $available = (int) ($centerVaccinesStock->get($key)?->stock_quantity ?? 0);

            $item['available_quantity'] = $available;
            $item['shortage_quantity'] = $item['required_quantity'] - $available;
            if ($item['shortage_quantity'] > 0) {
                $shortageAlerts[] = $item;
            } else {
                $sufficientVaccines[] = $item;
            }
        }

        // R3: Trends Data (Custom Date Range / 7-day daily & 6-month monthly)
        if ($fromDateInput && $toDateInput && $fromDateInput <= $toDateInput) {
            $start = \Carbon\Carbon::parse($fromDateInput)->startOfDay();
            $end = \Carbon\Carbon::parse($toDateInput)->endOfDay();
            $daysDiff = $start->diffInDays($end);
            $trendDays = min(60, (int) $daysDiff);

            $dailyGrouped = (clone $registrationQuery)
                ->whereDate('created_at', '>=', $start->toDateString())
                ->whereDate('created_at', '<=', $end->toDateString())
                ->selectRaw("DATE(created_at) as reg_date, COUNT(*) as reg_count, COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_price - points_discount_amount ELSE 0 END), 0) as rev_sum")
                ->groupBy(DB::raw('DATE(created_at)'))
                ->get()
                ->keyBy('reg_date');

            $dailyTrends = [];
            for ($i = 0; $i <= $trendDays; $i++) {
                $d = (clone $start)->addDays($i);
                $dateKey = $d->toDateString();
                $row = $dailyGrouped->get($dateKey);
                $dailyTrends[] = [
                    'date' => $dateKey,
                    'label' => $d->format('d/m'),
                    'revenue' => $row ? (int) $row->rev_sum : 0,
                    'registrations' => $row ? (int) $row->reg_count : 0,
                ];
            }
        } else {
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

        $recentRegistrations = (clone $filteredRegistrationQuery)->latest()->take(8)->get();
        $vaccinesCount = (clone $productQuery)->where('is_active', true)->count();
        $centersCount = $centers->count();
        $fromDate = $fromDateInput;
        $toDate = $toDateInput;

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
            'targetInjectionsCount',
            'targetDate',
            'targetDateLabel',
            'isAfterCutoff',
            'shortageAlerts',
            'sufficientVaccines',
            'dailyTrends',
            'monthlyTrends',
            'centers',
            'selectedCenterId',
            'fromDate',
            'toDate'
        ));
    }
}

