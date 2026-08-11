@extends('vaccine::layouts.admin')

@section('title', 'Quản lý Vắc Xin - Medicare')
@section('page_title', 'Danh Mục Sản Phẩm Tiêm Chủng')

@section('styles')
<style>
    /* Filter Bar Responsive Grid */
    .vaccine-filter-form {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
        align-items: flex-end;
    }
    .filter-group-search {
        flex: 1 1 240px;
    }
    .filter-group-select {
        flex: 0 1 140px;
    }
    .filter-group-buttons {
        display: flex;
        gap: 8px;
    }
    @media (max-width: 768px) {
        .filter-group-search {
            flex: 1 1 100%;
        }
        .filter-group-select {
            flex: 1 1 calc(50% - 6px);
        }
        .filter-group-buttons {
            flex: 1 1 100%;
            margin-top: 6px;
        }
        .filter-group-buttons button, .filter-group-buttons a {
            flex: 1;
            justify-content: center;
        }
    }
    
    .category-tag-modern {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #475569;
        font-size: 12.5px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        white-space: nowrap;
    }
</style>
@endsection

@section('admin_content')
<div class="card-modern">
    @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom:20px; padding:14px; border-radius:8px; background:#fef2f2; color:#991b1b; border:1px solid #fecaca;">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    {{-- Header: Title + Nút thêm --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
        <h2 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0;">
            Danh sách Vắc xin 
            <span style="font-size: 14px; font-weight: 400; color: var(--text-muted);">({{ $vaccines->total() }} {{ !empty($selectedCenterId) ? 'sản phẩm tại chi nhánh' : 'vắc xin toàn hệ thống' }})</span>
        </h2>
        @if($isSuperAdmin ?? false)
            <a href="{{ route('admin.vaccines.create', $selectedCenterId ? ['center_id' => $selectedCenterId] : []) }}" class="btn-modern btn-modern-primary">
                <i data-lucide="plus-circle"></i> Thêm Vắc xin Mới
            </a>
        @endif
    </div>

    {{-- Thanh tìm kiếm & lọc --}}
    <form method="GET" action="{{ route('admin.vaccines.index') }}" class="vaccine-filter-form">
        {{-- Tìm kiếm --}}
        <div class="filter-group-search">
            <label class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Tìm kiếm</label>
            <div style="position: relative;">
                <i data-lucide="search" style="width: 16px; height: 16px; position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên vắc xin, bệnh phòng, hãng SX..." class="form-control-modern" style="padding-left: 36px;">
            </div>
        </div>

        @if(isset($centers) && ($isSuperAdmin ?? false))
        <div class="filter-group-select">
            <label class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Chi nhánh</label>
            <select name="center_id" class="form-control-modern">
                <option value="" {{ $selectedCenterId === null ? 'selected' : '' }}>Tất cả chi nhánh</option>
                @foreach($centers as $center)
                    <option value="{{ $center->id }}" {{ (string) $selectedCenterId === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="filter-group-select">
            <label for="min_quantity" class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Số lượng từ</label>
            <input id="min_quantity" type="number" name="min_quantity" value="{{ request('min_quantity') }}" min="0" placeholder="Tối thiểu" class="form-control-modern">
        </div>

        <div class="filter-group-select">
            <label for="max_quantity" class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Số lượng đến</label>
            <input id="max_quantity" type="number" name="max_quantity" value="{{ request('max_quantity') }}" min="0" placeholder="Tối đa" class="form-control-modern">
        </div>



        {{-- Lọc danh mục --}}
        @if(isset($categories) && $categories->count())
        <div class="filter-group-select">
            <label class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Nhóm bệnh</label>
            <select name="category" class="form-control-modern">
                <option value="">Tất cả</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- Nút hành động --}}
        <div class="filter-group-buttons">
            <button type="submit" class="btn-modern btn-modern-primary" style="padding: 10px 18px; border-radius: 8px;">
                <i data-lucide="filter" style="width: 14px; height: 14px;"></i> Lọc
            </button>
            @if(request()->hasAny(['search', 'stock_status', 'category', 'featured', 'center_id', 'min_quantity', 'max_quantity']))
            <a href="{{ route('admin.vaccines.index') }}" class="btn-modern btn-modern-secondary" style="padding: 10px 18px; border-radius: 8px;">
                <i data-lucide="x" style="width: 14px; height: 14px;"></i> Xóa lọc
            </a>
            @endif
        </div>
    </form>

    <div id="table-container">
        @include('vaccine::admin.vaccines._table')
    </div>
</div>

@if($isSuperAdmin ?? false)
<!-- Modal Xem Tồn Kho Chi Nhánh -->
<div id="branchesStockModal" style="display: none; position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: white; border-radius: 12px; max-width: 600px; width: 100%; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
        <!-- Header -->
        <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <h3 id="modalVaccineName" style="font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0;">Tồn kho tại các chi nhánh</h3>
            <button type="button" id="closeBranchesStockModal" style="background: none; border: none; font-size: 24px; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">
                &times;
            </button>
        </div>
        <!-- Content -->
        <div style="padding: 20px; overflow-y: auto; flex-grow: 1;">
            <div id="modalLoading" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 0; color: #64748b;">
                <div style="border: 3px solid #f3f3f3; border-top: 3px solid var(--primary-color); border-radius: 50%; width: 24px; height: 24px; animation: spin-modal 1s linear infinite; margin-bottom: 12px;"></div>
                <span>Đang tải dữ liệu...</span>
            </div>
            <div id="modalTableWrapper" style="display: none;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; color: #475569; font-weight: 600;">
                            <th style="padding: 10px 8px;">Tên chi nhánh</th>
                            <th style="padding: 10px 8px; text-align: right; width: 130px;">Giá bán</th>
                            <th style="padding: 10px 8px; text-align: center; width: 90px;">Số lượng</th>
                            <th style="padding: 10px 8px; text-align: center; width: 110px;">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody id="modalBranchesList">
                        <!-- Điền bằng JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes spin-modal {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('branchesStockModal');
        const modalVaccineName = document.getElementById('modalVaccineName');
        const modalBranchesList = document.getElementById('modalBranchesList');
        const modalLoading = document.getElementById('modalLoading');
        const modalTableWrapper = document.getElementById('modalTableWrapper');
        const closeBtn = document.getElementById('closeBranchesStockModal');

        function formatMoney(amount) {
            if (amount === null || amount === undefined) return '—';
            return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
        }

        function escapeHtml(value) {
            const element = document.createElement('div');
            element.textContent = String(value ?? '');
            return element.innerHTML;
        }

        // Use event delegation for branch stock modal trigger button so it works after AJAX DOM replaces
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-view-branches-stock');
            if (!btn) return;

            const stockUrl = btn.dataset.stockUrl;

            modal.style.display = 'flex';
            modalLoading.style.display = 'flex';
            modalTableWrapper.style.display = 'none';
            modalVaccineName.textContent = 'Đang tải thông tin vắc xin...';

            fetch(stockUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Lỗi tải dữ liệu');
                    }
                    return response.json();
                })
                .then(data => {
                    modalVaccineName.textContent = `Tồn kho: ${data.vaccine_name}`;

                    let html = '';
                    data.branches.forEach(branch => {
                        let statusClass = 'badge-modern-danger';
                        let statusText = 'Hết hàng';

                        if (branch.is_active) {
                            if (branch.stock_status === 'available') {
                                statusClass = 'badge-modern-success';
                                statusText = 'Đầy đủ';
                            } else if (branch.stock_status === 'limited') {
                                statusClass = 'badge-modern-warning';
                                statusText = 'Còn ít';
                            }
                        } else {
                            statusText = 'Tạm ngưng';
                        }

                        const priceHtml = branch.sale_price && branch.sale_price < branch.price
                            ? `<span style="text-decoration: line-through; color: #94a3b8; font-size: 12px; display: block;">${formatMoney(branch.price)}</span>
                               <span style="color: #dc2626; font-weight: 600;">${formatMoney(branch.sale_price)}</span>`
                            : `<span style="font-weight: 500;">${formatMoney(branch.price)}</span>`;

                        const qtyText = branch.is_active ? branch.stock_quantity : '—';

                        html += `
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 8px; font-weight: 500; color: #1e293b;">${escapeHtml(branch.center_name)}</td>
                                <td style="padding: 12px 8px; text-align: right; vertical-align: middle;">${priceHtml}</td>
                                <td style="padding: 12px 8px; text-align: center; font-weight: 600; color: #334155;">${qtyText}</td>
                                <td style="padding: 12px 8px; text-align: center; vertical-align: middle;">
                                    <span class="badge-modern ${statusClass}">${statusText}</span>
                                </td>
                            </tr>
                        `;
                    });

                    modalBranchesList.innerHTML = html;
                    modalLoading.style.display = 'none';
                    modalTableWrapper.style.display = 'block';
                })
                .catch(err => {
                    console.error(err);
                    modalVaccineName.textContent = 'Lỗi!';
                    modalBranchesList.innerHTML = `
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px; color: #dc2626;">
                                Không thể tải dữ liệu tồn kho. Vui lòng kiểm tra lại.
                            </td>
                        </tr>
                    `;
                    modalLoading.style.display = 'none';
                    modalTableWrapper.style.display = 'block';
                });
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });
        }

        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }
    });
</script>
@endif
@endsection

@section('scripts')
    @include('vaccine::admin.partials._ajax_filter_js')
@endsection
