@extends('vaccine::layouts.admin')

@section('title', 'Quản lý đơn đặt lịch')
@section('page_title', 'Đơn Đặt Lịch')

@section('admin_content')
<div class="card-modern">
    <div style="display:flex; justify-content:flex-end; margin-bottom:18px;">
        <a href="{{ route('admin.registrations.create', request()->only('center_id')) }}" class="btn-modern btn-modern-primary">Đăng ký nhanh tại quầy</a>
    </div>
    <form action="{{ route('admin.registrations.index') }}" method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:end; margin-bottom:24px;">
        @if($isSuperAdmin ?? false)
            <div style="flex:1 1 180px;">
                <label class="form-label-modern" for="center_id">Chi nhánh</label>
                <select class="form-control-modern" id="center_id" name="center_id">
                    <option value="">Toàn hệ thống</option>
                    @foreach($centers as $center)
                        <option value="{{ $center->id }}" {{ (string) request('center_id') === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div style="flex:1 1 220px;">
            <label class="form-label-modern" for="search">Tìm kiếm</label>
            <input class="form-control-modern" id="search" name="search" value="{{ request('search') }}" placeholder="Mã đơn, tên hoặc SĐT">
        </div>
        <div style="flex:1 1 170px;">
            <label class="form-label-modern" for="booking_status">Lịch hẹn</label>
            <select class="form-control-modern" id="booking_status" name="booking_status">
                <option value="">Tất cả</option>
                @foreach(['pending' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'completed' => 'Đã hoàn tất', 'no_show' => 'Không đến', 'cancelled' => 'Đã hủy'] as $value => $label)
                    <option value="{{ $value }}" {{ request('booking_status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1 1 170px;">
            <label class="form-label-modern" for="payment_status">Thanh toán</label>
            <select class="form-control-modern" id="payment_status" name="payment_status">
                <option value="">Tất cả</option>
                <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
            </select>
        </div>
        <button type="submit" class="btn-modern btn-modern-primary">Lọc</button>
        <a href="{{ route('admin.registrations.export.csv', request()->query()) }}" class="btn-modern btn-modern-secondary">Xuất CSV</a>
    </form>

    @if($registrations->isEmpty())
        <p style="margin:0; color:var(--text-muted);">Không có đơn đặt lịch phù hợp.</p>
    @else
        <div class="table-responsive-modern">
            <table class="table-modern">
                <thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Chi nhánh</th><th>Khung giờ</th><th>Tổng tiền</th><th>Lịch hẹn</th><th>Thanh toán</th><th></th></tr></thead>
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
                            <td><a class="btn-action-sm" href="{{ route('admin.registrations.show', $registration) }}">Chi tiết</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="display:flex; justify-content:center; margin-top:24px;">{{ $registrations->links() }}</div>
    @endif
</div>
@endsection
