@extends('vaccine::layouts.admin')

@section('title', 'Quản lý Trung Tâm - Medicare Cờ Đỏ')
@section('page_title', 'Hệ Thống Trung Tâm Tiêm Chủng')

@section('admin_content')
<div class="card-modern">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <h2 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0;">Danh sách các trung tâm tiêm chủng</h2>
        <a href="{{ route('admin.centers.create') }}" class="btn-modern btn-modern-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Thêm Chi Nhánh Mới
        </a>
    </div>

    @if($centers->isEmpty())
        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; color: var(--text-light);"></i>
            <p>Chưa có chi nhánh trung tâm nào trong hệ thống.</p>
        </div>
    @else
        <div class="table-responsive-modern">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Tên trung tâm</th>
                        <th>Địa chỉ</th>
                        <th>Hotline</th>
                        <th style="text-align: center; width: 140px;">Trạng thái</th>
                        <th style="text-align: center; width: 220px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($centers as $center)
                        <tr>
                            <td style="font-weight: 600;">{{ $center->id }}</td>
                            <td style="font-weight: 700; color: var(--text-primary);">
                                {{ $center->name }}
                                @if($center->slug)
                                    <div style="font-size: 12px; color: var(--text-muted); font-weight: 500;">{{ $center->slug }}</div>
                                @endif
                            </td>
                            <td style="color: var(--text-muted);">{{ $center->address }}</td>
                            <td style="white-space: nowrap; font-weight: 500;">{{ $center->phone ?: 'Chưa cập nhật' }}</td>
                            <td style="text-align: center;">
                                <span class="badge-modern {{ $center->is_active ? 'badge-modern-success' : 'badge-modern-danger' }}">
                                    {{ $center->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: inline-flex; gap: 8px; justify-content: center; width: 100%;">
                                    <a href="{{ route('admin.centers.edit', $center->id) }}" class="btn-action-sm">
                                        <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.centers.destroy', $center->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa trung tâm này?')" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-sm btn-action-danger">
                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px;">
            {{ $centers->links() }}
        </div>
    @endif
</div>
@endsection
