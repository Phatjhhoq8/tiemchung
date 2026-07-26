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
            @php $firstBanner = $banners->first(); @endphp
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
                    <div style="background: #f8fafc; padding: 14px; border-radius: 8px; border: 1px dashed #cbd5e1; margin-bottom: 12px;">
                        <span style="font-size: 12.5px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">📤 Tải ảnh mới từ máy tính:</span>
                        <input type="file" name="image_file" id="banner_file_input" accept="image/*" onchange="previewFileImage(this, 'banner_preview_img_el')">
                    </div>
                </div>

                <div style="margin-top: 16px; text-align: center;">
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
                <div class="form-group" style="margin-bottom: 16px; background: #e0f2fe; padding: 14px; border-radius: 10px; border: 1px solid #bae6fd;">
                    <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 13.5px; color: #0369a1;">Chọn nhanh vắc xin sẵn có từ CSDL 40 loại:</label>
                    <select id="vaccine_select_dropdown" onchange="onSelectVaccineFromDb(this)" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #0284c7; font-weight: 600;">
                        @foreach($allVaccines as $v)
                            <option value="{{ $v->id }}" data-name="{{ $v->name }}" data-price="{{ $v->price }}" data-saleprice="{{ $v->sale_price }}" data-disease="{{ $v->disease_prevention }}" data-image="{{ $v->image }}" data-featured="{{ $v->is_featured ? 1 : 0 }}">
                                {{ $v->name }} ({{ number_format($v->price, 0, ',', '.') }} đ) {{ $v->is_featured ? '[Nổi bật]' : '' }}
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
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 13.5px;">Mô tả phòng bệnh:</label>
                    <input type="text" name="disease_prevention" id="vac_disease" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1;" required>
                </div>
            </div>

            <div class="fb-modal-footer">
                <button type="button" onclick="closeModal('vaccineModal')" class="btn-secondary" style="padding: 10px 18px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; cursor: pointer;">Hủy</button>
                <button type="button" onclick="saveVaccineSubmit()" class="btn-primary" style="padding: 10px 22px; border-radius: 8px; background: var(--primary-color); color: #ffffff; border: none; font-weight: 700; cursor: pointer;">Lưu Vắc Xin Này</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBannerModal() {
        document.getElementById('bannerModal').style.display = 'flex';
    }

    function openVaccineModal() {
        document.getElementById('vaccineModal').style.display = 'flex';
        const select = document.getElementById('vaccine_select_dropdown');
        if (select) {
            onSelectVaccineFromDb(select);
        }
    }

    function previewFileImage(input, previewElementId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewElementId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function onSelectVaccineFromDb(selectEl) {
        const option = selectEl.options[selectEl.selectedIndex];
        document.getElementById('vaccine_id').value = option.value;
        document.getElementById('vac_name').value = option.getAttribute('data-name') || '';
        document.getElementById('vac_price').value = option.getAttribute('data-price') || 0;
        document.getElementById('vac_disease').value = option.getAttribute('data-disease') || '';
    }

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
            }
        });
    }

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
            }
        });
    }
</script>
