@extends('vaccine::layouts.admin')

@section('title', 'Quản lý Vắc Xin - Medicare Cờ Đỏ')
@section('page_title', 'Danh Mục Vắc Xin & Gói Vắc Xin')

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
    <form method="GET" action="{{ route('admin.vaccines.index') }}" style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; align-items: flex-end;">
        {{-- Tìm kiếm --}}
        <div style="flex: 1; min-width: 220px;">
            <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">Tìm kiếm</label>
            <div style="position: relative;">
                <i data-lucide="search" style="width: 16px; height: 16px; position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên vắc xin, bệnh phòng, hãng SX..." style="width: 100%; padding: 10px 12px 10px 36px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; font-size: 14px;">
            </div>
        </div>

        {{-- Lọc phân loại --}}
        <div style="min-width: 140px;">
            <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">Phân loại</label>
            <select name="type" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #fff; font-size: 14px;">
                <option value="">Tất cả</option>
                <option value="single" {{ request('type') === 'single' ? 'selected' : '' }}>Vắc xin lẻ</option>
                <option value="package" {{ request('type') === 'package' ? 'selected' : '' }}>Gói vắc xin</option>
            </select>
        </div>

        {{-- Lọc tình trạng kho --}}
        <div style="min-width: 140px;">
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
        <div style="min-width: 140px;">
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
        <div style="display: flex; gap: 8px;">
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
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                <thead>
                    <tr style="border-bottom: 2px solid #cbd5e1; color: #475569;">
                        <th style="padding: 12px 14px; font-weight: 600; width: 40px;">#</th>
                        <th style="padding: 12px 14px; font-weight: 600;">Phân loại</th>
                        <th style="padding: 12px 14px; font-weight: 600;">Tên vắc xin</th>
                        <th style="padding: 12px 14px; font-weight: 600;">Danh mục</th>
                        <th style="padding: 12px 14px; font-weight: 600;">Nguồn gốc</th>
                        <th style="padding: 12px 14px; font-weight: 600;">Mũi tiêm</th>
                        <th style="padding: 12px 14px; font-weight: 600;">Giá bán lẻ</th>
                        <th style="padding: 12px 14px; font-weight: 600;">Giá ưu đãi</th>
                        <th style="padding: 12px 14px; font-weight: 600;">Tình trạng</th>
                        <th style="padding: 12px 14px; font-weight: 600; text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vaccines as $index => $vac)
                        <tr style="border-bottom: 1px solid #e2e8f0; color: #334155; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                            {{-- STT --}}
                            <td style="padding: 12px 14px; color: #94a3b8; font-size: 13px;">
                                {{ $vaccines->firstItem() + $index }}
                            </td>

                            {{-- Phân loại --}}
                            <td style="padding: 12px 14px;">
                                <span style="padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; white-space: nowrap;
                                    @if($vac->type === 'package') background-color: #fef3c7; color: #d97706;
                                    @else background-color: #e0f2fe; color: #0369a1; @endif">
                                    {{ $vac->type === 'package' ? 'Gói' : 'Lẻ' }}
                                </span>
                                @if($vac->is_featured)
                                    <span title="Nổi bật" style="margin-left: 4px;">⭐</span>
                                @endif
                            </td>

                            {{-- Tên + Hãng SX --}}
                            <td style="padding: 12px 14px;">
                                <div style="font-weight: 700; color: #1e293b;">{{ $vac->name }}</div>
                                @if($vac->manufacturer)
                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">{{ $vac->manufacturer }}</div>
                                @endif
                                @if($vac->dosage)
                                    <div style="font-size: 12px; color: #94a3b8;">{{ $vac->dosage }}</div>
                                @endif
                            </td>

                            {{-- Danh mục --}}
                            <td style="padding: 12px 14px; font-size: 13px;">
                                @if($vac->category)
                                    <span style="padding: 2px 8px; border-radius: 4px; background: #f1f5f9; color: #475569; font-size: 12px;">{{ $vac->category }}</span>
                                @else
                                    <span style="color: #cbd5e1;">—</span>
                                @endif
                            </td>

                            {{-- Nguồn gốc --}}
                            <td style="padding: 12px 14px; font-size: 13px;">{{ $vac->origin }}</td>

                            {{-- Mũi tiêm --}}
                            <td style="padding: 12px 14px; text-align: center; font-weight: 600;">{{ $vac->doses }}</td>

                            {{-- Giá bán lẻ --}}
                            <td style="padding: 12px 14px; font-weight: 600; white-space: nowrap;
                                @if($vac->hasSalePrice()) text-decoration: line-through; color: #94a3b8; font-size: 12px;
                                @else color: var(--primary-color); @endif">
                                {{ number_format($vac->price, 0, ',', '.') }} đ
                            </td>

                            {{-- Giá ưu đãi --}}
                            <td style="padding: 12px 14px; font-weight: 700; white-space: nowrap;">
                                @if($vac->hasSalePrice())
                                    <span style="color: #dc2626;">{{ number_format($vac->sale_price, 0, ',', '.') }} đ</span>
                                @else
                                    <span style="color: #cbd5e1;">—</span>
                                @endif
                            </td>

                            {{-- Tình trạng kho --}}
                            <td style="padding: 12px 14px;">
                                <span style="padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; white-space: nowrap;
                                    @if($vac->stock_status === 'available') background-color: #def7ec; color: #03543f;
                                    @elseif($vac->stock_status === 'limited') background-color: #fef3c7; color: #92400e;
                                    @else background-color: #fde8e8; color: #9b1c1c; @endif">
                                    {{ $vac->getStockLabel() }}
                                </span>
                            </td>

                            {{-- Hành động --}}
                            <td style="padding: 12px 14px; text-align: center;">
                                <div style="display: inline-flex; gap: 6px; align-items: center;">
                                    <form action="{{ route('admin.vaccines.toggle-featured', $vac->id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit" title="{{ $vac->is_featured ? 'Bỏ nổi bật' : 'Đánh dấu NỔI BẬT trang chủ' }}" style="padding: 5px 10px; border-radius: 6px; border: 1px solid {{ $vac->is_featured ? '#fef3c7' : '#e2e8f0' }}; background: {{ $vac->is_featured ? '#fffbeb' : '#ffffff' }}; color: {{ $vac->is_featured ? '#d97706' : '#94a3b8' }}; font-weight: 700; font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                            ⭐ {{ $vac->is_featured ? 'Nổi bật' : 'Thường' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.vaccines.edit', $vac->id) }}" class="btn-secondary" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; background:#ffffff; text-decoration: none; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; color: #475569;">
                                        <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.vaccines.destroy', $vac->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vắc xin này? Tất cả dữ liệu liên kết giỏ hàng sẽ bị xóa.')" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #fbd5d5; background:#fff5f5; color: #c8102e; font-weight: 600; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
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

        <!-- Custom Pagination Links -->
        <div class="admin-pagination" style="display: flex; justify-content: center;">
            {{ $vaccines->links() }}
        </div>
    @endif
</div>
@endsection
