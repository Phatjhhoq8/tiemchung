@extends('vaccine::layouts.admin')

@section('title', 'Quản Lý Bài Viết & Tin Tức Y Tế')
@section('page_title', 'Quản Lý Bài Viết & Tin Tức Y Tế')

@section('admin_content')
<div class="card-modern">
    
    {{-- Header: Title + Nút thêm --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
        <div>
            <h2 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0;">
                Danh sách bài viết tin tức
                <span style="font-size: 14px; font-weight: 400; color: var(--text-muted);">({{ $articles->total() }} bài viết)</span>
            </h2>
        </div>
        <a href="{{ route('admin.articles.create') }}" class="btn-modern btn-modern-primary">
            <i data-lucide="plus-circle"></i> Thêm bài viết mới
        </a>
    </div>

    <form method="GET" action="{{ route('admin.articles.index') }}" class="vaccine-filter-form" style="display:flex; gap:12px; align-items:flex-end; margin-bottom:20px; flex-wrap:wrap;">
        <div style="flex:2 1 260px;">
            <label class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Tìm kiếm</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tiêu đề, tóm tắt..." class="form-control-modern">
        </div>
        <div style="flex:1 1 150px;">
            <label class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Chuyên mục</label>
            <input type="text" name="category" value="{{ request('category') }}" placeholder="Chuyên mục..." class="form-control-modern">
        </div>
        <div style="flex:1 1 150px;">
            <label class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Trạng thái</label>
            <select name="is_published" class="form-control-modern">
                <option value="">Tất cả</option>
                <option value="1" {{ request('is_published') === '1' ? 'selected' : '' }}>Hiển thị</option>
                <option value="0" {{ request('is_published') === '0' ? 'selected' : '' }}>Ẩn</option>
            </select>
        </div>
        <div style="display: flex; gap: 8px; align-items: flex-end;">
            <button type="submit" class="btn-modern btn-modern-primary" style="height: 42px;">Lọc</button>
            @if(request()->hasAny(['search', 'category', 'is_published']))
                <a href="{{ route('admin.articles.index') }}" class="btn-modern btn-modern-secondary" style="height: 42px; display: inline-flex; align-items: center; text-decoration: none;">Xóa lọc</a>
            @endif
            <button type="button" id="bulkDeleteArticlesBtn" onclick="handleBulkDeleteArticles()" class="btn-modern" style="display: none; background-color: #dc2626; color: white; border: 1px solid #dc2626; height: 42px; padding: 0 16px; border-radius: 8px; font-weight: 700; gap: 6px; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
                Xóa mục đã chọn (<span id="selectedArticlesCountText">0</span>)
            </button>
        </div>
    </form>

    @if(session('success'))
        <div style="background-color: #dcfce7; color: #15803d; padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 600;">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive-modern">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;"><input type="checkbox" id="selectAllArticles" onchange="toggleSelectAllArticles(this)" style="width: 16px; height: 16px; cursor: pointer;"></th>
                    <th style="width: 70px; text-align: center;">STT</th>
                    <th style="width: 80px; text-align: center;">#ID</th>
                    <th style="width: 130px; text-align: center;">Hình ảnh</th>
                    <th>Tiêu đề bài viết</th>
                    <th style="width: 180px;">Chuyên mục</th>
                    <th style="text-align: center; width: 130px;">Trạng thái</th>
                    <th style="text-align: right; width: 200px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles as $article)
                    <tr>
                        <td style="text-align: center;"><input type="checkbox" class="article-select-checkbox" value="{{ $article->id }}" onchange="updateArticleBulkDeleteState()" style="width: 16px; height: 16px; cursor: pointer; accent-color: var(--primary-color);"></td>
                        <td style="text-align: center; font-weight: 700; color: var(--text-muted);">
                            {{ $articles->firstItem() + $loop->index }}
                        </td>
                        <td style="text-align: center; font-weight: 700; color: var(--text-muted);">#{{ $article->id }}</td>
                        <td style="text-align: center;">
                            <img src="{{ asset('images/vaccines/' . ($article->image ?: 'default_vaccine.jpg')) }}" alt="{{ $article->title }}" style="width: 90px; height: 60px; object-fit: cover; border-radius: 6px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);">
                        </td>
                        <td>
                            <strong style="color: var(--text-primary); display: block;">{{ $article->title }}</strong>
                            <span style="font-size: 12px; color: var(--text-muted);">Lượt xem: {{ number_format($article->views ?? 0) }}</span>
                        </td>
                        <td>
                            <span class="badge-modern badge-modern-info">{{ $article->category }}</span>
                        </td>
                        <td style="text-align: center;">
                            @if($article->is_published)
                                <span class="badge-modern badge-modern-success">Hiển thị</span>
                            @else
                                <span class="badge-modern badge-modern-danger">Đang ẩn</span>
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <div style="display: inline-flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                {{-- Nút Ẩn / Hiện (Ẩn mềm) --}}
                                <form action="{{ route('admin.articles.toggle-status', $article->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    @if($article->is_published)
                                        <button type="submit" class="btn-action-sm btn-action-warning" title="Bấm để ẩn bài viết khỏi trang chủ (Ẩn mềm)">Ẩn</button>
                                    @else
                                        <button type="submit" class="btn-action-sm btn-action-success" title="Bấm để hiển thị lại bài viết lên trang chủ">Hiện</button>
                                    @endif
                                </form>

                                {{-- Nút Chỉnh sửa --}}
                                <a href="{{ route('admin.articles.edit', $article->id) }}" class="btn-action-sm">Sửa</a>

                                {{-- Nút Xóa cứng (Xóa vĩnh viễn) --}}
                                <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" data-confirm="CẢNH BÁO: Hành động này sẽ XÓA VĨNH VIỄN bài viết khỏi hệ thống và không thể khôi phục. Bạn có chắc chắn muốn xóa cứng?" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-sm btn-action-danger" title="Xóa vĩnh viễn bài viết khỏi CSDL">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 40px; text-align: center; color: var(--text-light);">Chưa có bài viết nào. Hãy bấm "Thêm bài viết mới" để tạo bài viết đầu tiên.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($articles->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
            {{ $articles->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    window.toggleSelectAllArticles = function(selectAllCheckbox) {
        const checkboxes = document.querySelectorAll('.article-select-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        updateArticleBulkDeleteState();
    };

    window.updateArticleBulkDeleteState = function() {
        const checkboxes = document.querySelectorAll('.article-select-checkbox:checked');
        const count = checkboxes.length;
        const btn = document.getElementById('bulkDeleteArticlesBtn');
        const countText = document.getElementById('selectedArticlesCountText');
        const selectAllCheckbox = document.getElementById('selectAllArticles');
        
        if (btn) {
            if (count > 0) {
                btn.style.display = 'inline-flex';
                countText.textContent = count;
            } else {
                btn.style.display = 'none';
            }
        }
        
        const allVisible = document.querySelectorAll('.article-select-checkbox');
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = allVisible.length > 0 && allVisible.length === count;
        }
    };

    window.handleBulkDeleteArticles = async function() {
        const checkboxes = document.querySelectorAll('.article-select-checkbox:checked');
        const selectedIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
        
        if (selectedIds.length === 0) return;
        
        const msg = `Bạn có chắc chắn muốn XÓA VĨNH VIỄN ${selectedIds.length} bài viết đã chọn khỏi hệ thống?\nHành động này không thể khôi phục.`;
        if (!await window.AppDialog.confirm(msg)) {
            return;
        }
        
        const toastId = window.AppDialog.toast('Đang thực hiện xóa bài viết hàng loạt...', 'info');
        
        try {
            const response = await fetch('{{ route("articles.bulk-destroy") }}', {
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
                throw new Error(data.message || 'Lỗi khi xóa bài viết hàng loạt.');
            }
            
            window.AppDialog.toast(data.message || 'Đã xóa hàng loạt bài viết thành công.', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 800);
            
        } catch (error) {
            window.AppDialog.toast(error.message, 'error');
        }
    };
</script>
@endsection
