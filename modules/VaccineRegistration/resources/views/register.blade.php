@extends('vaccine::layouts.app')

@section('title', 'Đăng Ký Thông Tin Tiêm Chủng')

@section('content')
<div class="registration-container">
    <!-- Progress Stepper -->
    <div class="stepper">
        <div class="step active" id="stepIndicator1">
            <span class="step-num">1</span>
            <span class="step-label">Thông tin người tiêm</span>
        </div>
        <div class="step-line" id="stepLine1"></div>
        <div class="step" id="stepIndicator2">
            <span class="step-num">2</span>
            <span class="step-label">Địa điểm & Ngày tiêm</span>
        </div>
        <div class="step-line" id="stepLine2"></div>
        <div class="step" id="stepIndicator3">
            <span class="step-num">3</span>
            <span class="step-label">Thanh toán & Xác nhận</span>
        </div>
    </div>

    <div class="registration-layout">
        <!-- Main Form Column -->
        <div class="form-card">
            <form action="{{ route('register.post') }}" method="POST" id="regForm">
                @csrf
                
                @if(session('error'))
                    <div class="alert alert-danger">
                        <i data-lucide="alert-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i data-lucide="alert-circle"></i>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- STEP 1: Thông tin người tiêm -->
                <div class="form-step-content active" id="stepContent1">
                    <h2>Thông tin cá nhân người tiêm</h2>
                    <p class="step-desc">Vui lòng điền chính xác thông tin của người trực tiếp tiêm vắc xin.</p>
                    
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="patient_name">Họ tên người tiêm <span class="required">*</span></label>
                            <input type="text" name="patient_name" id="patient_name" value="{{ old('patient_name') }}" placeholder="Ví dụ: Nguyễn Văn A" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="patient_dob">Ngày sinh <span class="required">*</span></label>
                            <input type="date" name="patient_dob" id="patient_dob" value="{{ old('patient_dob') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Giới tính <span class="required">*</span></label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="patient_gender" value="Nam" {{ old('patient_gender', 'Nam') === 'Nam' ? 'checked' : '' }}>
                                    <span>Nam</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="patient_gender" value="Nữ" {{ old('patient_gender') === 'Nữ' ? 'checked' : '' }}>
                                    <span>Nữ</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="patient_phone">Số điện thoại liên hệ <span class="required">*</span></label>
                            <input type="text" name="patient_phone" id="patient_phone" value="{{ old('patient_phone') }}" placeholder="Ví dụ: 0987654321" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="patient_address">Địa chỉ thường trú <span class="required">*</span></label>
                            <input type="text" name="patient_address" id="patient_address" value="{{ old('patient_address') }}" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố" required>
                        </div>
                    </div>

                    <!-- Giám hộ (cho trẻ em) -->
                    <div class="guardian-section" id="guardianSection" style="display: none;">
                        <h3>Thông tin người liên hệ (Bố/Mẹ/Người giám hộ)</h3>
                        <p class="step-desc">Yêu cầu khi người tiêm là trẻ em dưới 15 tuổi.</p>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="guardian_name">Họ tên người giám hộ</label>
                                <input type="text" name="guardian_name" id="guardian_name" value="{{ old('guardian_name') }}" placeholder="Ví dụ: Nguyễn Văn B">
                            </div>
                            <div class="form-group">
                                <label for="guardian_phone">Số điện thoại người giám hộ</label>
                                <input type="text" name="guardian_phone" id="guardian_phone" value="{{ old('guardian_phone') }}" placeholder="Ví dụ: 0912345678">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div></div>
                        <button type="button" class="btn-primary" onclick="nextStep(1)">
                            <span>Tiếp tục</span> <i data-lucide="arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 2: Địa điểm & Ngày tiêm -->
                <div class="form-step-content" id="stepContent2">
                    <h2>Chọn địa điểm và thời gian tiêm</h2>
                    <p class="step-desc">Chọn trung tâm tiêm chủng VNVC gần nhất và ngày mong muốn thực hiện tiêm chủng.</p>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="center_name">Trung tâm tiêm chủng VNVC <span class="required">*</span></label>
                            <select name="center_name" id="center_name" required>
                                <option value="">-- Chọn trung tâm VNVC gần bạn nhất --</option>
                                @foreach($centers as $center)
                                    <option value="{{ $center }}" {{ old('center_name') === $center ? 'selected' : '' }}>{{ $center }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="injection_date">Ngày mong muốn tiêm <span class="required">*</span></label>
                            <input type="date" name="injection_date" id="injection_date" value="{{ old('injection_date') }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="prevStep(2)">
                            <i data-lucide="arrow-left"></i> <span>Quay lại</span>
                        </button>
                        <button type="button" class="btn-primary" onclick="nextStep(2)">
                            <span>Tiếp tục</span> <i data-lucide="arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 3: Phương thức thanh toán -->
                <div class="form-step-content" id="stepContent3">
                    <h2>Chọn phương thức thanh toán</h2>
                    <p class="step-desc">Vui lòng chọn cách thức thanh toán chi phí vắc xin để hoàn tất thủ tục đăng ký.</p>

                    <div class="payment-methods">
                        <label class="payment-card">
                            <input type="radio" name="payment_method" value="QR" {{ old('payment_method', 'QR') === 'QR' ? 'checked' : '' }} onchange="togglePaymentDesc('qr')">
                            <div class="payment-card-content">
                                <div class="payment-icon"><i data-lucide="qr-code"></i></div>
                                <div class="payment-text">
                                    <strong>Chuyển khoản / Quét mã QR</strong>
                                    <span>Hệ thống tạo mã QR động hỗ trợ mọi ngân hàng (Khuyên dùng)</span>
                                </div>
                            </div>
                        </label>

                        <label class="payment-card">
                            <input type="radio" name="payment_method" value="Thẻ" {{ old('payment_method') === 'Thẻ' ? 'checked' : '' }} onchange="togglePaymentDesc('card')">
                            <div class="payment-card-content">
                                <div class="payment-icon"><i data-lucide="credit-card"></i></div>
                                <div class="payment-text">
                                    <strong>Thẻ ATM nội địa / Quốc tế / Visa / Master</strong>
                                    <span>Thanh toán online qua cổng Napas cực nhanh</span>
                                </div>
                            </div>
                        </label>

                        <label class="payment-card">
                            <input type="radio" name="payment_method" value="Tại trung tâm" {{ old('payment_method') === 'Tại trung tâm' ? 'checked' : '' }} onchange="togglePaymentDesc('later')">
                            <div class="payment-card-content">
                                <div class="payment-icon"><i data-lucide="landmark"></i></div>
                                <div class="payment-text">
                                    <strong>Thanh toán trực tiếp tại trung tâm VNVC</strong>
                                    <span>Thanh toán bằng tiền mặt/quẹt thẻ khi đến tiêm chủng</span>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="payment-info-box" id="paymentDescBox">
                        <i data-lucide="info"></i>
                        <span id="paymentDescText">Hệ thống sẽ tạo mã QR thanh toán động sau khi bạn hoàn tất đăng ký. Quét mã để thanh toán và giữ chỗ vắc xin ngay lập tức.</span>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="prevStep(3)">
                            <i data-lucide="arrow-left"></i> <span>Quay lại</span>
                        </button>
                        <button type="submit" class="btn-submit-registration">
                            <i data-lucide="shield-check"></i> <span>Hoàn tất Đăng ký</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar Summary -->
        <div class="summary-card">
            <h3>Tóm tắt đăng ký tiêm</h3>
            <div class="summary-items">
                @foreach($cart as $id => $item)
                    <div class="summary-item">
                        <div class="item-name">
                            <strong>{{ $item['name'] }}</strong>
                            <span>{{ $item['origin'] }}</span>
                        </div>
                        <span class="item-price">{{ number_format($item['price'], 0, ',', '.') }} đ</span>
                    </div>
                @endforeach
            </div>
            
            <div class="summary-divider"></div>
            
            <div class="summary-total">
                <span>Tổng chi phí vắc xin:</span>
                <strong>{{ number_format($totalPrice, 0, ',', '.') }} đ</strong>
            </div>

            <div class="summary-note">
                <i data-lucide="sparkles"></i>
                <p>Giá vắc xin đã bao gồm: phí khám lâm sàng trước tiêm, phí dịch vụ tiêm chủng và các tiện ích đi kèm tại VNVC.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Logic tự động hiển thị giám hộ nếu người tiêm dưới 15 tuổi
    document.getElementById('patient_dob').addEventListener('change', function(e) {
        const dob = new Date(e.target.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        
        const guardianSec = document.getElementById('guardianSection');
        const gName = document.getElementById('guardian_name');
        const gPhone = document.getElementById('guardian_phone');
        
        if (age < 15) {
            guardianSec.style.display = 'block';
            gName.setAttribute('required', 'required');
            gPhone.setAttribute('required', 'required');
        } else {
            guardianSec.style.display = 'none';
            gName.removeAttribute('required');
            gPhone.removeAttribute('required');
        }
    });

    // Chuyển đổi mô tả phương thức thanh toán
    function togglePaymentDesc(method) {
        const textEl = document.getElementById('paymentDescText');
        if (method === 'qr') {
            textEl.textContent = 'Hệ thống sẽ tạo mã QR thanh toán động sau khi bạn hoàn tất đăng ký. Quét mã để thanh toán và giữ chỗ vắc xin ngay lập tức.';
        } else if (method === 'card') {
            textEl.textContent = 'Bạn sẽ được chuyển sang cổng thanh toán trực tuyến Napas để thanh toán bằng thẻ ATM, Thẻ quốc tế Visa/MasterCard.';
        } else {
            textEl.textContent = 'Mã đăng ký tiêm chủng sẽ được cấp trực tiếp. Bạn sẽ tiến hành thanh toán tiền vắc xin khi đến quầy check-in của trung tâm tiêm chủng.';
        }
    }

    // Logic Multi-step form
    let currentStep = 1;

    function nextStep(step) {
        if (!validateStep(step)) return;

        currentStep = step + 1;
        updateStepperUI();
    }

    function prevStep(step) {
        currentStep = step - 1;
        updateStepperUI();
    }

    function updateStepperUI() {
        // Ẩn/Hiện nội dung các bước
        document.querySelectorAll('.form-step-content').forEach(el => el.classList.remove('active'));
        document.getElementById('stepContent' + currentStep).classList.add('active');

        // Cập nhật Stepper Indicator
        for (let i = 1; i <= 3; i++) {
            const ind = document.getElementById('stepIndicator' + i);
            const line = document.getElementById('stepLine' + i);
            
            if (i < currentStep) {
                ind.classList.add('completed');
                ind.classList.remove('active');
            } else if (i === currentStep) {
                ind.classList.add('active');
                ind.classList.remove('completed');
            } else {
                ind.classList.remove('active', 'completed');
            }

            if (line) {
                if (i < currentStep) {
                    line.classList.add('active');
                } else {
                    line.classList.remove('active');
                }
            }
        }
    }

    function validateStep(step) {
        const content = document.getElementById('stepContent' + step);
        const inputs = content.querySelectorAll('input[required], select[required]');
        let isValid = true;

        inputs.forEach(input => {
            // Kiểm tra tính hợp lệ của input HTML5
            if (!input.checkValidity()) {
                input.reportValidity();
                isValid = false;
            }
        });

        return isValid;
    }
</script>
@endsection
