@extends('vaccine::layouts.admin')

@section('title', 'Khách hàng ' . $customer->name)
@section('page_title', 'Chi Tiết Khách Hàng')

@section('admin_content')
<div style="display:grid; gap:24px;">
    <div class="card-modern" style="display:flex; justify-content:space-between; gap:20px; flex-wrap:wrap;">
        <div>
            <a href="{{ route('admin.customers.index') }}" class="btn-action-sm">Quay lại</a>
            <h2 style="margin:16px 0 6px;">{{ $customer->name }}</h2>
            <p style="margin:0; color:var(--text-muted);">{{ \Modules\VaccineRegistration\Support\PhoneNormalizer::display($customer->phone) }}</p>
        </div>
        <div style="text-align:right;">
            <span style="display:block; color:var(--text-muted);">Số dư điểm toàn hệ thống</span>
            <strong style="font-size:28px; color:var(--primary-color);">{{ number_format($pointBalance) }} điểm</strong>
        </div>
    </div>

    @if($isSuperAdmin ?? false)
        <div class="card-modern">
            <h3 style="margin-top:0;">Điều chỉnh điểm</h3>
            <form id="pointsAdjustForm" method="POST" action="{{ route('admin.customers.points.adjust', $customer) }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:end;">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
                <div>
                    <label class="form-label-modern" for="points">Điểm cộng/trừ</label>
                    <input class="form-control-modern" id="points" type="number" name="points" required>
                </div>
                <div id="expiry_date_container" style="display:none;">
                    <label class="form-label-modern" for="expiry_date">Ngày hết hạn (tùy chọn)</label>
                    <input class="form-control-modern" id="expiry_date" type="date" name="expiry_date">
                </div>
                <div style="flex:1 1 280px;">
                    <label class="form-label-modern" for="note">Lý do</label>
                    <input class="form-control-modern" id="note" type="text" name="note" maxlength="255" required>
                </div>
                <button class="btn-modern btn-modern-primary" type="submit">Lưu điều chỉnh</button>
            </form>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const pointsInput = document.getElementById('points');
                const expiryContainer = document.getElementById('expiry_date_container');
                const expiryInput = document.getElementById('expiry_date');

                if (pointsInput && expiryContainer) {
                    pointsInput.addEventListener('input', function() {
                        const val = parseInt(this.value) || 0;
                        if (val > 0) {
                            expiryContainer.style.display = 'block';
                        } else {
                            expiryContainer.style.display = 'none';
                            if (expiryInput) {
                                expiryInput.value = '';
                            }
                        }
                    });
                }
            });
        </script>
    @endif

    <div class="card-modern">
        <h3 style="margin-top:0;">Lịch sử điểm toàn hệ thống</h3>
        @if($transactions->isEmpty())
            <p style="color:var(--text-muted);">Chưa có giao dịch điểm.</p>
        @else
            <div class="table-responsive-modern">
                <table class="table-modern">
                    <thead><tr><th>Thời gian</th><th>Loại</th><th>Chi nhánh</th><th>Điểm</th><th>Hạn dùng</th><th>Ghi chú</th></tr></thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $transaction->typeLabel() }}</td>
                                <td>{{ $transaction->center?->name ?? 'Hệ thống' }}</td>
                                <td style="font-weight:700; color:{{ $transaction->points >= 0 ? 'var(--primary-color)' : '#b91c1c' }};">{{ $transaction->points > 0 ? '+' : '' }}{{ number_format($transaction->points) }}</td>
                                <td>{{ $transaction->expired_at ? $transaction->expired_at->format('d/m/Y') : 'Vô hạn' }}</td>
                                <td>{{ $transaction->note }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="display:flex; justify-content:center; margin-top:20px;">{{ $transactions->links() }}</div>
        @endif
    </div>

    <div class="card-modern">
        <h3 style="margin-top:0;">Lịch sử mua tại phạm vi được phép</h3>
        @if($registrations->isEmpty())
            <p style="color:var(--text-muted);">Chưa có giao dịch tại phạm vi hiện tại.</p>
        @else
            <div class="table-responsive-modern">
                <table class="table-modern">
                    <thead><tr><th>Mã đơn</th><th>Người tiêm</th><th>Chi nhánh</th><th>Ngày hẹn</th><th>Thanh toán</th><th>Tổng tiền</th><th></th></tr></thead>
                    <tbody>
                        @foreach($registrations as $registration)
                            <tr>
                                <td>{{ $registration->registration_code }}</td>
                                <td>
                                    @if($registration->patient_id)
                                        <a href="{{ route('admin.patients.show', $registration->patient_id) }}" style="color:var(--primary-color); font-weight:600; text-decoration:none;">
                                            {{ $registration->patient_name }}
                                        </a>
                                    @else
                                        {{ $registration->patient_name }}
                                    @endif
                                </td>
                                <td>{{ $registration->center_name }}</td>
                                <td>{{ $registration->injection_date?->format('d/m/Y') }}</td>
                                <td>{{ $registration->paymentStatusLabel() }}</td>
                                <td>{{ number_format($registration->total_price) }} đ</td>
                                <td><a class="btn-action-sm" href="{{ route('admin.registrations.show', $registration) }}">Xem đơn</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="display:flex; justify-content:center; margin-top:20px;">{{ $registrations->links() }}</div>
        @endif
    </div>
</div>
@endsection
