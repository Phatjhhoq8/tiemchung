@extends('vaccine::layouts.admin')

@section('title', 'Quản lý Chi Nhánh - Medicare')
@section('page_title', 'Hệ Thống Chi Nhánh Tiêm Chủng')

@section('admin_content')
<div class="card-modern">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <h2 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0;">Danh sách các chi nhánh tiêm chủng</h2>
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

        <div style="display: flex; gap: 8px; align-items: flex-end;">
            <button type="submit" class="btn-modern btn-modern-primary" style="height: 42px;">Lọc</button>
            @if(request()->hasAny(['search', 'is_active']))
                <a href="{{ route('admin.centers.index') }}" class="btn-modern btn-modern-secondary" style="height: 42px; display: inline-flex; align-items: center; text-decoration: none;">Xóa lọc</a>
            @endif
            <button type="button" id="bulkDeleteCentersBtn" onclick="handleBulkDeleteCenters()" class="btn-modern" style="display: none; background-color: #dc2626; color: white; border: 1px solid #dc2626; height: 42px; padding: 0 16px; border-radius: 8px; font-weight: 700; gap: 6px; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
                Xóa mục đã chọn (<span id="selectedCentersCountText">0</span>)
            </button>
        </div>
    </form>

    <div id="table-container">
        @include('vaccine::admin.centers._table')
    </div>
</div>
@endsection

@section('scripts')
    @include('vaccine::admin.partials._ajax_filter_js')
    <script>
        window.toggleSelectAllCenters = function(selectAllCheckbox) {
            const checkboxes = document.querySelectorAll('.center-select-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateCenterBulkDeleteState();
        };

        window.updateCenterBulkDeleteState = function() {
            const checkboxes = document.querySelectorAll('.center-select-checkbox:checked');
            const count = checkboxes.length;
            const btn = document.getElementById('bulkDeleteCentersBtn');
            const countText = document.getElementById('selectedCentersCountText');
            const selectAllCheckbox = document.getElementById('selectAllCenters');
            
            if (btn) {
                if (count > 0) {
                    btn.style.display = 'inline-flex';
                    countText.textContent = count;
                } else {
                    btn.style.display = 'none';
                }
            }
            
            const allVisible = document.querySelectorAll('.center-select-checkbox');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allVisible.length > 0 && allVisible.length === count;
            }
        };

        window.handleBulkDeleteCenters = async function() {
            const checkboxes = document.querySelectorAll('.center-select-checkbox:checked');
            const selectedIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
            
            if (selectedIds.length === 0) return;
            
            const msg = `Bạn có chắc chắn muốn XÓA VĨNH VIỄN ${selectedIds.length} chi nhánh đã chọn khỏi hệ thống?\nHành động này không thể khôi phục.`;
            if (!await window.AppDialog.confirm(msg)) {
                return;
            }
            
            const toastId = window.AppDialog.toast('Đang thực hiện xóa chi nhánh hàng loạt...', 'info');
            
            try {
                const response = await fetch('{{ route("admin.centers.bulk-destroy") }}', {
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
                    throw new Error(data.message || 'Lỗi khi xóa chi nhánh hàng loạt.');
                }
                
                window.AppDialog.toast(data.message || 'Đã xóa hàng loạt chi nhánh thành công.', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 800);
                
            } catch (error) {
                window.AppDialog.toast(error.message, 'error');
            }
        };
    </script>
@endsection
