@extends('vaccine::layouts.app')

@section('title', 'Đặt Lịch Tiêm Chủng')

@section('content')
<div class="registration-container">
    @if($isEmptyCart)
        <!-- Empty Cart Panel: Interactive Stepper identical to the old SPA Modal -->
        <div class="max-w-xl mx-auto py-10 px-4" id="empty-cart-flow-container">
            <!-- Step 1: Warning (Rendered by default) -->
        </div>

        <script>
            // Render Step 1: Empty warning
            function renderEmptyWarning() {
                const container = document.getElementById('empty-cart-flow-container');
                if (!container) return;

                container.innerHTML = `
                    <div style="text-align: center; padding: 40px; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);" data-aos="fade-up">
                        <i data-lucide="help-circle" style="width: 56px; height: 56px; color: var(--secondary-color, #eaaa00); margin: 0 auto 16px auto;"></i>
                        <h3 style="font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 10px; text-align: center; display: block;">Bạn chưa chọn vắc xin nào</h3>
                        <p style="color: #64748b; margin-bottom: 24px; max-width: 400px; margin-left: auto; margin-right: auto; line-height: 1.5; font-size: 14.5px; text-align: center;">Bạn có thể chọn vắc xin từ danh mục hoặc gửi yêu cầu để bác sĩ Medicare tư vấn phác đồ phù hợp.</p>
                        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                            <a href="{{ route('vaccine.index') }}" class="btn-primary" style="background: var(--secondary-color, #eaaa00); color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; min-width: 160px;">Chọn vắc xin</a>
                            <button type="button" onclick="renderConsultationForm()" class="btn-primary" style="background: var(--primary-color, #c8102e); color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; min-width: 160px;">Yêu cầu tư vấn</button>
                        </div>
                    </div>
                `;
                if (window.lucide) window.lucide.createIcons();
            }

            // Render Step 2: Consultation Form
            function renderConsultationForm() {
                const container = document.getElementById('empty-cart-flow-container');
                if (!container) return;

                // Build center select options dynamically from PHP
                let centerOptions = '<option value="">-- Chọn trung tâm Medicare --</option>';
                @foreach($activeCenters as $center)
                    {
                        const isSelected = {{ $center->id == $currentCenter->id ? 'true' : 'false' }};
                        centerOptions += `<option value="{{ $center->id }}" ${isSelected ? 'selected' : ''}>{{ $center->name }}</option>`;
                    }
                @endforeach

                container.innerHTML = `
                    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03); padding: 32px; max-width: 500px; margin: 0 auto;" data-aos="fade-up">
                        <h3 style="font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 8px; text-align: justify;">Yêu cầu tư vấn <span style="color: var(--primary-color, #c8102e);">miễn phí</span></h3>
                        <p style="font-size: 14px; color: #64748b; text-align: justify; margin-bottom: 24px; line-height: 1.5;">Medicare sẽ liên hệ lại ngay để tư vấn phác đồ tiêm chủng vắc xin thích hợp nhất cho bạn.</p>
                        
                        <form id="publicConsultationForm" onsubmit="submitPublicConsultation(event)" style="display: flex; flex-direction: column; gap: 16px;">
                            @csrf
                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                <label style="font-size: 13.5px; font-weight: 600; color: #334155; text-align: justify;">Hình thức tư vấn <span style="color:#ef4444;">*</span></label>
                                <div style="display: inline-flex; width: 100%; background: #f1f5f9; border-radius: 30px; padding: 4px; border: 1px solid #cbd5e1; box-sizing: border-box;">
                                    <button type="button" id="btnConsultOnline" onclick="setConsultType('online')" style="flex: 1; border: none; padding: 10px 12px; border-radius: 26px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s; background: var(--primary-color, #c8102e); color: #ffffff; text-align: center;">
                                        Tư vấn qua điện thoại (trực tuyến)
                                    </button>
                                    <button type="button" id="btnConsultOffline" onclick="setConsultType('offline')" style="flex: 1; border: none; padding: 10px 12px; border-radius: 26px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s; background: transparent; color: #475569; text-align: center;">
                                        Tư vấn tại trung tâm
                                    </button>
                                    <input type="hidden" name="consultType" id="consultTypeValue" value="online">
                                </div>
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                <label for="consult_name" style="font-size: 13.5px; font-weight: 600; color: #334155; text-align: justify;">Họ tên người liên hệ <span class="text-red-500">*</span></label>
                                <input type="text" id="consult_name" name="customerName" placeholder="Nhập họ tên của bạn" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; font-size: 14px;">
                                <span class="error-msg text-xs text-red-500 mt-1 hidden" id="err_customerName" style="text-align: justify;"></span>
                            </div>
                            
                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                <label for="consult_phone" style="font-size: 13.5px; font-weight: 600; color: #334155; text-align: justify;">Số điện thoại liên hệ <span class="text-red-500">*</span></label>
                                <input type="tel" id="consult_phone" name="customerPhone" placeholder="Ví dụ: 0912345678" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; font-size: 14px;">
                                <span class="error-msg text-xs text-red-500 mt-1 hidden" id="err_customerPhone" style="text-align: justify;"></span>
                            </div>
                            
                            <div id="centerSelectGroup" style="display: none; flex-direction: column; gap: 6px;">
                                <label for="consult_center" style="font-size: 13.5px; font-weight: 600; color: #334155; text-align: justify;">Chi nhánh tư vấn gần nhất <span class="text-red-500">*</span></label>
                                <div style="position: relative; width: 100%;">
                                    <select id="consult_center" name="center_id" style="width: 100%; padding: 10px 36px 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; background: #fff; height: 42px; font-size: 14px; -webkit-appearance: none; -moz-appearance: none; appearance: none; cursor: pointer;">
                                        ${centerOptions}
                                    </select>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </div>
                                <span class="error-msg text-xs text-red-500 mt-1 hidden" id="err_center_id" style="text-align: justify;"></span>
                            </div>
                            
                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                <label for="consult_note" style="font-size: 13.5px; font-weight: 600; color: #334155; text-align: justify;">Ghi chú yêu cầu tư vấn</label>
                                <textarea id="consult_note" name="customerNote" rows="3" placeholder="Nhập vắc xin bạn muốn tiêm hoặc câu hỏi cần tư vấn..." style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; font-family: inherit; font-size: 14px;"></textarea>
                            </div>
                            
                            <div style="display: flex; gap: 12px; margin-top: 8px;">
                                <button type="button" onclick="renderEmptyWarning()" style="flex: 1; padding: 12px; border-radius: 8px; font-weight: 700; cursor: pointer; background: #f1f5f9; border: 1px solid #cbd5e1; color: #475569; font-size: 14px;">Quay lại</button>
                                <button type="submit" id="btnSubmitConsultation" style="flex: 2; background: var(--primary-color, #c8102e); color: #fff; border: none; padding: 12px; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px;">
                                    <i data-lucide="send" style="width: 16px; height: 16px;"></i> Gửi yêu cầu
                                </button>
                            </div>
                        </form>
                    </div>
                `;
                if (window.lucide) window.lucide.createIcons();
            }

            // Handle online/offline toggle switch
            function setConsultType(type) {
                const btnOnline = document.getElementById('btnConsultOnline');
                const btnOffline = document.getElementById('btnConsultOffline');
                const valInput = document.getElementById('consultTypeValue');
                const centerGroup = document.getElementById('centerSelectGroup');
                const centerSelect = document.getElementById('consult_center');
                if (!btnOnline || !btnOffline || !valInput || !centerGroup) return;

                if (type === 'online') {
                    valInput.value = 'online';
                    btnOnline.style.background = 'var(--primary-color, #c8102e)';
                    btnOnline.style.color = '#ffffff';
                    btnOffline.style.background = 'transparent';
                    btnOffline.style.color = '#475569';
                    centerGroup.style.display = 'none';
                    if (centerSelect) centerSelect.required = false;
                } else {
                    valInput.value = 'offline';
                    btnOffline.style.background = 'var(--primary-color, #c8102e)';
                    btnOffline.style.color = '#ffffff';
                    btnOnline.style.background = 'transparent';
                    btnOnline.style.color = '#475569';
                    centerGroup.style.display = 'flex';
                    if (centerSelect) centerSelect.required = true;
                }
            }

            // Submit AJAX consultation form
            async function submitPublicConsultation(event) {
                event.preventDefault();
                const form = document.getElementById('publicConsultationForm');
                const submitBtn = document.getElementById('btnSubmitConsultation');
                if (!form || !submitBtn) return;

                // Reset error highlights
                document.querySelectorAll('.error-msg').forEach(el => {
                    el.textContent = '';
                    el.classList.add('hidden');
                });

                if (!await window.AppDialog.confirm('Bạn có thực sự muốn gửi yêu cầu tư vấn tiêm chủng này không?')) {
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="w-4 h-4 animate-spin rounded-full border-2 border-white border-t-transparent"></i> Đang gửi...';

                try {
                    const formData = new FormData(form);
                    const payload = {
                        customerName: formData.get('customerName'),
                        customerPhone: formData.get('customerPhone'),
                        customerNote: formData.get('customerNote'),
                        center_id: formData.get('center_id') || null,
                        source: 'Website Empty Cart Form (' + (formData.get('consultType') === 'online' ? 'Online' : 'Offline') + ')'
                    };

                    const response = await fetch("{{ route('leads.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                const errEl = document.getElementById(`err_${key}`);
                                if (errEl) {
                                    errEl.textContent = data.errors[key][0];
                                    errEl.classList.remove('hidden');
                                }
                            });
                        }
                        throw new Error(data.message || 'Gửi yêu cầu thất bại.');
                    }

                    showToast(data.message || 'Yêu cầu tư vấn đã được gửi thành công!', 'success');
                    form.reset();
                    renderEmptyWarning();
                } catch (err) {
                    console.error(err);
                    showToast(err.message || 'Có lỗi xảy ra khi gửi yêu cầu.', 'error');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i data-lucide="send" style="width:16px;height:16px;"></i> Gửi yêu cầu';
                    if (window.lucide) window.lucide.createIcons();
                }
            }

            // Initialize warning view on load
            document.addEventListener('DOMContentLoaded', () => {
                renderEmptyWarning();
            });
        </script>
    @else
        <div class="registration-layout">
            <div class="form-card">
                <h1 style="margin-top: 0;">Đặt lịch tiêm chủng</h1>
                <p class="step-desc">Giá và khung giờ được áp dụng theo chi nhánh bạn đang chọn. Mỗi phiếu dành cho một người tiêm.</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('centers.select') }}" method="POST" style="margin: 24px 0; padding: 16px; border: 1px solid var(--border-color); border-radius: 10px; background: #f8fafc;">
                    @csrf
                    <label for="booking_center_id" style="display: block; margin-bottom: 8px; font-weight: 700;">Chi nhánh đặt lịch</label>
                    <select id="booking_center_id" name="center_id" onchange="this.form.submit()" style="width: 100%; padding: 12px; border-radius: 8px;">
                        @foreach($activeCenters as $center)
                            <option value="{{ $center->id }}" {{ $currentCenter?->id === $center->id ? 'selected' : '' }}>{{ $center->name }} - {{ $center->address }}</option>
                        @endforeach
                    </select>
                    <noscript><button type="submit" class="btn-secondary" style="margin-top: 12px;">Áp dụng chi nhánh</button></noscript>
                </form>

                @if($unavailableCount > 0)
                    <div class="alert alert-danger">
                        Một hoặc nhiều sản phẩm trong danh sách không được bán tại {{ $currentCenter->name }}. Vui lòng quay lại danh mục để điều chỉnh.
                    </div>
                @endif

                <form action="{{ route('register.post') }}" method="POST" id="publicRegisterForm" onsubmit="handlePublicRegisterSubmit(event)">
                    @csrf
                    <input type="hidden" name="idempotency_key" value="{{ old('idempotency_key', (string) \Illuminate\Support\Str::uuid()) }}">

                    <div style="padding:16px; margin-bottom:20px; border:1px solid #bfdbfe; border-radius:10px; background:#eff6ff;">
                        <h3 style="margin:0 0 6px; font-size:15px;">Tài khoản tích điểm toàn hệ thống</h3>
                        <p style="margin:0 0 14px; color:#475569; font-size:13px;">Một số điện thoại dùng chung cho toàn bộ người tiêm trong đơn và tại mọi chi nhánh.</p>
                        <div class="spa-form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                            <div class="form-group" style="margin:0;">
                                <label for="account_name">Họ tên chủ tài khoản <span class="required">*</span></label>
                                <input id="account_name" name="account_name" value="{{ old('account_name') }}" required autocomplete="name" style="width:100%; box-sizing:border-box; height:42px; padding:10px 12px; border:1px solid #cbd5e1; border-radius:8px;">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label for="account_phone">Số điện thoại tài khoản <span class="required">*</span></label>
                                <input id="account_phone" type="tel" name="account_phone" value="{{ old('account_phone') }}" required autocomplete="tel" placeholder="0912345678" style="width:100%; box-sizing:border-box; height:42px; padding:10px 12px; border:1px solid #cbd5e1; border-radius:8px;">
                            </div>
                        </div>
                    </div>

                    <!-- Patients List Container -->
                    <div id="patientsContainer" style="display: flex; flex-direction: column; gap: 20px;"></div>

                    <!-- Add Patient Action Button -->
                    <div style="display: flex; justify-content: flex-start; margin-top: 10px; margin-bottom: 24px;">
                        <button type="button" onclick="addPatientField()" style="background: none; border: 1.5px dashed var(--primary-color, #c8102e); color: var(--primary-color, #c8102e); padding: 10px 16px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 13.5px; display: flex; align-items: center; gap: 6px; transition: all 0.2s;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            Thêm người tiêm khác
                        </button>
                    </div>

                    <!-- Guardian Section (trẻ em dưới 15 tuổi) -->
                    <div id="guardianSection" style="display: none; flex-direction: column; gap: 14px; padding: 16px; background: #fff8f8; border: 1px solid #fecaca; border-radius: 10px; margin-bottom: 24px;">
                        <h4 style="margin: 0; font-size: 13.5px; font-weight: 700; color: #991b1b; display: flex; align-items: center; gap: 6px; text-align: left;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            Thông tin người giám hộ (Bắt buộc khi có người tiêm dưới 15 tuổi)
                        </h4>
                        <div class="spa-form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                <label for="guardian_name" style="font-size: 13px; font-weight: 600; color: #334155; text-align: left;">Họ tên người giám hộ <span class="text-red-500">*</span></label>
                                <input type="text" id="guardian_name" name="guardian_name" placeholder="Ví dụ: Nguyễn Văn A" style="padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px; height: 42px; box-sizing: border-box;">
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                <label for="guardian_phone" style="font-size: 13px; font-weight: 600; color: #334155; text-align: left;">Số điện thoại người giám hộ <span class="text-red-500">*</span></label>
                                <input type="tel" id="guardian_phone" name="guardian_phone" placeholder="Ví dụ: 0912345678" style="padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px; height: 42px; box-sizing: border-box;">
                            </div>
                        </div>
                    </div>

                    <div class="spa-form-row" style="margin-top: 22px; display: grid; grid-template-columns: 1fr 1fr; gap: 14px; box-sizing: border-box;">
                        <!-- Chọn Ngày -->
                        <div class="form-group" style="margin: 0; display: flex; flex-direction: column; gap: 6px;">
                            <label for="date_select">Ngày tiêm <span class="required">*</span></label>
                            <div style="position: relative; width: 100%;">
                                <select id="date_select" onchange="changePublicDateFilter(this.value)" required style="width: 100%; padding: 10px 36px 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px; background: #fff; -webkit-appearance: none; -moz-appearance: none; appearance: none; cursor: pointer; height: 42px;">
                                    <option value="">-- Chọn ngày tiêm --</option>
                                    @foreach($schedules as $schedule)
                                        <option value="{{ $schedule->date->format('Y-m-d') }}">{{ $schedule->date->format('d/m/Y') }}</option>
                                    @endforeach
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </div>

                        <!-- Chọn Khung Giờ -->
                        <div class="form-group" style="margin: 0; display: flex; flex-direction: column; gap: 6px;">
                            <label for="slot_id">Khung giờ tiêm <span class="required">*</span></label>
                            <div style="position: relative; width: 100%;">
                                <select id="slot_id" name="slot_id" required disabled style="width: 100%; padding: 10px 36px 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px; background: #f8fafc; -webkit-appearance: none; -moz-appearance: none; appearance: none; cursor: not-allowed; height: 42px;">
                                    <option value="">-- Vui lòng chọn ngày --</option>
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </div>
                    </div>
                    @if($schedules->isEmpty())
                        <small style="display: block; margin-top: 8px; color: #b91c1c;">Chi nhánh hiện chưa mở khung giờ. Vui lòng chọn chi nhánh khác hoặc liên hệ nhân viên.</small>
                    @endif

                    <div class="form-actions" style="margin-top: 28px;">
                        <a href="{{ route('vaccine.index') }}" class="btn-secondary">Quay lại danh mục</a>
                        <button type="submit" class="btn-submit-registration" {{ $unavailableCount > 0 || $schedules->isEmpty() ? 'disabled' : '' }}>
                            Hoàn tất đặt lịch
                        </button>
                    </div>
                </form>
            </div>

            <aside class="summary-card">
                <h3>Tóm tắt tại {{ $currentCenter->name }}</h3>
                <div class="summary-items">
                    @foreach($cart as $item)
                        <div class="summary-item">
                            <div class="item-name">
                                <strong>{{ $item['name'] }}</strong>
                                <span>{{ $item['disease_prevention'] }}</span>
                            </div>
                            <span class="item-price">{{ number_format($item['price'], 0, ',', '.') }} đ</span>
                        </div>
                    @endforeach
                </div>
                <div class="summary-divider"></div>
                <div class="summary-total">
                    <span>Tổng dự kiến:</span>
                    <strong>{{ number_format($totalPrice, 0, ',', '.') }} đ</strong>
                </div>
                <div class="summary-note">
                    <p>Thanh toán và sử dụng điểm được nhân viên xác nhận tại quầy. Giá cuối cùng được hệ thống chốt khi tạo phiếu.</p>
                </div>
            </aside>
        </div>
    @endif
</div>

<script>
    const publicSchedules = @json($schedules);
    let patientCount = 0;
    const todayStr = new Date().toISOString().split('T')[0];

    function changePublicDateFilter(selectedDate) {
        const slotSelect = document.getElementById('slot_id');
        if (!slotSelect) return;

        if (!selectedDate) {
            slotSelect.innerHTML = '<option value="">-- Vui lòng chọn ngày --</option>';
            slotSelect.disabled = true;
            slotSelect.style.background = '#f8fafc';
            slotSelect.style.cursor = 'not-allowed';
            return;
        }

        const daySchedule = publicSchedules.find(s => {
            const d = new Date(s.date);
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}` === selectedDate;
        });
        
        let options = '<option value="">-- Chọn khung giờ tiêm --</option>';
        if (daySchedule && daySchedule.slots) {
            daySchedule.slots.forEach(slot => {
                const remaining = Math.max(0, slot.capacity - slot.reserved_count);
                options += `<option value="${slot.id}">${slot.start_at} - ${slot.end_at} (còn ${remaining} chỗ)</option>`;
            });
        }

        slotSelect.innerHTML = options;
        slotSelect.disabled = false;
        slotSelect.style.background = '#fff';
        slotSelect.style.cursor = 'pointer';
    }

    function generateVaccineChecklistHtml(index) {
        let html = '';
        @foreach($cart as $id => $item)
            @if(!$item['unavailable_for_center'])
                html += `
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13.5px; padding: 6px 10px; border: 1px solid #e2e8f0; border-radius: 6px; background: #fff;">
                        <input type="checkbox" name="patients[${index}][vaccine_ids][]" value="{{ $id }}" data-price="{{ $item['price'] }}" class="patient-vaccine-checkbox" checked onchange="recalculateRegisterPrices()" style="width: 16px; height: 16px; accent-color: var(--primary-color);">
                        <span style="flex: 1; text-align: left;"><strong>{{ $item['name'] }}</strong></span>
                        <strong style="color: var(--primary-color);">{{ number_format($item['price'], 0, ',', '.') }} đ</strong>
                    </label>
                `;
            @endif
        @endforeach
        return html;
    }

    function addPatientField() {
        const container = document.getElementById('patientsContainer');
        if (!container) return;

        const index = patientCount;
        const blockHtml = `
            <div class="patient-form-block" id="patientBlock_${index}" style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; background: #ffffff; position: relative; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 14px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 4px;">
                    <h3 style="font-size: 14.5px; font-weight: 800; color: var(--accent-color, #004b8f); margin: 0;">Người tiêm #${index + 1}</h3>
                    ${index > 0 ? `
                        <button type="button" class="btn-remove-patient" onclick="removePatientField(${index})" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 4px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            Xóa người này
                        </button>
                    ` : ''}
                </div>
                
                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                        <label>Họ tên người tiêm <span class="required">*</span></label>
                        <input type="text" name="patients[${index}][name]" placeholder="Ví dụ: Nguyễn Văn A" required style="padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 100%; box-sizing: border-box; height: 42px;">
                    </div>
                    
                    <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                        <label>Số điện thoại người tiêm / liên hệ <span class="required">*</span></label>
                        <input type="tel" name="patients[${index}][phone]" placeholder="Ví dụ: 0912345678" required style="padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 100%; box-sizing: border-box; height: 42px;">
                    </div>
                </div>

                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                        <label>Ngày sinh người tiêm <span class="required">*</span></label>
                        <input type="date" name="patients[${index}][dob]" required max="${todayStr}" onchange="checkPatientAge()" class="patient-dob-input" style="padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 100%; box-sizing: border-box; height: 42px;">
                    </div>
                    
                    <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                        <label>Giới tính <span class="required">*</span></label>
                        <select name="patients[${index}][gender]" required style="padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 100%; background: #fff; cursor: pointer; height: 42px; box-sizing: border-box;">
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                    <label>Địa chỉ thường trú <span class="required">*</span></label>
                    <input type="text" name="patients[${index}][address]" placeholder="Số nhà, đường, phường/xã, quận/huyện..." required style="padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 100%; box-sizing: border-box; height: 42px;">
                </div>

                <div style="margin-top: 6px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; flex-direction: column; gap: 8px;">
                    <h4 style="font-size: 13px; font-weight: 700; color: #475569; margin: 0; display: flex; align-items: center; gap: 6px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        Chọn vắc xin cho người này:
                    </h4>
                    <div style="display: grid; gap: 8px;">
                        ${generateVaccineChecklistHtml(index)}
                    </div>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', blockHtml);
        patientCount++;
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
                guardianSec.style.display = 'flex';
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

    function validatePublicRegisterForm() {
        const patientBlocks = document.querySelectorAll('.patient-form-block');
        if (patientBlocks.length === 0) {
            showToast('Vui lòng thêm ít nhất một người tiêm.', 'error');
            return false;
        }
        
        for (let i = 0; i < patientBlocks.length; i++) {
            const block = patientBlocks[i];
            const checked = block.querySelectorAll('.patient-vaccine-checkbox:checked');
            if (checked.length === 0) {
                showToast(`Vui lòng chọn ít nhất một loại vắc xin cho Người tiêm #${i + 1}.`, 'error');
                return false;
            }
        }
        return true;
    }

    function handlePublicRegisterSubmit(event) {
        if (event) event.preventDefault();
        
        if (!validatePublicRegisterForm()) {
            return;
        }

        const doSubmit = () => {
            const submitBtn = document.querySelector('.btn-submit-registration');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg class="animate-spin" style="width:16px;height:16px;margin-right:6px;display:inline-block;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg> Đang gửi...';
            }
            if (typeof showToast === 'function') {
                showToast('Đang gửi yêu cầu đặt lịch, vui lòng đợi trong giây lát...', 'info');
            }
            document.getElementById('publicRegisterForm').submit();
        };

        const confirmFn = window.showConfirmDialog || (typeof showConfirmDialog === 'function' ? showConfirmDialog : null);
        if (confirmFn) {
            confirmFn({
                title: 'Xác nhận đặt lịch',
                message: 'Bạn có chắc chắn muốn hoàn tất đặt lịch tiêm chủng này không?',
                confirmText: 'Xác nhận',
                cancelText: 'Hủy',
                onConfirm: doSubmit
            });
        } else {
            doSubmit();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        addPatientField();
    });
</script>
@endsection
