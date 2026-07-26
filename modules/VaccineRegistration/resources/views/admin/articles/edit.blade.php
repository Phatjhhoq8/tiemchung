@extends('vaccine::layouts.admin')

@section('title', 'Chỉnh Sửa Bài Viết')

@section('admin_content')
<div style="width: 100%; padding: 20px 0;">
    <div style="margin-bottom: 24px;">
        <a href="{{ route('admin.articles.index') }}" style="color: #64748b; text-decoration: none; font-size: 14px; font-weight: 600;">← Quay lại danh sách</a>
        <h1 style="font-size: 24px; font-weight: 800; color: #1e293b; margin-top: 8px;">Chỉnh Sửa Bài Viết</h1>
    </div>

    <form action="{{ route('admin.articles.update', $article->id) }}" method="POST" enctype="multipart/form-data" style="background: #ffffff; padding: 32px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 8px;">Tiêu đề bài viết *</label>
            <input type="text" name="title" value="{{ old('title', $article->title) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 8px;">Chuyên mục *</label>
            <select name="category" required style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                <option value="Khuyến cáo Y tế" {{ old('category', $article->category) == 'Khuyến cáo Y tế' ? 'selected' : '' }}>Khuyến cáo Y tế</option>
                <option value="Vắc Xin Mới" {{ old('category', $article->category) == 'Vắc Xin Mới' ? 'selected' : '' }}>Vắc Xin Mới</option>
                <option value="Chăm Sóc Bé" {{ old('category', $article->category) == 'Chăm Sóc Bé' ? 'selected' : '' }}>Chăm Sóc Bé</option>
                <option value="Tin Tức Phòng Khám" {{ old('category', $article->category) == 'Tin Tức Phòng Khám' ? 'selected' : '' }}>Tin Tức Phòng Khám</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 8px;">Hình ảnh bài viết *</label>
            <input type="file" name="image_file" id="image_file" accept="image/*" style="display: none;">
            <input type="hidden" name="image" id="image_hidden" value="{{ old('image', $article->image) }}">
            
            <div id="image_dropzone" class="image-upload-zone" style="padding: 20px;">
                <div id="dropzone_prompt" style="{{ $article->image ? 'display: none;' : '' }}">
                    <i data-lucide="upload-cloud" style="width: 36px; height: 36px; color: var(--text-light); margin-bottom: 6px; display: inline-block;"></i>
                    <p style="font-weight: 600; color: var(--text-muted); margin: 0 0 4px 0; font-size: 14px;">Kéo thả hình ảnh vào đây hoặc click để tải lên</p>
                    <span style="font-size: 11px; color: var(--text-light);">Hỗ trợ: JPG, PNG, GIF, WEBP (Tối đa 2MB)</span>
                </div>
                <div id="image_preview_container" class="image-upload-preview-container" style="{{ $article->image ? 'display: block;' : '' }}">
                    <div class="image-upload-preview-wrapper">
                        <img id="image_preview" class="image-upload-preview" src="{{ $article->image ? asset('images/vaccines/' . $article->image) : '' }}" alt="Preview" style="max-height: 120px;">
                        <button type="button" id="btn_remove_image" class="image-upload-remove-btn" title="Xóa hình ảnh">
                            <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 8px;">Tóm tắt ngắn</label>
            <textarea name="summary" rows="3" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">{{ old('summary', $article->summary) }}</textarea>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 12px; font-size: 15px;">Thiết kế nội dung bài viết</label>
            
            <div id="blocks-container" style="display: flex; flex-direction: column; gap: 20px; margin-bottom: 24px;">
                <!-- Các khối (Khối văn bản / Khối hình ảnh) sẽ được tự động phân tích và sinh ra ở đây -->
            </div>
            
            <!-- Nút thêm khối -->
            <div style="display: flex; gap: 16px; justify-content: center; background-color: #f8fafc; padding: 20px; border-radius: 12px; border: 2px dashed #cbd5e1;">
                <button type="button" id="btn-add-text-block" style="background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 20px; font-weight: 600; color: #004b8f; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--accent-color)'; this.style.backgroundColor='#f0f9ff';" onmouseout="this.style.borderColor='#cbd5e1'; this.style.backgroundColor='#ffffff';">
                    <i data-lucide="file-text" style="width: 16px; height: 16px;"></i> + Thêm đoạn văn bản
                </button>
                <button type="button" id="btn-add-image-block" style="background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 20px; font-weight: 600; color: #eaaa00; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--secondary-color)'; this.style.backgroundColor='#fffdf0';" onmouseout="this.style.borderColor='#cbd5e1'; this.style.backgroundColor='#ffffff';">
                    <i data-lucide="image" style="width: 16px; height: 16px;"></i> + Thêm hình ảnh
                </button>
            </div>
            <textarea name="content" id="content-hidden" style="display: none;">{{ old('content', $article->content) }}</textarea>
        </div>

        <div style="margin-bottom: 24px; display: flex; gap: 24px;">
            <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; color: #334155; cursor: pointer;">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $article->is_published) ? 'checked' : '' }}> Hiển thị trên website
            </label>
            <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; color: #334155; cursor: pointer;">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $article->is_featured) ? 'checked' : '' }}> Bài viết nổi bật
            </label>
        </div>

        <button type="submit" style="background-color: var(--primary-color, #c8102e); color: #ffffff; padding: 12px 24px; border-radius: 8px; border: none; font-weight: 700; font-size: 14px; cursor: pointer;">Lưu thay đổi</button>
    </form>
</div>

<!-- TinyMCE 6 Core -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>

<!-- Block Builder Engine & Parser with TinyMCE Integration -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('blocks-container');
        const btnAddText = document.getElementById('btn-add-text-block');
        const btnAddImage = document.getElementById('btn-add-image-block');
        const form = document.querySelector('form');
        const contentHidden = document.getElementById('content-hidden');
        let blockCounter = 0;

        function initTinyMCE(id) {
            tinymce.init({
                selector: '#' + id,
                height: 380,
                menubar: false,
                plugins: 'lists link charmap preview searchreplace visualblocks code help wordcount',
                toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | forecolor backcolor | removeformat',
                branding: false,
                promotion: false,
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save();
                    });
                }
            });
        }

        function createBlockActions(blockId) {
            return `
                <div style="display: flex; gap: 6px; justify-content: flex-end;">
                    <button type="button" class="btn-move-up" onclick="moveBlockUp('${blockId}')" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; height: 28px; width: 28px;" title="Di chuyển lên">
                        <i data-lucide="arrow-up" style="width: 14px; height: 14px; color: #475569;"></i>
                    </button>
                    <button type="button" class="btn-move-down" onclick="moveBlockDown('${blockId}')" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; height: 28px; width: 28px;" title="Di chuyển xuống">
                        <i data-lucide="arrow-down" style="width: 14px; height: 14px; color: #475569;"></i>
                    </button>
                    <button type="button" class="btn-delete-block" onclick="deleteBlock('${blockId}')" style="background: #fee2e2; border: 1px solid #fecaca; border-radius: 4px; padding: 4px 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; height: 28px; width: 28px;" title="Xóa khối">
                        <i data-lucide="trash-2" style="width: 14px; height: 14px; color: #ef4444;"></i>
                    </button>
                </div>
            `;
        }

        window.addTextBlock = function(initialText = '') {
            blockCounter++;
            const blockId = 'block-' + blockCounter;
            const textareaId = 'textarea-block-' + blockCounter;
            
            const blockDiv = document.createElement('div');
            blockDiv.id = blockId;
            blockDiv.className = 'content-block block-type-text';
            blockDiv.style.backgroundColor = '#ffffff';
            blockDiv.style.border = '1px solid #cbd5e1';
            blockDiv.style.borderRadius = '12px';
            blockDiv.style.padding = '16px';
            blockDiv.style.boxShadow = '0 1px 3px rgba(0,0,0,0.02)';
            
            blockDiv.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                    <span style="font-weight: 700; color: #004b8f; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                        <i data-lucide="file-text" style="width: 16px; height: 16px;"></i> Khối đoạn văn bản
                    </span>
                    ${createBlockActions(blockId)}
                </div>
                <textarea id="${textareaId}" class="block-textarea form-control-modern" placeholder="Nhập nội dung đoạn văn bản tại đây..." rows="4" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 14px; font-family: inherit; font-size: 14px; line-height: 1.6;">${initialText}</textarea>
            `;
            
            container.appendChild(blockDiv);
            
            initTinyMCE(textareaId);

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        };

        window.addStaticImageBlock = function(imageUrl = '') {
            blockCounter++;
            const blockId = 'block-' + blockCounter;
            
            const blockDiv = document.createElement('div');
            blockDiv.id = blockId;
            blockDiv.className = 'content-block block-type-image';
            blockDiv.style.backgroundColor = '#ffffff';
            blockDiv.style.border = '1px solid #cbd5e1';
            blockDiv.style.borderRadius = '12px';
            blockDiv.style.padding = '16px';
            blockDiv.style.boxShadow = '0 1px 3px rgba(0,0,0,0.02)';
            
            blockDiv.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                    <span style="font-weight: 700; color: #eaaa00; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                        <i data-lucide="image" style="width: 16px; height: 16px;"></i> Khối hình ảnh
                    </span>
                    ${createBlockActions(blockId)}
                </div>
                <div class="block-image-upload-wrapper" style="text-align: center;">
                    <input type="file" class="block-image-file-input" accept="image/*" style="display: none;" onchange="uploadBlockImage(this, '${blockId}')">
                    <input type="hidden" class="block-image-url-value" value="${imageUrl}">
                    
                    <div class="block-image-dropzone image-upload-zone" onclick="triggerBlockFileInput('${blockId}')" style="padding: 16px; border: 2px dashed #cbd5e1; border-radius: 8px; cursor: pointer; background: #f8fafc; transition: all 0.2s;">
                        <div class="block-image-prompt" style="${imageUrl ? 'display: none;' : ''}">
                            <i data-lucide="upload-cloud" style="width: 32px; height: 32px; color: #64748b; margin-bottom: 4px; display: inline-block;"></i>
                            <p style="font-weight: 600; color: #475569; margin: 0 0 2px 0; font-size: 13px;">Nhấp để tải ảnh từ thiết bị lên</p>
                            <span style="font-size: 11px; color: #94a3b8;">Hỗ trợ: JPG, PNG, GIF, WEBP</span>
                        </div>
                        <div class="block-image-preview-container" style="${imageUrl ? 'display: block;' : 'display: none;'}">
                            <img class="block-image-preview-img" src="${imageUrl}" alt="Preview" style="max-height: 140px; border-radius: 6px; border: 1px solid #cbd5e1; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: inline-block;">
                            <p style="font-size: 12px; color: #64748b; margin: 4px 0 0 0;">Nhấp để đổi ảnh khác</p>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(blockDiv);
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        };

        window.triggerBlockFileInput = function(blockId) {
            const block = document.getElementById(blockId);
            const fileInput = block.querySelector('.block-image-file-input');
            fileInput.click();
        };

        window.uploadBlockImage = function(input, blockId) {
            if (input.files.length === 0) return;
            const file = input.files[0];
            if (!file.type.startsWith('image/')) {
                alert('Vui lòng chọn hình ảnh định dạng JPG, PNG, GIF, WEBP.');
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                alert('Dung lượng hình ảnh không được vượt quá 2MB.');
                return;
            }

            const block = document.getElementById(blockId);
            const prompt = block.querySelector('.block-image-prompt');
            const previewContainer = block.querySelector('.block-image-preview-container');
            const previewImg = block.querySelector('.block-image-preview-img');
            const hiddenUrlInput = block.querySelector('.block-image-url-value');

            const formData = new FormData();
            formData.append('file', file);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route("admin.articles.upload-image") }}');
            xhr.setRequestHeader('X-CSRF-Token', '{{ csrf_token() }}');

            prompt.innerHTML = `
                <i data-lucide="loader" class="animate-spin" style="width: 32px; height: 32px; color: var(--accent-color); margin-bottom: 4px; display: inline-block;"></i>
                <p style="font-weight: 600; color: #475569; margin: 0 0 2px 0; font-size: 13px;">Đang tải ảnh lên...</p>
            `;
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.location) {
                        hiddenUrlInput.value = response.location;
                        previewImg.src = response.location;
                        
                        prompt.style.display = 'none';
                        previewContainer.style.display = 'block';
                    } else {
                        alert('Có lỗi xảy ra khi upload ảnh.');
                        resetPrompt();
                    }
                } else {
                    alert('Lỗi tải ảnh lên server.');
                    resetPrompt();
                }
            };

            xhr.onerror = function() {
                alert('Không thể kết nối đến server.');
                resetPrompt();
            };

            xhr.send(formData);

            function resetPrompt() {
                prompt.innerHTML = `
                    <i data-lucide="upload-cloud" style="width: 32px; height: 32px; color: #64748b; margin-bottom: 4px; display: inline-block;"></i>
                    <p style="font-weight: 600; color: #475569; margin: 0 0 2px 0; font-size: 13px;">Nhấp để tải ảnh từ thiết bị lên</p>
                    <span style="font-size: 11px; color: #94a3b8;">Hỗ trợ: JPG, PNG, GIF, WEBP</span>
                `;
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        };

        window.moveBlockUp = function(blockId) {
            const block = document.getElementById(blockId);
            const prev = block.previousElementSibling;
            if (prev) {
                const currentTextarea = block.querySelector('.block-textarea');
                const prevTextarea = prev.querySelector('.block-textarea');

                if (currentTextarea) {
                    tinymce.execCommand('mceRemoveEditor', false, currentTextarea.id);
                }
                if (prevTextarea) {
                    tinymce.execCommand('mceRemoveEditor', false, prevTextarea.id);
                }

                container.insertBefore(block, prev);

                if (currentTextarea) {
                    initTinyMCE(currentTextarea.id);
                }
                if (prevTextarea) {
                    initTinyMCE(prevTextarea.id);
                }
            }
        };

        window.moveBlockDown = function(blockId) {
            const block = document.getElementById(blockId);
            const next = block.nextElementSibling;
            if (next) {
                const currentTextarea = block.querySelector('.block-textarea');
                const nextTextarea = next.querySelector('.block-textarea');

                if (currentTextarea) {
                    tinymce.execCommand('mceRemoveEditor', false, currentTextarea.id);
                }
                if (nextTextarea) {
                    tinymce.execCommand('mceRemoveEditor', false, nextTextarea.id);
                }

                container.insertBefore(next, block);

                if (currentTextarea) {
                    initTinyMCE(currentTextarea.id);
                }
                if (nextTextarea) {
                    initTinyMCE(nextTextarea.id);
                }
            }
        };

        window.deleteBlock = function(blockId) {
            if (confirm('Bạn có chắc chắn muốn xóa khối này?')) {
                const block = document.getElementById(blockId);
                const textarea = block.querySelector('.block-textarea');
                if (textarea) {
                    tinymce.execCommand('mceRemoveEditor', false, textarea.id);
                }
                block.remove();
            }
        };

        btnAddText.addEventListener('click', () => addTextBlock());
        btnAddImage.addEventListener('click', () => addStaticImageBlock());

        // Phân tích HTML cũ từ database để tự động sinh lại các khối tương ứng
        function parseExistingContent() {
            const existingHtml = contentHidden.value.trim();
            if (existingHtml) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(existingHtml, 'text/html');
                const nodes = doc.body.childNodes;
                let currentText = '';

                nodes.forEach(node => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        const img = node.querySelector('img') || (node.tagName === 'IMG' ? node : null);
                        if (img) {
                            if (currentText.trim()) {
                                addTextBlock(currentText.trim());
                                currentText = '';
                            }
                            addStaticImageBlock(img.getAttribute('src'));
                        } else {
                            currentText += node.outerHTML + '\n';
                        }
                    } else if (node.nodeType === Node.TEXT_NODE) {
                        const val = node.nodeValue.trim();
                        if (val) {
                            currentText += `<p>${val}</p>\n`;
                        }
                    }
                });

                if (currentText.trim()) {
                    addTextBlock(currentText.trim());
                }
            } else {
                addTextBlock();
            }
        }

        form.addEventListener('submit', function(e) {
            let htmlContent = '';
            const blocks = container.querySelectorAll('.content-block');
            
            blocks.forEach(block => {
                if (block.classList.contains('block-type-text')) {
                    const textarea = block.querySelector('.block-textarea');
                    let text = '';
                    if (textarea && tinymce.get(textarea.id)) {
                        text = tinymce.get(textarea.id).getContent().trim();
                    } else if (textarea) {
                        text = textarea.value.trim();
                    }
                    if (text) {
                        htmlContent += `${text}\n`;
                    }
                } else if (block.classList.contains('block-type-image')) {
                    const url = block.querySelector('.block-image-url-value').value;
                    if (url) {
                        htmlContent += `<div style="text-align: center; margin: 2rem 0;"><img src="${url}" alt="Ảnh bài viết" style="max-width: 100%; height: auto; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); display: inline-block;"></div>\n`;
                    }
                }
            });

            contentHidden.value = htmlContent;
        });

        parseExistingContent();
    });
</script>
@endsection
