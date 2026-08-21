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
        <div style="display: flex; gap: 8px; align-items: flex-end;">
            <button type="submit" class="btn-modern btn-modern-primary" style="height: 42px;">Lọc</button>
            @if(request()->hasAny(['search', 'is_active']))
                <a href="{{ route('admin.banners.index') }}" class="btn-modern btn-modern-secondary" style="height: 42px; display: inline-flex; align-items: center; text-decoration: none;">Xóa lọc</a>
            @endif
            <button type="button" id="bulkDeleteBannersBtn" onclick="handleBulkDeleteBanners()" class="btn-modern" style="display: none; background-color: #dc2626; color: white; border: 1px solid #dc2626; height: 42px; padding: 0 16px; border-radius: 8px; font-weight: 700; gap: 6px; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
                Xóa mục đã chọn (<span id="selectedBannersCountText">0</span>)
            </button>
        </div>
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
                        <th style="width: 40px; text-align: center;"><input type="checkbox" id="selectAllBanners" onchange="toggleSelectAllBanners(this)" style="width: 16px; height: 16px; cursor: pointer;"></th>
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
                            <td style="text-align: center;"><input type="checkbox" class="banner-select-checkbox" value="{{ $banner->id }}" onchange="updateBannerBulkDeleteState()" style="width: 16px; height: 16px; cursor: pointer; accent-color: var(--primary-color);"></td>
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

@section('scripts')
<script>
    window.toggleSelectAllBanners = function(selectAllCheckbox) {
        const checkboxes = document.querySelectorAll('.banner-select-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        updateBannerBulkDeleteState();
    };

    window.updateBannerBulkDeleteState = function() {
        const checkboxes = document.querySelectorAll('.banner-select-checkbox:checked');
        const count = checkboxes.length;
        const btn = document.getElementById('bulkDeleteBannersBtn');
        const countText = document.getElementById('selectedBannersCountText');
        const selectAllCheckbox = document.getElementById('selectAllBanners');
        
        if (btn) {
            if (count > 0) {
                btn.style.display = 'inline-flex';
                countText.textContent = count;
            } else {
                btn.style.display = 'none';
            }
        }
        
        const allVisible = document.querySelectorAll('.banner-select-checkbox');
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = allVisible.length > 0 && allVisible.length === count;
        }
    };

    window.handleBulkDeleteBanners = async function() {
        const checkboxes = document.querySelectorAll('.banner-select-checkbox:checked');
        const selectedIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
        
        if (selectedIds.length === 0) return;
        
        const msg = `Bạn có chắc chắn muốn XÓA VĨNH VIỄN ${selectedIds.length} biểu ngữ đã chọn khỏi hệ thống?\nHành động này không thể khôi phục.`;
        if (!await window.AppDialog.confirm(msg)) {
            return;
        }
        
        const toastId = window.AppDialog.toast('Đang thực hiện xóa biểu ngữ hàng loạt...', 'info');
        
        try {
            const response = await fetch('{{ route("admin.banners.bulk-destroy") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    ids: selectedIds
                })
            });
            
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Lỗi khi xóa biểu ngữ hàng loạt.');
            }
            
            window.AppDialog.toast(data.message || 'Đã xóa hàng loạt biểu ngữ thành công.', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 800);
            
        } catch (error) {
            window.AppDialog.toast(error.message, 'error');
        }
    };
</script>
@endsection
