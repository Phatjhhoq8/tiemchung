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
    const cards = document.querySelectorAll(`.vaccine-card[data-id="${vaccineId}"], .catalog-product-card[data-id="${vaccineId}"]`);
    const detailBtns = document.querySelectorAll(`.btn-select-detail[data-id="${vaccineId}"], .btn-select-detail`);
    
    let isSelected = false;
    if (cards.length > 0 && cards[0].classList.contains('selected')) {
        isSelected = true;
    } else if (detailBtns.length > 0 && detailBtns[0].classList.contains('btn-selected')) {
        isSelected = true;
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
            const inCart = Boolean(data.cart && data.cart[vaccineId]);
            
            // Cập nhật thẻ vắc xin ở tất cả vị trí (trang chủ, danh mục sản phẩm, vắc xin liên quan)
            cards.forEach(cardEl => {
                const btn = cardEl.querySelector('.btn-select-vaccine');
                if (inCart) {
                    cardEl.classList.add('selected');
                    if (btn) {
                        btn.classList.add('btn-selected');
                        btn.innerHTML = `<i data-lucide="x"></i> <span>Hủy chọn</span>`;
                    }
                } else {
                    cardEl.classList.remove('selected');
                    if (btn) {
                        btn.classList.remove('btn-selected');
                        btn.innerHTML = `<i data-lucide="plus"></i> <span>Chọn tiêm</span>`;
                    }
                }
            });
            
            // Cập nhật nút đăng ký trên trang chi tiết sản phẩm
            detailBtns.forEach(btn => {
                const btnVacId = btn.getAttribute('data-id');
                if (!btnVacId || parseInt(btnVacId) === parseInt(vaccineId)) {
                    if (inCart) {
                        btn.classList.add('btn-selected');
                        btn.style.backgroundColor = 'var(--secondary-color, #eaaa00)';
                        btn.innerHTML = `<i data-lucide="check" style="width: 17px; height: 17px;"></i> <span>Đã chọn vắc xin</span>`;
                    } else {
                        btn.classList.remove('btn-selected');
                        btn.style.backgroundColor = 'var(--primary-color, #c8102e)';
                        btn.innerHTML = `<i data-lucide="plus" style="width: 17px; height: 17px;"></i> <span>Đăng ký tiêm chủng</span>`;
                    }
                }
            });
            
            // Cập nhật nút trong Quick View Modal nếu đang mở
            const modalBtn = document.getElementById(`modalSelectBtn_${vaccineId}`);
            if (modalBtn) {
                if (inCart) {
                    modalBtn.classList.add('btn-selected');
                    modalBtn.style.backgroundColor = '#fff1f2';
                    modalBtn.style.borderColor = '#fecdd3';
                    modalBtn.style.color = 'var(--primary-color, #c8102e)';
                    modalBtn.innerHTML = `<i data-lucide="x"></i> <span>Hủy chọn</span>`;
                } else {
                    modalBtn.classList.remove('btn-selected');
                    modalBtn.style.backgroundColor = 'var(--primary-color, #c8102e)';
                    modalBtn.style.borderColor = 'var(--primary-color, #c8102e)';
                    modalBtn.style.color = '#ffffff';
                    modalBtn.innerHTML = `<i data-lucide="plus"></i> <span>Chọn vắc xin này</span>`;
                }
            }

            if (window.lucide) {
                lucide.createIcons();
            }
            updateFloatingCart(data.cart, data.cart_count, data.total_price);
            showToast(inCart ? 'Đã thêm vắc xin vào danh sách tiêm!' : 'Đã xóa vắc xin khỏi danh sách tiêm', 'success');
        }
    } catch (error) {
        console.error('Lỗi giỏ hàng:', error);
        showToast('Có lỗi xảy ra khi cập nhật giỏ hàng. Vui lòng thử lại.', 'error');
    }
}

function toggleHeaderCartDropdown(event) {
    if (event) {
        event.stopPropagation();
    }
    const dropdown = document.getElementById('headerCartDropdown');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Bắt sự kiện click ngoài màn hình để tự động đóng dropdown giỏ hàng trên Header
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('headerCartWrapper');
    const dropdown = document.getElementById('headerCartDropdown');
    if (dropdown && !dropdown.classList.contains('hidden') && wrapper && !wrapper.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});

function updateFloatingCart(cart, count, totalPrice) {
    const cartBtn = document.getElementById('headerCartBtn');
    const cartCountEl = document.getElementById('cartCount');
    const drawerTotalPriceEl = document.getElementById('drawerTotalPrice');
    const cartListEl = document.getElementById('cartItemsList');
    const dropdown = document.getElementById('headerCartDropdown');
    
    if (cartBtn) {
        cartBtn.classList.remove('hidden');
        if (count === 0 && dropdown) {
            dropdown.classList.add('hidden');
        }
    }
    
    if (cartCountEl) cartCountEl.textContent = count;
    
    const formattedPrice = new Intl.NumberFormat('vi-VN').format(totalPrice) + ' đ';
    if (drawerTotalPriceEl) drawerTotalPriceEl.textContent = formattedPrice;
    
    if (cartListEl) {
        if (!cart || Object.keys(cart).length === 0) {
            cartListEl.innerHTML = `
                <div style="text-align: center; padding: 24px 12px; color: #94a3b8; font-size: 13.5px;">
                    <i data-lucide="shopping-cart" style="width: 32px; height: 32px; margin-bottom: 8px; opacity: 0.5;"></i>
                    <p style="margin: 0;">Chưa có vắc xin nào trong danh sách tiêm</p>
                </div>
            `;
        } else {
            cartListEl.innerHTML = '';
            Object.entries(cart).forEach(([id, item]) => {
                const itemPriceFormatted = new Intl.NumberFormat('vi-VN').format(item.price) + ' đ';
                const itemHtml = `
                    <div class="cart-item-row" data-id="${id}">
                        <div class="cart-item-info">
                            <strong class="cart-item-name">${item.name}</strong>
                            <span class="cart-item-price">${itemPriceFormatted}</span>
                        </div>
                        <button type="button" onclick="toggleCart(${id})" class="cart-item-remove" title="Xóa vắc xin">
                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                        </button>
                    </div>
                `;
                cartListEl.insertAdjacentHTML('beforeend', itemHtml);
            });
        }
        if (window.lucide) {
            lucide.createIcons();
        }
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
            document.querySelectorAll('.vaccine-card, .catalog-product-card').forEach(card => card.classList.remove('selected'));
            document.querySelectorAll('.btn-select-vaccine').forEach(btn => {
                btn.classList.remove('btn-selected');
                btn.innerHTML = `<i data-lucide="plus"></i> <span>Chọn vắc xin</span>`;
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

                            <button id="modalSelectBtn_${v.id}" onclick="toggleCart(${v.id})" class="btn-select-detail ${v.is_in_cart ? 'btn-selected' : ''}" style="padding: 12px 22px; border-radius: 10px; border: 1px solid ${v.is_in_cart ? '#fecdd3' : 'var(--primary-color, #c8102e)'}; color: ${v.is_in_cart ? 'var(--primary-color, #c8102e)' : '#ffffff'}; font-weight: 700; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; background-color: ${v.is_in_cart ? '#fff1f2' : 'var(--primary-color, #c8102e)'};">
                                <i data-lucide="${v.is_in_cart ? 'x' : 'plus'}" style="width: 18px; height: 18px;"></i>
                                <span>${v.is_in_cart ? 'Hủy chọn' : 'Chọn vắc xin này'}</span>
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
            window.lastFetchCenters = errData.centers || []; // lưu trữ tạm thời để vẽ form tư vấn
            body.innerHTML = `
                <div style="text-align: center; padding: 40px;">
                    <i data-lucide="help-circle" style="width: 56px; height: 56px; color: var(--secondary-color, #eaaa00); margin: 0 auto 16px auto;"></i>
                    <h3 style="font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 10px;">Bạn chưa chọn vắc xin nào</h3>
                    <p style="color: #64748b; margin-bottom: 24px; max-width: 400px; margin-left: auto; margin-right: auto; line-height: 1.5; font-size: 14.5px;">Bạn có muốn tham khảo các gói vắc xin ưu đãi thiết kế sẵn hoặc gửi yêu cầu tư vấn để bác sĩ Medicare liên hệ ngay?</p>
                    <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                        <a href="/vaccines?type=package" class="btn-primary" style="background: var(--secondary-color, #eaaa00); color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; min-width: 160px;">Chọn gói vắc xin</a>
                        <button onclick="renderSpaConsultForm()" class="btn-primary" style="background: var(--primary-color, #c8102e); color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; min-width: 160px;">Yêu cầu tư vấn</button>
                    </div>
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
    if (!confirm('Bạn có thực sự muốn gửi thông tin đăng ký tiêm chủng này không?')) {
        return;
    }
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
const vaccineFilterParams = new URLSearchParams(window.location.search);
let currentSearch = vaccineFilterParams.get('search') || '';
let currentAgeGroup = vaccineFilterParams.get('age_group') || '';
let currentDisease = vaccineFilterParams.get('disease') || '';
let currentOrigin = vaccineFilterParams.get('origin') || '';
let currentDoses = vaccineFilterParams.get('doses') || '';
let currentSort = vaccineFilterParams.get('sort') || 'popular';
let currentType = vaccineFilterParams.get('type') || '';
let currentPage = vaccineFilterParams.get('page') || '1';

function debouncedFilterVaccinesSpa() {
    clearTimeout(filterTimer);
    filterTimer = setTimeout(() => {
        const input = document.getElementById('spaSearchInput');
        if (input) {
            currentSearch = input.value;
            currentPage = '1';
            filterVaccinesSpa();
        }
    }, 300);
}

function setAgeGroupFilter(age, event) {
    if (event) event.preventDefault();
    currentAgeGroup = age;
    currentPage = '1';

    setActiveFilterRow('ageGroupFilterSelect', age);

    filterVaccinesSpa();
}

function setDiseaseFilter(disease, event) {
    if (event) event.preventDefault();
    currentDisease = disease;
    currentPage = '1';

    setActiveFilterRow('diseaseFilterSelect', disease);

    filterVaccinesSpa();
}

function setVaccineTypeFilter(type, event) {
    if (event) event.preventDefault();
    currentType = type;
    currentPage = '1';

    const btnSingle = document.getElementById('tabBtnSingle');
    const btnPackage = document.getElementById('tabBtnPackage');
    const typeInput = document.getElementById('spaTypeInput');

    if (typeInput) typeInput.value = type;

    if (btnSingle && btnPackage) {
        btnSingle.classList.toggle('active', type === 'single');
        btnPackage.classList.toggle('active', type === 'package');
    }

    const titleEl = document.getElementById('vaccineSectionTitle');
    if (titleEl) {
        const countLabel = document.getElementById('vaccineCountLabel');
        const count = countLabel ? countLabel.textContent : '0';
        titleEl.innerHTML = `${type === 'package' ? 'Danh sách gói vắc xin' : 'Danh sách vắc xin'} <span id="vaccineCountLabel" class="sr-only">${count}</span>`;
    }

    filterVaccinesSpa();
}

function setOriginFilter(origin, event) {
    if (event) event.preventDefault();
    currentOrigin = origin;
    currentPage = '1';

    setActiveFilterRow('originFilterSelect', origin);

    filterVaccinesSpa();
}

function setDosesFilter(doses, event) {
    if (event) event.preventDefault();
    currentDoses = doses;
    currentPage = '1';

    setActiveFilterRow('dosesFilterSelect', doses);

    filterVaccinesSpa();
}

function setSortFilter(sort, event) {
    if (event) event.preventDefault();
    currentSort = sort || 'popular';
    currentPage = '1';

    document.querySelectorAll('#sortPillGroup .sort-pill').forEach((button) => {
        button.classList.toggle('active', button.dataset.sort === currentSort);
    });

    filterVaccinesSpa();
}

function resetVaccineFilters(event) {
    if (event) event.preventDefault();
    currentSearch = '';
    currentAgeGroup = '';
    currentDisease = '';
    currentOrigin = '';
    currentDoses = '';
    currentSort = 'popular';
    currentType = '';
    currentPage = '1';

    const searchInput = document.getElementById('spaSearchInput');
    if (searchInput) searchInput.value = '';

    setActiveFilterRow('ageGroupFilterSelect', '');
    setActiveFilterRow('diseaseFilterSelect', '');
    setActiveFilterRow('originFilterSelect', '');
    setActiveFilterRow('dosesFilterSelect', '');

    document.querySelectorAll('#sortPillGroup .sort-pill').forEach((button) => {
        button.classList.toggle('active', button.dataset.sort === 'popular');
    });

    const btnClear = document.getElementById('btnClearFilters');
    if (btnClear) btnClear.style.display = 'none';

    filterVaccinesSpa();
}

async function filterVaccinesSpa(event, page = null) {
    if (event) event.preventDefault();
    if (page) currentPage = page;
    if (event && event.type === 'submit') currentPage = '1';

    const container = document.getElementById('vaccineGridContainer');
    if (!container) return;

    const searchInput = document.getElementById('spaSearchInput');
    if (searchInput) currentSearch = searchInput.value;

    const params = new URLSearchParams();
    if (currentSearch) params.append('search', currentSearch);
    if (currentDisease) params.append('disease', currentDisease);
    if (currentAgeGroup) params.append('age_group', currentAgeGroup);
    if (currentOrigin) params.append('origin', currentOrigin);
    if (currentDoses) params.append('doses', currentDoses);
    if (currentType) params.append('type', currentType);
    if (currentSort && currentSort !== 'popular') params.append('sort', currentSort);
    if (currentPage && currentPage !== '1') params.append('page', currentPage);

    const btnClear = document.getElementById('btnClearFilters');
    if (btnClear) {
        if (currentSearch || currentDisease || currentAgeGroup || currentOrigin || currentDoses || currentSort !== 'popular' || currentType) {
            btnClear.style.display = 'inline-flex';
        } else {
            btnClear.style.display = 'none';
        }
    }

    container.style.opacity = '0.4';
    container.style.transition = 'opacity 0.2s ease';

    try {
        const queryString = params.toString();
        const url = queryString ? `/vaccines?${queryString}` : '/vaccines';
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

document.addEventListener('click', (event) => {
    const link = event.target.closest('.catalog-pagination a');
    if (!link) return;

    event.preventDefault();
    const url = new URL(link.href);
    filterVaccinesSpa(event, url.searchParams.get('page') || '1');
});

function setActiveFilterRow(containerId, value) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.querySelectorAll('.lc-check-row').forEach((row) => {
        row.classList.toggle('active', row.dataset.value === value);
    });
}

function toggleCatalogFilterGroup(button) {
    const group = button.closest('.lc-filter-group');
    if (!group) return;

    group.classList.toggle('open');
    const icon = button.querySelector('i');
    if (icon) icon.setAttribute('data-lucide', group.classList.contains('open') ? 'chevron-up' : 'chevron-down');
    lucide.createIcons();
}

function filterOriginOptions(keyword) {
    const normalizedKeyword = keyword.trim().toLowerCase();
    document.querySelectorAll('#originFilterSelect .origin-option').forEach((option) => {
        option.style.display = option.textContent.toLowerCase().includes(normalizedKeyword) ? 'flex' : 'none';
    });
}

function filterDiseaseOptions(keyword) {
    const normalizedKeyword = keyword.trim().toLowerCase();
    document.querySelectorAll('#diseaseFilterSelect .disease-option').forEach((option) => {
        option.style.display = option.textContent.toLowerCase().includes(normalizedKeyword) ? 'flex' : 'none';
    });
}

// ==========================================================================
// SPA CONSULTATION FORM
// ==========================================================================
function renderSpaConsultForm() {
    const body = document.getElementById('spaRegisterBody');
    if (!body) return;

    const centers = window.lastFetchCenters || [];
    const centerOptions = centers.map(c => `
        <option value="${c.name}">${c.name} — ${c.address}</option>
    `).join('') || `
        <option value="Medicare Chi nhánh 1: Cờ Đỏ">Medicare Chi nhánh 1: Cờ Đỏ — Cờ Đỏ, Cần Thơ</option>
        <option value="Medicare Chi nhánh 2: Thới Lai">Medicare Chi nhánh 2: Thới Lai — Thới Lai, Cần Thơ</option>
    `;

    body.innerHTML = `
        <div style="padding: 24px; max-width: 500px; margin: 0 auto;">
            <h3 style="font-size: 20px; font-weight: 800; color: var(--text-primary, #1e293b); margin-bottom: 8px; text-align: center;">Yêu cầu tư vấn <span style="color: var(--primary-color, #c8102e);">miễn phí</span></h3>
            <p style="font-size: 14px; color: var(--text-muted, #64748b); text-align: center; margin-bottom: 24px; line-height:1.5;">Medicare sẽ liên hệ lại ngay để tư vấn phác đồ tiêm chủng vắc xin thích hợp nhất cho bạn.</p>
            
            <form id="spaConsultForm" onsubmit="submitSpaConsult(event)">
                <div class="form-group" style="margin-bottom: 16px; display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13.5px; font-weight: 600; color: #334155; text-align: left;">Hình thức tư vấn <span style="color:#ef4444;">*</span></label>
                    <div class="consult-type-toggle" style="display: inline-flex; width: 100%; background: #f1f5f9; border-radius: 30px; padding: 4px; border: 1px solid #cbd5e1; margin-top: 6px; box-sizing: border-box;">
                        <button type="button" id="btnSpaConsultOnline" onclick="setSpaConsultType('online')" style="flex: 1; border: none; padding: 10px 12px; border-radius: 26px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s; background: var(--primary-color, #c8102e); color: #ffffff; text-align: center;">
                            Tư vấn qua điện thoại (Online)
                        </button>
                        <button type="button" id="btnSpaConsultOffline" onclick="setSpaConsultType('offline')" style="flex: 1; border: none; padding: 10px 12px; border-radius: 26px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s; background: transparent; color: #475569; text-align: center;">
                            Tư vấn tại trung tâm (Offline)
                        </button>
                        <input type="hidden" name="consultType" id="spaConsultTypeValue" value="online">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 16px; display: flex; flex-direction: column; gap: 6px;">
                    <label for="consultName" style="font-size: 13.5px; font-weight: 600; color: #334155; text-align: left;">Họ tên người liên hệ <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="consultName" name="customerName" placeholder="Nhập họ tên của bạn" required style="width:100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline:none;">
                    <span class="error-msg" id="err_spa_customerName" style="color: #ef4444; font-size: 12px; display:none; text-align:left;"></span>
                </div>
                
                <div class="form-group" style="margin-bottom: 16px; display: flex; flex-direction: column; gap: 6px;">
                    <label for="consultPhone" style="font-size: 13.5px; font-weight: 600; color: #334155; text-align: left;">Số điện thoại liên hệ <span style="color:#ef4444;">*</span></label>
                    <input type="tel" id="consultPhone" name="customerPhone" placeholder="Ví dụ: 0912345678" required style="width:100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline:none;">
                    <span class="error-msg" id="err_spa_customerPhone" style="color: #ef4444; font-size: 12px; display:none; text-align:left;"></span>
                </div>
                
                <div class="form-group" id="spaCenterSelectGroup" style="margin-bottom: 16px; display: none; flex-direction: column; gap: 6px;">
                    <label for="consultCenter" style="font-size: 13.5px; font-weight: 600; color: #334155; text-align: left;">Chi nhánh tư vấn gần nhất <span style="color:#ef4444;">*</span></label>
                    <select id="consultCenter" name="centerName" style="width:100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline:none; background: #fff; height:42px;">
                        <option value="">-- Chọn trung tâm Medicare --</option>
                        ${centerOptions}
                    </select>
                    <span class="error-msg" id="err_spa_centerName" style="color: #ef4444; font-size: 12px; display:none; text-align:left;"></span>
                </div>
                
                <div class="form-group" style="margin-bottom: 20px; display: flex; flex-direction: column; gap: 6px;">
                    <label for="consultNote" style="font-size: 13.5px; font-weight: 600; color: #334155; text-align: left;">Ghi chú yêu cầu tư vấn</label>
                    <textarea id="consultNote" name="customerNote" rows="3" placeholder="Nhập vắc xin bạn muốn tiêm hoặc câu hỏi cần tư vấn..." style="width:100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline:none; font-family:inherit;"></textarea>
                    <span class="error-msg" id="err_spa_customerNote" style="color: #ef4444; font-size: 12px; display:none; text-align:left;"></span>
                </div>
                
                <div style="display:flex; gap:12px;">
                    <button type="button" onclick="openSpaRegisterModal(null)" class="btn-secondary" style="flex:1; padding: 12px; border-radius: 8px; font-weight: 700; cursor: pointer; background:#f1f5f9; border:1px solid #cbd5e1; color:#475569;">Quay lại</button>
                    <button type="submit" class="btn-primary" id="btnSubmitSpaConsult" style="flex:2; background: var(--primary-color, #c8102e); color: #fff; border: none; padding: 12px; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <i data-lucide="send" style="width: 16px; height: 16px;"></i> Gửi yêu cầu
                    </button>
                </div>
            </form>
        </div>
    `;
    lucide.createIcons();
}

async function submitSpaConsult(event) {
    if (event) event.preventDefault();
    if (!confirm('Bạn có thực sự muốn gửi yêu cầu tư vấn tiêm chủng này không?')) {
        return;
    }

    const form = document.getElementById('spaConsultForm');
    const submitBtn = document.getElementById('btnSubmitSpaConsult');
    const body = document.getElementById('spaRegisterBody');

    // Reset errors
    document.querySelectorAll('.error-msg').forEach(el => {
        el.textContent = '';
        el.style.display = 'none';
    });

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-lucide="loader-2" style="width: 16px; height: 16px; animation: spin 1s linear infinite;"></i> Đang gửi...';
    lucide.createIcons();

    const formData = new FormData(form);
    
    // Gửi yêu cầu tư vấn với tên bệnh mặc định là "Tư vấn chung"
    const url = '/vaccines/disease/T%C6%B0%20v%E1%BA%A5n%20chung/consult';

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json();

        if (response.status === 422) {
            // Validation error
            Object.entries(data.errors).forEach(([field, messages]) => {
                const errEl = document.getElementById('err_spa_' + field);
                if (errEl) {
                    errEl.textContent = messages[0];
                    errEl.style.display = 'block';
                }
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i data-lucide="send" style="width: 16px; height: 16px;"></i> Gửi yêu cầu';
            lucide.createIcons();
        } else if (!response.ok) {
            throw new Error(data.message || 'Lỗi hệ thống');
        } else {
            // Success
            body.innerHTML = `
                <div style="text-align: center; padding: 40px 24px;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
                        <i data-lucide="check-circle-2" style="width: 32px; height: 32px;"></i>
                    </div>
                    <h3 style="font-size: 20px; font-weight: 800; color: #065f46; margin-bottom: 10px;">Gửi yêu cầu thành công</h3>
                    <p style="font-size: 14.5px; color: #1e293b; font-weight: 700; margin-bottom: 8px;">Mã tư vấn: ${data.registration_code}</p>
                    <p style="color: #4b5563; line-height: 1.6; margin-bottom: 24px; max-width: 360px; margin-left: auto; margin-right: auto;">${data.message}</p>
                    <button onclick="closeSpaRegisterModal()" class="btn-primary" style="background: var(--primary-color, #c8102e); color: #fff; border: none; padding: 10px 32px; border-radius: 8px; font-weight: 700; cursor: pointer;">Đóng lại</button>
                </div>
            `;
            lucide.createIcons();
        }
    } catch (e) {
        alert('Có lỗi xảy ra: ' + e.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i data-lucide="send" style="width: 16px; height: 16px;"></i> Gửi yêu cầu';
        lucide.createIcons();
    }
}

function setSpaConsultType(value) {
    const input = document.getElementById('spaConsultTypeValue');
    const btnOnline = document.getElementById('btnSpaConsultOnline');
    const btnOffline = document.getElementById('btnSpaConsultOffline');
    const group = document.getElementById('spaCenterSelectGroup');
    const select = document.getElementById('consultCenter');
    
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

// Automatic Dynamic Table of Contents (TOC) & Smooth Scroll Highlight Generator
document.addEventListener('DOMContentLoaded', function() {
    initDynamicTOC();
});

function initDynamicTOC() {
    const targetNav = document.getElementById('autoTocNav') || document.getElementById('vaccineTocNav');
    const tocWidget = document.getElementById('autoTocWidget');

    if (!targetNav) return;

    // Query ONLY headings inside the actual article body text or vaccine sections
    const headings = document.querySelectorAll('.article-main-content .article-body-content h2, .article-main-content .article-body-content h3, .article-main-content section h2, .article-main-content section h3');
    
    if (!headings || headings.length === 0) {
        if (tocWidget) tocWidget.style.display = 'none';
        return;
    }

    if (tocWidget) tocWidget.style.display = 'block';

    targetNav.innerHTML = '';
    const tocItems = [];

    headings.forEach((heading, index) => {
        if (!heading.id) {
            heading.id = 'heading-toc-' + index;
        }
        const link = document.createElement('a');
        link.href = '#' + heading.id;
        link.className = 'toc-link-item' + (index === 0 ? ' active' : '');
        link.style.display = 'flex';
        link.style.alignItems = 'center';
        link.style.gap = '6px';
        link.innerHTML = `<i data-lucide="chevron-right" style="width: 14px; height: 14px; flex-shrink: 0;"></i> <span>${heading.textContent.trim()}</span>`;
        
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetEl = document.getElementById(heading.id);
            if (targetEl) {
                const yOffset = -110;
                const y = targetEl.getBoundingClientRect().top + window.pageYOffset + yOffset;
                window.scrollTo({ top: y, behavior: 'smooth' });
            }
        });

        targetNav.appendChild(link);
        tocItems.push({ heading, link });
    });

    if (window.lucide) {
        lucide.createIcons();
    }

    // Scroll active link observer
    const observerOptions = {
        root: null,
        rootMargin: '-100px 0px -65% 0px',
        threshold: 0
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                tocItems.forEach(item => {
                    if (item.heading === entry.target) {
                        item.link.classList.add('active');
                    } else {
                        item.link.classList.remove('active');
                    }
                });
            }
        });
    }, observerOptions);

    headings.forEach(heading => observer.observe(heading));
}
