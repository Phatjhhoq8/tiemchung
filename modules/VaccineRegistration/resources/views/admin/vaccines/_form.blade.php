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
        <i data-lucide="info" style="width: 18px; height: 18px; color: var(--accent-color);"></i>
        Thông tin cơ bản
    </h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Tên Vắc xin -->
        <div class="form-group-modern" style="grid-column: span 2; margin-bottom: 0;">
            <label for="name" class="form-label-modern">Tên vắc xin / Gói vắc xin <span style="color: #ef4444;">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $vaccine->name) }}" required class="form-control-modern">
        </div>

        <!-- Phân loại -->
        <div class="form-group-modern" style="margin-bottom: 0;">
            <label for="type" class="form-label-modern">Phân loại <span style="color: #ef4444;">*</span></label>
            <select name="type" id="type" required class="form-control-modern" style="background-image: none;">
                <option value="single" {{ old('type', $vaccine->type) === 'single' ? 'selected' : '' }}>Vắc xin lẻ</option>
                <option value="package" {{ old('type', $vaccine->type) === 'package' ? 'selected' : '' }}>Gói vắc xin</option>
            </select>
        </div>

        <!-- Danh mục bệnh -->
        <div class="form-group-modern" style="margin-bottom: 0;">
            <label for="category" class="form-label-modern">Danh mục bệnh</label>
            <input type="text" name="category" id="category" value="{{ old('category', $vaccine->category) }}" placeholder="VD: Cúm, HPV, Viêm gan, Bạch hầu..." list="category-list" class="form-control-modern">
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
            <input type="text" name="disease_prevention" id="disease_prevention" value="{{ old('disease_prevention', $vaccine->disease_prevention) }}" placeholder="VD: Bạch hầu, Ho gà, Uốn ván, Bại liệt..." required class="form-control-modern">
        </div>
    </div>
</div>

{{-- ===== GIÁ CẢ & SỐ LIỀU ===== --}}
<div class="card-modern">
    <h3 style="font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
        <i data-lucide="dollar-sign" style="width: 18px; height: 18px; color: var(--accent-color);"></i>
        Giá cả & Phác đồ
    </h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
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
            <input type="number" name="doses" id="doses" value="{{ old('doses', $vaccine->doses ?: 1) }}" required min="1" class="form-control-modern">
        </div>

        <!-- Tình trạng kho -->
        <div class="form-group-modern" style="margin-bottom: 0;">
            <label for="stock_status" class="form-label-modern">Tình trạng kho <span style="color: #ef4444;">*</span></label>
            <select name="stock_status" id="stock_status" required class="form-control-modern" style="background-image: none;">
                <option value="available" {{ old('stock_status', $vaccine->stock_status ?? 'available') === 'available' ? 'selected' : '' }}>Đầy đủ</option>
                <option value="limited" {{ old('stock_status', $vaccine->stock_status) === 'limited' ? 'selected' : '' }}>Còn ít</option>
                <option value="out_of_stock" {{ old('stock_status', $vaccine->stock_status) === 'out_of_stock' ? 'selected' : '' }}>Hết hàng</option>
            </select>
        </div>
    </div>
</div>

{{-- ===== NGUỒN GỐC & QUY CÁCH ===== --}}
<div class="card-modern">
    <h3 style="font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
        <i data-lucide="globe" style="width: 18px; height: 18px; color: var(--accent-color);"></i>
        Nguồn gốc & Quy cách
    </h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Hãng sản xuất -->
        <div class="form-group-modern" style="margin-bottom: 0;">
            <label for="manufacturer" class="form-label-modern">Hãng sản xuất</label>
            <input type="text" name="manufacturer" id="manufacturer" value="{{ old('manufacturer', $vaccine->manufacturer) }}" placeholder="VD: MSD, Sanofi, GlaxoSmithKline..." class="form-control-modern">
        </div>

        <!-- Quốc gia nguồn gốc -->
        <div class="form-group-modern" style="margin-bottom: 0;">
            <label for="origin" class="form-label-modern">Nước sản xuất (Nguồn gốc)</label>
            <input type="text" name="origin" id="origin" value="{{ old('origin', $vaccine->origin) }}" placeholder="VD: Mỹ, Pháp, Bỉ, Đức..." class="form-control-modern">
        </div>

        <!-- Quy cách đóng gói -->
        <div class="form-group-modern" style="grid-column: span 2; margin-bottom: 0;">
            <label for="dosage" class="form-label-modern">Quy cách liều lượng (Hàm lượng/Đóng gói)</label>
            <input type="text" name="dosage" id="dosage" value="{{ old('dosage', $vaccine->dosage) }}" placeholder="VD: Hộp 1 bơm tiêm đóng sẵn gia liều 0.5ml dung dịch..." class="form-control-modern">
        </div>
    </div>
</div>

{{-- ===== HÌNH ẢNH & HIỂN THỊ ===== --}}
<div class="card-modern">
    <h3 style="font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
        <i data-lucide="image" style="width: 18px; height: 18px; color: var(--accent-color);"></i>
        Hình ảnh & Hiển thị
    </h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Đường dẫn hình ảnh -->
        <div class="form-group-modern" style="margin-bottom: 0;">
            <label for="image" class="form-label-modern">Tên file ảnh vắc xin</label>
            <input type="text" name="image" id="image" value="{{ old('image', $vaccine->image) }}" placeholder="VD: qdenga.jpg (trống = ảnh mặc định)" class="form-control-modern">
        </div>

        <!-- Thứ tự hiển thị -->
        <div class="form-group-modern" style="margin-bottom: 0;">
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
        <textarea name="description" id="description" rows="5" placeholder="Nhập công dụng chi tiết, hướng dẫn, lưu ý phác đồ tiêm chủng..." class="form-control-modern" style="font-family: inherit; resize: vertical;">{{ old('description', $vaccine->description) }}</textarea>
    </div>
</div>
