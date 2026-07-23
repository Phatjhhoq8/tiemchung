@extends('vaccine::layouts.admin')

@section('title', 'Quản lý Vắc Xin - Medicare Cờ Đỏ')
@section('page_title', 'Danh Mục Vắc Xin & Gói Vắc Xin')

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

    /* Table Design System */
    .admin-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        text-align: left;
        font-size: 14px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
    }
    .admin-table th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 16px;
        border-bottom: 2px solid #e2e8f0;
        vertical-align: middle;
        white-space: nowrap;
    }
    .admin-table td {
        padding: 14px 16px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        background-color: #ffffff;
    }
    .admin-table tr:last-child td {
        border-bottom: none;
    }
    .admin-table tr:hover td {
        background-color: #f8fafc;
    }

    /* Badges & Tags Standardized System */
    .badge-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 24px;
        padding: 0 10px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        white-space: nowrap;
        line-height: 1;
    }
    .badge-pill-package {
        background-color: #fffbebf5;
        color: #d97706;
        border: 1px solid #fef3c7;
    }
    .badge-pill-single {
        background-color: #f0f9ff;
        color: #0284c7;
        border: 1px solid #e0f2fe;
    }

    .category-tag {
        display: inline-flex;
        align-items: center;
        height: 24px;
        padding: 0 10px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #475569;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 26px;
        padding: 0 12px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
        line-height: 1;
    }
    .status-available {
        background-color: #def7ec;
        color: #03543f;
        border: 1px solid #bcf0da;
    }
    .status-limited {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .status-out {
        background-color: #fde8e8;
        color: #9b1c1c;
        border: 1px solid #fbd5d5;
    }

    .btn-action-sm {
        height: 32px;
        padding: 0 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        cursor: pointer;
        text-decoration: none;
        box-sizing: border-box;
        transition: all 0.2s ease;
        line-height: 1;
    }
</style>
@endsection

@section('admin_content')
<div style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; padding: 30px;">
    {{-- Header: Title + Nút thêm --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <h2 style="font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 700; color: #1e293b; margin: 0;">
            Danh sách Vắc xin 
            <span style="font-size: 14px; font-weight: 400; color: #64748b;">({{ $vaccines->total() }} sản phẩm)</span>
        </h2>
        <a href="{{ route('admin.vaccines.create') }}" class="btn-primary" style="padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus-circle"></i> Thêm Vắc xin Mới
        </a>
    </div>

    {{-- Thanh tìm kiếm & lọc --}}
    <form method="GET" action="{{ route('admin.vaccines.index') }}" class="vaccine-filter-form">
        {{-- Tìm kiếm --}}
        <div class="filter-group-search">
            <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">Tìm kiếm</label>
            <div style="position: relative;">
                <i data-lucide="search" style="width: 16px; height: 16px; position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên vắc xin, bệnh phòng, hãng SX..." style="width: 100%; padding: 10px 12px 10px 36px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; font-size: 14px;">
            </div>
        </div>

        {{-- Lọc phân loại --}}
        <div class="filter-group-select">
            <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">Phân loại</label>
            <select name="type" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #fff; font-size: 14px;">
                <option value="">Tất cả</option>
                <option value="single" {{ request('type') === 'single' ? 'selected' : '' }}>Vắc xin lẻ</option>
                <option value="package" {{ request('type') === 'package' ? 'selected' : '' }}>Gói vắc xin</option>
            </select>
        </div>

        {{-- Lọc tình trạng kho --}}
        <div class="filter-group-select">
            <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">Tình trạng</label>
            <select name="stock_status" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #fff; font-size: 14px;">
                <option value="">Tất cả</option>
                <option value="available" {{ request('stock_status') === 'available' ? 'selected' : '' }}>Đầy đủ</option>
                <option value="limited" {{ request('stock_status') === 'limited' ? 'selected' : '' }}>Còn ít</option>
                <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Hết hàng</option>
            </select>
        </div>

        {{-- Lọc danh mục --}}
        @if(isset($categories) && $categories->count())
        <div class="filter-group-select">
            <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">Danh mục</label>
            <select name="category" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #fff; font-size: 14px;">
                <option value="">Tất cả</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- Nút hành động --}}
        <div class="filter-group-buttons">
            <button type="submit" style="padding: 10px 18px; border-radius: 8px; border: 1px solid #3b82f6; background: #3b82f6; color: #ffffff; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-size: 14px;">
                <i data-lucide="filter" style="width: 14px; height: 14px;"></i> Lọc
            </button>
            @if(request()->hasAny(['search', 'type', 'stock_status', 'category', 'featured']))
            <a href="{{ route('admin.vaccines.index') }}" style="padding: 10px 18px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 14px;">
                <i data-lucide="x" style="width: 14px; height: 14px;"></i> Xóa lọc
            </a>
            @endif
        </div>
    </form>

    @if($vaccines->isEmpty())
        <div style="text-align: center; padding: 40px; color: #64748b;">
            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; color: #94a3b8;"></i>
            <p>Không tìm thấy vắc xin nào
                @if(request()->hasAny(['search', 'type', 'stock_status', 'category']))
                    phù hợp với bộ lọc.
                @else
                    trong hệ thống.
                @endif
            </p>
        </div>
    @else
        <div class="table-responsive" style="overflow-x: auto; margin-bottom: 24px;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 45px; text-align: center;">#</th>
                        <th style="width: 110px;">Phân loại</th>
                        <th>Tên Vắc Xin & Chi Tiết</th>
                        <th>Danh mục</th>
                        <th style="width: 100px;">Nguồn gốc</th>
                        <th style="width: 75px; text-align: center;">Mũi tiêm</th>
                        <th style="width: 110px;">Giá bán lẻ</th>
                        <th style="width: 110px;">Giá ưu đãi</th>
                        <th style="width: 110px; text-align: center;">Tình trạng</th>
                        <th style="width: 180px; text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vaccines as $index => $vac)
                        <tr>
                            {{-- STT --}}
                            <td style="text-align: center; color: #94a3b8; font-size: 13px; font-weight: 500;">
                                {{ $vaccines->firstItem() + $index }}
                            </td>

                            {{-- Phân loại --}}
                            <td>
                                <div style="display: inline-flex; align-items: center; gap: 6px;">
                                    <span class="badge-pill {{ $vac->type === 'package' ? 'badge-pill-package' : 'badge-pill-single' }}">
                                        {{ $vac->type === 'package' ? 'Gói' : 'Lẻ' }}
                                    </span>
                                    @if($vac->is_featured)
                                        <span title="Nổi bật" style="font-size: 14px;">⭐</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Tên + Hãng SX --}}
                            <td>
                                <div style="font-weight: 700; color: #1e293b; font-size: 14px;">{{ $vac->name }}</div>
                                @if($vac->manufacturer)
                                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">{{ $vac->manufacturer }}</div>
                                @endif
                                @if($vac->dosage)
                                    <div style="font-size: 12px; color: #94a3b8;">{{ $vac->dosage }}</div>
                                @endif
                            </td>

                            {{-- Danh mục --}}
                            <td>
                                @if($vac->category)
                                    <span class="category-tag">{{ $vac->category }}</span>
                                @else
                                    <span style="color: #cbd5e1;">—</span>
                                @endif
                            </td>

                            {{-- Nguồn gốc --}}
                            <td style="font-size: 13px; font-weight: 500;">{{ $vac->origin ?: '—' }}</td>

                            {{-- Mũi tiêm --}}
                            <td style="text-align: center; font-weight: 700; color: #1e293b;">{{ $vac->doses }}</td>

                            {{-- Giá bán lẻ --}}
                            <td style="font-weight: 600; white-space: nowrap;
                                @if($vac->hasSalePrice()) text-decoration: line-through; color: #94a3b8; font-size: 12px;
                                @else color: var(--primary-color, #c8102e); font-size: 14px; @endif">
                                {{ number_format($vac->price, 0, ',', '.') }} đ
                            </td>

                            {{-- Giá ưu đãi --}}
                            <td style="font-weight: 700; white-space: nowrap;">
                                @if($vac->hasSalePrice())
                                    <span style="color: #dc2626; font-size: 14px;">{{ number_format($vac->sale_price, 0, ',', '.') }} đ</span>
                                @else
                                    <span style="color: #cbd5e1;">—</span>
                                @endif
                            </td>

                            {{-- Tình trạng kho --}}
                            <td style="text-align: center;">
                                <span class="status-badge 
                                    @if($vac->stock_status === 'available') status-available
                                    @elseif($vac->stock_status === 'limited') status-limited
                                    @else status-out @endif">
                                    {{ $vac->getStockLabel() }}
                                </span>
                            </td>

                            {{-- Hành động --}}
                            <td style="text-align: center;">
                                <div style="display: inline-flex; gap: 6px; align-items: center; justify-content: center;">
                                    <form action="{{ route('admin.vaccines.toggle-featured', $vac->id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn-action-sm" title="{{ $vac->is_featured ? 'Bỏ nổi bật' : 'Đánh dấu NỔI BẬT trang chủ' }}" style="border: 1px solid {{ $vac->is_featured ? '#fef3c7' : '#e2e8f0' }}; background: {{ $vac->is_featured ? '#fffbeb' : '#ffffff' }}; color: {{ $vac->is_featured ? '#d97706' : '#94a3b8' }};">
                                            ⭐ {{ $vac->is_featured ? 'Bỏ Nổi Bật' : 'Nổi Bật' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.vaccines.edit', $vac->id) }}" class="btn-action-sm" style="border: 1px solid #cbd5e1; background: #ffffff; color: #475569;">
                                        <i data-lucide="edit-3" style="width: 13px; height: 13px;"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.vaccines.destroy', $vac->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vắc xin này?')" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-sm" style="border: 1px solid #fbd5d5; background: #fff5f5; color: #c8102e;">
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
        <div class="admin-pagination" style="display: flex; justify-content: center;">
            {{ $vaccines->links() }}
        </div>
    @endif
</div>
@endsection
