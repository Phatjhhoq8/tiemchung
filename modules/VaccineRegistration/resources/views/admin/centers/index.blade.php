@extends('vaccine::layouts.admin')

@section('title', 'Quản lý Trung Tâm - Medicare')
@section('page_title', 'Hệ Thống Trung Tâm Tiêm Chủng')

@section('admin_content')
<div class="card-modern">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <h2 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0;">Danh sách các trung tâm tiêm chủng</h2>
        <a href="{{ route('admin.centers.create') }}" class="btn-modern btn-modern-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Thêm Chi Nhánh Mới
        </a>
    </div>

    <form method="GET" action="{{ route('admin.centers.index') }}" class="vaccine-filter-form" style="display:flex; gap:12px; align-items:flex-end; margin-bottom:20px; flex-wrap:wrap;">
        <div style="flex:2 1 240px;">
            <label class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Tìm kiếm</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên chi nhánh, địa chỉ, hotline..." class="form-control-modern">
        </div>
        <div style="flex:1 1 130px;">
            <label class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Trạng thái</label>
            <select name="is_active" class="form-control-modern">
                <option value="">Tất cả</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Tạm dừng</option>
            </select>
        </div>

        @php
            $selectedDay = request('filter_day') ?? request('day');
            $selectedMonth = request('filter_month') ?? request('month');
            $selectedYear = request('filter_year') ?? request('year');
            $currentYear = (int) date('Y');
        @endphp
        <div style="flex: 0 1 100px;">
            <label class="form-label-modern" for="filter_day" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Ngày</label>
            <select class="form-control-modern" id="filter_day" name="filter_day">
                <option value="">Tất cả</option>
                @for($d = 1; $d <= 31; $d++)
                    <option value="{{ $d }}" {{ (string)$selectedDay === (string)$d ? 'selected' : '' }}>Ngày {{ $d }}</option>
                @endfor
            </select>
        </div>
        <div style="flex: 0 1 110px;">
            <label class="form-label-modern" for="filter_month" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Tháng</label>
            <select class="form-control-modern" id="filter_month" name="filter_month">
                <option value="">Tất cả</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ (string)$selectedMonth === (string)$m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                @endfor
            </select>
        </div>
        <div style="flex: 0 1 100px;">
            <label class="form-label-modern" for="filter_year" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Năm</label>
            <select class="form-control-modern" id="filter_year" name="filter_year">
                <option value="">Tất cả</option>
                @for($y = $currentYear + 1; $y >= 2023; $y--)
                    <option value="{{ $y }}" {{ (string)$selectedYear === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <button type="submit" class="btn-modern btn-modern-primary" style="height: 42px;">Lọc</button>
        @if(request()->hasAny(['search', 'is_active', 'filter_day', 'day', 'filter_month', 'month', 'filter_year', 'year']))
            <a href="{{ route('admin.centers.index') }}" class="btn-modern btn-modern-secondary" style="height: 42px; display: inline-flex; align-items: center; text-decoration: none;">Xóa lọc</a>
        @endif
    </form>

    <div id="table-container">
        @include('vaccine::admin.centers._table')
    </div>
</div>
@endsection

@section('scripts')
    @include('vaccine::admin.partials._ajax_filter_js')
@endsection
