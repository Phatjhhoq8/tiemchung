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

        <button type="submit" class="btn-modern btn-modern-primary" style="height: 42px;">Lọc</button>
        @if(request()->hasAny(['search', 'is_active']))
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
