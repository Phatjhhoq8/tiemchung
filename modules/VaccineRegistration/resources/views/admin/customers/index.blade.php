@extends('vaccine::layouts.admin')

@section('title', 'Khách hàng và điểm')
@section('page_title', 'Khách Hàng & Điểm')

@section('admin_content')
<div class="card-modern">
    <form method="GET" action="{{ route('admin.customers.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:end; margin-bottom:24px;">
        @if($isSuperAdmin ?? false)
        <div style="flex:1 1 220px;">
            <label class="form-label-modern" for="customer_center_id">Chi nhánh</label>
            <select class="form-control-modern" id="customer_center_id" name="center_id">
                <option value="" {{ $selectedCenterId === null ? 'selected' : '' }}>Tất cả chi nhánh</option>
                @foreach($adminCenters as $center)
                    <option value="{{ $center->id }}" {{ (string) $selectedCenterId === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div style="flex:1 1 280px;">
            <label class="form-label-modern" for="search">{{ ($isSuperAdmin ?? false) ? 'Tên hoặc số điện thoại' : 'Tra cứu chính xác số điện thoại' }}</label>
            <input class="form-control-modern" id="search" type="search" name="search" value="{{ $search }}" placeholder="Ví dụ: 0912345678">
        </div>
        <button class="btn-modern btn-modern-primary" type="submit">Tra cứu</button>
    </form>

    @if(!($isSuperAdmin ?? false) && $search === '')
        <p style="margin:0; color:var(--text-muted);">Nhập số điện thoại để tra cứu khách hàng và số dư điểm dùng chung toàn hệ thống.</p>
    @elseif($customers->isEmpty())
        <p style="margin:0; color:var(--text-muted);">Không tìm thấy khách hàng phù hợp.</p>
    @else
        <div class="table-responsive-modern">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Số điện thoại</th>
                        <th>Số dư điểm</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                        <tr>
                            <td style="font-weight:700;">{{ $customer->name }}</td>
                            <td>{{ \Modules\VaccineRegistration\Support\PhoneNormalizer::display($customer->phone) }}</td>
                            <td style="font-weight:700; color:var(--primary-color);">{{ number_format((int) $customer->point_transactions_sum_points) }} điểm</td>
                            <td><a class="btn-action-sm" href="{{ route('admin.customers.show', $customer) }}">Chi tiết</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="display:flex; justify-content:center; margin-top:24px;">{{ $customers->links() }}</div>
    @endif
</div>
@endsection
