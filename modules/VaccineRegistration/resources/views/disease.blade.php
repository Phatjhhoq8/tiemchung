@extends('vaccine::layouts.app')

@section('title', $info['title'] . ' - Medicare')

@section('styles')
<style>
    .disease-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    .disease-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted, #64748b);
        font-size: 14px;
        margin-bottom: 24px;
    }
    .disease-breadcrumb a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s;
    }
    .disease-breadcrumb a:hover {
        color: var(--primary-color, #c8102e);
    }
    .disease-breadcrumb i {
        width: 14px;
        height: 14px;
    }
    .disease-header {
        margin-bottom: 36px;
    }
    .disease-header h1 {
        font-size: 32px;
        font-weight: 800;
        color: var(--accent-color, #004b8f);
        margin: 0 0 12px 0;
    }
    .disease-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 40px;
    }
    @media (min-width: 992px) {
        .disease-layout {
            grid-template-columns: 1.6fr 1fr;
        }
    }
    .disease-info-column {
        display: flex;
        flex-direction: column;
        gap: 40px;
    }
    .disease-article-box {
        background: #ffffff;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        border: 1px solid #f1f5f9;
    }
    .disease-article-box h6 {
        font-size: 20px;
        font-weight: 700;
        color: var(--primary-color, #c8102e);
        margin: 0 0 20px 0;
        border-bottom: 2px solid #fee2e2;
        padding-bottom: 10px;
    }
    .disease-article-box p {
        font-size: 15.5px;
        line-height: 1.7;
        color: #334155;
        margin: 0 0 16px 0;
    }
    .disease-article-box p strong {
        color: var(--text-primary, #1e293b);
    }
    .disease-article-box ul {
        margin: 0 0 20px 20px;
        padding: 0;
    }
    .disease-article-box li {
        font-size: 15.5px;
        line-height: 1.7;
        color: #334155;
        margin-bottom: 8px;
    }
    .vaccine-list-section h3 {
        font-size: 20px;
        font-weight: 700;
        color: var(--accent-color, #004b8f);
        margin: 0 0 24px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .vaccine-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }
    @media (min-width: 576px) {
        .vaccine-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    .vaccine-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }
    .vaccine-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(200, 16, 46, 0.08);
        border-color: rgba(200, 16, 46, 0.2);
    }
    .vaccine-badge-origin {
        background: #f1f5f9;
        color: #475569;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        align-self: flex-start;
        margin-bottom: 12px;
    }
    .vaccine-img-wrapper {
        height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
    }
    .vaccine-img-wrapper img {
        max-height: 100%;
        max-width: 80%;
        object-fit: contain;
    }
    .vaccine-name {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 6px 0;
        line-height: 1.4;
        min-height: 44px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .vaccine-prevents {
        font-size: 13px;
        color: var(--text-muted, #64748b);
        margin-bottom: 16px;
        line-height: 1.4;
        min-height: 36px;
    }
    .vaccine-footer {
        border-top: 1px dashed #e2e8f0;
        padding-top: 16px;
        margin-top: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .vaccine-price-box {
        display: flex;
        flex-direction: column;
    }
    .vaccine-price {
        font-size: 18px;
        font-weight: 800;
        color: var(--primary-color, #c8102e);
    }
    .vaccine-btn-add {
        width: 100%;
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #10b981;
        padding: 10px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .vaccine-btn-add:hover {
        background: #059669;
        color: #ffffff;
    }
    .vaccine-btn-add.selected {
        background: var(--secondary-color, #eaaa00);
        color: #ffffff;
        border-color: var(--secondary-color, #eaaa00);
    }
    .vaccine-btn-add.selected:hover {
        opacity: 0.9;
    }
    
    /* Consultant Form Column */
    .consult-column {
        position: relative;
    }
    .consult-card {
        background: #ffffff;
        border-radius: 16px;
        border: 2px solid var(--primary-color, #c8102e);
        padding: 32px;
        box-shadow: 0 10px 30px rgba(200, 16, 46, 0.05);
        position: sticky;
        top: 100px;
    }
    .consult-card h3 {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-primary, #1e293b);
        margin: 0 0 8px 0;
    }
    .consult-card h3 span {
        color: var(--primary-color, #c8102e);
    }
    .consult-card p {
        font-size: 14px;
        color: var(--text-muted, #64748b);
        margin-bottom: 24px;
        line-height: 1.5;
    }
    .consult-form-group {
        margin-bottom: 16px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .consult-form-group label {
        font-size: 13.5px;
        font-weight: 600;
        color: #334155;
    }
    .consult-form-group label span {
        color: #ef4444;
    }
    .consult-form-group input,
    .consult-form-group select,
    .consult-form-group textarea {
        width: 100%;
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 14.5px;
        outline: none;
        transition: all 0.2s;
    }
    .consult-form-group input:focus,
    .consult-form-group select:focus,
    .consult-form-group textarea:focus {
        border-color: var(--primary-color, #c8102e);
        box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.15);
    }
    .consult-btn-submit {
        width: 100%;
        background: var(--primary-color, #c8102e);
        color: #ffffff;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .consult-btn-submit:hover {
        background: var(--primary-hover, #a00d24);
        box-shadow: 0 4px 12px rgba(200, 16, 46, 0.2);
    }
    .consult-btn-submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    /* Success Box Overlay */
    .success-overlay {
        text-align: center;
        padding: 20px 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
    }
    .success-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #ecfdf5;
        color: #10b981;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .success-overlay h4 {
        font-size: 18px;
        font-weight: 700;
        color: #065f46;
        margin: 0;
    }
    .success-overlay p {
        font-size: 14.5px;
        line-height: 1.6;
        color: #374151;
        margin: 0;
    }
    
    .error-msg {
        color: #ef4444;
        font-size: 12.5px;
        margin-top: 4px;
        display: none;
    }
</style>
@endsection

@section('content')
<div class="disease-page">
    <div class="disease-breadcrumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i data-lucide="chevron-right"></i>
        <a href="{{ route('vaccine.index') }}">Danh mục vắc xin</a>
        <i data-lucide="chevron-right"></i>
        <span>{{ $info['title'] }}</span>
    </div>
    
    <div class="disease-header">
        <h1>{{ $info['title'] }}</h1>
    </div>
    
    <div class="disease-layout">
        <!-- Cột trái: Thông tin chuyên môn & Vắc xin -->
        <div class="disease-info-column">
            <div class="disease-article-box">
                {!! $info['description'] !!}
            </div>
            
            <div class="vaccine-list-section">
                <h3><i data-lucide="syringe"></i> Danh sách vắc xin phòng bệnh ({{ $vaccines->count() }})</h3>
                
                @if($vaccines->isEmpty())
                    <div style="background: #ffffff; border-radius: 12px; padding: 30px; text-align: center; border: 1px dashed #cbd5e1; color: var(--text-muted);">
                        Hiện tại chưa có vắc xin cụ thể cho nhóm bệnh này trên hệ thống. Vui lòng gửi thông tin tư vấn để bác sĩ hỗ trợ.
                    </div>
                @else
                    <div class="vaccine-grid">
                        @foreach($vaccines as $vac)
                            <div class="vaccine-card">
                                <div>
                                    <div class="vaccine-badge-origin">
                                        <i data-lucide="globe" style="width: 12px; height: 12px;"></i>
                                        <span>{{ $vac->origin }}</span>
                                    </div>
                                    <div class="vaccine-img-wrapper">
                                        <img src="{{ asset('images/vaccines/' . ($vac->image ?: 'hexaxim.jpg')) }}" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/hexaxim.jpg') }}';" alt="{{ $vac->name }}">
                                    </div>
                                    <h4 class="vaccine-name">{{ $vac->name }}</h4>
                                    <p class="vaccine-prevents">Phòng bệnh: {{ $vac->disease_prevention }}</p>
                                </div>
                                <div class="vaccine-footer">
                                    <div class="vaccine-price-box">
                                        <span style="font-size: 11px; color: var(--text-muted);">Giá tiêm lẻ:</span>
                                        <span class="vaccine-price">{{ number_format($vac->price, 0, ',', '.') }} đ</span>
                                    </div>
                                    <button class="vaccine-btn-add {{ isset($cart[$vac->id]) ? 'selected' : '' }}" onclick="toggleCartFromDisease({{ $vac->id }}, this)">
                                        <i data-lucide="{{ isset($cart[$vac->id]) ? 'check' : 'shopping-cart' }}" style="width: 16px; height: 16px;"></i>
                                        <span>{{ isset($cart[$vac->id]) ? 'Đã chọn đặt' : 'Đặt mua ngay' }}</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Cột phải: Form tư vấn -->
        <div class="consult-column">
            <div class="consult-card" id="consultFormCard">
                <h3>Yêu cầu tư vấn <span>miễn phí</span></h3>
                <p>Medicare sẽ liên hệ lại ngay để tư vấn phác đồ tiêm chủng vắc xin phòng bệnh {{ $diseaseDecoded }} thích hợp nhất cho bạn.</p>
                
                <form id="diseaseConsultForm" onsubmit="submitDiseaseConsult(event)">
                    @csrf
                    <div class="consult-form-group">
                        <label>Hình thức tư vấn <span>*</span></label>
                        <div class="consult-type-toggle" style="display: inline-flex; width: 100%; background: #f1f5f9; border-radius: 30px; padding: 4px; border: 1px solid #cbd5e1; margin-top: 6px; box-sizing: border-box;">
                            <button type="button" id="btnConsultOnline" onclick="setConsultType('online')" style="flex: 1; border: none; padding: 10px 12px; border-radius: 26px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s; background: var(--primary-color, #c8102e); color: #ffffff; text-align: center;">
                                Tư vấn qua điện thoại (Online)
                            </button>
                            <button type="button" id="btnConsultOffline" onclick="setConsultType('offline')" style="flex: 1; border: none; padding: 10px 12px; border-radius: 26px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s; background: transparent; color: #475569; text-align: center;">
                                Tư vấn tại trung tâm (Offline)
                            </button>
                            <input type="hidden" name="consultType" id="consultTypeValue" value="online">
                        </div>
                    </div>

                    <div class="consult-form-group">
                        <label for="customerName">Họ tên người liên hệ <span>*</span></label>
                        <input type="text" name="customerName" id="customerName" placeholder="Nhập họ tên của bạn" required>
                        <span class="error-msg" id="err_customerName"></span>
                    </div>
                    
                    <div class="consult-form-group">
                        <label for="customerPhone">Số điện thoại liên hệ <span>*</span></label>
                        <input type="tel" name="customerPhone" id="customerPhone" placeholder="Ví dụ: 0912345678" required>
                        <span class="error-msg" id="err_customerPhone"></span>
                    </div>
                    
                    <div class="consult-form-group" id="centerSelectGroup" style="display: none;">
                        <label for="centerName">Chi nhánh tư vấn gần nhất <span>*</span></label>
                        <select name="centerName" id="centerName">
                            <option value="">-- Chọn trung tâm Medicare --</option>
                            @foreach($centers as $c)
                                <option value="{{ $c->name }}">{{ $c->name }} — {{ $c->address }}</option>
                            @endforeach
                        </select>
                        <span class="error-msg" id="err_centerName"></span>
                    </div>
                    
                    <div class="consult-form-group">
                        <label for="customerNote">Ghi chú yêu cầu tư vấn</label>
                        <textarea name="customerNote" id="customerNote" rows="3" placeholder="Nhập độ tuổi người tiêm hoặc câu hỏi của bạn (nếu có)..."></textarea>
                        <span class="error-msg" id="err_customerNote"></span>
                    </div>
                    
                    <button type="submit" class="consult-btn-submit" id="btnSubmitConsult">
                        <i data-lucide="send" style="width: 16px; height: 16px;"></i>
                        <span>Gửi yêu cầu ngay</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Hàm toggle giỏ hàng
    async function toggleCartFromDisease(vaccineId, buttonEl) {
        const isSelected = buttonEl.classList.contains('selected');
        const url = isSelected ? '{{ route("cart.remove") }}' : '{{ route("cart.add") }}';
        
        buttonEl.disabled = true;
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ vaccine_id: vaccineId })
            });
            
            const data = await response.json();
            if (data.success) {
                // Thay đổi trạng thái button
                if (isSelected) {
                    buttonEl.classList.remove('selected');
                    buttonEl.innerHTML = '<i data-lucide="shopping-cart" style="width: 16px; height: 16px;"></i> <span>Đặt mua ngay</span>';
                } else {
                    buttonEl.classList.add('selected');
                    buttonEl.innerHTML = '<i data-lucide="check" style="width: 16px; height: 16px;"></i> <span>Đã chọn đặt</span>';
                }
                lucide.createIcons();
                
                // Cập nhật giỏ hàng bay (floating cart) nếu có hàm của hệ thống
                const cartBadge = document.getElementById('cartCount');
                const cartPrice = document.getElementById('cartTotalPrice');
                const drawerTotalPrice = document.getElementById('drawerTotalPrice');
                const cartItemsList = document.getElementById('cartItemsList');
                
                if (cartBadge) cartBadge.textContent = data.cart_count;
                
                const formattedPrice = new Intl.NumberFormat('vi-VN').format(data.total_price) + ' đ';
                if (cartPrice) cartPrice.textContent = formattedPrice;
                if (drawerTotalPrice) drawerTotalPrice.textContent = formattedPrice;
                
                // Cập nhật drawer list
                if (cartItemsList) {
                    let itemsHtml = '';
                    Object.entries(data.cart).forEach(([id, item]) => {
                        itemsHtml += `
                            <div class="cart-item" data-id="${id}">
                                <div class="cart-item-info">
                                    <h5>${item.name}</h5>
                                    <span>${new Intl.NumberFormat('vi-VN').format(item.price)} đ</span>
                                </div>
                                <button class="remove-item-btn" onclick="toggleCart(${id})">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </div>
                        `;
                    });
                    cartItemsList.innerHTML = itemsHtml;
                    lucide.createIcons();
                }
                
                // Show/hide floating cart
                const floatCart = document.getElementById('floatingCart');
                if (floatCart) {
                    if (data.cart_count > 0) {
                        floatCart.classList.remove('hidden');
                    } else {
                        floatCart.classList.add('hidden');
                    }
                }
            }
        } catch (e) {
            console.error('Lỗi giỏ hàng:', e);
        } finally {
            buttonEl.disabled = false;
        }
    }

    // Submit form tư vấn bằng AJAX
    async function submitDiseaseConsult(event) {
        event.preventDefault();
        if (!confirm('Bạn có thực sự muốn gửi yêu cầu tư vấn tiêm chủng này không?')) {
            return;
        }
        
        const form = document.getElementById('diseaseConsultForm');
        const submitBtn = document.getElementById('btnSubmitConsult');
        const cardContainer = document.getElementById('consultFormCard');
        
        // Reset errors
        document.querySelectorAll('.error-msg').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader-2" style="width: 16px; height: 16px; animation: spin 1s linear infinite;"></i> <span>Đang gửi...</span>';
        lucide.createIcons();
        
        const formData = new FormData(form);
        const url = '{{ route("vaccine.disease.consult", ["disease" => urlencode($diseaseDecoded)]) }}';
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (response.status === 422) {
                // Validation error
                Object.entries(data.errors).forEach(([field, messages]) => {
                    const errEl = document.getElementById('err_' + field);
                    if (errEl) {
                        errEl.textContent = messages[0];
                        errEl.style.display = 'block';
                    }
                });
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i data-lucide="send" style="width: 16px; height: 16px;"></i> <span>Gửi yêu cầu ngay</span>';
                lucide.createIcons();
            } else if (!response.ok) {
                throw new Error(data.message || 'Lỗi hệ thống');
            } else {
                // Success
                cardContainer.innerHTML = `
                    <div class="success-overlay">
                        <div class="success-icon-wrapper">
                            <i data-lucide="check-circle-2" style="width: 32px; height: 32px;"></i>
                        </div>
                        <h4>Gửi Yêu Cầu Thành Công</h4>
                        <p style="margin-bottom: 8px;"><strong>Mã tư vấn: ${data.registration_code}</strong></p>
                        <p>${data.message}</p>
                        <button onclick="window.location.reload()" class="consult-btn-submit" style="background: #10b981; max-width: 160px; margin-top: 16px;">Xác nhận</button>
                    </div>
                `;
                lucide.createIcons();
            }
        } catch (e) {
            alert('Có lỗi xảy ra: ' + e.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i data-lucide="send" style="width: 16px; height: 16px;"></i> <span>Gửi yêu cầu ngay</span>';
            lucide.createIcons();
        }
    }

    function setConsultType(value) {
        const input = document.getElementById('consultTypeValue');
        const btnOnline = document.getElementById('btnConsultOnline');
        const btnOffline = document.getElementById('btnConsultOffline');
        const group = document.getElementById('centerSelectGroup');
        const select = document.getElementById('centerName');
        
        if (!input || !btnOnline || !btnOffline || !group || !select) return;
        
        input.value = value;
        
        if (value === 'online') {
            btnOnline.style.background = 'var(--primary-color, #c8102e)';
            btnOnline.style.color = '#ffffff';
            btnOffline.style.background = 'transparent';
            btnOffline.style.color = '#475569';
            
            group.style.display = 'none';
            select.removeAttribute('required');
            select.value = '';
        } else {
            btnOffline.style.background = 'var(--primary-color, #c8102e)';
            btnOffline.style.color = '#ffffff';
            btnOnline.style.background = 'transparent';
            btnOnline.style.color = '#475569';
            
            group.style.display = 'flex';
            select.setAttribute('required', 'required');
        }
    }
</script>
@endsection
