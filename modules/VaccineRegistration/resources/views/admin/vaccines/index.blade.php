@extends('vaccine::layouts.admin')

@section('title', 'Quản lý Vắc Xin - Medicare Cờ Đỏ')
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
        flex: 0 1 160px;
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
    {{-- Header: Title + Nút thêm --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
        <h2 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0;">
            Danh sách Vắc xin 
            <span style="font-size: 14px; font-weight: 400; color: var(--text-muted);">({{ $vaccines->total() }} sản phẩm)</span>
        </h2>
        <a href="{{ route('admin.vaccines.create') }}" class="btn-modern btn-modern-primary">
            <i data-lucide="plus-circle"></i> Thêm Vắc xin Mới
        </a>
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

        {{-- Lọc phân loại --}}
        <div class="filter-group-select">
            <label class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Phân loại</label>
            <select name="type" class="form-control-modern" style="background-image: none;">
                <option value="">Tất cả</option>
                <option value="single" {{ request('type') === 'single' ? 'selected' : '' }}>Vắc xin lẻ</option>
                <option value="package" {{ request('type') === 'package' ? 'selected' : '' }}>Gói vắc xin</option>
            </select>
        </div>

        {{-- Lọc tình trạng kho --}}
        <div class="filter-group-select">
            <label class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Tình trạng</label>
            <select name="stock_status" class="form-control-modern" style="background-image: none;">
                <option value="">Tất cả</option>
                <option value="available" {{ request('stock_status') === 'available' ? 'selected' : '' }}>Đầy đủ</option>
                <option value="limited" {{ request('stock_status') === 'limited' ? 'selected' : '' }}>Còn ít</option>
                <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Hết hàng</option>
            </select>
        </div>

        {{-- Lọc danh mục --}}
        @if(isset($categories) && $categories->count())
        <div class="filter-group-select">
            <label class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Danh mục</label>
            <select name="category" class="form-control-modern" style="background-image: none;">
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
            @if(request()->hasAny(['search', 'type', 'stock_status', 'category', 'featured']))
            <a href="{{ route('admin.vaccines.index') }}" class="btn-modern btn-modern-secondary" style="padding: 10px 18px; border-radius: 8px;">
                <i data-lucide="x" style="width: 14px; height: 14px;"></i> Xóa lọc
            </a>
            @endif
        </div>
    </form>

    @if($vaccines->isEmpty())
        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; color: var(--text-light);"></i>
            <p>Không tìm thấy vắc xin nào
                @if(request()->hasAny(['search', 'type', 'stock_status', 'category']))
                    phù hợp với bộ lọc.
                @else
                    trong hệ thống.
                @endif
            </p>
        </div>
    @else
        <div class="table-responsive-modern">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 45px; text-align: center;">#</th>
                        <th style="width: 120px;">Phân loại</th>
                        <th>Tên Vắc Xin & Chi Tiết</th>
                        <th>Danh mục</th>
                        <th style="width: 120px;">Nguồn gốc</th>
                        <th style="width: 90px; text-align: center;">Mũi tiêm</th>
                        <th style="width: 120px;">Giá bán lẻ</th>
                        <th style="width: 120px;">Giá ưu đãi</th>
                        <th style="width: 110px; text-align: center;">Tình trạng</th>
                        <th style="width: 200px; text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vaccines as $index => $vac)
                        <tr>
                            {{-- STT --}}
                            <td style="text-align: center; color: var(--text-light); font-weight: 600;">
                                {{ $vaccines->firstItem() + $index }}
                            </td>

                            {{-- Phân loại --}}
                            <td>
                                <div style="display: inline-flex; flex-direction: column; gap: 4px;">
                                    <span class="badge-modern {{ $vac->type === 'package' ? 'badge-modern-warning' : 'badge-modern-info' }}">
                                        {{ $vac->type === 'package' ? 'Gói' : 'Lẻ' }}
                                    </span>
                                    @if($vac->is_featured)
                                        <span title="Nổi bật" style="font-size: 11px; font-weight: 700; color: #d97706; background-color: #fffbeb; border: 1px solid #fde68a; padding: 2px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px; width: fit-content;">
                                            <i data-lucide="star" style="width: 9px; height: 9px; fill: #d97706;"></i> Nổi bật
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Tên + Hãng SX --}}
                            <td>
                                <div style="font-weight: 700; color: var(--text-primary); font-size: 14.5px; font-family: var(--font-display);">{{ $vac->name }}</div>
                                @if($vac->manufacturer)
                                    <div style="font-size: 12.5px; color: var(--text-muted); margin-top: 2px;">{{ $vac->manufacturer }}</div>
                                @endif
                                @if($vac->dosage)
                                    <div style="font-size: 12px; color: var(--text-light);">{{ $vac->dosage }}</div>
                                @endif
                            </td>

                            {{-- Danh mục --}}
                            <td>
                                @if($vac->category)
                                    <span class="category-tag-modern">{{ $vac->category }}</span>
                                @else
                                    <span style="color: var(--text-light);">—</span>
                                @endif
                            </td>

                            {{-- Nguồn gốc --}}
                            <td style="font-weight: 500;">{{ $vac->origin ?: '—' }}</td>

                            {{-- Mũi tiêm --}}
                            <td style="text-align: center; font-weight: 700; color: var(--text-primary);">{{ $vac->doses }}</td>

                            {{-- Giá bán lẻ --}}
                            <td style="font-weight: 600; white-space: nowrap;
                                @if($vac->hasSalePrice()) text-decoration: line-through; color: var(--text-light); font-size: 12.5px;
                                @else color: var(--primary-color); font-size: 14.5px; @endif">
                                {{ number_format($vac->price, 0, ',', '.') }} đ
                            </td>

                            {{-- Giá ưu đãi --}}
                            <td style="font-weight: 700; white-space: nowrap;">
                                @if($vac->hasSalePrice())
                                    <span style="color: #dc2626; font-size: 14.5px;">{{ number_format($vac->sale_price, 0, ',', '.') }} đ</span>
                                @else
                                    <span style="color: var(--text-light);">—</span>
                                @endif
                            </td>

                            {{-- Tình trạng kho --}}
                            <td style="text-align: center;">
                                <span class="badge-modern 
                                    @if($vac->stock_status === 'available') badge-modern-success
                                    @elseif($vac->stock_status === 'limited') badge-modern-warning
                                    @else badge-modern-danger @endif">
                                    {{ $vac->getStockLabel() }}
                                </span>
                            </td>

                            {{-- Hành động --}}
                            <td style="text-align: center;">
                                <div style="display: inline-flex; gap: 6px; align-items: center; justify-content: center;">
                                    <form action="{{ route('admin.vaccines.toggle-featured', $vac->id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn-action-sm" title="{{ $vac->is_featured ? 'Bỏ nổi bật' : 'Đánh dấu NỔI BẬT trang chủ' }}" style="border-color: {{ $vac->is_featured ? '#fde68a' : '#cbd5e1' }}; background-color: {{ $vac->is_featured ? '#fffbeb' : '#ffffff' }}; color: {{ $vac->is_featured ? '#d97706' : '#475569' }};">
                                            @if($vac->is_featured)
                                                <i data-lucide="star-off" style="width: 13px; height: 13px;"></i> Bỏ Nổi Bật
                                            @else
                                                <i data-lucide="star" style="width: 13px; height: 13px;"></i> Nổi Bật
                                            @endif
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.vaccines.edit', $vac->id) }}" class="btn-action-sm">
                                        <i data-lucide="edit-2" style="width: 13px; height: 13px;"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.vaccines.destroy', $vac->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vắc xin này?')" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-sm" style="border-color: #fecaca; background-color: #fef2f2; color: var(--primary-color);">
                                            <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Custom Pagination Links -->
        <div style="display: flex; justify-content: center; margin-top: 24px;">
            {{ $vaccines->links() }}
        </div>
    @endif
</div>
@endsection
