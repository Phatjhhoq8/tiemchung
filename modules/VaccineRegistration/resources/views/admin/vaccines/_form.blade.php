@if ($errors->any())
    <div class="alert alert-danger" style="margin-bottom: 24px; padding: 16px; border-radius: 8px; background-color: #fde8e8; color: #9b1c1c; border: 1px solid #fbd5d5;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ===== THÔNG TIN CƠ BẢN ===== --}}
<div class="card-modern">
    <h3 style="font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
        <i data-lucide="info" style="width: 18px; height: 18px; color: var(--accent-color);"></i>@php
    // Dùng chung cho cả thêm mới và chỉnh sửa
@endphp

<style>
    /* CSS cho các trường bị disabled trong form */
    .form-control-modern:disabled,
    .form-control-modern[disabled],
    textarea:disabled,
    select:disabled {
        background-color: #f1f5f9 !important;
        color: #64748b !important;
        border-color: #cbd5e1 !important;
        cursor: not-allowed !important;
        opacity: 0.85 !important;
    }
    
    /* Hover cấm trên nhãn checkbox/radio bị disabled */
    label[style*="cursor: pointer"]:has(input[disabled]) {
        cursor: not-allowed !important;
    }
</style>
        Thông tin cơ bản
    </h3>
    <div class="form-grid-2">
        <!-- Tên Vắc xin -->
        <div class="form-group-modern" style="grid-column: span 2; margin-bottom: 0;">
            <label for="name" class="form-label-modern">Tên vắc xin / Gói vắc xin <span style="color: #ef4444;">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $vaccine->name) }}" required class="form-control-modern" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>
        </div>

        @if(isset($centers) && ($isSuperAdmin ?? false))
        <div class="form-group-modern" style="grid-column: span 2; margin-bottom: 0;">
            <label for="center_id" class="form-label-modern">Chi nhánh áp dụng giá/tồn kho <span style="color: #ef4444;">*</span></label>
            <select name="center_id" id="center_id" required class="form-control-modern" style="background-image: none;" @if($vaccine->exists) onchange="if (this.value) window.location.href = '{{ route('admin.vaccines.edit', $vaccine->id) }}?center_id=' + encodeURIComponent(this.value)" @endif>
                <option value="">-- Chọn chi nhánh --</option>
                @foreach($centers as $center)
                    <option value="{{ $center->id }}" {{ (string) old('center_id', $selectedCenterId ?? null) === (string) $center->id ? 'selected' : '' }}>{{ $center->name }} - {{ $center->phone }}</option>
                @endforeach
            </select>
            <small style="display:block; margin-top:6px; color:#64748b;">Thông tin giá, ưu đãi, tồn kho và nổi bật sẽ lưu riêng cho chi nhánh này.</small>
        </div>
        @endif
        @if(!($isSuperAdmin ?? false))
            <input type="hidden" name="center_id" value="{{ $adminUser?->center_id }}">
        @endif

        <!-- Phân loại -->
        <div class="form-group-modern" style="margin-bottom: 0;">
            <label for="type" class="form-label-modern">Phân loại <span style="color: #ef4444;">*</span></label>
            <select name="type" id="type" required class="form-control-modern" style="background-image: none;" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>
                <option value="single" {{ old('type', $vaccine->type) === 'single' ? 'selected' : '' }}>Vắc xin lẻ</option>
                <option value="package" {{ old('type', $vaccine->type) === 'package' ? 'selected' : '' }}>Gói vắc xin</option>
            </select>
        </div>

        <!-- Nhóm bệnh -->
        <div class="form-group-modern" style="margin-bottom: 0;">
            <label for="category" class="form-label-modern">Nhóm bệnh</label>
            <input type="text" name="category" id="category" value="{{ old('category', $vaccine->category) }}" placeholder="VD: Cúm, HPV, Viêm gan, Bạch hầu..." list="category-list" class="form-control-modern" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>
            @if(isset($categories) && $categories->count())
            <datalist id="category-list">
                @foreach($categories as $cat)
                    <option value="{{ $cat }}">
                @endforeach
            </datalist>
            @endif
        </div>

        <!-- Bệnh phòng ngừa -->
        <div class="form-group-modern" style="grid-column: span 2; margin-bottom: 0;">
            <label for="disease_prevention" class="form-label-modern">Bệnh phòng ngừa <span style="color: #ef4444;">*</span></label>
            <input type="text" name="disease_prevention" id="disease_prevention" value="{{ old('disease_prevention', $vaccine->disease_prevention) }}" placeholder="VD: Bạch hầu, Ho gà, Uốn ván, Bại liệt..." required class="form-control-modern" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>
        </div>
    </div>
</div>

{{-- ===== GIÁ CẢ & SỐ LIỀU ===== --}}
<div class="card-modern">
    <h3 style="font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
        <i data-lucide="dollar-sign" style="width: 18px; height: 18px; color: var(--accent-color);"></i>
        Giá cả & Phác đồ
    </h3>
    <div class="form-grid-2">
        <!-- Giá tiêm -->
        <div class="form-group-modern" style="margin-bottom: 0;">
            <label for="price" class="form-label-modern">Giá bán lẻ / liều (VND) <span style="color: #ef4444;">*</span></label>
            <input type="number" name="price" id="price" value="{{ old('price', $vaccine->price) }}" required min="0" class="form-control-modern">
        </div>

        <!-- Giá ưu đãi -->
        <div class="form-group-modern" style="margin-bottom: 0;">
            <label for="sale_price" class="form-label-modern">
                Giá ưu đãi (VND)
                <span style="font-size: 12px; color: var(--text-light); font-weight: 400; margin-left: 4px;">— Để trống nếu không giảm giá</span>
            </label>
            <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price', $vaccine->sale_price) }}" min="0" placeholder="VD: 995000" class="form-control-modern">
        </div>

        <!-- Số mũi tiêm -->
        <div class="form-group-modern" style="margin-bottom: 0;">
            <label for="doses" class="form-label-modern">Số mũi tiêm theo phác đồ <span style="color: #ef4444;">*</span></label>
            <input type="number" name="doses" id="doses" value="{{ old('doses', $vaccine->doses ?: 1) }}" required min="1" class="form-control-modern" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>
        </div>

        <!-- Số lượng tồn kho -->
        <div class="form-group-modern" style="margin-bottom: 0;">
            <label for="stock_quantity" class="form-label-modern">Số lượng tồn kho <span style="color: #ef4444;">*</span></label>
            <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $vaccine->stock_quantity ?? 0) }}" required min="0" placeholder="VD: 50" class="form-control-modern">
        </div>

        <div class="form-group-modern" style="margin-bottom: 0;">
            <label for="center_is_active" class="form-label-modern">Kinh doanh tại chi nhánh <span style="color: #ef4444;">*</span></label>
            <select name="center_is_active" id="center_is_active" required class="form-control-modern" style="background-image: none;">
                <option value="1" {{ (int) old('center_is_active', $vaccine->center_is_active ?? 1) === 1 ? 'selected' : '' }}>Đang kinh doanh</option>
                <option value="0" {{ (int) old('center_is_active', $vaccine->center_is_active ?? 1) === 0 ? 'selected' : '' }}>Tạm ngưng</option>
            </select>
            <small style="display:block; margin-top:6px; color:#64748b;">Tạm ngưng sẽ giữ nguyên dữ liệu nhưng không cho đặt lịch hoặc nhập kho tại chi nhánh này.</small>
        </div>
    </div>
</div>

{{-- ===== NGUỒN GỐC & QUY CÁCH ===== --}}
<div class="card-modern">
    <h3 style="font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
        <i data-lucide="globe" style="width: 18px; height: 18px; color: var(--accent-color);"></i>
        Nguồn gốc & Quy cách
    </h3>
    <div class="form-grid-2">
        <!-- Hãng sản xuất -->
        <div class="form-group-modern" style="margin-bottom: 0;">
            <label for="manufacturer" class="form-label-modern">Hãng sản xuất</label>
            <input type="text" name="manufacturer" id="manufacturer" value="{{ old('manufacturer', $vaccine->manufacturer) }}" placeholder="VD: MSD, Sanofi, GlaxoSmithKline..." class="form-control-modern" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>
        </div>

        <!-- Quốc gia nguồn gốc -->
        <div class="form-group-modern" style="margin-bottom: 0;">
            <label for="origin" class="form-label-modern">Nước sản xuất (Nguồn gốc)</label>
            <input type="text" name="origin" id="origin" value="{{ old('origin', $vaccine->origin) }}" placeholder="VD: Mỹ, Pháp, Bỉ, Đức..." class="form-control-modern" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>
        </div>

        <!-- Quy cách đóng gói -->
        <div class="form-group-modern" style="grid-column: span 2; margin-bottom: 0;">
            <label for="dosage" class="form-label-modern">Quy cách liều lượng (Hàm lượng/Đóng gói)</label>
            <input type="text" name="dosage" id="dosage" value="{{ old('dosage', $vaccine->dosage) }}" placeholder="VD: Hộp 1 bơm tiêm đóng sẵn gia liều 0.5ml dung dịch..." class="form-control-modern" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>
        </div>
    </div>
</div>

{{-- ===== HÌNH ẢNH & HIỂN THỊ ===== --}}
<div class="card-modern">
    <h3 style="font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
        <i data-lucide="image" style="width: 18px; height: 18px; color: var(--accent-color);"></i>
        Hình ảnh & Hiển thị
    </h3>
    <div class="form-grid-2">
        <!-- Tải lên hình ảnh -->
        <div class="form-group-modern" style="margin-bottom: 0; grid-column: span 2;">
            <label class="form-label-modern">Hình ảnh vắc xin / Gói vắc xin</label>
            <input type="file" name="image_file" id="image_file" accept="image/*" style="display: none;" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>
            <input type="hidden" name="image" id="image_hidden" value="{{ old('image', $vaccine->image) }}">
            
            <div id="image_dropzone" class="image-upload-zone {{ !($isSuperAdmin ?? false) ? 'disabled-zone' : '' }}" style="{{ !($isSuperAdmin ?? false) ? 'opacity: 0.7; cursor: not-allowed; background-color: #f1f5f9; border-color: #cbd5e1;' : '' }}">
                <div id="dropzone_prompt" style="{{ $vaccine->image ? 'display: none;' : '' }}">
                    <i data-lucide="upload-cloud" style="width: 40px; height: 40px; color: var(--text-light); margin-bottom: 8px; display: inline-block;"></i>
                    <p style="font-weight: 600; color: var(--text-muted); margin: 0 0 4px 0;">Kéo thả hình ảnh vào đây hoặc click để tải lên</p>
                    <span style="font-size: 12px; color: var(--text-light);">Hỗ trợ: JPG, PNG, GIF, WEBP (Tối đa 2MB)</span>
                </div>
                <div id="image_preview_container" class="image-upload-preview-container" style="{{ $vaccine->image ? 'display: block;' : '' }}">
                    <div class="image-upload-preview-wrapper">
                        <img id="image_preview" class="image-upload-preview" src="{{ $vaccine->image ? asset('images/vaccines/' . $vaccine->image) : '' }}" alt="Preview">
                        @if($isSuperAdmin ?? false)
                        <button type="button" id="btn_remove_image" class="image-upload-remove-btn" title="Xóa hình ảnh">
                            <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Thứ tự hiển thị -->
        <div class="form-group-modern" style="margin-bottom: 0; grid-column: span 2;">
            <label for="sort_order" class="form-label-modern">
                Thứ tự hiển thị
                <span style="font-size: 12px; color: var(--text-light); font-weight: 400; margin-left: 4px;">— Số nhỏ hơn hiển thị trước</span>
            </label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $vaccine->sort_order ?? 0) }}" min="0" class="form-control-modern">
        </div>

        <!-- Vắc xin nổi bật -->
        <div class="form-group-modern" style="grid-column: span 2; margin-bottom: 0;">
            <label style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer; padding: 12px 16px; border-radius: 8px; border: 1px solid #cbd5e1; background: #f8fafc; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--accent-color)'" onmouseout="this.style.borderColor='#cbd5e1'">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $vaccine->is_featured) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--primary-color); cursor: pointer;">
                <span style="font-weight: 600; color: #475569; font-family: var(--font-display);">Đánh dấu là vắc xin nổi bật</span>
                <span style="font-size: 12px; color: var(--text-light); font-weight: 400;">— Hiển thị ưu tiên trên trang chủ</span>
            </label>
        </div>
    </div>
</div>

{{-- ===== MÔ TẢ CHI TIẾT ===== --}}
<div class="card-modern" style="margin-bottom: 0;">
    <h3 style="font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
        <i data-lucide="file-text" style="width: 18px; height: 18px; color: var(--accent-color);"></i>
        Mô tả chi tiết
    </h3>
    <div class="form-group-modern" style="margin-bottom: 0;">
        <textarea name="description" id="description" rows="5" placeholder="Nhập công dụng chi tiết, hướng dẫn, lưu ý phác đồ tiêm chủng..." class="form-control-modern" style="font-family: inherit; resize: vertical;" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>{{ old('description', $vaccine->description) }}</textarea>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropzone = document.getElementById('image_dropzone');
        const fileInput = document.getElementById('image_file');
        const hiddenInput = document.getElementById('image_hidden');
        const promptBlock = document.getElementById('dropzone_prompt');
        const previewContainer = document.getElementById('image_preview_container');
        const previewImg = document.getElementById('image_preview');
        const removeBtn = document.getElementById('btn_remove_image');

        if (!dropzone) return;

        // Click to choose file
        dropzone.addEventListener('click', function(e) {
            if (dropzone.classList.contains('disabled-zone')) return;
            if (e.target.closest('#btn_remove_image')) return;
            fileInput.click();
        });

        // File input change
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });

        // Drag & Drop events
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(e) {
                if (dropzone.classList.contains('disabled-zone')) {
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }
                highlight(e);
            }, false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(e) {
                if (dropzone.classList.contains('disabled-zone')) {
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }
                unhighlight(e);
            }, false);
        });

        function highlight(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.borderColor = 'var(--accent-color)';
            dropzone.style.backgroundColor = '#f1f5f9';
        }

        function unhighlight(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.borderColor = 'var(--border-color)';
            dropzone.style.backgroundColor = '#f8fafc';
        }

        dropzone.addEventListener('drop', function(e) {
            if (dropzone.classList.contains('disabled-zone')) {
                e.preventDefault();
                e.stopPropagation();
                return;
            }
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
            fileInput.files = files; // Update file input files
        });

        function handleFiles(files) {
            if (files.length === 0) return;
            const file = files[0];
            if (!file.type.startsWith('image/')) {
                alert('Vui lòng chỉ tải lên file hình ảnh.');
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                alert('Dung lượng hình ảnh không được vượt quá 2MB.');
                return;
            }

            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = function() {
                previewImg.src = reader.result;
                promptBlock.style.display = 'none';
                previewContainer.style.display = 'block';
                hiddenInput.value = ''; // Reset hidden value because a new file is uploaded
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        }

        // Remove image action
        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileInput.value = '';
                hiddenInput.value = '';
                previewImg.src = '';
                previewContainer.style.display = 'none';
                promptBlock.style.display = 'block';
            });
        }
    });
</script>
@endsection
