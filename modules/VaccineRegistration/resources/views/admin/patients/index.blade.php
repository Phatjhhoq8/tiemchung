@extends('vaccine::layouts.admin')

@section('title', 'Hồ sơ bệnh nhân')
@section('page_title', 'Hồ Sơ Bệnh Nhân')

@section('admin_content')
<div style="display:grid; gap:24px;">
    <!-- Form bộ lọc & tìm kiếm -->
    <div class="card-modern">
        <form method="GET" action="{{ route('admin.patients.index') }}" class="vaccine-filter-form" style="display:flex; gap:12px; flex-wrap:wrap; align-items:end;">
            @if($isSuperAdmin ?? false)
            <div style="flex:1 1 200px;">
                <label class="form-label-modern" for="patient_center_id">Chi nhánh đăng ký</label>
                <select class="form-control-modern" id="patient_center_id" name="center_id">
                    <option value="" {{ $selectedCenterId === null ? 'selected' : '' }}>Tất cả chi nhánh</option>
                    @foreach($adminCenters as $center)
                        <option value="{{ $center->id }}" {{ (string) $selectedCenterId === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div style="flex:1 1 280px;">
                <label class="form-label-modern" for="search">Tìm kiếm bệnh nhân</label>
                <input class="form-control-modern" id="search" type="search" name="search" value="{{ $search }}" placeholder="Nhập tên, số điện thoại, số CCCD...">
            </div>
            <div style="flex: 0 1 160px;">
                <label class="form-label-modern" for="from_date">Từ ngày tạo</label>
                <input class="form-control-modern" id="from_date" type="date" name="from_date" value="{{ request('from_date') }}">
            </div>
            <div style="flex: 0 1 160px;">
                <label class="form-label-modern" for="to_date">Đến ngày tạo</label>
                <input class="form-control-modern" id="to_date" type="date" name="to_date" value="{{ request('to_date') }}">
            </div>
            <div style="display:flex; gap:8px;">
                <button class="btn-modern btn-modern-primary" type="submit">Lọc kết quả</button>
                @if(request()->hasAny(['search', 'center_id', 'from_date', 'to_date']))
                    <a href="{{ route('admin.patients.index') }}" class="btn-modern btn-modern-secondary" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">Xóa lọc</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Danh sách bệnh nhân -->
    <div class="card-modern">
        <h3 style="margin-top:0; margin-bottom:20px; color:var(--accent-color);">Danh sách Bệnh nhân</h3>
        
        <div id="table-container">
            @include('vaccine::admin.patients._table')
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @include('vaccine::admin.partials._ajax_filter_js')
@endsection
