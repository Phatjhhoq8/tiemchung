@if(!($isSuperAdmin ?? false) && ($search ?? '') === '')
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
