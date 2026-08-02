@extends('vaccine::layouts.app')

@section('title', 'Đặt Lịch Tiêm Chủng')

@section('content')
<div class="registration-container">
    <div class="registration-layout">
        <div class="form-card">
            <h1 style="margin-top: 0;">Đặt lịch tiêm chủng</h1>
            <p class="step-desc">Giá và khung giờ được áp dụng theo chi nhánh bạn đang chọn. Mỗi phiếu dành cho một người tiêm.</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('centers.select') }}" method="POST" style="margin: 24px 0; padding: 16px; border: 1px solid var(--border-color); border-radius: 10px; background: #f8fafc;">
                @csrf
                <label for="booking_center_id" style="display: block; margin-bottom: 8px; font-weight: 700;">Chi nhánh đặt lịch</label>
                <select id="booking_center_id" name="center_id" onchange="this.form.submit()" style="width: 100%; padding: 12px; border-radius: 8px;">
                    @foreach($activeCenters as $center)
                        <option value="{{ $center->id }}" {{ $currentCenter?->id === $center->id ? 'selected' : '' }}>{{ $center->name }} - {{ $center->address }}</option>
                    @endforeach
                </select>
                <noscript><button type="submit" class="btn-secondary" style="margin-top: 12px;">Áp dụng chi nhánh</button></noscript>
            </form>

            @if($unavailableCount > 0)
                <div class="alert alert-danger">
                    Một hoặc nhiều sản phẩm trong danh sách không được bán tại {{ $currentCenter->name }}. Vui lòng quay lại danh mục để điều chỉnh.
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ old('idempotency_key', (string) \Illuminate\Support\Str::uuid()) }}">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="patient_name">Họ và tên <span class="required">*</span></label>
                        <input id="patient_name" type="text" name="patient_name" value="{{ old('patient_name') }}" maxlength="255" autocomplete="name" required>
                    </div>
                    <div class="form-group">
                        <label for="patient_phone">Số điện thoại <span class="required">*</span></label>
                        <input id="patient_phone" type="tel" name="patient_phone" value="{{ old('patient_phone') }}" inputmode="tel" autocomplete="tel" placeholder="Ví dụ: 0912345678" required>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 22px;">
                    <label>Vắc xin đăng ký <span class="required">*</span></label>
                    <div style="display: grid; gap: 10px; margin-top: 10px;">
                        @foreach($cart as $id => $item)
                            <label style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid {{ $item['unavailable_for_center'] ? '#fecaca' : '#e2e8f0' }}; border-radius: 8px; {{ $item['unavailable_for_center'] ? 'opacity: .55;' : '' }}">
                                <input type="checkbox" name="vaccine_ids[]" value="{{ $id }}" {{ !$item['unavailable_for_center'] ? 'checked' : 'disabled' }}>
                                <span style="flex: 1;">
                                    <strong>{{ $item['name'] }}</strong>
                                    <small style="display: block; color: #64748b; margin-top: 3px;">{{ $item['disease_prevention'] }}</small>
                                </span>
                                <strong style="color: var(--primary-color);">{{ number_format($item['price'], 0, ',', '.') }} đ</strong>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group" style="margin-top: 22px;">
                    <label for="slot_id">Ngày và khung giờ tiêm <span class="required">*</span></label>
                    <select id="slot_id" name="slot_id" required style="width: 100%; padding: 12px; border-radius: 8px;">
                        <option value="">-- Chọn khung giờ --</option>
                        @foreach($schedules as $schedule)
                            <optgroup label="{{ $schedule->date->format('d/m/Y') }}">
                                @foreach($schedule->slots as $slot)
                                    <option value="{{ $slot->id }}" {{ (string) old('slot_id') === (string) $slot->id ? 'selected' : '' }}>
                                        {{ $slot->start_at }} - {{ $slot->end_at }} (còn {{ max(0, $slot->capacity - $slot->reserved_count) }} chỗ)
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @if($schedules->isEmpty())
                        <small style="display: block; margin-top: 8px; color: #b91c1c;">Chi nhánh hiện chưa mở khung giờ. Vui lòng chọn chi nhánh khác hoặc liên hệ nhân viên.</small>
                    @endif
                </div>

                <div class="form-actions" style="margin-top: 28px;">
                    <a href="{{ route('vaccine.index') }}" class="btn-secondary">Quay lại danh mục</a>
                    <button type="submit" class="btn-submit-registration" {{ $unavailableCount > 0 || $schedules->isEmpty() ? 'disabled' : '' }}>
                        Hoàn tất đặt lịch
                    </button>
                </div>
            </form>
        </div>

        <aside class="summary-card">
            <h3>Tóm tắt tại {{ $currentCenter->name }}</h3>
            <div class="summary-items">
                @foreach($cart as $item)
                    <div class="summary-item">
                        <div class="item-name">
                            <strong>{{ $item['name'] }}</strong>
                            <span>{{ $item['disease_prevention'] }}</span>
                        </div>
                        <span class="item-price">{{ number_format($item['price'], 0, ',', '.') }} đ</span>
                    </div>
                @endforeach
            </div>
            <div class="summary-divider"></div>
            <div class="summary-total">
                <span>Tổng dự kiến:</span>
                <strong>{{ number_format($totalPrice, 0, ',', '.') }} đ</strong>
            </div>
            <div class="summary-note">
                <p>Thanh toán và sử dụng điểm được nhân viên xác nhận tại quầy. Giá cuối cùng được hệ thống chốt khi tạo phiếu.</p>
            </div>
        </aside>
    </div>
</div>
@endsection
