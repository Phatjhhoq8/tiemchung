@extends('vaccine::layouts.admin')

@section('title', 'Quản lý Nhóm Bệnh - Medicare Admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-slate-800 font-bold" style="font-weight: 800; color: #0f172a;">Quản lý Nhóm Bệnh</h1>
            <p class="text-muted mb-0" style="font-size: 14px; color: #64748b;">Danh mục các nhóm bệnh của vắc xin hiện có trên hệ thống.</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="openCreateModal()" style="background-color: #c8102e; border-color: #c8102e; font-weight: 700; border-radius: 8px; padding: 10px 20px; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            Thêm Nhóm Bệnh
        </button>
    </div>

    <!-- Main Table Card -->
    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 14.5px;">
                    <thead style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <tr>
                            <th style="padding: 16px 24px; font-weight: 700; color: #475569; width: 10%;">#</th>
                            <th style="padding: 16px 24px; font-weight: 700; color: #475569;">Tên Nhóm Bệnh</th>
                            <th style="padding: 16px 24px; font-weight: 700; color: #475569; width: 25%; text-align: center;">Số Lượng Vắc Xin</th>
                            <th style="padding: 16px 24px; font-weight: 700; color: #475569; width: 20%; text-align: right;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 16px 24px; color: #64748b; font-weight: 600;">{{ $loop->iteration }}</td>
                                <td style="padding: 16px 24px;">
                                    <strong style="color: #0f172a; font-size: 15.5px;">{{ $cat->category }}</strong>
                                </td>
                                <td style="padding: 16px 24px; text-align: center;">
                                    <span class="badge bg-light text-primary font-bold" style="padding: 6px 12px; border-radius: 20px; font-size: 13px; background-color: #eff6ff !important; color: #1d4ed8 !important; font-weight: 700;">
                                        {{ $cat->vaccine_count }} sản phẩm
                                    </span>
                                </td>
                                <td style="padding: 16px 24px; text-align: right;">
                                    <div class="d-inline-flex gap-2">
                                        <button onclick="openEditModal('{{ e($cat->category) }}')" class="btn btn-sm btn-outline-secondary" title="Sửa tên nhóm bệnh" style="border-radius: 6px; padding: 6px 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                            <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i> Sửa
                                        </button>
                                        <button onclick="handleDeleteCategory('{{ e($cat->category) }}')" class="btn btn-sm btn-outline-danger" title="Xóa nhóm bệnh" style="border-radius: 6px; padding: 6px 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Xóa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5" style="color: #94a3b8;">
                                    <i data-lucide="folder-open" style="width: 48px; height: 48px; color: #cbd5e1; margin-bottom: 12px;"></i>
                                    <p class="mb-0 font-medium">Chưa có nhóm bệnh nào được tạo.</p>
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

<!-- Modal Thêm/Sửa Nhóm Bệnh -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(4px);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 50px rgba(0,0,0,0.15);">
            <div class="modal-header border-0 pb-0" style="padding: 24px 24px 10px 24px;">
                <h5 class="modal-title font-bold text-slate-900" id="modalTitle" style="font-weight: 800; font-size: 18px; color: #0f172a;">Thêm Nhóm Bệnh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm" onsubmit="submitCategoryForm(event)">
                <input type="hidden" id="actionType" value="create">
                <input type="hidden" id="oldCategoryName" value="">
                
                <div class="modal-body" style="padding: 10px 24px 24px 24px;">
                    <div class="form-group mb-3">
                        <label for="categoryName" class="form-label font-semibold text-slate-700" style="font-weight: 600; font-size: 14px; margin-bottom: 6px; display: block;">Tên nhóm bệnh mới <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryName" placeholder="Nhập tên nhóm bệnh (Ví dụ: Cúm mùa, HPV...)" required style="border-radius: 8px; padding: 10px 14px; font-size: 14.5px;">
                        <div class="invalid-feedback" id="err_categoryName" style="font-size: 12.5px; margin-top: 4px;"></div>
                    </div>
                    <div class="alert alert-info py-2 px-3 border-0 mb-0" id="createHelperText" style="border-radius: 8px; background-color: #f0fdf4; color: #166534; font-size: 12.5px; display: none;">
                        <i data-lucide="info" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;"></i>
                        Nhóm bệnh mới sẽ có hiệu lực ngay sau khi gán cho vắc xin bất kỳ.
                    </div>
                    <div class="alert alert-warning py-2 px-3 border-0 mb-0" id="editHelperText" style="border-radius: 8px; background-color: #fffbeb; color: #854d0e; font-size: 12.5px; display: none;">
                        <i data-lucide="alert-triangle" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;"></i>
                        Thay đổi tên sẽ cập nhật đồng loạt cho toàn bộ các vắc xin thuộc nhóm này.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0" style="padding: 10px 24px 24px 24px; display: flex; gap: 8px; justify-content: flex-end;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600; padding: 10px 20px;">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitCategory" style="background-color: #c8102e; border-color: #c8102e; border-radius: 8px; font-weight: 700; padding: 10px 20px;">Lưu lại</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xác Nhận Xóa Nguy Hiểm -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(4px);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 50px rgba(0,0,0,0.15);">
            <div class="modal-header border-0 pb-0" style="padding: 24px 24px 10px 24px;">
                <h5 class="modal-title font-bold text-danger" style="font-weight: 800; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="alert-octagon" style="width: 24px; height: 24px; color: #dc2626;"></i>
                    Xác Nhận Xóa Nhóm Bệnh
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 10px 24px 24px 24px; text-align: left;">
                <p style="font-size: 14.5px; color: #334155; line-height: 1.6; margin-bottom: 14px;">
                    Bạn đang chuẩn bị xóa nhóm bệnh: <strong id="deleteCategoryNameText" class="text-dark">...</strong>
                </p>
                <div class="alert alert-danger border-0 p-3 mb-3" style="border-radius: 8px; background-color: #fef2f2; color: #991b1b; font-size: 13.5px;" id="deleteWarningBox">
                    <span style="font-weight: 700; display: block; margin-bottom: 6px;">CẢNH BÁO MỨC ĐỘ NGUY HIỂM:</span>
                    Nhóm bệnh này đang được gán cho <strong id="deleteCategoryVaccineCount">0</strong> vắc xin dưới đây. Việc xóa nhóm bệnh sẽ đưa trường "Nhóm bệnh" của các vắc xin này về trạng thái rỗng (Trống).
                    <ul id="deleteCategoryVaccineList" style="margin: 8px 0 0 16px; padding: 0; font-size: 13px; line-height: 1.5; max-height: 120px; overflow-y: auto;">
                        <!-- JS Render -->
                    </ul>
                </div>
                <p style="font-size: 13.5px; color: #64748b; margin-bottom: 0;">Bạn có chắc chắn muốn thực hiện hành động này không? Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer border-0 pt-0" style="padding: 10px 24px 24px 24px; display: flex; gap: 8px; justify-content: flex-end;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600; padding: 10px 20px;">Hủy bỏ</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDelete" onclick="confirmDeleteCategory()" style="background-color: #dc2626; border-color: #dc2626; border-radius: 8px; font-weight: 700; padding: 10px 20px;">Thực Hiện Xóa</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let categoryModal = null;
    let deleteConfirmModal = null;
    let categoryToDelete = '';

    document.addEventListener('DOMContentLoaded', function() {
        categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
        deleteConfirmModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    });

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Thêm Nhóm Bệnh';
        document.getElementById('actionType').value = 'create';
        document.getElementById('categoryName').value = '';
        document.getElementById('oldCategoryName').value = '';
        
        document.getElementById('createHelperText').style.display = 'block';
        document.getElementById('editHelperText').style.display = 'none';
        
        document.getElementById('err_categoryName').style.display = 'none';
        document.getElementById('categoryName').classList.remove('is-invalid');
        
        categoryModal.show();
    }

    function openEditModal(name) {
        document.getElementById('modalTitle').textContent = 'Chỉnh Sửa Nhóm Bệnh';
        document.getElementById('actionType').value = 'edit';
        document.getElementById('categoryName').value = name;
        document.getElementById('oldCategoryName').value = name;
        
        document.getElementById('createHelperText').style.display = 'none';
        document.getElementById('editHelperText').style.display = 'block';
        
        document.getElementById('err_categoryName').style.display = 'none';
        document.getElementById('categoryName').classList.remove('is-invalid');
        
        categoryModal.show();
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
            nameInput.classList.add('is-invalid');
            errEl.textContent = 'Vui lòng điền tên nhóm bệnh.';
            errEl.style.display = 'block';
            return;
        }

        if (actionType === 'create') {
            // Đối với create, vì không có bảng categories riêng nên thực ra là hướng dẫn hoặc chỉ thông báo
            window.AppDialog.toast('Nhóm bệnh sẽ được tự động tạo và hiển thị khi bạn thêm vắc xin mới hoặc sửa vắc xin cũ và gán nhóm này.', 'info');
            categoryModal.hide();
            return;
        }

        // Action edit: Gọi API updateCategory
        const confirmMsg = `Bạn có thực sự muốn đổi tên nhóm bệnh từ "${oldName}" thành "${nameVal}" không?\nToàn bộ vắc xin liên quan sẽ được cập nhật.`;
        if (!await window.AppDialog.confirm(confirmMsg)) {
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang lưu...';

        try {
            const response = await fetch('{{ route("categories.update") }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    old_name: oldName,
                    new_name: nameVal
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Lỗi cập nhật danh mục.');
            }

            window.AppDialog.toast(data.message || 'Cập nhật thành công nhóm bệnh.', 'success');
            categoryModal.hide();
            
            // Reload page sau 1s
            setTimeout(() => {
                window.location.reload();
            }, 1000);
            
        } catch (error) {
            window.AppDialog.toast(error.message, 'error');
            nameInput.classList.add('is-invalid');
            errEl.textContent = error.message;
            errEl.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Lưu lại';
        }
    }

    async function handleDeleteCategory(name) {
        categoryToDelete = name;
        
        // Show loading toast or block
        const checkingToast = window.AppDialog.toast('Đang kiểm tra dữ liệu vắc xin liên quan...', 'info');
        
        try {
            const response = await fetch('{{ route("categories.check-delete") }}', {
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

            document.getElementById('deleteCategoryNameText').textContent = name;
            
            const warningBox = document.getElementById('deleteWarningBox');
            const vacCountEl = document.getElementById('deleteCategoryVaccineCount');
            const vacListEl = document.getElementById('deleteCategoryVaccineList');
            
            vacCountEl.textContent = data.vaccine_count;
            
            if (data.has_vaccines) {
                warningBox.style.display = 'block';
                let listHtml = '';
                data.vaccine_names.forEach(vName => {
                    listHtml += `<li style="list-style-type: square; margin-bottom: 2px;">${vName}</li>`;
                });
                vacListEl.innerHTML = listHtml;
            } else {
                warningBox.style.display = 'none';
                vacListEl.innerHTML = '';
            }

            deleteConfirmModal.show();
            
        } catch (error) {
            window.AppDialog.toast(error.message, 'error');
        }
    }

    async function confirmDeleteCategory() {
        const btnDelete = document.getElementById('btnConfirmDelete');
        btnDelete.disabled = true;
        btnDelete.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xóa...';

        try {
            const response = await fetch('{{ route("categories.destroy") }}', {
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
            deleteConfirmModal.hide();
            
            // Reload page sau 1s
            setTimeout(() => {
                window.location.reload();
            }, 1000);
            
        } catch (error) {
            window.AppDialog.toast(error.message, 'error');
            btnDelete.disabled = false;
            btnDelete.innerHTML = 'Thực Thế Xóa';
        }
    }
</script>
@endsection
