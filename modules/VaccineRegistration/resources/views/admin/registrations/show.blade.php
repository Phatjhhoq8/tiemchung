@extends('vaccine::layouts.admin')

@section('title', 'Chi tiết đơn đăng ký ' . $registration->registration_code . ' - Medicare Cờ Đỏ')
@section('page_title', 'Chi Tiết Đơn Đăng Ký Tiêm Chủng')

@section('admin_content')
<div style="max-width: 900px; margin: 0 auto; display: grid; grid-template-columns: 1fr; gap: 30px;">
    
    <!-- Header Back Button & Status Form -->
    <div style="background-color: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <a href="{{ route('admin.registrations.index') }}" style="color: #64748b; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Quay lại danh sách
        </a>

        <!-- Form cập nhật trạng thái đơn -->
        <form action="{{ route('admin.registrations.status', $registration->id) }}" method="POST" style="display: flex; gap: 10px; align-items: center;">
            @csrf
            @method('PATCH')
            <label for="status" style="font-weight: 600; color: #475569; font-size: 14px;">Trạng thái đơn:</label>
            <select name="status" id="status" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #ffffff; font-weight: 500; font-size: 14px;">
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ $registration->status === $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary" style="padding: 8px 16px; border-radius: 8px; border: none; color: #ffffff; font-weight: 600; cursor: pointer; font-size: 14px;">Cập nhật</button>
        </form>
    </div>

    <!-- Main Content Details Split -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 24px;">
        
        <!-- Cột 1: Thông tin người tiêm & người giám hộ -->
        <div style="background-color: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 30px;">
            <h3 style="font-family: 'Roboto', sans-serif; font-size: 17px; font-weight: 700; color: #1e293b; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; display: flex; align-items: center; gap: 8px;"><i data-lucide="user" style="color:var(--primary-color);"></i> Thông tin người tiêm</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px; line-height: 2;">
                <tr>
                    <td style="color: #64748b; font-weight: 500; width: 140px;">Mã đơn:</td>
                    <td style="font-weight: 700; color: var(--primary-color);">{{ $registration->registration_code }}</td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 500;">Họ tên:</td>
                    <td style="font-weight: 600; color: #1e293b;">{{ $registration->patient_name }}</td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 500;">Ngày sinh:</td>
                    <td>{{ date('d/m/Y', strtotime($registration->patient_dob)) }}</td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 500;">Giới tính:</td>
                    <td>{{ $registration->patient_gender }}</td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 500;">Số điện thoại:</td>
                    <td>{{ $registration->patient_phone }}</td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 500;">Địa chỉ:</td>
                    <td>{{ $registration->patient_address }}</td>
                </tr>
            </table>

            @if($registration->guardian_name)
                <h3 style="font-family: 'Roboto', sans-serif; font-size: 17px; font-weight: 700; color: #1e293b; margin-top: 30px; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; display: flex; align-items: center; gap: 8px;"><i data-lucide="users" style="color:var(--primary-color);"></i> Thông tin người giám hộ</h3>
                <table style="width: 100%; border-collapse: collapse; font-size: 14px; line-height: 2;">
                    <tr>
                        <td style="color: #64748b; font-weight: 500; width: 140px;">Người giám hộ:</td>
                        <td style="font-weight: 600; color: #1e293b;">{{ $registration->guardian_name }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; font-weight: 500;">Số điện thoại:</td>
                        <td>{{ $registration->guardian_phone }}</td>
                    </tr>
                </table>
            @endif
        </div>

        <!-- Cột 2: Thông tin lịch tiêm & thanh toán -->
        <div style="background-color: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 30px;">
            <h3 style="font-family: 'Roboto', sans-serif; font-size: 17px; font-weight: 700; color: #1e293b; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; display: flex; align-items: center; gap: 8px;"><i data-lucide="calendar" style="color:var(--primary-color);"></i> Lịch tiêm & Thanh toán</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px; line-height: 2;">
                <tr>
                    <td style="color: #64748b; font-weight: 500; width: 150px;">Trung tâm tiêm:</td>
                    <td style="font-weight: 600; color: #1e293b;">{{ $registration->center_name }}</td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 500;">Ngày hẹn tiêm:</td>
                    <td style="font-weight: 600; color: #c8102e; font-size: 15px;">{{ date('d/m/Y', strtotime($registration->injection_date)) }}</td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 500;">Phương thức TT:</td>
                    <td>{{ $registration->payment_method }}</td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 500;">Trạng thái đơn:</td>
                    <td>
                        <span class="badge" style="padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; display: inline-block;
                            @if($registration->status === 'Đã thanh toán') background-color: #def7ec; color: #03543f;
                            @elseif($registration->status === 'Đã tiêm') background-color: #e1effe; color: #1e429f;
                            @elseif($registration->status === 'Đã hủy') background-color: #fde8e8; color: #9b1c1c;
                            @else background-color: #fef08a; color: #713f12; @endif">
                            {{ $registration->status }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 500;">Thời gian đăng ký:</td>
                    <td>{{ $registration->created_at->format('d/m/Y H:i:s') }}</td>
                </tr>
                <tr style="border-top: 1px solid #e2e8f0; margin-top: 10px;">
                    <td style="color: #64748b; font-weight: 700; font-size: 15px; padding-top: 10px;">Tổng chi phí:</td>
                    <td style="font-weight: 800; color: var(--primary-color); font-size: 20px; padding-top: 10px;">{{ number_format($registration->total_price, 0, ',', '.') }} đ</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Danh sách Vắc xin đã đăng ký tiêm -->
    <div style="background-color: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 30px;">
        <h3 style="font-family: 'Roboto', sans-serif; font-size: 17px; font-weight: 700; color: #1e293b; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; display: flex; align-items: center; gap: 8px;"><i data-lucide="syringe" style="color:var(--primary-color);"></i> Danh sách vắc xin chọn tiêm ({{ $registration->vaccines->count() }})</h3>
        
        <table style="width: 100%; border-collapse: collapse; font-size: 14px; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #cbd5e1; color: #475569;">
                    <th style="padding: 10px 12px;">Phân loại</th>
                    <th style="padding: 10px 12px;">Tên vắc xin</th>
                    <th style="padding: 10px 12px;">Nguồn gốc</th>
                    <th style="padding: 10px 12px; text-align: center;">Mũi tiêm</th>
                    <th style="padding: 10px 12px; text-align: right;">Giá tại thời điểm đăng ký</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registration->vaccines as $vac)
                    <tr style="border-bottom: 1px solid #e2e8f0; color: #334155;">
                        <td style="padding: 12px 12px;">
                            <span style="padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase;
                                @if($vac->type === 'package') background-color: #fef3c7; color: #d97706;
                                @else background-color: #e0f2fe; color: #0369a1; @endif">
                                {{ $vac->type === 'package' ? 'Gói vắc xin' : 'Vắc xin lẻ' }}
                            </span>
                        </td>
                        <td style="padding: 12px 12px; font-weight: 600;">{{ $vac->name }}</td>
                        <td style="padding: 12px 12px;">{{ $vac->origin }}</td>
                        <td style="padding: 12px 12px; text-align: center;">{{ $vac->doses }}</td>
                        <td style="padding: 12px 12px; text-align: right; font-weight: 600; color: var(--primary-color);">{{ number_format($vac->pivot->price, 0, ',', '.') }} đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
