@if ($errors->any())
    <div class="alert alert-danger" style="margin-bottom: 24px; padding: 16px; border-radius: 8px; background-color: #fde8e8; color: #9b1c1c; border: 1px solid #fbd5d5;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 30px;">
    <!-- Tiêu đề -->
    <div class="form-group" style="grid-column: span 2;">
        <label for="title" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Tiêu đề banner <span style="color: #ef4444;">*</span></label>
        <input type="text" name="title" id="title" value="{{ old('title', $banner->title) }}" required placeholder="VD: Tiêm vắc xin – Bảo vệ gia đình bạn" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <!-- Phụ đề -->
    <div class="form-group" style="grid-column: span 2;">
        <label for="subtitle" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Phụ đề / Mô tả ngắn</label>
        <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" placeholder="VD: Ưu đãi 7% khi mua gói vắc xin" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <!-- URL hình ảnh -->
    <div class="form-group" style="grid-column: span 2;">
        <label for="image_url" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">URL hình ảnh <span style="color: #ef4444;">*</span></label>
        <input type="text" name="image_url" id="image_url" value="{{ old('image_url', $banner->image_url) }}" required placeholder="VD: /images/banners/banner1.jpg hoặc https://..." style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
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
