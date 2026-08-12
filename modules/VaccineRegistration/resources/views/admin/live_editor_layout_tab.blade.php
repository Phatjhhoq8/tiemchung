<div style="background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #cbd5e1; margin-bottom: 24px;">
    <h3 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 800; color: #1e293b;">Sắp Xếp & Cấu Hình Các Phần Trang Chủ</h3>
    <p style="margin: 0 0 20px 0; font-size: 13px; color: #64748b;">
        Thay đổi thứ tự hiển thị của các phần trên trang chủ bằng cách sử dụng các nút di chuyển Lên (▲) và Xuống (▼). Bạn cũng có thể ẩn/hiện hoặc đổi màu nền của từng phần.
    </p>

    <form id="layoutConfigForm">
        @csrf
        <div id="layoutRowsContainer" style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($layoutConfig as $keyName => $section)
                <div class="layout-section-row" data-key="{{ $keyName }}" style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s;">
                    <!-- Trái: Điều hướng & Tiêu đề -->
                    <div style="display: flex; align-items: center; gap: 14px; flex-grow: 1;">
                        <!-- Nút bấm Lên Xuống -->
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <button type="button" onclick="moveSectionRow(this, 'up')" style="width: 28px; height: 28px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; font-size: 11px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #475569;" title="Di chuyển lên">▲</button>
                            <button type="button" onclick="moveSectionRow(this, 'down')" style="width: 28px; height: 28px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; font-size: 11px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #475569;" title="Di chuyển xuống">▼</button>
                        </div>
                        
                        <!-- Tên Phần -->
                        <div>
                            <strong style="font-size: 14.5px; color: #1e293b; display: block;">{{ $section['name'] }}</strong>
                            <span style="font-size: 11.5px; color: #64748b;">Mã: {{ $keyName }}</span>
                            <input type="hidden" name="layout[{{ $keyName }}][order]" class="section-order-input" value="{{ $section['order'] }}">
                        </div>
                    </div>

                    <!-- Phải: Các tùy chọn cấu hình -->
                    <div style="display: flex; align-items: center; gap: 20px; flex-shrink: 0;">
                        <!-- Màu nền -->
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <label style="font-size: 11px; font-weight: 700; color: #64748b;">Màu nền</label>
                            <select name="layout[{{ $keyName }}][bg]" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 12.5px; color: #334155; outline: none; background: #fff;">
                                <option value="white" {{ ($section['bg'] ?? 'white') === 'white' ? 'selected' : '' }}>Nền trắng tiêu chuẩn</option>
                                <option value="red" {{ ($section['bg'] ?? 'white') === 'red' ? 'selected' : '' }}>Nền đỏ Medicare</option>
                                <option value="dark" {{ ($section['bg'] ?? 'white') === 'dark' ? 'selected' : '' }}>Nền tối Navy</option>
                                <option value="light-blue" {{ ($section['bg'] ?? 'white') === 'light-blue' ? 'selected' : '' }}>Nền xanh nhạt</option>
                            </select>
                        </div>

                        <!-- Độ rộng padding -->
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <label style="font-size: 11px; font-weight: 700; color: #64748b;">Khoảng đệm</label>
                            <select name="layout[{{ $keyName }}][padding]" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 12.5px; color: #334155; outline: none; background: #fff;">
                                <option value="standard" {{ ($section['padding'] ?? 'standard') === 'standard' ? 'selected' : '' }}>Tiêu chuẩn (py-20)</option>
                                <option value="compact" {{ ($section['padding'] ?? 'standard') === 'compact' ? 'selected' : '' }}>Thu gọn (py-12)</option>
                                <option value="spacious" {{ ($section['padding'] ?? 'standard') === 'spacious' ? 'selected' : '' }}>Rộng rãi (py-28)</option>
                            </select>
                        </div>

                        <!-- Trạng thái ẩn hiện -->
                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                            <label style="font-size: 11px; font-weight: 700; color: #64748b;">Trạng thái</label>
                            <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; user-select: none; margin-top: 4px;">
                                <input type="hidden" name="layout[{{ $keyName }}][is_visible]" value="0">
                                <input type="checkbox" name="layout[{{ $keyName }}][is_visible]" value="1" {{ ($section['is_visible'] ?? true) ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: var(--primary-color, #c8102e); cursor: pointer;">
                                <span style="font-size: 13px; color: #334155;">Hiển thị</span>
                            </label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Nút lưu tạm cấu hình nháp -->
        <div style="margin-top: 24px; display: flex; justify-content: flex-end;">
            <button type="button" onclick="saveLayoutConfigDraft()" class="btn-primary" style="padding: 10px 24px; border-radius: 8px; background: var(--primary-color, #c8102e); border: none; font-weight: 700; color: #fff; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#a00d24'" onmouseout="this.style.backgroundColor='var(--primary-color, #c8102e)'">
                <i data-lucide="save" style="width: 16px; height: 16px;"></i> Lưu Sắp Xếp Nháp
            </button>
        </div>
    </form>
</div>

<script>
    /**
     * Di chuyển hàng cấu hình lên hoặc xuống trong danh sách.
     */
    function moveSectionRow(btn, direction) {
        const row = btn.closest('.layout-section-row');
        if (!row) return;

        const container = document.getElementById('layoutRowsContainer');
        if (direction === 'up') {
            const prev = row.previousElementSibling;
            if (prev) {
                container.insertBefore(row, prev);
            }
        } else if (direction === 'down') {
            const next = row.nextElementSibling;
            if (next) {
                container.insertBefore(next, row);
            }
        }

        // Cập nhật lại giá trị input 'order' tự động theo thứ tự DOM mới
        recalculateRowOrders();
    }

    /**
     * Tính toán lại giá trị của các input order dựa theo vị trí thực tế của DOM.
     */
    function recalculateRowOrders() {
        const container = document.getElementById('layoutRowsContainer');
        const rows = container.querySelectorAll('.layout-section-row');
        rows.forEach((row, index) => {
            const orderInput = row.querySelector('.section-order-input');
            if (orderInput) {
                orderInput.value = (index + 1) * 10;
            }
        });
    }

    /**
     * Lưu bản nháp cấu hình sắp xếp layout.
     */
    async function saveLayoutConfigDraft() {
        if (!await window.AppDialog.confirm('Bạn có chắc chắn muốn lưu cấu hình sắp xếp nháp hiện tại không?')) {
            return;
        }

        const form = document.getElementById('layoutConfigForm');
        const formData = new FormData(form);

        fetch("{{ route('admin.live-editor.layout.save') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        })
        .then(res => res.json())
        .then(async data => {
            if (data.success) {
                await window.AppDialog.alert(data.message);
                window.location.reload();
            } else {
                await window.AppDialog.alert("❌ Không thể lưu cấu hình nháp.");
            }
        })
        .catch(async err => {
            console.error(err);
            await window.AppDialog.alert("❌ Đã xảy ra lỗi kết nối.");
        });
    }
</script>
