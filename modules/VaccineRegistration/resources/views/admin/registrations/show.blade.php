@extends('vaccine::layouts.admin')

@section('title', 'Đơn ' . $registration->registration_code)
@section('page_title', 'Chi Tiết Đơn Đặt Lịch')

@section('admin_content')
<div style="max-width:1000px; margin:0 auto; display:grid; gap:24px;">
    <div class="card-modern" style="display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; align-items:center;">
        <a href="{{ route('admin.registrations.index') }}" class="btn-action-sm">Quay lại danh sách</a>
        <div style="font-weight:800; color:var(--primary-color);">{{ $registration->registration_code }}</div>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:24px;">
        <div class="card-modern">
            <h3 style="margin-top:0;">Người tiêm</h3>
            <p style="margin:0 0 8px;"><strong>{{ $registration->patient_name }}</strong></p>
            <p style="margin:0 0 12px;">{{ \Modules\VaccineRegistration\Support\PhoneNormalizer::display($registration->patient_phone) }}</p>
            @if($registration->customer)
                <p style="margin:0 0 12px; color:var(--text-muted);"><strong>Tài khoản tích điểm:</strong> {{ $registration->customer->name }} · {{ \Modules\VaccineRegistration\Support\PhoneNormalizer::display($registration->customer->phone) }}</p>
                <a class="btn-action-sm" href="{{ route('admin.customers.show', $registration->customer) }}">Xem lịch sử mua và điểm</a>
            @endif
        </div>

        <div class="card-modern">
            <h3 style="margin-top:0;">Lịch hẹn</h3>
            <p><strong>Chi nhánh:</strong> {{ $registration->center_name }}</p>
            <p><strong>Ngày:</strong> {{ $registration->injection_date?->format('d/m/Y') }}</p>
            <p><strong>Khung giờ:</strong> {{ $registration->slot ? $registration->slot->start_at . ' - ' . $registration->slot->end_at : 'Chưa chọn' }}</p>
            <form action="{{ route('admin.registrations.status', $registration) }}" method="POST" style="display:flex; gap:10px; flex-wrap:wrap; align-items:end;">
                @csrf
                @method('PATCH')
                <div style="flex:1; min-width:180px;">
                    <label class="form-label-modern" for="booking_status">Trạng thái lịch hẹn</label>
                    <select id="booking_status" name="booking_status" class="form-control-modern">
                        @foreach(['pending' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'completed' => 'Đã hoàn tất', 'no_show' => 'Không đến', 'cancelled' => 'Đã hủy'] as $value => $label)
                            <option value="{{ $value }}" {{ $registration->booking_status === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-modern" style="background: var(--accent-color, #004b8f); color: #fff; font-weight: 700; border: none; padding: 0 18px; border-radius: 8px; cursor: pointer; height: 42px; transition: all 0.2s;">Cập nhật</button>
            </form>
        </div>
    </div>

    <div class="card-modern">
        <h3 style="margin-top:0;">Thanh toán tại quầy và điểm</h3>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:16px; margin-bottom:20px;">
            <div><span style="color:var(--text-muted); display:block;">Trạng thái thanh toán</span><strong>{{ $registration->paymentStatusLabel() }}</strong></div>
            <div><span style="color:var(--text-muted); display:block;">Tổng đơn</span><strong>{{ number_format($registration->total_price) }} đ</strong></div>
            <div><span style="color:var(--text-muted); display:block;">Giảm bằng điểm</span><strong>{{ number_format($registration->points_discount_amount) }} đ</strong></div>
            <div><span style="color:var(--text-muted); display:block;">Thực thu</span><strong style="color:var(--primary-color);">{{ number_format($registration->netPaidAmount()) }} đ</strong></div>
        </div>

        @if($registration->payment_status === \Modules\VaccineRegistration\Models\Registration::PAYMENT_UNPAID && $registration->booking_status !== \Modules\VaccineRegistration\Models\Registration::BOOKING_CANCELLED)
            <form action="{{ route('admin.registrations.settle', $registration) }}" method="POST" data-confirm="Xác nhận đã thu tiền tại quầy?" style="display:flex; gap:12px; flex-wrap:wrap; align-items:end;">
                @csrf
                <div style="flex:1 1 230px;">
                    <label class="form-label-modern" for="redeem_points">Điểm khách muốn dùng</label>
                    <input class="form-control-modern" id="redeem_points" type="number" name="redeem_points" min="0" max="{{ $pointQuote['available_points'] ?? 0 }}" value="{{ old('redeem_points', 0) }}">
                    <small style="display:block; margin-top:5px; color:var(--text-muted);">Số dư toàn hệ thống: {{ number_format($pointQuote['balance'] ?? 0) }} điểm. Có thể dùng tối đa {{ number_format($pointQuote['available_points'] ?? 0) }} điểm (50% đơn).</small>
                </div>
                <button type="submit" class="btn-modern btn-modern-primary">Xác nhận thanh toán</button>
            </form>
        @elseif($registration->payment_status === \Modules\VaccineRegistration\Models\Registration::PAYMENT_PAID)
            <form action="{{ route('admin.registrations.refund', $registration) }}" method="POST" data-confirm="Hoàn tiền toàn bộ đơn này? Điểm sẽ được hoàn lại.">
                @csrf
                <button type="submit" class="btn-modern btn-modern-secondary">Hoàn tiền toàn bộ</button>
            </form>
        @endif
    </div>

    <div class="card-modern">
        <h3 style="margin-top:0;">Sản phẩm đã chọn</h3>
        <div class="table-responsive-modern">
            <table class="table-modern">
                <thead><tr><th>Tên vắc xin</th><th style="text-align:center;">Số lượng</th><th style="text-align:right;">Giá chốt</th><th style="text-align:right;">Thành tiền</th></tr></thead>
                <tbody>
                    @foreach($registration->vaccines as $vaccine)
                        <tr>
                            <td><strong>{{ $vaccine->name }}</strong><small style="display:block; color:var(--text-muted);">{{ $vaccine->origin }}</small></td>
                            <td style="text-align:center;">{{ $vaccine->pivot->quantity }}</td>
                            <td style="text-align:right;">{{ number_format($vaccine->pivot->price) }} đ</td>
                            <td style="text-align:right; font-weight:700; color:var(--primary-color);">{{ number_format($vaccine->pivot->price * $vaccine->pivot->quantity) }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
