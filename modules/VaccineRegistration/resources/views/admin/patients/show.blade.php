@extends('vaccine::layouts.admin')

@section('title', 'Hồ sơ bệnh nhân ' . $patient->full_name)
@section('page_title', 'Chi Tiết Hồ Sơ Bệnh Nhân')

@section('admin_content')
<div style="display:grid; gap:24px;">
    <!-- Nút quay lại và tiêu đề -->
    <div class="card-modern" style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap;">
        <div>
            <a href="{{ route('admin.patients.index') }}" class="btn-action-sm">Quay lại</a>
            <h2 style="margin:16px 0 6px; color:var(--accent-color);">{{ $patient->full_name }}</h2>
            <p style="margin:0; color:var(--text-muted);">Mã bệnh nhân: #{{ $patient->id }}</p>
        </div>
        <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <!-- Nếu có tài khoản tích điểm tương ứng thì cho link sang xem tài khoản tích điểm -->
            @php
                $customerPhone = \Modules\VaccineRegistration\Support\PhoneNormalizer::normalize($patient->phone);
                $customer = \Modules\VaccineRegistration\Models\Customer::where('phone', $customerPhone)->first();
            @endphp
            @if($customer)
                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn-modern btn-modern-secondary" style="text-decoration:none; display:inline-flex; align-items:center; height:38px;">Xem tài khoản tích điểm</a>
            @endif
            <button onclick="openEditModal()" class="btn-modern btn-modern-primary" style="background:var(--accent-color); border:none; display:inline-flex; align-items:center; height:38px; cursor:pointer; font-weight:700;">Chỉnh sửa thông tin</button>
        </div>
    </div>

    <!-- Thông tin cá nhân & Tiền sử bệnh -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:24px;">
        <div class="card-modern">
            <h3 style="margin-top:0; border-bottom:1px solid #e2e8f0; padding-bottom:10px; color:var(--accent-color);">Thông tin hành chính</h3>
            <table style="width:100%; border-collapse:collapse;">
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 0; font-weight:600; color:var(--text-muted); width:140px;">Ngày sinh:</td>
                    <td style="padding:10px 0; font-weight:700;">{{ $patient->dob ? $patient->dob->format('d/m/Y') : 'Chưa rõ' }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 0; font-weight:600; color:var(--text-muted);">Giới tính:</td>
                    <td style="padding:10px 0; font-weight:700;">{{ $patient->gender === 'male' ? 'Nam' : ($patient->gender === 'female' ? 'Nữ' : $patient->gender) }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 0; font-weight:600; color:var(--text-muted);">Số điện thoại:</td>
                    <td style="padding:10px 0; font-weight:700;">{{ \Modules\VaccineRegistration\Support\PhoneNormalizer::display($patient->phone) }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 0; font-weight:600; color:var(--text-muted);">Số CCCD/Định danh:</td>
                    <td style="padding:10px 0; font-weight:700;">{{ $patient->identity_card ?? 'Chưa cập nhật' }}</td>
                </tr>
                <tr>
                    <td style="padding:10px 0; font-weight:600; color:var(--text-muted);">Địa chỉ:</td>
                    <td style="padding:10px 0; font-weight:700;">{{ $patient->address ?? 'Chưa cập nhật' }}</td>
                </tr>
            </table>
        </div>

        <div class="card-modern">
            <h3 style="margin-top:0; border-bottom:1px solid #e2e8f0; padding-bottom:10px; color:var(--accent-color);">Tiền sử bệnh án</h3>
            <div style="padding:15px; background:#f8fafc; border-radius:8px; min-height:120px; border-left:4px solid var(--secondary-color);">
                @if($patient->medical_history)
                    <p style="margin:0; line-height:1.6; white-space:pre-line; text-align:justify;">{{ $patient->medical_history }}</p>
                @else
                    <p style="margin:0; color:var(--text-muted); font-style:italic;">Không có ghi nhận tiền sử bệnh đặc biệt hoặc dị ứng thuốc/vắc xin.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Lịch sử tiêm chủng thực tế (Administered Doses) -->
    <div class="card-modern">
        <h3 style="margin-top:0; border-bottom:1px solid #e2e8f0; padding-bottom:10px; color:var(--primary-color);">Lịch sử tiêm chủng thực tế</h3>
        @if($patient->administeredDoses->isEmpty())
            <p style="color:var(--text-muted); padding:20px 0; text-align:center; font-style:italic;">Chưa có dữ liệu tiêm chủng thực tế được ghi nhận cho bệnh nhân này.</p>
        @else
            <!-- Client Filter Bar -->
            <div style="display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; align-items: center;">
                <div style="position: relative; flex-grow: 1; max-width: 300px;">
                    <input type="text" id="searchDose" placeholder="Tìm vắc xin, lô..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 13.5px; outline: none;" onkeyup="filterDoses()">
                </div>
                <select id="filterDoseCenter" style="padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 13.5px; color: var(--text-muted); outline: none; background: #ffffff;" onchange="filterDoses()">
                    <option value="">Tất cả chi nhánh</option>
                </select>
            </div>

            <div class="table-responsive-modern">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">STT</th>
                            <th>Thời gian tiêm</th>
                            <th>Vắc xin</th>
                            <th>Lô vắc xin</th>
                            <th>Chi nhánh</th>
                            <th>Người thực hiện</th>
                            <th style="text-align:center;">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody id="doseTableBody">
                        @foreach($patient->administeredDoses->sortByDesc('administered_at') as $dose)
                            <tr>
                                <td style="text-align: center; color:var(--text-muted); font-weight:600;">{{ $loop->iteration }}</td>
                                <td style="font-weight:600;">{{ $dose->administered_at ? $dose->administered_at->format('d/m/Y H:i') : 'Chưa rõ' }}</td>
                                <td class="dose-vaccine" style="font-weight:700; color:var(--accent-color);">{{ $dose->vaccine?->name }}</td>
                                <td><code class="dose-lot" style="background:#f1f5f9; padding:2px 6px; border-radius:4px;">{{ $dose->inventoryLot?->lot_number ?? 'Không rõ lô' }}</code></td>
                                <td class="dose-center">{{ $dose->center?->name }}</td>
                                <td>{{ $dose->administrator?->name ?? 'Hệ thống' }}</td>
                                <td style="text-align:center;">
                                    <span class="badge-modern badge-modern-success" style="background:#d1fae5; color:#065f46;">Đã tiêm xong</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Lịch sử đăng ký đặt tiêm (Registrations) -->
    <div class="card-modern">
        <h3 style="margin-top:0; border-bottom:1px solid #e2e8f0; padding-bottom:10px; color:var(--accent-color);">Lịch sử đăng ký tiêm</h3>
        @if($patient->registrations->isEmpty())
            <p style="color:var(--text-muted); padding:20px 0; text-align:center;">Chưa có đơn đăng ký đặt lịch tiêm nào.</p>
        @else
            <!-- Client Filter Bar -->
            <div style="display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; align-items: center;">
                <div style="position: relative; flex-grow: 1; max-width: 250px;">
                    <input type="text" id="searchReg" placeholder="Tìm mã đơn, chi nhánh..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 13.5px; outline: none;" onkeyup="filterRegs()">
                </div>
                <select id="filterRegStatus" style="padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 13.5px; color: var(--text-muted); outline: none; background: #ffffff;" onchange="filterRegs()">
                    <option value="">Tất cả trạng thái lịch</option>
                    <option value="Đã xác nhận">Đã xác nhận</option>
                    <option value="Đã hoàn thành">Đã hoàn thành</option>
                    <option value="Đã hủy">Đã hủy</option>
                    <option value="Chờ xác nhận">Chờ xác nhận</option>
                </select>
                <select id="filterRegPayment" style="padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 13.5px; color: var(--text-muted); outline: none; background: #ffffff;" onchange="filterRegs()">
                    <option value="">Tất cả thanh toán</option>
                    <option value="Đã thanh toán">Đã thanh toán</option>
                    <option value="Chưa thanh toán">Chưa thanh toán</option>
                </select>
            </div>

            <div class="table-responsive-modern">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">STT</th>
                            <th>Mã đơn</th>
                            <th>Chi nhánh</th>
                            <th>Ngày hẹn tiêm</th>
                            <th>Trạng thái lịch</th>
                            <th>Thanh toán</th>
                            <th>Tổng đơn</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="regTableBody">
                        @foreach($patient->registrations->sortByDesc('injection_date') as $reg)
                            <tr>
                                <td style="text-align: center; color:var(--text-muted); font-weight:600;">{{ $loop->iteration }}</td>
                                <td class="reg-code" style="font-weight:700; color:var(--accent-color);">{{ $reg->registration_code }}</td>
                                <td class="reg-center">{{ $reg->center_name }}</td>
                                <td>{{ $reg->injection_date ? $reg->injection_date->format('d/m/Y') : 'Chưa chọn' }}</td>
                                <td>
                                    @php
                                        $bookingColor = match($reg->booking_status) {
                                            'confirmed' => '#065f46',
                                            'completed' => '#1e3a8a',
                                            'cancelled' => '#991b1b',
                                            default => '#92400e'
                                        };
                                        $bookingBg = match($reg->booking_status) {
                                            'confirmed' => '#d1fae5',
                                            'completed' => '#dbeafe',
                                            'cancelled' => '#fee2e2',
                                            default => '#fef3c7'
                                        };
                                    @endphp
                                    <span class="badge-modern reg-status" style="background:{{ $bookingBg }}; color:{{ $bookingColor }}; font-weight:700; padding:4px 8px; border-radius:6px; font-size:12px;">
                                        {{ match($reg->booking_status) {
                                            'confirmed' => 'Đã xác nhận',
                                            'completed' => 'Đã hoàn thành',
                                            'cancelled' => 'Đã hủy',
                                            default => 'Chờ xác nhận'
                                        } }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-modern reg-payment" style="background:{{ $reg->payment_status === 'paid' ? '#d1fae5' : '#fee2e2' }}; color:{{ $reg->payment_status === 'paid' ? '#065f46' : '#991b1b' }}; font-weight:700; padding:4px 8px; border-radius:6px; font-size:12px;">
                                        {{ $reg->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                    </span>
                                </td>
                                <td style="font-weight:700; color:var(--primary-color);">{{ number_format($reg->total_price) }} đ</td>
                                <td style="text-align:right;">
                                    <a href="{{ route('admin.registrations.show', $reg->id) }}" class="btn-action-sm">Xem chi tiết</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Modal Chỉnh sửa thông tin -->
<div id="editPatientModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.5); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:15px; box-sizing:border-box;">
    <div class="card-modern" style="width:100%; max-width:600px; margin:auto; box-shadow:0 10px 25px rgba(0,0,0,0.25); animation: slideDown 0.3s ease-out; position:relative; max-height: 90vh; overflow-y: auto; text-align: left; padding: 24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:15px; margin-bottom:20px;">
            <h3 style="margin:0; color:var(--accent-color); font-size:18px;">Chỉnh sửa Hồ sơ Bệnh nhân</h3>
            <button onclick="closeEditModal()" style="background:none; border:none; font-size:24px; cursor:pointer; color:#94a3b8; font-weight:bold; line-height:1;">&times;</button>
        </div>
        
        <form action="{{ route('admin.patients.update', $patient->id) }}" method="POST" style="display:grid; gap:16px;">
            @csrf
            @method('PATCH')
            
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                <div style="grid-column: 1 / -1;">
                    <label class="form-label-modern" for="full_name">Họ và tên <span style="color:#dc2626;">*</span></label>
                    <input class="form-control-modern" id="full_name" type="text" name="full_name" value="{{ $patient->full_name }}" required>
                </div>
                
                <div>
                    <label class="form-label-modern" for="dob">Ngày sinh <span style="color:#dc2626;">*</span></label>
                    <input class="form-control-modern" id="dob" type="date" name="dob" value="{{ $patient->dob ? $patient->dob->format('Y-m-d') : '' }}" required>
                </div>
                
                <div>
                    <label class="form-label-modern" for="gender">Giới tính <span style="color:#dc2626;">*</span></label>
                    <select class="form-control-modern" id="gender" name="gender" required>
                        <option value="male" {{ $patient->gender === 'male' ? 'selected' : '' }}>Nam</option>
                        <option value="female" {{ $patient->gender === 'female' ? 'selected' : '' }}>Nữ</option>
                        <option value="other" {{ (!in_array($patient->gender, ['male', 'female'])) ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>
                
                <div>
                    <label class="form-label-modern" for="phone">Số điện thoại <span style="color:#dc2626;">*</span></label>
                    <input class="form-control-modern" id="phone" type="text" name="phone" value="{{ $patient->phone }}" required>
                </div>
                
                <div>
                    <label class="form-label-modern" for="identity_card">Số CCCD / Định danh</label>
                    <input class="form-control-modern" id="identity_card" type="text" name="identity_card" value="{{ $patient->identity_card }}">
                </div>
            </div>
            
            <div>
                <label class="form-label-modern" for="address">Địa chỉ</label>
                <input class="form-control-modern" id="address" type="text" name="address" value="{{ $patient->address }}">
            </div>
            
            <div>
                <label class="form-label-modern" for="medical_history">Tiền sử bệnh án / Dị ứng</label>
                <textarea class="form-control-modern" id="medical_history" name="medical_history" rows="4" placeholder="Nhập tiền sử bệnh lý, dị ứng vắc xin, chống chỉ định..." style="resize:vertical; font-family:inherit; padding:10px; line-height:1.5;">{{ $patient->medical_history }}</textarea>
            </div>
            
            <div style="display:flex; justify-content:flex-end; gap:12px; border-top:1px solid #e2e8f0; padding-top:15px; margin-top:10px;">
                <button type="button" onclick="closeEditModal()" class="btn-modern btn-modern-secondary" style="height:38px; cursor:pointer;">Hủy</button>
                <button type="submit" class="btn-modern btn-modern-primary" style="height:38px; cursor:pointer; background:var(--primary-color);">Cập nhật hồ sơ</button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes slideDown {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>

<script>
function openEditModal() {
    const modal = document.getElementById('editPatientModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    const modal = document.getElementById('editPatientModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

// Close modal when clicking outside contents
window.addEventListener('click', function(event) {
    const modal = document.getElementById('editPatientModal');
    if (event.target == modal) {
        closeEditModal();
    }
});

// Client-side filtering logic for administered doses
function filterDoses() {
    const searchVal = document.getElementById('searchDose').value.toLowerCase();
    const centerVal = document.getElementById('filterDoseCenter').value;
    const rows = document.querySelectorAll('#doseTableBody tr');
    
    rows.forEach(row => {
        const vaccine = row.querySelector('.dose-vaccine').textContent.toLowerCase();
        const lot = row.querySelector('.dose-lot').textContent.toLowerCase();
        const center = row.querySelector('.dose-center').textContent;
        
        const matchesSearch = vaccine.includes(searchVal) || lot.includes(searchVal);
        const matchesCenter = centerVal === '' || center === centerVal;
        
        row.style.display = (matchesSearch && matchesCenter) ? '' : 'none';
    });
}

// Client-side filtering logic for registrations
function filterRegs() {
    const searchVal = document.getElementById('searchReg').value.toLowerCase();
    const statusVal = document.getElementById('filterRegStatus').value;
    const paymentVal = document.getElementById('filterRegPayment').value;
    const rows = document.querySelectorAll('#regTableBody tr');
    
    rows.forEach(row => {
        const code = row.querySelector('.reg-code').textContent.toLowerCase();
        const center = row.querySelector('.reg-center').textContent.toLowerCase();
        const status = row.querySelector('.reg-status').textContent.trim();
        const payment = row.querySelector('.reg-payment').textContent.trim();
        
        const matchesSearch = code.includes(searchVal) || center.includes(searchVal);
        const matchesStatus = statusVal === '' || status === statusVal;
        const matchesPayment = paymentVal === '' || payment === paymentVal;
        
        row.style.display = (matchesSearch && matchesStatus && matchesPayment) ? '' : 'none';
    });
}

// Dynamically populate centers list from table data
document.addEventListener('DOMContentLoaded', () => {
    const centerSelect = document.getElementById('filterDoseCenter');
    if (centerSelect) {
        const centers = new Set();
        document.querySelectorAll('#doseTableBody .dose-center').forEach(el => {
            centers.add(el.textContent.trim());
        });
        centers.forEach(c => {
            if (c) {
                const opt = document.createElement('option');
                opt.value = c;
                opt.textContent = c;
                centerSelect.appendChild(opt);
            }
        });
    }
});
</script>
@endsection
