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
    <!-- Tên Vắc xin -->
    <div class="form-group" style="grid-column: span 2;">
        <label for="name" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Tên vắc xin / Gói vắc xin *</label>
        <input type="text" name="name" id="name" value="{{ old('name', $vaccine->name) }}" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <!-- Phân loại -->
    <div class="form-group">
        <label for="type" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Phân loại *</label>
        <select name="type" id="type" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #ffffff;">
            <option value="single" {{ old('type', $vaccine->type) === 'single' ? 'selected' : '' }}>Vắc xin lẻ</option>
            <option value="package" {{ old('type', $vaccine->type) === 'package' ? 'selected' : '' }}>Gói vắc xin</option>
        </select>
    </div>

    <!-- Giá tiêm -->
    <div class="form-group">
        <label for="price" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Giá tiêm (VND) *</label>
        <input type="number" name="price" id="price" value="{{ old('price', $vaccine->price) }}" required min="0" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <!-- Số mũi tiêm theo phác đồ -->
    <div class="form-group">
        <label for="doses" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Số mũi tiêm theo phác đồ *</label>
        <input type="number" name="doses" id="doses" value="{{ old('doses', $vaccine->doses ?: 1) }}" required min="1" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <!-- Nguồn gốc xuất xứ -->
    <div class="form-group">
        <label for="origin" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Nguồn gốc xuất xứ *</label>
        <input type="text" name="origin" id="origin" value="{{ old('origin', $vaccine->origin) }}" placeholder="Ví dụ: GlaxoSmithKline (Bỉ)" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <!-- Độ tuổi chỉ định -->
    <div class="form-group">
        <label for="age_group" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Độ tuổi chỉ định *</label>
        <input type="text" name="age_group" id="age_group" value="{{ old('age_group', $vaccine->age_group) }}" placeholder="Ví dụ: Trẻ từ 2 tháng đến 2 tuổi" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <!-- Công dụng phòng bệnh -->
    <div class="form-group">
        <label for="disease_prevention" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Bệnh phòng ngừa *</label>
        <input type="text" name="disease_prevention" id="disease_prevention" value="{{ old('disease_prevention', $vaccine->disease_prevention) }}" placeholder="Ví dụ: Cúm mùa, Bạch hầu, Ho gà..." required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <!-- Đường dẫn hình ảnh -->
    <div class="form-group" style="grid-column: span 2;">
        <label for="image" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Tên file ảnh vắc xin (tùy chọn)</label>
        <input type="text" name="image" id="image" value="{{ old('image', $vaccine->image) }}" placeholder="Ví dụ: qdenga.jpg (nếu trống sẽ tự gán ảnh mặc định)" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
    </div>

    <!-- Mô tả chi tiết -->
    <div class="form-group" style="grid-column: span 2;">
        <label for="description" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Mô tả chi tiết vắc xin</label>
        <textarea name="description" id="description" rows="5" placeholder="Nhập công dụng chi tiết, hướng dẫn, lưu ý phác đồ..." style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; font-family: inherit;">{{ old('description', $vaccine->description) }}</textarea>
    </div>
</div>
