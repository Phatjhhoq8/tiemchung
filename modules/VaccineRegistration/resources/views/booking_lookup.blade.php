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
                <input id="registration_code" name="registration_code" type="text" value="{{ old('registration_code', $registration_code ?? '') }}" placeholder="Nhập để xem đích danh 1 lịch hẹn" style="min-height: 46px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 14px; font-size: 1rem; width: 100%;">
            </div>
            <div style="align-self: flex-end; width: clamp(100px, 100%, 150px); min-height: 46px;">
                <button type="submit" class="btn-primary-header" style="min-height: 46px; border: 0; cursor: pointer; width: 100%; border-radius: 8px; justify-content: center;"><i data-lucide="search" style="width: 17px; height: 17px;"></i> Tra cứu</button>
            </div>
        </form>

        @error('phone')
            <p style="margin: .75rem 0 0; color: #b91c1c; font-weight: 600;">{{ $message }}</p>
        @enderror
    </section>

    @if($lookedUp && $registrations->isEmpty())
        <div style="margin-top: 1.5rem; padding: 1.25rem; border: 1px solid #fde68a; border-radius: 12px; background: #fffbeb; color: #92400e; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="alert-circle" style="width: 22px; height: 22px; flex-shrink: 0; color: #d97706;"></i>
            <div>
                @if(!empty($registration_code))
                    Không tìm thấy lịch hẹn khớp với mã <strong>{{ $registration_code }}</strong> và số điện thoại này. Vui lòng kiểm tra lại mã hoặc để trống ô mã để tra cứu toàn bộ lịch hẹn.
                @else
                    Chưa tìm thấy lịch hẹn với số điện thoại này. Vui lòng kiểm tra lại số đã dùng khi đặt lịch hoặc liên hệ chi nhánh để được hỗ trợ.
                @endif
            </div>
        </div>
    @endif

    @if($registrations->isNotEmpty())
        @if(isset($points))
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 18px; border-radius: 16px; margin-top: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.04);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #dcfce7; color: #16a34a; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="gift" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <span style="font-size: 0.85rem; color: #166534; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 2px;">Điểm tích lũy thành viên</span>
                        <strong style="font-size: 1.3rem; color: #15803d; font-family: var(--font-display); line-height: 1.2;">{{ number_format($points) }} điểm</strong>
                    </div>
                </div>
                <div style="font-size: 0.85rem; color: #166534; font-weight: 600; background: #dcfce7; padding: 6px 14px; border-radius: 30px;">
                    Tương đương: <strong>{{ number_format($points) }} đ</strong>
                </div>
            </div>
        @endif

        <section style="margin-top: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 1.25rem;">
                <div>
                    <h2 style="margin: 0; color: #0f172a; font-size: 1.35rem; font-weight: 800;">
                        @if(!empty($registration_code))
                            Chi tiết lịch hẹn (Khớp mã: {{ $registration_code }})
                        @else
                            Danh sách lịch hẹn đã đăng ký ({{ $registrations->count() }})
                        @endif
                    </h2>
                    @if(empty($registration_code))
                        <p style="margin: 4px 0 0; color: #64748b; font-size: 0.9rem;">Thông tin cá nhân được bảo vệ. Nhập Mã đặt lịch vào ô tìm kiếm để mở khóa chi tiết.</p>
                    @endif
                </div>
            </div>

            @if(empty($registration_code) && $registrations->count() > 1)
                <!-- Interactive Filter & Search Bar for multi-booking lists -->
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; margin-bottom: 1.25rem; display: flex; flex-direction: column; gap: 12px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);">
                    <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                        <!-- Real-time text search -->
                        <div style="position: relative; flex: 1 1 240px;">
                            <i data-lucide="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #94a3b8; pointer-events: none;"></i>
                            <input type="text" id="lookupQuickSearch" placeholder="Lọc theo tên người tiêm, chi nhánh..." oninput="filterLookupCards()" style="width: 100%; padding: 8px 12px 8px 36px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none; box-sizing: border-box;">
                        </div>

                        <!-- Status filter pills -->
                        <div style="display: flex; gap: 6px; flex-wrap: wrap;" id="statusFilterPills">
                            <button type="button" class="lookup-pill active" onclick="setLookupStatusFilter('all', this)" style="padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; border: 1px solid var(--primary-color, #c8102e); background: var(--primary-color, #c8102e); color: #fff; cursor: pointer; transition: all 0.2s;">
                                Tất cả ({{ $registrations->count() }})
                            </button>
                            <button type="button" class="lookup-pill" onclick="setLookupStatusFilter('pending', this)" style="padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; border: 1px solid #cbd5e1; background: #fff; color: #475569; cursor: pointer; transition: all 0.2s;">
                                Chờ xác nhận
                            </button>
                            <button type="button" class="lookup-pill" onclick="setLookupStatusFilter('confirmed', this)" style="padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; border: 1px solid #cbd5e1; background: #fff; color: #475569; cursor: pointer; transition: all 0.2s;">
                                Đã xác nhận
                            </button>
                            <button type="button" class="lookup-pill" onclick="setLookupStatusFilter('completed', this)" style="padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; border: 1px solid #cbd5e1; background: #fff; color: #475569; cursor: pointer; transition: all 0.2s;">
                                Đã hoàn tất
                            </button>
                            <button type="button" class="lookup-pill" onclick="setLookupStatusFilter('cancelled', this)" style="padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; border: 1px solid #cbd5e1; background: #fff; color: #475569; cursor: pointer; transition: all 0.2s;">
                                Đã hủy
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <div style="display: grid; gap: 1rem;" id="lookupCardsContainer">
                @foreach($registrations as $registration)
                    <article class="lookup-result-card" 
                             data-status="{{ $registration->booking_status }}" 
                             data-search="{{ mb_strtolower($registration->patient_name . ' ' . $registration->center_name . ' ' . $registration->registration_code, 'UTF-8') }}"
                             style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; background: #ffffff; box-shadow: 0 4px 16px rgba(15, 23, 42, .04); transition: all 0.2s;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
                            <div>
                                <strong style="color: #0f172a; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                                    {{ $registration->display_name }}
                                    @if(!$registration->is_masked)
                                        <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 4px; background: #ecfdf5; color: #059669; font-size: 11.5px; font-weight: 700;">
                                            <i data-lucide="check-circle" style="width: 12px; height: 12px;"></i> Khớp mã xác thực
                                        </span>
                                    @endif
                                </strong>
                                <div style="margin-top: 4px; color: #64748b; font-size: .9rem;">Mã phiếu: <strong>{{ $registration->display_code }}</strong></div>
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
                            <div style="margin-top: 1rem; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc; color: #475569; font-size: .85rem; display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap;">
                                <span>💡 Nhập <strong>Mã đặt lịch</strong> trong ô tìm kiếm để xem chi tiết đầy đủ tên và bảng giá.</span>
                                <button type="button" onclick="focusLookupCodeInput()" style="background: none; border: none; color: var(--primary-color); font-weight: 700; font-size: 13px; cursor: pointer; text-decoration: underline;">
                                    Điền mã tra cứu
                                </button>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>

            <div id="lookupNoResultsMsg" style="display: none; margin-top: 1rem; padding: 1.5rem; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; text-align: center; color: #64748b; font-size: 14px;">
                Không có lịch hẹn nào phù hợp với bộ lọc đã chọn.
            </div>
        </section>
    @endif
</div>

<script>
    let currentLookupStatus = 'all';

    function setLookupStatusFilter(status, btn) {
        currentLookupStatus = status;
        document.querySelectorAll('.lookup-pill').forEach(p => {
            p.style.background = '#fff';
            p.style.color = '#475569';
            p.style.borderColor = '#cbd5e1';
            p.style.fontWeight = '600';
        });
        btn.style.background = 'var(--primary-color, #c8102e)';
        btn.style.color = '#fff';
        btn.style.borderColor = 'var(--primary-color, #c8102e)';
        btn.style.fontWeight = '700';

        filterLookupCards();
    }

    function filterLookupCards() {
        const query = (document.getElementById('lookupQuickSearch')?.value || '').trim().toLowerCase();
        const cards = document.querySelectorAll('.lookup-result-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const cardStatus = card.dataset.status || '';
            const cardSearch = card.dataset.search || '';

            const matchesStatus = currentLookupStatus === 'all' || cardStatus === currentLookupStatus;
            const matchesQuery = !query || cardSearch.includes(query);

            if (matchesStatus && matchesQuery) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const noResults = document.getElementById('lookupNoResultsMsg');
        if (noResults) {
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    function focusLookupCodeInput() {
        const codeInput = document.getElementById('registration_code');
        if (codeInput) {
            codeInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            codeInput.focus();
            codeInput.style.borderColor = 'var(--primary-color, #c8102e)';
            codeInput.style.boxShadow = '0 0 0 3px rgba(200, 16, 46, 0.15)';
        }
    }
</script>
@endsection
