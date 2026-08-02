@extends('vaccine::layouts.admin')

@section('title', 'Đăng ký nhanh tại quầy')
@section('page_title', 'Đăng Ký Nhanh Tại Quầy')

@section('admin_content')
<div style="max-width:1000px; margin:0 auto; display:grid; gap:24px;">
    <div class="card-modern" style="display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; align-items:center;">
        <div>
            <h3 style="margin:0 0 5px;">Tạo phiếu cho khách đến trực tiếp</h3>
            <p style="margin:0; color:var(--text-muted);">Giá và khung giờ được chốt theo chi nhánh đã chọn.</p>
        </div>
        <a href="{{ route('admin.registrations.index') }}" class="btn-action-sm">Quay lại danh sách</a>
    </div>

    @if($isSuperAdmin ?? false)
        <form method="GET" action="{{ route('admin.registrations.create') }}" class="card-modern" style="display:flex; gap:12px; flex-wrap:wrap; align-items:end;">
            <div style="flex:1 1 260px;">
                <label class="form-label-modern" for="center_switcher">Chi nhánh tạo phiếu</label>
                <select id="center_switcher" name="center_id" class="form-control-modern" onchange="this.form.submit()">
                    @foreach($centers as $item)
                        <option value="{{ $item->id }}" {{ $center->id === $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    @endif

    @if($errors->any())
        <div class="alert alert-danger"><ul style="margin:0; padding-left:20px;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('admin.registrations.store') }}" class="card-modern" style="display:grid; gap:22px;">
        @csrf
        <input type="hidden" name="center_id" value="{{ $center->id }}">
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
            <div>
                <label class="form-label-modern" for="patient_name">Họ tên người tiêm</label>
                <input class="form-control-modern" id="patient_name" name="patient_name" value="{{ old('patient_name') }}" autocomplete="name" required>
            </div>
            <div>
                <label class="form-label-modern" for="patient_phone">Số điện thoại</label>
                <input class="form-control-modern" id="patient_phone" name="patient_phone" value="{{ old('patient_phone') }}" inputmode="tel" autocomplete="tel" placeholder="0912345678" required>
            </div>
            <div>
                <label class="form-label-modern" for="booking_status">Trạng thái lịch</label>
                <select class="form-control-modern" id="booking_status" name="booking_status">
                    <option value="confirmed" {{ old('booking_status', 'confirmed') === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                    <option value="pending" {{ old('booking_status') === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                </select>
            </div>
        </div>

        <div>
            <label class="form-label-modern" for="slot_id">Ngày và khung giờ tại {{ $center->name }}</label>
            <select class="form-control-modern" id="slot_id" name="slot_id" required>
                <option value="">-- Chọn khung giờ còn chỗ --</option>
                @foreach($slots->groupBy(fn ($slot) => $slot->schedule->date->format('d/m/Y')) as $date => $dateSlots)
                    <optgroup label="{{ $date }}">
                        @foreach($dateSlots as $slot)
                            <option value="{{ $slot->id }}" {{ (string) old('slot_id') === (string) $slot->id ? 'selected' : '' }}>{{ $slot->start_at }} - {{ $slot->end_at }} (còn {{ $slot->capacity - $slot->reserved_count }} chỗ)</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @if($slots->isEmpty())<small style="display:block; margin-top:6px; color:#b91c1c;">Chi nhánh chưa có khung giờ còn chỗ.</small>@endif
        </div>

        <div>
            <label class="form-label-modern">Vắc xin đăng ký</label>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:10px; margin-top:8px; max-height:420px; overflow:auto; padding:4px;">
                @foreach($vaccines as $centerVaccine)
                    @php($price = $centerVaccine->hasSalePrice() ? $centerVaccine->sale_price : $centerVaccine->price)
                    <label style="display:flex; gap:10px; align-items:flex-start; border:1px solid #e2e8f0; border-radius:10px; padding:12px; cursor:pointer;">
                        <input type="checkbox" name="vaccine_ids[]" value="{{ $centerVaccine->vaccine_id }}" {{ in_array($centerVaccine->vaccine_id, old('vaccine_ids', [])) ? 'checked' : '' }}>
                        <span style="flex:1;"><strong>{{ $centerVaccine->vaccine->name }}</strong><small style="display:block; color:var(--text-muted); margin-top:3px;">{{ $centerVaccine->vaccine->origin }}</small></span>
                        <strong style="color:var(--primary-color); white-space:nowrap;">{{ number_format($price) }} đ</strong>
                    </label>
                @endforeach
            </div>
            @if($vaccines->isEmpty())<small style="display:block; margin-top:6px; color:#b91c1c;">Chi nhánh chưa có vắc xin khả dụng.</small>@endif
        </div>

        <div style="display:flex; justify-content:flex-end; gap:12px; flex-wrap:wrap;">
            <a href="{{ route('admin.registrations.index') }}" class="btn-modern btn-modern-secondary">Hủy</a>
            <button type="submit" class="btn-modern btn-modern-primary" {{ $slots->isEmpty() || $vaccines->isEmpty() ? 'disabled' : '' }}>Tạo phiếu tại quầy</button>
        </div>
    </form>
</div>
@endsection
