@extends('vaccine::layouts.admin')

@section('title', 'Quản Lý Yêu Cầu Tư Vấn - Medicare')
@section('page_title', 'Danh Sách Khách Hàng Yêu Cầu Tư Vấn')

@section('admin_content')
<div class="card-modern">
    <!-- Bộ lọc & Tìm kiếm -->
    <div style="margin-bottom: 30px; padding-bottom: 24px; border-bottom: 1px solid var(--border-color);">
        <form action="{{ route('admin.leads.index') }}" method="GET" class="vaccine-filter-form" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
            @if($isSuperAdmin ?? false)
            <div style="width: 200px;">
                <label for="center_id" class="form-label-modern">Chi nhánh</label>
                <select name="center_id" id="center_id" class="form-control-modern">
                    <option value="" {{ $selectedCenterId === null ? 'selected' : '' }}>Tất cả chi nhánh</option>
                    @foreach($centers as $center)
                        <option value="{{ $center->id }}" {{ (string) $selectedCenterId === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Tìm kiếm -->
            <div style="flex: 1 1 220px;">
                <label for="search" class="form-label-modern">Tìm kiếm nhanh</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nhập tên khách hàng, SĐT, nguồn..." class="form-control-modern">
            </div>

            <!-- Trạng thái -->
            <div style="width: 170px;">
                <label for="status" class="form-label-modern">Trạng thái</label>
                <select name="status" id="status" class="form-control-modern">
                    <option value="">-- Tất cả --</option>
                    <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>Mới</option>
                    <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Đã liên hệ</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Hủy bỏ</option>
                </select>
            </div>

            @php
                $selectedDay = request('filter_day') ?? request('day');
                $selectedMonth = request('filter_month') ?? request('month');
                $selectedYear = request('filter_year') ?? request('year');
                $currentYear = (int) date('Y');
            @endphp
            <div style="width: 100px;">
                <label class="form-label-modern" for="filter_day">Ngày</label>
                <select class="form-control-modern" id="filter_day" name="filter_day">
                    <option value="">Tất cả</option>
                    @for($d = 1; $d <= 31; $d++)
                        <option value="{{ $d }}" {{ (string)$selectedDay === (string)$d ? 'selected' : '' }}>Ngày {{ $d }}</option>
                    @endfor
                </select>
            </div>
            <div style="width: 110px;">
                <label class="form-label-modern" for="filter_month">Tháng</label>
                <select class="form-control-modern" id="filter_month" name="filter_month">
                    <option value="">Tất cả</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ (string)$selectedMonth === (string)$m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                    @endfor
                </select>
            </div>
            <div style="width: 100px;">
                <label class="form-label-modern" for="filter_year">Năm</label>
                <select class="form-control-modern" id="filter_year" name="filter_year">
                    <option value="">Tất cả</option>
                    @for($y = $currentYear + 1; $y >= 2023; $y--)
                        <option value="{{ $y }}" {{ (string)$selectedYear === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Nút Lọc -->
            <button type="submit" class="btn-modern btn-modern-primary" style="padding: 10px 24px; border-radius: 8px;">
                <i data-lucide="filter" style="width: 14px; height: 14px;"></i> Lọc
            </button>
            
            @if(request()->hasAny(['search', 'status', 'center_id', 'filter_day', 'day', 'filter_month', 'month', 'filter_year', 'year']))
                <a href="{{ route('admin.leads.index') }}" class="btn-modern btn-modern-secondary" style="padding: 10px 20px; border-radius: 8px;">Xóa bộ lọc</a>
            @endif
        </form>
    </div>

    <div id="table-container">
        @include('vaccine::admin.leads._table')
    </div>
</div>
@endsection

@section('scripts')
    @include('vaccine::admin.partials._ajax_filter_js')
@endsection
