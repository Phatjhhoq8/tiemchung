@extends('vaccine::layouts.admin')

@section('title', 'Khách hàng và điểm')
@section('page_title', 'Khách Hàng & Điểm')

@section('admin_content')
<div class="card-modern">
    <form method="GET" action="{{ route('admin.customers.index') }}" class="vaccine-filter-form" style="display:flex; gap:12px; flex-wrap:wrap; align-items:end; margin-bottom:24px;">
        @if($isSuperAdmin ?? false)
        <div style="flex:1 1 200px;">
            <label class="form-label-modern" for="customer_center_id">Chi nhánh</label>
            <select class="form-control-modern" id="customer_center_id" name="center_id">
                <option value="" {{ $selectedCenterId === null ? 'selected' : '' }}>Tất cả chi nhánh</option>
                @foreach($adminCenters as $center)
                    <option value="{{ $center->id }}" {{ (string) $selectedCenterId === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div style="flex:1 1 240px;">
            <label class="form-label-modern" for="search">{{ ($isSuperAdmin ?? false) ? 'Tên hoặc số điện thoại' : 'Tra cứu chính xác số điện thoại' }}</label>
            <input class="form-control-modern" id="search" type="search" name="search" value="{{ $search }}" placeholder="Ví dụ: 0912345678">
        </div>

        @php
            $selectedDay = request('filter_day') ?? request('day');
            $selectedMonth = request('filter_month') ?? request('month');
            $selectedYear = request('filter_year') ?? request('year');
            $currentYear = (int) date('Y');
        @endphp
        <div style="flex: 0 1 100px;">
            <label class="form-label-modern" for="filter_day">Ngày</label>
            <select class="form-control-modern" id="filter_day" name="filter_day">
                <option value="">Tất cả</option>
                @for($d = 1; $d <= 31; $d++)
                    <option value="{{ $d }}" {{ (string)$selectedDay === (string)$d ? 'selected' : '' }}>Ngày {{ $d }}</option>
                @endfor
            </select>
        </div>
        <div style="flex: 0 1 110px;">
            <label class="form-label-modern" for="filter_month">Tháng</label>
            <select class="form-control-modern" id="filter_month" name="filter_month">
                <option value="">Tất cả</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ (string)$selectedMonth === (string)$m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                @endfor
            </select>
        </div>
        <div style="flex: 0 1 100px;">
            <label class="form-label-modern" for="filter_year">Năm</label>
            <select class="form-control-modern" id="filter_year" name="filter_year">
                <option value="">Tất cả</option>
                @for($y = $currentYear + 1; $y >= 2023; $y--)
                    <option value="{{ $y }}" {{ (string)$selectedYear === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <button class="btn-modern btn-modern-primary" type="submit">Tra cứu</button>
        @if(request()->hasAny(['search', 'center_id', 'filter_day', 'day', 'filter_month', 'month', 'filter_year', 'year']))
            <a href="{{ route('admin.customers.index') }}" class="btn-modern btn-modern-secondary" style="text-decoration:none; display:inline-flex; align-items:center;">Xóa lọc</a>
        @endif
    </form>

    <div id="table-container">
        @include('vaccine::admin.customers._table')
    </div>
</div>
@endsection

@section('scripts')
    @include('vaccine::admin.partials._ajax_filter_js')
@endsection
