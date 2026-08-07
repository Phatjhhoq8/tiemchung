if (window.__MEDICARE_APP_INITIALIZED__) {
    console.warn('Medicare App JS already initialized.');
} else {
    window.__MEDICARE_APP_INITIALIZED__ = true;

if (typeof window.lucide === 'undefined') {
    window.lucide = {
        createIcons: () => console.warn('Thư viện Lucide không được tải thành công từ CDN.')
    };
}

const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const getAbsoluteUrl = (path) => {
    const base = window.Laravel?.baseUrl || '';
    return base + (base && !path.startsWith('/') ? '/' : '') + path;
};
const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
}[character]));
const formatCurrency = (value) => `${new Intl.NumberFormat('vi-VN').format(Number(value) || 0)} đ`;
const renderIcons = () => window.lucide?.createIcons();

function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    const colors = { success: '#10b981', error: '#ef4444', info: '#3b82f6' };
    const icons = { success: 'check-circle', error: 'alert-circle', info: 'info' };
    toast.className = `toast-item toast-${type}`;
    toast.style.cssText = `background:${colors[type] || colors.info};color:#fff;padding:12px 20px;border-radius:10px;box-shadow:0 10px 25px rgba(0,0,0,.18);display:flex;align-items:center;gap:10px;font-size:14px;font-weight:600;pointer-events:auto;animation:toastSlideIn .3s cubic-bezier(.16,1,.3,1);transition:all .3s ease;`;
    toast.innerHTML = `<i data-lucide="${icons[type] || icons.info}" style="width:20px;height:20px;flex-shrink:0;"></i><span style="flex-grow:1;"></span><button type="button" aria-label="Đóng thông báo" style="background:none;border:0;color:#fff;cursor:pointer;padding:0;"><i data-lucide="x" style="width:16px;height:16px;"></i></button>`;
    toast.querySelector('span').textContent = message;
    toast.querySelector('button').addEventListener('click', () => toast.remove());
    container.appendChild(toast);
    renderIcons();

    window.setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(50px)';
        window.setTimeout(() => toast.remove(), 300);
    }, 4000);
}

function showConfirmDialog({ title, message, confirmText = 'Đồng ý', cancelText = 'Hủy bỏ', onConfirm, onCancel }) {
    const localEscapeHtml = (str) => String(str ?? '').replace(/[&<>"']/g, (c) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[c]));

    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 9999999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.25s ease;
    `;

    const dialog = document.createElement('div');
    dialog.style.cssText = `
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        padding: 24px;
        width: 90%;
        max-width: 420px;
        transform: scale(0.9);
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 16px;
    `;

    dialog.innerHTML = `
        <div style="width: 56px; height: 56px; background: #fee2e2; color: var(--primary-color, #c8102e); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        </div>
        <div>
            <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 800; color: #1e293b; text-align: center;">${localEscapeHtml(title)}</h3>
            <p style="margin: 0; font-size: 14.5px; color: #64748b; line-height: 1.5; text-align: center;">${localEscapeHtml(message)}</p>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 8px;">
            <button type="button" class="dialog-cancel-btn" style="background: #f1f5f9; color: #475569; border: none; padding: 12px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px; transition: all 0.2s;">
                ${localEscapeHtml(cancelText)}
            </button>
            <button type="button" class="dialog-confirm-btn" style="background: var(--primary-color, #c8102e); color: #fff; border: none; padding: 12px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px; transition: all 0.2s;">
                ${localEscapeHtml(confirmText)}
            </button>
        </div>
    `;

    overlay.appendChild(dialog);
    document.body.appendChild(overlay);

    setTimeout(() => {
        overlay.style.opacity = '1';
        dialog.style.transform = 'scale(1)';
    }, 10);

    const closeDialog = () => {
        overlay.style.opacity = '0';
        dialog.style.transform = 'scale(0.9)';
        setTimeout(() => overlay.remove(), 250);
    };

    overlay.querySelector('.dialog-cancel-btn').addEventListener('click', () => {
        closeDialog();
        if (onCancel) onCancel();
    });

    overlay.querySelector('.dialog-confirm-btn').addEventListener('click', () => {
        closeDialog();
        if (onConfirm) onConfirm();
    });
}

function toggleCartDrawer() {
    document.getElementById('floatingCart')?.classList.toggle('expanded');
}

function toggleHeaderCartDropdown(event) {
    event?.stopPropagation();
    document.getElementById('headerCartDropdown')?.classList.toggle('hidden');
}

document.addEventListener('click', (event) => {
    const wrapper = document.getElementById('headerCartWrapper');
    const dropdown = document.getElementById('headerCartDropdown');
    if (dropdown && wrapper && !wrapper.contains(event.target)) dropdown.classList.add('hidden');
});

function setCartSelection(vaccineId, inCart) {
    const id = Number(vaccineId);
    document.querySelectorAll(`.vaccine-card[data-id="${id}"], .catalog-product-card[data-id="${id}"]`).forEach((card) => {
        card.classList.toggle('selected', inCart);
        const button = card.querySelector('.btn-select-vaccine');
        if (!button) return;

        button.classList.toggle('btn-selected', inCart);
        button.innerHTML = inCart
            ? '<i data-lucide="x"></i><span>Hủy chọn</span>'
            : '<i data-lucide="plus"></i><span>Chọn tiêm</span>';
    });

    document.querySelectorAll(`.btn-select-detail[data-id="${id}"], #modalSelectBtn_${id}`).forEach((button) => {
        button.classList.toggle('btn-selected', inCart);
        button.style.backgroundColor = inCart ? '#fff1f2' : 'var(--primary-color, #c8102e)';
        button.style.borderColor = inCart ? '#fecdd3' : 'var(--primary-color, #c8102e)';
        button.style.color = inCart ? 'var(--primary-color, #c8102e)' : '#ffffff';
        button.innerHTML = inCart
            ? '<i data-lucide="x"></i><span>Hủy chọn</span>'
            : '<i data-lucide="plus"></i><span>Chọn vắc xin này</span>';
    });
}

async function toggleCart(vaccineId, forceRemove = false) {
    const id = Number(vaccineId);
    if (!Number.isSafeInteger(id) || id < 1) return;

    const cards = document.querySelectorAll(`.vaccine-card[data-id="${id}"], .catalog-product-card[data-id="${id}"]`);
    const selected = forceRemove || cards[0]?.classList.contains('selected') || document.getElementById(`modalSelectBtn_${id}`)?.classList.contains('btn-selected');

    try {
        const response = await fetch(getAbsoluteUrl(selected ? '/cart/remove' : '/cart/add'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ vaccine_id: id })
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) throw new Error(data.message || 'Giao tiếp máy chủ thất bại.');

        const inCart = Boolean(data.cart?.[id]);
        setCartSelection(id, inCart);
        updateFloatingCart(data.cart, data.cart_count, data.total_price);
        renderIcons();
        showToast(inCart ? 'Đã thêm vắc xin vào danh sách tiêm.' : 'Đã xóa vắc xin khỏi danh sách tiêm.', 'success');
    } catch (error) {
        console.error('Lỗi giỏ hàng:', error);
        showToast(error.message || 'Có lỗi xảy ra khi cập nhật giỏ hàng. Vui lòng thử lại.', 'error');
    }
}

function updateFloatingCart(cart, count, totalPrice) {
    const normalizedCart = cart || {};
    const itemCount = Number(count) || 0;
    document.getElementById('headerCartBtn')?.classList.remove('hidden');
    document.getElementById('cartCount')?.replaceChildren(document.createTextNode(itemCount));
    document.getElementById('drawerTotalPrice')?.replaceChildren(document.createTextNode(formatCurrency(totalPrice)));

    if (itemCount === 0) document.getElementById('headerCartDropdown')?.classList.add('hidden');

    const cartList = document.getElementById('cartItemsList');
    if (!cartList) return;

    const entries = Object.entries(normalizedCart);
    if (!entries.length) {
        cartList.innerHTML = '<div style="text-align:center;padding:24px 12px;color:#94a3b8;font-size:13.5px;"><i data-lucide="shopping-cart" style="width:32px;height:32px;margin-bottom:8px;opacity:.5;"></i><p style="margin:0;">Chưa có vắc xin nào trong danh sách tiêm</p></div>';
        renderIcons();
        return;
    }

    cartList.innerHTML = entries.map(([id, item]) => `
        <div class="cart-item-row" data-id="${Number(id)}">
            <div class="cart-item-info">
                <strong class="cart-item-name">${escapeHtml(item.name)}</strong>
                <div style="display:flex;gap:8px;align-items:center;margin-top:4px;">
                    <span class="cart-item-price" style="font-weight:700;color:var(--primary-color);font-size:13.5px;">${formatCurrency(item.price)}</span>
                    <span style="font-size:12px;color:#64748b;background:#f1f5f9;padding:2px 8px;border-radius:4px;font-weight:600;">SL: ${Number(item.quantity) || 1}</span>
                </div>
                ${item.unavailable_for_center ? '<div style="margin-top:6px;color:#b91c1c;font-size:12px;font-weight:700;">Sản phẩm này không có ở chi nhánh hiện tại</div>' : ''}
            </div>
            <button type="button" onclick="toggleCart(${Number(id)}, true)" class="cart-item-remove" title="Xóa vắc xin"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
        </div>
    `).join('');
    renderIcons();
}

async function clearCartUI() {
    try {
        const response = await fetch(getAbsoluteUrl('/cart/clear'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            }
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) throw new Error(data.message || 'Không thể xóa danh sách tiêm.');

        document.querySelectorAll('.vaccine-card[data-id], .catalog-product-card[data-id]').forEach((card) => setCartSelection(card.dataset.id, false));
        updateFloatingCart({}, 0, 0);
        renderIcons();
        showToast('Đã xóa sạch danh sách tiêm.', 'info');
    } catch (error) {
        console.error('Lỗi xóa giỏ hàng:', error);
        showToast(error.message || 'Có lỗi xảy ra khi xóa danh sách tiêm.', 'error');
    }
}

async function openVaccineDetailModal(vaccineId, event) {
    event?.preventDefault();
    const id = Number(vaccineId);
    const modal = document.getElementById('vaccineDetailModal');
    const content = document.getElementById('modalDetailContent');
    if (!Number.isSafeInteger(id) || !modal || !content) return;

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    content.innerHTML = '<div style="padding:60px;text-align:center;"><i data-lucide="loader-2" style="width:40px;height:40px;color:var(--primary-color, #c8102e);animation:spin 1s linear infinite;"></i><p style="margin-top:12px;color:#64748b;font-weight:500;">Đang tải thông tin vắc xin...</p></div>';
    renderIcons();

    try {
        const response = await fetch(getAbsoluteUrl(`/vaccines/${id}`), {
            headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' }
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) throw new Error('Không thể nạp dữ liệu vắc xin.');

        const vaccine = data.vaccine;
        const views = document.getElementById(`vaccine-views-count-${vaccine.id}`);
        if (views) views.textContent = Number(vaccine.views || 0).toLocaleString('vi-VN');

        content.innerHTML = `
            <div style="display:flex;flex-wrap:wrap;overflow:hidden;border-radius:16px;">
                <div style="flex:1 1 300px;background:#f8fafc;display:flex;align-items:center;justify-content:center;position:relative;min-height:280px;padding:20px;">
                    <img src="${escapeHtml(vaccine.image)}" alt="${escapeHtml(vaccine.name)}" style="max-width:100%;max-height:260px;object-fit:contain;border-radius:12px;">
                    <span style="position:absolute;top:16px;left:16px;background:${vaccine.type === 'package' ? '#0284c7' : '#c8102e'};color:#fff;padding:4px 12px;border-radius:12px;font-size:11px;font-weight:700;text-transform:uppercase;">${escapeHtml(vaccine.type_label)}</span>
                </div>
                <div style="flex:1 1 400px;padding:32px;display:flex;flex-direction:column;justify-content:space-between;">
                    <div>
                        <span style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;">Xuất xứ: ${escapeHtml(vaccine.origin)}</span>
                        <h2 style="font-size:24px;font-weight:800;color:#1e293b;margin:6px 0 12px;line-height:1.3;">${escapeHtml(vaccine.name)}</h2>
                        <p style="font-size:14px;color:#475569;line-height:1.6;margin-bottom:20px;">${escapeHtml(vaccine.description || 'Vắc xin an toàn, đã được kiểm định nghiêm ngặt theo tiêu chuẩn của Bộ Y Tế.')}</p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;background:#f8fafc;padding:16px;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:20px;">
                            <div><span style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;">Phòng bệnh</span><strong style="display:block;font-size:13.5px;color:#1e293b;margin-top:2px;">${escapeHtml(vaccine.disease_prevention)}</strong></div>
                            <div><span style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;">Độ tuổi chỉ định</span><strong style="display:block;font-size:13.5px;color:#1e293b;margin-top:2px;">${escapeHtml(vaccine.age_group)}</strong></div>
                            <div><span style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;">Phác đồ tiêm</span><strong style="display:block;font-size:13.5px;color:#1e293b;margin-top:2px;">${escapeHtml(vaccine.doses)} mũi tiêm</strong></div>
                            <div><span style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;">Nhà sản xuất</span><strong style="display:block;font-size:13.5px;color:#1e293b;margin-top:2px;">${escapeHtml(vaccine.manufacturer || vaccine.origin)}</strong></div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;border-top:1px solid #e2e8f0;padding-top:16px;gap:16px;">
                        <div><span style="font-size:12px;color:#64748b;display:block;">Giá tiêm niêm yết:</span><strong style="font-size:22px;font-weight:800;color:var(--primary-color, #c8102e);">${escapeHtml(vaccine.formatted_price)}</strong></div>
                        <button id="modalSelectBtn_${vaccine.id}" onclick="toggleCart(${vaccine.id})" class="btn-select-detail ${vaccine.is_in_cart ? 'btn-selected' : ''}" style="padding:12px 22px;border-radius:10px;border:1px solid ${vaccine.is_in_cart ? '#fecdd3' : 'var(--primary-color, #c8102e)'};color:${vaccine.is_in_cart ? 'var(--primary-color, #c8102e)' : '#fff'};font-weight:700;font-size:14px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;background:${vaccine.is_in_cart ? '#fff1f2' : 'var(--primary-color, #c8102e)'};"><i data-lucide="${vaccine.is_in_cart ? 'x' : 'plus'}" style="width:18px;height:18px;"></i><span>${vaccine.is_in_cart ? 'Hủy chọn' : 'Chọn vắc xin này'}</span></button>
                    </div>
                </div>
            </div>`;
        renderIcons();
    } catch (error) {
        console.error('Lỗi tải chi tiết vắc xin:', error);
        content.innerHTML = '<div style="padding:40px;text-align:center;color:#ef4444;">Không thể tải dữ liệu vắc xin. Vui lòng thử lại.</div>';
    }
}

function closeVaccineDetailModal() {
    const modal = document.getElementById('vaccineDetailModal');
    if (!modal) return;
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeVaccineDetailModal();
});

let filterTimer;
let filterRequest;
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
    window.clearTimeout(filterTimer);
    filterTimer = window.setTimeout(() => {
        currentSearch = document.getElementById('spaSearchInput')?.value || '';
        currentPage = '1';
        filterVaccinesSpa();
    }, 300);
}

function setActiveFilterRow(containerId, value) {
    document.getElementById(containerId)?.querySelectorAll('.lc-check-row').forEach((row) => {
        row.classList.toggle('active', row.dataset.value === String(value));
    });
}

function setAgeGroupFilter(age, event) {
    event?.preventDefault();
    currentAgeGroup = age;
    currentPage = '1';
    setActiveFilterRow('ageGroupFilterSelect', age);
    filterVaccinesSpa();
}

function setDiseaseFilter(disease, event) {
    event?.preventDefault();
    currentDisease = disease;
    currentPage = '1';
    setActiveFilterRow('diseaseFilterSelect', disease);
    filterVaccinesSpa();
}

function setOriginFilter(origin, event) {
    event?.preventDefault();
    currentOrigin = origin;
    currentPage = '1';
    setActiveFilterRow('originFilterSelect', origin);
    filterVaccinesSpa();
}

function setDosesFilter(doses, event) {
    event?.preventDefault();
    currentDoses = doses;
    currentPage = '1';
    setActiveFilterRow('dosesFilterSelect', doses);
    filterVaccinesSpa();
}

function setVaccineTypeFilter(type, event) {
    event?.preventDefault();
    currentType = type;
    currentPage = '1';
    document.querySelectorAll('#tabBtnSingle, #tabBtnPackage').forEach((button) => {
        button.classList.toggle('active', button.id === (type === 'single' ? 'tabBtnSingle' : 'tabBtnPackage'));
    });
    filterVaccinesSpa();
}

function setSortFilter(sort, event) {
    event?.preventDefault();
    currentSort = sort || 'popular';
    currentPage = '1';
    document.querySelectorAll('#sortPillGroup .sort-pill').forEach((button) => {
        button.classList.toggle('active', button.dataset.sort === currentSort);
    });
    filterVaccinesSpa();
}

function resetVaccineFilters(event) {
    event?.preventDefault();
    currentSearch = '';
    currentAgeGroup = '';
    currentDisease = '';
    currentOrigin = '';
    currentDoses = '';
    currentSort = 'popular';
    currentType = '';
    currentPage = '1';
    const search = document.getElementById('spaSearchInput');
    if (search) search.value = '';
    ['ageGroupFilterSelect', 'diseaseFilterSelect', 'originFilterSelect', 'dosesFilterSelect'].forEach((id) => setActiveFilterRow(id, ''));
    document.querySelectorAll('#sortPillGroup .sort-pill').forEach((button) => button.classList.toggle('active', button.dataset.sort === 'popular'));
    filterVaccinesSpa();
}

async function filterVaccinesSpa(event, page = null) {
    event?.preventDefault();
    if (page) currentPage = page;
    if (event?.type === 'submit') currentPage = '1';

    const container = document.getElementById('vaccineGridContainer');
    if (!container) return;
    currentSearch = document.getElementById('spaSearchInput')?.value || currentSearch;

    const params = new URLSearchParams();
    if (currentSearch) params.set('search', currentSearch);
    if (currentDisease) params.set('disease', currentDisease);
    if (currentAgeGroup) params.set('age_group', currentAgeGroup);
    if (currentOrigin) params.set('origin', currentOrigin);
    if (currentDoses) params.set('doses', currentDoses);
    if (currentType) params.set('type', currentType);
    if (currentSort !== 'popular') params.set('sort', currentSort);
    if (currentPage !== '1') params.set('page', currentPage);

    const hasFilters = [...params.keys()].length > 0;
    const clearButton = document.getElementById('btnClearFilters');
    if (clearButton) clearButton.style.display = hasFilters ? 'inline-flex' : 'none';

    filterRequest?.abort();
    const request = new AbortController();
    filterRequest = request;
    const path = params.toString() ? `/vaccines?${params}` : '/vaccines';
    container.style.opacity = '.4';
    container.style.transition = 'opacity .2s ease';

    try {
        const response = await fetch(getAbsoluteUrl(path), {
            signal: request.signal,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-Vaccine-Filter': 'true', 'Accept': 'application/json' }
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) throw new Error('Lọc vắc xin thất bại.');
        if (filterRequest !== request) return;

        container.innerHTML = data.html;
        document.getElementById('vaccineCountLabel')?.replaceChildren(document.createTextNode(data.count));
        window.history.pushState({}, '', getAbsoluteUrl(path));
        renderIcons();
    } catch (error) {
        if (error.name !== 'AbortError') console.error('Lỗi lọc vắc xin:', error);
    } finally {
        if (filterRequest === request) container.style.opacity = '1';
    }
}

document.addEventListener('click', (event) => {
    const link = event.target.closest('.catalog-pagination a');
    if (!link) return;
    event.preventDefault();
    filterVaccinesSpa(event, new URL(link.href).searchParams.get('page') || '1');
});

function toggleCatalogFilterGroup(button) {
    const group = button.closest('.lc-filter-group');
    if (!group) return;
    group.classList.toggle('open');
    const icon = button.querySelector('i, svg');
    if (icon) {
        const isNewOpen = group.classList.contains('open');
        const newIcon = document.createElement('i');
        newIcon.setAttribute('data-lucide', isNewOpen ? 'chevron-up' : 'chevron-down');
        icon.parentNode.replaceChild(newIcon, icon);
    }
    renderIcons();
}

function filterOriginOptions(keyword) {
    const normalized = (keyword || '').trim().toLowerCase();
    document.querySelectorAll('#originFilterSelect .origin-option').forEach((option) => {
        option.style.display = option.textContent.toLowerCase().includes(normalized) ? 'flex' : 'none';
    });
}

function filterDiseaseOptions(keyword) {
    const normalized = (keyword || '').trim().toLowerCase();
    document.querySelectorAll('#diseaseFilterSelect .disease-option').forEach((option) => {
        option.style.display = option.textContent.toLowerCase().includes(normalized) ? 'flex' : 'none';
    });
}

function toggleMobileFilterBottomSheet(show) {
    const overlay = document.getElementById('mobileFilterOverlay');
    const sheet = document.getElementById('mobileFilterBottomSheet');
    if (!overlay || !sheet) return;
    if (show) {
        overlay.classList.add('open');
        sheet.classList.add('open');
        document.body.style.overflow = 'hidden';
    } else {
        overlay.classList.remove('open');
        sheet.classList.remove('open');
        document.body.style.overflow = '';
    }
}

function openBranchModal() {
    const overlay = document.getElementById('mobileBranchModalOverlay');
    const modal = document.getElementById('mobileBranchModal');
    if (!overlay || !modal) return;
    overlay.classList.add('open');
    modal.classList.add('open');
}

function closeBranchModal() {
    const overlay = document.getElementById('mobileBranchModalOverlay');
    const modal = document.getElementById('mobileBranchModal');
    if (!overlay || !modal) return;
    overlay.classList.remove('open');
    modal.classList.remove('open');
}

function toggleCustomDropdown(event, menuId) {
    if (event) event.stopPropagation();
    const menu = document.getElementById(menuId);
    if (!menu) return;
    const isOpen = menu.classList.contains('open');
    closeAllCustomDropdowns();
    if (!isOpen) {
        menu.classList.add('open');
        const trigger = event.currentTarget;
        const icon = trigger ? trigger.querySelector('.dropdown-chevron-icon') : null;
        if (icon) icon.style.transform = 'rotate(180deg)';
    }
}

function closeAllCustomDropdowns() {
    document.querySelectorAll('.custom-dropdown-menu.open').forEach(menu => {
        menu.classList.remove('open');
    });
    document.querySelectorAll('.dropdown-chevron-icon').forEach(icon => {
        icon.style.transform = 'rotate(0deg)';
    });
}

document.addEventListener('click', closeAllCustomDropdowns);

function toggleMobileTocAccordion(event) {
    if (event) event.stopPropagation();
    const nav = document.getElementById('mobileAutoTocNav');
    const icon = document.getElementById('mobileTocChevronIcon');
    if (!nav) return;
    const isHidden = nav.style.display === 'none' || nav.style.display === '' || getComputedStyle(nav).display === 'none';
    if (isHidden) {
        nav.style.display = 'flex';
        nav.style.flexDirection = 'column';
        nav.style.marginTop = '12px';
        nav.style.paddingTop = '10px';
        nav.style.borderTop = '1px dashed #e2e8f0';
        nav.style.gap = '6px';
        if (icon) icon.style.transform = 'rotate(180deg)';
    } else {
        nav.style.display = 'none';
        if (icon) icon.style.transform = 'rotate(0deg)';
    }
}

function initDynamicTOC() {
    const desktopNav = document.getElementById('autoTocNav') || document.getElementById('vaccineTocNav');
    const mobileNav = document.getElementById('mobileAutoTocNav');
    const widget = document.getElementById('autoTocWidget');
    const mobileWidget = document.getElementById('mobileAutoTocWidget');

    if (!desktopNav && !mobileNav) return;

    const headings = document.querySelectorAll('.article-main-content .article-body-content h2, .article-main-content section h2, .vaccine-detail-section h2');
    if (!headings.length) {
        if (widget) widget.style.display = 'none';
        if (mobileWidget) mobileWidget.style.display = 'none';
        return;
    }

    if (widget) widget.style.display = '';
    if (mobileWidget) mobileWidget.style.display = '';

    if (desktopNav) desktopNav.replaceChildren();
    if (mobileNav) mobileNav.replaceChildren();

    const items = [];
    headings.forEach((heading, index) => {
        heading.id ||= `heading-toc-${index}`;

        const createLink = () => {
            const link = document.createElement('a');
            link.href = `#${heading.id}`;
            link.className = `toc-link-item${index === 0 ? ' active' : ''}`;
            link.style.cssText = 'display:flex;align-items:flex-start;gap:8px;';
            link.innerHTML = '<i data-lucide="chevron-right" style="width:14px;height:14px;flex-shrink:0;margin-top:3px;"></i><span></span>';
            link.querySelector('span').textContent = heading.textContent.trim();
            link.addEventListener('click', (event) => {
                event.preventDefault();
                link.blur();
                const targetEl = heading.closest('section') || heading;
                const targetTop = Math.max(0, targetEl.getBoundingClientRect().top + window.pageYOffset - 100);
                window.scrollTo({ top: targetTop, behavior: 'smooth' });
                items.forEach((item) => {
                    if (item.desktopLink) item.desktopLink.classList.toggle('active', item.heading === heading);
                    if (item.mobileLink) item.mobileLink.classList.toggle('active', item.heading === heading);
                });
            });
            return link;
        };

        const desktopLink = desktopNav ? createLink() : null;
        const mobileLink = mobileNav ? createLink() : null;

        if (desktopNav && desktopLink) desktopNav.appendChild(desktopLink);
        if (mobileNav && mobileLink) mobileNav.appendChild(mobileLink);

        items.push({ heading, desktopLink, mobileLink });
    });
    renderIcons();

    let isTicking = false;
    function updateActiveTocOnScroll() {
        const scrollPosition = window.pageYOffset + 140;
        let currentActiveIndex = 0;

        for (let i = 0; i < items.length; i++) {
            const headingTop = items[i].heading.getBoundingClientRect().top + window.pageYOffset;
            if (headingTop <= scrollPosition) {
                currentActiveIndex = i;
            } else {
                break;
            }
        }

        items.forEach((item, idx) => {
            if (item.desktopLink) item.desktopLink.classList.toggle('active', idx === currentActiveIndex);
            if (item.mobileLink) item.mobileLink.classList.toggle('active', idx === currentActiveIndex);
        });

        isTicking = false;
    }

    window.addEventListener('scroll', () => {
        if (!isTicking) {
            window.requestAnimationFrame(updateActiveTocOnScroll);
            isTicking = true;
        }
    }, { passive: true });

    updateActiveTocOnScroll();
}

// ==================== NEWS PAGE SPA FILTERING ====================
let newsFilterTimer;
let newsFilterRequest;
let currentNewsCategory = new URLSearchParams(window.location.search).get('category') || '';
let currentNewsSearch = new URLSearchParams(window.location.search).get('search') || '';
let currentNewsPage = new URLSearchParams(window.location.search).get('page') || '1';

function debouncedFilterNewsSpa() {
    window.clearTimeout(newsFilterTimer);
    newsFilterTimer = window.setTimeout(() => {
        currentNewsSearch = document.getElementById('newsSearchInput')?.value || '';
        currentNewsPage = '1';
        filterNewsSpa();
    }, 300);
}

function filterNewsCategorySpa(category, event) {
    event?.preventDefault();
    currentNewsCategory = category || '';
    currentNewsPage = '1';
    
    document.querySelectorAll('#newsNavTabs .news-nav-tab').forEach((tab) => {
        tab.classList.toggle('active', (tab.dataset.cat || '') === currentNewsCategory);
    });
    document.querySelectorAll('.news-type-cloud .news-cloud-item').forEach((item) => {
        const itemCat = new URL(item.href, window.location.origin).searchParams.get('category') || '';
        item.classList.toggle('active', itemCat === currentNewsCategory);
    });
    
    filterNewsSpa();
}

async function filterNewsSpa(event, page = null) {
    event?.preventDefault();
    if (page) currentNewsPage = page;
    if (event?.type === 'submit') currentNewsPage = '1';

    const container = document.querySelector('.app-main');
    if (!container) return;
    currentNewsSearch = document.getElementById('newsSearchInput')?.value || currentNewsSearch;

    const params = new URLSearchParams();
    if (currentNewsCategory) params.set('category', currentNewsCategory);
    if (currentNewsSearch) params.set('search', currentNewsSearch);
    if (currentNewsPage !== '1') params.set('page', currentNewsPage);

    newsFilterRequest?.abort();
    const request = new AbortController();
    newsFilterRequest = request;
    const path = params.toString() ? `/news?${params}` : '/news';

    const catalogPage = document.getElementById('newsCatalogContainer');
    if (catalogPage) {
        catalogPage.style.opacity = '.4';
        catalogPage.style.transition = 'opacity .2s ease';
    }

    try {
        const response = await fetch(getAbsoluteUrl(path), {
            signal: request.signal,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-SPA-Request': 'true', 'Accept': 'application/json' }
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) throw new Error('Lọc tin tức thất bại.');
        if (newsFilterRequest !== request) return;

        const parser = new DOMParser();
        const doc = parser.parseFromString(data.html, 'text/html');
        const newMain = doc.querySelector('.app-main');

        if (newMain) {
            container.innerHTML = newMain.innerHTML;
            if (data.title) document.title = data.title;
            window.history.pushState({ spa: true, url: path }, '', getAbsoluteUrl(path));
            window.scrollTo({ top: 180, behavior: 'smooth' });
            renderIcons();
            if (typeof AOS !== 'undefined') AOS.init({ once: true, offset: 50 });
            initDynamicTOC();
        }
    } catch (error) {
        if (error.name !== 'AbortError') console.error('Lỗi lọc tin tức:', error);
    } finally {
        if (catalogPage) catalogPage.style.opacity = '1';
    }
}

// News Pagination Click Interceptor
document.addEventListener('click', (event) => {
    const link = event.target.closest('.news-pagination a');
    if (!link) return;
    event.preventDefault();
    const page = new URL(link.href).searchParams.get('page') || '1';
    filterNewsSpa(event, page);
});

// ==================== GLOBAL SPA ROUTER ====================
async function navigateSpa(url, pushState = true) {
    try {
        const targetUrl = new URL(url, window.location.origin);
        const currentUrl = new URL(window.location.href);
        
        if (targetUrl.origin !== window.location.origin || 
            targetUrl.pathname.startsWith('/admin') || 
            targetUrl.pathname.match(/\.(pdf|zip|png|jpg|jpeg|csv|xlsx)$/i) ||
            (targetUrl.hash && targetUrl.pathname === currentUrl.pathname && targetUrl.search === currentUrl.search)) {
            return false;
        }

        const isSamePage = targetUrl.pathname === currentUrl.pathname;
        
        if (isSamePage) {
            // SAME-PAGE TAB / FILTER SWITCH (Zero Flicker Strategy)
            const targetSection = document.getElementById('newsArticlesFeed') || document.getElementById('newsCatalogContainer') || document.getElementById('vaccineGridContainer');
            
            if (targetSection) {
                const targetCat = targetUrl.searchParams.get('category') || '';
                document.querySelectorAll('#newsNavTabs .news-nav-tab').forEach((tab) => {
                    const tabCat = new URL(tab.href, window.location.origin).searchParams.get('category') || '';
                    tab.classList.toggle('active', tabCat === targetCat);
                });
                document.querySelectorAll('.news-type-cloud .news-cloud-item').forEach((item) => {
                    const itemCat = new URL(item.href, window.location.origin).searchParams.get('category') || '';
                    item.classList.toggle('active', itemCat === targetCat);
                });

                const response = await fetch(targetUrl.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-SPA-Request': 'true' }
                });

                if (!response.ok) return false;

                const htmlText = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlText, 'text/html');

                const newSection = doc.getElementById(targetSection.id);
                if (newSection) {
                    targetSection.innerHTML = newSection.innerHTML;
                    
                    const newTitle = doc.querySelector('title')?.innerText;
                    if (newTitle) document.title = newTitle;

                    if (pushState) {
                        window.history.pushState({ spa: true, url: targetUrl.href }, '', targetUrl.href);
                    }

                    renderIcons();
                    return true;
                }
            }
        }

        // CROSS-PAGE NAVIGATION
        const mainContainer = document.querySelector('.app-main');
        if (!mainContainer) return false;

        mainContainer.style.opacity = '0.3';
        mainContainer.style.transition = 'opacity 0.15s ease-out';

        const response = await fetch(targetUrl.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-SPA-Request': 'true'
            }
        });

        if (!response.ok) return false;

        const htmlText = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(htmlText, 'text/html');

        const newMain = doc.querySelector('.app-main');
        if (!newMain) return false;

        const newTitle = doc.querySelector('title')?.innerText;
        if (newTitle) document.title = newTitle;

        mainContainer.innerHTML = newMain.innerHTML;

        // Sync new stylesheets from fetched document to current head
        const newStylesheets = doc.querySelectorAll('link[rel="stylesheet"]');
        newStylesheets.forEach((link) => {
            const href = link.getAttribute('href');
            if (href && !document.querySelector(`link[href="${href}"]`)) {
                const newLink = document.createElement('link');
                newLink.rel = 'stylesheet';
                newLink.href = href;
                document.head.appendChild(newLink);
            }
        });

        if (pushState) {
            window.history.pushState({ spa: true, url: targetUrl.href }, '', targetUrl.href);
        }

        document.querySelectorAll('.nav-menu .nav-link, .mobile-nav-link').forEach((link) => {
            const linkUrl = new URL(link.href, window.location.origin);
            const isActive = linkUrl.pathname === targetUrl.pathname;
            link.classList.toggle('active', isActive);
        });

        window.scrollTo({ top: 0, behavior: 'smooth' });

        renderIcons();
        if (typeof AOS !== 'undefined') {
            try { AOS.init({ once: true, offset: 50 }); } catch (e) {}
        }
        initDynamicTOC();

        return true;
    } catch (error) {
        console.error('SPA Navigation Error:', error);
        return false;
    } finally {
        const mainContainer = document.querySelector('.app-main');
        if (mainContainer) mainContainer.style.opacity = '1';
    }
}

// Global SPA Link Interceptor
document.addEventListener('click', (event) => {
    if (event.target.closest('.catalog-pagination, .news-pagination')) return;

    const link = event.target.closest('a[href]');
    if (!link) return;

    const href = link.getAttribute('href');
    if (!href || 
        href.startsWith('#') || 
        href.startsWith('javascript:') || 
        href.startsWith('tel:') || 
        href.startsWith('mailto:') ||
        link.target === '_blank' ||
        link.dataset.noSpa === 'true') {
        return;
    }

    try {
        const targetUrl = new URL(link.href, window.location.origin);
        
        if (targetUrl.origin === window.location.origin && 
            !targetUrl.pathname.startsWith('/admin') && 
            !targetUrl.pathname.match(/\.(pdf|zip|png|jpg|jpeg|csv|xlsx)$/i) &&
            !(targetUrl.hash && targetUrl.pathname === window.location.pathname)) {
            
            event.preventDefault();
            navigateSpa(targetUrl.href);
        }
    } catch (err) {
        console.error('Lỗi định tuyến SPA link:', err);
    }
});

// ==================== SPA REGISTER MODAL ====================
async function openSpaRegisterModal(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const modal = document.getElementById('spaRegisterModal');
    const body = document.getElementById('spaRegisterBody');
    if (!modal || !body) return;

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    body.innerHTML = `
        <div style="text-align: center; padding: 40px;">
            <i data-lucide="loader-2" style="width: 36px; height: 36px; color: var(--primary-color, #c8102e); animation: spin 1s linear infinite;"></i>
            <p style="margin-top: 12px; color: #64748b;">Đang tải thông tin đặt lịch...</p>
        </div>
    `;
    if (window.lucide) window.lucide.createIcons();

    try {
        const response = await fetch(getAbsoluteUrl('/register'), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        
        if (!response.ok || !data.success) {
            window.lastFetchCenters = data.centers || [];
            window.lastCurrentCenter = data.current_center || null;
            body.innerHTML = `
                <div style="text-align: center; padding: 40px;" data-aos="fade-up">
                    <i data-lucide="help-circle" style="width: 56px; height: 56px; color: var(--secondary-color, #eaaa00); margin: 0 auto 16px auto;"></i>
                    <h3 style="font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 10px; text-align: center; display: block;">Bạn chưa chọn vắc xin nào</h3>
                    <p style="color: #64748b; margin-bottom: 24px; max-width: 400px; margin-left: auto; margin-right: auto; line-height: 1.5; font-size: 14.5px; text-align: center;">Bạn có muốn tham khảo các gói vắc xin ưu đãi thiết kế sẵn hoặc gửi yêu cầu tư vấn để bác sĩ Medicare liên hệ ngay?</p>
                    <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                        <a href="${getAbsoluteUrl('/vaccines')}" class="btn-primary" onclick="closeSpaRegisterModal()" style="background: var(--secondary-color, #eaaa00); color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; min-width: 160px;">Chọn vắc xin</a>
                        <button onclick="renderSpaConsultForm()" class="btn-primary" style="background: var(--primary-color, #c8102e); color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; min-width: 160px;">Yêu cầu tư vấn</button>
                    </div>
                </div>
            `;
            if (window.lucide) window.lucide.createIcons();
            return;
        }

        window.lastFetchCenters = data.centers || [];
        window.lastCartData = data;
        window.lastCurrentCenter = data.current_center || null;
        renderSpaRegisterForm(data);
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

function switchSpaModalTab(tab) {
    if (tab === 'register') {
        if (window.lastCartData) {
            renderSpaRegisterForm(window.lastCartData);
        }
    } else {
        renderSpaConsultForm();
    }
}

function renderSpaConsultForm() {
    const body = document.getElementById('spaRegisterBody');
    if (!body) return;

    const hasCart = window.lastCartData && window.lastCartData.success;
    const tabHtml = hasCart ? `
        <div style="display: flex; gap: 24px; border-bottom: 2px solid #e2e8f0; margin-bottom: 24px;">
            <button type="button" onclick="switchSpaModalTab('register')" style="border: none; background: none; font-size: 15.5px; font-weight: 700; padding: 12px 6px; cursor: pointer; color: #64748b; border-bottom: 3px solid transparent; transition: all 0.2s; margin-bottom: -2px;">
                1. Đăng ký tiêm chủng (vắc xin đã chọn)
            </button>
            <button type="button" onclick="switchSpaModalTab('consult')" style="border: none; background: none; font-size: 15.5px; font-weight: 700; padding: 12px 6px; cursor: pointer; color: var(--primary-color, #c8102e); border-bottom: 3px solid var(--primary-color, #c8102e); transition: all 0.2s; margin-bottom: -2px;">
                2. Gửi yêu cầu tư vấn nhanh
            </button>
        </div>
    ` : '';

    let centerOptions = '<option value="">-- Chọn trung tâm Medicare --</option>';
    const currentCenterId = window.lastCurrentCenter ? Number(window.lastCurrentCenter.id) : null;
    (window.lastFetchCenters || []).forEach(c => {
        const isSelected = currentCenterId && Number(c.id) === currentCenterId;
        centerOptions += `<option value="${c.id}" ${isSelected ? 'selected' : ''}>${escapeHtml(c.name)}</option>`;
    });

    body.innerHTML = `
        ${tabHtml}
        <div style="background: #ffffff; padding: 24px; max-width: 500px; margin: 0 auto;">
            <h3 style="font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 8px; text-align: justify;">Yêu cầu tư vấn <span style="color: var(--primary-color, #c8102e);">miễn phí</span></h3>
            <p style="font-size: 14px; color: #64748b; text-align: justify; margin-bottom: 24px; line-height: 1.5;">Medicare sẽ liên hệ lại ngay để tư vấn phác đồ tiêm chủng vắc xin thích hợp nhất cho bạn.</p>
            
            <form id="spaConsultForm" onsubmit="submitSpaConsult(event)" style="display: flex; flex-direction: column; gap: 16px;">
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13.5px; font-weight: 600; color: #334155; text-align: justify;">Hình thức tư vấn <span style="color:#ef4444;">*</span></label>
                    <div style="display: inline-flex; width: 100%; background: #f1f5f9; border-radius: 30px; padding: 4px; border: 1px solid #cbd5e1; box-sizing: border-box;">
                        <button type="button" id="btnSpaConsultOnline" onclick="setSpaConsultType('online')" style="flex: 1; border: none; padding: 10px 12px; border-radius: 26px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s; background: var(--primary-color, #c8102e); color: #ffffff; text-align: center;">
                            Tư vấn qua điện thoại (Online)
                        </button>
                        <button type="button" id="btnSpaConsultOffline" onclick="setSpaConsultType('offline')" style="flex: 1; border: none; padding: 10px 12px; border-radius: 26px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s; background: transparent; color: #475569; text-align: center;">
                            Tư vấn tại trung tâm (Offline)
                        </button>
                        <input type="hidden" name="consultType" id="spaConsultTypeValue" value="online">
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label for="spa_consult_name" style="font-size: 13.5px; font-weight: 600; color: #334155; text-align: justify;">Họ tên người liên hệ <span class="text-red-500">*</span></label>
                    <input type="text" id="spa_consult_name" name="customerName" placeholder="Nhập họ tên của bạn" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; font-size: 14px;">
                    <span class="error-msg text-xs text-red-500 mt-1 hidden" id="err_spa_customerName" style="text-align: justify;"></span>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label for="spa_consult_phone" style="font-size: 13.5px; font-weight: 600; color: #334155; text-align: justify;">Số điện thoại liên hệ <span class="text-red-500">*</span></label>
                    <input type="tel" id="spa_consult_phone" name="customerPhone" placeholder="Ví dụ: 0912345678" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; font-size: 14px;">
                    <span class="error-msg text-xs text-red-500 mt-1 hidden" id="err_spa_customerPhone" style="text-align: justify;"></span>
                </div>
                
                <div id="spaCenterSelectGroup" style="display: none; flex-direction: column; gap: 6px;">
                    <label for="spa_consult_center" style="font-size: 13.5px; font-weight: 600; color: #334155; text-align: justify;">Chi nhánh tư vấn gần nhất <span class="text-red-500">*</span></label>
                    <div style="position: relative; width: 100%;">
                        <select id="spa_consult_center" name="center_id" style="width: 100%; padding: 10px 36px 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; background: #fff; height: 42px; font-size: 14px; -webkit-appearance: none; -moz-appearance: none; appearance: none; cursor: pointer;">
                            ${centerOptions}
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                    <span class="error-msg text-xs text-red-500 mt-1 hidden" id="err_spa_center_id" style="text-align: justify;"></span>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label for="spa_consult_note" style="font-size: 13.5px; font-weight: 600; color: #334155; text-align: justify;">Ghi chú yêu cầu tư vấn</label>
                    <textarea id="spa_consult_note" name="customerNote" rows="3" placeholder="Nhập vắc xin bạn muốn tiêm hoặc câu hỏi cần tư vấn..." style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; font-family: inherit; font-size: 14px;"></textarea>
                </div>
                
                <div style="display: flex; gap: 12px; margin-top: 8px;">
                    <button type="button" onclick="${hasCart ? "switchSpaModalTab('register')" : "openSpaRegisterModal(null)"}" style="flex: 1; padding: 12px; border-radius: 8px; font-weight: 700; cursor: pointer; background: #f1f5f9; border: 1px solid #cbd5e1; color: #475569; font-size: 14px;">Quay lại</button>
                    <button type="submit" id="btnSubmitSpaConsult" style="flex: 2; background: var(--primary-color, #c8102e); color: #fff; border: none; padding: 12px; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px;">
                        <i data-lucide="send" style="width: 16px; height: 16px;"></i> Gửi yêu cầu
                    </button>
                </div>
            </form>
        </div>
    `;
    if (window.lucide) window.lucide.createIcons();
}

function setSpaConsultType(type) {
    const btnOnline = document.getElementById('btnSpaConsultOnline');
    const btnOffline = document.getElementById('btnSpaConsultOffline');
    const valInput = document.getElementById('spaConsultTypeValue');
    const centerGroup = document.getElementById('spaCenterSelectGroup');
    const centerSelect = document.getElementById('spa_consult_center');
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

async function submitSpaConsult(event) {
    if (event) event.preventDefault();
    const form = document.getElementById('spaConsultForm');
    const submitBtn = document.getElementById('btnSubmitSpaConsult');
    if (!form || !submitBtn) return;

    if (!confirm('Bạn có thực sự muốn gửi yêu cầu tư vấn tiêm chủng này không?')) {
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
            source: 'SPA Modal Empty Cart Form (' + (formData.get('consultType') === 'online' ? 'Online' : 'Offline') + ')'
        };

        const response = await fetch(getAbsoluteUrl('/leads'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (!response.ok) {
            if (response.status === 422 && data.errors) {
                Object.keys(data.errors).forEach(key => {
                    const errEl = document.getElementById(`err_spa_${key}`);
                    if (errEl) {
                        errEl.textContent = data.errors[key][0];
                        errEl.classList.remove('hidden');
                    }
                });
            }
            throw new Error(data.message || 'Gửi yêu cầu thất bại.');
        }

        showToast(data.message || 'Yêu cầu tư vấn đã được gửi thành công!', 'success');
        closeSpaRegisterModal();
    } catch (err) {
        console.error(err);
        showToast(err.message || 'Có lỗi xảy ra khi gửi yêu cầu.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i data-lucide="send" style="width:16px;height:16px;"></i> Gửi yêu cầu';
        if (window.lucide) window.lucide.createIcons();
    }
}

function renderSpaRegisterForm(data) {
    const body = document.getElementById('spaRegisterBody');
    if (!body) return;

    window.lastSchedules = data.schedules || [];

    const currentCenterId = data.current_center ? data.current_center.id : '';
    let centerOptions = '';
    data.centers.forEach(c => {
        centerOptions += `<option value="${c.id}" ${Number(c.id) === Number(currentCenterId) ? 'selected' : ''}>${escapeHtml(c.name)} - ${escapeHtml(c.address)}</option>`;
    });

    let dateOptions = '<option value="">-- Chọn ngày tiêm --</option>';
    (data.schedules || []).forEach(sch => {
        const datePart = (sch.date || '').substring(0, 10);
        const parts = datePart.split('-');
        const formattedDate = parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : datePart;
        dateOptions += `<option value="${datePart}">${formattedDate}</option>`;
    });


    let vaccineCheckboxes = '';
    Object.entries(data.cart).forEach(([id, item]) => {
        const formattedPrice = new Intl.NumberFormat('vi-VN').format(item.price) + ' đ';
        const unavailable = Boolean(item.unavailable_for_center);
        vaccineCheckboxes += `
            <label style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid ${unavailable ? '#fecaca' : '#e2e8f0'}; border-radius: 8px; cursor: ${unavailable ? 'not-allowed' : 'pointer'}; ${unavailable ? 'opacity: 0.55;' : ''}">
                <input type="checkbox" name="vaccine_ids[]" value="${id}" ${unavailable ? 'disabled' : 'checked'} onchange="recalculateSpaRegisterPrices()">
                <span style="flex: 1; text-align: left;">
                    <strong>${escapeHtml(item.name)}</strong>
                    <small style="display: block; color: #64748b; margin-top: 3px;">${escapeHtml(item.disease_prevention)}</small>
                </span>
                <strong style="color: var(--primary-color, #c8102e);">${formattedPrice}</strong>
            </label>
        `;
    });

    body.innerHTML = `
        <div style="display: flex; gap: 24px; border-bottom: 2px solid #e2e8f0; margin-bottom: 24px;">
            <button type="button" onclick="switchSpaModalTab('register')" style="border: none; background: none; font-size: 15.5px; font-weight: 700; padding: 12px 6px; cursor: pointer; color: var(--primary-color, #c8102e); border-bottom: 3px solid var(--primary-color, #c8102e); transition: all 0.2s; margin-bottom: -2px;">
                1. Đăng ký tiêm chủng (vắc xin đã chọn)
            </button>
            <button type="button" onclick="switchSpaModalTab('consult')" style="border: none; background: none; font-size: 15.5px; font-weight: 700; padding: 12px 6px; cursor: pointer; color: #64748b; border-bottom: 3px solid transparent; transition: all 0.2s; margin-bottom: -2px;">
                2. Gửi yêu cầu tư vấn nhanh
            </button>
        </div>

        <div class="spa-register-grid">
            <!-- Form Column -->
            <div style="min-width: 0;">
                <form id="spaFormRegisterSubmit" onsubmit="submitSpaRegistrationForm(event)" style="display: flex; flex-direction: column; gap: 16px;">
                    <div id="spaFormErrorAlert" style="display: none; background: #fef2f2; border: 1px solid #ef4444; color: #991b1b; padding: 12px 16px; border-radius: 8px; font-size: 13.5px; text-align: left;"></div>
                    
                    <!-- Patients List Container -->
                    <div id="spaPatientsContainer" style="display: flex; flex-direction: column; gap: 20px;"></div>

                    <!-- Add Patient Action Button -->
                    <div style="display: flex; justify-content: flex-start; margin-top: 4px; margin-bottom: 8px;">
                        <button type="button" onclick="addSpaPatientField()" style="background: none; border: 1.5px dashed var(--primary-color, #c8102e); color: var(--primary-color, #c8102e); padding: 10px 16px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 13.5px; display: flex; align-items: center; gap: 6px; transition: all 0.2s;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            Thêm người tiêm khác
                        </button>
                    </div>

                    <!-- Guardian Section (trẻ em dưới 15 tuổi) -->
                    <div id="spaGuardianSection" style="display: none; flex-direction: column; gap: 14px; padding: 16px; background: #fff8f8; border: 1px solid #fecaca; border-radius: 10px; margin-bottom: 8px;">
                        <h4 style="margin: 0; font-size: 13.5px; font-weight: 700; color: #991b1b; display: flex; align-items: center; gap: 6px; text-align: left;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            Thông tin người giám hộ (Bắt buộc khi có người tiêm dưới 15 tuổi)
                        </h4>
                        <div class="spa-form-row">
                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                <label for="spa_guardian_name" style="font-size: 13px; font-weight: 600; color: #334155; text-align: left;">Họ tên người giám hộ <span class="text-red-500">*</span></label>
                                <input type="text" id="spa_guardian_name" name="guardian_name" placeholder="Ví dụ: Nguyễn Văn A" style="padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px;">
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                <label for="spa_guardian_phone" style="font-size: 13px; font-weight: 600; color: #334155; text-align: left;">Số điện thoại người giám hộ <span class="text-red-500">*</span></label>
                                <input type="tel" id="spa_guardian_phone" name="guardian_phone" placeholder="Ví dụ: 0912345678" style="padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px;">
                            </div>
                        </div>
                    </div>

                    <!-- Center Selection -->
                    <div style="display: flex; flex-direction: column; gap: 6px; padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;">
                        <label for="spa_booking_center_id" style="font-size: 13.5px; font-weight: 700; color: #334155; text-align: left;">Chi nhánh đặt lịch</label>
                        <div style="position: relative; width: 100%;">
                            <select id="spa_booking_center_id" name="center_id" onchange="changeSpaRegisterCenter(this.value)" style="width: 100%; padding: 10px 36px 10px 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px; background: #fff; -webkit-appearance: none; -moz-appearance: none; appearance: none; cursor: pointer;">
                                ${centerOptions}
                            </select>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </div>

                    <!-- Date & Slot Hours Grid -->
                    <div class="spa-form-row">
                        <!-- Chọn Ngày -->
                        <div style="display: flex; flex-direction: column; gap: 6px;">
                            <label for="spa_date_select" style="font-size: 13.5px; font-weight: 600; color: #334155; text-align: left;">Ngày tiêm <span class="text-red-500">*</span></label>
                            <div style="position: relative; width: 100%;">
                                <select id="spa_date_select" onchange="changeSpaDateFilter(this.value)" required style="width: 100%; padding: 10px 36px 10px 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px; background: #fff; -webkit-appearance: none; -moz-appearance: none; appearance: none; cursor: pointer;">
                                    ${dateOptions}
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </div>
                        
                        <!-- Chọn Khung Giờ -->
                        <div style="display: flex; flex-direction: column; gap: 6px;">
                            <label for="spa_slot_id" style="font-size: 13.5px; font-weight: 600; color: #334155; text-align: left;">Khung giờ tiêm <span class="text-red-500">*</span></label>
                            <div style="position: relative; width: 100%;">
                                <select id="spa_slot_id" name="slot_id" required disabled style="width: 100%; padding: 10px 36px 10px 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px; background: #f8fafc; -webkit-appearance: none; -moz-appearance: none; appearance: none; cursor: not-allowed;">
                                    <option value="">-- Vui lòng chọn ngày --</option>
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </div>
                    </div>
                    ${data.schedules.length === 0 ? '<small style="color: #b91c1c; text-align: left; margin-top: 2px; display: block;">Chi nhánh hiện chưa mở lịch tiêm. Vui lòng chọn chi nhánh khác.</small>' : ''}

                    <div style="display: flex; justify-content: flex-end; margin-top: 14px;">
                        <button type="submit" id="spaSubmitBtn" style="background: var(--primary-color, #c8102e); color: #fff; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px;">
                            Hoàn tất đặt lịch
                        </button>
                    </div>
                </form>
            </div>

            <!-- Summary Column -->
            <aside style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; gap: 16px; height: fit-content; text-align: left; width: 280px; min-width: 280px; flex-shrink: 0; box-sizing: border-box;">
                <h4 style="margin: 0; font-size: 15px; font-weight: 800; color: #1e293b; text-transform: uppercase;">Tóm tắt</h4>
                <div id="spaSummaryItems" style="display: flex; flex-direction: column; gap: 10px;">
                    <!-- populated dynamically -->
                </div>
                <div style="height: 1px; background: #e2e8f0;"></div>
                <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 800;">
                    <span style="font-size: 14px; color: #475569;">Tổng dự kiến:</span>
                    <strong id="spaSummaryTotalPrice" style="font-size: 18px; color: var(--primary-color);">0 đ</strong>
                </div>
                <div style="font-size: 12px; color: #64748b; line-height: 1.5; text-align: justify;">
                    Thanh toán và sử dụng điểm được nhân viên xác nhận tại quầy. Giá cuối cùng được hệ thống chốt khi tạo phiếu.
                </div>
            </aside>
        </div>
    `;

    window.spaPatientCount = 0;
    addSpaPatientField();
}

async function changeSpaRegisterCenter(centerId) {
    const body = document.getElementById('spaRegisterBody');
    if (!body) return;

    // Preserve user inputs
    const patientName = document.getElementById('spa_patient_name')?.value || '';
    const patientPhone = document.getElementById('spa_patient_phone')?.value || '';

    // Apply transient loading overlay style rather than wiping the screen
    const grid = document.querySelector('.spa-register-grid');
    if (grid) {
        grid.style.opacity = '0.5';
        grid.style.pointerEvents = 'none';
    }

    try {
        const formData = new FormData();
        formData.append('center_id', centerId);
        formData.append('redirect_to', 'ajax');

        // 1. Post selection to update session center
        await fetch(getAbsoluteUrl('/centers/select'), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: formData
        });

        // 2. Fetch new center specific schedules and cart states
        const response = await fetch(getAbsoluteUrl('/register'), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        
        if (response.ok && data.success) {
            window.lastFetchCenters = data.centers || [];
            window.lastCartData = data;
            window.lastCurrentCenter = data.current_center || null;

            // Redraw form layout
            renderSpaRegisterForm(data);

            // Restore inputs back to the first patient block
            const nameInput = document.querySelector('.spa-patient-name');
            const phoneInput = document.querySelector('.spa-patient-phone');
            if (nameInput) nameInput.value = patientName;
            if (phoneInput) phoneInput.value = patientPhone;
        } else {
            throw new Error(data.message || 'Lọc chi nhánh thất bại.');
        }
    } catch (e) {
        console.error(e);
        showToast('Không thể chuyển chi nhánh. Vui lòng thử lại.', 'error');
        if (grid) {
            grid.style.opacity = '1';
            grid.style.pointerEvents = 'auto';
        }
    }
}

function addSpaPatientField() {
    const container = document.getElementById('spaPatientsContainer');
    if (!container) return;

    const index = window.spaPatientCount || 0;
    const today = new Date().toISOString().split('T')[0];

    // Build vaccine checkboxes list from current cart
    let vaccineCheckboxes = '';
    Object.entries(window.lastCartData.cart || {}).forEach(([id, item]) => {
        const formattedPrice = new Intl.NumberFormat('vi-VN').format(item.price) + ' đ';
        const unavailable = Boolean(item.unavailable_for_center);
        vaccineCheckboxes += `
            <label style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border: 1px solid ${unavailable ? '#fecaca' : '#e2e8f0'}; border-radius: 8px; cursor: ${unavailable ? 'not-allowed' : 'pointer'}; font-size: 13.5px; background: #fff; ${unavailable ? 'opacity: 0.55;' : ''}">
                <input type="checkbox" value="${id}" data-price="${item.price}" class="spa-patient-vaccine-checkbox" ${unavailable ? 'disabled' : 'checked'} onchange="recalculateSpaRegisterPrices()" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--primary-color);">
                <span style="flex: 1; text-align: left;">
                    <strong>${escapeHtml(item.name)}</strong>
                    <small style="display: block; color: #64748b; margin-top: 3px;">${escapeHtml(item.disease_prevention)}</small>
                </span>
                <strong style="color: var(--primary-color);">${formattedPrice}</strong>
            </label>
        `;
    });

    const blockHtml = `
        <div class="spa-patient-block" id="spaPatientBlock_${index}" style="padding: 18px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; display: flex; flex-direction: column; gap: 14px; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                <h4 style="font-size: 14px; font-weight: 800; color: var(--accent-color, #004b8f); margin: 0;">Người tiêm #${index + 1}</h4>
                ${index > 0 ? `
                    <button type="button" onclick="removeSpaPatientField(${index})" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 4px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        Xóa người này
                    </button>
                ` : ''}
            </div>

            <div class="spa-form-row">
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; font-weight: 600; color: #334155; text-align: left;">Họ và tên người tiêm <span class="text-red-500">*</span></label>
                    <input type="text" class="spa-patient-name" required placeholder="Ví dụ: Nguyễn Văn A" style="padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px;">
                </div>
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; font-weight: 600; color: #334155; text-align: left;">Số điện thoại liên hệ <span class="text-red-500">*</span></label>
                    <input type="tel" class="spa-patient-phone" required placeholder="Ví dụ: 0912345678" style="padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px;">
                </div>
            </div>

            <div class="spa-form-row">
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; font-weight: 600; color: #334155; text-align: left;">Ngày sinh <span class="text-red-500">*</span></label>
                    <input type="date" class="spa-patient-dob" required max="${today}" onchange="checkSpaPatientAge()" style="padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px;">
                </div>
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; font-weight: 600; color: #334155; text-align: left;">Giới tính <span class="text-red-500">*</span></label>
                    <select class="spa-patient-gender" required style="padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px; background: #fff; cursor: pointer;">
                        <option value="Nam">Nam</option>
                        <option value="Nữ">Nữ</option>
                        <option value="Khác">Khác</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label style="font-size: 13px; font-weight: 600; color: #334155; text-align: left;">Địa chỉ thường trú <span class="text-red-500">*</span></label>
                <input type="text" class="spa-patient-address" required placeholder="Số nhà, tên đường, phường/xã..." style="padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px;">
            </div>

            <div style="margin-top: 6px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; flex-direction: column; gap: 8px;">
                <h5 style="margin: 0; font-size: 13px; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 6px; text-align: left;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    Chọn vắc xin cho người này:
                </h5>
                <div style="display: grid; gap: 8px;">
                    ${vaccineCheckboxes}
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', blockHtml);
    window.spaPatientCount++;
    recalculateSpaRegisterPrices();
}

function removeSpaPatientField(index) {
    const block = document.getElementById(`spaPatientBlock_${index}`);
    if (block) {
        block.remove();
        recalculateSpaRegisterPrices();
        checkSpaPatientAge();
    }
}

function checkSpaPatientAge() {
    let hasMinor = false;
    document.querySelectorAll('.spa-patient-dob').forEach(input => {
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

    const guardianSec = document.getElementById('spaGuardianSection');
    const gName = document.getElementById('spa_guardian_name');
    const gPhone = document.getElementById('spa_guardian_phone');

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

function changeSpaDateFilter(selectedDate) {
    const slotSelect = document.getElementById('spa_slot_id');
    if (!slotSelect) return;

    if (!selectedDate) {
        slotSelect.innerHTML = '<option value="">-- Vui lòng chọn ngày --</option>';
        slotSelect.disabled = true;
        slotSelect.style.background = '#f8fafc';
        slotSelect.style.cursor = 'not-allowed';
        return;
    }

    const schedules = window.lastSchedules || [];
    const daySchedule = schedules.find(s => (s.date || '').substring(0, 10) === selectedDate);
    
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

function recalculateSpaRegisterPrices() {
    const form = document.getElementById('spaFormRegisterSubmit');
    const summaryContainer = document.getElementById('spaSummaryItems');
    const totalPriceEl = document.getElementById('spaSummaryTotalPrice');
    const submitBtn = document.getElementById('spaSubmitBtn');
    if (!form || !summaryContainer || !totalPriceEl) return;

    const checkedCheckboxes = form.querySelectorAll('.spa-patient-vaccine-checkbox:checked');
    
    let total = 0;
    const itemsMap = {};

    checkedCheckboxes.forEach(cb => {
        const id = cb.value;
        const cartItem = window.lastCartData.cart[id];
        if (cartItem) {
            total += Number(cartItem.price);
            if (!itemsMap[id]) {
                itemsMap[id] = {
                    name: cartItem.name,
                    price: cartItem.price,
                    count: 0
                };
            }
            itemsMap[id].count += 1;
        }
    });

    let summaryHtml = '';
    Object.values(itemsMap).forEach(item => {
        summaryHtml += `
            <div style="display: flex; justify-content: space-between; font-size: 13.5px;">
                <span style="font-weight: 700; color: #334155; max-width: 180px;">${escapeHtml(item.name)} ${item.count > 1 ? `(x${item.count})` : ''}</span>
                <span style="font-weight: 700; color: var(--primary-color);">${new Intl.NumberFormat('vi-VN').format(item.price * item.count)} đ</span>
            </div>
        `;
    });

    summaryContainer.innerHTML = summaryHtml || '<div style="color:#94a3b8;font-size:13px;">Chưa chọn vắc xin nào.</div>';
    totalPriceEl.textContent = new Intl.NumberFormat('vi-VN').format(total) + ' đ';

    if (submitBtn) {
        submitBtn.disabled = checkedCheckboxes.length === 0;
    }
}

async function submitSpaRegistrationForm(event) {
    if (event) event.preventDefault();
    const form = document.getElementById('spaFormRegisterSubmit');
    const errEl = document.getElementById('spaFormErrorAlert');
    const submitBtn = document.getElementById('spaSubmitBtn');
    if (!form || !submitBtn || !errEl) return;

    errEl.style.display = 'none';
    errEl.textContent = '';

    // Validate that each patient block has at least one vaccine checked
    const blocks = form.querySelectorAll('.spa-patient-block');
    if (blocks.length === 0) {
        errEl.textContent = 'Vui lòng thêm ít nhất một người tiêm.';
        errEl.style.display = 'block';
        return;
    }

    for (let i = 0; i < blocks.length; i++) {
        const block = blocks[i];
        const checked = block.querySelectorAll('.spa-patient-vaccine-checkbox:checked');
        if (checked.length === 0) {
            errEl.textContent = `Vui lòng chọn ít nhất một loại vắc xin cho Người tiêm #${i + 1}.`;
            errEl.style.display = 'block';
            return;
        }
    }

    showConfirmDialog({
        title: 'Xác nhận đặt lịch',
        message: 'Bạn có chắc chắn muốn hoàn tất đặt lịch tiêm chủng này không?',
        confirmText: 'Xác nhận',
        cancelText: 'Hủy',
        onConfirm: async () => {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="w-4 h-4 animate-spin rounded-full border-2 border-white border-t-transparent"></i> Đang gửi...';
            showToast('Đang gửi yêu cầu đặt lịch, vui lòng đợi trong giây lát...', 'info');

            try {
                const formData = new FormData(form);
                const patients = [];

                blocks.forEach((block, index) => {
                    const name = block.querySelector('.spa-patient-name')?.value || '';
                    const phone = block.querySelector('.spa-patient-phone')?.value || '';
                    const dob = block.querySelector('.spa-patient-dob')?.value || '';
                    const gender = block.querySelector('.spa-patient-gender')?.value || 'Khác';
                    const address = block.querySelector('.spa-patient-address')?.value || '';
                    const checkedVacIds = [];
                    block.querySelectorAll('.spa-patient-vaccine-checkbox:checked').forEach(cb => {
                        checkedVacIds.push(Number(cb.value));
                    });
                    
                    patients.push({
                        name,
                        phone,
                        dob,
                        gender,
                        address,
                        vaccine_ids: checkedVacIds
                    });
                });

                const payload = {
                    patients: patients,
                    slot_id: Number(formData.get('slot_id')),
                    guardian_name: formData.get('guardian_name') || null,
                    guardian_phone: formData.get('guardian_phone') || null,
                    payment_method: 'Tại trung tâm',
                    idempotency_key: 'spa_' + Math.random().toString(36).substring(2)
                };

                const response = await fetch(getAbsoluteUrl('/register'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok) {
                    let errMsg = data.message || 'Lỗi đặt lịch tiêm chủng.';
                    if (response.status === 422 && data.errors) {
                        const firstErrKey = Object.keys(data.errors)[0];
                        errMsg = data.errors[firstErrKey][0];
                    }
                    throw new Error(errMsg);
                }

                showToast(data.message || 'Đặt lịch tiêm chủng thành công!', 'success');
                closeSpaRegisterModal();

                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                }
            } catch (err) {
                console.error(err);
                errEl.textContent = err.message || 'Có lỗi xảy ra khi gửi yêu cầu.';
                errEl.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Hoàn tất đặt lịch';
            }
        }
    });
}

async function openSpaConsultationModal(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const modal = document.getElementById('spaRegisterModal');
    const body = document.getElementById('spaRegisterBody');
    if (!modal || !body) return;

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    if (!window.lastFetchCenters || window.lastFetchCenters.length === 0) {
        body.innerHTML = `
            <div style="text-align: center; padding: 40px;">
                <i data-lucide="loader-2" style="width: 36px; height: 36px; color: var(--primary-color, #c8102e); animation: spin 1s linear infinite;"></i>
                <p style="margin-top: 12px; color: #64748b;">Đang chuẩn bị form tư vấn...</p>
            </div>
        `;
        if (window.lucide) window.lucide.createIcons();

        try {
            const response = await fetch(getAbsoluteUrl('/register'), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            window.lastFetchCenters = data.centers || [];
            window.lastCurrentCenter = data.current_center || null;
        } catch (e) {
            console.error('Lỗi tải chi nhánh cho tư vấn:', e);
        }
    }

    renderSpaConsultForm();
}

    window.addEventListener('popstate', () => {
        navigateSpa(window.location.href, false);
    });

    // Expose functions globally to window so HTML onclick inline events can invoke them
    window.toggleCart = toggleCart;
    window.toggleCartDrawer = toggleCartDrawer;
    window.toggleHeaderCartDropdown = toggleHeaderCartDropdown;
    window.clearCartUI = clearCartUI;
    window.openVaccineDetailModal = openVaccineDetailModal;
    window.closeVaccineDetailModal = closeVaccineDetailModal;
    window.setAgeGroupFilter = setAgeGroupFilter;
    window.setDiseaseFilter = setDiseaseFilter;
    window.setOriginFilter = setOriginFilter;
    window.setDosesFilter = setDosesFilter;
    window.setVaccineTypeFilter = setVaccineTypeFilter;
    window.setSortFilter = setSortFilter;
    window.resetVaccineFilters = resetVaccineFilters;
    window.filterVaccinesSpa = filterVaccinesSpa;
    window.toggleCatalogFilterGroup = toggleCatalogFilterGroup;
    window.filterOriginOptions = filterOriginOptions;
    window.filterDiseaseOptions = filterDiseaseOptions;
    window.toggleMobileFilterBottomSheet = toggleMobileFilterBottomSheet;
    window.openBranchModal = openBranchModal;
    window.closeBranchModal = closeBranchModal;
    window.toggleCustomDropdown = toggleCustomDropdown;
    window.toggleMobileTocAccordion = toggleMobileTocAccordion;
    window.initDynamicTOC = initDynamicTOC;
    window.filterNewsCategorySpa = filterNewsCategorySpa;
    window.openSpaRegisterModal = openSpaRegisterModal;
    window.closeSpaRegisterModal = closeSpaRegisterModal;
    window.switchSpaModalTab = switchSpaModalTab;
    window.renderSpaConsultForm = renderSpaConsultForm;
    window.submitSpaConsult = submitSpaConsult;
    window.setSpaConsultType = setSpaConsultType;
    window.renderSpaRegisterForm = renderSpaRegisterForm;
    window.changeSpaRegisterCenter = changeSpaRegisterCenter;
    window.recalculateSpaRegisterPrices = recalculateSpaRegisterPrices;
    window.submitSpaRegistrationForm = submitSpaRegistrationForm;
    window.openSpaConsultationModal = openSpaConsultationModal;
    window.changeSpaDateFilter = changeSpaDateFilter;
    window.addSpaPatientField = addSpaPatientField;
    window.removeSpaPatientField = removeSpaPatientField;
    window.checkSpaPatientAge = checkSpaPatientAge;
    window.showConfirmDialog = showConfirmDialog;
}

// Initial Page Load: Run initDynamicTOC on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    initDynamicTOC();
});

