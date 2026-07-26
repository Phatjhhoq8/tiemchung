@extends('vaccine::layouts.admin')

@section('title', 'Quản lý Banner - Medicare Cờ Đỏ')
@section('page_title', 'Quản Lý Banner Trang Chủ')

@section('admin_content')
<div class="card-modern">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <h2 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0;">
            Danh sách Banner
            <span style="font-size: 14px; font-weight: 400; color: var(--text-muted);">({{ $banners->total() }} banner)</span>
        </h2>
        <a href="{{ route('admin.banners.create') }}" class="btn-modern btn-modern-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Thêm Banner Mới
        </a>
    </div>

    @if($banners->isEmpty())
        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i data-lucide="image" style="width: 48px; height: 48px; margin-bottom: 12px; color: var(--text-light);"></i>
            <p>Chưa có banner nào trong hệ thống.</p>
        </div>
    @else
        <div class="table-responsive-modern">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">Thứ tự</th>
                        <th>Tiêu đề</th>
                        <th>Phụ đề</th>
                        <th>URL ảnh</th>
                        <th style="text-align: center; width: 140px;">Trạng thái</th>
                        <th style="text-align: center; width: 220px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($banners as $banner)
                        <tr>
                            <td style="text-align: center; font-weight: 600; color: var(--text-muted);">{{ $banner->sort_order }}</td>
                            <td style="font-weight: 700; color: var(--text-primary);">{{ $banner->title }}</td>
                            <td style="color: var(--text-muted);">{{ Str::limit($banner->subtitle, 50) }}</td>
                            <td style="color: var(--text-light); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $banner->image_url }}</td>
                            <td style="text-align: center;">
                                <span class="badge-modern {{ $banner->is_active ? 'badge-modern-success' : 'badge-modern-danger' }}">
                                    {{ $banner->is_active ? 'Hiển thị' : 'Ẩn' }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: inline-flex; gap: 8px; justify-content: center; width: 100%;">
                                    <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn-action-sm">
                                        <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa banner này?')" style="display: inline;">
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

        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $banners->links() }}
        </div>
    @endif
</div>
@endsection
