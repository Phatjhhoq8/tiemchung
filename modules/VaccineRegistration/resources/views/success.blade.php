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
        <p>Hệ thống đã ghi nhận lịch hẹn tiêm chủng của bạn. Mã hồ sơ của bạn là:</p>
        <div class="registration-code-badge">
            <span>{{ $registration->registration_code }}</span>
        </div>
    </div>

    <div class="success-layout">
        <!-- Receipt/Info Ticket -->
        <div class="ticket-card">
            <div class="ticket-header">
                <h3>PHIẾU ĐĂNG KÝ TIÊM CHỦNG</h3>
                <span>Hệ thống tiêm chủng trẻ em & người lớn VNVC</span>
            </div>
            
            <div class="ticket-body">
                <div class="ticket-section">
                    <h4><i data-lucide="user"></i> Thông tin người tiêm</h4>
                    <table class="ticket-table">
                        <tr>
                            <th>Họ tên người tiêm:</th>
                            <td>{{ $registration->patient_name }}</td>
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
                            <td>Sáng: 7h30 - 11h30 | Chiều: 13h30 - 17h30 (Không nghỉ trưa thứ 7 & CN)</td>
                        </tr>
                    </table>
                </div>

                <div class="ticket-section">
                    <h4><i data-lucide="syringe"></i> Danh sách vắc xin chọn tiêm</h4>
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
                        <span>Tổng chi phí thanh toán:</span>
                        <strong>{{ number_format($registration->total_price, 0, ',', '.') }} đ</strong>
                    </div>
                    <div class="total-row">
                        <span>Trạng thái:</span>
                        <span class="status-badge {{ $registration->status === 'Đã thanh toán' ? 'status-paid' : 'status-pending' }}">
                            {{ $registration->status }}
                        </span>
                    </div>
                    <div class="total-row">
                        <span>Phương thức thanh toán:</span>
                        <span>{{ $registration->payment_method }}</span>
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
                <p>Xuất trình mã này tại quầy tiếp đón trung tâm tiêm chủng để check-in và nhận số thứ tự khám nhanh chóng.</p>
            </div>
        </div>

        <!-- Payment Guidance / Action Side -->
        <div class="payment-action-card">
            @if($registration->status === 'Đã thanh toán' && ($registration->payment_method === 'QR' || $registration->payment_method === 'Thẻ'))
                <div class="payment-success-box">
                    <div class="payment-success-icon"><i data-lucide="credit-card"></i></div>
                    <h3>Thanh Toán Thành Công!</h3>
                    <p>Giao dịch của bạn đã hoàn tất trực tuyến. VNVC đã dành riêng và lưu trữ các liều vắc xin của bạn trong hệ thống lưu trữ lạnh tiêu chuẩn quốc tế GSP.</p>
                    <div class="action-buttons">
                        <a href="{{ route('vaccine.index') }}" class="btn-primary">Về trang chủ</a>
                        <button onclick="window.print()" class="btn-secondary"><i data-lucide="printer"></i> In phiếu đăng ký</button>
                    </div>
                </div>
            @else
                <!-- Thanh toán tại quầy hoặc QR quét thanh toán giả lập -->
                @if($registration->payment_method === 'QR')
                    <div class="qr-payment-box">
                        <h3>Quét QR Thanh Toán Để Giữ Chỗ</h3>
                        <p>Vui lòng chuyển khoản đúng số tiền để hệ thống tự động xác nhận và kích hoạt giữ chỗ vắc xin cho bạn.</p>
                        
                        <!-- VietQR API giả lập -->
                        <div class="qr-code-wrapper">
                            <img src="https://api.vietqr.io/image/970415-113113113-qr_only.jpg?amount={{ $registration->total_price }}&addInfo={{ $registration->registration_code }}&accountName=VNVC%20TIEM%20CHUNG" alt="Mã VietQR Thanh Toán">
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
                                <strong>CONG TY CO PHAN TIEM CHUNG VNVC</strong>
                            </div>
                            <div class="detail-row">
                                <span>Số tiền chuyển khoản:</span>
                                <strong class="price-highlight">{{ number_format($registration->total_price, 0, ',', '.') }} đ</strong>
                            </div>
                            <div class="detail-row">
                                <span>Nội dung chuyển khoản (Bắt buộc):</span>
                                <strong class="code-highlight">{{ $registration->registration_code }}</strong>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <a href="{{ route('vaccine.index') }}" class="btn-primary">Hoàn thành</a>
                            <button onclick="window.print()" class="btn-secondary"><i data-lucide="printer"></i> In phiếu</button>
                        </div>
                    </div>
                @else
                    <div class="counter-payment-box">
                        <div class="later-payment-icon"><i data-lucide="landmark"></i></div>
                        <h3>Hướng dẫn thanh toán tại quầy</h3>
                        <p>Bạn đã chọn phương thức thanh toán tại trung tâm tiêm chủng. Vui lòng thực hiện các bước sau:</p>
                        <ol class="step-guide">
                            <li>Lưu mã đăng ký hoặc chụp ảnh màn hình phiếu tiêm chủng này.</li>
                            <li>Đến trung tâm <strong>{{ $registration->center_name }}</strong> vào ngày <strong>{{ date('d/m/Y', strtotime($registration->injection_date)) }}</strong>.</li>
                            <li>Xuất trình mã đăng ký tại Quầy đăng ký tiếp nhận để check-in và thanh toán trước khi vào khám lâm sàng và tiêm chủng.</li>
                        </ol>
                        
                        <div class="warning-alert">
                            <i data-lucide="alert-triangle"></i>
                            <p><strong>Lưu ý:</strong> Thanh toán tại quầy không đảm bảo giữ chỗ vắc xin trong trường hợp loại vắc xin đó khan hiếm trên hệ thống. Chúng tôi khuyên bạn nên chọn thanh toán QR để giữ vắc xin trước.</p>
                        </div>

                        <div class="action-buttons">
                            <a href="{{ route('vaccine.index') }}" class="btn-primary">Về trang chủ</a>
                            <button onclick="window.print()" class="btn-secondary"><i data-lucide="printer"></i> In phiếu</button>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
