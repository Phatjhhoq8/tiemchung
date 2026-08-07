@extends('vaccine::layouts.admin')

@section('title', 'Nhập / Xuất Kho')
@section('page_title', 'Nhập / Xuất Kho Theo Chi Nhánh')

@section('admin_content')
<div class="card-modern">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; margin-bottom:24px;">
        <h2 style="font-family:var(--font-display); font-size:18px; font-weight:800; margin:0;">Lịch sử nhập/xuất kho</h2>
        @if(!($isSuperAdmin ?? false))
            <a href="{{ route('admin.stock.create', ['center_id' => $selectedCenterId]) }}" class="btn-modern btn-modern-primary" style="text-decoration:none;"><i data-lucide="package-plus"></i> Nhập hàng</a>
        @endif
    </div>

    <form method="GET" action="{{ route('admin.stock.index') }}" class="vaccine-filter-form" style="display:flex; gap:12px; align-items:flex-end; margin-bottom:20px; flex-wrap:wrap;">
        <div style="flex:2 1 260px;">
            <label class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Tìm kiếm</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên vắc xin, ghi chú..." class="form-control-modern">
        </div>
        
        @if($isSuperAdmin ?? false)
        <div style="flex:1 1 200px;">
            <label class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Chi nhánh</label>
            <select name="center_id" class="form-control-modern" style="background-image:none;">
                <option value="">Tất cả chi nhánh</option>
                @foreach($centers as $center)
                    <option value="{{ $center->id }}" {{ (string) $selectedCenterId === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                @endforeach
            </select>
        </div>
        @else
            <input type="hidden" name="center_id" value="{{ $selectedCenterId }}">
        @endif

        <div style="flex:1 1 150px;">
            <label class="form-label-modern" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Loại giao dịch</label>
            <select name="type" class="form-control-modern" style="background-image:none;">
                <option value="">Tất cả</option>
                <option value="import" {{ request('type') === 'import' ? 'selected' : '' }}>Nhập vào</option>
                <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>Bán ra</option>
                <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Điều chỉnh</option>
            </select>
        </div>

        <button type="submit" class="btn-modern btn-modern-primary" style="height: 42px;">Lọc</button>
        @if(request()->hasAny(['search', 'center_id', 'type']))
            <a href="{{ route('admin.stock.index') }}" class="btn-modern btn-modern-secondary" style="height: 42px; display: inline-flex; align-items: center; text-decoration: none;">Xóa lọc</a>
        @endif
    </form>

    <div class="table-responsive-modern">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Chi nhánh</th>
                    <th>Sản phẩm</th>
                    <th>Loại</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Người tạo</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                    <tr>
                        <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $movement->center?->name }}</td>
                        <td style="font-weight:700;">{{ $movement->vaccine?->name }}</td>
                        <td>
                            <span class="badge-modern {{ $movement->type === 'sale' ? 'badge-modern-info' : 'badge-modern-success' }}">
                                {{ $movement->type === 'sale' ? 'Bán ra' : ($movement->type === 'import' ? 'Nhập vào' : 'Điều chỉnh') }}
                            </span>
                        </td>
                        <td style="font-weight:800;">{{ $movement->quantity }}</td>
                        <td>{{ number_format($movement->unit_price, 0, ',', '.') }} đ</td>
                        <td>{{ $movement->creator?->name ?? 'Hệ thống' }}</td>
                        <td>{{ $movement->note }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center; color:var(--text-muted); padding:32px;">Chưa có dữ liệu kho.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:20px;">{{ $movements->links() }}</div>
</div>
@endsection
