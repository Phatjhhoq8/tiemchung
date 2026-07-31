@extends('vaccine::layouts.admin')

@section('title', 'Nhập hàng')
@section('page_title', 'Ghi Nhận Nhập Hàng')

@section('admin_content')
<form method="POST" action="{{ route('admin.stock.store') }}">
    @csrf
    <div class="card-modern">
        @if ($errors->any())
            <div style="margin-bottom:20px; padding:14px; border-radius:8px; background:#fef2f2; color:#991b1b; border:1px solid #fecaca;">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif
        <div class="form-grid-2">
            <div class="form-group-modern" style="margin-bottom:0;">
                <label class="form-label-modern">Chi nhánh *</label>
                @if($isSuperAdmin ?? false)
                    <select name="center_id" class="form-control-modern" style="background-image:none;" required>
                        @foreach($centers as $center)
                            <option value="{{ $center->id }}" {{ (string) $selectedCenterId === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                        @endforeach
                    </select>
                @else
                    <input type="hidden" name="center_id" value="{{ $selectedCenterId }}">
                    <input class="form-control-modern" value="{{ $adminUser?->center?->name }}" disabled>
                @endif
            </div>
            <div class="form-group-modern" style="margin-bottom:0;">
                <label class="form-label-modern">Sản phẩm *</label>
                <select name="vaccine_id" class="form-control-modern" style="background-image:none;" required>
                    @foreach($vaccines as $vaccine)
                        <option value="{{ $vaccine->id }}">{{ $vaccine->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group-modern" style="margin-bottom:0;">
                <label class="form-label-modern">Loại *</label>
                <select name="type" class="form-control-modern" style="background-image:none;" required>
                    <option value="import">Nhập vào</option>
                    <option value="adjustment">Điều chỉnh tăng</option>
                </select>
            </div>
            <div class="form-group-modern" style="margin-bottom:0;">
                <label class="form-label-modern">Số lượng *</label>
                <input type="number" name="quantity" min="1" value="1" class="form-control-modern" required>
            </div>
            <div class="form-group-modern" style="margin-bottom:0;">
                <label class="form-label-modern">Đơn giá nhập</label>
                <input type="number" name="unit_price" min="0" value="0" class="form-control-modern">
            </div>
            <div class="form-group-modern" style="grid-column:span 2; margin-bottom:0;">
                <label class="form-label-modern">Ghi chú</label>
                <textarea name="note" rows="3" class="form-control-modern" style="resize:vertical;"></textarea>
            </div>
        </div>
    </div>
    <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px;">
        <a href="{{ route('admin.stock.index', ['center_id' => $selectedCenterId]) }}" class="btn-modern btn-modern-secondary" style="text-decoration:none;">Hủy</a>
        <button type="submit" class="btn-modern btn-modern-primary">Lưu nhập kho</button>
    </div>
</form>
@endsection
