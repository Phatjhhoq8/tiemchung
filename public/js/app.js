// Quản lý CSRF Token trong Laravel cho các request AJAX
const getCsrfToken = () => {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
};

// ==========================================================================
// GIỎ HÀNG VẮC XIN (CART MANAGEMENT)
// ==========================================================================

// Bật/tắt mở rộng ngăn kéo giỏ hàng nổi
function toggleCartDrawer() {
    const cartEl = document.getElementById('floatingCart');
    if (cartEl) {
        cartEl.classList.toggle('expanded');
    }
}

// Hàm thêm/xóa vắc xin khỏi giỏ hàng qua AJAX
async function toggleCart(vaccineId) {
    const cardEl = document.querySelector(`.vaccine-card[data-id="${vaccineId}"]`);
    const isSelected = cardEl ? cardEl.classList.contains('selected') : false;
    
    // Xác định route và data gửi đi
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
            // 1. Cập nhật trạng thái hiển thị trên thẻ vắc xin ở trang chủ
            if (cardEl) {
                const btn = cardEl.querySelector('.btn-select-vaccine');
                if (isSelected) {
                    cardEl.classList.remove('selected');
                    btn.classList.remove('btn-selected');
                    btn.innerHTML = `<i data-lucide="plus"></i> <span>Chọn tiêm</span>`;
                } else {
                    cardEl.classList.add('selected');
                    btn.classList.add('btn-selected');
                    btn.innerHTML = `<i data-lucide="check"></i> <span>Đã chọn</span>`;
                }
                lucide.createIcons(); // Khởi tạo lại icons mới thay thế
            }
            
            // 2. Cập nhật thông số giỏ hàng nổi
            updateFloatingCart(data.cart, data.cart_count, data.total_price);
        }
    } catch (error) {
        console.error('Lỗi giỏ hàng:', error);
        alert('Có lỗi xảy ra khi cập nhật danh sách tiêm chủng. Vui lòng thử lại.');
    }
}

// Cập nhật giao diện giỏ hàng nổi động
function updateFloatingCart(cart, count, totalPrice) {
    const cartEl = document.getElementById('floatingCart');
    const cartCountEl = document.getElementById('cartCount');
    const cartTotalPriceEl = document.getElementById('cartTotalPrice');
    const drawerTotalPriceEl = document.getElementById('drawerTotalPrice');
    const cartListEl = document.getElementById('cartItemsList');
    
    if (!cartEl) return;
    
    // Nếu giỏ hàng trống, ẩn đi
    if (count === 0) {
        cartEl.classList.add('hidden');
        cartEl.classList.remove('expanded');
        return;
    }
    
    // Hiện giỏ hàng
    cartEl.classList.remove('hidden');
    
    // Cập nhật số lượng & tổng giá
    if (cartCountEl) cartCountEl.textContent = count;
    
    const formattedPrice = new Intl.NumberFormat('vi-VN').format(totalPrice) + ' đ';
    if (cartTotalPriceEl) cartTotalPriceEl.textContent = formattedPrice;
    if (drawerTotalPriceEl) drawerTotalPriceEl.textContent = formattedPrice;
    
    // Cập nhật danh sách các item trong drawer
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

// Xóa sạch tất cả các vắc xin đã chọn
async function clearCartUI() {
    const cartListEl = document.getElementById('cartItemsList');
    if (!cartListEl) return;
    
    const items = cartListEl.querySelectorAll('.cart-item');
    // Duyệt qua từng item và xóa khỏi giỏ hàng
    for (const item of items) {
        const id = item.getAttribute('data-id');
        await toggleCart(id);
    }
}
