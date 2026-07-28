@extends('vaccine::layouts.admin')

@section('title', 'Thêm Bài Viết Mới')

@section('admin_content')
<style>
    /* Styling for Colab-like Notebook Cells */
    .notebook-cell {
        position: relative;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 16px;
        margin: 12px 0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .notebook-cell:hover,
    .notebook-cell.active-cell {
        border-color: var(--accent-color, #004b8f);
        box-shadow: 0 4px 12px rgba(0, 75, 143, 0.08);
    }
    .cell-controls {
        position: absolute;
        top: -14px;
        right: 16px;
        display: none;
        gap: 4px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 2px 4px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        z-index: 99;
    }
    .notebook-cell:hover .cell-controls,
    .notebook-cell.active-cell .cell-controls {
        display: flex;
    }
    .cell-btn {
        background: #ffffff;
        border: none;
        border-radius: 4px;
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #475569;
        transition: background 0.15s, color 0.15s;
    }
    .cell-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .cell-btn.btn-delete:hover {
        background: #fee2e2;
        color: #ef4444;
    }
    
    .insert-btn {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 20px;
        padding: 6px 16px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #475569;
        transition: all 0.15s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .insert-btn:hover {
        background: #f8fafc;
        border-color: var(--accent-color, #004b8f);
        color: var(--accent-color, #004b8f);
        box-shadow: 0 2px 6px rgba(0, 75, 143, 0.08);
    }
</style>

<div style="width: 100%; padding: 20px 0;">
    <div style="margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ route('admin.articles.index') }}" style="color: #64748b; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Quay lại danh sách
        </a>
        <h1 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0;">Thêm Bài Viết Mới</h1>
    </div>

    @if($errors->any())
        <div style="background-color: #fef2f2; border: 1px solid #fee2e2; color: #991b1b; padding: 14px 20px; border-radius: 8px; margin-bottom: 24px; font-size: 14px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="articleCreateForm" action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div style="display: flex; gap: 28px; flex-wrap: wrap; align-items: flex-start;">
            
            <!-- Left Side (30% Width): Metadata & Configuration -->
            <div style="width: 340px; display: flex; flex-direction: column; gap: 24px; flex-shrink: 0;">
                
                <!-- Action Publish Card -->
                <div style="background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <h3 style="font-size: 15px; font-weight: 800; color: #1e293b; margin: 0 0 16px 0; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                        Đăng bài viết
                    </h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 20px;">
                        <label style="display: flex; align-items: center; gap: 10px; font-weight: 600; color: #334155; cursor: pointer; font-size: 14px;">
                            <input type="checkbox" name="is_published" value="1" checked style="width: 16px; height: 16px; border-radius: 4px; border: 1px solid #cbd5e1; accent-color: var(--primary-color);"> 
                            Hiển thị trên website
                        </label>
                        <label style="display: flex; align-items: center; gap: 10px; font-weight: 600; color: #334155; cursor: pointer; font-size: 14px;">
                            <input type="checkbox" name="is_featured" value="1" style="width: 16px; height: 16px; border-radius: 4px; border: 1px solid #cbd5e1; accent-color: var(--primary-color);"> 
                            Bài viết nổi bật
                        </label>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button type="submit" style="width: 100%; background-color: var(--primary-color, #c8102e); color: #ffffff; padding: 12px; border-radius: 8px; border: none; font-weight: 700; font-size: 14px; cursor: pointer; text-align: center; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='var(--primary-color-hover, #a00d24)'" onmouseout="this.style.backgroundColor='var(--primary-color, #c8102e)'">
                            Lưu bài viết
                        </button>
                        <a href="{{ route('admin.articles.index') }}" style="width: 100%; border: 1px solid #cbd5e1; background-color: #ffffff; color: #475569; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 13.5px; cursor: pointer; text-align: center; text-decoration: none; display: block; box-sizing: border-box; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='#ffffff'">
                            Hủy bỏ
                        </a>
                    </div>
                </div>
                
                <!-- Category Select Card -->
                <div style="background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <h3 style="font-size: 15px; font-weight: 800; color: #1e293b; margin: 0 0 14px 0; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                        Chuyên mục *
                    </h3>
                    <select name="category" required style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: #ffffff;" onfocus="this.style.borderColor='var(--accent-color)'" onblur="this.style.borderColor='#cbd5e1'">
                        <option value="Tin Nóng Y Học">Tin Nóng Y Học</option>
                        <option value="Khuyến Cáo Y Tế">Khuyến Cáo Y Tế</option>
                        <option value="Bệnh Truyền Nhiễm">Bệnh Truyền Nhiễm</option>
                        <option value="Vắc Xin Mới">Vắc Xin Mới</option>
                        <option value="Chăm Sóc Trẻ Em">Chăm Sóc Trẻ Em</option>
                        <option value="Tiêm Chủng Người Lớn">Tiêm Chủng Người Lớn</option>
                        <option value="Tiêm Phòng Mẹ Bầu">Tiêm Phòng Mẹ Bầu</option>
                        <option value="Góc Chuyên Gia">Góc Chuyên Gia</option>
                    </select>
                </div>
                
                <!-- Dedicated Thumbnail Image Card -->
                <div style="background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <h3 style="font-size: 15px; font-weight: 800; color: #1e293b; margin: 0 0 14px 0; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                        Ảnh đại diện danh mục *
                    </h3>
                    
                    <input type="file" name="image_file" id="image_file" accept="image/*" style="display: none;">
                    <input type="hidden" name="image" id="image_hidden" value="">
                    
                    <div id="image_dropzone" style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; background: #f8fafc; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--accent-color)'; this.style.background='#f0f9ff';" onmouseout="this.style.borderColor='#cbd5e1'; this.style.background='#f8fafc';">
                        <div id="dropzone_prompt">
                            <i data-lucide="upload-cloud" style="width: 32px; height: 32px; color: #64748b; margin-bottom: 6px; display: inline-block;"></i>
                            <p style="font-weight: 600; color: #475569; margin: 0 0 4px 0; font-size: 13px;">Chọn hoặc kéo ảnh vào đây</p>
                            <span style="font-size: 11px; color: #94a3b8; display: block;">JPG, PNG, GIF, WEBP (Tối đa 2MB)</span>
                        </div>
                        
                        <div id="image_preview_container" style="display: none;">
                            <div style="position: relative; display: inline-block;">
                                <img id="image_preview" src="" alt="Preview" style="max-width: 100%; max-height: 150px; border-radius: 6px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <button type="button" id="btn_remove_image" style="position: absolute; top: -8px; right: -8px; background: #ef4444; color: #ffffff; border: none; border-radius: 50%; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.2);" title="Xóa hình ảnh">
                                    <i data-lucide="x" style="width: 12px; height: 12px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <!-- Right Side (70% Width): Content Area -->
            <div style="flex: 1; min-width: 600px; display: flex; flex-direction: column; gap: 24px;">
                
                <!-- Title & Summary Box -->
                <div style="background: #ffffff; padding: 28px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 8px; font-size: 15px;">Tiêu đề bài viết *</label>
                        <input type="text" name="title" required value="{{ old('title') }}" placeholder="Nhập tiêu đề tin tức y khoa..." style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--accent-color)'" onblur="this.style.borderColor='#cbd5e1'">
                    </div>
                    
                    <div style="margin-bottom: 0;">
                        <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 8px; font-size: 15px;">Tóm tắt ngắn (Hiển thị ngoài thẻ danh mục) *</label>
                        <textarea name="summary" required rows="3" placeholder="Nhập tóm tắt ngắn giới thiệu bài viết hiển thị ở danh mục..." style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; font-family: inherit; line-height: 1.5; outline: none; transition: border-color 0.2s; resize: vertical;" onfocus="this.style.borderColor='var(--accent-color)'" onblur="this.style.borderColor='#cbd5e1'">{{ old('summary') }}</textarea>
                    </div>
                </div>
                
                <!-- Content Area -->
                <div style="background: #ffffff; padding: 28px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 16px; font-size: 15px;">Nội dung chi tiết bài viết</label>
                    
                    <!-- Notebook Cells Canvas -->
                    <div id="notebook-container" style="display: flex; flex-direction: column;">
                        <!-- Cells are appended here -->
                    </div>
                    
                    <!-- Static Buttons for Adding Cells -->
                    <div style="display: flex; gap: 12px; margin-top: 20px; background: #f8fafc; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <button type="button" class="insert-btn" onclick="addNewCell('text')">
                            <i data-lucide="file-text" style="width: 14px; height: 14px; color: #004b8f;"></i> + Thêm đoạn văn bản
                        </button>
                        <button type="button" class="insert-btn" onclick="addNewCell('image')">
                            <i data-lucide="image" style="width: 14px; height: 14px; color: #eaaa00;"></i> + Thêm hình ảnh
                        </button>
                    </div>
                    
                    <textarea name="content" id="content-hidden" style="display: none;"></textarea>
                </div>
                
            </div>
            
        </div>
    </form>
</div>

<!-- TinyMCE 6 Core -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notebookContainer = document.getElementById('notebook-container');
        const form = document.getElementById('articleCreateForm');
        const contentHidden = document.getElementById('content-hidden');
        
        let cellCounter = 0;
        
        // Helper to initialize TinyMCE on a textarea ID
        function initTinyMCE(id) {
            tinymce.init({
                selector: '#' + id,
                height: 260,
                menubar: false,
                plugins: 'lists link image charmap preview searchreplace visualblocks code help wordcount',
                toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | forecolor backcolor | link | removeformat | code',
                branding: false,
                promotion: false,
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save();
                    });
                    editor.on('focus', function () {
                        const cell = document.getElementById(id).closest('.notebook-cell');
                        if (cell) {
                            document.querySelectorAll('.notebook-cell').forEach(c => c.classList.remove('active-cell'));
                            cell.classList.add('active-cell');
                        }
                    });
                }
            });
        }
        
        // Add a new cell (Appends to the bottom of the container)
        window.addNewCell = function(type, initialValue = '') {
            cellCounter++;
            const cellId = 'cell-' + cellCounter;
            const elementId = 'cell-input-' + cellCounter;
            
            const cellDiv = document.createElement('div');
            cellDiv.id = cellId;
            cellDiv.className = 'notebook-cell';
            cellDiv.setAttribute('data-type', type);
            
            // Lắng nghe click để active cell
            cellDiv.addEventListener('click', function(e) {
                if (!e.target.closest('.cell-btn')) {
                    document.querySelectorAll('.notebook-cell').forEach(c => c.classList.remove('active-cell'));
                    cellDiv.classList.add('active-cell');
                }
            });
            
            let cellHTML = `
                <!-- Cell Control toolbar -->
                <div class="cell-controls">
                    <button type="button" class="cell-btn" onclick="moveCellUp('${cellId}')" title="Di chuyển lên"><i data-lucide="arrow-up" style="width: 14px; height: 14px;"></i></button>
                    <button type="button" class="cell-btn" onclick="moveCellDown('${cellId}')" title="Di chuyển xuống"><i data-lucide="arrow-down" style="width: 14px; height: 14px;"></i></button>
                    <button type="button" class="cell-btn btn-delete" onclick="deleteCell('${cellId}')" title="Xóa ô"><i data-lucide="trash-2" style="width: 14px; height: 14px;"></i></button>
                </div>
            `;
            
            if (type === 'text') {
                cellHTML += `
                    <div style="font-size: 11px; font-weight: 700; color: #004b8f; margin-bottom: 8px; display: flex; align-items: center; gap: 4px;">
                        <i data-lucide="file-text" style="width: 13px; height: 13px;"></i> [Ô Văn Bản]
                    </div>
                    <textarea id="${elementId}" class="cell-textarea" placeholder="Nhập nội dung văn bản y khoa..." style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px;">${initialValue}</textarea>
                `;
            } else {
                const hasImg = !!initialValue;
                cellHTML += `
                    <div style="font-size: 11px; font-weight: 700; color: #eaaa00; margin-bottom: 8px; display: flex; align-items: center; gap: 4px;">
                        <i data-lucide="image" style="width: 13px; height: 13px;"></i> [Ô Hình Ảnh]
                    </div>
                    <div style="text-align: center;">
                        <input type="file" class="cell-image-file" accept="image/*" style="display: none;" onchange="uploadCellImage(this, '${cellId}')">
                        <input type="hidden" class="cell-image-url" id="${elementId}" value="${initialValue}">
                        
                        <div class="cell-image-dropzone" onclick="triggerCellFileInput('${cellId}')" style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 16px; cursor: pointer; background: #f8fafc; transition: all 0.2s;">
                            <div class="cell-image-prompt" style="${hasImg ? 'display: none;' : ''}">
                                <i data-lucide="upload-cloud" style="width: 32px; height: 32px; color: #64748b; margin-bottom: 4px; display: inline-block;"></i>
                                <p style="font-weight: 600; color: #475569; margin: 0 0 2px 0; font-size: 12.5px;">Click để tải ảnh lên thiết bị</p>
                                <span style="font-size: 10.5px; color: #94a3b8;">Hỗ trợ JPG, PNG, GIF, WEBP</span>
                            </div>
                            <div class="cell-image-preview" style="${hasImg ? 'display: block;' : 'display: none;'}">
                                <img src="${initialValue}" alt="Preview" style="max-height: 150px; border-radius: 6px; border: 1px solid #cbd5e1; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: inline-block;">
                                <p style="font-size: 11.5px; color: #64748b; margin: 4px 0 0 0;">Click để đổi ảnh khác</p>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            cellDiv.innerHTML = cellHTML;
            notebookContainer.appendChild(cellDiv);
            
            if (type === 'text') {
                initTinyMCE(elementId);
            }
            
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        };
        
        // Trigger file input
        window.triggerCellFileInput = function(cellId) {
            const cell = document.getElementById(cellId);
            const input = cell.querySelector('.cell-image-file');
            input.click();
        };
        
        // Upload image using AJAX
        window.uploadCellImage = function(input, cellId) {
            if (input.files.length === 0) return;
            const file = input.files[0];
            const cell = document.getElementById(cellId);
            const prompt = cell.querySelector('.cell-image-prompt');
            const preview = cell.querySelector('.cell-image-preview');
            const previewImg = cell.querySelector('.cell-image-preview img');
            const hiddenUrl = cell.querySelector('.cell-image-url');
            
            const formData = new FormData();
            formData.append('file', file);
            
            prompt.innerHTML = `
                <i data-lucide="loader" class="animate-spin" style="width: 32px; height: 32px; color: var(--accent-color); margin-bottom: 4px; display: inline-block;"></i>
                <p style="font-weight: 600; color: #475569; margin: 0; font-size: 12.5px;">Đang tải ảnh...</p>
            `;
            if (typeof lucide !== 'undefined') lucide.createIcons();
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route("admin.articles.upload-image") }}');
            xhr.setRequestHeader('X-CSRF-Token', '{{ csrf_token() }}');
            
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.location) {
                        hiddenUrl.value = response.location;
                        previewImg.src = response.location;
                        prompt.style.display = 'none';
                        preview.style.display = 'block';
                    } else {
                        alert('Upload failed.');
                        resetPrompt();
                    }
                } else {
                    alert('Upload failed.');
                    resetPrompt();
                }
            };
            xhr.onerror = function() {
                alert('Connection error.');
                resetPrompt();
            };
            xhr.send(formData);
            
            function resetPrompt() {
                prompt.innerHTML = `
                    <i data-lucide="upload-cloud" style="width: 32px; height: 32px; color: #64748b; margin-bottom: 4px; display: inline-block;"></i>
                    <p style="font-weight: 600; color: #475569; margin: 0 0 2px 0; font-size: 12.5px;">Click để tải ảnh lên thiết bị</p>
                    <span style="font-size: 10.5px; color: #94a3b8;">Hỗ trợ JPG, PNG, GIF, WEBP</span>
                `;
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        };
        
        // Helper để hủy TinyMCE an toàn trước khi di chuyển DOM
        function removeEditorIfExist(el) {
            try {
                const textarea = el.querySelector('.cell-textarea');
                if (textarea && typeof tinymce !== 'undefined' && tinymce.get(textarea.id)) {
                    tinymce.get(textarea.id).remove();
                }
            } catch (e) {
                console.warn("Lỗi khi hủy TinyMCE instance:", e);
            }
        }

        // Helper để khởi tạo lại TinyMCE sau khi di chuyển DOM
        function restoreEditorIfExist(el) {
            try {
                const textarea = el.querySelector('.cell-textarea');
                if (textarea) {
                    initTinyMCE(textarea.id);
                }
            } catch (e) {
                console.warn("Lỗi khi khởi tạo TinyMCE instance:", e);
            }
        }

        // Move cell up
        window.moveCellUp = function(cellId) {
            const cell = document.getElementById(cellId);
            const prev = cell.previousElementSibling;
            if (prev) {
                removeEditorIfExist(cell);
                removeEditorIfExist(prev);
                
                notebookContainer.insertBefore(cell, prev);
                
                restoreEditorIfExist(cell);
                restoreEditorIfExist(prev);
            }
        };
        
        // Move cell down
        window.moveCellDown = function(cellId) {
            const cell = document.getElementById(cellId);
            const next = cell.nextElementSibling;
            if (next) {
                removeEditorIfExist(cell);
                removeEditorIfExist(next);
                
                notebookContainer.insertBefore(next, cell);
                
                restoreEditorIfExist(cell);
                restoreEditorIfExist(next);
            }
        };
        
        // Delete cell
        window.deleteCell = function(cellId) {
            if (confirm('Bạn có chắc chắn muốn xóa ô này?')) {
                const cell = document.getElementById(cellId);
                if (cell) {
                    try {
                        const textarea = cell.querySelector('.cell-textarea');
                        if (textarea && typeof tinymce !== 'undefined' && tinymce.get(textarea.id)) {
                            tinymce.get(textarea.id).remove();
                        }
                    } catch (e) {
                        console.warn("Lỗi khi hủy TinyMCE instance khi xóa:", e);
                    }
                    cell.remove();
                }
            }
        };
        
        // Form submit listener: Aggregate HTML content
        form.addEventListener('submit', function(e) {
            let htmlContent = '';
            const cells = notebookContainer.querySelectorAll('.notebook-cell');
            
            cells.forEach(cell => {
                const type = cell.getAttribute('data-type');
                if (type === 'text') {
                    const textarea = cell.querySelector('.cell-textarea');
                    let text = '';
                    if (textarea && tinymce.get(textarea.id)) {
                        text = tinymce.get(textarea.id).getContent().trim();
                    } else if (textarea) {
                        text = textarea.value.trim();
                    }
                    if (text) {
                        htmlContent += `${text}\n`;
                    }
                } else if (type === 'image') {
                    const url = cell.querySelector('.cell-image-url').value;
                    if (url) {
                        htmlContent += `<div style="text-align: center; margin: 2rem 0;"><img src="${url}" alt="Ảnh bài viết" style="max-width: 100%; height: auto; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); display: inline-block;"></div>\n`;
                    }
                }
            });
            
            contentHidden.value = htmlContent;
        });

        // Dedicated Thumbnail upload preview handler
        const dropzone = document.getElementById('image_dropzone');
        const fileInput = document.getElementById('image_file');
        const previewImg = document.getElementById('image_preview');
        const previewContainer = document.getElementById('image_preview_container');
        const prompt = document.getElementById('dropzone_prompt');
        const btnRemove = document.getElementById('btn_remove_image');
        const hiddenInput = document.getElementById('image_hidden');

        dropzone.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    prompt.style.display = 'none';
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        });

        if (btnRemove) {
            btnRemove.addEventListener('click', (e) => {
                e.stopPropagation(); // Stop click triggering dropzone
                fileInput.value = '';
                hiddenInput.value = '';
                previewImg.src = '';
                previewContainer.style.display = 'none';
                prompt.style.display = 'block';
            });
        }
        
        // Spawn first text cell by default on load
        addNewCell('text');
    });
</script>
@endsection
