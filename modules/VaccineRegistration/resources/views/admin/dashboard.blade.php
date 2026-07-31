@extends('vaccine::layouts.admin')

@section('title', 'Admin Dashboard - Medicare Cờ Đỏ')
@section('page_title', 'Bảng Điều Khiển Quản Trị')

@section('styles')
<style>
    @media (max-width: 639px) {
        .dashboard-grid {
            gap: 12px !important;
            margin-bottom: 24px !important;
        }
    }
</style>
@endsection

@section('admin_content')
@if($isSuperAdmin ?? false)
<form method="GET" action="{{ route('admin.dashboard') }}" class="card-modern" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap; margin-bottom:24px;">
    <div style="flex:1 1 260px;">
        <label class="form-label-modern">Lọc thống kê theo chi nhánh</label>
        <select name="center_id" class="form-control-modern" style="background-image:none;">
            <option value="">Toàn hệ thống</option>
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

<!-- Thẻ thống kê Widgets -->
<div class="dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; margin-bottom: 40px;">
    <!-- Widget 1: Tổng đăng ký -->
    <div class="stat-card-modern">
        <div>
            <span class="stat-card-title">Tổng Đăng Ký</span>
            <strong class="stat-card-number">{{ $totalRegistrations }}</strong>
        </div>
        <div class="stat-card-icon-wrapper" style="background-color: #eff6ff; color: #1d4ed8;">
            <i data-lucide="clipboard-list"></i>
        </div>
    </div>

    <!-- Widget 2: Doanh thu -->
    <div class="stat-card-modern">
        <div>
            <span class="stat-card-title">Doanh Thu (Dự Kiến)</span>
            <strong class="stat-card-number" style="color: #10b981; font-size: 24px;">{{ number_format($totalRevenue, 0, ',', '.') }} đ</strong>
        </div>
        <div class="stat-card-icon-wrapper" style="background-color: #ecfdf5; color: #10b981;">
            <i data-lucide="dollar-sign"></i>
        </div>
    </div>

    <!-- Widget 3: Chờ thanh toán -->
    <div class="stat-card-modern">
        <div>
            <span class="stat-card-title">Chờ Thanh Toán</span>
            <strong class="stat-card-number">{{ $pendingCount }}</strong>
        </div>
        <div class="stat-card-icon-wrapper" style="background-color: #fffbeb; color: #d97706;">
            <i data-lucide="clock"></i>
        </div>
    </div>

    <!-- Widget 4: Đã hoàn tất -->
    <div class="stat-card-modern">
        <div>
            <span class="stat-card-title">Đã Hoàn Tất</span>
            <strong class="stat-card-number">{{ $completedCount }}</strong>
        </div>
        <div class="stat-card-icon-wrapper" style="background-color: #f5f3ff; color: #7c3aed;">
            <i data-lucide="shield-check"></i>
        </div>
    </div>

    <div class="stat-card-modern">
        <div>
            <span class="stat-card-title">Yêu Cầu Tư Vấn</span>
            <strong class="stat-card-number">{{ $consultCount }}</strong>
        </div>
        <div class="stat-card-icon-wrapper" style="background-color: #eff6ff; color: #0284c7;">
            <i data-lucide="headphones"></i>
        </div>
    </div>

    <div class="stat-card-modern">
        <div>
            <span class="stat-card-title">Sản Phẩm Bán Ra</span>
            <strong class="stat-card-number">{{ $soldQuantity }}</strong>
        </div>
        <div class="stat-card-icon-wrapper" style="background-color: #ecfdf5; color: #16a34a;">
            <i data-lucide="shopping-bag"></i>
        </div>
    </div>

    <div class="stat-card-modern">
        <div>
            <span class="stat-card-title">Sản Phẩm Nhập Vào</span>
            <strong class="stat-card-number">{{ $importedQuantity }}</strong>
        </div>
        <div class="stat-card-icon-wrapper" style="background-color: #fff7ed; color: #ea580c;">
            <i data-lucide="package-plus"></i>
        </div>
    </div>
</div>

<!-- Khung phụ thống kê nhanh & Quản trị nhanh -->
<div class="dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 40px;">
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
                <strong style="font-size: 20px; color: var(--text-primary); font-family: var(--font-display);">Slider Hero</strong>
            </div>
        </div>
        <a href="{{ route('admin.banners.index') }}" class="btn-action-sm">
            <i data-lucide="image" style="width: 14px; height: 14px;"></i> Banner
        </a>
    </div>
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
                            <td>
                                <span class="badge-modern 
                                    @if($reg->status === 'Đã thanh toán') badge-modern-success
                                    @elseif($reg->status === 'Đã tiêm') badge-modern-info
                                    @elseif($reg->status === 'Đã hủy') badge-modern-danger
                                    @else badge-modern-warning @endif">
                                    {{ $reg->status }}
                                </span>
                            </td>
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
