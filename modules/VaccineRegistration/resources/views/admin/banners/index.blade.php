@extends('vaccine::layouts.admin')

@section('title', 'Quản lý Banner - Medicare')
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

    <form method="GET" action="{{ route('admin.banners.index') }}" class="vaccine-filter-form" style="display:flex; gap:12px; align-items:flex-end; margin-bottom:20px; flex-wrap:wrap;">
        <div style="flex:2 1 260px;">
            <label class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Tìm kiếm</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tiêu đề, phụ đề..." class="form-control-modern">
        </div>
        <div style="flex:1 1 150px;">
            <label class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Trạng thái</label>
            <select name="is_active" class="form-control-modern">
                <option value="">Tất cả</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Hiển thị</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Ẩn</option>
            </select>
        </div>
        <button type="submit" class="btn-modern btn-modern-primary" style="height: 42px;">Lọc</button>
        @if(request()->hasAny(['search', 'is_active']))
            <a href="{{ route('admin.banners.index') }}" class="btn-modern btn-modern-secondary" style="height: 42px; display: inline-flex; align-items: center; text-decoration: none;">Xóa lọc</a>
        @endif
    </form>

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
                        <th style="width: 70px; text-align: center;">STT</th>
                        <th style="width: 80px; text-align: center;">Thứ tự</th>
                        <th>Tiêu đề</th>
                        <th>Phụ đề</th>
                        <th>URL ảnh</th>
                        <th style="text-align: center; width: 130px;">Trạng thái</th>
                        <th style="text-align: right; width: 200px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($banners as $banner)
                        <tr>
                            <td style="text-align: center; font-weight: 700; color: var(--text-muted);">
                                {{ $banners->firstItem() + $loop->index }}
                            </td>
                            <td style="text-align: center; font-weight: 600; color: var(--text-muted);">{{ $banner->sort_order }}</td>
                            <td style="font-weight: 700; color: var(--text-primary);">{{ $banner->title }}</td>
                            <td style="color: var(--text-muted);">{{ Str::limit($banner->subtitle, 50) }}</td>
                            <td style="color: var(--text-light); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $banner->image_url }}</td>
                            <td style="text-align: center;">
                                @if($banner->is_active)
                                    <span class="badge-modern badge-modern-success">Hiển thị</span>
                                @else
                                    <span class="badge-modern badge-modern-danger">Đang ẩn</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <div style="display: inline-flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                    {{-- Nút Ẩn / Hiện (Ẩn mềm) --}}
                                    <form action="{{ route('admin.banners.toggle-status', $banner->id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        @if($banner->is_active)
                                            <button type="submit" class="btn-action-sm btn-action-warning" title="Bấm để ẩn biểu ngữ khỏi slider (Ẩn mềm)">Ẩn</button>
                                        @else
                                            <button type="submit" class="btn-action-sm btn-action-success" title="Bấm để hiển thị lại biểu ngữ lên slider">Hiện</button>
                                        @endif
                                    </form>

                                    {{-- Nút Sửa --}}
                                    <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn-action-sm">Sửa</a>

                                    {{-- Nút Xóa cứng (Xóa vĩnh viễn) --}}
                                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" data-confirm="CẢNH BÁO: Hành động này sẽ XÓA VĨNH VIỄN biểu ngữ khỏi hệ thống và không thể khôi phục. Bạn có chắc chắn muốn xóa cứng?" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-sm btn-action-danger" title="Xóa vĩnh viễn biểu ngữ khỏi CSDL">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
            {{ $banners->links() }}
        </div>
    @endif
</div>
@endsection
