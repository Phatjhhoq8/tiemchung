@if ($errors->any())
    <div class="alert alert-danger" style="margin-bottom: 24px; padding: 16px; border-radius: 8px; background-color: #fde8e8; color: #9b1c1c; border: 1px solid #fbd5d5;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div style="display: grid; grid-template-columns: 1fr; gap: 24px; margin-bottom: 30px;">
    <!-- Tên Trung tâm -->
    <div class="form-group">
        <label for="name" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Tên chi nhánh trung tâm *</label>
        <input type="text" name="name" id="name" value="{{ old('name', $center->name) }}" placeholder="Ví dụ: Medicare Cờ Đỏ (Trụ sở chính)" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <div class="form-group">
        <label for="slug" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Mã chi nhánh</label>
        <input type="text" name="slug" id="slug" value="{{ old('slug', $center->slug) }}" placeholder="Ví dụ: medicare-co-do" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <!-- Địa chỉ chi tiết -->
    <div class="form-group">
        <label for="address" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Địa chỉ chi tiết *</label>
        <input type="text" name="address" id="address" value="{{ old('address', $center->address) }}" placeholder="Số nhà, tên đường, khu vực..." required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    {{-- Grid 2 cột cho các thông tin ngắn, tự động xếp dọc trên mobile --}}
    <div class="form-grid-2">
        <!-- Số điện thoại -->
        <div class="form-group" style="margin-bottom: 0;">
            <label for="phone" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Số điện thoại chi nhánh (tùy chọn)</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $center->phone) }}" placeholder="Ví dụ: 0938603839" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="zalo_phone" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Số Zalo</label>
            <input type="text" name="zalo_phone" id="zalo_phone" value="{{ old('zalo_phone', $center->zalo_phone) }}" placeholder="Bỏ trống sẽ dùng số điện thoại" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="sort_order" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Thứ tự hiển thị</label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $center->sort_order ?? 0) }}" min="0" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
        </div>

        <!-- Trạng thái hoạt động -->
        <div class="form-group" style="margin-bottom: 0;">
            <label for="is_active" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Trạng thái hoạt động *</label>
            <select name="is_active" id="is_active" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #ffffff;">
                <option value="1" {{ old('is_active', $center->is_active) == 1 ? 'selected' : '' }}>Hoạt động bình thường</option>
                <option value="0" {{ old('is_active', $center->is_active) == 0 ? 'selected' : '' }}>Tạm dừng phục vụ</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="working_hours" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Giờ làm việc</label>
        <input type="text" name="working_hours" id="working_hours" value="{{ old('working_hours', $center->working_hours) }}" placeholder="Ví dụ: 7:00 – 17:00" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <div class="form-group">
        <label for="map_url" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Link bản đồ / iframe src</label>
        <textarea name="map_url" id="map_url" rows="3" placeholder="Google Maps embed src hoặc link bản đồ" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; resize: vertical;">{{ old('map_url', $center->map_url) }}</textarea>
    </div>
</div>
