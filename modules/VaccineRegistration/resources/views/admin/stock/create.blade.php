@extends('vaccine::layouts.admin')

@section('title', 'Nhập hàng')
@section('page_title', 'Ghi Nhận Nhập Hàng')

@section('admin_content')
<form method="POST" action="{{ route('admin.stock.store') }}">
    @csrf
    <div class="card-modern">
        @if ($errors->any())
            <div style="margin-bottom:20px; padding:14px; border-radius:8px; background:#fef2f2; color:#991b1b; border:1px solid #fecaca;">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif
        <div class="form-grid-2">
            <div class="form-group-modern" style="margin-bottom:0;">
                <label class="form-label-modern">Chi nhánh *</label>
                @if($isSuperAdmin ?? false)
                    <select name="center_id" class="form-control-modern" style="background-image:none;" required onchange="if (this.value) window.location.href = '{{ route('admin.stock.create') }}?center_id=' + encodeURIComponent(this.value)">
                        <option value="">-- Chọn chi nhánh nhập kho --</option>
                        @foreach($centers as $center)
                            <option value="{{ $center->id }}" {{ (string) $selectedCenterId === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                        @endforeach
                    </select>
                @else
                    <input type="hidden" name="center_id" value="{{ $selectedCenterId }}">
                    <input class="form-control-modern" value="{{ $adminUser?->center?->name }}" disabled>
                @endif
            </div>
            @if(($isSuperAdmin ?? false) && !$selectedCenterId)
                <div style="grid-column:span 2; padding:12px 14px; border-radius:8px; background:#fffbeb; color:#92400e; border:1px solid #fde68a;">
                    Vui lòng chọn một chi nhánh để tải danh sách vắc xin đang được phép nhập kho.
                </div>
            @endif
            <div class="form-group-modern" style="margin-bottom:0; position:relative;">
                <label class="form-label-modern" for="vaccine_search_input">Sản phẩm *</label>
                <input type="text" id="vaccine_search_input" placeholder="Gõ tìm tên sản phẩm..." class="form-control-modern" required autocomplete="off">
                <input type="hidden" name="vaccine_id" id="vaccine_id" required>
                
                <div id="vaccine_dropdown_list" style="display:none; position:absolute; top:100%; left:0; right:0; max-height:220px; overflow-y:auto; background:#fff; border:1px solid #cbd5e1; border-radius:8px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); z-index:99; margin-top:4px;">
                    @foreach($vaccines as $vaccine)
                        <div class="vaccine-option-item" data-id="{{ $vaccine->id }}" data-name="{{ strtolower($vaccine->name) }}" style="padding:10px 14px; cursor:pointer; font-size:13.5px; border-bottom:1px solid #f1f5f9; transition:all 0.15s; color:#334155;">
                            {{ $vaccine->name }}
                        </div>
                    @endforeach
                    <div id="no_vaccines_found" style="display:none; padding:12px 14px; font-size:13px; color:#94a3b8; text-align:center;">
                        Không tìm thấy sản phẩm phù hợp.
                    </div>
                </div>
            </div>
            <div class="form-group-modern" style="margin-bottom:0;">
                <label class="form-label-modern">Loại *</label>
                <select name="type" class="form-control-modern" style="background-image:none;" required>
                    <option value="import">Nhập vào</option>
                    <option value="adjustment">Điều chỉnh tăng</option>
                </select>
            </div>
            <div class="form-group-modern" style="margin-bottom:0;">
                <label class="form-label-modern">Số lượng *</label>
                <input type="number" name="quantity" min="1" value="1" class="form-control-modern" required>
            </div>
            <div class="form-group-modern" style="margin-bottom:0;">
                <label class="form-label-modern">Đơn giá nhập</label>
                <input type="number" name="unit_price" min="0" value="0" class="form-control-modern">
            </div>
            <div class="form-group-modern" style="grid-column:span 2; margin-bottom:0;">
                <label class="form-label-modern">Ghi chú</label>
                <textarea name="note" rows="3" class="form-control-modern" style="resize:vertical;"></textarea>
            </div>
        </div>
    </div>
    <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px;">
        <a href="{{ route('admin.stock.index') }}" class="btn-modern btn-modern-secondary" style="text-decoration:none;">Hủy</a>
        <button type="submit" class="btn-modern btn-modern-primary" {{ !$selectedCenterId ? 'disabled' : '' }}>Lưu nhập kho</button>
    </div>
</form>
@endsection

<style>
    .vaccine-option-item:hover {
        background-color: #fef2f2 !important;
        color: var(--primary-color, #c8102e) !important;
    }
    .vaccine-option-item.selected {
        background-color: #fef2f2 !important;
        color: var(--primary-color, #c8102e) !important;
        font-weight: 600;
    }
</style>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('vaccine_search_input');
        const hidden = document.getElementById('vaccine_id');
        const dropdown = document.getElementById('vaccine_dropdown_list');
        const options = document.querySelectorAll('.vaccine-option-item');
        const noFound = document.getElementById('no_vaccines_found');

        if (!input || !hidden || !dropdown) return;

        function removeTones(str) {
            return str.normalize('NFD')
                      .replace(/[\u0300-\u036f]/g, '')
                      .replace(/đ/g, 'd')
                      .replace(/Đ/g, 'D');
        }

        // Hiện dropdown khi focus
        input.addEventListener('focus', () => {
            dropdown.style.display = 'block';
            filterOptions();
        });

        // Ẩn dropdown khi click ra ngoài
        document.addEventListener('click', (e) => {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        // Lọc khi người dùng gõ
        input.addEventListener('input', () => {
            hidden.value = '';
            options.forEach(option => option.classList.remove('selected'));
            dropdown.style.display = 'block';
            filterOptions();
        });

        function filterOptions() {
            const query = removeTones(input.value.toLowerCase().trim());
            let visibleCount = 0;

            options.forEach(opt => {
                const name = removeTones(opt.getAttribute('data-name'));
                if (name.includes(query)) {
                    opt.style.display = 'block';
                    visibleCount++;
                } else {
                    opt.style.display = 'none';
                }
            });

            if (visibleCount === 0) {
                noFound.style.display = 'block';
            } else {
                noFound.style.display = 'none';
            }
        }

        // Chọn item khi click (Mousedown xảy ra trước blur)
        options.forEach(opt => {
            opt.addEventListener('mousedown', function(e) {
                e.preventDefault(); // Ngăn input mất focus
                
                input.value = this.textContent.trim();
                hidden.value = this.getAttribute('data-id');
                
                options.forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
                
                dropdown.style.display = 'none';
            });
        });

        // Nếu xóa trắng thì reset ID
        input.addEventListener('change', function() {
            if (this.value.trim() === '') {
                hidden.value = '';
            }
        });

        // Ngăn submit nếu chưa chọn sản phẩm
        const form = input.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!hidden.value) {
                    e.preventDefault();
                    alert('Vui lòng chọn một sản phẩm hợp lệ từ danh sách gợi ý.');
                    input.focus();
                }
            });
        }
    });
</script>
@endsection
