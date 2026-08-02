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
    const icon = button.querySelector('i');
    if (icon) icon.setAttribute('data-lucide', group.classList.contains('open') ? 'chevron-up' : 'chevron-down');
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

    if (widget) widget.style.display = 'block';
    if (mobileWidget) mobileWidget.style.display = 'block';

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

// Handle Browser Back / Forward buttons
window.addEventListener('popstate', () => {
    navigateSpa(window.location.href, false);
});
}

// Initial Page Load: Run initDynamicTOC on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    initDynamicTOC();
});

