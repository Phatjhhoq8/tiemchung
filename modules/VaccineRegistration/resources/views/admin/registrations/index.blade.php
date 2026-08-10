@extends('vaccine::layouts.admin')

@section('title', 'Quản lý đơn đặt lịch')
@section('page_title', 'Đơn Đặt Lịch')

@section('admin_content')
<div class="card-modern">
    <div style="display:flex; justify-content:flex-end; margin-bottom:18px;">
        <a href="{{ route('admin.registrations.create', request()->only('center_id')) }}" class="btn-modern btn-modern-primary">Đăng ký nhanh tại quầy</a>
    </div>
    <form action="{{ route('admin.registrations.index') }}" method="GET" class="vaccine-filter-form" style="display:flex; gap:12px; flex-wrap:wrap; align-items:end; margin-bottom:24px;">
        @if($isSuperAdmin ?? false)
            <div style="flex:1 1 180px;">
                <label class="form-label-modern" for="center_id">Chi nhánh</label>
                <select class="form-control-modern" id="center_id" name="center_id">
                    <option value="" {{ $selectedCenterId === null ? 'selected' : '' }}>Tất cả chi nhánh</option>
                    @foreach($centers as $center)
                        <option value="{{ $center->id }}" {{ (string) $selectedCenterId === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div style="flex:1 1 220px;">
            <label class="form-label-modern" for="search">Tìm kiếm</label>
            <input class="form-control-modern" id="search" name="search" value="{{ request('search') }}" placeholder="Mã đơn, tên hoặc SĐT">
        </div>
        <div style="flex:1 1 150px;">
            <label class="form-label-modern" for="injection_date_from">Từ ngày hẹn</label>
            <input class="form-control-modern" id="injection_date_from" type="date" name="injection_date_from" value="{{ request('injection_date_from') }}">
        </div>
        <div style="flex:1 1 150px;">
            <label class="form-label-modern" for="injection_date_to">Đến ngày hẹn</label>
            <input class="form-control-modern" id="injection_date_to" type="date" name="injection_date_to" value="{{ request('injection_date_to') }}">
        </div>

        @php
            $selectedDay = request('filter_day') ?? request('day');
            $selectedMonth = request('filter_month') ?? request('month');
            $selectedYear = request('filter_year') ?? request('year');
            $currentYear = (int) date('Y');
        @endphp
        <div style="flex: 0 1 100px;">
            <label class="form-label-modern" for="filter_day">Ngày</label>
            <select class="form-control-modern" id="filter_day" name="filter_day" style="background-image:none;">
                <option value="">Tất cả</option>
                @for($d = 1; $d <= 31; $d++)
                    <option value="{{ $d }}" {{ (string)$selectedDay === (string)$d ? 'selected' : '' }}>Ngày {{ $d }}</option>
                @endfor
            </select>
        </div>
        <div style="flex: 0 1 110px;">
            <label class="form-label-modern" for="filter_month">Tháng</label>
            <select class="form-control-modern" id="filter_month" name="filter_month" style="background-image:none;">
                <option value="">Tất cả</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ (string)$selectedMonth === (string)$m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                @endfor
            </select>
        </div>
        <div style="flex: 0 1 100px;">
            <label class="form-label-modern" for="filter_year">Năm</label>
            <select class="form-control-modern" id="filter_year" name="filter_year" style="background-image:none;">
                <option value="">Tất cả</option>
                @for($y = $currentYear + 1; $y >= 2023; $y--)
                    <option value="{{ $y }}" {{ (string)$selectedYear === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <div style="flex:1 1 150px;">
            <label class="form-label-modern" for="booking_status">Lịch hẹn</label>
            <select class="form-control-modern" id="booking_status" name="booking_status">
                <option value="">Tất cả</option>
                @foreach(['pending' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'completed' => 'Đã hoàn tất', 'no_show' => 'Không đến', 'cancelled' => 'Đã hủy'] as $value => $label)
                    <option value="{{ $value }}" {{ request('booking_status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1 1 150px;">
            <label class="form-label-modern" for="payment_status">Thanh toán</label>
            <select class="form-control-modern" id="payment_status" name="payment_status">
                <option value="">Tất cả</option>
                <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
            </select>
        </div>
        <button type="submit" class="btn-modern btn-modern-primary">Lọc</button>
        @if(request()->hasAny(['search', 'booking_status', 'payment_status', 'center_id', 'injection_date_from', 'injection_date_to', 'filter_day', 'day', 'filter_month', 'month', 'filter_year', 'year']))
            <a href="{{ route('admin.registrations.index') }}" class="btn-modern btn-modern-secondary" style="text-decoration:none; display:inline-flex; align-items:center;">Xóa lọc</a>
        @endif
        <a href="{{ route('admin.registrations.export.csv', request()->query()) }}" class="btn-modern btn-modern-secondary" style="text-decoration:none; display:inline-flex; align-items:center;">Xuất CSV</a>
    </form>

    <div id="table-container">
        @include('vaccine::admin.registrations._table')
    </div>
</div>
@endsection

@section('scripts')
    @include('vaccine::admin.partials._ajax_filter_js')
@endsection
