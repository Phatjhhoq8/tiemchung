@extends('vaccine::layouts.admin')

@section('title', 'Chi tiết đơn đăng ký ' . $registration->registration_code . ' - Medicare Cờ Đỏ')
@section('page_title', 'Chi Tiết Đơn Đăng Ký Tiêm Chủng')

@section('admin_content')
<style>
    .btn-modern-back:hover {
        background: var(--primary-color, #c8102e) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(200, 16, 46, 0.15);
    }
    .btn-modern-back:hover .back-arrow-icon {
        transform: translateX(-4px);
    }
</style>
<div style="max-width: 900px; margin: 0 auto; display: grid; grid-template-columns: 1fr; gap: 30px;">
    
    <!-- Header Back Button & Status Form -->
    <div class="card-modern" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <a href="{{ route('admin.registrations.index') }}" class="btn-modern-back" style="color: var(--primary-color, #c8102e); text-decoration: none; font-size: 14px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; font-family: var(--font-display); padding: 8px 16px; border: 2px solid var(--primary-color, #c8102e); border-radius: 8px; background: #ffffff; transition: all 0.2s ease;">
            <i data-lucide="arrow-left" class="back-arrow-icon" style="width: 16px; height: 16px; transition: transform 0.2s ease;"></i> Quay lại danh sách
        </a>

        <!-- Form cập nhật trạng thái đơn -->
        <form action="{{ route('admin.registrations.status', $registration->id) }}" method="POST" onsubmit="return confirm('Bạn có thực sự muốn cập nhật trạng thái đơn này không?')" style="display: flex; gap: 10px; align-items: center;">
            @csrf
            @method('PATCH')
            <label for="status" class="form-label-modern" style="font-weight: 600; font-size: 14px; margin-bottom: 0;">Trạng thái đơn:</label>
            <select name="status" id="status" class="form-control-modern" style="padding: 8px 12px; font-weight: 500; font-size: 14px; width: 180px; background-image: none;">
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ $registration->status === $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-modern btn-modern-primary" style="padding: 8px 16px; border-radius: 8px;">Cập nhật</button>
        </form>
    </div>

    <!-- Main Content Details Split -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 24px;">
        
        <!-- Cột 1: Thông tin người tiêm & người giám hộ -->
        <div class="card-modern">
            <h3 style="font-family: var(--font-display); font-size: 17px; font-weight: 700; color: var(--text-primary); margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="user" style="color: var(--accent-color);"></i> Thông tin người tiêm
            </h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px; line-height: 2;">
                <tr>
                    <td style="color: var(--text-muted); font-weight: 500; width: 140px;">Mã đơn:</td>
                    <td style="font-weight: 700; color: var(--primary-color);">{{ $registration->registration_code }}</td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); font-weight: 500;">Họ tên:</td>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $registration->patient_name }}</td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); font-weight: 500;">Ngày sinh:</td>
                    <td style="color: var(--text-primary);">{{ date('d/m/Y', strtotime($registration->patient_dob)) }}</td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); font-weight: 500;">Giới tính:</td>
                    <td style="color: var(--text-primary);">{{ $registration->patient_gender }}</td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); font-weight: 500;">Số điện thoại:</td>
                    <td style="color: var(--text-primary);">{{ $registration->patient_phone }}</td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); font-weight: 500;">Địa chỉ:</td>
                    <td style="color: var(--text-primary);">{{ $registration->patient_address }}</td>
                </tr>
            </table>

            @if($registration->guardian_name)
                <h3 style="font-family: var(--font-display); font-size: 17px; font-weight: 700; color: var(--text-primary); margin-top: 30px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="users" style="color: var(--accent-color);"></i> Thông tin người giám hộ
                </h3>
                <table style="width: 100%; border-collapse: collapse; font-size: 14px; line-height: 2;">
                    <tr>
                        <td style="color: var(--text-muted); font-weight: 500; width: 140px;">Người giám hộ:</td>
                        <td style="font-weight: 600; color: var(--text-primary);">{{ $registration->guardian_name }}</td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-muted); font-weight: 500;">Số điện thoại:</td>
                        <td style="color: var(--text-primary);">{{ $registration->guardian_phone }}</td>
                    </tr>
                </table>
            @endif
        </div>

        <!-- Cột 2: Thông tin lịch tiêm & thanh toán -->
        <div class="card-modern">
            <h3 style="font-family: var(--font-display); font-size: 17px; font-weight: 700; color: var(--text-primary); margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="calendar" style="color: var(--accent-color);"></i> Lịch tiêm & Thanh toán
            </h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px; line-height: 2;">
                <tr>
                    <td style="color: var(--text-muted); font-weight: 500; width: 150px;">Trung tâm tiêm:</td>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $registration->center_name }}</td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); font-weight: 500;">Ngày hẹn tiêm:</td>
                    <td style="font-weight: 700; color: var(--primary-color); font-size: 15px;">{{ date('d/m/Y', strtotime($registration->injection_date)) }}</td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); font-weight: 500;">Phương thức TT:</td>
                    <td style="color: var(--text-primary);">{{ $registration->payment_method }}</td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); font-weight: 500;">Trạng thái đơn:</td>
                    <td>
                        <span class="badge-modern 
                            @if($registration->status === 'Đã thanh toán') badge-modern-success
                            @elseif($registration->status === 'Đã tiêm') badge-modern-info
                            @elseif($registration->status === 'Đã hủy') badge-modern-danger
                            @elseif($registration->status === 'Đã tư vấn') badge-modern-success
                            @elseif($registration->status === 'Chờ tư vấn') badge-modern-warning
                            @else badge-modern-warning @endif">
                            {{ $registration->status }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); font-weight: 500;">Thời gian đăng ký:</td>
                    <td style="color: var(--text-primary);">{{ $registration->created_at->format('d/m/Y H:i:s') }}</td>
                </tr>
                <tr style="border-top: 1px solid var(--border-color); margin-top: 10px;">
                    <td style="color: var(--text-primary); font-weight: 700; font-size: 15px; padding-top: 10px;">Tổng chi phí:</td>
                    <td style="font-weight: 800; color: var(--primary-color); font-size: 20px; padding-top: 10px;">{{ number_format($registration->total_price, 0, ',', '.') }} đ</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Danh sách Vắc xin đã đăng ký tiêm -->
    <div class="card-modern">
        <h3 style="font-family: var(--font-display); font-size: 17px; font-weight: 700; color: var(--text-primary); margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="syringe" style="color: var(--accent-color);"></i> Danh sách vắc xin chọn tiêm ({{ $registration->vaccines->count() }})
        </h3>
        
        <div class="table-responsive-modern">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Phân loại</th>
                        <th>Tên vắc xin</th>
                        <th>Nguồn gốc</th>
                        <th style="text-align: center;">Mũi tiêm</th>
                        <th style="text-align: right;">Giá tại thời điểm đăng ký</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registration->vaccines as $vac)
                        <tr>
                            <td>
                                <span class="badge-modern {{ $vac->type === 'package' ? 'badge-modern-warning' : 'badge-modern-info' }}">
                                    {{ $vac->type === 'package' ? 'Gói' : 'Lẻ' }}
                                </span>
                            </td>
                            <td style="font-weight: 600; color: var(--text-primary);">{{ $vac->name }}</td>
                            <td>{{ $vac->origin }}</td>
                            <td style="text-align: center;">{{ $vac->doses }}</td>
                            <td style="text-align: right; font-weight: 600; color: var(--primary-color);">{{ number_format($vac->pivot->price, 0, ',', '.') }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
