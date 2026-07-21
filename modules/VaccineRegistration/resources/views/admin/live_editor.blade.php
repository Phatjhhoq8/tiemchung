@extends('vaccine::layouts.admin')

@section('title', 'Chỉnh Sửa Trực Quan Trang Chủ - Medicare')
@section('page_title', '🎨 Trình Chỉnh Sửa Trực Quan (Facebook Style Page Editor)')

@section('styles')
<style>
    /* Facebook Customizer Overlay Frames */
    .live-edit-frame {
        position: relative;
        border: 2px dashed #0284c7;
        border-radius: 12px;
        transition: all 0.3s ease;
        cursor: pointer;
        margin-bottom: 24px;
        background-color: rgba(2, 132, 199, 0.02);
    }
    .live-edit-frame:hover {
        border-color: var(--primary-color, #c8102e);
        box-shadow: 0 0 20px rgba(200, 16, 46, 0.2);
        background-color: rgba(200, 16, 46, 0.03);
    }
    .edit-frame-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background-color: #0284c7;
        color: #ffffff;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 700;
        z-index: 50;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        pointer-events: none;
    }
    .live-edit-frame:hover .edit-frame-badge {
        background-color: var(--primary-color, #c8102e);
    }

    /* Modal Styling */
    .fb-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(4px);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .fb-modal-content {
        background: #ffffff;
        width: 100%;
        max-width: 650px;
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        overflow: hidden;
        animation: modalSlideUp 0.3s ease-out;
    }
    @keyframes modalSlideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .fb-modal-header {
        background: #f8fafc;
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .fb-modal-body {
        padding: 24px;
        max-height: 75vh;
        overflow-y: auto;
    }
    .fb-modal-footer {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }
    .image-picker-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 12px;
        margin-top: 12px;
    }
    .image-picker-item {
        width: 100%;
        height: 75px;
        border-radius: 8px;
        object-fit: cover;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.2s;
    }
    .image-picker-item.selected, .image-picker-item:hover {
        border-color: var(--primary-color);
        transform: scale(1.05);
    }
</style>
@endsection

@section('admin_content')
<!-- Thanh Thông Báo Trình Chỉnh Sửa Xem Trước -->
<div style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; padding: 16px 24px; border-radius: 14px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
    <div>
        <h3 style="margin: 0 0 4px 0; font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="layout-template"></i> Chế Độ Chỉnh Sửa Trực Quan (Live Visual Page Editor)
        </h3>
        <p style="margin: 0; font-size: 13.5px; opacity: 0.9;">Nhấp chuột trực tiếp vào bất kỳ khung nào bên dưới để chọn hình ảnh từ máy/thư viện, chỉnh sửa thông tin hoặc chọn sản phẩm vắc xin có sẵn.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('home') }}" target="_blank" class="btn-secondary" style="background: #ffffff; color: #0284c7; padding: 10px 16px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 13.5px; display: inline-flex; align-items: center; gap: 6px;">
            <i data-lucide="external-link" style="width: 16px; height: 16px;"></i> Xem Trang Thực Tế
        </a>
    </div>
</div>

<!-- GIAO DIỆN XEM TRƯỚC VỚI CÁC KHUNG CHỈNH SỬA INTERACTIVE -->

<!-- 1. Khung Slider Banner -->
<div class="live-edit-frame" onclick="openBannerModal()" title="Nhấp vào để chỉnh sửa Banner Slider">
    <div class="edit-frame-badge"><i data-lucide="edit-3" style="width: 14px; height: 14px;"></i> Bấm sửa Banner Slider</div>
    
    <div style="padding: 24px; background: #ffffff; border-radius: 10px;">
        <h4 style="margin: 0 0 12px 0; color: #475569; font-size: 14px; text-transform: uppercase; font-weight: 700;">[Khung 1: Hero Banner Slider]</h4>
        <div style="position: relative; height: 260px; border-radius: 12px; overflow: hidden; background: #000000;">
            @php $firstBanner = $banners->first(); @endphp
            <img id="preview-banner-img" src="{{ asset($firstBanner ? $firstBanner->image_url : 'images/banners/banner_family.jpg') }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.85;">
            <div style="position: absolute; bottom: 30px; left: 30px; color: #ffffff; max-width: 500px;">
                <span style="background: var(--primary-color); padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 700;">HỆ THỐNG MEDICARE</span>
                <h2 id="preview-banner-title" style="font-size: 26px; margin: 8px 0; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">{{ $firstBanner->title ?? 'Hệ Thống Tiêm Chủng Medicare' }}</h2>
                <p id="preview-banner-subtitle" style="font-size: 14px; opacity: 0.9; margin: 0;">{{ $firstBanner->subtitle ?? 'Cung cấp vắc xin an toàn cho trẻ em và người lớn' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- 2. Khung Sản Phẩm / Vắc Xin Nổi Bật -->
<div class="live-edit-frame" onclick="openVaccineModal()" title="Nhấp vào để chọn/chỉnh sửa Vắc xin nổi bật">
    <div class="edit-frame-badge"><i data-lucide="sparkles" style="width: 14px; height: 14px;"></i> Bấm chọn Vắc Xin Nổi Bật</div>
    
    <div style="padding: 24px; background: #ffffff; border-radius: 10px;">
        <h4 style="margin: 0 0 16px 0; color: #475569; font-size: 14px; text-transform: uppercase; font-weight: 700;">[Khung 2: Danh Mục Vắc Xin Nổi Bật Trên Trang Chủ]</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            @foreach($featuredVaccines->take(4) as $vac)
                <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; background: #f8fafc; position: relative;">
                    <span style="position: absolute; top: 10px; right: 10px; background: #fef3c7; color: #d97706; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 700;">⭐ Nổi Bật</span>
                    <img src="{{ asset('images/vaccines/' . ($vac->image ?: 'default_vaccine.jpg')) }}" style="width: 100%; height: 110px; object-fit: cover; border-radius: 8px; margin-bottom: 10px;">
                    <div style="font-weight: 700; font-size: 14px; color: #1e293b;">{{ $vac->name }}</div>
                    <div style="color: var(--primary-color); font-weight: 800; font-size: 14px; margin-top: 4px;">{{ number_format($vac->price, 0, ',', '.') }} đ</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ================= MODAL CHỈNH SỬA BANNER ================= -->
<div id="bannerModal" class="fb-modal-overlay">
    <div class="fb-modal-content">
        <div class="fb-modal-header">
            <h3 style="margin: 0; font-size: 17px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="image" style="color: #0284c7;"></i> Tùy Chỉnh Hero Banner
            </h3>
            <button onclick="closeModal('bannerModal')" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <form id="bannerForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="banner_id" id="banner_id" value="{{ $firstBanner->id ?? 1 }}">
            <input type="hidden" name="image_existing" id="banner_image_existing" value="{{ $firstBanner->image_url ?? '' }}">
            
            <div class="fb-modal-body">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 13.5px;">Tiêu đề Banner:</label>
                    <input type="text" name="title" id="banner_title" class="form-control" value="{{ $firstBanner->title ?? '' }}" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1;" required>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 13.5px;">Phụ đề mô tả:</label>
                    <textarea name="subtitle" id="banner_subtitle" class="form-control" rows="2" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1;">{{ $firstBanner->subtitle ?? '' }}</textarea>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 13.5px;">Hình Ảnh Banner:</label>
                    
                    <!-- Lựa chọn 1: Tải từ máy tính -->
                    <div style="background: #f8fafc; padding: 14px; border-radius: 8px; border: 1px dashed #cbd5e1; margin-bottom: 12px;">
                        <span style="font-size: 12.5px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">📤 Tải ảnh mới từ máy tính:</span>
                        <input type="file" name="image_file" id="banner_file_input" accept="image/*" onchange="previewFileImage(this, 'banner_preview_img_el')">
                    </div>

                    <!-- Lựa chọn 2: Chọn ảnh có sẵn trong thư viện -->
                    <span style="font-size: 12.5px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">🖼️ Hoặc chọn ảnh có sẵn trong thư viện:</span>
                    <div class="image-picker-grid">
                        <img src="{{ asset('images/banners/banner_family.jpg') }}" class="image-picker-item" onclick="selectLibraryImage(this, 'banner_image_existing', 'banner_preview_img_el')">
                        <img src="{{ asset('images/banners/banner_child.jpg') }}" class="image-picker-item" onclick="selectLibraryImage(this, 'banner_image_existing', 'banner_preview_img_el')">
                        <img src="{{ asset('images/banners/banner_adult.jpg') }}" class="image-picker-item" onclick="selectLibraryImage(this, 'banner_image_existing', 'banner_preview_img_el')">
                    </div>
                </div>

                <div style="margin-top: 16px; text-align: center;">
                    <span style="font-size: 12px; color: #64748b; display: block; margin-bottom: 4px;">Xem trước hình ảnh đã chọn:</span>
                    <img id="banner_preview_img_el" src="{{ asset($firstBanner ? $firstBanner->image_url : 'images/banners/banner_family.jpg') }}" style="max-height: 120px; border-radius: 8px; border: 1px solid #cbd5e1;">
                </div>
            </div>

            <div class="fb-modal-footer">
                <button type="button" onclick="closeModal('bannerModal')" class="btn-secondary" style="padding: 10px 18px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; cursor: pointer;">Hủy</button>
                <button type="button" onclick="saveBannerSubmit()" class="btn-primary" style="padding: 10px 22px; border-radius: 8px; background: var(--primary-color); color: #ffffff; border: none; font-weight: 700; cursor: pointer;">Lưu Thay Đổi</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL CHỈNH SỬA VẮC XIN NỔI BẬT ================= -->
<div id="vaccineModal" class="fb-modal-overlay">
    <div class="fb-modal-content">
        <div class="fb-modal-header">
            <h3 style="margin: 0; font-size: 17px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="syringe" style="color: var(--primary-color);"></i> Chọn & Chỉnh Sửa Vắc Xin Nổi Bật
            </h3>
            <button onclick="closeModal('vaccineModal')" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <form id="vaccineForm" enctype="multipart/form-data">
            @csrf
            
            <div class="fb-modal-body">
                <!-- Chọn sản phẩm có sẵn từ CSDL -->
                <div class="form-group" style="margin-bottom: 16px; background: #e0f2fe; padding: 14px; border-radius: 10px; border: 1px solid #bae6fd;">
                    <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 13.5px; color: #0369a1;">🎯 Chọn nhanh vắc xin sẵn có từ CSDL 40 loại:</label>
                    <select id="vaccine_select_dropdown" onchange="onSelectVaccineFromDb(this)" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #0284c7; font-weight: 600;">
                        @foreach($allVaccines as $v)
                            <option value="{{ $v->id }}" data-name="{{ $v->name }}" data-price="{{ $v->price }}" data-saleprice="{{ $v->sale_price }}" data-disease="{{ $v->disease_prevention }}" data-image="{{ $v->image }}" data-featured="{{ $v->is_featured ? 1 : 0 }}">
                                {{ $v->name }} ({{ number_format($v->price, 0, ',', '.') }} đ) {{ $v->is_featured ? '⭐ Nổi bật' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="vaccine_id" id="vaccine_id" value="{{ $allVaccines->first()->id ?? 1 }}">
                <input type="hidden" name="image_existing" id="vac_image_existing" value="">

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 13.5px;">Tên Vắc Xin:</label>
                    <input type="text" name="name" id="vac_name" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1;" required>
                </div>

                <div style="display: flex; gap: 14px; margin-bottom: 16px;">
                    <div style="flex: 1;">
                        <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 13.5px;">Giá Bán Lẻ (VNĐ):</label>
                        <input type="number" name="price" id="vac_price" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1;" required>
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 13.5px;">Giá Ưu Đãi (Khuyến mại):</label>
                        <input type="number" name="sale_price" id="vac_saleprice" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1;">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 13.5px;">Mô tả công dụng phòng bệnh:</label>
                    <input type="text" name="disease_prevention" id="vac_disease" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1;" required>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 13.5px;">Hình Ảnh Vắc Xin:</label>
                    <div style="background: #f8fafc; padding: 14px; border-radius: 8px; border: 1px dashed #cbd5e1;">
                        <span style="font-size: 12.5px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">📤 Tải ảnh đại diện mới từ máy tính:</span>
                        <input type="file" name="image_file" id="vac_file_input" accept="image/*" onchange="previewFileImage(this, 'vac_preview_img_el')">
                    </div>
                </div>

                <div style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="is_featured" id="vac_is_featured" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                    <label for="vac_is_featured" style="font-weight: 700; font-size: 14px; color: #d97706; cursor: pointer;">⭐ Đặt hiển thị NỔI BẬT trên Trang Chủ</label>
                </div>

                <div style="text-align: center;">
                    <img id="vac_preview_img_el" src="{{ asset('images/vaccines/default_vaccine.jpg') }}" style="max-height: 100px; border-radius: 8px; border: 1px solid #cbd5e1;">
                </div>
            </div>

            <div class="fb-modal-footer">
                <button type="button" onclick="closeModal('vaccineModal')" class="btn-secondary" style="padding: 10px 18px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; cursor: pointer;">Hủy</button>
                <button type="button" onclick="saveVaccineSubmit()" class="btn-primary" style="padding: 10px 22px; border-radius: 8px; background: var(--primary-color); color: #ffffff; border: none; font-weight: 700; cursor: pointer;">Lưu Vắc Xin Này</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openBannerModal() {
        document.getElementById('bannerModal').style.display = 'flex';
    }

    function openVaccineModal() {
        document.getElementById('vaccineModal').style.display = 'flex';
        // Nạp vắc xin đầu tiên
        const select = document.getElementById('vaccine_select_dropdown');
        if (select) {
            onSelectVaccineFromDb(select);
        }
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Tải & Xem trước ảnh từ máy tính
    function previewFileImage(input, previewElementId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewElementId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Chọn ảnh từ thư viện
    function selectLibraryImage(imgEl, inputId, previewElementId) {
        document.querySelectorAll('.image-picker-item').forEach(el => el.classList.remove('selected'));
        imgEl.classList.add('selected');
        document.getElementById(inputId).value = imgEl.src;
        document.getElementById(previewElementId).src = imgEl.src;
    }

    // Đổi vắc xin từ Dropdown CSDL
    function onSelectVaccineFromDb(selectEl) {
        const option = selectEl.options[selectEl.selectedIndex];
        document.getElementById('vaccine_id').value = option.value;
        document.getElementById('vac_name').value = option.getAttribute('data-name') || '';
        document.getElementById('vac_price').value = option.getAttribute('data-price') || 0;
        document.getElementById('vac_saleprice').value = option.getAttribute('data-saleprice') || '';
        document.getElementById('vac_disease').value = option.getAttribute('data-disease') || '';
        
        const isFeatured = option.getAttribute('data-featured') === '1';
        document.getElementById('vac_is_featured').checked = isFeatured;

        const imgName = option.getAttribute('data-image') || 'default_vaccine.jpg';
        const imgUrl = "{{ asset('images/vaccines') }}/" + imgName;
        document.getElementById('vac_preview_img_el').src = imgUrl;
        document.getElementById('vac_image_existing').value = imgUrl;
    }

    // AJAX Lưu Banner
    function saveBannerSubmit() {
        const form = document.getElementById('bannerForm');
        const formData = new FormData(form);

        fetch("{{ route('admin.live-editor.banner') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("🎉 " + data.message);
                closeModal('bannerModal');
                window.location.reload();
            } else {
                alert("Có lỗi xảy ra, vui lòng kiểm tra lại.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Lỗi cập nhật banner.");
        });
    }

    // AJAX Lưu Vắc Xin
    function saveVaccineSubmit() {
        const form = document.getElementById('vaccineForm');
        const formData = new FormData(form);

        fetch("{{ route('admin.live-editor.vaccine') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("🎉 " + data.message);
                closeModal('vaccineModal');
                window.location.reload();
            } else {
                alert("Có lỗi xảy ra, vui lòng kiểm tra lại.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Lỗi cập nhật vắc xin.");
        });
    }
</script>
@endsection
