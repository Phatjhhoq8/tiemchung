@extends('vaccine::layouts.app')

@section('title', 'Tra Cứu Lịch Hẹn')

@section('content')
<div style="max-width: 980px; margin: 0 auto; padding: 0 1rem 3rem;">
    <section style="background: linear-gradient(135deg, #fff1f2, #ffffff); border: 1px solid #fecdd3; border-radius: 20px; padding: clamp(1.5rem, 4vw, 2.5rem);">
        <span style="display: inline-flex; align-items: center; gap: 6px; color: var(--primary-color); font-size: .82rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase;"><i data-lucide="calendar-search" style="width: 16px; height: 16px;"></i> Dành cho khách hàng</span>
        <h1 style="margin: .65rem 0 .5rem; color: #0f172a; font-size: clamp(1.7rem, 4vw, 2.35rem);">Tra cứu lịch hẹn</h1>
        <p style="max-width: 620px; margin: 0; color: #475569; line-height: 1.6;">Nhập số điện thoại đã dùng khi đặt lịch để xem lịch hẹn, trạng thái thanh toán và các vắc xin đã đăng ký.</p>

        <form action="{{ route('booking.lookup.submit') }}" method="POST" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 1.5rem;">
            @csrf
            <div style="flex: 1 1 260px; display: flex; flex-direction: column; gap: 4px;">
                <label for="phone" style="font-size: .85rem; font-weight: 700; color: #475569;">Số điện thoại (Bắt buộc)</label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone', $phone ? \Modules\VaccineRegistration\Support\PhoneNormalizer::display($phone) : '') }}" inputmode="tel" autocomplete="tel" placeholder="Ví dụ: 0912345678" required style="min-height: 46px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 14px; font-size: 1rem; width: 100%;">
            </div>
            <div style="flex: 1 1 260px; display: flex; flex-direction: column; gap: 4px;">
                <label for="registration_code" style="font-size: .85rem; font-weight: 700; color: #475569;">Mã đặt lịch (Không bắt buộc)</label>
                <input id="registration_code" name="registration_code" type="text" value="{{ old('registration_code', $registration_code ?? '') }}" placeholder="Nhập để xem đầy đủ thông tin" style="min-height: 46px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 14px; font-size: 1rem; width: 100%;">
            </div>
            <div style="align-self: flex-end; width: clamp(100px, 100%, 150px); min-height: 46px;">
                <button type="submit" class="btn-primary-header" style="min-height: 46px; border: 0; cursor: pointer; width: 100%; border-radius: 8px;"><i data-lucide="search" style="width: 17px; height: 17px;"></i> Tra cứu</button>
            </div>
        </form>

        @error('phone')
            <p style="margin: .75rem 0 0; color: #b91c1c; font-weight: 600;">{{ $message }}</p>
        @enderror
    </section>

    @if($lookedUp && $registrations->isEmpty())
        <div style="margin-top: 1.5rem; padding: 1.25rem; border: 1px solid #fde68a; border-radius: 8px; background: #fffbeb; color: #92400e;">
            Chưa tìm thấy lịch hẹn với số điện thoại này. Vui lòng kiểm tra lại số đã dùng khi đặt lịch hoặc liên hệ chi nhánh để được hỗ trợ.
        </div>
    @endif

    @if($registrations->isNotEmpty())
        <section style="margin-top: 1.75rem;">
            <h2 style="margin: 0 0 1rem; color: #0f172a; font-size: 1.3rem;">Lịch hẹn và lịch sử đăng ký ({{ $registrations->count() }})</h2>
            <div style="display: grid; gap: 1rem;">
                @foreach($registrations as $registration)
                    <article style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; background: #ffffff; box-shadow: 0 4px 16px rgba(15, 23, 42, .04);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
                            <div>
                                <strong style="color: #0f172a; font-size: 1.05rem;">{{ $registration->display_name }}</strong>
                                <div style="margin-top: 4px; color: #64748b; font-size: .9rem;">Mã phiếu: {{ $registration->display_code }}</div>
                            </div>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <span style="padding: 5px 10px; border-radius: 6px; background: #fef2f2; color: #b91c1c; font-size: .82rem; font-weight: 700;">{{ $registration->bookingStatusLabel() }}</span>
                                <span style="padding: 5px 10px; border-radius: 6px; background: #eff6ff; color: #1d4ed8; font-size: .82rem; font-weight: 700;">{{ $registration->paymentStatusLabel() }}</span>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: .75rem 1.5rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f1f5f9; color: #475569; font-size: .93rem;">
                            <div><strong style="display: block; color: #64748b; font-size: .78rem; text-transform: uppercase; letter-spacing: .04em;">Chi nhánh</strong>{{ $registration->center_name }}</div>
                            <div><strong style="display: block; color: #64748b; font-size: .78rem; text-transform: uppercase; letter-spacing: .04em;">Lịch tiêm</strong>{{ $registration->injection_date?->format('d/m/Y') ?? 'Đang cập nhật' }}@if($registration->slot) · {{ $registration->slot->start_at }} - {{ $registration->slot->end_at }}@endif</div>
                            <div><strong style="display: block; color: #64748b; font-size: .78rem; text-transform: uppercase; letter-spacing: .04em;">Tổng dự kiến</strong><span style="color: var(--primary-color); font-weight: 800;">{{ $registration->display_price }}</span></div>
                        </div>
                        <div style="margin-top: 1rem; color: #334155; line-height: 1.6;"><strong>Vắc xin:</strong> {{ $registration->display_vaccines }}</div>
                        
                        @if(!$registration->is_masked && $registration->payment_status === \Modules\VaccineRegistration\Models\Registration::PAYMENT_UNPAID && $registration->booking_status !== \Modules\VaccineRegistration\Models\Registration::BOOKING_CANCELLED)
                            <div style="margin-top: 1.25rem; padding: 12px 16px; border: 1.5px dashed #cbd5e1; border-radius: 10px; background: #f8fafc; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><rect x="7" y="7" width="3" height="3"></rect><rect x="14" y="7" width="3" height="3"></rect><rect x="7" y="14" width="3" height="3"></rect><rect x="14" y="14" width="3" height="3"></rect></svg>
                                    <div style="text-align: left;">
                                        <strong style="display: block; font-size: .88rem; color: #475569;">Thanh toán trực tuyến (Mã QR)</strong>
                                        <span style="font-size: .8rem; color: #64748b;">Quét mã QR thanh toán nhanh qua Mobile Banking</span>
                                    </div>
                                </div>
                                <button type="button" disabled style="background: #e2e8f0; color: #94a3b8; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: .82rem; cursor: not-allowed; display: inline-flex; align-items: center; gap: 4px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    Thanh toán QR (Tạm khóa)
                                </button>
                            </div>
                        @elseif($registration->is_masked && $registration->payment_status === \Modules\VaccineRegistration\Models\Registration::PAYMENT_UNPAID && $registration->booking_status !== \Modules\VaccineRegistration\Models\Registration::BOOKING_CANCELLED)
                            <div style="margin-top: 1rem; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; background: #f8fafc; color: #475569; font-size: .85rem;">
                                💡 Nhập **Mã đặt lịch** trong form tra cứu phía trên để xem chi tiết đầy đủ và sử dụng mã QR thanh toán.
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
