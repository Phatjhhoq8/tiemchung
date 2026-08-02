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

        <aside class="counter-payment-box" style="margin:0;">
            <div class="later-payment-icon"><i data-lucide="landmark"></i></div>
            <h3>Thanh toán tại quầy</h3>
            <ol class="step-guide">
                <li>Đến đúng chi nhánh và khung giờ đã đặt.</li>
                <li>Xuất trình mã phiếu <strong>{{ $registration->registration_code }}</strong> cùng số điện thoại đã đăng ký.</li>
                <li>Nhân viên xác nhận thanh toán và hỗ trợ dùng hoặc tích điểm.</li>
            </ol>
            <div class="action-buttons">
                <a href="{{ route('home') }}" class="btn-primary">Về trang chủ</a>
                <button type="button" onclick="window.print()" class="btn-secondary">In phiếu</button>
            </div>
        </aside>
    </div>
</div>
@endsection
