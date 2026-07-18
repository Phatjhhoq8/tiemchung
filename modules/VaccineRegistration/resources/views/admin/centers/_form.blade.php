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

    <!-- Địa chỉ chi tiết -->
    <div class="form-group">
        <label for="address" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Địa chỉ chi tiết *</label>
        <input type="text" name="address" id="address" value="{{ old('address', $center->address) }}" placeholder="Số nhà, tên đường, khu vực..." required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <!-- Số điện thoại -->
    <div class="form-group">
        <label for="phone" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Số điện thoại chi nhánh (tùy chọn)</label>
        <input type="text" name="phone" id="phone" value="{{ old('phone', $center->phone) }}" placeholder="Ví dụ: 0938603839" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <!-- Trạng thái hoạt động -->
    <div class="form-group">
        <label for="is_active" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Trạng thái hoạt động *</label>
        <select name="is_active" id="is_active" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #ffffff;">
            <option value="1" {{ old('is_active', $center->is_active) == 1 ? 'selected' : '' }}>Hoạt động bình thường</option>
            <option value="0" {{ old('is_active', $center->is_active) == 0 ? 'selected' : '' }}>Tạm dừng phục vụ</option>
        </select>
    </div>
</div>
