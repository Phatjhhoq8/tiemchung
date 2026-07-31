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
                    <h2>Thông tin người tiêm & Chọn vắc xin</h2>
                    <p class="step-desc">Medicare hỗ trợ đăng ký tiêm chủng cho nhiều người thân trong gia đình cùng lúc.</p>

                    <!-- Patients Container -->
                    <div id="patientsContainer">
                        <!-- Patient blocks will be inserted here dynamically -->
                    </div>

                    <button type="button" class="btn-secondary" onclick="addPatientField()" style="margin-top: 10px; display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; border-radius: 8px; font-weight: 700; background: #f8fafc; border: 1px dashed #cbd5e1; cursor: pointer; color: #475569; transition: all 0.2s;">
                        <i data-lucide="user-plus" style="width: 16px; height: 16px;"></i> Thêm người tiêm khác
                    </button>

                    <!-- Người giám hộ (nếu người tiêm < 15 tuổi) -->
                    <div id="guardianSection" style="display: none; margin-top: 24px; padding-top: 24px; border-top: 1px dashed var(--border-color);">
                        <h3 style="margin-bottom: 12px; font-size: 16px; color: var(--primary-color);">Thông tin người giám hộ</h3>
                        <p class="step-desc">Hệ thống phát hiện có người tiêm dưới 15 tuổi, vui lòng khai báo thông tin cha/mẹ hoặc người giám hộ hợp pháp.</p>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="guardian_name">Họ tên người giám hộ <span class="required">*</span></label>
                                <input type="text" name="guardian_name" id="guardian_name" value="{{ old('guardian_name') }}" placeholder="Ví dụ: Nguyễn Văn B">
                            </div>

                            <div class="form-group">
                                <label for="guardian_phone">Số điện thoại người giám hộ <span class="required">*</span></label>
                                <input type="text" name="guardian_phone" id="guardian_phone" value="{{ old('guardian_phone') }}" placeholder="Ví dụ: 0932477184">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 24px;">
                        <div></div>
                        <button type="button" class="btn-primary" onclick="nextStep(1)">
                            <span>Tiếp tục</span> <i data-lucide="arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 2: Địa điểm & Ngày tiêm -->
                <div class="form-step-content" id="stepContent2">
                    <h2>Chọn chi nhánh và thời gian tiêm</h2>
                    <p class="step-desc">Vui lòng chọn chi nhánh trung tâm tiêm chủng Medicare thuận tiện nhất cho bạn và gia đình.</p>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="center_id">Chi nhánh trung tâm tiêm chủng Medicare <span class="required">*</span></label>
                            <select name="center_id" id="center_id" required style="padding: 12px; font-size: 15px; border-radius: 8px;">
                                <option value="">-- Vui lòng chọn chi nhánh Medicare --</option>
                                @foreach($centers as $center)
                                    <option value="{{ $center->id }}" {{ (string) old('center_id', $currentCenter?->id) === (string) $center->id ? 'selected' : '' }}>
                                        📍 {{ $center->name }} — Địa chỉ: {{ $center->address }} (Hotline: {{ $center->phone }})
                                    </option>
                                @endforeach
                            </select>
                            <small style="display: block; margin-top: 8px; color: #64748b;">Đổi chi nhánh tại đây sẽ cập nhật giá theo chi nhánh đã chọn.</small>
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
                                    <strong>Thanh toán trực tiếp tại trung tâm Medicare</strong>
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
                        <button type="submit" class="btn-submit-registration" style="background: var(--primary-color, #c8102e);">
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
                            <span>{{ $item['disease_prevention'] ?? 'Vắc xin phòng bệnh' }}</span>
                            @if(!empty($item['unavailable_for_center']))
                                <span style="color: #b91c1c; font-weight: 800; margin-top: 4px;">Sản phẩm này không có ở chi nhánh hiện tại</span>
                            @endif
                        </div>
                        <span class="item-price">{{ number_format($item['price'], 0, ',', '.') }} đ</span>
                    </div>
                @endforeach
            </div>
            @if(($unavailableCount ?? 0) > 0)
                <div style="margin-top: 14px; padding: 12px; border-radius: 10px; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; font-size: 13px; font-weight: 700; line-height: 1.5;">
                    Có {{ $unavailableCount }} sản phẩm không có ở chi nhánh hiện tại. Vui lòng xóa sản phẩm đó hoặc đổi chi nhánh trước khi đăng ký.
                </div>
            @endif
            
            <div class="summary-divider"></div>
            
            <div class="summary-total">
                <span>Tổng chi phí vắc xin:</span>
                <strong>{{ number_format($totalPrice, 0, ',', '.') }} đ</strong>
            </div>

            <div class="summary-note">
                <i data-lucide="sparkles"></i>
                <p>Giá vắc xin đang được tính theo chi nhánh {{ $currentCenter?->name ?? 'Medicare' }}.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const cartData = {!! json_encode($cart) !!};
    const unavailableCount = {{ (int) ($unavailableCount ?? 0) }};
    const todayStr = "{{ date('Y-m-d') }}";
    const baseAssetUrl = "{{ asset('images/vaccines') }}";
    let patientCount = 0;

    function generateVaccineChecklistHtml(index) {
        let html = '';
        Object.entries(cartData).forEach(([vId, item]) => {
            const formattedPrice = new Intl.NumberFormat('vi-VN').format(item.price) + ' đ';
            const imageUrl = `${baseAssetUrl}/${item.image || 'hexaxim.jpg'}`;
            const desc = item.disease_prevention || 'Phòng ngừa các bệnh truyền nhiễm nguy hiểm';
            const unavailable = Boolean(item.unavailable_for_center);
            html += `
                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: ${unavailable ? 'not-allowed' : 'pointer'}; padding: 12px; border: 1px solid ${unavailable ? '#fecaca' : '#e2e8f0'}; border-radius: 8px; background: ${unavailable ? '#fef2f2' : '#ffffff'}; transition: border-color 0.2s; margin-bottom: 8px; width: 100%;">
                    <input type="checkbox" name="patients[${index}][vaccine_ids][]" value="${vId}" data-price="${item.price}" ${unavailable ? 'disabled' : 'checked'} class="patient-vaccine-checkbox" onchange="recalculateRegisterPrices()" style="margin-top: 4px; width: 16px; height: 16px;">
                    <img src="${imageUrl}" alt="${item.name}" style="width: 50px; height: 50px; border-radius: 6px; object-fit: cover; border: 1px solid #f1f5f9; flex-shrink: 0;">
                    <div style="flex-grow: 1; display: flex; flex-direction: column; gap: 2px;">
                        <span style="font-size: 13.5px; font-weight: 700; color: #1e293b;">${item.name}</span>
                        <span style="font-size: 11.5px; color: #64748b; line-height: 1.4;"><strong>Phòng bệnh:</strong> ${desc}</span>
                        <span style="font-size: 13px; font-weight: 800; color: var(--primary-color, #c8102e); margin-top: 2px;">${formattedPrice}</span>
                        ${unavailable ? '<span style="font-size: 12px; color: #b91c1c; font-weight: 800;">Sản phẩm này không có ở chi nhánh hiện tại</span>' : ''}
                    </div>
                </label>
            `;
        });
        return html;
    }

    function addPatientField() {
        const container = document.getElementById('patientsContainer');
        if (!container) return;

        const index = patientCount;
        const blockHtml = `
            <div class="patient-form-block" id="patientBlock_${index}" style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; background: #ffffff; position: relative; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                    <h3 style="font-size: 15px; font-weight: 800; color: var(--accent-color, #004b8f); margin: 0; font-family: var(--font-display, inherit);">Người tiêm #${index + 1}</h3>
                    ${index > 0 ? `
                        <button type="button" class="btn-remove-patient" onclick="removePatientField(${index})" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 4px;">
                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Xóa người này
                        </button>
                    ` : ''}
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Họ tên người tiêm <span class="required">*</span></label>
                        <input type="text" name="patients[${index}][name]" placeholder="Ví dụ: Nguyễn Văn A" required style="padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 100%;">
                    </div>
                    
                    <div class="form-group">
                        <label>Ngày sinh người tiêm <span class="required">*</span></label>
                        <input type="date" name="patients[${index}][dob]" required max="${todayStr}" onchange="checkPatientAge()" class="patient-dob-input" style="padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 100%;">
                    </div>
                    
                    <div class="form-group">
                        <label>Giới tính <span class="required">*</span></label>
                        <select name="patients[${index}][gender]" required style="padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 100%;">
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Số điện thoại liên hệ <span class="required">*</span></label>
                        <input type="text" name="patients[${index}][phone]" placeholder="Ví dụ: 0938603839" required style="padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 100%;">
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Địa chỉ thường trú <span class="required">*</span></label>
                        <input type="text" name="patients[${index}][address]" placeholder="Số nhà, đường, phường/xã, quận/huyện..." required style="padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 100%;">
                    </div>
                </div>

                <div style="margin-top: 16px; padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <h4 style="font-size: 13.5px; font-weight: 700; color: #334155; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                        <i data-lucide="syringe" style="width: 15px; height: 15px; color: var(--primary-color, #c8102e);"></i>
                        Chọn vắc xin cho người này:
                    </h4>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        ${generateVaccineChecklistHtml(index)}
                    </div>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', blockHtml);
        patientCount++;
        
        if (window.lucide) {
            lucide.createIcons();
        }

        recalculateRegisterPrices();
    }

    function removePatientField(index) {
        const block = document.getElementById(`patientBlock_${index}`);
        if (block) {
            block.remove();
            recalculateRegisterPrices();
            checkPatientAge();
        }
    }

    function checkPatientAge() {
        let hasMinor = false;
        document.querySelectorAll('.patient-dob-input').forEach(input => {
            if (input.value) {
                const dob = new Date(input.value);
                const today = new Date();
                let age = today.getFullYear() - dob.getFullYear();
                const m = today.getMonth() - dob.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }
                if (age < 15) {
                    hasMinor = true;
                }
            }
        });

        const guardianSec = document.getElementById('guardianSection');
        const gName = document.getElementById('guardian_name');
        const gPhone = document.getElementById('guardian_phone');
        
        if (guardianSec && gName && gPhone) {
            if (hasMinor) {
                guardianSec.style.display = 'block';
                gName.setAttribute('required', 'required');
                gPhone.setAttribute('required', 'required');
            } else {
                guardianSec.style.display = 'none';
                gName.removeAttribute('required');
                gPhone.removeAttribute('required');
            }
        }
    }

    function recalculateRegisterPrices() {
        let total = 0;
        document.querySelectorAll('.patient-vaccine-checkbox:checked').forEach(cb => {
            total += parseFloat(cb.getAttribute('data-price') || 0);
        });

        const grandTotalEl = document.querySelector('.summary-total strong');
        if (grandTotalEl) {
            grandTotalEl.textContent = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
        }
    }

    // Initialize with first patient
    document.addEventListener('DOMContentLoaded', () => {
        addPatientField();
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
        if (step === 2 && unavailableCount > 0) {
            alert('Có sản phẩm không có ở chi nhánh hiện tại. Vui lòng xóa sản phẩm đó hoặc đổi chi nhánh trước khi đăng ký.');
            return;
        }
        if (!validateStep(step)) return;

        currentStep = step + 1;
        updateStepperUI();
    }

    function prevStep(step) {
        currentStep = step - 1;
        updateStepperUI();
    }



    function updateStepperUI() {
        document.querySelectorAll('.form-step-content').forEach(el => el.classList.remove('active'));
        document.getElementById('stepContent' + currentStep).classList.add('active');

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
            if (!input.checkValidity()) {
                input.reportValidity();
                isValid = false;
            }
        });

        if (isValid && step === 1) {
            // Check that each patient block has at least one vaccine selected
            const patientBlocks = document.querySelectorAll('.patient-form-block');
            if (patientBlocks.length === 0) {
                alert('Vui lòng thêm ít nhất một người tiêm.');
                isValid = false;
            }
            
            for (let i = 0; i < patientBlocks.length; i++) {
                const block = patientBlocks[i];
                const checked = block.querySelectorAll('.patient-vaccine-checkbox:checked');
                if (checked.length === 0) {
                    alert(`Vui lòng chọn ít nhất một loại vắc xin cho Người tiêm #${i + 1}.`);
                    isValid = false;
                    break;
                }
            }
        }

        return isValid;
    }
</script>
@endsection
