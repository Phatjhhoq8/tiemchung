@extends('vaccine::layouts.admin')

@section('title', 'Bảng Điều Khiển Quản Trị - Medicare')
@section('page_title', 'Bảng Điều Khiển Quản Trị')

@section('styles')
<style>
    @media (max-width: 639px) {
        .dashboard-grid {
            gap: 12px !important;
            margin-bottom: 24px !important;
        }
        .today-widget-grid {
            grid-template-columns: 1fr !important;
        }
    }
    .today-widget-card {
        background: linear-gradient(135deg, #004b8f 0%, #002d57 100%);
        color: #ffffff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 28px;
        box-shadow: 0 4px 15px rgba(0, 75, 143, 0.15);
        position: relative;
        overflow: hidden;
        border-left: 6px solid #eaaa00;
    }
    .today-widget-badge {
        display: inline-block;
        background-color: #eaaa00;
        color: #0f172a;
        font-weight: 700;
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }
    .today-widget-number {
        font-size: 38px;
        font-weight: 800;
        line-height: 1;
        color: #ffffff;
        margin: 8px 0;
        font-family: var(--font-display);
    }
    .chart-container {
        width: 100%;
        overflow-x: auto;
        background: #ffffff;
    }
    .chart-svg {
        width: 100%;
        height: auto;
        min-width: 600px;
        display: block;
    }
    .btn-quick-range {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        color: #334155;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-quick-range:hover {
        background: #eff6ff;
        border-color: #004b8f;
        color: #004b8f;
    }
    .shortage-scroll-container::-webkit-scrollbar {
        width: 6px;
    }
    .shortage-scroll-container::-webkit-scrollbar-track {
        background: #f8fafc;
        border-radius: 4px;
    }
    .shortage-scroll-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .shortage-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .filter-row-container {
        display: flex;
        gap: 16px;
        align-items: flex-end;
        flex-wrap: wrap;
        width: 100%;
    }
    .filter-item-branch {
        width: 250px;
        flex-shrink: 0;
    }
    .filter-item-date {
        width: 180px;
        flex-shrink: 0;
    }
    .filter-item-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-left: auto;
        flex-shrink: 0;
    }
    @media (max-width: 850px) {
        .filter-item-branch, .filter-item-date {
            width: 100% !important;
        }
        .filter-item-buttons {
            margin-left: 0 !important;
            width: 100% !important;
            margin-top: 8px;
        }
        .filter-item-buttons button, .filter-item-buttons a {
            flex: 1;
            justify-content: center;
        }
    }
</style>
@endsection

@section('admin_content')
<form method="GET" action="{{ route('admin.dashboard') }}" id="admin-dashboard-filter-form" class="card-modern" style="display:flex; flex-direction:column; gap:16px; margin-bottom:24px; padding:20px; background:#ffffff; border-radius:12px; border:1px solid var(--border-color); box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
    <div class="filter-row-container">
        @if($isSuperAdmin ?? false)
        <div class="filter-item-branch">
            <label class="form-label-modern" style="font-size:13px; font-weight:700; color:#004b8f; margin-bottom:6px; display:block;">Chi nhánh</label>
            <select name="center_id" id="filter-center-id" class="form-control-modern" style="width:100%;">
                <option value="" {{ $selectedCenterId === null ? 'selected' : '' }}>Tất cả chi nhánh</option>
                @foreach($centers as $center)
                    <option value="{{ $center->id }}" {{ (string) $selectedCenterId === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                @endforeach
            </select>
        </div>
        @else
        <div class="filter-item-branch">
            <label class="form-label-modern" style="font-size:13px; font-weight:700; color:#004b8f; margin-bottom:6px; display:block;">Chi nhánh hiện tại</label>
            <div style="padding: 10px 14px; background:#f8fafc; border-radius:6px; border:1px solid #e2e8f0; font-weight:700; color:#0f172a; font-size:14px; width:100%; box-sizing:border-box;">
                {{ $adminUser?->center?->name ?? 'Chi nhánh của bạn' }}
            </div>
        </div>
        @endif

        <div class="filter-item-date">
            <label class="form-label-modern" style="font-size:13px; font-weight:700; color:#004b8f; margin-bottom:6px; display:block;">Từ ngày</label>
            <input type="date" name="from_date" id="filter-from-date" value="{{ $fromDate ?? '' }}" class="form-control-modern" style="width:100%;">
        </div>

        <div class="filter-item-date">
            <label class="form-label-modern" style="font-size:13px; font-weight:700; color:#004b8f; margin-bottom:6px; display:block;">Đến ngày</label>
            <input type="date" name="to_date" id="filter-to-date" value="{{ $toDate ?? '' }}" class="form-control-modern" style="width:100%;">
        </div>

        <div class="filter-item-buttons">
            <button type="submit" class="btn-modern btn-modern-primary" style="padding:10px 20px; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                <i data-lucide="filter" style="width:15px; height:15px;"></i> Lọc thống kê
            </button>
            @if(!empty($fromDate) || !empty($toDate) || !empty($selectedCenterId))
                <a href="{{ route('admin.dashboard') }}" class="btn-modern" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; text-decoration:none; padding:10px 16px; font-weight:600; display:inline-flex; align-items:center; gap:6px; border-radius:8px;">
                    Đặt lại
                </a>
            @endif
        </div>
    </div>

    <!-- Quick Date Range Presets -->
    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; padding-top:12px; border-top:1px dashed #e2e8f0; font-size:12.5px;">
        <span style="color:#64748b; font-weight:600;">Chọn nhanh mốc thời gian:</span>
        <button type="button" onclick="setQuickDateRange('today')" class="btn-quick-range">Hôm nay</button>
        <button type="button" onclick="setQuickDateRange('7days')" class="btn-quick-range">7 ngày qua</button>
        <button type="button" onclick="setQuickDateRange('30days')" class="btn-quick-range">30 ngày qua</button>
        <button type="button" onclick="setQuickDateRange('this_month')" class="btn-quick-range">Tháng này</button>
        <button type="button" onclick="setQuickDateRange('last_month')" class="btn-quick-range">Tháng trước</button>
    </div>
</form>

<!-- R2 Widget: Lịch hẹn tiêm & Cảnh báo tồn kho vắc xin theo chi nhánh (Cutoff 20:30 tối) -->
<div class="today-widget-card" style="margin-bottom: 28px; background: linear-gradient(135deg, #00386c 0%, #004b8f 100%); border-radius: 14px; padding: 22px 24px; color: #ffffff; box-shadow: 0 8px 24px rgba(0, 75, 143, 0.18); border: 1px solid rgba(255,255,255,0.1);">
    <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 20px; margin-bottom: {{ !empty($shortageAlerts) && count($shortageAlerts) > 0 ? '18px' : '0' }};">
        <div style="flex: 1 1 500px;">
            <div style="margin-bottom: 8px;">
                @if($isAfterCutoff ?? false)
                    <span class="today-widget-badge" style="background-color: #eaaa00; color: #002b53; font-weight: 800; padding: 4px 12px; border-radius: 20px; font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block;">
                        Rà Soát Tồn Kho Ngày Mai (Sau 20:30)
                    </span>
                @else
                    <span class="today-widget-badge" style="background-color: #eaaa00; color: #002b53; font-weight: 800; padding: 4px 12px; border-radius: 20px; font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block;">
                        Lịch Tiêm & Tồn Kho Trong Ngày
                    </span>
                @endif
            </div>

            <h2 style="font-size: 21px; font-weight: 700; color: #ffffff; margin: 0 0 6px 0; font-family: var(--font-display);">
                Lịch Tiêm & Đối Soát Kho: {{ $targetDateLabel ?? 'Hôm nay' }}
            </h2>

            <p style="margin: 0; color: #cbd5e1; font-size: 13.5px; line-height: 1.45;">
                @if($isAfterCutoff ?? false)
                    Tự động kiểm tra trước nhu cầu vắc xin cho ca tiêm sáng mai để chi nhánh kịp điều chuyển kho.
                @else
                    Rà soát vắc xin khả dụng phục vụ các ca tiêm (hệ thống tự động chuyển sang kiểm tra ngày mai sau 20h30).
                @endif
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
            <div style="text-align: right;">
                <div class="today-widget-number" style="font-size: 34px; font-weight: 800; color: #ffffff; line-height: 1;">
                    {{ $targetInjectionsCount ?? $todayInjectionsCount }}
                </div>
                <span style="font-size: 13px; color: #eaaa00; font-weight: 700;">Ca hẹn tiêm chủng</span>
            </div>
            <a href="{{ route('admin.registrations.index', ['injection_date' => $targetDate ?? date('Y-m-d')]) }}" class="btn-modern" style="background-color: #c8102e; color: #ffffff; font-weight: 700; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-size: 13.5px; white-space: nowrap; box-shadow: 0 4px 12px rgba(200, 16, 46, 0.35);">
                Xem danh sách tiêm
            </a>
        </div>
    </div>

    <!-- Khối Cảnh Báo Thiếu Vắc Xin (Stock Shortage Alerts) -->
    @if(!empty($shortageAlerts) && count($shortageAlerts) > 0)
        <div style="background: #ffffff; border-radius: 10px; padding: 18px 20px; color: #0f172a; border-left: 5px solid #c8102e; box-shadow: 0 6px 18px rgba(0,0,0,0.12);">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid #fee2e2;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; background: #fee2e2; color: #c8102e; border-radius: 50%; font-weight: 800; font-size: 12px;">!</span>
                    <strong style="color: #c8102e; font-size: 14px; font-family: var(--font-display);">
                        CẢNH BÁO THIẾU VẮC XIN: Cần bổ sung {{ count($shortageAlerts) }} loại vắc xin cho lịch tiêm
                    </strong>
                </div>
                @php
                    $targetCenterParam = $selectedCenterId ?? ($shortageAlerts[0]['center_id'] ?? null);
                @endphp
                <a href="{{ route('admin.vaccines.index', array_filter(['center_id' => $targetCenterParam])) }}" class="btn-modern" style="background-color: #c8102e; color: #ffffff; font-size: 12.5px; font-weight: 700; text-decoration: none; padding: 6px 14px; border-radius: 6px;">
                    Quản lý vắc xin &rarr;
                </a>
            </div>

            <div class="shortage-scroll-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px; max-height: 380px; overflow-y: auto; padding-right: 6px;">
                @foreach($shortageAlerts as $alert)
                    <div style="background: #fff5f5; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 14px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px; gap: 8px;">
                            <a href="{{ route('admin.vaccines.index', ['center_id' => $alert['center_id'], 'search' => $alert['vaccine_name']]) }}" style="color: #991b1b; font-size: 13.5px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="Xem tại kho chi nhánh {{ $alert['center_name'] }}">
                                {{ $alert['vaccine_name'] }}
                                <i data-lucide="arrow-up-right" style="width: 14px; height: 14px; stroke-width: 2.5;"></i>
                            </a>
                            <span style="background: #ef4444; color: #ffffff; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700; white-space: nowrap;">
                                Thiếu -{{ $alert['shortage_quantity'] }} liều
                            </span>
                        </div>
                        <div style="font-size: 12px; color: #4b5563; margin-bottom: 6px;">
                            Chi nhánh: <strong style="color: #0f172a;">{{ $alert['center_name'] }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 12px; color: #374151; padding-top: 6px; border-top: 1px dashed #fecaca;">
                            <span>Cần tiêm: <strong style="color: #004b8f;">{{ $alert['required_quantity'] }} liều</strong></span>
                            <span>Tồn khả dụng: <strong style="color: #d97706;">{{ $alert['available_quantity'] }} liều</strong></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif(!empty($demands) && count($demands) > 0)
        <div style="background: rgba(255,255,255,0.1); border-radius: 8px; padding: 10px 16px; margin-top: 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; font-size: 13px;">
            <div style="display: flex; align-items: center; gap: 8px; color: #e2e8f0;">
                <span style="display: inline-block; width: 8px; height: 8px; background: #22c55e; border-radius: 50%;"></span>
                <span>Tồn kho an toàn: Toàn bộ <strong>{{ count($demands) }} loại vắc xin</strong> cho ca tiêm đều có sẵn đủ liều trong kho.</span>
            </div>
            <a href="{{ route('admin.vaccines.index', array_filter(['center_id' => $selectedCenterId])) }}" style="color: #eaaa00; font-weight: 700; text-decoration: none; font-size: 12.5px;">
                Quản lý vắc xin &rarr;
            </a>
        </div>
    @endif
</div>

<!-- Thẻ thống kê Widgets Grid (R1 Dynamic Metrics Included) -->
<div class="dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 32px;">
    <!-- Widget 1: Tổng đăng ký -->
    <div class="stat-card-modern">
        <div>
            <span class="stat-card-title">Tổng Đăng Ký</span>
            <strong class="stat-card-number">{{ $totalRegistrations }}</strong>
        </div>
        <div class="stat-card-icon-wrapper" style="background-color: #eff6ff; color: #004b8f;">
            <i data-lucide="clipboard-list"></i>
        </div>
    </div>

    <!-- Widget 2: Doanh thu -->
    <div class="stat-card-modern">
        <div>
            <span class="stat-card-title">Doanh Thu Đã Thanh Toán</span>
            <strong class="stat-card-number" style="color: #c8102e; font-size: 22px;">{{ number_format($totalRevenue, 0, ',', '.') }} đ</strong>
        </div>
        <div class="stat-card-icon-wrapper" style="background-color: #fff1f2; color: #c8102e;">
            <i data-lucide="dollar-sign"></i>
        </div>
    </div>

    <!-- Widget 3: Chờ thanh toán -->
    <div class="stat-card-modern">
        <div>
            <span class="stat-card-title">Chờ Thanh Toán</span>
            <strong class="stat-card-number" style="color: #d97706;">{{ $pendingCount }}</strong>
        </div>
        <div class="stat-card-icon-wrapper" style="background-color: #fffbeb; color: #eaaa00;">
            <i data-lucide="clock"></i>
        </div>
    </div>

    <!-- Widget 4: Đã hoàn tất -->
    <div class="stat-card-modern">
        <div>
            <span class="stat-card-title">Đã Hoàn Tất</span>
            <strong class="stat-card-number" style="color: #004b8f;">{{ $completedCount }}</strong>
        </div>
        <div class="stat-card-icon-wrapper" style="background-color: #f0f9ff; color: #004b8f;">
            <i data-lucide="shield-check"></i>
        </div>
    </div>

    <!-- Widget 5: Vắc xin đã tiêm / đã bán -->
    <div class="stat-card-modern">
        <div>
            <span class="stat-card-title">Vắc Xin Đã Tiêm</span>
            <strong class="stat-card-number" style="color: #c8102e;">{{ number_format($soldQuantity, 0, ',', '.') }}</strong>
            <span style="display:block; font-size:11px; color:#64748b;">Đơn hoàn thành tiêm</span>
        </div>
        <div class="stat-card-icon-wrapper" style="background-color: #fef2f2; color: #c8102e;">
            <i data-lucide="check-circle"></i>
        </div>
    </div>
</div>

<!-- R3 SVG Chart: Revenue & Registration Trends -->
@php
    // Calculations for 7-day / Custom Date Range chart
    $total7DaysRev = array_sum(array_column($dailyTrends, 'revenue'));
    $total7DaysReg = array_sum(array_column($dailyTrends, 'registrations'));
    $maxDailyRev = max(100000, max(array_column($dailyTrends, 'revenue')));
    $maxDailyReg = max(5, max(array_column($dailyTrends, 'registrations')));
    $dailyCount = count($dailyTrends);

    $dailyRevPoints = [];
    $dailyRegPoints = [];
    $dailyNodes = [];

    foreach ($dailyTrends as $idx => $item) {
        if ($dailyCount === 1) {
            $x = 375; // Center point perfectly
        } else {
            $x = 75 + ($idx * (600 / ($dailyCount - 1)));
        }
        $yRev = 220 - (($item['revenue'] / $maxDailyRev) * 160);
        $yReg = 220 - (($item['registrations'] / $maxDailyReg) * 160);

        $dailyRevPoints[] = round($x, 1) . "," . round($yRev, 1);
        $dailyRegPoints[] = round($x, 1) . "," . round($yReg, 1);
        $dailyNodes[] = [
            'x' => round($x, 1),
            'yRev' => $yRev,
            'yReg' => $yReg,
            'item' => $item,
        ];
    }

    if ($dailyCount === 1) {
        $singleRevY = round($dailyNodes[0]['yRev'], 1);
        $singleRegY = round($dailyNodes[0]['yReg'], 1);
        $dailyRevPolyline = "270,$singleRevY 480,$singleRevY";
        $dailyRegPolyline = "270,$singleRegY 480,$singleRegY";
        $dailyRevAreaD = "M 315,220 L 315,$singleRevY L 435,$singleRevY L 435,220 Z";
        $dailyRegAreaD = "M 330,220 L 330,$singleRegY L 420,$singleRegY L 420,220 Z";
    } else {
        $dailyRevPolyline = implode(' ', $dailyRevPoints);
        $dailyRegPolyline = implode(' ', $dailyRegPoints);
        $dailyRevAreaD = "M 75,220 L " . implode(' L ', $dailyRevPoints) . " L 675,220 Z";
        $dailyRegAreaD = "M 75,220 L " . implode(' L ', $dailyRegPoints) . " L 675,220 Z";
    }

    // Calculations for 6-month chart
    $total6MonthsRev = array_sum(array_column($monthlyTrends, 'revenue'));
    $total6MonthsReg = array_sum(array_column($monthlyTrends, 'registrations'));
    $maxMonthlyRev = max(100000, max(array_column($monthlyTrends, 'revenue')));
    $maxMonthlyReg = max(5, max(array_column($monthlyTrends, 'registrations')));
    $monthlyCount = count($monthlyTrends);

    $monthlyRevPoints = [];
    $monthlyRegPoints = [];
    $monthlyNodes = [];

    foreach ($monthlyTrends as $idx => $item) {
        if ($monthlyCount === 1) {
            $x = 375;
        } else {
            $x = 75 + ($idx * (600 / ($monthlyCount - 1)));
        }
        $yRev = 220 - (($item['revenue'] / $maxMonthlyRev) * 160);
        $yReg = 220 - (($item['registrations'] / $maxMonthlyReg) * 160);

        $monthlyRevPoints[] = round($x, 1) . "," . round($yRev, 1);
        $monthlyRegPoints[] = round($x, 1) . "," . round($yReg, 1);
        $monthlyNodes[] = [
            'x' => round($x, 1),
            'yRev' => $yRev,
            'yReg' => $yReg,
            'item' => $item,
        ];
    }

    if ($monthlyCount === 1) {
        $singleMRevY = round($monthlyNodes[0]['yRev'], 1);
        $singleMRegY = round($monthlyNodes[0]['yReg'], 1);
        $monthlyRevPolyline = "270,$singleMRevY 480,$singleMRevY";
        $monthlyRegPolyline = "270,$singleMRegY 480,$singleMRegY";
        $monthlyRevAreaD = "M 315,220 L 315,$singleMRevY L 435,$singleMRevY L 435,220 Z";
        $monthlyRegAreaD = "M 330,220 L 330,$singleMRegY L 420,$singleMRegY L 420,220 Z";
    } else {
        $monthlyRevPolyline = implode(' ', $monthlyRevPoints);
        $monthlyRegPolyline = implode(' ', $monthlyRegPoints);
        $monthlyRevAreaD = "M 75,220 L " . implode(' L ', $monthlyRevPoints) . " L 675,220 Z";
        $monthlyRegAreaD = "M 75,220 L " . implode(' L ', $monthlyRegPoints) . " L 675,220 Z";
    }
@endphp

<div class="card-modern" id="dashboard-chart-wrapper" style="position: relative; margin-bottom: 32px; padding: 24px; background: #ffffff; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 4px 16px rgba(0,0,0,0.02);">
    <!-- Chart Header & Tab Toggles -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 18px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color);">
        <div>
            <h2 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; color: #004b8f; margin: 0 0 4px 0;">Biểu Đồ Xu Hướng Doanh Thu & Đăng Ký</h2>
            <span style="font-size: 13px; color: #64748b;">
                @if(!empty($fromDate) && !empty($toDate))
                    Xu hướng thống kê từ ngày {{ date('d/m/Y', strtotime($fromDate)) }} đến ngày {{ date('d/m/Y', strtotime($toDate)) }}
                @else
                    Đường Doanh thu thực nhận (VND) và Đường Số lượt đăng ký tiêm chủng
                @endif
            </span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <button type="button" id="tab-7days-btn" onclick="switchTrendTab('7days')" class="btn-modern" style="background-color: #c8102e; color: #ffffff; padding: 7px 18px; font-size: 13px; font-weight: 700; border-radius: 8px; border: none; cursor: pointer; transition: all 0.2s;">
                @if(!empty($fromDate) && !empty($toDate))
                    {{ date('d/m', strtotime($fromDate)) }} - {{ date('d/m', strtotime($toDate)) }}
                @else
                    7 Ngày Gần Đây
                @endif
            </button>
            <button type="button" id="tab-6months-btn" onclick="switchTrendTab('6months')" class="btn-modern" style="background-color: #f1f5f9; color: #334155; padding: 7px 18px; font-size: 13px; font-weight: 700; border-radius: 8px; border: 1px solid #cbd5e1; cursor: pointer; transition: all 0.2s;">6 Tháng Gần Đây</button>
        </div>
    </div>

    <!-- Summary Metrics & Legend Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 20px; padding: 12px 16px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
        <!-- Dynamic Summary Totals -->
        <div id="summary-7days-stats" style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
            <div style="font-size: 13px; color: #475569;">
                @if(!empty($fromDate) && !empty($toDate))
                    Tổng doanh thu kỳ lọc:
                @else
                    Tổng doanh thu 7 ngày:
                @endif
                <strong style="color: #c8102e; font-size: 15px;">{{ number_format($total7DaysRev, 0, ',', '.') }} đ</strong>
            </div>
            <div style="height: 14px; width: 1px; background: #cbd5e1;"></div>
            <div style="font-size: 13px; color: #475569;">
                Tổng lượt đăng ký: <strong style="color: #004b8f; font-size: 15px;">{{ $total7DaysReg }} ca</strong>
            </div>
        </div>
        <div id="summary-6months-stats" style="display: none; align-items: center; gap: 20px; flex-wrap: wrap;">
            <div style="font-size: 13px; color: #475569;">
                Tổng doanh thu 6 tháng: <strong style="color: #c8102e; font-size: 15px;">{{ number_format($total6MonthsRev, 0, ',', '.') }} đ</strong>
            </div>
            <div style="height: 14px; width: 1px; background: #cbd5e1;"></div>
            <div style="font-size: 13px; color: #475569;">
                Tổng lượt đăng ký: <strong style="color: #004b8f; font-size: 15px;">{{ $total6MonthsReg }} ca</strong>
            </div>
        </div>

        <!-- Legend Items -->
        <div style="display: flex; align-items: center; gap: 20px; font-size: 12.5px; font-weight: 600; color: #334155;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 18px; height: 3.5px; background-color: #c8102e; border-radius: 2px;"></span>
                <span style="display: inline-block; width: 8px; height: 8px; background-color: #ffffff; border: 2.5px solid #c8102e; border-radius: 50%; margin-left: -13px;"></span>
                <span>Doanh thu đã thanh toán (Đường Đỏ)</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 18px; height: 3px; background-color: #004b8f; border-radius: 2px;"></span>
                <span style="display: inline-block; width: 8px; height: 8px; background-color: #eaaa00; border: 2px solid #004b8f; border-radius: 50%; margin-left: -13px;"></span>
                <span>Lượt đăng ký tiêm (Đường Xanh)</span>
            </div>
        </div>
    </div>

    <!-- Floating Interactive Tooltip -->
    <div id="dashboard-chart-tooltip" style="position: absolute; pointer-events: none; opacity: 0; transform: translateY(6px); transition: opacity 0.15s ease, transform 0.15s ease; background: #0f172a; color: #ffffff; padding: 10px 14px; border-radius: 8px; font-size: 12.5px; box-shadow: 0 12px 30px rgba(0,0,0,0.3); z-index: 50; min-width: 175px; border: 1px solid #334155;">
        <div id="tooltip-title" style="font-weight: 700; color: #94a3b8; margin-bottom: 6px; padding-bottom: 4px; border-bottom: 1px solid #334155; font-size: 12px;"></div>
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 3px;">
            <span style="display: flex; align-items: center; gap: 6px; color: #e2e8f0;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #c8102e; display: inline-block;"></span>Doanh thu:</span>
            <strong id="tooltip-revenue" style="color: #f87171; font-size: 13px;">0 đ</strong>
        </div>
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
            <span style="display: flex; align-items: center; gap: 6px; color: #e2e8f0;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #38bdf8; display: inline-block;"></span>Lượt tiêm:</span>
            <strong id="tooltip-registrations" style="color: #38bdf8; font-size: 13px;">0 ca</strong>
        </div>
    </div>

    <!-- 7 Days Chart View (Dual Line with Area Glow) -->
    <div id="chart-7days-view" class="chart-container" style="overflow-x: auto;">
        <svg viewBox="0 0 740 280" class="chart-svg" style="width: 100%; height: auto; min-width: 600px;">
            <defs>
                <linearGradient id="gradDailyRev" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#c8102e" stop-opacity="0.18" />
                    <stop offset="100%" stop-color="#c8102e" stop-opacity="0.0" />
                </linearGradient>
                <linearGradient id="gradDailyReg" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#004b8f" stop-opacity="0.12" />
                    <stop offset="100%" stop-color="#004b8f" stop-opacity="0.0" />
                </linearGradient>
            </defs>

            <!-- Background Grid & Y-Axis Lines -->
            <line x1="75" y1="60" x2="675" y2="60" stroke="#f1f5f9" stroke-width="1" />
            <line x1="75" y1="113" x2="675" y2="113" stroke="#f1f5f9" stroke-width="1" />
            <line x1="75" y1="167" x2="675" y2="167" stroke="#f1f5f9" stroke-width="1" />
            <line x1="75" y1="220" x2="675" y2="220" stroke="#cbd5e1" stroke-width="1.5" />

            <!-- Left Y-Axis Labels (Revenue - VND) -->
            <text x="67" y="64" text-anchor="end" font-size="10.5" fill="#c8102e" font-weight="700">{{ number_format($maxDailyRev / 1000, 0) }}k</text>
            <text x="67" y="117" text-anchor="end" font-size="10.5" fill="#64748b" font-weight="600">{{ number_format(($maxDailyRev * 0.66) / 1000, 0) }}k</text>
            <text x="67" y="171" text-anchor="end" font-size="10.5" fill="#64748b" font-weight="600">{{ number_format(($maxDailyRev * 0.33) / 1000, 0) }}k</text>
            <text x="67" y="224" text-anchor="end" font-size="10.5" fill="#64748b" font-weight="600">0đ</text>

            <!-- Right Y-Axis Labels (Registrations) -->
            <text x="683" y="64" text-anchor="start" font-size="10.5" fill="#004b8f" font-weight="700">{{ $maxDailyReg }}</text>
            <text x="683" y="117" text-anchor="start" font-size="10.5" fill="#004b8f" font-weight="700">{{ round($maxDailyReg * 0.66) }}</text>
            <text x="683" y="171" text-anchor="start" font-size="10.5" fill="#004b8f" font-weight="700">{{ round($maxDailyReg * 0.33) }}</text>
            <text x="683" y="224" text-anchor="start" font-size="10.5" fill="#004b8f" font-weight="700">0</text>

            <!-- 1. Revenue Area Fill & Polyline (Đường Doanh Thu Đỏ) -->
            <path d="{{ $dailyRevAreaD }}" fill="url(#gradDailyRev)" />
            <polyline points="{{ $dailyRevPolyline }}" fill="none" stroke="#c8102e" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />

            <!-- 2. Registration Line Area & Polyline (Đường Lượt Đăng Ký Xanh) -->
            <path d="{{ $dailyRegAreaD }}" fill="url(#gradDailyReg)" />
            <polyline points="{{ $dailyRegPolyline }}" fill="none" stroke="#004b8f" stroke-width="2.5" stroke-dasharray="5,4" stroke-linecap="round" stroke-linejoin="round" />

            <!-- 3. Nodes & X-Axis Labels -->
            @foreach($dailyNodes as $n)
                <!-- X-Axis Date Label -->
                <text x="{{ $n['x'] }}" y="246" text-anchor="middle" font-size="11.5" fill="#475569" font-weight="600">{{ $n['item']['label'] }}</text>

                <!-- Revenue Node (Red) -->
                <circle cx="{{ $n['x'] }}" cy="{{ round($n['yRev'], 1) }}" r="5.5" fill="#ffffff" stroke="#c8102e" stroke-width="2.5" />
                @if($n['item']['revenue'] > 0)
                    <text x="{{ $n['x'] }}" y="{{ round($n['yRev'] - 11, 1) }}" text-anchor="middle" font-size="10.5" fill="#c8102e" font-weight="700">{{ number_format($n['item']['revenue'] / 1000, 0) }}k</text>
                @endif

                <!-- Registration Node (Navy & Gold) -->
                <circle cx="{{ $n['x'] }}" cy="{{ round($n['yReg'], 1) }}" r="4.5" fill="#eaaa00" stroke="#004b8f" stroke-width="2" />
                @if($n['item']['registrations'] > 0)
                    <text x="{{ $n['x'] }}" y="{{ round($n['yReg'] + 17, 1) }}" text-anchor="middle" font-size="10.5" fill="#004b8f" font-weight="700">{{ $n['item']['registrations'] }}</text>
                @endif

                <!-- Interactive Slice for Hover Tooltip -->
                <rect x="{{ $n['x'] - 40 }}" y="40" width="80" height="200" fill="transparent" class="interactive-slice" style="cursor: pointer;"
                    onmouseenter="showChartTooltip(event, 'Ngày {{ $n['item']['label'] }}', {{ $n['item']['revenue'] }}, {{ $n['item']['registrations'] }})"
                    onmousemove="moveChartTooltip(event)"
                    onmouseleave="hideChartTooltip()" />
            @endforeach
        </svg>
    </div>

    <!-- 6 Months Chart View (Dual Line with Area Glow) -->
    <div id="chart-6months-view" class="chart-container" style="display: none; overflow-x: auto;">
        <svg viewBox="0 0 740 280" class="chart-svg" style="width: 100%; height: auto; min-width: 600px;">
            <defs>
                <linearGradient id="gradMonthlyRev" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#c8102e" stop-opacity="0.18" />
                    <stop offset="100%" stop-color="#c8102e" stop-opacity="0.0" />
                </linearGradient>
                <linearGradient id="gradMonthlyReg" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#004b8f" stop-opacity="0.12" />
                    <stop offset="100%" stop-color="#004b8f" stop-opacity="0.0" />
                </linearGradient>
            </defs>

            <!-- Background Grid & Y-Axis Lines -->
            <line x1="75" y1="60" x2="675" y2="60" stroke="#f1f5f9" stroke-width="1" />
            <line x1="75" y1="113" x2="675" y2="113" stroke="#f1f5f9" stroke-width="1" />
            <line x1="75" y1="167" x2="675" y2="167" stroke="#f1f5f9" stroke-width="1" />
            <line x1="75" y1="220" x2="675" y2="220" stroke="#cbd5e1" stroke-width="1.5" />

            <!-- Left Y-Axis Labels (Revenue - VND) -->
            <text x="67" y="64" text-anchor="end" font-size="10.5" fill="#c8102e" font-weight="700">{{ number_format($maxMonthlyRev / 1000, 0) }}k</text>
            <text x="67" y="117" text-anchor="end" font-size="10.5" fill="#64748b" font-weight="600">{{ number_format(($maxMonthlyRev * 0.66) / 1000, 0) }}k</text>
            <text x="67" y="171" text-anchor="end" font-size="10.5" fill="#64748b" font-weight="600">{{ number_format(($maxMonthlyRev * 0.33) / 1000, 0) }}k</text>
            <text x="67" y="224" text-anchor="end" font-size="10.5" fill="#64748b" font-weight="600">0đ</text>

            <!-- Right Y-Axis Labels (Registrations) -->
            <text x="683" y="64" text-anchor="start" font-size="10.5" fill="#004b8f" font-weight="700">{{ $maxMonthlyReg }}</text>
            <text x="683" y="117" text-anchor="start" font-size="10.5" fill="#004b8f" font-weight="700">{{ round($maxMonthlyReg * 0.66) }}</text>
            <text x="683" y="171" text-anchor="start" font-size="10.5" fill="#004b8f" font-weight="700">{{ round($maxMonthlyReg * 0.33) }}</text>
            <text x="683" y="224" text-anchor="start" font-size="10.5" fill="#004b8f" font-weight="700">0</text>

            <!-- 1. Revenue Area Fill & Polyline (Đường Doanh Thu Đỏ) -->
            <path d="{{ $monthlyRevAreaD }}" fill="url(#gradMonthlyRev)" />
            <polyline points="{{ $monthlyRevPolyline }}" fill="none" stroke="#c8102e" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />

            <!-- 2. Registration Line Area & Polyline (Đường Lượt Đăng Ký Xanh) -->
            <path d="{{ $monthlyRegAreaD }}" fill="url(#gradMonthlyReg)" />
            <polyline points="{{ $monthlyRegPolyline }}" fill="none" stroke="#004b8f" stroke-width="2.5" stroke-dasharray="5,4" stroke-linecap="round" stroke-linejoin="round" />

            <!-- 3. Nodes & X-Axis Labels -->
            @foreach($monthlyNodes as $n)
                <!-- X-Axis Month Label -->
                <text x="{{ $n['x'] }}" y="246" text-anchor="middle" font-size="11.5" fill="#475569" font-weight="600">{{ $n['item']['label'] }}</text>

                <!-- Revenue Node (Red) -->
                <circle cx="{{ $n['x'] }}" cy="{{ round($n['yRev'], 1) }}" r="5.5" fill="#ffffff" stroke="#c8102e" stroke-width="2.5" />
                @if($n['item']['revenue'] > 0)
                    <text x="{{ $n['x'] }}" y="{{ round($n['yRev'] - 11, 1) }}" text-anchor="middle" font-size="10.5" fill="#c8102e" font-weight="700">{{ number_format($n['item']['revenue'] / 1000, 0) }}k</text>
                @endif

                <!-- Registration Node (Navy & Gold) -->
                <circle cx="{{ $n['x'] }}" cy="{{ round($n['yReg'], 1) }}" r="4.5" fill="#eaaa00" stroke="#004b8f" stroke-width="2" />
                @if($n['item']['registrations'] > 0)
                    <text x="{{ $n['x'] }}" y="{{ round($n['yReg'] + 17, 1) }}" text-anchor="middle" font-size="10.5" fill="#004b8f" font-weight="700">{{ $n['item']['registrations'] }}</text>
                @endif

                <!-- Interactive Slice for Hover Tooltip -->
                <rect x="{{ $n['x'] - 45 }}" y="40" width="90" height="200" fill="transparent" class="interactive-slice" style="cursor: pointer;"
                    onmouseenter="showChartTooltip(event, '{{ $n['item']['label'] }}', {{ $n['item']['revenue'] }}, {{ $n['item']['registrations'] }})"
                    onmousemove="moveChartTooltip(event)"
                    onmouseleave="hideChartTooltip()" />
            @endforeach
        </svg>
    </div>
</div>

<script>
function switchTrendTab(tab) {
    var tab7 = document.getElementById('chart-7days-view');
    var tab6 = document.getElementById('chart-6months-view');
    var sum7 = document.getElementById('summary-7days-stats');
    var sum6 = document.getElementById('summary-6months-stats');
    var btn7 = document.getElementById('tab-7days-btn');
    var btn6 = document.getElementById('tab-6months-btn');
    if (!tab7 || !tab6 || !btn7 || !btn6) return;
    
    if (tab === '7days') {
        tab7.style.display = 'block';
        tab6.style.display = 'none';
        if (sum7) sum7.style.display = 'flex';
        if (sum6) sum6.style.display = 'none';
        btn7.style.backgroundColor = '#c8102e';
        btn7.style.color = '#ffffff';
        btn7.style.border = 'none';
        btn6.style.backgroundColor = '#f1f5f9';
        btn6.style.color = '#334155';
        btn6.style.border = '1px solid #cbd5e1';
    } else {
        tab7.style.display = 'none';
        tab6.style.display = 'block';
        if (sum7) sum7.style.display = 'none';
        if (sum6) sum6.style.display = 'flex';
        btn6.style.backgroundColor = '#c8102e';
        btn6.style.color = '#ffffff';
        btn6.style.border = 'none';
        btn7.style.backgroundColor = '#f1f5f9';
        btn7.style.color = '#334155';
        btn7.style.border = '1px solid #cbd5e1';
    }
}

function showChartTooltip(event, label, revenue, regCount) {
    var tooltip = document.getElementById('dashboard-chart-tooltip');
    var titleEl = document.getElementById('tooltip-title');
    var revEl = document.getElementById('tooltip-revenue');
    var regEl = document.getElementById('tooltip-registrations');
    if (!tooltip || !titleEl || !revEl || !regEl) return;
    
    titleEl.textContent = label;
    revEl.textContent = new Intl.NumberFormat('vi-VN').format(revenue) + ' đ';
    regEl.textContent = regCount + ' ca tiêm';
    
    tooltip.style.opacity = '1';
    tooltip.style.transform = 'translateY(0)';
    moveChartTooltip(event);
}

function moveChartTooltip(event) {
    var tooltip = document.getElementById('dashboard-chart-tooltip');
    var container = document.getElementById('dashboard-chart-wrapper');
    if (!tooltip || !container) return;
    
    var rect = container.getBoundingClientRect();
    var x = event.clientX - rect.left + 15;
    var y = event.clientY - rect.top - 65;
    
    if (x + 200 > rect.width) {
        x = event.clientX - rect.left - 195;
    }
    if (y < 10) y = 10;
    
    tooltip.style.left = x + 'px';
    tooltip.style.top = y + 'px';
}

function hideChartTooltip() {
    var tooltip = document.getElementById('dashboard-chart-tooltip');
    if (tooltip) {
        tooltip.style.opacity = '0';
        tooltip.style.transform = 'translateY(6px)';
    }
}

function setQuickDateRange(range) {
    var fromInput = document.getElementById('filter-from-date');
    var toInput = document.getElementById('filter-to-date');
    var form = document.getElementById('admin-dashboard-filter-form');
    if (!fromInput || !toInput || !form) return;

    var today = new Date();
    var formatDate = function(d) {
        var year = d.getFullYear();
        var month = String(d.getMonth() + 1).padStart(2, '0');
        var day = String(d.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    };

    if (range === 'today') {
        fromInput.value = formatDate(today);
        toInput.value = formatDate(today);
    } else if (range === '7days') {
        var from7 = new Date();
        from7.setDate(today.getDate() - 6);
        fromInput.value = formatDate(from7);
        toInput.value = formatDate(today);
    } else if (range === '30days') {
        var from30 = new Date();
        from30.setDate(today.getDate() - 29);
        fromInput.value = formatDate(from30);
        toInput.value = formatDate(today);
    } else if (range === 'this_month') {
        var fromMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        fromInput.value = formatDate(fromMonth);
        toInput.value = formatDate(today);
    } else if (range === 'last_month') {
        var fromLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        var toLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
        fromInput.value = formatDate(fromLastMonth);
        toInput.value = formatDate(toLastMonth);
    }
    form.submit();
}
</script>

<!-- Khung phụ thống kê nhanh & Quản trị nhanh -->
<div class="dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 40px;">
    @if($isSuperAdmin ?? false)
    <div class="stat-card-modern">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="background-color: #fff1f2; color: #e11d48; padding: 12px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="syringe"></i>
            </div>
            <div>
                <span style="display: block; color: var(--text-muted); font-size: 14px; font-weight: 500; font-family: var(--font-display);">Danh mục Vắc Xin</span>
                <strong style="font-size: 20px; color: var(--text-primary); font-family: var(--font-display);">{{ $vaccinesCount }} loại</strong>
            </div>
        </div>
        <a href="{{ route('admin.vaccines.index', ['featured' => 1]) }}" class="btn-action-sm">
            <i data-lucide="star" style="width: 14px; height: 14px;"></i> Nổi Bật
        </a>
    </div>

    <div class="stat-card-modern">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="background-color: #fee2e2; color: #dc2626; padding: 12px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="image"></i>
            </div>
            <div>
                <span style="display: block; color: var(--text-muted); font-size: 14px; font-weight: 500; font-family: var(--font-display);">Banner Trang Chủ</span>
                <strong style="font-size: 20px; color: var(--text-primary); font-family: var(--font-display);">Trình chiếu đầu trang</strong>
            </div>
        </div>
        <a href="{{ route('admin.banners.index') }}" class="btn-action-sm">
            <i data-lucide="image" style="width: 14px; height: 14px;"></i> Banner
        </a>
    </div>
    @endif
</div>

<!-- Danh sách đăng ký gần đây -->
<div class="card-modern">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
        <h2 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0;">Đơn Đăng Ký Tiêm Mới Nhất</h2>
        <a href="{{ route('admin.registrations.index') }}" style="color: var(--primary-color); font-weight: 600; text-decoration: none; font-size: 14px; display: flex; align-items: center; gap: 4px; font-family: var(--font-display);">
            Xem tất cả <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
        </a>
    </div>

    @if($recentRegistrations->isEmpty())
        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; color: var(--text-light);"></i>
            <p>Chưa có đơn đăng ký tiêm chủng nào được lưu trong hệ thống.</p>
        </div>
    @else
        <div class="table-responsive-modern">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">STT</th>
                        <th>Mã đơn</th>
                        <th>Họ tên người tiêm</th>
                        <th>Số điện thoại</th>
                        <th>Địa điểm tiêm</th>
                        <th>Ngày tiêm</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentRegistrations as $reg)
                        <tr>
                            <td style="text-align: center; color: var(--text-muted); font-weight: 600;">{{ $loop->iteration }}</td>
                            <td style="font-weight: 700; color: var(--primary-color);">{{ $reg->registration_code }}</td>
                            <td style="font-weight: 600;">{{ $reg->patient_name }}</td>
                            <td>{{ $reg->patient_phone }}</td>
                            <td>{{ $reg->center_name }}</td>
                            <td>{{ date('d/m/Y', strtotime($reg->injection_date)) }}</td>
                            <td style="font-weight: 600;">{{ number_format($reg->total_price, 0, ',', '.') }} đ</td>
                            <td>{{ $reg->bookingStatusLabel() }} · {{ $reg->paymentStatusLabel() }}</td>
                            <td>
                                <a href="{{ route('admin.registrations.show', $reg->id) }}" class="btn-action-sm">
                                    <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Chi tiết
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
