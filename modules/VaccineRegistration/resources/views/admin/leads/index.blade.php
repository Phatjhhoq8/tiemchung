@extends('vaccine::layouts.admin')

@section('title', 'Quản lý Tư Vấn Lead - Medicare Cờ Đỏ')
@section('page_title', 'Danh Sách Khách Hàng Yêu Cầu Tư Vấn (CRM Leads)')

@section('admin_content')
<div class="card-modern">
    <!-- Bộ lọc & Tìm kiếm -->
    <div style="margin-bottom: 30px; padding-bottom: 24px; border-bottom: 1px solid var(--border-color);">
        <form action="{{ route('admin.leads.index') }}" method="GET" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
            @if($isSuperAdmin ?? false)
            <div style="width: 220px;">
                <label for="center_id" class="form-label-modern">Chi nhánh</label>
                <select name="center_id" id="center_id" class="form-control-modern" style="background-image: none;">
                    <option value="" {{ $selectedCenterId === null ? 'selected' : '' }}>Tất cả chi nhánh</option>
                    @foreach($centers as $center)
                        <option value="{{ $center->id }}" {{ (string) $selectedCenterId === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Tìm kiếm -->
            <div style="flex: 1 1 250px;">
                <label for="search" class="form-label-modern">Tìm kiếm nhanh</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nhập tên khách hàng, SĐT, nguồn..." class="form-control-modern">
            </div>

            <!-- Trạng thái -->
            <div style="width: 200px;">
                <label for="status" class="form-label-modern">Trạng thái</label>
                <select name="status" id="status" class="form-control-modern" style="background-image: none;">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>Mới (New)</option>
                    <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Đã liên hệ</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Hủy bỏ</option>
                </select>
            </div>

            <!-- Nút Lọc -->
            <button type="submit" class="btn-modern btn-modern-primary" style="padding: 10px 24px; border-radius: 8px;">
                <i data-lucide="filter" style="width: 14px; height: 14px;"></i> Lọc
            </button>
            
            @if(request()->hasAny(['search', 'status', 'center_id']))
                <a href="{{ route('admin.leads.index') }}" class="btn-modern btn-modern-secondary" style="padding: 10px 20px; border-radius: 8px;">Xóa bộ lọc</a>
            @endif
        </form>
    </div>

    @if($leads->isEmpty())
        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; color: var(--text-light);"></i>
            <p>Chưa có yêu cầu tư vấn nào.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ & Tên</th>
                        <th>Số điện thoại</th>
                        <th>Nguồn yêu cầu</th>
                        <th>Trung tâm</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                        <tr>
                            <td>#{{ $lead->id }}</td>
                            <td><strong style="color: var(--text-main);">{{ $lead->name }}</strong></td>
                            <td>{{ $lead->phone }}</td>
                            <td><span class="badge-modern badge-modern-secondary">{{ $lead->source ?? 'Website' }}</span></td>
                            <td>{{ $lead->center?->name ?? 'Tất cả / Medicare' }}</td>
                            <td>
                                @if($lead->status === 'new')
                                    <span class="badge-modern badge-modern-warning">Mới</span>
                                @elseif($lead->status === 'contacted')
                                    <span class="badge-modern badge-modern-info">Đã liên hệ</span>
                                @elseif($lead->status === 'completed')
                                    <span class="badge-modern badge-modern-success">Hoàn thành</span>
                                @else
                                    <span class="badge-modern badge-modern-secondary">{{ $lead->status }}</span>
                                @endif
                            </td>
                            <td>{{ $lead->created_at->format('H:i d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.leads.show', $lead->id) }}" class="btn-modern btn-modern-secondary" style="padding: 4px 12px; font-size: 12px;">Xem</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $leads->links() }}
        </div>
    @endif
</div>
@endsection
