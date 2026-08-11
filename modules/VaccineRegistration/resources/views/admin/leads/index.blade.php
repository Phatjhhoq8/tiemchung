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

            <div style="width: 150px;">
                <label for="from_date" class="form-label-modern">Từ ngày</label>
                <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" class="form-control-modern">
            </div>
            <div style="width: 150px;">
                <label for="to_date" class="form-label-modern">Đến ngày</label>
                <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" class="form-control-modern">
            </div>

            <!-- Nút Lọc -->
            <div style="display: flex; gap: 8px; align-items: flex-end;">
                <button type="submit" class="btn-modern btn-modern-primary" style="height: 42px;">Lọc</button>
                @if(request()->hasAny(['search', 'status', 'center_id', 'from_date', 'to_date']))
                    <a href="{{ route('admin.leads.index') }}" class="btn-modern btn-modern-secondary" style="height: 42px; display: inline-flex; align-items: center; text-decoration: none;">Xóa lọc</a>
                @endif
            </div>
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
