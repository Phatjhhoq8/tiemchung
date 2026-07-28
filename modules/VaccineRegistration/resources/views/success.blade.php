@extends('vaccine::layouts.app')

@section('title', 'Đăng Ký Tiêm Chủng Thành Công')

@section('content')
<div class="success-container">
    <!-- Success Banner -->
    <div class="success-banner">
        <div class="success-icon-animation">
            <i data-lucide="check-circle"></i>
        </div>
        <h1>Đăng Ký Tiêm Chủng Thành Công!</h1>
        <p>Hệ thống đã ghi nhận lịch hẹn tiêm chủng của bạn. Mã hồ sơ của bạn:</p>
        <div class="registration-code-badge" style="display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; margin-top: 10px;">
            @foreach($registrations as $reg)
                <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 6px; font-weight: 800; font-size: 15px;">{{ $reg->registration_code }}</span>
            @endforeach
        </div>
    </div>

    <div class="success-layout" style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 30px; align-items: start;">
        <!-- Left Column: Patient Tickets -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            @foreach($registrations as $index => $registration)
                <!-- Ticket Card -->
                <div class="ticket-card" style="margin: 0; width: 100%;">
                    <div class="ticket-header" style="background: var(--accent-color, #004b8f);">
                        <h3>PHIẾU ĐĂNG KÝ TIÊM CHỦNG #{{ $index + 1 }}</h3>
                        <span>Hệ thống phòng tiêm chủng Medicare Cờ Đỏ</span>
                    </div>
                    
                    <div class="ticket-body">
                        <div class="ticket-section">
                            <h4><i data-lucide="user"></i> Thông tin người tiêm</h4>
                            <table class="ticket-table">
                                <tr>
                                    <th>Họ tên người tiêm:</th>
                                    <td><strong>{{ $registration->patient_name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Ngày sinh:</th>
                                    <td>{{ date('d/m/Y', strtotime($registration->patient_dob)) }}</td>
                                </tr>
                                <tr>
                                    <th>Giới tính:</th>
                                    <td>{{ $registration->patient_gender }}</td>
                                </tr>
                                <tr>
                                    <th>Số điện thoại:</th>
                                    <td>{{ $registration->patient_phone }}</td>
                                </tr>
                                <tr>
                                    <th>Địa chỉ:</th>
                                    <td>{{ $registration->patient_address }}</td>
                                </tr>
                                @if($registration->guardian_name)
                                <tr>
                                    <th>Người giám hộ:</th>
                                    <td>{{ $registration->guardian_name }} (SĐT: {{ $registration->guardian_phone }})</td>
                                </tr>
                                @endif
                            </table>
                        </div>

                        <div class="ticket-section">
                            <h4><i data-lucide="calendar"></i> Lịch hẹn tiêm chủng</h4>
                            <table class="ticket-table">
                                <tr>
                                    <th>Trung tâm tiêm:</th>
                                    <td>{{ $registration->center_name }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày hẹn tiêm:</th>
                                    <td class="highlight-date">{{ date('d/m/Y', strtotime($registration->injection_date)) }}</td>
                                </tr>
                                <tr>
                                    <th>Thời gian làm việc:</th>
                                    <td>Sáng: 7h30 - 11h30 | Chiều: 13h30 - 17h30 (Mở cửa tất cả các ngày trong tuần)</td>
                                </tr>
                            </table>
                        </div>

                        <div class="ticket-section">
                            <h4><i data-lucide="syringe"></i> Vắc xin chọn tiêm cho người này</h4>
                            <div class="ticket-vaccines-list">
                                @foreach($registration->vaccines as $vaccine)
                                    <div class="ticket-vaccine-item">
                                        <div class="vaccine-info">
                                            <strong>{{ $vaccine->name }}</strong>
                                            <span>{{ $vaccine->origin }}</span>
                                        </div>
                                        <span class="vaccine-price">{{ number_format($vaccine->pivot->price, 0, ',', '.') }} đ</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="ticket-section-total">
                            <div class="total-row">
                                <span>Phí tiêm chủng người này:</span>
                                <strong style="color: var(--accent-color, #004b8f);">{{ number_format($registration->total_price, 0, ',', '.') }} đ</strong>
                            </div>
                            <div class="total-row">
                                <span>Trạng thái:</span>
                                <span class="status-badge {{ $registration->status === 'Đã thanh toán' ? 'status-paid' : 'status-pending' }}">
                                    {{ $registration->status }}
                                </span>
                            </div>
                            <div class="total-row">
                                <span>Mã hồ sơ:</span>
                                <strong style="color: var(--primary-color);">{{ $registration->registration_code }}</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ticket-footer">
                        <div class="barcode-mock">
                            <div class="barcode-lines">
                                <span></span><span></span><span></span><span></span><span></span><span></span>
                                <span></span><span></span><span></span><span></span><span></span><span></span>
                                <span></span><span></span><span></span><span></span><span></span><span></span>
                            </div>
                            <span class="barcode-text">{{ $registration->registration_code }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Right Column: Grand Total & Combined Payment Box -->
        <div class="payment-action-card" style="margin: 0; width: 100%; position: sticky; top: 20px;">
            @php
                $firstReg = $registrations->first();
                $allCodesStr = implode(', ', $registrations->pluck('registration_code')->toArray());
                $memoCodes = implode('-', $registrations->pluck('registration_code')->toArray());
            @endphp

            @if($firstReg->status === 'Đã thanh toán' && ($firstReg->payment_method === 'QR' || $firstReg->payment_method === 'Thẻ'))
                <div class="payment-success-box">
                    <div class="payment-success-icon"><i data-lucide="credit-card"></i></div>
                    <h3>Thanh Toán Thành Công!</h3>
                    <p>Giao dịch của bạn đã hoàn tất trực tuyến. Medicare Cờ Đỏ đã dành riêng và lưu trữ các liều vắc xin của bạn trong hệ thống bảo quản lạnh tiêu chuẩn GSP.</p>
                    <div class="action-buttons">
                        <a href="{{ route('home') }}" class="btn-primary">Về trang chủ</a>
                        <button onclick="window.print()" class="btn-secondary"><i data-lucide="printer"></i> In phiếu đăng ký</button>
                    </div>
                </div>
            @else
                <!-- Thanh toán tại quầy hoặc QR quét thanh toán giả lập -->
                @if($firstReg->payment_method === 'QR')
                    <div class="qr-payment-box">
                        <h3>Quét QR Thanh Toán Tổng Hóa Đơn</h3>
                        <p>Vui lòng chuyển khoản đúng số tiền để hệ thống tự động xác nhận và kích hoạt giữ chỗ vắc xin cho tất cả người tiêm.</p>
                        
                        <!-- VietQR API kết hợp số tiền tổng -->
                        <div class="qr-code-wrapper">
                            <img src="https://api.vietqr.io/image/970415-113113113-qr_only.jpg?amount={{ $grandTotal }}&addInfo={{ $memoCodes }}&accountName=MEDICARE%20CO%20DO" alt="Mã VietQR Thanh Toán">
                        </div>

                        <div class="payment-transfer-details">
                            <div class="detail-row">
                                <span>Ngân hàng:</span>
                                <strong>VietinBank (ICB)</strong>
                            </div>
                            <div class="detail-row">
                                <span>Số tài khoản:</span>
                                <strong>113 113 113</strong>
                            </div>
                            <div class="detail-row">
                                <span>Chủ tài khoản:</span>
                                <strong>PHONG TIEM CHUNG MEDICARE CO DO</strong>
                            </div>
                            <div class="detail-row">
                                <span>Tổng tiền thanh toán ({{ $registrations->count() }} người):</span>
                                <strong class="price-highlight" style="color: var(--primary-color, #c8102e); font-size: 19px;">{{ number_format($grandTotal, 0, ',', '.') }} đ</strong>
                            </div>
                            <div class="detail-row">
                                <span>Nội dung chuyển khoản (Bắt buộc):</span>
                                <strong class="code-highlight" style="font-size: 14px; word-break: break-all;">{{ $memoCodes }}</strong>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <a href="{{ route('home') }}" class="btn-primary" style="background: var(--primary-color);">Hoàn thành</a>
                            <button onclick="window.print()" class="btn-secondary"><i data-lucide="printer"></i> In phiếu</button>
                        </div>
                    </div>
                @else
                    <div class="counter-payment-box">
                        <div class="later-payment-icon"><i data-lucide="landmark"></i></div>
                        <h3>Hướng dẫn thanh toán tại quầy</h3>
                        <p>Bạn đã chọn phương thức thanh toán tại trung tâm tiêm chủng. Vui lòng thực hiện các bước sau:</p>
                        <ol class="step-guide">
                            <li>Lưu mã đăng ký hoặc chụp ảnh màn hình các phiếu tiêm chủng này.</li>
                            <li>Đến trung tâm <strong>{{ $firstReg->center_name }}</strong> vào ngày <strong>{{ date('d/m/Y', strtotime($firstReg->injection_date)) }}</strong>.</li>
                            <li>Xuất trình danh sách mã đăng ký: <strong>{{ $allCodesStr }}</strong> tại Quầy tiếp đón để check-in và thanh toán tiền tổng: <strong>{{ number_format($grandTotal, 0, ',', '.') }} đ</strong> trước khi tiêm.</li>
                        </ol>
                        
                        <div class="warning-alert">
                            <i data-lucide="alert-triangle"></i>
                            <p><strong>Lưu ý:</strong> Thanh toán tại quầy không đảm bảo giữ chỗ vắc xin trong trường hợp loại vắc xin đó khan hiếm trên hệ thống. Chúng tôi khuyên bạn nên chọn thanh toán QR để giữ vắc xin trước.</p>
                        </div>

                        <div class="action-buttons">
                            <a href="{{ route('home') }}" class="btn-primary" style="background: var(--primary-color);">Về trang chủ</a>
                            <button onclick="window.print()" class="btn-secondary"><i data-lucide="printer"></i> In phiếu</button>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
