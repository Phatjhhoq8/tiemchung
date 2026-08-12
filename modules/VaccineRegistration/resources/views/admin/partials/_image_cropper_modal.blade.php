<!-- ================= MEDICARE IMAGE CROPPER MODAL ================= -->
<div id="medicare_cropper_modal" class="medicare-cropper-modal-overlay" style="display: none;">
    <div class="medicare-cropper-modal-container">
        <!-- Header -->
        <div class="medicare-cropper-header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="cropper-header-icon"><i data-lucide="crop"></i></span>
                <div>
                    <h3 class="cropper-title">Tùy Chỉnh & Cắt Hình Ảnh</h3>
                    <p class="cropper-subtitle">Kéo thả khung hoặc phóng to/thu nhỏ để chọn vùng hình ảnh hoàn hảo nhất</p>
                </div>
            </div>
            <button type="button" class="cropper-close-btn" onclick="window.closeMedicareCropperModal()">&times;</button>
        </div>

        <!-- Body / Workspace -->
        <div class="medicare-cropper-body">
            <div class="medicare-cropper-canvas-wrap">
                <img id="medicare_cropper_image" src="" alt="Vùng cắt ảnh" style="max-width: 100%;">
            </div>

            <!-- Toolbar điều khiển -->
            <div class="medicare-cropper-toolbar">
                <!-- Nhóm Tỷ lệ khung hình -->
                <div class="cropper-control-group">
                    <span class="cropper-group-label">Tỷ lệ khung:</span>
                    <div class="cropper-btn-group ratio-btn-group">
                        <button type="button" class="cropper-tool-btn ratio-btn" data-ratio="16/9" onclick="window.setCropperRatio(16/9, this)">16:9</button>
                        <button type="button" class="cropper-tool-btn ratio-btn" data-ratio="21/9" onclick="window.setCropperRatio(21/9, this)">21:9</button>
                        <button type="button" class="cropper-tool-btn ratio-btn" data-ratio="4/3" onclick="window.setCropperRatio(4/3, this)">4:3</button>
                        <button type="button" class="cropper-tool-btn ratio-btn" data-ratio="1/1" onclick="window.setCropperRatio(1/1, this)">1:1 (Vuông)</button>
                        <button type="button" class="cropper-tool-btn ratio-btn" data-ratio="free" onclick="window.setCropperRatio(NaN, this)">Tự do</button>
                    </div>
                </div>

                <!-- Nhóm Công cụ thao tác nhanh -->
                <div class="cropper-control-group">
                    <span class="cropper-group-label">Công cụ:</span>
                    <div class="cropper-btn-group">
                        <button type="button" class="cropper-tool-btn" onclick="window.cropperAction('zoom', 0.1)" title="Phóng to">
                            <i data-lucide="zoom-in"></i> Phóng to
                        </button>
                        <button type="button" class="cropper-tool-btn" onclick="window.cropperAction('zoom', -0.1)" title="Thu nhỏ">
                            <i data-lucide="zoom-out"></i> Thu nhỏ
                        </button>
                        <button type="button" class="cropper-tool-btn" onclick="window.cropperAction('rotate', -90)" title="Xoay trái 90°">
                            <i data-lucide="rotate-ccw"></i> Xoay trái
                        </button>
                        <button type="button" class="cropper-tool-btn" onclick="window.cropperAction('rotate', 90)" title="Xoay phải 90°">
                            <i data-lucide="rotate-cw"></i> Xoay phải
                        </button>
                        <button type="button" class="cropper-tool-btn" onclick="window.cropperAction('scaleX')" title="Lật ngang">
                            <i data-lucide="flip-horizontal"></i> Lật ngang
                        </button>
                        <button type="button" class="cropper-tool-btn" onclick="window.cropperAction('reset')" title="Đặt lại ban đầu">
                            <i data-lucide="refresh-cw"></i> Đặt lại
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="medicare-cropper-footer">
            <button type="button" class="btn-cropper-cancel" onclick="window.closeMedicareCropperModal()">
                Hủy bỏ
            </button>
            <button type="button" class="btn-cropper-apply" onclick="window.applyMedicareCropper()">
                <i data-lucide="check"></i> Áp Dụng Cắt Ảnh
            </button>
        </div>
    </div>
</div>

<style>
/* Cropper Modal CSS */
.medicare-cropper-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(15, 23, 42, 0.78);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    animation: cropperFadeIn 0.25s ease-out forwards;
}

@keyframes cropperFadeIn {
    from { opacity: 0; transform: scale(0.98); }
    to { opacity: 1; transform: scale(1); }
}

.medicare-cropper-modal-container {
    background: #ffffff;
    border-radius: 16px;
    width: 100%;
    max-width: 860px;
    max-height: 92vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.28);
    border: 1px solid rgba(226, 232, 240, 0.9);
    overflow: hidden;
}

.medicare-cropper-header {
    padding: 16px 24px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8fafc;
}

.cropper-header-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #fee2e2;
    color: var(--primary-color, #c8102e);
    display: flex;
    align-items: center;
    justify-content: center;
}

.cropper-title {
    margin: 0;
    font-size: 17px;
    font-weight: 800;
    color: #1e293b;
    font-family: var(--font-display);
}

.cropper-subtitle {
    margin: 2px 0 0 0;
    font-size: 12.5px;
    color: #64748b;
}

.cropper-close-btn {
    background: transparent;
    border: none;
    font-size: 26px;
    line-height: 1;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 8px;
    transition: all 0.2s ease;
}
.cropper-close-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}

.medicare-cropper-body {
    padding: 20px 24px;
    overflow-y: auto;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.medicare-cropper-canvas-wrap {
    width: 100%;
    height: 380px;
    background: #0f172a;
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    box-shadow: inset 0 2px 8px rgba(0,0,0,0.3);
}

.medicare-cropper-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    background: #f8fafc;
    padding: 12px 16px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}

.cropper-control-group {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.cropper-group-label {
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.cropper-btn-group {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.cropper-tool-btn {
    padding: 6px 12px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s ease;
}

.cropper-tool-btn i, .cropper-tool-btn svg {
    width: 14px;
    height: 14px;
}

.cropper-tool-btn:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
}

.cropper-tool-btn.active {
    background: var(--primary-color, #c8102e);
    border-color: var(--primary-color, #c8102e);
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(200, 16, 46, 0.25);
}

.medicare-cropper-footer {
    padding: 14px 24px;
    border-top: 1px solid #e2e8f0;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
}

.btn-cropper-cancel {
    padding: 10px 18px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-cropper-cancel:hover {
    background: #f1f5f9;
    color: #0f172a;
}

.btn-cropper-apply {
    padding: 10px 22px;
    border-radius: 8px;
    border: none;
    background: var(--primary-color, #c8102e);
    color: #ffffff;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(200, 16, 46, 0.25);
}
.btn-cropper-apply:hover {
    background: #a00d24;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(200, 16, 46, 0.32);
}

/* Nút Cắt lại đính kèm trong các preview container */
.btn-recrop-trigger {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: 8px;
    padding: 6px 12px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #0f172a;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.btn-recrop-trigger:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    color: var(--primary-color, #c8102e);
}
</style>

<script>
(function() {
    let activeCropper = null;
    let cropperOptions = null;
    let currentScaleX = 1;
    let currentScaleY = 1;

    /**
     * Mở Modal Cắt Ảnh
     * @param {Object} options 
     *  - file: File (bắt buộc)
     *  - defaultRatio: number | NaN (mặc định 16/9)
     *  - ratioName: string ('16:9', '21:9', '4:3', '1:1', 'free')
     *  - onCropComplete: function(blob, dataUrl, file)
     *  - onCancel: function()
     */
    window.openMedicareCropperModal = function(options) {
        if (!options || !options.file) return;
        cropperOptions = options;

        const modal = document.getElementById('medicare_cropper_modal');
        const imgEl = document.getElementById('medicare_cropper_image');
        if (!modal || !imgEl) return;

        // Reset flip state
        currentScaleX = 1;
        currentScaleY = 1;

        const reader = new FileReader();
        reader.onload = function(e) {
            imgEl.src = e.target.result;
            modal.style.display = 'flex';

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Hủy cropper cũ nếu có
            if (activeCropper) {
                activeCropper.destroy();
                activeCropper = null;
            }

            // Xác định tỷ lệ mặc định
            let targetRatio = typeof options.defaultRatio !== 'undefined' ? options.defaultRatio : 16 / 9;
            
            // Highlight ratio button
            document.querySelectorAll('.ratio-btn').forEach(btn => {
                const btnRatio = btn.getAttribute('data-ratio');
                if (options.ratioName && btnRatio === options.ratioName) {
                    btn.classList.add('active');
                } else if (!options.ratioName && ((targetRatio === 16/9 && btnRatio === '16/9') || (targetRatio === 1 && btnRatio === '1/1'))) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Khởi tạo Cropper.js
            setTimeout(() => {
                if (typeof Cropper === 'undefined') {
                    console.error('Cropper.js is not loaded.');
                    return;
                }

                activeCropper = new Cropper(imgEl, {
                    aspectRatio: targetRatio,
                    viewMode: 2, // Đảm bảo crop box luôn nằm trong canvas
                    dragMode: 'move',
                    autoCropArea: 0.95,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: true,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    responsive: true,
                });
            }, 100);
        };
        reader.readAsDataURL(options.file);
    };

    /**
     * Đổi tỷ lệ cắt
     */
    window.setCropperRatio = function(ratio, btnEl) {
        if (!activeCropper) return;
        activeCropper.setAspectRatio(ratio);

        document.querySelectorAll('.ratio-btn').forEach(btn => btn.classList.remove('active'));
        if (btnEl) btnEl.classList.add('active');
    };

    /**
     * Thao tác công cụ (zoom, rotate, flip, reset)
     */
    window.cropperAction = function(action, val) {
        if (!activeCropper) return;

        switch (action) {
            case 'zoom':
                activeCropper.zoom(val);
                break;
            case 'rotate':
                activeCropper.rotate(val);
                break;
            case 'scaleX':
                currentScaleX = currentScaleX === 1 ? -1 : 1;
                activeCropper.scaleX(currentScaleX);
                break;
            case 'reset':
                currentScaleX = 1;
                currentScaleY = 1;
                activeCropper.reset();
                break;
        }
    };

    /**
     * Đóng Modal
     */
    window.closeMedicareCropperModal = function() {
        const modal = document.getElementById('medicare_cropper_modal');
        if (modal) modal.style.display = 'none';

        if (activeCropper) {
            activeCropper.destroy();
            activeCropper = null;
        }

        if (cropperOptions && typeof cropperOptions.onCancel === 'function') {
            cropperOptions.onCancel();
        }
    };

    /**
     * Xác nhận cắt ảnh và trích xuất File
     */
    window.applyMedicareCropper = function() {
        if (!activeCropper || !cropperOptions) return;

        const canvas = activeCropper.getCroppedCanvas({
            maxWidth: 2560,
            maxHeight: 2560,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        if (!canvas) {
            alert('Không thể trích xuất ảnh đã cắt. Vui lòng thử lại.');
            return;
        }

        const originalFile = cropperOptions.file;
        const mimeType = originalFile.type || 'image/jpeg';
        const quality = 0.92;

        canvas.toBlob(function(blob) {
            if (!blob) return;

            // Tạo đối tượng File chuẩn HTML5 từ Blob
            const croppedFile = new File([blob], originalFile.name, {
                type: mimeType,
                lastModified: Date.now()
            });

            const dataUrl = canvas.toDataURL(mimeType, quality);

            if (typeof cropperOptions.onCropComplete === 'function') {
                cropperOptions.onCropComplete(blob, dataUrl, croppedFile);
            }

            window.closeMedicareCropperModal();
        }, mimeType, quality);
    };
})();
</script>
