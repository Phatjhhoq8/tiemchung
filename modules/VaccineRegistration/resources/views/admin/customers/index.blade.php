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

        <div style="flex: 0 1 160px;">
            <label class="form-label-modern" for="from_date">Từ ngày</label>
            <input class="form-control-modern" id="from_date" type="date" name="from_date" value="{{ request('from_date') }}">
        </div>
        <div style="flex: 0 1 160px;">
            <label class="form-label-modern" for="to_date">Đến ngày</label>
            <input class="form-control-modern" id="to_date" type="date" name="to_date" value="{{ request('to_date') }}">
        </div>

        <button class="btn-modern btn-modern-primary" type="submit">Tra cứu</button>
        @if(request()->hasAny(['search', 'center_id', 'from_date', 'to_date']))
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
