@extends('vaccine::layouts.app')

@section('title', 'Đặt Lịch Thành Công')

@section('content')
@php($registration = $registrations->first())
<div class="success-container">
    <div class="success-banner">
        <div class="success-icon-animation"><i data-lucide="check-circle"></i></div>
        <h1>Đặt lịch thành công</h1>
        <p>Medicare đã ghi nhận yêu cầu của bạn. Vui lòng lưu mã phiếu để nhân viên hỗ trợ tại quầy.</p>
        <div class="registration-code-badge">{{ $registration->registration_code }}</div>
    </div>

    <div class="success-layout" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:24px;">
        <section class="ticket-card" style="margin:0; width:auto;">
            <div class="ticket-header"><h3>PHIẾU ĐẶT LỊCH</h3><span>{{ $registration->center_name }}</span></div>
            <div class="ticket-body">
                <div class="ticket-section">
                    <h4>Thông tin khách hàng</h4>
                    <table class="ticket-table">
                        <tr><th>Họ tên:</th><td>{{ $registration->patient_name }}</td></tr>
                        <tr><th>Số điện thoại:</th><td>{{ \Modules\VaccineRegistration\Support\PhoneNormalizer::display($registration->patient_phone) }}</td></tr>
                    </table>
                </div>
                <div class="ticket-section">
                    <h4>Lịch hẹn</h4>
                    <table class="ticket-table">
                        <tr><th>Ngày:</th><td>{{ $registration->injection_date?->format('d/m/Y') }}</td></tr>
                        <tr><th>Khung giờ:</th><td>{{ $registration->slot ? $registration->slot->start_at . ' - ' . $registration->slot->end_at : 'Theo hướng dẫn của chi nhánh' }}</td></tr>
                    </table>
                </div>
                <div class="ticket-section">
                    <h4>Vắc xin đã chọn</h4>
                    @foreach($registration->vaccines as $vaccine)
                        <div class="ticket-vaccine-item">
                            <span>{{ $vaccine->name }}</span>
                            <strong>{{ number_format($vaccine->pivot->price * $vaccine->pivot->quantity) }} đ</strong>
                        </div>
                    @endforeach
                </div>
                <div class="ticket-section-total"><div class="total-row"><span>Tổng dự kiến:</span><strong>{{ number_format($registration->total_price) }} đ</strong></div></div>
            </div>
        </section>

        <aside class="counter-payment-box" style="margin:0; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div class="later-payment-icon"><i data-lucide="landmark"></i></div>
                <h3>Hình thức thanh toán</h3>
                
                <!-- Thanh toán tại quầy -->
                <div style="margin-bottom: 24px;">
                    <h4 style="margin: 0 0 8px 0; font-size: 14.5px; font-weight: 800; color: var(--accent-color, #004b8f);">1. Thanh toán tại quầy</h4>
                    <ol class="step-guide" style="margin: 0; padding-left: 20px;">
                        <li>Đến đúng chi nhánh và khung giờ đã đặt.</li>
                        <li>Xuất trình mã phiếu <strong>{{ $registration->registration_code }}</strong> cùng số điện thoại đã đăng ký.</li>
                        <li>Nhân viên xác nhận thanh toán và hỗ trợ dùng hoặc tích điểm.</li>
                    </ol>
                </div>

                <!-- Thanh toán Online -->
                <div style="padding: 16px; border: 1px dashed #cbd5e1; border-radius: 10px; background: #f8fafc; opacity: 0.75; position: relative;">
                    <h4 style="margin: 0 0 6px 0; font-size: 14.5px; font-weight: 800; color: #64748b; display: flex; align-items: center; gap: 6px;">
                        <i data-lucide="qr-code" style="width: 18px; height: 18px;"></i>
                        2. Thanh toán trực tuyến (Mã QR)
                    </h4>
                    <p style="margin: 0 0 10px 0; font-size: 13px; color: #64748b; line-height: 1.4;">Quét mã QR thanh toán nhanh qua ứng dụng Ngân hàng (VietQR / Napas247).</p>
                    <button type="button" disabled style="width: 100%; padding: 10px; border-radius: 8px; border: none; background: #e2e8f0; color: #94a3b8; font-weight: 700; font-size: 13.5px; cursor: not-allowed; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <i data-lucide="lock" style="width: 14px; height: 14px;"></i>
                        Thanh toán QR (Tạm khóa)
                    </button>
                </div>
            </div>
            
            <div class="action-buttons" style="margin-top: 24px;">
                <a href="{{ route('home') }}" class="btn-primary">Về trang chủ</a>
                <a href="{{ route('booking.lookup') }}" class="btn-secondary">Tra cứu lịch hẹn</a>
                <button type="button" onclick="window.print()" class="btn-secondary">In phiếu</button>
            </div>
        </aside>
    </div>
</div>
@endsection
