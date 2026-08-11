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
</style>
@endsection

@section('admin_content')
@if($isSuperAdmin ?? false)
<form method="GET" action="{{ route('admin.dashboard') }}" class="card-modern" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap; margin-bottom:24px;">
    <div style="flex:1 1 260px;">
        <label class="form-label-modern">Lọc thống kê theo chi nhánh</label>
        <select name="center_id" class="form-control-modern">
            <option value="" {{ $selectedCenterId === null ? 'selected' : '' }}>Tất cả chi nhánh</option>
            @foreach($centers as $center)
                <option value="{{ $center->id }}" {{ (string) $selectedCenterId === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn-modern btn-modern-primary">Lọc</button>
</form>
@else
<div class="card-modern" style="margin-bottom:24px; color:var(--text-muted); font-weight:700;">
    Thống kê chi nhánh: {{ $adminUser?->center?->name ?? 'Chi nhánh của bạn' }}
</div>
@endif

<!-- R2 Widget: Lịch hẹn tiêm hôm nay (Prominent Medical Staff Widget) -->
<div class="today-widget-card">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <span class="today-widget-badge">Theo Dõi Tiêm Chủng Hôm Nay</span>
            <h2 style="font-size: 20px; font-weight: 700; color: #ffffff; margin: 0 0 6px 0; font-family: var(--font-display);">Lịch Hẹn Tiêm Trong Ngày</h2>
            <p style="margin: 0; color: #cbd5e1; font-size: 14px;">Tổng số ca tiêm dự kiến ngày {{ date('d/m/Y') }} theo dữ liệu hệ thống</p>
        </div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="text-align: right;">
                <div class="today-widget-number">{{ $todayInjectionsCount }}</div>
                <span style="font-size: 13px; color: #eaaa00; font-weight: 600;">Ca hẹn tiêm chủng</span>
            </div>
            <a href="{{ route('admin.registrations.index') }}" class="btn-modern" style="background-color: #c8102e; color: #ffffff; font-weight: 600; text-decoration: none; padding: 10px 18px; border-radius: 8px; font-size: 14px; white-space: nowrap;">
                Xem danh sách tiêm
            </a>
        </div>
    </div>
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

    <!-- Widget 5: Yêu cầu tư vấn (R1) -->
    <div class="stat-card-modern">
        <div>
            <span class="stat-card-title">Yêu Cầu Tư Vấn</span>
            <strong class="stat-card-number" style="color: #0f172a;">{{ $consultCount }}</strong>
            <span style="display:block; font-size:11px; color:#64748b;">Chưa xử lý (Mới/Chờ)</span>
        </div>
        <div class="stat-card-icon-wrapper" style="background-color: #fef3c7; color: #b45309;">
            <i data-lucide="message-square"></i>
        </div>
    </div>

    <!-- Widget 6: Tồn kho vắc xin (R1) -->
    <div class="stat-card-modern">
        <div>
            <span class="stat-card-title">Tồn Kho Vắc Xin</span>
            <strong class="stat-card-number" style="color: #004b8f;">{{ number_format($importedQuantity, 0, ',', '.') }}</strong>
            <span style="display:block; font-size:11px; color:#64748b;">Liều hiện có trong kho</span>
        </div>
        <div class="stat-card-icon-wrapper" style="background-color: #e0f2fe; color: #0369a1;">
            <i data-lucide="package"></i>
        </div>
    </div>

    <!-- Widget 7: Vắc xin đã tiêm / đã bán (R1) -->
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
    // Calculations for 7-day chart
    $maxDailyRev = max(100000, max(array_column($dailyTrends, 'revenue')));
    $maxDailyReg = max(5, max(array_column($dailyTrends, 'registrations')));
    $dailyCount = count($dailyTrends);

    $dailyRevPoints = [];
    $dailyRegPoints = [];

    foreach ($dailyTrends as $idx => $item) {
        $x = 70 + ($idx * (600 / max(1, $dailyCount - 1)));
        $yRev = 210 - (($item['revenue'] / $maxDailyRev) * 150);
        $yReg = 210 - (($item['registrations'] / $maxDailyReg) * 150);

        $dailyRevPoints[] = "$x," . round($yRev, 1);
        $dailyRegPoints[] = "$x," . round($yReg, 1);
    }
    $dailyRevPolyline = implode(' ', $dailyRevPoints);
    $dailyRegPolyline = implode(' ', $dailyRegPoints);
    $dailyRevAreaD = "M 70,210 L " . implode(' L ', $dailyRevPoints) . " L 670,210 Z";

    // Calculations for 6-month chart
    $maxMonthlyRev = max(100000, max(array_column($monthlyTrends, 'revenue')));
    $maxMonthlyReg = max(5, max(array_column($monthlyTrends, 'registrations')));
    $monthlyCount = count($monthlyTrends);

    $monthlyRevPoints = [];
    $monthlyRegPoints = [];

    foreach ($monthlyTrends as $idx => $item) {
        $x = 70 + ($idx * (600 / max(1, $monthlyCount - 1)));
        $yRev = 210 - (($item['revenue'] / $maxMonthlyRev) * 150);
        $yReg = 210 - (($item['registrations'] / $maxMonthlyReg) * 150);

        $monthlyRevPoints[] = "$x," . round($yRev, 1);
        $monthlyRegPoints[] = "$x," . round($yReg, 1);
    }
    $monthlyRevPolyline = implode(' ', $monthlyRevPoints);
    $monthlyRegPolyline = implode(' ', $monthlyRegPoints);
    $monthlyRevAreaD = "M 70,210 L " . implode(' L ', $monthlyRevPoints) . " L 670,210 Z";
@endphp

<div class="card-modern" style="margin-bottom: 32px; padding: 24px; background: #ffffff; border-radius: 12px; border: 1px solid var(--border-color);">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color);">
        <div>
            <h2 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; color: #004b8f; margin: 0;">Biểu Đồ Xu Hướng Doanh Thu & Đăng Ký</h2>
            <span style="font-size: 13px; color: #64748b;">Dữ liệu doanh thu thực nhận và số lượng lượt đăng ký tiêm chủng</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="button" id="tab-7days-btn" onclick="switchTrendTab('7days')" class="btn-modern" style="background-color: #c8102e; color: #ffffff; padding: 6px 16px; font-size: 13px; font-weight: 600; border-radius: 6px; border: none; cursor: pointer;">7 Ngày Gần Đây</button>
            <button type="button" id="tab-6months-btn" onclick="switchTrendTab('6months')" class="btn-modern" style="background-color: #f1f5f9; color: #0f172a; padding: 6px 16px; font-size: 13px; font-weight: 600; border-radius: 6px; border: 1px solid #cbd5e1; cursor: pointer;">6 Tháng Gần Đây</button>
        </div>
    </div>

    <!-- Chart Legend -->
    <div style="display: flex; align-items: center; gap: 24px; margin-bottom: 16px; font-size: 13px; font-weight: 600; color: #334155; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="display: inline-block; width: 14px; height: 14px; background-color: #c8102e; border-radius: 3px;"></span>
            <span>Doanh thu đã thanh toán (VND)</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="display: inline-block; width: 14px; height: 14px; background-color: #004b8f; border-radius: 3px;"></span>
            <span>Số lượng đăng ký tiêm</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px; margin-left: auto;">
            <span style="display: inline-block; width: 10px; height: 10px; background-color: #eaaa00; border-radius: 50%;"></span>
            <span style="color: #64748b; font-weight: 500;">Thương hiệu Medicare</span>
        </div>
    </div>

    <!-- 7 Days Chart View -->
    <div id="chart-7days-view" class="chart-container">
        <svg viewBox="0 0 720 270" class="chart-svg">
            <defs>
                <linearGradient id="gradDailyRev" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#c8102e" stop-opacity="0.2" />
                    <stop offset="100%" stop-color="#c8102e" stop-opacity="0.0" />
                </linearGradient>
            </defs>

            <!-- Background Grid & Y-Axis Lines -->
            <line x1="70" y1="60" x2="670" y2="60" stroke="#f1f5f9" stroke-width="1" />
            <line x1="70" y1="110" x2="670" y2="110" stroke="#f1f5f9" stroke-width="1" />
            <line x1="70" y1="160" x2="670" y2="160" stroke="#f1f5f9" stroke-width="1" />
            <line x1="70" y1="210" x2="670" y2="210" stroke="#cbd5e1" stroke-width="1.5" />

            <!-- Left Y-Axis Labels (Revenue) -->
            <text x="62" y="64" text-anchor="end" font-size="10" fill="#64748b" font-weight="600">{{ number_format($maxDailyRev / 1000, 0) }}k</text>
            <text x="62" y="114" text-anchor="end" font-size="10" fill="#64748b" font-weight="600">{{ number_format(($maxDailyRev * 0.66) / 1000, 0) }}k</text>
            <text x="62" y="164" text-anchor="end" font-size="10" fill="#64748b" font-weight="600">{{ number_format(($maxDailyRev * 0.33) / 1000, 0) }}k</text>
            <text x="62" y="214" text-anchor="end" font-size="10" fill="#64748b" font-weight="600">0đ</text>

            <!-- Right Y-Axis Labels (Registrations) -->
            <text x="678" y="64" text-anchor="start" font-size="10" fill="#004b8f" font-weight="600">{{ $maxDailyReg }}</text>
            <text x="678" y="114" text-anchor="start" font-size="10" fill="#004b8f" font-weight="600">{{ round($maxDailyReg * 0.66) }}</text>
            <text x="678" y="164" text-anchor="start" font-size="10" fill="#004b8f" font-weight="600">{{ round($maxDailyReg * 0.33) }}</text>
            <text x="678" y="214" text-anchor="start" font-size="10" fill="#004b8f" font-weight="600">0</text>

            <!-- Revenue Area Fill -->
            <path d="{{ $dailyRevAreaD }}" fill="url(#gradDailyRev)" />

            <!-- Revenue Polyline (Medicare Red #c8102e) -->
            <polyline points="{{ $dailyRevPolyline }}" fill="none" stroke="#c8102e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

            <!-- Registration Polyline (Medicare Navy #004b8f) -->
            <polyline points="{{ $dailyRegPolyline }}" fill="none" stroke="#004b8f" stroke-width="2.5" stroke-dasharray="4,3" stroke-linecap="round" stroke-linejoin="round" />

            <!-- Data Nodes & Labels -->
            @foreach($dailyTrends as $idx => $item)
                @php
                    $x = 70 + ($idx * (600 / max(1, $dailyCount - 1)));
                    $yRev = 210 - (($item['revenue'] / $maxDailyRev) * 150);
                    $yReg = 210 - (($item['registrations'] / $maxDailyReg) * 150);
                @endphp
                <!-- X-Axis Label -->
                <text x="{{ $x }}" y="234" text-anchor="middle" font-size="11" fill="#475569" font-weight="600">{{ $item['label'] }}</text>

                <!-- Revenue Circle & Label -->
                <circle cx="{{ $x }}" cy="{{ round($yRev, 1) }}" r="5" fill="#ffffff" stroke="#c8102e" stroke-width="2.5" />
                @if($item['revenue'] > 0)
                    <text x="{{ $x }}" y="{{ round($yRev - 9, 1) }}" text-anchor="middle" font-size="9" fill="#c8102e" font-weight="700">{{ number_format($item['revenue'] / 1000, 0) }}k</text>
                @endif

                <!-- Registration Circle & Label -->
                <circle cx="{{ $x }}" cy="{{ round($yReg, 1) }}" r="4" fill="#eaaa00" stroke="#004b8f" stroke-width="2" />
                @if($item['registrations'] > 0)
                    <text x="{{ $x }}" y="{{ round($yReg + 14, 1) }}" text-anchor="middle" font-size="9" fill="#004b8f" font-weight="700">{{ $item['registrations'] }}</text>
                @endif
            @endforeach
        </svg>
    </div>

    <!-- 6 Months Chart View -->
    <div id="chart-6months-view" class="chart-container" style="display: none;">
        <svg viewBox="0 0 720 270" class="chart-svg">
            <defs>
                <linearGradient id="gradMonthlyRev" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#c8102e" stop-opacity="0.2" />
                    <stop offset="100%" stop-color="#c8102e" stop-opacity="0.0" />
                </linearGradient>
            </defs>

            <!-- Background Grid & Y-Axis Lines -->
            <line x1="70" y1="60" x2="670" y2="60" stroke="#f1f5f9" stroke-width="1" />
            <line x1="70" y1="110" x2="670" y2="110" stroke="#f1f5f9" stroke-width="1" />
            <line x1="70" y1="160" x2="670" y2="160" stroke="#f1f5f9" stroke-width="1" />
            <line x1="70" y1="210" x2="670" y2="210" stroke="#cbd5e1" stroke-width="1.5" />

            <!-- Left Y-Axis Labels (Revenue) -->
            <text x="62" y="64" text-anchor="end" font-size="10" fill="#64748b" font-weight="600">{{ number_format($maxMonthlyRev / 1000, 0) }}k</text>
            <text x="62" y="114" text-anchor="end" font-size="10" fill="#64748b" font-weight="600">{{ number_format(($maxMonthlyRev * 0.66) / 1000, 0) }}k</text>
            <text x="62" y="164" text-anchor="end" font-size="10" fill="#64748b" font-weight="600">{{ number_format(($maxMonthlyRev * 0.33) / 1000, 0) }}k</text>
            <text x="62" y="214" text-anchor="end" font-size="10" fill="#64748b" font-weight="600">0đ</text>

            <!-- Right Y-Axis Labels (Registrations) -->
            <text x="678" y="64" text-anchor="start" font-size="10" fill="#004b8f" font-weight="600">{{ $maxMonthlyReg }}</text>
            <text x="678" y="114" text-anchor="start" font-size="10" fill="#004b8f" font-weight="600">{{ round($maxMonthlyReg * 0.66) }}</text>
            <text x="678" y="164" text-anchor="start" font-size="10" fill="#004b8f" font-weight="600">{{ round($maxMonthlyReg * 0.33) }}</text>
            <text x="678" y="214" text-anchor="start" font-size="10" fill="#004b8f" font-weight="600">0</text>

            <!-- Revenue Area Fill -->
            <path d="{{ $monthlyRevAreaD }}" fill="url(#gradMonthlyRev)" />

            <!-- Revenue Polyline (Medicare Red #c8102e) -->
            <polyline points="{{ $monthlyRevPolyline }}" fill="none" stroke="#c8102e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

            <!-- Registration Polyline (Medicare Navy #004b8f) -->
            <polyline points="{{ $monthlyRegPolyline }}" fill="none" stroke="#004b8f" stroke-width="2.5" stroke-dasharray="4,3" stroke-linecap="round" stroke-linejoin="round" />

            <!-- Data Nodes & Labels -->
            @foreach($monthlyTrends as $idx => $item)
                @php
                    $x = 70 + ($idx * (600 / max(1, $monthlyCount - 1)));
                    $yRev = 210 - (($item['revenue'] / $maxMonthlyRev) * 150);
                    $yReg = 210 - (($item['registrations'] / $maxMonthlyReg) * 150);
                @endphp
                <!-- X-Axis Label -->
                <text x="{{ $x }}" y="234" text-anchor="middle" font-size="11" fill="#475569" font-weight="600">{{ $item['short_label'] }}</text>

                <!-- Revenue Circle & Label -->
                <circle cx="{{ $x }}" cy="{{ round($yRev, 1) }}" r="5" fill="#ffffff" stroke="#c8102e" stroke-width="2.5" />
                @if($item['revenue'] > 0)
                    <text x="{{ $x }}" y="{{ round($yRev - 9, 1) }}" text-anchor="middle" font-size="9" fill="#c8102e" font-weight="700">{{ number_format($item['revenue'] / 1000, 0) }}k</text>
                @endif

                <!-- Registration Circle & Label -->
                <circle cx="{{ $x }}" cy="{{ round($yReg, 1) }}" r="4" fill="#eaaa00" stroke="#004b8f" stroke-width="2" />
                @if($item['registrations'] > 0)
                    <text x="{{ $x }}" y="{{ round($yReg + 14, 1) }}" text-anchor="middle" font-size="9" fill="#004b8f" font-weight="700">{{ $item['registrations'] }}</text>
                @endif
            @endforeach
        </svg>
    </div>
</div>

<script>
function switchTrendTab(tab) {
    var tab7 = document.getElementById('chart-7days-view');
    var tab6 = document.getElementById('chart-6months-view');
    var btn7 = document.getElementById('tab-7days-btn');
    var btn6 = document.getElementById('tab-6months-btn');
    if (!tab7 || !tab6 || !btn7 || !btn6) return;
    
    if (tab === '7days') {
        tab7.style.display = 'block';
        tab6.style.display = 'none';
        btn7.style.backgroundColor = '#c8102e';
        btn7.style.color = '#ffffff';
        btn7.style.border = 'none';
        btn6.style.backgroundColor = '#f1f5f9';
        btn6.style.color = '#0f172a';
        btn6.style.border = '1px solid #cbd5e1';
    } else {
        tab7.style.display = 'none';
        tab6.style.display = 'block';
        btn6.style.backgroundColor = '#c8102e';
        btn6.style.color = '#ffffff';
        btn6.style.border = 'none';
        btn7.style.backgroundColor = '#f1f5f9';
        btn7.style.color = '#0f172a';
        btn7.style.border = '1px solid #cbd5e1';
    }
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
