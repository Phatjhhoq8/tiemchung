@if($registrations->isEmpty())
    <p style="margin:0; color:var(--text-muted);">Không có đơn đặt lịch phù hợp.</p>
@else
    <div class="table-responsive-modern">
        <table class="table-modern">
            <thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Chi nhánh</th><th>Khung giờ</th><th>Tổng tiền</th><th>Lịch hẹn</th><th>Thanh toán</th><th style="text-align: right;">Thao tác</th></tr></thead>
            <tbody>
                @foreach($registrations as $registration)
                    <tr>
                        <td style="font-weight:700; color:var(--primary-color);">{{ $registration->registration_code }}</td>
                        <td><strong>{{ $registration->patient_name }}</strong><small style="display:block; color:var(--text-muted);">{{ \Modules\VaccineRegistration\Support\PhoneNormalizer::display($registration->patient_phone) }}</small></td>
                        <td>{{ $registration->center_name }}</td>
                        <td>{{ $registration->injection_date?->format('d/m/Y') }}</td>
                        <td>{{ number_format($registration->total_price) }} đ</td>
                        <td>{{ $registration->bookingStatusLabel() }}</td>
                        <td>{{ $registration->paymentStatusLabel() }}</td>
                        <td style="text-align: right; white-space: nowrap;"><a class="btn-action-sm" href="{{ route('admin.registrations.show', $registration) }}">Chi tiết</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="display:flex; justify-content:center; margin-top:24px;">{{ $registrations->links() }}</div>
@endif
