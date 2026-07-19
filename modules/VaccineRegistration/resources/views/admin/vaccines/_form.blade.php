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
<div style="margin-bottom: 32px;">
    <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e2e8f0;">
        <i data-lucide="info" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle; margin-right: 6px;"></i>
        Thông tin cơ bản
    </h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Tên Vắc xin -->
        <div class="form-group" style="grid-column: span 2;">
            <label for="name" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Tên vắc xin / Gói vắc xin <span style="color: #ef4444;">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $vaccine->name) }}" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#cbd5e1'">
        </div>

        <!-- Phân loại -->
        <div class="form-group">
            <label for="type" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Phân loại <span style="color: #ef4444;">*</span></label>
            <select name="type" id="type" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #ffffff;">
                <option value="single" {{ old('type', $vaccine->type) === 'single' ? 'selected' : '' }}>Vắc xin lẻ</option>
                <option value="package" {{ old('type', $vaccine->type) === 'package' ? 'selected' : '' }}>Gói vắc xin</option>
            </select>
        </div>

        <!-- Danh mục bệnh -->
        <div class="form-group">
            <label for="category" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Danh mục bệnh</label>
            <input type="text" name="category" id="category" value="{{ old('category', $vaccine->category) }}" placeholder="VD: Cúm, HPV, Viêm gan, Bạch hầu..." list="category-list" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
            @if(isset($categories) && $categories->count())
            <datalist id="category-list">
                @foreach($categories as $cat)
                    <option value="{{ $cat }}">
                @endforeach
            </datalist>
            @endif
        </div>

        <!-- Bệnh phòng ngừa -->
        <div class="form-group" style="grid-column: span 2;">
            <label for="disease_prevention" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Bệnh phòng ngừa <span style="color: #ef4444;">*</span></label>
            <input type="text" name="disease_prevention" id="disease_prevention" value="{{ old('disease_prevention', $vaccine->disease_prevention) }}" placeholder="VD: Bạch hầu, Ho gà, Uốn ván, Bại liệt..." required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
        </div>
    </div>
</div>

{{-- ===== GIÁ CẢ & SỐ LIỀU ===== --}}
<div style="margin-bottom: 32px;">
    <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e2e8f0;">
        <i data-lucide="dollar-sign" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle; margin-right: 6px;"></i>
        Giá cả & Phác đồ
    </h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Giá tiêm -->
        <div class="form-group">
            <label for="price" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Giá bán lẻ / liều (VND) <span style="color: #ef4444;">*</span></label>
            <input type="number" name="price" id="price" value="{{ old('price', $vaccine->price) }}" required min="0" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
        </div>

        <!-- Giá ưu đãi -->
        <div class="form-group">
            <label for="sale_price" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">
                Giá ưu đãi (VND)
                <span style="font-size: 12px; color: #94a3b8; font-weight: 400; margin-left: 4px;">— Để trống nếu không giảm giá</span>
            </label>
            <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price', $vaccine->sale_price) }}" min="0" placeholder="VD: 995000" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
        </div>

        <!-- Số mũi tiêm -->
        <div class="form-group">
            <label for="doses" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Số mũi tiêm theo phác đồ <span style="color: #ef4444;">*</span></label>
            <input type="number" name="doses" id="doses" value="{{ old('doses', $vaccine->doses ?: 1) }}" required min="1" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
        </div>

        <!-- Tình trạng kho -->
        <div class="form-group">
            <label for="stock_status" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Tình trạng kho <span style="color: #ef4444;">*</span></label>
            <select name="stock_status" id="stock_status" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #ffffff;">
                <option value="available" {{ old('stock_status', $vaccine->stock_status ?? 'available') === 'available' ? 'selected' : '' }}>✅ Đầy đủ</option>
                <option value="limited" {{ old('stock_status', $vaccine->stock_status) === 'limited' ? 'selected' : '' }}>⚠️ Còn ít</option>
                <option value="out_of_stock" {{ old('stock_status', $vaccine->stock_status) === 'out_of_stock' ? 'selected' : '' }}>❌ Hết hàng</option>
            </select>
        </div>
    </div>
</div>

{{-- ===== NGUỒN GỐC & QUY CÁCH ===== --}}
<div style="margin-bottom: 32px;">
    <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e2e8f0;">
        <i data-lucide="globe" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle; margin-right: 6px;"></i>
        Nguồn gốc & Quy cách
    </h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Nguồn gốc xuất xứ -->
        <div class="form-group">
            <label for="origin" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Nước sản xuất <span style="color: #ef4444;">*</span></label>
            <input type="text" name="origin" id="origin" value="{{ old('origin', $vaccine->origin) }}" placeholder="VD: Bỉ, Mỹ, Pháp, Việt Nam..." required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
        </div>

        <!-- Hãng sản xuất -->
        <div class="form-group">
            <label for="manufacturer" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Hãng sản xuất</label>
            <input type="text" name="manufacturer" id="manufacturer" value="{{ old('manufacturer', $vaccine->manufacturer) }}" placeholder="VD: GlaxoSmithKline, Sanofi Pasteur..." style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
        </div>

        <!-- Độ tuổi chỉ định -->
        <div class="form-group">
            <label for="age_group" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Độ tuổi chỉ định <span style="color: #ef4444;">*</span></label>
            <input type="text" name="age_group" id="age_group" value="{{ old('age_group', $vaccine->age_group) }}" placeholder="VD: Trẻ từ 2 tháng đến 2 tuổi" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
        </div>

        <!-- Quy cách đóng gói -->
        <div class="form-group">
            <label for="dosage" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Quy cách đóng gói</label>
            <input type="text" name="dosage" id="dosage" value="{{ old('dosage', $vaccine->dosage) }}" placeholder="VD: 0.5ml, 1ml lọ..." style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
        </div>
    </div>
</div>

{{-- ===== HÌNH ẢNH & HIỂN THỊ ===== --}}
<div style="margin-bottom: 32px;">
    <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e2e8f0;">
        <i data-lucide="image" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle; margin-right: 6px;"></i>
        Hình ảnh & Hiển thị
    </h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Đường dẫn hình ảnh -->
        <div class="form-group">
            <label for="image" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Tên file ảnh vắc xin</label>
            <input type="text" name="image" id="image" value="{{ old('image', $vaccine->image) }}" placeholder="VD: qdenga.jpg (trống = ảnh mặc định)" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
        </div>

        <!-- Thứ tự hiển thị -->
        <div class="form-group">
            <label for="sort_order" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">
                Thứ tự hiển thị
                <span style="font-size: 12px; color: #94a3b8; font-weight: 400; margin-left: 4px;">— Số nhỏ hơn hiển thị trước</span>
            </label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $vaccine->sort_order ?? 0) }}" min="0" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
        </div>

        <!-- Vắc xin nổi bật -->
        <div class="form-group" style="grid-column: span 2;">
            <label style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer; padding: 12px 16px; border-radius: 8px; border: 1px solid #cbd5e1; background: #f8fafc; transition: all 0.2s;" onmouseover="this.style.borderColor='#3b82f6'" onmouseout="this.style.borderColor='#cbd5e1'">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $vaccine->is_featured) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #f59e0b; cursor: pointer;">
                <span style="font-weight: 600; color: #475569;">⭐ Đánh dấu là vắc xin nổi bật</span>
                <span style="font-size: 12px; color: #94a3b8; font-weight: 400;">— Hiển thị ưu tiên trên trang chủ</span>
            </label>
        </div>
    </div>
</div>

{{-- ===== MÔ TẢ CHI TIẾT ===== --}}
<div style="margin-bottom: 30px;">
    <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e2e8f0;">
        <i data-lucide="file-text" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle; margin-right: 6px;"></i>
        Mô tả chi tiết
    </h3>
    <div class="form-group">
        <textarea name="description" id="description" rows="5" placeholder="Nhập công dụng chi tiết, hướng dẫn, lưu ý phác đồ tiêm chủng..." style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; font-family: inherit; resize: vertical;">{{ old('description', $vaccine->description) }}</textarea>
    </div>
</div>
