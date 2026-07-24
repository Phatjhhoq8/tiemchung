// Fallback an toàn cho Lucide Icon nếu thư viện CDN không tải được
if (typeof window.lucide === 'undefined') {
    window.lucide = {
        createIcons: () => console.warn('Thư viện Lucide không được tải thành công từ CDN.')
    };
}

// Quản lý CSRF Token trong Laravel cho các request AJAX
const getCsrfToken = () => {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
};

// ==========================================================================
// TOAST NOTIFICATIONS SYSTEM
// ==========================================================================
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast-item toast-${type}`;
    
    const bgColor = type === 'success' ? '#10b981' : (type === 'error' ? '#ef4444' : '#3b82f6');
    const iconName = type === 'success' ? 'check-circle' : (type === 'error' ? 'alert-circle' : 'info');

    toast.style.cssText = `
        background: ${bgColor};
        color: #ffffff;
        padding: 12px 20px;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.18);
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 600;
        pointer-events: auto;
        animation: toastSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        transition: all 0.3s ease;
    `;

    toast.innerHTML = `
        <i data-lucide="${iconName}" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
        <span style="flex-grow: 1;">${message}</span>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #fff; cursor: pointer; padding: 0;"><i data-lucide="x" style="width: 16px; height: 16px;"></i></button>
    `;

    container.appendChild(toast);
    lucide.createIcons();

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(50px)';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// ==========================================================================
// GIỎ HÀNG VẮC XIN (CART MANAGEMENT)
// ==========================================================================
function toggleCartDrawer() {
    const cartEl = document.getElementById('floatingCart');
    if (cartEl) {
        cartEl.classList.toggle('expanded');
    }
}

async function toggleCart(vaccineId) {
    const cards = document.querySelectorAll(`.vaccine-card[data-id="${vaccineId}"]`);
    let isSelected = false;
    if (cards.length > 0) {
        isSelected = cards[0].classList.contains('selected');
    }
    
    const url = isSelected ? '/cart/remove' : '/cart/add';
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ vaccine_id: vaccineId })
        });
        
        if (!response.ok) {
            throw new Error('Giao tiếp máy chủ thất bại.');
        }
        
        const data = await response.json();
        
        if (data.success) {
            // Cập nhật thẻ vắc xin ở tất cả vị trí (Grid trang chủ, Bảng giá, Modal)
            cards.forEach(cardEl => {
                const btn = cardEl.querySelector('.btn-select-vaccine');
                if (isSelected) {
                    cardEl.classList.remove('selected');
                    if (btn) {
                        btn.classList.remove('btn-selected');
                        btn.innerHTML = `<i data-lucide="plus"></i> <span>Chọn tiêm</span>`;
                    }
                } else {
                    cardEl.classList.add('selected');
                    if (btn) {
                        btn.classList.add('btn-selected');
                        btn.innerHTML = `<i data-lucide="check"></i> <span>Đã chọn</span>`;
                    }
                }
            });
            
            // Cập nhật nút trong Quick View Modal nếu đang mở
            const modalBtn = document.getElementById(`modalSelectBtn_${vaccineId}`);
            if (modalBtn) {
                if (isSelected) {
                    modalBtn.classList.remove('btn-selected');
                    modalBtn.style.backgroundColor = 'var(--primary-color, #c8102e)';
                    modalBtn.innerHTML = `<i data-lucide="plus"></i> <span>Chọn tiêm vắc xin này</span>`;
                } else {
                    modalBtn.classList.add('btn-selected');
                    modalBtn.style.backgroundColor = 'var(--secondary-color, #0284c7)';
                    modalBtn.innerHTML = `<i data-lucide="check"></i> <span>Đã chọn trong danh sách</span>`;
                }
            }

            lucide.createIcons();
            updateFloatingCart(data.cart, data.cart_count, data.total_price);
            showToast(isSelected ? 'Đã xóa vắc xin khỏi danh sách tiêm' : 'Đã thêm vắc xin vào danh sách tiêm!', 'success');
        }
    } catch (error) {
        console.error('Lỗi giỏ hàng:', error);
        showToast('Có lỗi xảy ra khi cập nhật giỏ hàng. Vui lòng thử lại.', 'error');
    }
}

function updateFloatingCart(cart, count, totalPrice) {
    const cartEl = document.getElementById('floatingCart');
    const cartCountEl = document.getElementById('cartCount');
    const cartTotalPriceEl = document.getElementById('cartTotalPrice');
    const drawerTotalPriceEl = document.getElementById('drawerTotalPrice');
    const cartListEl = document.getElementById('cartItemsList');
    
    if (!cartEl) return;
    
    if (count === 0) {
        cartEl.classList.add('hidden');
        cartEl.classList.remove('expanded');
        return;
    }
    
    cartEl.classList.remove('hidden');
    
    if (cartCountEl) cartCountEl.textContent = count;
    
    const formattedPrice = new Intl.NumberFormat('vi-VN').format(totalPrice) + ' đ';
    if (cartTotalPriceEl) cartTotalPriceEl.textContent = formattedPrice;
    if (drawerTotalPriceEl) drawerTotalPriceEl.textContent = formattedPrice;
    
    if (cartListEl) {
        cartListEl.innerHTML = '';
        Object.entries(cart).forEach(([id, item]) => {
            const itemPriceFormatted = new Intl.NumberFormat('vi-VN').format(item.price) + ' đ';
            const itemHtml = `
                <div class="cart-item" data-id="${id}">
                    <div class="cart-item-info">
                        <h5>${item.name}</h5>
                        <span>${itemPriceFormatted}</span>
                    </div>
                    <button class="remove-item-btn" onclick="toggleCart(${id})">
                        <i data-lucide="trash-2"></i>
                    </button>
                </div>
            `;
            cartListEl.insertAdjacentHTML('beforeend', itemHtml);
        });
        lucide.createIcons();
    }
}

async function clearCartUI() {
    try {
        const response = await fetch('/cart/clear', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (data.success) {
            updateFloatingCart({}, 0, 0);
            document.querySelectorAll('.vaccine-card').forEach(card => card.classList.remove('selected'));
            document.querySelectorAll('.btn-select-vaccine').forEach(btn => {
                btn.classList.remove('btn-selected');
                btn.innerHTML = `<i data-lucide="plus"></i> <span>Chọn tiêm</span>`;
            });
            lucide.createIcons();
            showToast('Đã xóa sạch giỏ hàng.', 'info');
        }
    } catch (e) {
        console.error(e);
    }
}

// ==========================================================================
// QUICK VIEW VACCINE DETAIL MODAL (SPA)
// ==========================================================================
async function openVaccineDetailModal(vaccineId, event) {
    if (event) event.preventDefault();

    const modal = document.getElementById('vaccineDetailModal');
    const content = document.getElementById('modalDetailContent');
    if (!modal || !content) return;

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    content.innerHTML = `
        <div style="padding: 60px; text-align: center;">
            <i data-lucide="loader-2" style="width: 40px; height: 40px; color: var(--primary-color, #c8102e); animation: spin 1s linear infinite;"></i>
            <p style="margin-top: 12px; color: #64748b; font-weight: 500;">Đang tải thông tin vắc xin...</p>
        </div>
    `;
    lucide.createIcons();

    try {
        const response = await fetch(`/vaccines/${vaccineId}`, {
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Không thể nạp dữ liệu vắc xin.');
        const data = await response.json();

        if (data.success) {
            const v = data.vaccine;

            // Cập nhật số lượt xem hiển thị trên thẻ card ngoài trang chủ / danh mục (Cộng +1 khi click)
            const viewsEl = document.getElementById(`vaccine-views-count-${v.id}`);
            if (viewsEl) {
                viewsEl.textContent = (v.views || 0).toLocaleString('vi-VN');
            }

            content.innerHTML = `
                <div style="display: flex; flex-wrap: wrap; overflow: hidden; border-radius: 16px;">
                    <div style="flex: 1 1 300px; background: #f8fafc; display: flex; align-items: center; justify-content: center; position: relative; min-height: 280px; padding: 20px;">
                        <img src="${v.image}" alt="${v.name}" style="max-width: 100%; max-height: 260px; object-fit: contain; border-radius: 12px;">
                        <span style="position: absolute; top: 16px; left: 16px; background: ${v.type === 'package' ? '#0284c7' : '#c8102e'}; color: #ffffff; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                            ${v.type_label}
                        </span>
                        <span style="position: absolute; bottom: 16px; left: 16px; background: rgba(0,0,0,0.6); color: #ffffff; padding: 4px 10px; border-radius: 8px; font-size: 11.5px; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                            <i data-lucide="eye" style="width: 13px; height: 13px;"></i> ${v.formatted_views || (v.views + ' lượt xem')}
                        </span>
                    </div>

                    <div style="flex: 1 1 400px; padding: 32px; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; items-center; justify-content: space-between;">
                                <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Xuất xứ: ${v.origin}</span>
                                <span style="font-size: 12px; font-weight: 600; color: #64748b; display: flex; align-items: center; gap: 4px;"><i data-lucide="eye" style="width: 14px; height: 14px;"></i> ${v.formatted_views || (v.views + ' lượt xem')}</span>
                            </div>
                            <h2 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 6px 0 12px 0; line-height: 1.3;">${v.name}</h2>
                            <p style="font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 20px;">${v.description || 'Vắc xin an toàn, đã được kiểm định nghiêm ngặt theo tiêu chuẩn của Bộ Y Tế.'}</p>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
                                <div>
                                    <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Phòng bệnh</span>
                                    <strong style="display: block; font-size: 13.5px; color: #1e293b; margin-top: 2px;">${v.disease_prevention}</strong>
                                </div>
                                <div>
                                    <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Độ tuổi chỉ định</span>
                                    <strong style="display: block; font-size: 13.5px; color: #1e293b; margin-top: 2px;">${v.age_group}</strong>
                                </div>
                                <div>
                                    <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Phác đồ tiêm</span>
                                    <strong style="display: block; font-size: 13.5px; color: #1e293b; margin-top: 2px;">${v.doses} mũi tiêm</strong>
                                </div>
                                <div>
                                    <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Nhà sản xuất</span>
                                    <strong style="display: block; font-size: 13.5px; color: #1e293b; margin-top: 2px;">${v.manufacturer || v.origin}</strong>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                            <div>
                                <span style="font-size: 12px; color: #64748b; display: block;">Giá tiêm niêm yết:</span>
                                <strong style="font-size: 22px; font-weight: 800; color: var(--primary-color, #c8102e);">${v.formatted_price}</strong>
                            </div>

                            <button id="modalSelectBtn_${v.id}" onclick="toggleCart(${v.id})" class="btn-select-detail ${v.is_in_cart ? 'btn-selected' : ''}" style="padding: 12px 22px; border-radius: 10px; border: none; color: #ffffff; font-weight: 700; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; background-color: ${v.is_in_cart ? 'var(--secondary-color, #0284c7)' : 'var(--primary-color, #c8102e)'};">
                                <i data-lucide="${v.is_in_cart ? 'check' : 'plus'}" style="width: 18px; height: 18px;"></i>
                                <span>${v.is_in_cart ? 'Đã chọn trong danh sách' : 'Chọn tiêm vắc xin này'}</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            lucide.createIcons();
        }
    } catch (err) {
        console.error(err);
        content.innerHTML = `<div style="padding: 40px; text-align: center; color: #ef4444;">Không thể tải dữ liệu vắc xin. Vui lòng thử lại.</div>`;
    }
}

function closeVaccineDetailModal() {
    const modal = document.getElementById('vaccineDetailModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// ==========================================================================
// SPA REGISTRATION DRAWER / MODAL
// ==========================================================================
async function openSpaRegisterModal(event) {
    if (event) event.preventDefault();

    const modal = document.getElementById('spaRegisterModal');
    const body = document.getElementById('spaRegisterBody');
    if (!modal || !body) return;

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    body.innerHTML = `
        <div style="text-align: center; padding: 40px;">
            <i data-lucide="loader-2" style="width: 36px; height: 36px; color: var(--primary-color, #c8102e); animation: spin 1s linear infinite;"></i>
            <p style="margin-top: 12px; color: #64748b;">Đang tải danh sách tiêm & trung tâm...</p>
        </div>
    `;
    lucide.createIcons();

    try {
        const response = await fetch('/register', {
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            const errData = await response.json();
            body.innerHTML = `
                <div style="text-align: center; padding: 40px;">
                    <i data-lucide="alert-circle" style="width: 48px; height: 48px; color: #f59e0b; margin-bottom: 12px;"></i>
                    <h3 style="font-size: 18px; font-weight: 700; color: #1e293b;">${errData.message || 'Danh sách tiêm chủng trống.'}</h3>
                    <p style="color: #64748b; margin-bottom: 20px;">Vui lòng chọn ít nhất 1 loại vắc xin từ bảng giá trước khi đăng ký tiêm.</p>
                    <button onclick="closeSpaRegisterModal()" class="btn-primary" style="background: var(--primary-color); color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 700; cursor: pointer;">Chọn vắc xin ngay</button>
                </div>
            `;
            lucide.createIcons();
            return;
        }

        const data = await response.json();
        if (data.success) {
            renderSpaRegisterForm(data);
        }
    } catch (e) {
        console.error(e);
        body.innerHTML = `<div style="padding: 40px; text-align: center; color: #ef4444;">Đã có lỗi xảy ra. Vui lòng thử lại.</div>`;
    }
}

function closeSpaRegisterModal() {
    const modal = document.getElementById('spaRegisterModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

function renderSpaRegisterForm(data) {
    const body = document.getElementById('spaRegisterBody');
    if (!body) return;

    const cartItems = Object.entries(data.cart).map(([id, item]) => `
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px dashed #e2e8f0; font-size: 13.5px;">
            <strong style="color: #1e293b;">${item.name}</strong>
            <span style="color: var(--primary-color, #c8102e); font-weight: 700;">${new Intl.NumberFormat('vi-VN').format(item.price)} đ</span>
        </div>
    `).join('');

    const centerOptions = data.centers.map(c => `
        <option value="${c.name}">📍 ${c.name} — ${c.address} (${c.phone})</option>
    `).join('');

    const todayStr = new Date().toISOString().split('T')[0];

    body.innerHTML = `
        <div style="display: grid; grid-template-columns: 1fr 300px; gap: 28px;">
            <!-- Form Đăng ký -->
            <div>
                <form id="spaFormRegisterSubmit" onsubmit="submitSpaRegistrationForm(event)">
                    <div id="spaFormErrorAlert" style="display: none; background: #fef2f2; border: 1px solid #ef4444; color: #991b1b; padding: 12px 16px; border-radius: 8px; font-size: 13.5px; margin-bottom: 20px;"></div>

                    <h4 style="font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="user" style="width: 18px; height: 18px; color: var(--primary-color);"></i>
                        1. Thông tin cá nhân người tiêm
                    </h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Họ tên người tiêm <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="patient_name" required placeholder="Ví dụ: Nguyễn Văn A" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Ngày sinh <span style="color: #ef4444;">*</span></label>
                            <input type="date" name="patient_dob" required max="${todayStr}" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Giới tính <span style="color: #ef4444;">*</span></label>
                            <select name="patient_gender" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Số điện thoại <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="patient_phone" required placeholder="0938603839" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                        </div>
                        <div style="grid-column: span 2;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Địa chỉ liên hệ <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="patient_address" required placeholder="Số nhà, đường, phường/xã, huyện..." style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                        </div>
                    </div>

                    <h4 style="font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="map-pin" style="width: 18px; height: 18px; color: #0284c7;"></i>
                        2. Địa điểm & Ngày hẹn tiêm
                    </h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 20px;">
                        <div style="grid-column: span 2;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Chi nhánh trung tâm Medicare <span style="color: #ef4444;">*</span></label>
                            <select name="center_name" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                                ${centerOptions}
                            </select>
                        </div>
                        <div style="grid-column: span 2;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Ngày tiêm dự kiến <span style="color: #ef4444;">*</span></label>
                            <input type="date" name="injection_date" required min="${todayStr}" value="${todayStr}" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                        </div>
                    </div>

                    <h4 style="font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="credit-card" style="width: 18px; height: 18px; color: #16a34a;"></i>
                        3. Phương thức thanh toán
                    </h4>

                    <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 24px;">
                        <label style="display: flex; align-items: center; gap: 10px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; cursor: pointer; background: #fff;">
                            <input type="radio" name="payment_method" value="QR" checked style="width: 16px; height: 16px;">
                            <span style="font-weight: 700; font-size: 13.5px; color: #1e293b;">Chuyển khoản / Quét mã QR tự động (Khuyên dùng)</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 10px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; cursor: pointer; background: #fff;">
                            <input type="radio" name="payment_method" value="Tại trung tâm" style="width: 16px; height: 16px;">
                            <span style="font-weight: 700; font-size: 13.5px; color: #1e293b;">Thanh toán trực tiếp khi đến tiêm chủng</span>
                        </label>
                    </div>

                    <button type="submit" id="spaSubmitBtn" style="width: 100%; background: var(--primary-color, #c8102e); color: #fff; border: none; padding: 14px; border-radius: 10px; font-size: 16px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.2s;">
                        <i data-lucide="shield-check" style="width: 20px; height: 20px;"></i>
                        <span>Xác Nhận Đăng Ký Tiêm Chủng</span>
                    </button>
                </form>
            </div>

            <!-- Tóm tắt đơn hàng -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 12px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <h4 style="font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 12px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Vắc xin đăng ký</h4>
                    ${cartItems}
                </div>

                <div style="border-top: 2px solid #e2e8f0; padding-top: 14px; margin-top: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 13px; color: #64748b; font-weight: 600;">TỔNG CHI PHÍ:</span>
                        <strong style="font-size: 20px; color: var(--primary-color, #c8102e); font-weight: 800;">${data.formatted_total_price}</strong>
                    </div>
                    <p style="font-size: 11.5px; color: #64748b; margin-top: 10px; line-height: 1.5;">Đã bao gồm phí khám lâm sàng trước tiêm và theo dõi sau tiêm tại Medicare.</p>
                </div>
            </div>
        </div>
    `;
    lucide.createIcons();
}

async function submitSpaRegistrationForm(event) {
    event.preventDefault();
    const form = event.target;
    const btn = document.getElementById('spaSubmitBtn');
    const alertBox = document.getElementById('spaFormErrorAlert');

    if (alertBox) alertBox.style.display = 'none';

    btn.disabled = true;
    btn.innerHTML = `<i data-lucide="loader-2" style="width: 20px; height: 20px; animation: spin 1s linear infinite;"></i> Đang xử lý đăng ký...`;
    lucide.createIcons();

    const formData = new FormData(form);

    try {
        const response = await fetch('/register', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json();

        if (response.status === 422 && data.errors) {
            btn.disabled = false;
            btn.innerHTML = `<i data-lucide="shield-check" style="width: 20px; height: 20px;"></i> <span>Xác Nhận Đăng Ký Tiêm Chủng</span>`;
            lucide.createIcons();

            let errHtml = `<strong style="display: block; margin-bottom: 6px;">Vui lòng kiểm tra lại thông tin:</strong><ul style="margin: 0; padding-left: 20px;">`;
            Object.values(data.errors).forEach(errArr => {
                errArr.forEach(err => {
                    errHtml += `<li>${err}</li>`;
                });
            });
            errHtml += `</ul>`;
            alertBox.innerHTML = errHtml;
            alertBox.style.display = 'block';
            return;
        }

        if (data.success) {
            updateFloatingCart({}, 0, 0);
            renderRegistrationTicketSuccess(data);
            showToast('Đăng ký tiêm chủng thành công!', 'success');
        } else {
            throw new Error(data.message || 'Đăng ký không thành công.');
        }
    } catch (err) {
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = `<i data-lucide="shield-check" style="width: 20px; height: 20px;"></i> <span>Xác Nhận Đăng Ký Tiêm Chủng</span>`;
        lucide.createIcons();
        if (alertBox) {
            alertBox.textContent = err.message || 'Đã có lỗi xảy ra trong quá trình đăng ký.';
            alertBox.style.display = 'block';
        }
    }
}

function renderRegistrationTicketSuccess(data) {
    const body = document.getElementById('spaRegisterBody');
    if (!body) return;

    body.innerHTML = `
        <div style="text-align: center; padding: 20px 0;">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: #ecfdf5; color: #10b981; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; border: 3px solid #10b981;">
                <i data-lucide="check-circle" style="width: 40px; height: 40px;"></i>
            </div>
            <h2 style="font-size: 24px; font-weight: 800; color: #1e293b; margin-bottom: 6px;">Đăng Ký Tiêm Chủng Thành Công!</h2>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 24px;">Cảm ơn quý khách đã tin tưởng và chọn dịch vụ tiêm chủng Medicare.</p>

            <div style="max-width: 500px; margin: 0 auto; background: #f8fafc; border: 2px dashed #cbd5e1; padding: 24px; border-radius: 16px; text-align: left;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 14px;">
                    <span style="font-size: 13px; color: #64748b; font-weight: 700; text-transform: uppercase;">MÃ PHIẾU ĐĂNG KÝ:</span>
                    <strong style="font-size: 20px; color: var(--primary-color, #c8102e); font-weight: 900; letter-spacing: 0.05em;">${data.registration_code}</strong>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13.5px; color: #334155; margin-bottom: 14px;">
                    <div><strong>Họ tên:</strong> ${data.patient_name}</div>
                    <div><strong>Số điện thoại:</strong> ${data.patient_phone}</div>
                    <div><strong>Ngày tiêm hẹn:</strong> ${data.injection_date}</div>
                    <div><strong>Trạng thái:</strong> <span style="color: #0284c7; font-weight: 700;">${data.status}</span></div>
                    <div style="grid-column: span 2;"><strong>Địa điểm tiêm:</strong> ${data.center_name}</div>
                    <div style="grid-column: span 2;"><strong>Tổng thanh toán:</strong> <strong style="color: var(--primary-color); font-size: 16px;">${data.total_price_formatted}</strong></div>
                </div>
            </div>

            <div style="margin-top: 24px; display: flex; justify-content: center; gap: 14px;">
                <button onclick="window.print()" class="btn-secondary" style="padding: 10px 20px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                    <i data-lucide="printer" style="width: 18px; height: 18px;"></i> In phiếu hẹn
                </button>
                <button onclick="closeSpaRegisterModal()" class="btn-primary" style="padding: 10px 24px; border-radius: 8px; border: none; background: var(--primary-color, #c8102e); color: #fff; cursor: pointer; font-weight: 700;">
                    Hoàn tất & Đóng
                </button>
            </div>
        </div>
    `;
    lucide.createIcons();
}

// ==========================================================================
// AJAX VACCINE SEARCH & FILTER ENGINE (SPA)
// ==========================================================================
let filterTimer = null;
let currentSearch = '';
let currentAgeGroup = '';
let currentDisease = '';
let currentType = 'single';

function debouncedFilterVaccinesSpa() {
    clearTimeout(filterTimer);
    filterTimer = setTimeout(() => {
        const input = document.getElementById('spaSearchInput');
        if (input) {
            currentSearch = input.value;
            filterVaccinesSpa();
        }
    }, 300);
}

function setAgeGroupFilter(age, event) {
    if (event) event.preventDefault();
    currentAgeGroup = age;

    document.querySelectorAll('#ageGroupFilterContainer .filter-chip').forEach(chip => {
        chip.classList.remove('active');
    });
    if (event && event.currentTarget) {
        event.currentTarget.classList.add('active');
    }

    filterVaccinesSpa();
}

function setDiseaseFilter(disease, event) {
    if (event) event.preventDefault();
    currentSearch = disease;
    const searchInput = document.getElementById('spaSearchInput');
    if (searchInput) searchInput.value = disease;

    document.querySelectorAll('#diseaseFilterList a').forEach(a => a.classList.remove('active'));
    if (event && event.currentTarget) event.currentTarget.classList.add('active');

    filterVaccinesSpa();
}

function setVaccineTypeFilter(type, event) {
    if (event) event.preventDefault();
    currentType = type;

    const btnSingle = document.getElementById('tabBtnSingle');
    const btnPackage = document.getElementById('tabBtnPackage');
    const typeInput = document.getElementById('spaTypeInput');
    const titleEl = document.getElementById('vaccineSectionTitle');

    if (typeInput) typeInput.value = type;

    if (btnSingle && btnPackage) {
        if (type === 'single') {
            btnSingle.style.color = 'var(--primary-color)';
            btnSingle.style.borderBottomColor = 'var(--primary-color)';
            btnPackage.style.color = 'var(--text-muted)';
            btnPackage.style.borderBottomColor = 'transparent';
        } else {
            btnPackage.style.color = 'var(--primary-color)';
            btnPackage.style.borderBottomColor = 'var(--primary-color)';
            btnSingle.style.color = 'var(--text-muted)';
            btnSingle.style.borderBottomColor = 'transparent';
        }
    }

    filterVaccinesSpa();
}

function resetVaccineFilters(event) {
    if (event) event.preventDefault();
    currentSearch = '';
    currentAgeGroup = '';
    currentType = 'single';

    const searchInput = document.getElementById('spaSearchInput');
    if (searchInput) searchInput.value = '';

    const btnClear = document.getElementById('btnClearFilters');
    if (btnClear) btnClear.style.display = 'none';

    setVaccineTypeFilter('single');
}

async function filterVaccinesSpa(event) {
    if (event) event.preventDefault();

    const container = document.getElementById('vaccineGridContainer');
    if (!container) return;

    const searchInput = document.getElementById('spaSearchInput');
    if (searchInput) currentSearch = searchInput.value;

    const params = new URLSearchParams();
    if (currentSearch) params.append('search', currentSearch);
    if (currentAgeGroup) params.append('age_group', currentAgeGroup);
    if (currentType) params.append('type', currentType);

    const btnClear = document.getElementById('btnClearFilters');
    if (btnClear) {
        if (currentSearch || currentAgeGroup || currentType !== 'single') {
            btnClear.style.display = 'inline-flex';
        } else {
            btnClear.style.display = 'none';
        }
    }

    container.style.opacity = '0.4';
    container.style.transition = 'opacity 0.2s ease';

    try {
        const url = `/vaccines?${params.toString()}`;
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Lọc vắc xin thất bại.');
        const data = await response.json();

        if (data.success) {
            container.innerHTML = data.html;
            container.style.opacity = '1';
            lucide.createIcons();

            const countEl = document.getElementById('vaccineCountLabel');
            if (countEl) countEl.textContent = data.count;

            window.history.pushState({}, '', url);
        }
    } catch (e) {
        console.error(e);
        container.style.opacity = '1';
    }
}
