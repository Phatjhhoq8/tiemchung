@extends('vaccine::layouts.admin')

@section('title', 'Quản lý Nhóm Bệnh - Medicare Admin')

@section('admin_content')
<div class="container-fluid px-4 py-4" style="max-width: 1000px; margin: 0 auto;">
    <!-- Header Page -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div style="text-align: left;">
            <h1 style="font-weight: 800; color: #0f172a; font-size: 24px; margin: 0 0 4px 0;">Quản lý Nhóm Bệnh</h1>
            <p style="font-size: 14px; color: #64748b; margin: 0;">Danh mục các nhóm bệnh của vắc xin hiện có trên hệ thống.</p>
        </div>
        <button type="button" onclick="openCreateModal()" style="background-color: #c8102e; border: 1px solid #c8102e; color: #ffffff; font-weight: 700; border-radius: 8px; padding: 10px 20px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s; height: fit-content;">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            Thêm Nhóm Bệnh
        </button>
    </div>

    <!-- Main Table Card -->
    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03); border: 1px solid #e2e8f0; background: #ffffff;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 14.5px; width: 100%; border-collapse: collapse;">
                    <thead style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <tr>
                            <th style="padding: 16px 20px; font-weight: 700; color: #475569; width: 80px; text-align: center; border-bottom: 1px solid #e2e8f0;">#</th>
                            <th style="padding: 16px 20px; font-weight: 700; color: #475569; text-align: left; border-bottom: 1px solid #e2e8f0;">Tên Nhóm Bệnh</th>
                            <th style="padding: 16px 20px; font-weight: 700; color: #475569; width: 180px; text-align: center; border-bottom: 1px solid #e2e8f0;">Số Lượng Vắc Xin</th>
                            <th style="padding: 16px 20px; font-weight: 700; color: #475569; width: 180px; text-align: center; border-bottom: 1px solid #e2e8f0;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                            <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;">
                                <td style="padding: 14px 20px; color: #64748b; font-weight: 600; text-align: center;">{{ $loop->iteration }}</td>
                                <td style="padding: 14px 20px; text-align: left;">
                                    <strong style="color: #0f172a; font-size: 15px;">{{ $cat->category }}</strong>
                                </td>
                                <td style="padding: 14px 20px; text-align: center;">
                                    <span class="badge bg-light text-primary font-bold" style="padding: 6px 12px; border-radius: 20px; font-size: 12.5px; background-color: #eff6ff !important; color: #1d4ed8 !important; font-weight: 700; display: inline-block;">
                                        {{ $cat->vaccine_count }} sản phẩm
                                    </span>
                                </td>
                                <td style="padding: 14px 20px; text-align: center;">
                                    <div style="display: inline-flex; gap: 8px; justify-content: center; align-items: center;">
                                        <button onclick="handleOpenEditModal(this)" data-category="{{ $cat->category }}" data-title="{{ $cat->article_title }}" data-content="{{ $cat->article_content }}" class="btn-action-edit" title="Sửa tên nhóm bệnh" style="border-radius: 6px; padding: 6px 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; font-size: 13px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; cursor: pointer; transition: all 0.2s;">
                                            <i data-lucide="edit-3" style="width: 13.5px; height: 13.5px;"></i> Sửa
                                        </button>
                                        <button onclick="handleDeleteCategory('{{ e($cat->category) }}')" class="btn-action-delete" title="Xóa nhóm bệnh" style="border-radius: 6px; padding: 6px 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; font-size: 13px; border: 1px solid #fecaca; background: #fef2f2; color: #dc2626; cursor: pointer; transition: all 0.2s;">
                                            <i data-lucide="trash-2" style="width: 13.5px; height: 13.5px;"></i> Xóa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 48px 20px; color: #94a3b8;">
                                    <i data-lucide="folder-open" style="width: 48px; height: 48px; color: #cbd5e1; margin-bottom: 12px;"></i>
                                    <p style="margin: 0 0 4px 0; font-weight: 600;">Chưa có nhóm bệnh nào được tạo.</p>
                                    <small>Vui lòng cập nhật trường Nhóm bệnh ở trang quản lý Vắc xin.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm/Sửa Nhóm Bệnh (Tự tùy biến, không phụ thuộc Bootstrap JS) -->
<div id="categoryModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; padding: 16px; backdrop-filter: blur(4px);">
    <div style="background: #ffffff; border-radius: 16px; max-width: 520px; width: 100%; box-shadow: 0 20px 50px rgba(0,0,0,0.15); overflow: hidden; box-sizing: border-box;">
        <!-- Header -->
        <div style="padding: 20px 24px 10px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9;">
            <h5 id="modalTitle" style="font-weight: 800; font-size: 18px; color: #0f172a; margin: 0;">Thêm Nhóm Bệnh</h5>
            <button type="button" onclick="closeCategoryModal()" style="background: transparent; border: none; font-size: 24px; cursor: pointer; color: #94a3b8; font-weight: 300; line-height: 1; padding: 0; margin-top: -4px;">&times;</button>
        </div>
        <!-- Body -->
        <form id="categoryForm" onsubmit="submitCategoryForm(event)" style="margin: 0;">
            <input type="hidden" id="actionType" value="create">
            <input type="hidden" id="oldCategoryName" value="">
            
            <div style="padding: 20px 24px; box-sizing: border-box; display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label for="categoryName" style="font-weight: 600; font-size: 14px; color: #475569; margin-bottom: 8px; display: block; text-align: left;">Tên nhóm bệnh <span style="color: #dc2626;">*</span></label>
                    <input type="text" id="categoryName" placeholder="Nhập tên nhóm bệnh (Ví dụ: Cúm mùa, HPV...)" required style="width: 100%; border-radius: 8px; padding: 10px 14px; font-size: 14.5px; border: 1px solid #cbd5e1; box-sizing: border-box; outline: none; transition: border-color 0.15s;">
                    <div id="err_categoryName" style="font-size: 12.5px; color: #dc2626; margin-top: 6px; display: none; text-align: left;"></div>
                </div>

                <!-- Custom fields for category descriptions (Edit only) -->
                <div id="categoryEditDetailsFields" style="display: none; flex-direction: column; gap: 14px;">
                    <div>
                        <label for="categoryTitle" style="font-weight: 600; font-size: 14px; color: #475569; margin-bottom: 8px; display: block; text-align: left;">Tiêu đề mô tả (Hiển thị ở Client)</label>
                        <input type="text" id="categoryTitle" placeholder="Ví dụ: Chủ động phòng ngừa bệnh Mô cầu B hiệu quả" style="width: 100%; border-radius: 8px; padding: 10px 14px; font-size: 14.5px; border: 1px solid #cbd5e1; box-sizing: border-box; outline: none;">
                    </div>
                    <div>
                        <label for="categoryContent" style="font-weight: 600; font-size: 14px; color: #475569; margin-bottom: 8px; display: block; text-align: left;">Nội dung mô tả chi tiết (Hỗ trợ HTML)</label>
                        <textarea id="categoryContent" placeholder="Nhập nội dung mô tả chi tiết của nhóm bệnh..." rows="6" style="width: 100%; border-radius: 8px; padding: 10px 14px; font-size: 14.5px; border: 1px solid #cbd5e1; box-sizing: border-box; outline: none; resize: vertical; font-family: inherit; line-height: 1.5;"></textarea>
                    </div>
                </div>

                <div id="createHelperText" style="border-radius: 8px; background-color: #f0fdf4; color: #166534; font-size: 12.5px; padding: 10px 12px; display: none; text-align: left; line-height: 1.4;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px; margin-top: -2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="12"></line></svg>
                    Nhóm bệnh mới sẽ có hiệu lực ngay sau khi lưu và hiển thị trong danh mục vắc xin.
                </div>
                <div id="editHelperText" style="border-radius: 8px; background-color: #fffbeb; color: #854d0e; font-size: 12.5px; padding: 10px 12px; display: none; text-align: left; line-height: 1.4;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px; margin-top: -2px;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    Thay đổi tên sẽ cập nhật đồng loạt cho toàn bộ các vắc xin thuộc nhóm này.
                </div>
            </div>
            <!-- Footer -->
            <div style="padding: 10px 24px 20px 24px; display: flex; gap: 8px; justify-content: flex-end; box-sizing: border-box; border-top: 1px solid #f1f5f9; padding-top: 16px;">
                <button type="button" onclick="closeCategoryModal()" style="border-radius: 8px; font-weight: 600; padding: 9px 18px; background: #f1f5f9; border: 1px solid #cbd5e1; color: #475569; cursor: pointer; transition: all 0.2s;">Hủy bỏ</button>
                <button type="submit" id="btnSubmitCategory" style="background-color: #c8102e; border: 1px solid #c8102e; color: white; border-radius: 8px; font-weight: 700; padding: 9px 18px; cursor: pointer; transition: all 0.2s;">Lưu lại</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Xác Nhận Xóa Nguy Hiểm (Tự tùy biến, không phụ thuộc Bootstrap JS) -->
<div id="deleteConfirmModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; padding: 16px; backdrop-filter: blur(4px);">
    <div style="background: #ffffff; border-radius: 16px; max-width: 480px; width: 100%; box-shadow: 0 20px 50px rgba(0,0,0,0.15); overflow: hidden; box-sizing: border-box;">
        <!-- Header -->
        <div style="padding: 20px 24px 10px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9;">
            <h5 style="font-weight: 800; font-size: 18px; color: #dc2626; margin: 0; display: inline-flex; align-items: center; gap: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: #dc2626;"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                Xác Nhận Xóa Nhóm Bệnh
            </h5>
            <button type="button" onclick="closeDeleteConfirmModal()" style="background: transparent; border: none; font-size: 24px; cursor: pointer; color: #94a3b8; font-weight: 300; line-height: 1; padding: 0; margin-top: -4px;">&times;</button>
        </div>
        <!-- Body -->
        <div style="padding: 20px 24px; box-sizing: border-box; text-align: left;">
            <p style="font-size: 14.5px; color: #334155; line-height: 1.6; margin: 0 0 14px 0;">
                Bạn đang chuẩn bị xóa nhóm bệnh: <strong id="deleteCategoryNameText" style="color: #0f172a; font-weight: 800;">...</strong>
            </p>
            <div id="deleteWarningBox" style="border-radius: 8px; background-color: #fef2f2; color: #991b1b; font-size: 13.5px; padding: 14px; border: 1px solid #fee2e2; margin-bottom: 14px; box-sizing: border-box; line-height: 1.5;">
                <span style="font-weight: 700; display: block; margin-bottom: 6px;">CẢNH BÁO MỨC ĐỘ NGUY HIỂM:</span>
                Nhóm bệnh này đang được gán cho <strong id="deleteCategoryVaccineCount">0</strong> vắc xin dưới đây. Việc xóa nhóm bệnh sẽ đưa trường "Nhóm bệnh" của các vắc xin này về trạng thái rỗng (Trống).
                <ul id="deleteCategoryVaccineList" style="margin: 8px 0 0 16px; padding: 0; font-size: 13px; line-height: 1.5; max-height: 120px; overflow-y: auto;">
                    <!-- JS Render -->
                </ul>
            </div>
            <p style="font-size: 13.5px; color: #64748b; margin: 0;">Bạn có chắc chắn muốn thực hiện hành động này không? Hành động này không thể hoàn tác.</p>
        </div>
        <!-- Footer -->
        <div style="padding: 10px 24px 20px 24px; display: flex; gap: 8px; justify-content: flex-end; box-sizing: border-box; border-top: 1px solid #f1f5f9; padding-top: 16px;">
            <button type="button" onclick="closeDeleteConfirmModal()" style="border-radius: 8px; font-weight: 600; padding: 9px 18px; background: #f1f5f9; border: 1px solid #cbd5e1; color: #475569; cursor: pointer; transition: all 0.2s;">Hủy bỏ</button>
            <button type="button" id="btnConfirmDelete" onclick="confirmDeleteCategory()" style="background-color: #dc2626; border: 1px solid #dc2626; color: white; border-radius: 8px; font-weight: 700; padding: 9px 18px; cursor: pointer; transition: all 0.2s;">Thực Hiện Xóa</button>
        </div>
    </div>
</div>

<style>
    /* CSS Hover effects */
    .btn-action-edit:hover {
        background: #f1f5f9 !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
    }
    .btn-action-delete:hover {
        background: #fee2e2 !important;
        border-color: #fca5a5 !important;
        color: #b91c1c !important;
    }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
</style>
@endsection

@section('scripts')
<script>
    let categoryToDelete = '';

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Thêm Nhóm Bệnh';
        document.getElementById('actionType').value = 'create';
        document.getElementById('categoryName').value = '';
        document.getElementById('oldCategoryName').value = '';
        
        document.getElementById('categoryEditDetailsFields').style.display = 'none';
        document.getElementById('createHelperText').style.display = 'block';
        document.getElementById('editHelperText').style.display = 'none';
        
        document.getElementById('err_categoryName').style.display = 'none';
        
        document.getElementById('categoryModal').style.display = 'flex';
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').style.display = 'none';
    }

    function handleOpenEditModal(btn) {
        const name = btn.getAttribute('data-category');
        const title = btn.getAttribute('data-title');
        const content = btn.getAttribute('data-content');
        openEditModal(name, title, content);
    }

    function openEditModal(name, title, content) {
        document.getElementById('modalTitle').textContent = 'Chỉnh Sửa Nhóm Bệnh';
        document.getElementById('actionType').value = 'edit';
        document.getElementById('categoryName').value = name;
        document.getElementById('oldCategoryName').value = name;
        
        document.getElementById('categoryTitle').value = title || '';
        document.getElementById('categoryContent').value = content || '';
        
        document.getElementById('categoryEditDetailsFields').style.display = 'flex';
        document.getElementById('createHelperText').style.display = 'none';
        document.getElementById('editHelperText').style.display = 'block';
        
        document.getElementById('err_categoryName').style.display = 'none';
        
        document.getElementById('categoryModal').style.display = 'flex';
    }

    function closeDeleteConfirmModal() {
        document.getElementById('deleteConfirmModal').style.display = 'none';
    }

    async function submitCategoryForm(event) {
        event.preventDefault();
        
        const actionType = document.getElementById('actionType').value;
        const nameInput = document.getElementById('categoryName');
        const nameVal = nameInput.value.trim();
        const oldName = document.getElementById('oldCategoryName').value;
        const submitBtn = document.getElementById('btnSubmitCategory');
        const errEl = document.getElementById('err_categoryName');
        
        if (!nameVal) {
            errEl.textContent = 'Vui lòng điền tên nhóm bệnh.';
            errEl.style.display = 'block';
            return;
        }

        if (actionType === 'create') {
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Đang lưu...';
            try {
                const response = await fetch('{{ route("admin.categories.store-ajax") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        category: nameVal
                    })
                });

                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Lỗi tạo nhóm bệnh mới.');
                }

                window.AppDialog.toast(data.message || 'Tạo nhóm bệnh mới thành công.', 'success');
                closeCategoryModal();
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } catch (error) {
                window.AppDialog.toast(error.message, 'error');
                errEl.textContent = error.message;
                errEl.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Lưu lại';
            }
            return;
        }

        // Action edit
        const titleVal = document.getElementById('categoryTitle').value.trim();
        const contentVal = document.getElementById('categoryContent').value;
        
        const confirmMsg = `Bạn có thực sự muốn lưu thay đổi cho nhóm bệnh "${oldName}" không?\nToàn bộ vắc xin liên quan và nội dung mô tả sẽ được cập nhật.`;
        if (!await window.AppDialog.confirm(confirmMsg)) {
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Đang lưu...';

        try {
            const response = await fetch('{{ route("admin.categories.update") }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    old_name: oldName,
                    new_name: nameVal,
                    title: titleVal,
                    content: contentVal
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Lỗi cập nhật danh mục.');
            }

            window.AppDialog.toast(data.message || 'Cập nhật thành công nhóm bệnh.', 'success');
            closeCategoryModal();
            
            setTimeout(() => {
                window.location.reload();
            }, 800);
            
        } catch (error) {
            window.AppDialog.toast(error.message, 'error');
            errEl.textContent = error.message;
            errEl.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Lưu lại';
        }
    }

    async function handleDeleteCategory(name) {
        categoryToDelete = name;
        
        const checkingToast = window.AppDialog.toast('Đang kiểm tra dữ liệu vắc xin liên quan...', 'info');
        
        try {
            const response = await fetch('{{ route("admin.categories.check-delete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    category: name
                })
            });

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Lỗi kiểm tra danh mục.');
            }

            const nameEl = document.getElementById('deleteCategoryNameText');
            if (nameEl) nameEl.textContent = name;
            
            const warningBox = document.getElementById('deleteWarningBox');
            const vacCountEl = document.getElementById('deleteCategoryVaccineCount');
            const vacListEl = document.getElementById('deleteCategoryVaccineList');
            
            if (vacCountEl) vacCountEl.textContent = data.vaccine_count;
            
            if (data.has_vaccines) {
                if (warningBox) warningBox.style.display = 'block';
                let listHtml = '';
                data.vaccine_names.forEach(vName => {
                    listHtml += `<li style="list-style-type: square; margin-bottom: 2px;">${vName}</li>`;
                });
                if (vacListEl) vacListEl.innerHTML = listHtml;
            } else {
                if (warningBox) warningBox.style.display = 'none';
                if (vacListEl) vacListEl.innerHTML = '';
            }

            document.getElementById('deleteConfirmModal').style.display = 'flex';
            
        } catch (error) {
            window.AppDialog.toast(error.message, 'error');
        }
    }

    async function confirmDeleteCategory() {
        const btnDelete = document.getElementById('btnConfirmDelete');
        btnDelete.disabled = true;
        btnDelete.innerHTML = 'Đang xóa...';

        try {
            const response = await fetch('{{ route("admin.categories.destroy") }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    category: categoryToDelete
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Lỗi xóa danh mục.');
            }

            window.AppDialog.toast(data.message || 'Xóa nhóm bệnh thành công.', 'success');
            closeDeleteConfirmModal();
            
            setTimeout(() => {
                window.location.reload();
            }, 800);
            
        } catch (error) {
            window.AppDialog.toast(error.message, 'error');
            btnDelete.disabled = false;
            btnDelete.innerHTML = 'Thực Hiện Xóa';
        }
    }
</script>
@endsection
