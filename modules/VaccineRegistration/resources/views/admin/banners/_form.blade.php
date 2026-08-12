@if ($errors->any())
    <div class="alert alert-danger" style="margin-bottom: 24px; padding: 16px; border-radius: 8px; background-color: #fde8e8; color: #9b1c1c; border: 1px solid #fbd5d5;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-grid-2" style="margin-bottom: 30px;">
    <!-- Tiêu đề -->
    <div class="form-group" style="grid-column: span 2;">
        <label for="title" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Tiêu đề banner <span style="color: #ef4444;">*</span></label>
        <input type="text" name="title" id="title" value="{{ old('title', $banner->title) }}" required placeholder="VD: Tiêm vắc xin – Bảo vệ gia đình bạn" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <!-- Phụ đề -->
    <div class="form-group" style="grid-column: span 2;">
        <label for="subtitle" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Phụ đề / Mô tả ngắn</label>
        <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" placeholder="VD: Ưu đãi 7% khi đăng ký tiêm chủng" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <!-- Tải lên hình ảnh Banner -->
    <div class="form-group" style="grid-column: span 2;">
        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Hình ảnh Banner <span style="font-size: 13px; font-weight: 400; color: #64748b;">(Không bắt buộc - mặc định dùng ảnh hệ thống)</span></label>
        <input type="file" name="image_file" id="image_file" accept="image/*" style="display: none;">
        <input type="hidden" name="image_url" id="image_url" value="{{ old('image_url', $banner->image_url) }}">
        
        <div id="image_dropzone" class="image-upload-zone" style="padding: 20px;">
            <div id="dropzone_prompt" style="{{ $banner->image_url ? 'display: none;' : '' }}">
                <i data-lucide="upload-cloud" style="width: 36px; height: 36px; color: var(--text-light); margin-bottom: 6px; display: inline-block;"></i>
                <p style="font-weight: 600; color: var(--text-muted); margin: 0 0 4px 0; font-size: 14px;">Kéo thả hình ảnh banner vào đây hoặc click để tải lên</p>
                <span style="font-size: 11px; color: var(--text-light);">Hỗ trợ: JPG, PNG, GIF, WEBP (Tối đa 2MB)</span>
            </div>
            <div id="image_preview_container" class="image-upload-preview-container" style="{{ $banner->image_url ? 'display: block;' : '' }}">
                <div class="image-upload-preview-wrapper" style="text-align: center;">
                    <img id="image_preview" class="image-upload-preview" src="{{ $banner->image_url ? asset($banner->image_url) : '' }}" alt="Xem trước hình ảnh" style="max-height: 140px; border-radius: 8px;">
                    <button type="button" id="btn_remove_image" class="image-upload-remove-btn" title="Xóa hình ảnh">
                        <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                    </button>
                </div>
                <div style="text-align: center; margin-top: 6px;">
                    <button type="button" id="btn_recrop_image" class="btn-recrop-trigger" style="display: none;">
                        <i data-lucide="crop"></i> Cắt lại hình ảnh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- URL liên kết -->
    <div class="form-group">
        <label for="link_url" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Liên kết khi click</label>
        <input type="text" name="link_url" id="link_url" value="{{ old('link_url', $banner->link_url) }}" placeholder="VD: /vaccines hoặc /register" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <!-- Thứ tự -->
    <div class="form-group">
        <label for="sort_order" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Thứ tự hiển thị</label>
        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" min="0" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <!-- Trạng thái -->
    <div class="form-group" style="grid-column: span 2;">
        <label style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer; padding: 12px 16px; border-radius: 8px; border: 1px solid #cbd5e1; background: #f8fafc;">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #10b981; cursor: pointer;">
            <span style="font-weight: 600; color: #475569;">✅ Hiển thị banner trên trang chủ</span>
        </label>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropzone = document.getElementById('image_dropzone');
        const fileInput = document.getElementById('image_file');
        const hiddenInput = document.getElementById('image_url');
        const promptBlock = document.getElementById('dropzone_prompt');
        const previewContainer = document.getElementById('image_preview_container');
        const previewImg = document.getElementById('image_preview');
        const removeBtn = document.getElementById('btn_remove_image');
        const recropBtn = document.getElementById('btn_recrop_image');
        let currentRawFile = null;

        if (!dropzone) return;

        // Click to choose file
        dropzone.addEventListener('click', function(e) {
            if (e.target.closest('#btn_remove_image') || e.target.closest('#btn_recrop_image')) return;
            fileInput.click();
        });

        // File input change
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                handleFiles(this.files);
            }
        });

        // Drag & Drop events
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, highlight, false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, unhighlight, false);
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
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                handleFiles(files);
            }
        });

        function handleFiles(files) {
            if (files.length === 0) return;
            const file = files[0];
            if (!file.type.startsWith('image/')) {
                window.AppDialog ? window.AppDialog.alert('Vui lòng chỉ tải lên tệp hình ảnh.') : alert('Vui lòng chỉ tải lên tệp hình ảnh.');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                window.AppDialog ? window.AppDialog.alert('Dung lượng hình ảnh không được vượt quá 5 MB.') : alert('Dung lượng hình ảnh không được vượt quá 5 MB.');
                return;
            }

            currentRawFile = file;

            // Mở Modal Cắt Ảnh
            if (typeof window.openMedicareCropperModal === 'function') {
                window.openMedicareCropperModal({
                    file: file,
                    defaultRatio: 16 / 9,
                    ratioName: '16:9',
                    onCropComplete: function(croppedBlob, croppedDataUrl, croppedFile) {
                        // Gán File đã cắt vào input file bằng DataTransfer
                        const dt = new DataTransfer();
                        dt.items.add(croppedFile);
                        fileInput.files = dt.files;

                        // Cập nhật xem trước
                        previewImg.src = croppedDataUrl;
                        promptBlock.style.display = 'none';
                        previewContainer.style.display = 'block';
                        if (recropBtn) recropBtn.style.display = 'inline-flex';
                        hiddenInput.value = '';

                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    }
                });
            } else {
                // Fallback nếu modal chưa load
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onloadend = function() {
                    previewImg.src = reader.result;
                    promptBlock.style.display = 'none';
                    previewContainer.style.display = 'block';
                    hiddenInput.value = '';
                };
            }
        }

        // Nút cắt lại ảnh
        if (recropBtn) {
            recropBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (currentRawFile && typeof window.openMedicareCropperModal === 'function') {
                    window.openMedicareCropperModal({
                        file: currentRawFile,
                        defaultRatio: 16 / 9,
                        ratioName: '16:9',
                        onCropComplete: function(croppedBlob, croppedDataUrl, croppedFile) {
                            const dt = new DataTransfer();
                            dt.items.add(croppedFile);
                            fileInput.files = dt.files;
                            previewImg.src = croppedDataUrl;
                        }
                    });
                }
            });
        }

        // Remove image action
        removeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            fileInput.value = '';
            hiddenInput.value = '';
            previewImg.src = '';
            currentRawFile = null;
            previewContainer.style.display = 'none';
            if (recropBtn) recropBtn.style.display = 'none';
            promptBlock.style.display = 'block';
        });
    });
</script>
@endsection
