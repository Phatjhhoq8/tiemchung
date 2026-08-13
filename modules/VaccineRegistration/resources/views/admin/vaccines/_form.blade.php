@php
    $isSuperAdmin = $isSuperAdminAllCenters ?? $isSuperAdmin;
@endphp
@if ($errors->any())
    <div class="alert alert-danger" style="margin-bottom: 24px; padding: 16px; border-radius: 8px; background-color: #fde8e8; color: #9b1c1c; border: 1px solid #fbd5d5;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
@endif



<style>
    /* CSS cho các trường bị disabled/readonly trong form */
    .form-control-modern:disabled,
    .form-control-modern[disabled],
    textarea:disabled,
    select:disabled {
        background-color: #f1f5f9 !important;
        color: #64748b !important;
        border-color: #cbd5e1 !important;
        cursor: not-allowed !important;
        opacity: 0.85 !important;
    }
    
    label[style*="cursor: pointer"]:has(input[disabled]) {
        cursor: not-allowed !important;
    }

    /* Tùy chỉnh CSS cho input type="date" và quyển lịch calendar picker theo tông màu Medicare */
    input[type="date"] {
        accent-color: #c8102e !important;
        color-scheme: light;
        font-family: inherit;
        cursor: pointer;
    }
    
    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        filter: invert(18%) sepia(85%) saturate(5451%) hue-rotate(345deg) brightness(85%) contrast(98%);
        transition: background-color 0.2s ease, transform 0.15s ease;
    }
    
    input[type="date"]::-webkit-calendar-picker-indicator:hover {
        background-color: #fee2e2;
        transform: scale(1.1);
    }

    input[type="date"]::-webkit-datetime-edit {
        color: #334155;
    }

    .form-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    @media (max-width: 768px) {
        .form-grid-3 {
            grid-template-columns: 1fr;
        }
    }
    /* Autocomplete Floating Menu */
    .medicare-autocomplete-menu {
        position: absolute;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.12);
        z-index: 1050;
        max-height: 200px;
        overflow-y: auto;
        width: auto;
        min-width: 250px;
        max-width: 400px;
        padding: 6px;
        box-sizing: border-box;
    }
    .medicare-autocomplete-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 10px;
        font-size: 13px;
        border-radius: 6px;
        cursor: pointer;
        color: #334155;
        transition: background 0.12s;
        gap: 8px;
    }
    .medicare-autocomplete-item:hover {
        background: #f1f5f9;
    }
    .medicare-autocomplete-text {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .medicare-autocomplete-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }
</style>

{{-- ===== THÔNG TIN CƠ BẢN ===== --}}
<div class="card-modern" style="margin-bottom: 24px; padding: 24px;">
    <h3 style="font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0 0 20px 0; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
        <i data-lucide="info" style="width: 18px; height: 18px; color: var(--accent-color);"></i>
        Thông tin cơ bản
    </h3>
    <div class="form-grid-2">
        <!-- Tên Vắc xin -->
        <div class="form-group-modern" style="grid-column: span 2; margin-bottom: 0;">
            <label for="name" class="form-label-modern">Tên vắc xin <span style="color: #ef4444;">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $vaccine->name) }}" required class="form-control-modern" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>
        </div>

        @if(isset($centers) && ($isSuperAdmin ?? false))
        <div class="form-group-modern" style="grid-column: span 2; margin-bottom: 0;">
            <label for="center_id" class="form-label-modern">Chi nhánh áp dụng giá/tồn kho <span style="color: #ef4444;">*</span></label>
            <select name="center_id" id="center_id" required class="form-control-modern no-custom-select">
                <option value="">-- Chọn chi nhánh --</option>
                <option value="all" {{ (string) old('center_id', $selectedCenterId ?? null) === 'all' ? 'selected' : '' }}>-- Áp dụng cho tất cả chi nhánh --</option>
                @foreach($centers as $center)
                    <option value="{{ $center->id }}" {{ (string) old('center_id', $selectedCenterId ?? null) === (string) $center->id ? 'selected' : '' }}>{{ $center->name }} - {{ $center->phone }}</option>
                @endforeach
            </select>
            <small style="display:block; margin-top:6px; color:#64748b;">Thông tin giá, ưu đãi, tồn kho và nổi bật sẽ tự động cập nhật động theo chi nhánh chọn.</small>
        </div>
        @endif
        @if(!($isSuperAdmin ?? false))
            <input type="hidden" name="center_id" value="{{ $adminUser?->center_id }}">
        @endif

        <!-- Nhóm bệnh -->
        <div class="form-group-modern" style="margin-bottom: 0; position: relative;">
            <label for="category_hidden" class="form-label-modern">Nhóm bệnh</label>
            <input type="hidden" name="category" id="category_hidden" value="{{ old('category', $vaccine->category) }}">
            
            <div id="category_dropdown_wrapper" style="position: relative;">
                <div id="category_trigger" class="form-control-modern" style="cursor: pointer; display: flex; align-items: center; justify-content: space-between; user-select: none; {{ !($isSuperAdmin ?? false) ? 'background-color: #f1f5f9; cursor: not-allowed;' : '' }}">
                    <span id="category_selected_label" style="{{ old('category', $vaccine->category) ? 'color: var(--text-primary); font-weight: 500;' : 'color: #94a3b8;' }}">
                        {{ old('category', $vaccine->category) ?: '-- Chọn hoặc gõ tìm nhóm bệnh --' }}
                    </span>
                    <svg style="width: 14px; height: 14px; color: #64748b; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>

                @if($isSuperAdmin ?? false)
                <div id="category_menu" style="display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.12); z-index: 1000; padding: 8px;">
                    <input type="text" id="category_search_input" placeholder="Gõ tìm hoặc thêm nhóm bệnh mới..." class="form-control-modern" style="margin-bottom: 6px; font-size: 13px; padding: 6px 10px;">
                    
                    <div id="category_list_container" style="max-height: 200px; overflow-y: auto;">
                        @if(isset($categories) && $categories->count())
                            @foreach($categories as $cat)
                                <div class="category-option-item" data-value="{{ $cat }}" style="display: flex; align-items: center; justify-content: space-between; padding: 7px 10px; font-size: 13.5px; border-radius: 6px; cursor: pointer; color: #334155; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                    <span class="cat-item-text" style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-right: 8px;">{{ $cat }}</span>
                                    <div class="cat-item-actions" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-shrink: 0;">
                                        <button type="button" class="btn-cat-edit" data-cat="{{ $cat }}" style="background: none; border: none; color: #475569; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Sửa tên nhóm bệnh">Sửa</button>
                                        <button type="button" class="btn-cat-delete" data-cat="{{ $cat }}" style="background: none; border: none; color: #ef4444; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Xóa nhóm bệnh">Xóa</button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div id="category_custom_add_btn" style="padding: 8px 10px; font-size: 13px; font-weight: 600; color: #c8102e; border-top: 1px solid #f1f5f9; margin-top: 4px; cursor: pointer; display: none;">
                        + Thêm mới: "<span id="category_custom_text"></span>"
                    </div>
                </div>
                @endif

                {{-- MODAL CẢNH BÁO XÓA NHÓM BỆNH --}}
                <div id="cat_delete_modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
                    <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 460px; padding: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); box-sizing: border-box;">
                        <h4 style="margin: 0 0 12px 0; font-family: var(--font-display); font-size: 17px; font-weight: 700; color: #0f172a;">Xác nhận xóa nhóm bệnh</h4>
                        <p id="cat_delete_modal_text" style="margin: 0 0 16px 0; font-size: 14px; color: #475569; line-height: 1.5;"></p>
                        
                        <div id="cat_vaccine_list_box" style="display: none; max-height: 120px; overflow-y: auto; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; margin-bottom: 18px; font-size: 13px; color: #334155;">
                            <strong style="display: block; margin-bottom: 4px; color: #0f172a;">Vắc xin đang sử dụng nhóm bệnh này:</strong>
                            <ul id="cat_vaccine_names_ul" style="margin: 0; padding-left: 18px; line-height: 1.6;"></ul>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 12px;">
                            <button type="button" id="cat_modal_btn_cancel" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer;">Hủy</button>
                            <button type="button" id="cat_modal_btn_confirm" style="background: #c8102e; color: #ffffff; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 700; cursor: pointer;">Xác nhận xóa</button>
                        </div>
                    </div>
                </div>

                {{-- MODAL CHỈNH SỬA TÊN NHÓM BỆNH --}}
                <div id="cat_edit_modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
                    <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 440px; padding: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); box-sizing: border-box;">
                        <h4 style="margin: 0 0 14px 0; font-family: var(--font-display); font-size: 17px; font-weight: 700; color: #0f172a;">Chỉnh sửa tên nhóm bệnh</h4>
                        <div style="margin-bottom: 18px;">
                            <label for="cat_edit_input_name" style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Tên nhóm bệnh mới</label>
                            <input type="text" id="cat_edit_input_name" class="form-control-modern" placeholder="Nhập tên nhóm bệnh...">
                            <input type="hidden" id="cat_edit_old_name">
                        </div>

                        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 12px;">
                            <button type="button" id="cat_edit_btn_cancel" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer;">Hủy</button>
                            <button type="button" id="cat_edit_btn_save" style="background: #c8102e; color: #ffffff; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 700; cursor: pointer;">Lưu thay đổi</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Độ tuổi chỉ định -->
        <div class="form-group-modern" style="margin-bottom: 0; position: relative;">
            <label for="age_group_hidden" class="form-label-modern">Độ tuổi chỉ định <span style="color: #ef4444;">*</span></label>
            <input type="hidden" name="age_group" id="age_group_hidden" value="{{ old('age_group', $vaccine->age_group) }}">
            
            <div id="age_group_dropdown_wrapper" style="position: relative;">
                <div id="age_group_trigger" class="form-control-modern" style="cursor: pointer; display: flex; align-items: center; justify-content: space-between; user-select: none; {{ !($isSuperAdmin ?? false) ? 'background-color: #f1f5f9; cursor: not-allowed;' : '' }}">
                    <span id="age_group_selected_label" style="{{ old('age_group', $vaccine->age_group) ? 'color: var(--text-primary); font-weight: 500;' : 'color: #94a3b8;' }}">
                        {{ old('age_group', $vaccine->age_group) ?: '-- Chọn hoặc gõ tìm độ tuổi chỉ định --' }}
                    </span>
                    <svg style="width: 14px; height: 14px; color: #64748b; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>

                @if($isSuperAdmin ?? false)
                <div id="age_group_menu" class="metadata-menu-box" style="display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.12); z-index: 1000; padding: 8px;">
                    <input type="text" id="age_group_search_input" placeholder="Gõ tìm hoặc thêm độ tuổi mới..." class="form-control-modern" style="margin-bottom: 6px; font-size: 13px; padding: 6px 10px;">
                    
                    <div id="age_group_list_container" class="metadata-list-container" style="max-height: 200px; overflow-y: auto;">
                        @if(isset($ageGroups) && $ageGroups->count())
                            @foreach($ageGroups as $val)
                                <div class="metadata-option-item" data-value="{{ $val }}" style="display: flex; align-items: center; justify-content: space-between; padding: 7px 10px; font-size: 13.5px; border-radius: 6px; cursor: pointer; color: #334155; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                    <span class="meta-item-text" style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-right: 8px;">{{ $val }}</span>
                                    <div class="meta-item-actions" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-shrink: 0;">
                                        <button type="button" class="btn-meta-edit" data-field="age_group" data-value="{{ $val }}" style="background: none; border: none; color: #475569; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Sửa">Sửa</button>
                                        <button type="button" class="btn-meta-delete" data-field="age_group" data-value="{{ $val }}" style="background: none; border: none; color: #ef4444; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Xóa">Xóa</button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div id="age_group_custom_add_btn" class="metadata-custom-add-btn" style="padding: 8px 10px; font-size: 13px; font-weight: 600; color: #c8102e; border-top: 1px solid #f1f5f9; margin-top: 4px; cursor: pointer; display: none;">
                        + Thêm mới: "<span class="metadata-custom-text"></span>"
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Bệnh phòng ngừa -->
        <div class="form-group-modern" style="grid-column: span 2; margin-bottom: 0; position: relative;">
            <label for="disease_prevention_hidden" class="form-label-modern">Bệnh phòng ngừa <span style="color: #ef4444;">*</span></label>
            <input type="hidden" name="disease_prevention" id="disease_prevention_hidden" value="{{ old('disease_prevention', $vaccine->disease_prevention) }}">
            
            <div id="disease_prevention_dropdown_wrapper" style="position: relative;">
                <div id="disease_prevention_trigger" class="form-control-modern" style="cursor: pointer; display: flex; align-items: center; justify-content: space-between; user-select: none; {{ !($isSuperAdmin ?? false) ? 'background-color: #f1f5f9; cursor: not-allowed;' : '' }}">
                    <span id="disease_prevention_selected_label" style="{{ old('disease_prevention', $vaccine->disease_prevention) ? 'color: var(--text-primary); font-weight: 500;' : 'color: #94a3b8;' }}">
                        {{ old('disease_prevention', $vaccine->disease_prevention) ?: '-- Chọn hoặc gõ tìm bệnh phòng ngừa --' }}
                    </span>
                    <svg style="width: 14px; height: 14px; color: #64748b; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>

                @if($isSuperAdmin ?? false)
                <div id="disease_prevention_menu" class="metadata-menu-box" style="display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.12); z-index: 1000; padding: 8px;">
                    <input type="text" id="disease_prevention_search_input" placeholder="Gõ tìm hoặc thêm bệnh phòng ngừa mới..." class="form-control-modern" style="margin-bottom: 6px; font-size: 13px; padding: 6px 10px;">
                    
                    <div id="disease_prevention_list_container" class="metadata-list-container" style="max-height: 200px; overflow-y: auto;">
                        @if(isset($diseasePreventions) && $diseasePreventions->count())
                            @foreach($diseasePreventions as $val)
                                <div class="metadata-option-item" data-value="{{ $val }}" style="display: flex; align-items: center; justify-content: space-between; padding: 7px 10px; font-size: 13.5px; border-radius: 6px; cursor: pointer; color: #334155; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                    <span class="meta-item-text" style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-right: 8px;">{{ $val }}</span>
                                    <div class="meta-item-actions" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-shrink: 0;">
                                        <button type="button" class="btn-meta-edit" data-field="disease_prevention" data-value="{{ $val }}" style="background: none; border: none; color: #475569; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Sửa">Sửa</button>
                                        <button type="button" class="btn-meta-delete" data-field="disease_prevention" data-value="{{ $val }}" style="background: none; border: none; color: #ef4444; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Xóa">Xóa</button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div id="disease_prevention_custom_add_btn" class="metadata-custom-add-btn" style="padding: 8px 10px; font-size: 13px; font-weight: 600; color: #c8102e; border-top: 1px solid #f1f5f9; margin-top: 4px; cursor: pointer; display: none;">
                        + Thêm mới: "<span class="metadata-custom-text"></span>"
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ===== GIÁ CẢ & SỐ LIỀU ===== --}}
<div class="card-modern" style="margin-bottom: 24px; padding: 24px;">
    <h3 style="font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0 0 20px 0; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
        <i data-lucide="dollar-sign" style="width: 18px; height: 18px; color: var(--accent-color);"></i>
        Giá cả & Tồn kho
    </h3>
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <div class="form-grid-3">
            <!-- Giá tiêm gốc -->
            <div class="form-group-modern" style="margin-bottom: 0;">
                <label for="price" class="form-label-modern">Giá gốc / liều (VND) <span style="color: #ef4444;">*</span></label>
                <input type="number" name="price" id="price" value="{{ old('price', $vaccine->price) }}" required min="0" placeholder="VD: 1000000" class="form-control-modern">
            </div>

            <!-- Công cụ tính % giảm giá -->
            <div class="form-group-modern" style="margin-bottom: 0;">
                <label for="discount_percent" class="form-label-modern">% Giảm giá</label>
                <div style="position: relative;">
                    <input type="number" id="discount_percent" min="0" max="100" step="0.1" placeholder="Gõ % (VD: 10)" class="form-control-modern" style="padding-right: 30px;">
                    <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #64748b; font-weight: 600;">%</span>
                </div>
            </div>

            <!-- Giá ưu đãi -->
            <div class="form-group-modern" style="margin-bottom: 0;">
                <label for="sale_price" class="form-label-modern">Giá ưu đãi (VND)</label>
                <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price', $vaccine->sale_price) }}" min="0" placeholder="VD: 995000" class="form-control-modern">
                <div id="sale_price_notice" style="margin-top: 6px; font-size: 12.5px; color: #475569; display: none;"></div>
            </div>
        </div>

        <div class="form-grid-2">
            <!-- Số mũi tiêm (Ẩn đi, tự động tính từ phác đồ con) -->
            <input type="hidden" name="doses" id="doses" value="{{ old('doses', $vaccine->doses ?: 1) }}">

            <!-- Số lượng tồn kho -->
            <div class="form-group-modern" style="margin-bottom: 0;">
                <label for="stock_quantity" class="form-label-modern">Số lượng tồn kho <span style="color: #ef4444;">*</span></label>
                <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $vaccine->stock_quantity ?? 0) }}" required min="0" placeholder="VD: 50" class="form-control-modern">
            </div>

            <!-- Kinh doanh tại chi nhánh -->
            <div class="form-group-modern" style="margin-bottom: 0;">
                <label for="center_is_active" class="form-label-modern">Kinh doanh tại chi nhánh <span style="color: #ef4444;">*</span></label>
                <select name="center_is_active" id="center_is_active" required class="form-control-modern no-custom-select">
                    <option value="1" {{ (int) old('center_is_active', $vaccine->center_is_active ?? 1) === 1 ? 'selected' : '' }}>Đang kinh doanh</option>
                    <option value="0" {{ (int) old('center_is_active', $vaccine->center_is_active ?? 1) === 0 ? 'selected' : '' }}>Tạm ngưng</option>
                </select>
            </div>
        </div>
    </div>
</div>

{{-- ===== NGUỒN GỐC & QUY CÁCH ===== --}}
<div class="card-modern" style="margin-bottom: 24px; padding: 24px;">
    <h3 style="font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0 0 20px 0; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
        <i data-lucide="globe" style="width: 18px; height: 18px; color: var(--accent-color);"></i>
        Nguồn gốc & Quy cách
    </h3>
    <div class="form-grid-2">
        <!-- Hãng sản xuất -->
        <div class="form-group-modern" style="margin-bottom: 0; position: relative;">
            <label for="manufacturer_hidden" class="form-label-modern">Hãng sản xuất</label>
            <input type="hidden" name="manufacturer" id="manufacturer_hidden" value="{{ old('manufacturer', $vaccine->manufacturer) }}">
            
            <div id="manufacturer_dropdown_wrapper" style="position: relative;">
                <div id="manufacturer_trigger" class="form-control-modern" style="cursor: pointer; display: flex; align-items: center; justify-content: space-between; user-select: none; {{ !($isSuperAdmin ?? false) ? 'background-color: #f1f5f9; cursor: not-allowed;' : '' }}">
                    <span id="manufacturer_selected_label" style="{{ old('manufacturer', $vaccine->manufacturer) ? 'color: var(--text-primary); font-weight: 500;' : 'color: #94a3b8;' }}">
                        {{ old('manufacturer', $vaccine->manufacturer) ?: '-- Chọn hoặc gõ tìm hãng sản xuất --' }}
                    </span>
                    <svg style="width: 14px; height: 14px; color: #64748b; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>

                @if($isSuperAdmin ?? false)
                <div id="manufacturer_menu" class="metadata-menu-box" style="display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.12); z-index: 1000; padding: 8px;">
                    <input type="text" id="manufacturer_search_input" placeholder="Gõ tìm hoặc thêm hãng sản xuất mới..." class="form-control-modern" style="margin-bottom: 6px; font-size: 13px; padding: 6px 10px;">
                    
                    <div id="manufacturer_list_container" class="metadata-list-container" style="max-height: 200px; overflow-y: auto;">
                        @if(isset($manufacturers) && $manufacturers->count())
                            @foreach($manufacturers as $val)
                                <div class="metadata-option-item" data-value="{{ $val }}" style="display: flex; align-items: center; justify-content: space-between; padding: 7px 10px; font-size: 13.5px; border-radius: 6px; cursor: pointer; color: #334155; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                    <span class="meta-item-text" style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-right: 8px;">{{ $val }}</span>
                                    <div class="meta-item-actions" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-shrink: 0;">
                                        <button type="button" class="btn-meta-edit" data-field="manufacturer" data-value="{{ $val }}" style="background: none; border: none; color: #475569; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Sửa">Sửa</button>
                                        <button type="button" class="btn-meta-delete" data-field="manufacturer" data-value="{{ $val }}" style="background: none; border: none; color: #ef4444; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Xóa">Xóa</button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div id="manufacturer_custom_add_btn" class="metadata-custom-add-btn" style="padding: 8px 10px; font-size: 13px; font-weight: 600; color: #c8102e; border-top: 1px solid #f1f5f9; margin-top: 4px; cursor: pointer; display: none;">
                        + Thêm mới: "<span class="metadata-custom-text"></span>"
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Quốc gia nguồn gốc -->
        <div class="form-group-modern" style="margin-bottom: 0; position: relative;">
            <label for="origin_hidden" class="form-label-modern">Nước sản xuất (Nguồn gốc) <span style="color: #ef4444;">*</span></label>
            <input type="hidden" name="origin" id="origin_hidden" value="{{ old('origin', $vaccine->origin) }}">
            
            <div id="origin_dropdown_wrapper" style="position: relative;">
                <div id="origin_trigger" class="form-control-modern" style="cursor: pointer; display: flex; align-items: center; justify-content: space-between; user-select: none; {{ !($isSuperAdmin ?? false) ? 'background-color: #f1f5f9; cursor: not-allowed;' : '' }}">
                    <span id="origin_selected_label" style="{{ old('origin', $vaccine->origin) ? 'color: var(--text-primary); font-weight: 500;' : 'color: #94a3b8;' }}">
                        {{ old('origin', $vaccine->origin) ?: '-- Chọn hoặc gõ tìm quốc gia --' }}
                    </span>
                    <svg style="width: 14px; height: 14px; color: #64748b; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>

                @if($isSuperAdmin ?? false)
                <div id="origin_menu" class="metadata-menu-box" style="display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.12); z-index: 1000; padding: 8px;">
                    <input type="text" id="origin_search_input" placeholder="Gõ tìm hoặc thêm quốc gia mới..." class="form-control-modern" style="margin-bottom: 6px; font-size: 13px; padding: 6px 10px;">
                    
                    <div id="origin_list_container" class="metadata-list-container" style="max-height: 200px; overflow-y: auto;">
                        @if(isset($origins) && $origins->count())
                            @foreach($origins as $val)
                                <div class="metadata-option-item" data-value="{{ $val }}" style="display: flex; align-items: center; justify-content: space-between; padding: 7px 10px; font-size: 13.5px; border-radius: 6px; cursor: pointer; color: #334155; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                    <span class="meta-item-text" style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-right: 8px;">{{ $val }}</span>
                                    <div class="meta-item-actions" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-shrink: 0;">
                                        <button type="button" class="btn-meta-edit" data-field="origin" data-value="{{ $val }}" style="background: none; border: none; color: #475569; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Sửa">Sửa</button>
                                        <button type="button" class="btn-meta-delete" data-field="origin" data-value="{{ $val }}" style="background: none; border: none; color: #ef4444; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Xóa">Xóa</button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div id="origin_custom_add_btn" class="metadata-custom-add-btn" style="padding: 8px 10px; font-size: 13px; font-weight: 600; color: #c8102e; border-top: 1px solid #f1f5f9; margin-top: 4px; cursor: pointer; display: none;">
                        + Thêm mới: "<span class="metadata-custom-text"></span>"
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Quy cách đóng gói -->
        <div class="form-group-modern" style="grid-column: span 2; margin-bottom: 0; position: relative;">
            <label for="dosage_hidden" class="form-label-modern">Quy cách liều lượng (Hàm lượng/Đóng gói)</label>
            <input type="hidden" name="dosage" id="dosage_hidden" value="{{ old('dosage', $vaccine->dosage) }}">
            
            <div id="dosage_dropdown_wrapper" style="position: relative;">
                <div id="dosage_trigger" class="form-control-modern" style="cursor: pointer; display: flex; align-items: center; justify-content: space-between; user-select: none; {{ !($isSuperAdmin ?? false) ? 'background-color: #f1f5f9; cursor: not-allowed;' : '' }}">
                    <span id="dosage_selected_label" style="{{ old('dosage', $vaccine->dosage) ? 'color: var(--text-primary); font-weight: 500;' : 'color: #94a3b8;' }}">
                        {{ old('dosage', $vaccine->dosage) ?: '-- Chọn hoặc gõ tìm quy cách --' }}
                    </span>
                    <svg style="width: 14px; height: 14px; color: #64748b; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>

                @if($isSuperAdmin ?? false)
                <div id="dosage_menu" class="metadata-menu-box" style="display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.12); z-index: 1000; padding: 8px;">
                    <input type="text" id="dosage_search_input" placeholder="Gõ tìm hoặc thêm quy cách mới..." class="form-control-modern" style="margin-bottom: 6px; font-size: 13px; padding: 6px 10px;">
                    
                    <div id="dosage_list_container" class="metadata-list-container" style="max-height: 200px; overflow-y: auto;">
                        @if(isset($dosages) && $dosages->count())
                            @foreach($dosages as $val)
                                <div class="metadata-option-item" data-value="{{ $val }}" style="display: flex; align-items: center; justify-content: space-between; padding: 7px 10px; font-size: 13.5px; border-radius: 6px; cursor: pointer; color: #334155; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                    <span class="meta-item-text" style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-right: 8px;">{{ $val }}</span>
                                    <div class="meta-item-actions" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-shrink: 0;">
                                        <button type="button" class="btn-meta-edit" data-field="dosage" data-value="{{ $val }}" style="background: none; border: none; color: #475569; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Sửa">Sửa</button>
                                        <button type="button" class="btn-meta-delete" data-field="dosage" data-value="{{ $val }}" style="background: none; border: none; color: #ef4444; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Xóa">Xóa</button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div id="dosage_custom_add_btn" class="metadata-custom-add-btn" style="padding: 8px 10px; font-size: 13px; font-weight: 600; color: #c8102e; border-top: 1px solid #f1f5f9; margin-top: 4px; cursor: pointer; display: none;">
                        + Thêm mới: "<span class="metadata-custom-text"></span>"
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ===== NỘI DUNG CHUYÊN MÔN ĐÃ XÁC MINH ===== --}}
<div class="card-modern" style="margin-bottom: 24px; padding: 24px;">
    <h3 style="font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0 0 8px 0; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
        <i data-lucide="clipboard-check" style="width: 18px; height: 18px; color: var(--accent-color);"></i>
        Nội dung chuyên môn đã xác minh
    </h3>
    <p style="margin: 0 0 18px 0; color: #64748b; font-size: 13px;">Chỉ nhập nội dung có trong tài liệu nguồn của đúng sản phẩm. Để trống nếu chưa xác minh.</p>
    <div class="form-grid-2">
        <!-- Đường dùng -->
        <div class="form-group-modern" style="margin-bottom: 0; position: relative;">
            <label for="administration_route_hidden" class="form-label-modern">Đường dùng</label>
            <input type="hidden" name="administration_route" id="administration_route_hidden" value="{{ old('administration_route', $vaccine->administration_route) }}">
            
            <div id="administration_route_dropdown_wrapper" style="position: relative;">
                <div id="administration_route_trigger" class="form-control-modern" style="cursor: pointer; display: flex; align-items: center; justify-content: space-between; user-select: none; {{ !($isSuperAdmin ?? false) ? 'background-color: #f1f5f9; cursor: not-allowed;' : '' }}">
                    <span id="administration_route_selected_label" style="{{ old('administration_route', $vaccine->administration_route) ? 'color: var(--text-primary); font-weight: 500;' : 'color: #94a3b8;' }}">
                        {{ old('administration_route', $vaccine->administration_route) ?: '-- Chọn hoặc gõ tìm đường dùng --' }}
                    </span>
                    <svg style="width: 14px; height: 14px; color: #64748b; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>

                @if($isSuperAdmin ?? false)
                <div id="administration_route_menu" class="metadata-menu-box" style="display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.12); z-index: 1000; padding: 8px;">
                    <input type="text" id="administration_route_search_input" placeholder="Gõ tìm hoặc thêm đường dùng mới..." class="form-control-modern" style="margin-bottom: 6px; font-size: 13px; padding: 6px 10px;">
                    
                    <div id="administration_route_list_container" class="metadata-list-container" style="max-height: 200px; overflow-y: auto;">
                        @if(isset($administrationRoutes) && $administrationRoutes->count())
                            @foreach($administrationRoutes as $val)
                                <div class="metadata-option-item" data-value="{{ $val }}" style="display: flex; align-items: center; justify-content: space-between; padding: 7px 10px; font-size: 13.5px; border-radius: 6px; cursor: pointer; color: #334155; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                    <span class="meta-item-text" style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-right: 8px;">{{ $val }}</span>
                                    <div class="meta-item-actions" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-shrink: 0;">
                                        <button type="button" class="btn-meta-edit" data-field="administration_route" data-value="{{ $val }}" style="background: none; border: none; color: #475569; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Sửa">Sửa</button>
                                        <button type="button" class="btn-meta-delete" data-field="administration_route" data-value="{{ $val }}" style="background: none; border: none; color: #ef4444; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Xóa">Xóa</button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div id="administration_route_custom_add_btn" class="metadata-custom-add-btn" style="padding: 8px 10px; font-size: 13px; font-weight: 600; color: #c8102e; border-top: 1px solid #f1f5f9; margin-top: 4px; cursor: pointer; display: none;">
                        + Thêm mới: "<span class="metadata-custom-text"></span>"
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div class="form-group-modern" style="margin-bottom: 0; position: relative;">
            <label for="source_review_date_display" class="form-label-modern">Ngày rà soát nguồn</label>
            <div class="medicare-datepicker-wrapper" style="position: relative;">
                <input type="hidden" name="source_review_date" id="source_review_date" value="{{ old('source_review_date', $vaccine->source_review_date?->format('Y-m-d')) }}">
                
                <div id="source_review_date_trigger" class="form-control-modern" style="cursor: pointer; display: flex; align-items: center; justify-content: space-between; user-select: none; {{ !($isSuperAdmin ?? false) ? 'background-color: #f1f5f9; cursor: not-allowed;' : '' }}">
                    <span id="source_review_date_display" style="{{ old('source_review_date', $vaccine->source_review_date) ? 'color: var(--text-primary); font-weight: 500;' : 'color: #94a3b8;' }}">
                        {{ old('source_review_date', $vaccine->source_review_date) ? \Carbon\Carbon::parse(old('source_review_date', $vaccine->source_review_date))->format('d/m/Y') : 'dd/mm/yyyy' }}
                    </span>
                    <svg style="width: 16px; height: 16px; color: #94a3b8; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>

                @if($isSuperAdmin ?? false)
                <div id="source_review_date_popup" style="display: none; position: absolute; top: calc(100% + 4px); right: 0; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; box-shadow: 0 15px 30px -5px rgba(0,0,0,0.15); z-index: 1050; padding: 16px; width: 280px; box-sizing: border-box; user-select: none;">
                    <!-- Header: Month/Year navigation -->
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <button type="button" id="dp_prev_month" style="background: none; border: none; font-size: 18px; color: #475569; cursor: pointer; padding: 2px 8px; border-radius: 6px;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">‹</button>
                        <span id="dp_month_year_title" style="font-weight: 700; font-size: 14px; color: #0f172a; font-family: var(--font-display);">Tháng 8, 2026</span>
                        <button type="button" id="dp_next_month" style="background: none; border: border: none; font-size: 18px; color: #475569; cursor: pointer; padding: 2px 8px; border-radius: 6px;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">›</button>
                    </div>

                    <!-- Weekdays Header -->
                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; text-align: center; font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 6px;">
                        <div>T2</div><div>T3</div><div>T4</div><div>T5</div><div>T6</div><div>T7</div><div style="color:#ef4444;">CN</div>
                    </div>

                    <!-- Days Grid -->
                    <div id="dp_days_grid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 3px; text-align: center;"></div>

                    <!-- Footer Actions -->
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 12px; padding-top: 10px; border-top: 1px solid #f1f5f9;">
                        <button type="button" id="dp_btn_clear" style="background: none; border: none; color: #64748b; font-size: 12.5px; font-weight: 600; cursor: pointer; padding: 4px 8px; border-radius: 4px;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#64748b'">Xóa</button>
                        <button type="button" id="dp_btn_today" style="background: none; border: none; color: #c8102e; font-size: 12.5px; font-weight: 700; cursor: pointer; padding: 4px 8px; border-radius: 4px;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='none'">Hôm nay</button>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <input type="hidden" name="detailed_schedule" id="detailed_schedule" value="{{ old('detailed_schedule', $vaccine->detailed_schedule) }}">

        @foreach([
            'contraindications' => 'Chống chỉ định',
            'adverse_effects' => 'Phản ứng bất lợi',
            'warnings' => 'Cảnh báo và thận trọng',
        ] as $field => $label)
            <div class="form-group-modern" style="grid-column: span 2; margin-bottom: 0;">
                <label for="{{ $field }}" class="form-label-modern">{{ $label }}</label>
                <textarea name="{{ $field }}" id="{{ $field }}" rows="4" class="form-control-modern" style="font-family: inherit; resize: vertical;" placeholder="Chỉ nhập theo tài liệu nguồn" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>{{ old($field, $vaccine->$field) }}</textarea>
            </div>
        @endforeach
        <div class="form-group-modern" style="grid-column: span 2; margin-bottom: 0;">
            <label for="source_reference_url" class="form-label-modern">URL nguồn / tài liệu tham khảo</label>
            <input type="url" name="source_reference_url" id="source_reference_url" value="{{ old('source_reference_url', $vaccine->source_reference_url) }}" placeholder="https://..." class="form-control-modern" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>
        </div>
    </div>
</div>

{{-- ===== PHÁC ĐỒ TIÊM CHỦNG THEO ĐỘ TUỔI ===== --}}
<div class="card-modern" style="margin-bottom: 24px; padding: 24px;">
    <h3 style="font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0 0 20px 0; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
        <i data-lucide="list-todo" style="width: 18px; height: 18px; color: var(--accent-color);"></i>
        Phác đồ tiêm chủng theo độ tuổi (Có thể cấu hình giá riêng cho từng phác đồ)
    </h3>
    
    <div class="table-responsive-modern" style="margin-bottom: 16px;">
        <table class="table-modern" id="regimens-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Độ tuổi (Ví dụ: Trẻ từ 9 - 14 tuổi) <span style="color: #ef4444;">*</span></th>
                    <th style="width: 10%; text-align: center;">Số mũi tiêm <span style="color: #ef4444;">*</span></th>
                    <th style="width: 15%; text-align: center;">Giá gốc riêng (VND)</th>
                    <th style="width: 15%; text-align: center;">Giá ưu đãi riêng (VND)</th>
                    <th style="width: 25%;">Mô tả phác đồ (Ví dụ: Mũi 1 và Mũi 2 cách nhau 6 tháng)</th>
                    <th style="width: 10%; text-align: center;">Hành động</th>
                </tr>
            </thead>
            <tbody id="regimens-tbody">
                @php
                    $regimens = old('regimens', $vaccine->exists ? $vaccine->regimens : collect());
                @endphp
                @forelse($regimens as $index => $regimen)
                    @php
                        $regimenId = is_array($regimen) ? ($regimen['id'] ?? null) : $regimen->id;
                        $ageGroup = is_array($regimen) ? $regimen['age_group'] : $regimen->age_group;
                        $dosesVal = is_array($regimen) ? $regimen['doses'] : $regimen->doses;
                        $priceVal = is_array($regimen) ? ($regimen['price'] ?? null) : $regimen->price;
                        $salePriceVal = is_array($regimen) ? ($regimen['sale_price'] ?? null) : $regimen->sale_price;
                        $desc = is_array($regimen) ? ($regimen['schedule_description'] ?? '') : $regimen->schedule_description;
                    @endphp
                    <tr data-index="{{ $index }}">
                        <td>
                            @if($regimenId)
                                <input type="hidden" name="regimens[{{ $index }}][id]" value="{{ $regimenId }}">
                            @endif
                            <div style="position: relative;">
                                <input type="text" name="regimens[{{ $index }}][age_group]" value="{{ $ageGroup }}" required class="form-control-modern regimen-age-group-input" placeholder="VD: Trẻ em từ 9-14 tuổi" autocomplete="off" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <input type="number" name="regimens[{{ $index }}][doses]" value="{{ $dosesVal }}" required min="1" class="form-control-modern" style="text-align: center;" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>
                        </td>
                        <td>
                            <input type="number" name="regimens[{{ $index }}][price]" value="{{ $priceVal }}" min="0" class="form-control-modern" placeholder="Mặc định" style="text-align: right;" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>
                        </td>
                        <td>
                            <input type="number" name="regimens[{{ $index }}][sale_price]" value="{{ $salePriceVal }}" min="0" class="form-control-modern" placeholder="Mặc định" style="text-align: right;" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>
                        </td>
                        <td>
                            <div style="position: relative;">
                                <input type="text" name="regimens[{{ $index }}][schedule_description]" value="{{ $desc }}" class="form-control-modern regimen-schedule-input" placeholder="VD: Cách nhau 6 tháng" autocomplete="off" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            @if($isSuperAdmin ?? false)
                                <button type="button" class="btn-action-sm btn-action-danger" onclick="removeRegimenRow(this)" style="background:#fee2e2; color:#b91c1c; border:none; padding:6px 12px; border-radius:4px; cursor:pointer;">Xóa</button>
                            @else
                                <span style="color:var(--text-light); font-size:12px;">N/A</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    @if(!($isSuperAdmin ?? false))
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted);">Không có phác đồ tiêm chủng nào được định nghĩa.</td>
                        </tr>
                    @endif
                @endforelse
            </tbody>
        </table>
    </div>

    @if($isSuperAdmin ?? false)
        <div style="text-align: left;">
            <button type="button" class="btn-modern" id="add-regimen-btn" style="background:#f4f8fa; border:1px dashed var(--accent-color); color:var(--accent-color); font-weight:700; padding:8px 16px; border-radius:6px; cursor:pointer;">
                <i data-lucide="plus-circle" style="width:16px; height:16px; display:inline-block; vertical-align:middle; margin-right:4px;"></i> Thêm phác đồ mới
            </button>
        </div>
    @endif
</div>



{{-- ===== HÌNH ẢNH & HIỂN THỊ ===== --}}
<div class="card-modern" style="margin-bottom: 24px; padding: 24px;">
    <h3 style="font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0 0 20px 0; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
        <i data-lucide="image" style="width: 18px; height: 18px; color: var(--accent-color);"></i>
        Hình ảnh & Hiển thị
    </h3>
    <div class="form-grid-2">
        <!-- Tải lên hình ảnh -->
        <div class="form-group-modern" style="margin-bottom: 0; grid-column: span 2;">
            <label class="form-label-modern">Hình ảnh vắc xin</label>
            <input type="file" name="image_file" id="image_file" accept="image/*" style="display: none;" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>
            <input type="hidden" name="image" id="image_hidden" value="{{ old('image', $vaccine->image) }}">
            
            <div id="image_dropzone" class="image-upload-zone {{ !($isSuperAdmin ?? false) ? 'disabled-zone' : '' }}" style="{{ !($isSuperAdmin ?? false) ? 'opacity: 0.7; cursor: not-allowed; background-color: #f1f5f9; border-color: #cbd5e1;' : '' }}">
                <div id="dropzone_prompt" style="{{ $vaccine->image ? 'display: none;' : '' }}">
                    <i data-lucide="upload-cloud" style="width: 40px; height: 40px; color: var(--text-light); margin-bottom: 8px; display: inline-block;"></i>
                    <p style="font-weight: 600; color: var(--text-muted); margin: 0 0 4px 0;">Kéo thả hình ảnh vào đây hoặc click để tải lên</p>
                    <span style="font-size: 12px; color: var(--text-light);">Hỗ trợ: JPG, PNG, GIF, WEBP (Tối đa 2MB)</span>
                </div>
                <div id="image_preview_container" class="image-upload-preview-container" style="{{ $vaccine->image ? 'display: block;' : '' }}">
                    <div class="image-upload-preview-wrapper" style="text-align: center;">
                        <img id="image_preview" class="image-upload-preview" src="{{ $vaccine->image ? asset('images/vaccines/' . $vaccine->image) : '' }}" alt="Xem trước hình ảnh">
                        @if($isSuperAdmin ?? false)
                        <button type="button" id="btn_remove_image" class="image-upload-remove-btn" title="Xóa hình ảnh">
                            <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                        </button>
                        @endif
                    </div>
                    @if($isSuperAdmin ?? false)
                    <div style="text-align: center; margin-top: 6px;">
                        <button type="button" id="btn_recrop_image" class="btn-recrop-trigger" style="display: none;">
                            <i data-lucide="crop"></i> Cắt lại hình ảnh
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Thứ tự hiển thị -->
        <div class="form-group-modern" style="margin-bottom: 0; grid-column: span 2;">
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
<div class="card-modern" style="padding: 24px;">
    <h3 style="font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0 0 20px 0; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
        <i data-lucide="file-text" style="width: 18px; height: 18px; color: var(--accent-color);"></i>
        Mô tả chi tiết
    </h3>
    <div class="form-group-modern" style="margin-bottom: 0;">
        <textarea name="description" id="description" rows="5" placeholder="Nhập công dụng chi tiết, hướng dẫn, lưu ý phác đồ tiêm chủng..." class="form-control-modern" style="font-family: inherit; resize: vertical;" {{ !($isSuperAdmin ?? false) ? 'disabled' : '' }}>{{ old('description', $vaccine->description) }}</textarea>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ===== HÀM CHUẨN HÓA DROPDOWN ĐỒNG BỘ 100% CSS =====
        function initCustomDropdown(selectEl, enableSearch = false) {
            if (!selectEl) return;
            if (selectEl.dataset.customDropdownInitialized) return;
            selectEl.dataset.customDropdownInitialized = 'true';
            selectEl.style.display = 'none';
            
            const wrapper = document.createElement('div');
            wrapper.className = 'custom-select-wrapper';
            wrapper.style.position = 'relative';
            selectEl.parentNode.insertBefore(wrapper, selectEl);
            wrapper.appendChild(selectEl);
            
            const trigger = document.createElement('div');
            trigger.className = 'form-control-modern custom-select-trigger';
            trigger.style.cursor = 'pointer';
            trigger.style.display = 'flex';
            trigger.style.alignItems = 'center';
            trigger.style.justifyContent = 'space-between';
            trigger.style.userSelect = 'none';
            
            if (selectEl.disabled) {
                trigger.style.backgroundColor = '#f1f5f9';
                trigger.style.cursor = 'not-allowed';
            }
            
            const labelSpan = document.createElement('span');
            labelSpan.className = 'custom-select-label';
            const selectedOption = selectEl.options[selectEl.selectedIndex];
            labelSpan.textContent = selectedOption ? selectedOption.text : '-- Chọn --';
            if (selectedOption && selectedOption.value !== '') {
                labelSpan.style.color = 'var(--text-primary)';
                labelSpan.style.fontWeight = '500';
            } else {
                labelSpan.style.color = '#94a3b8';
            }
            
            const arrowSpan = document.createElement('span');
            arrowSpan.style.display = 'flex';
            arrowSpan.style.alignItems = 'center';
            arrowSpan.innerHTML = '<svg style="width: 14px; height: 14px; color: #64748b; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';
            
            trigger.appendChild(labelSpan);
            trigger.appendChild(arrowSpan);
            wrapper.appendChild(trigger);
            
            if (selectEl.disabled) return;
            
            const menu = document.createElement('div');
            menu.className = 'custom-select-menu';
            menu.style.display = 'none';
            menu.style.position = 'absolute';
            menu.style.top = 'calc(100% + 4px)';
            menu.style.left = '0';
            menu.style.right = '0';
            menu.style.background = '#ffffff';
            menu.style.border = '1px solid #cbd5e1';
            menu.style.borderRadius = '8px';
            menu.style.boxShadow = '0 10px 25px -5px rgba(0,0,0,0.12)';
            menu.style.zIndex = '1000';
            menu.style.padding = '8px';
            
            let searchInput = null;
            if (enableSearch) {
                searchInput = document.createElement('input');
                searchInput.type = 'text';
                searchInput.placeholder = 'Gõ tìm kiếm...';
                searchInput.className = 'form-control-modern';
                searchInput.style.marginBottom = '6px';
                searchInput.style.fontSize = '13px';
                searchInput.style.padding = '6px 10px';
                menu.appendChild(searchInput);
            }
            
            const listContainer = document.createElement('div');
            listContainer.style.maxHeight = '200px';
            listContainer.style.overflowY = 'auto';
            
            function buildOptions() {
                listContainer.innerHTML = '';
                Array.from(selectEl.options).forEach(opt => {
                    const item = document.createElement('div');
                    item.className = 'custom-select-option-item';
                    item.setAttribute('data-value', opt.value);
                    item.textContent = opt.text;
                    item.style.padding = '7px 10px';
                    item.style.fontSize = '13.5px';
                    item.style.borderRadius = '6px';
                    item.style.cursor = 'pointer';
                    item.style.color = '#334155';
                    item.style.transition = 'background 0.15s';
                    
                    if (selectEl.value === opt.value) {
                        item.style.background = '#fee2e2';
                        item.style.fontWeight = '600';
                        item.style.color = '#c8102e';
                    }
                    
                    item.addEventListener('mouseover', function() {
                        if (selectEl.value !== opt.value) this.style.background = '#f1f5f9';
                    });
                    item.addEventListener('mouseout', function() {
                        if (selectEl.value !== opt.value) this.style.background = 'transparent';
                    });
                    
                    item.addEventListener('click', function(e) {
                        e.stopPropagation();
                        selectEl.value = opt.value;
                        labelSpan.textContent = opt.text;
                        labelSpan.style.color = opt.value !== '' ? 'var(--text-primary)' : '#94a3b8';
                        labelSpan.style.fontWeight = opt.value !== '' ? '500' : 'normal';
                        menu.style.display = 'none';
                        selectEl.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                    
                    listContainer.appendChild(item);
                });
            }
            
            buildOptions();
            menu.appendChild(listContainer);
            wrapper.appendChild(menu);
            
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                const isExpanded = menu.style.display === 'block';
                document.querySelectorAll('.custom-select-menu').forEach(m => m.style.display = 'none');
                document.querySelectorAll('#category_menu').forEach(m => m.style.display = 'none');
                
                menu.style.display = isExpanded ? 'none' : 'block';
                if (!isExpanded && searchInput) {
                    searchInput.value = '';
                    filterItems('');
                    setTimeout(() => searchInput.focus(), 50);
                }
            });
            
            function filterItems(term) {
                const query = term.toLowerCase().trim();
                const items = listContainer.querySelectorAll('.custom-select-option-item');
                items.forEach(item => {
                    const txt = item.textContent.toLowerCase();
                    if (txt.includes(query)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    filterItems(this.value);
                });
            }
            
            document.addEventListener('click', function(e) {
                if (!wrapper.contains(e.target)) {
                    menu.style.display = 'none';
                }
            });
        }

        // Khởi tạo các Select Dropdown khác về cùng 1 CSS
        const centerSelectInput = document.getElementById('center_id');
        if (centerSelectInput) initCustomDropdown(centerSelectInput, true);

        const activeSelectInput = document.getElementById('center_is_active');
        if (activeSelectInput) initCustomDropdown(activeSelectInput, false);
        // ===== 0. CẤU HÌNH DROPDOWN NHÓM BỆNH CÓ Ô TÌM KIẾM =====
        const catWrapper = document.getElementById('category_dropdown_wrapper');
        const catTrigger = document.getElementById('category_trigger');
        const catMenu = document.getElementById('category_menu');
        const catSearchInput = document.getElementById('category_search_input');
        const catHiddenInput = document.getElementById('category_hidden');
        const catSelectedLabel = document.getElementById('category_selected_label');
        const catListContainer = document.getElementById('category_list_container');
        const catCustomAddBtn = document.getElementById('category_custom_add_btn');
        const catCustomText = document.getElementById('category_custom_text');

        if (catTrigger && catMenu) {
            catTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                const isExpanded = catMenu.style.display === 'block';
                catMenu.style.display = isExpanded ? 'none' : 'block';
                if (!isExpanded && catSearchInput) {
                    catSearchInput.value = '';
                    filterCategoryItems('');
                    setTimeout(() => catSearchInput.focus(), 50);
                }
            });

            document.addEventListener('click', function(e) {
                if (catWrapper && !catWrapper.contains(e.target)) {
                    catMenu.style.display = 'none';
                }
            });

            function filterCategoryItems(term) {
                const query = term.toLowerCase().trim();
                const items = catListContainer ? catListContainer.querySelectorAll('.category-option-item') : [];
                let exactMatch = false;

                items.forEach(item => {
                    const val = item.getAttribute('data-value').toLowerCase();
                    if (val === query) exactMatch = true;
                    if (val.includes(query)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (query.length > 0 && !exactMatch && catCustomAddBtn && catCustomText) {
                    catCustomText.textContent = term.trim();
                    catCustomAddBtn.style.display = 'block';
                } else if (catCustomAddBtn) {
                    catCustomAddBtn.style.display = 'none';
                }
            }

            if (catSearchInput) {
                catSearchInput.addEventListener('input', function() {
                    filterCategoryItems(this.value);
                });
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (catListContainer) {
                catListContainer.addEventListener('click', function(e) {
                    const editBtn = e.target.closest('.btn-cat-edit');
                    const deleteBtn = e.target.closest('.btn-cat-delete');
                    const item = e.target.closest('.category-option-item');

                    if (editBtn) {
                        e.stopPropagation();
                        const oldCat = editBtn.getAttribute('data-cat');
                        openEditCatModal(oldCat);
                        return;
                    }

                    if (deleteBtn) {
                        e.stopPropagation();
                        const catToDelete = deleteBtn.getAttribute('data-cat');
                        openDeleteCatModal(catToDelete);
                        return;
                    }

                    if (item) {
                        const val = item.getAttribute('data-value');
                        selectCategoryValue(val);
                    }
                });
            }

            function openEditCatModal(oldCat) {
                const editModal = document.getElementById('cat_edit_modal');
                const editInputName = document.getElementById('cat_edit_input_name');
                const editOldName = document.getElementById('cat_edit_old_name');
                const editBtnSave = document.getElementById('cat_edit_btn_save');
                const editBtnCancel = document.getElementById('cat_edit_btn_cancel');

                if (!editModal || !editInputName || !editOldName || !editBtnSave) return;

                editInputName.value = oldCat;
                editOldName.value = oldCat;
                editModal.style.display = 'flex';
                setTimeout(() => editInputName.focus(), 50);

                editBtnSave.onclick = function() {
                    const newCat = editInputName.value.trim();
                    if (!newCat) {
                        alert('Vui lòng nhập tên nhóm bệnh!');
                        return;
                    }
                    if (newCat === oldCat) {
                        editModal.style.display = 'none';
                        return;
                    }

                    fetch('{{ route("admin.categories.update") }}', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ old_name: oldCat, new_name: newCat })
                    })
                    .then(res => res.json())
                    .then(data => {
                        editModal.style.display = 'none';
                        if (catHiddenInput && catHiddenInput.value === oldCat) {
                            selectCategoryValue(newCat);
                        }
                        updateCategoryOptions(data.categories, catHiddenInput ? catHiddenInput.value : newCat);
                    })
                    .catch(err => console.error(err));
                };

                editBtnCancel.onclick = function() {
                    editModal.style.display = 'none';
                };
            }

            function openDeleteCatModal(category) {
                fetch('{{ route("admin.categories.check-delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ category: category })
                })
                .then(res => res.json())
                .then(data => {
                    const modal = document.getElementById('cat_delete_modal');
                    const modalText = document.getElementById('cat_delete_modal_text');
                    const listBox = document.getElementById('cat_vaccine_list_box');
                    const ul = document.getElementById('cat_vaccine_names_ul');
                    const confirmBtn = document.getElementById('cat_modal_btn_confirm');
                    const cancelBtn = document.getElementById('cat_modal_btn_cancel');

                    if (!modal || !confirmBtn) return;

                    if (data.has_vaccines) {
                        modalText.innerHTML = `Nhóm bệnh <strong>"${data.category}"</strong> đang được sử dụng bởi <strong>${data.vaccine_count} vắc xin</strong>. Bạn có chắc chắn muốn xóa nhóm bệnh này không?`;
                        listBox.style.display = 'block';
                        ul.innerHTML = data.vaccine_names.map(name => `<li>${name}</li>`).join('');
                    } else {
                        modalText.innerHTML = `Bạn có chắc chắn muốn xóa nhóm bệnh <strong>"${data.category}"</strong> không?`;
                        listBox.style.display = 'none';
                        ul.innerHTML = '';
                    }

                    modal.style.display = 'flex';

                    confirmBtn.onclick = function() {
                        fetch('{{ route("admin.categories.destroy") }}', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ category: category })
                        })
                        .then(res => res.json())
                        .then(resData => {
                            modal.style.display = 'none';
                            updateCategoryOptions(resData.categories, '');
                        });
                    };

                    cancelBtn.onclick = function() {
                        modal.style.display = 'none';
                    };
                });
            }

            function updateCategoryOptions(categories, selectedVal) {
                if (!catListContainer) return;
                catListContainer.innerHTML = '';
                categories.forEach(cat => {
                    const div = document.createElement('div');
                    div.className = 'category-option-item';
                    div.setAttribute('data-value', cat);
                    div.style.display = 'flex';
                    div.style.alignItems = 'center';
                    div.style.justifyContent = 'space-between';
                    div.style.padding = '7px 10px';
                    div.style.fontSize = '13.5px';
                    div.style.borderRadius = '6px';
                    div.style.cursor = 'pointer';
                    div.style.color = '#334155';
                    div.style.transition = 'background 0.15s';
                    div.onmouseover = () => div.style.background = '#f1f5f9';
                    div.onmouseout = () => div.style.background = 'transparent';

                    div.innerHTML = `
                        <span class="cat-item-text" style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-right: 8px;">${cat}</span>
                        <div class="cat-item-actions" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-shrink: 0;">
                            <button type="button" class="btn-cat-edit" data-cat="${cat}" style="background: none; border: none; color: #475569; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Sửa tên nhóm bệnh">Sửa</button>
                            <button type="button" class="btn-cat-delete" data-cat="${cat}" style="background: none; border: none; color: #ef4444; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Xóa nhóm bệnh">Xóa</button>
                        </div>
                    `;
                    catListContainer.appendChild(div);
                });

                if (selectedVal !== undefined) {
                    selectCategoryValue(selectedVal);
                }
            }

            if (catCustomAddBtn) {
                catCustomAddBtn.addEventListener('click', function() {
                    if (catSearchInput && catSearchInput.value.trim()) {
                        selectCategoryValue(catSearchInput.value.trim());
                    }
                });
            }

            function selectCategoryValue(val) {
                if (catHiddenInput) catHiddenInput.value = val;
                if (catSelectedLabel) {
                    catSelectedLabel.textContent = val || '-- Chọn hoặc gõ tìm nhóm bệnh --';
                    catSelectedLabel.style.color = val ? 'var(--text-primary)' : '#94a3b8';
                    catSelectedLabel.style.fontWeight = val ? '500' : 'normal';
                }
                catMenu.style.display = 'none';
            }
        }

        // ===== 0.5. CẤU HÌNH CÁC DROPDOWN METADATA KHÁC =====
        function initMetadataDropdown(field, labelText) {
            const wrapper = document.getElementById(field + '_dropdown_wrapper');
            const trigger = document.getElementById(field + '_trigger');
            const menu = document.getElementById(field + '_menu');
            const searchInput = document.getElementById(field + '_search_input');
            const hiddenInput = document.getElementById(field + '_hidden');
            const selectedLabel = document.getElementById(field + '_selected_label');
            const listContainer = document.getElementById(field + '_list_container');
            const customAddBtn = document.getElementById(field + '_custom_add_btn');
            const customTextSpan = wrapper ? wrapper.querySelector('.metadata-custom-text') : null;

            if (!trigger || !menu) return;

            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                const isExpanded = menu.style.display === 'block';
                // Đóng tất cả các menu khác
                document.querySelectorAll('.custom-select-menu').forEach(m => m.style.display = 'none');
                document.querySelectorAll('#category_menu').forEach(m => m.style.display = 'none');
                document.querySelectorAll('.metadata-menu-box').forEach(m => {
                    if (m !== menu) m.style.display = 'none';
                });

                menu.style.display = isExpanded ? 'none' : 'block';
                if (!isExpanded && searchInput) {
                    searchInput.value = '';
                    filterMetaItems('');
                    setTimeout(() => searchInput.focus(), 50);
                }
            });

            document.addEventListener('click', function(e) {
                if (wrapper && !wrapper.contains(e.target)) {
                    menu.style.display = 'none';
                }
            });

            function filterMetaItems(term) {
                const query = term.toLowerCase().trim();
                const items = listContainer ? listContainer.querySelectorAll('.metadata-option-item') : [];
                let exactMatch = false;

                items.forEach(item => {
                    const val = item.getAttribute('data-value').toLowerCase();
                    if (val === query) exactMatch = true;
                    if (val.includes(query)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (query.length > 0 && !exactMatch && customAddBtn && customTextSpan) {
                    customTextSpan.textContent = term.trim();
                    customAddBtn.style.display = 'block';
                } else if (customAddBtn) {
                    customAddBtn.style.display = 'none';
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    filterMetaItems(this.value);
                });
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (listContainer) {
                listContainer.addEventListener('click', function(e) {
                    const editBtn = e.target.closest('.btn-meta-edit');
                    const deleteBtn = e.target.closest('.btn-meta-delete');
                    const item = e.target.closest('.metadata-option-item');

                    if (editBtn) {
                        e.stopPropagation();
                        const val = editBtn.getAttribute('data-value');
                        openEditMetaModal(field, val, labelText);
                        return;
                    }

                    if (deleteBtn) {
                        e.stopPropagation();
                        const val = deleteBtn.getAttribute('data-value');
                        openDeleteMetaModal(field, val, labelText);
                        return;
                    }

                    if (item) {
                        const val = item.getAttribute('data-value');
                        selectMetaValue(val);
                    }
                });
            }

            if (customAddBtn) {
                customAddBtn.addEventListener('click', function() {
                    if (searchInput && searchInput.value.trim()) {
                        selectMetaValue(searchInput.value.trim());
                    }
                });
            }

            function selectMetaValue(val) {
                if (hiddenInput) hiddenInput.value = val;
                if (selectedLabel) {
                    selectedLabel.textContent = val || '-- Chọn hoặc gõ tìm ' + labelText + ' --';
                    selectedLabel.style.color = val ? 'var(--text-primary)' : '#94a3b8';
                    selectedLabel.style.fontWeight = val ? '500' : 'normal';
                }
                menu.style.display = 'none';
            }

            function openEditMetaModal(field, oldValue, labelText) {
                const editModal = document.getElementById('cat_edit_modal');
                const editInputName = document.getElementById('cat_edit_input_name');
                const editOldName = document.getElementById('cat_edit_old_name');
                const editBtnSave = document.getElementById('cat_edit_btn_save');
                const editBtnCancel = document.getElementById('cat_edit_btn_cancel');
                const editTitle = editModal ? editModal.querySelector('h4') : null;

                if (!editModal || !editInputName || !editOldName || !editBtnSave) return;

                if (editTitle) editTitle.textContent = 'Chỉnh sửa ' + labelText;
                editInputName.value = oldValue;
                editOldName.value = oldValue;
                editModal.style.display = 'flex';
                setTimeout(() => editInputName.focus(), 50);

                editBtnSave.onclick = function() {
                    const newValue = editInputName.value.trim();
                    if (!newValue) {
                        alert('Vui lòng nhập giá trị!');
                        return;
                    }
                    if (newValue === oldValue) {
                        editModal.style.display = 'none';
                        return;
                    }

                    fetch('{{ route("admin.metadata.update") }}', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ field: field, old_value: oldValue, new_value: newValue })
                    })
                    .then(res => res.json())
                    .then(data => {
                        editModal.style.display = 'none';
                        if (hiddenInput && hiddenInput.value === oldValue) {
                            selectMetaValue(newValue);
                        }
                        updateMetaOptions(data.values, hiddenInput ? hiddenInput.value : newValue);
                    })
                    .catch(err => console.error(err));
                };

                editBtnCancel.onclick = function() {
                    editModal.style.display = 'none';
                };
            }

            function openDeleteMetaModal(field, value, labelText) {
                fetch('{{ route("admin.metadata.check-delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ field: field, value: value })
                })
                .then(res => res.json())
                .then(data => {
                    const modal = document.getElementById('cat_delete_modal');
                    const modalTitle = modal ? modal.querySelector('h4') : null;
                    const modalText = document.getElementById('cat_delete_modal_text');
                    const listBox = document.getElementById('cat_vaccine_list_box');
                    const ul = document.getElementById('cat_vaccine_names_ul');
                    const confirmBtn = document.getElementById('cat_modal_btn_confirm');
                    const cancelBtn = document.getElementById('cat_modal_btn_cancel');

                    if (!modal || !confirmBtn) return;

                    if (modalTitle) modalTitle.textContent = 'Xác nhận xóa ' + labelText;

                    if (data.has_vaccines) {
                        modalText.innerHTML = `Giá trị <strong>"${data.value}"</strong> đang được sử dụng bởi <strong>${data.vaccine_count} vắc xin</strong>. Bạn có chắc chắn muốn xóa không?`;
                        listBox.style.display = 'block';
                        ul.innerHTML = data.vaccine_names.map(name => `<li>${name}</li>`).join('');
                    } else {
                        modalText.innerHTML = `Bạn có chắc chắn muốn xóa giá trị <strong>"${data.value}"</strong> không?`;
                        listBox.style.display = 'none';
                        ul.innerHTML = '';
                    }

                    modal.style.display = 'flex';

                    confirmBtn.onclick = function() {
                        fetch('{{ route("admin.metadata.destroy") }}', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ field: field, value: value })
                        })
                        .then(res => {
                            if (!res.ok) {
                                return res.json().then(errData => {
                                    throw new Error(errData.message || "Có lỗi xảy ra.");
                                });
                            }
                            return res.json();
                        })
                        .then(resData => {
                            modal.style.display = 'none';
                            updateMetaOptions(resData.values, '');
                        })
                        .catch(err => {
                            modal.style.display = 'none';
                            alert(err.message);
                        });
                    };

                    cancelBtn.onclick = function() {
                        modal.style.display = 'none';
                    };
                });
            }

            function updateMetaOptions(values, selectedVal) {
                if (!listContainer) return;
                listContainer.innerHTML = '';
                values.forEach(val => {
                    const div = document.createElement('div');
                    div.className = 'metadata-option-item';
                    div.setAttribute('data-value', val);
                    div.style.display = 'flex';
                    div.style.alignItems = 'center';
                    div.style.justifyContent = 'space-between';
                    div.style.padding = '7px 10px';
                    div.style.fontSize = '13.5px';
                    div.style.borderRadius = '6px';
                    div.style.cursor = 'pointer';
                    div.style.color = '#334155';
                    div.style.transition = 'background 0.15s';
                    div.onmouseover = () => div.style.background = '#f1f5f9';
                    div.onmouseout = () => div.style.background = 'transparent';

                    div.innerHTML = `
                        <span class="meta-item-text" style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-right: 8px;">${val}</span>
                        <div class="meta-item-actions" style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-shrink: 0;">
                            <button type="button" class="btn-meta-edit" data-field="${field}" data-value="${val}" style="background: none; border: none; color: #475569; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Sửa">Sửa</button>
                            <button type="button" class="btn-meta-delete" data-field="${field}" data-value="${val}" style="background: none; border: none; color: #ef4444; font-size: 12px; font-weight: 500; cursor: pointer; padding: 0 4px;" title="Xóa">Xóa</button>
                        </div>
                    `;
                    listContainer.appendChild(div);
                });

                if (selectedVal !== undefined) {
                    selectMetaValue(selectedVal);
                }
            }
        }

        // Khởi tạo các dropdown metadata
        initMetadataDropdown('disease_prevention', 'bệnh phòng ngừa');
        initMetadataDropdown('origin', 'quốc gia');
        initMetadataDropdown('dosage', 'quy cách');
        initMetadataDropdown('age_group', 'độ tuổi chỉ định');
        initMetadataDropdown('manufacturer', 'hãng sản xuất');
        initMetadataDropdown('administration_route', 'đường dùng');

        // ===== 0.6. CHỨC NĂNG AUTOCOMPLETE CHO PHÁC ĐỒ TIÊM CHỦNG =====
        function initAutocompleteForInput(inputEl, field, labelText) {
            if (!inputEl) return;

            let values = [];
            if (field === 'regimen_age_group') {
                values = @json($regimenAgeGroups ?? []);
            } else if (field === 'regimen_schedule_description') {
                values = @json($regimenScheduleDescriptions ?? []);
            }

            let menu = null;

            const closeOnScroll = function() {
                hideMenu();
            };

            inputEl.addEventListener('focus', function() {
                showMenu();
            });

            inputEl.addEventListener('input', function() {
                if (!menu) {
                    showMenu();
                } else {
                    filterItems(this.value);
                }
            });

            document.addEventListener('click', function(e) {
                if (menu && !menu.contains(e.target) && e.target !== inputEl) {
                    hideMenu();
                }
            });

            function showMenu() {
                document.querySelectorAll('.medicare-autocomplete-menu').forEach(m => m.remove());

                menu = document.createElement('div');
                menu.className = 'medicare-autocomplete-menu';
                
                const rect = inputEl.getBoundingClientRect();
                menu.style.position = 'absolute';
                menu.style.top = (rect.bottom + window.scrollY) + 'px';
                menu.style.left = (rect.left + window.scrollX) + 'px';
                menu.style.width = rect.width + 'px';
                menu.style.zIndex = '9999';

                renderItems(values);
                document.body.appendChild(menu);
                filterItems(inputEl.value);

                window.addEventListener('scroll', closeOnScroll, true);
                window.addEventListener('resize', closeOnScroll);
            }

            function hideMenu() {
                if (menu) {
                    menu.remove();
                    menu = null;
                    window.removeEventListener('scroll', closeOnScroll, true);
                    window.removeEventListener('resize', closeOnScroll);
                }
            }

            function filterItems(term) {
                if (!menu) return;
                const query = term.toLowerCase().trim();
                const items = menu.querySelectorAll('.medicare-autocomplete-item');

                items.forEach(item => {
                    const val = item.getAttribute('data-value').toLowerCase();
                    if (val.includes(query)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            function renderItems(list) {
                if (!menu) return;
                menu.innerHTML = '';
                
                if (list.length === 0) {
                    menu.innerHTML = '<div style="padding:8px; font-size:12px; color:#94a3b8; text-align:center;">Chưa có dữ liệu lịch sử</div>';
                    return;
                }

                list.forEach(val => {
                    const item = document.createElement('div');
                    item.className = 'medicare-autocomplete-item';
                    item.setAttribute('data-value', val);

                    item.innerHTML = `
                        <span class="medicare-autocomplete-text">${val}</span>
                        <div class="medicare-autocomplete-actions">
                            <button type="button" class="btn-meta-edit-inline" style="background:none; border:none; color:#475569; font-size:11px; font-weight:500; cursor:pointer; padding:0 2px;" title="Sửa">Sửa</button>
                            <button type="button" class="btn-meta-delete-inline" style="background:none; border:none; color:#ef4444; font-size:11px; font-weight:500; cursor:pointer; padding:0 2px;" title="Xóa">Xóa</button>
                        </div>
                    `;

                    item.addEventListener('click', function(e) {
                        const editBtn = e.target.closest('.btn-meta-edit-inline');
                        const deleteBtn = e.target.closest('.btn-meta-delete-inline');

                        if (editBtn) {
                            e.stopPropagation();
                            openEditModal(val);
                            return;
                        }

                        if (deleteBtn) {
                            e.stopPropagation();
                            openDeleteModal(val);
                            return;
                        }

                        inputEl.value = val;
                        inputEl.dispatchEvent(new Event('input', { bubbles: true }));
                        hideMenu();
                    });

                    menu.appendChild(item);
                });
            }

            function openEditModal(oldValue) {
                const editModal = document.getElementById('cat_edit_modal');
                const editInputName = document.getElementById('cat_edit_input_name');
                const editOldName = document.getElementById('cat_edit_old_name');
                const editBtnSave = document.getElementById('cat_edit_btn_save');
                const editBtnCancel = document.getElementById('cat_edit_btn_cancel');
                const editTitle = editModal ? editModal.querySelector('h4') : null;

                if (!editModal || !editInputName || !editOldName || !editBtnSave) return;

                if (editTitle) editTitle.textContent = 'Chỉnh sửa ' + labelText;
                editInputName.value = oldValue;
                editOldName.value = oldValue;
                editModal.style.display = 'flex';
                setTimeout(() => editInputName.focus(), 50);

                editBtnSave.onclick = function() {
                    const newValue = editInputName.value.trim();
                    if (!newValue) {
                        alert('Vui lòng nhập giá trị!');
                        return;
                    }
                    if (newValue === oldValue) {
                        editModal.style.display = 'none';
                        return;
                    }

                    fetch('{{ route("admin.metadata.update") }}', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ field: field, old_value: oldValue, new_value: newValue })
                    })
                    .then(res => res.json())
                    .then(data => {
                        editModal.style.display = 'none';
                        document.querySelectorAll('.regimen-age-group-input, .regimen-schedule-input').forEach(el => {
                            if (el.value === oldValue) {
                                el.value = newValue;
                                el.dispatchEvent(new Event('input', { bubbles: true }));
                            }
                        });
                        values = data.values;
                        hideMenu();
                    })
                    .catch(err => console.error(err));
                };

                editBtnCancel.onclick = function() {
                    editModal.style.display = 'none';
                };
            }

            function openDeleteModal(value) {
                fetch('{{ route("admin.metadata.check-delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ field: field, value: value })
                })
                .then(res => res.json())
                .then(data => {
                    const modal = document.getElementById('cat_delete_modal');
                    const modalTitle = modal ? modal.querySelector('h4') : null;
                    const modalText = document.getElementById('cat_delete_modal_text');
                    const listBox = document.getElementById('cat_vaccine_list_box');
                    const ul = document.getElementById('cat_vaccine_names_ul');
                    const confirmBtn = document.getElementById('cat_modal_btn_confirm');
                    const cancelBtn = document.getElementById('cat_modal_btn_cancel');

                    if (!modal || !confirmBtn) return;

                    if (modalTitle) modalTitle.textContent = 'Xác nhận xóa ' + labelText;

                    if (data.has_vaccines) {
                        modalText.innerHTML = `Giá trị phác đồ <strong>"${data.value}"</strong> đang được sử dụng ở <strong>${data.vaccine_count} vắc xin</strong>. Bạn có chắc chắn muốn xóa không?`;
                        listBox.style.display = 'block';
                        ul.innerHTML = data.vaccine_names.map(name => `<li>${name}</li>`).join('');
                    } else {
                        modalText.innerHTML = `Bạn có chắc chắn muốn xóa giá trị phác đồ <strong>"${data.value}"</strong> không?`;
                        listBox.style.display = 'none';
                        ul.innerHTML = '';
                    }

                    modal.style.display = 'flex';

                    confirmBtn.onclick = function() {
                        fetch('{{ route("admin.metadata.destroy") }}', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ field: field, value: value })
                        })
                        .then(res => res.json())
                        .then(resData => {
                            modal.style.display = 'none';
                            document.querySelectorAll('.regimen-age-group-input, .regimen-schedule-input').forEach(el => {
                                if (el.value === value) {
                                    el.value = '';
                                    el.dispatchEvent(new Event('input', { bubbles: true }));
                                }
                            });
                            values = resData.values;
                            hideMenu();
                        })
                        .catch(err => console.error(err));
                    };

                    cancelBtn.onclick = function() {
                        modal.style.display = 'none';
                    };
                });
            }
        }

        // Khởi tạo các ô phác đồ tiêm hiện tại
        document.querySelectorAll('.regimen-age-group-input').forEach(el => initAutocompleteForInput(el, 'regimen_age_group', 'độ tuổi phác đồ'));
        document.querySelectorAll('.regimen-schedule-input').forEach(el => initAutocompleteForInput(el, 'regimen_schedule_description', 'lịch tiêm'));

        // ===== 1. CÔNG CỤ TÍNH % GIẢM GIÁ 1 CHIỀU =====
        const priceInput = document.getElementById('price');
        const discountInput = document.getElementById('discount_percent');
        const salePriceInput = document.getElementById('sale_price');
        const saleNotice = document.getElementById('sale_price_notice');

        function updateSalePriceNotice() {
            if (!priceInput || !salePriceInput || !saleNotice) return;
            const priceVal = parseFloat(priceInput.value);
            const saleVal = parseFloat(salePriceInput.value);

            if (!isNaN(priceVal) && priceVal > 0 && !isNaN(saleVal) && saleVal > 0 && saleVal < priceVal) {
                const actualPctRaw = ((priceVal - saleVal) / priceVal) * 100;
                const actualPctFormatted = (Math.round(actualPctRaw * 10) / 10).toFixed(1).replace('.0', '');
                const floorPct = Math.floor(actualPctRaw);

                if (floorPct > 0) {
                    saleNotice.style.display = 'block';
                    if (actualPctRaw === floorPct || actualPctFormatted === String(floorPct)) {
                        saleNotice.textContent = `Mức giảm hiện tại: ${floorPct}%`;
                    } else {
                        saleNotice.textContent = `Mức giảm hiện tại xấp xỉ ${actualPctFormatted}%. Gợi ý mức giảm nguyên: ${floorPct}%`;
                    }
                    return;
                }
            }
            saleNotice.style.display = 'none';
            saleNotice.textContent = '';
        }

        function calculateSalePriceFromPercent() {
            if (!priceInput || !discountInput || !salePriceInput) return;
            const priceVal = parseFloat(priceInput.value);
            const discountVal = parseFloat(discountInput.value);

            if (!isNaN(discountVal) && discountVal === 0) {
                // Nhập 0% -> Tự động nhận diện là Không giảm giá (để trống ô Giá ưu đãi)
                salePriceInput.value = '';
                updateSalePriceNotice();
            } else if (!isNaN(priceVal) && priceVal > 0 && !isNaN(discountVal) && discountVal > 0 && discountVal <= 100) {
                let calculated = priceVal * (1 - (discountVal / 100));
                // Tính chính xác giá tiền (làm tròn số nguyên đồng, không làm tròn hàng nghìn)
                calculated = Math.round(calculated);
                salePriceInput.value = calculated;
                updateSalePriceNotice();
            }
        }

        // Tính ban đầu nếu đã có sẵn price và sale_price
        if (priceInput && salePriceInput && priceInput.value && salePriceInput.value) {
            const p = parseFloat(priceInput.value);
            const s = parseFloat(salePriceInput.value);
            if (p > 0 && s > 0 && s < p) {
                const initialPct = Math.round(((p - s) / p) * 100 * 10) / 10;
                discountInput.value = initialPct;
            }
            updateSalePriceNotice();
        }

        if (discountInput) {
            discountInput.addEventListener('input', calculateSalePriceFromPercent);
        }
        if (priceInput) {
            priceInput.addEventListener('input', function() {
                if (discountInput && discountInput.value !== '') {
                    calculateSalePriceFromPercent();
                } else {
                    updateSalePriceNotice();
                }
            });
        }
        if (salePriceInput) {
            salePriceInput.addEventListener('input', function() {
                // Không tự động gõ đè con số vào ô discountInput!
                // Cập nhật thông báo mức giảm xấp xỉ văn bản thuần bên dưới ô Giá ưu đãi:
                updateSalePriceNotice();
            });
        }

        // ===== 2. SPA AJAX FETCH CHO CHI NHÁNH =====
        const centerSelect = document.getElementById('center_id');
        if (centerSelect) {
            centerSelect.addEventListener('change', function() {
                const centerId = this.value;
                if (!centerId) return;

                const vaccineId = "{{ $vaccine->id ?? '' }}";
                if (!vaccineId) return; // Form thêm mới không cần fetch AJAX edit

                fetch(`{{ url('/admin/vaccines') }}/${vaccineId}/center-data?center_id=${encodeURIComponent(centerId)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success && res.data) {
                        const d = res.data;
                        if (priceInput) priceInput.value = d.price ?? '';
                        if (salePriceInput) salePriceInput.value = d.sale_price ?? '';
                        
                        const qtyInput = document.getElementById('stock_quantity');
                        if (qtyInput) qtyInput.value = d.stock_quantity ?? 0;

                        const activeSelect = document.getElementById('center_is_active');
                        if (activeSelect) activeSelect.value = d.center_is_active ?? 1;

                        const featuredCheckbox = document.querySelector('input[name="is_featured"]');
                        if (featuredCheckbox) featuredCheckbox.checked = !!d.is_featured;

                        const sortOrderInput = document.getElementById('sort_order');
                        if (sortOrderInput) sortOrderInput.value = d.sort_order ?? 0;

                        // Tính lại % gợi ý nếu có giá ưu đãi
                        if (d.price && d.sale_price && d.price > 0 && d.sale_price < d.price) {
                            const pct = Math.round(((d.price - d.sale_price) / d.price) * 100 * 10) / 10;
                            if (discountInput) discountInput.value = pct;
                        } else if (discountInput) {
                            discountInput.value = '';
                        }
                    }
                })
                .catch(err => console.error('Error fetching center vaccine data:', err));
            });
        }

        // ===== 3. HÌNH ẢNH DROPZONE WITH IMAGE CROPPER =====
        const dropzone = document.getElementById('image_dropzone');
        const fileInput = document.getElementById('image_file');
        const hiddenInput = document.getElementById('image_hidden');
        const promptBlock = document.getElementById('dropzone_prompt');
        const previewContainer = document.getElementById('image_preview_container');
        const previewImg = document.getElementById('image_preview');
        const removeBtn = document.getElementById('btn_remove_image');
        const recropBtn = document.getElementById('btn_recrop_image');
        let currentVaccineRawFile = null;

        if (dropzone && fileInput) {
            dropzone.addEventListener('click', function(e) {
                if (dropzone.classList.contains('disabled-zone')) return;
                if (e.target.closest('#btn_remove_image') || e.target.closest('#btn_recrop_image')) return;
                fileInput.click();
            });

            fileInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    handleFiles(this.files);
                }
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, function(e) {
                    if (dropzone.classList.contains('disabled-zone')) {
                        e.preventDefault();
                        e.stopPropagation();
                        return;
                    }
                    highlight(e);
                }, false);
            });
            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, function(e) {
                    if (dropzone.classList.contains('disabled-zone')) {
                        e.preventDefault();
                        e.stopPropagation();
                        return;
                    }
                    unhighlight(e);
                }, false);
            });

            function highlight(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.style.borderColor = 'var(--accent-color)';
                dropzone.style.backgroundColor = '#f1f5f9';
            }

            function unhighlight(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.style.borderColor = 'var(--border-color)';
                dropzone.style.backgroundColor = '#f8fafc';
            }

            dropzone.addEventListener('drop', function(e) {
                if (dropzone.classList.contains('disabled-zone')) {
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files && files.length > 0) {
                    handleFiles(files);
                }
            });

            function handleFiles(files) {
                if (files.length === 0) return;
                const file = files[0];
                if (!file.type.startsWith('image/')) {
                    if (window.AppDialog) {
                        window.AppDialog.alert('Vui lòng chỉ tải lên tệp hình ảnh.');
                    } else {
                        alert('Vui lòng chỉ tải lên tệp hình ảnh.');
                    }
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    if (window.AppDialog) {
                        window.AppDialog.alert('Dung lượng hình ảnh không được vượt quá 5 MB.');
                    } else {
                        alert('Dung lượng hình ảnh không được vượt quá 5 MB.');
                    }
                    return;
                }

                currentVaccineRawFile = file;

                // Mở Modal Cắt Ảnh (Tỷ lệ 1:1 mặc định cho vắc-xin)
                if (typeof window.openMedicareCropperModal === 'function') {
                    window.openMedicareCropperModal({
                        file: file,
                        defaultRatio: 1,
                        ratioName: '1/1',
                        onCropComplete: function(croppedBlob, croppedDataUrl, croppedFile) {
                            const dt = new DataTransfer();
                            dt.items.add(croppedFile);
                            fileInput.files = dt.files;

                            previewImg.src = croppedDataUrl;
                            promptBlock.style.display = 'none';
                            previewContainer.style.display = 'block';
                            if (recropBtn) recropBtn.style.display = 'inline-flex';
                            hiddenInput.value = '';

                            if (typeof lucide !== 'undefined') {
                                lucide.createIcons();
                            }
                        }
                    });
                } else {
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onloadend = function() {
                        previewImg.src = reader.result;
                        promptBlock.style.display = 'none';
                        previewContainer.style.display = 'block';
                        hiddenInput.value = '';
                    };
                }
            }

            if (recropBtn) {
                recropBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (currentVaccineRawFile && typeof window.openMedicareCropperModal === 'function') {
                        window.openMedicareCropperModal({
                            file: currentVaccineRawFile,
                            defaultRatio: 1,
                            ratioName: '1/1',
                            onCropComplete: function(croppedBlob, croppedDataUrl, croppedFile) {
                                const dt = new DataTransfer();
                                dt.items.add(croppedFile);
                                fileInput.files = dt.files;
                                previewImg.src = croppedDataUrl;
                            }
                        });
                    }
                });
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    fileInput.value = '';
                    hiddenInput.value = '';
                    previewImg.src = '';
                    currentVaccineRawFile = null;
                    previewContainer.style.display = 'none';
                    if (recropBtn) recropBtn.style.display = 'none';
                    promptBlock.style.display = 'block';
                });
            }
        }
        // ===== 3. CẤU HÌNH BỘ LỊCH POPUP MEDICARE DATEPICKER =====
        const dpTrigger = document.getElementById('source_review_date_trigger');
        const dpPopup = document.getElementById('source_review_date_popup');
        const dpDisplay = document.getElementById('source_review_date_display');
        const dpHidden = document.getElementById('source_review_date');
        const dpTitle = document.getElementById('dp_month_year_title');
        const dpDaysGrid = document.getElementById('dp_days_grid');
        const dpPrevBtn = document.getElementById('dp_prev_month');
        const dpNextBtn = document.getElementById('dp_next_month');
        const dpClearBtn = document.getElementById('dp_btn_clear');
        const dpTodayBtn = document.getElementById('dp_btn_today');

        const todayObj = new Date();
        todayObj.setHours(0, 0, 0, 0);

        let viewYear = todayObj.getFullYear();
        let viewMonth = todayObj.getMonth();
        let selectedDateStr = dpHidden ? dpHidden.value : '';

        if (selectedDateStr) {
            const parts = selectedDateStr.split('-');
            if (parts.length === 3) {
                viewYear = parseInt(parts[0], 10);
                viewMonth = parseInt(parts[1], 10) - 1;
            }
        }

        const monthNames = [
            'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
            'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
        ];

        function renderCalendar() {
            if (!dpDaysGrid || !dpTitle) return;
            dpTitle.textContent = `${monthNames[viewMonth]}, ${viewYear}`;
            dpDaysGrid.innerHTML = '';

            const firstDayOfMonth = new Date(viewYear, viewMonth, 1);
            let startDay = firstDayOfMonth.getDay();
            startDay = (startDay === 0) ? 6 : startDay - 1;

            const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

            for (let i = 0; i < startDay; i++) {
                const emptyCell = document.createElement('div');
                dpDaysGrid.appendChild(emptyCell);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dayBtn = document.createElement('div');
                dayBtn.textContent = day;
                dayBtn.style.padding = '6px 0';
                dayBtn.style.fontSize = '13px';
                dayBtn.style.borderRadius = '8px';
                dayBtn.style.cursor = 'pointer';
                dayBtn.style.transition = 'all 0.15s';
                dayBtn.style.color = '#334155';

                const curDateObj = new Date(viewYear, viewMonth, day);
                curDateObj.setHours(0, 0, 0, 0);

                const yyyy = viewYear;
                const mm = String(viewMonth + 1).padStart(2, '0');
                const dd = String(day).padStart(2, '0');
                const dateIso = `${yyyy}-${mm}-${dd}`;

                const isFuture = curDateObj > todayObj;
                const isToday = curDateObj.getTime() === todayObj.getTime();
                const isSelected = selectedDateStr === dateIso;

                if (isFuture) {
                    dayBtn.style.color = '#cbd5e1';
                    dayBtn.style.cursor = 'not-allowed';
                    dayBtn.style.opacity = '0.5';
                } else if (isSelected) {
                    dayBtn.style.background = '#c8102e';
                    dayBtn.style.color = '#ffffff';
                    dayBtn.style.fontWeight = '700';
                } else if (isToday) {
                    dayBtn.style.border = '1px solid #c8102e';
                    dayBtn.style.color = '#c8102e';
                    dayBtn.style.fontWeight = '700';
                }

                if (!isFuture) {
                    dayBtn.addEventListener('mouseover', function() {
                        if (!isSelected) {
                            this.style.background = '#fee2e2';
                            this.style.color = '#991b1b';
                        }
                    });
                    dayBtn.addEventListener('mouseout', function() {
                        if (!isSelected) {
                            this.style.background = 'transparent';
                            this.style.color = isToday ? '#c8102e' : '#334155';
                        }
                    });
                    dayBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        selectDate(dateIso, `${dd}/${mm}/${yyyy}`);
                    });
                }

                dpDaysGrid.appendChild(dayBtn);
            }
        }

        function selectDate(isoStr, displayStr) {
            selectedDateStr = isoStr;
            if (dpHidden) dpHidden.value = isoStr;
            if (dpDisplay) {
                dpDisplay.textContent = displayStr;
                dpDisplay.style.color = 'var(--text-primary)';
                dpDisplay.style.fontWeight = '500';
            }
            if (dpPopup) dpPopup.style.display = 'none';
        }

        if (dpTrigger && dpPopup) {
            dpTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                const isExpanded = dpPopup.style.display === 'block';
                document.querySelectorAll('.custom-select-menu').forEach(m => m.style.display = 'none');
                document.querySelectorAll('#category_menu').forEach(m => m.style.display = 'none');

                dpPopup.style.display = isExpanded ? 'none' : 'block';
                if (!isExpanded) {
                    renderCalendar();
                }
            });

            document.addEventListener('click', function(e) {
                if (dpPopup && !dpPopup.contains(e.target) && !dpTrigger.contains(e.target)) {
                    dpPopup.style.display = 'none';
                }
            });

            if (dpPrevBtn) {
                dpPrevBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    viewMonth--;
                    if (viewMonth < 0) {
                        viewMonth = 11;
                        viewYear--;
                    }
                    renderCalendar();
                });
            }

            if (dpNextBtn) {
                dpNextBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    viewMonth++;
                    if (viewMonth > 11) {
                        viewMonth = 0;
                        viewYear++;
                    }
                    renderCalendar();
                });
            }

            if (dpClearBtn) {
                dpClearBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    selectedDateStr = '';
                    if (dpHidden) dpHidden.value = '';
                    if (dpDisplay) {
                        dpDisplay.textContent = 'dd/mm/yyyy';
                        dpDisplay.style.color = '#94a3b8';
                        dpDisplay.style.fontWeight = 'normal';
                    }
                    if (dpPopup) dpPopup.style.display = 'none';
                });
            }

            if (dpTodayBtn) {
                dpTodayBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const now = new Date();
                    const yyyy = now.getFullYear();
                    const mm = String(now.getMonth() + 1).padStart(2, '0');
                    const dd = String(now.getDate()).padStart(2, '0');
                    viewYear = yyyy;
                    viewMonth = now.getMonth();
                    selectDate(`${yyyy}-${mm}-${dd}`, `${dd}/${mm}/${yyyy}`);
                });
            }

            // ===== QUẢN LÝ PHÁC ĐỒ TIÊM CHỦNG =====
            const addBtn = document.getElementById('add-regimen-btn');
            const tbody = document.getElementById('regimens-tbody');
            if (addBtn && tbody) {
                let rowIndex = tbody.querySelectorAll('tr[data-index]').length;

                addBtn.addEventListener('click', () => {
                    const tr = document.createElement('tr');
                    tr.setAttribute('data-index', rowIndex);
                    tr.innerHTML = `
                        <td>
                            <div style="position: relative;">
                                <input type="text" name="regimens[${rowIndex}][age_group]" required class="form-control-modern regimen-age-group-input" placeholder="VD: Trẻ em từ 9-14 tuổi" autocomplete="off">
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <input type="number" name="regimens[${rowIndex}][doses]" value="1" required min="1" class="form-control-modern" style="text-align: center;">
                        </td>
                        <td>
                            <input type="number" name="regimens[${rowIndex}][price]" min="0" class="form-control-modern" placeholder="Mặc định" style="text-align: right;">
                        </td>
                        <td>
                            <input type="number" name="regimens[${rowIndex}][sale_price]" min="0" class="form-control-modern" placeholder="Mặc định" style="text-align: right;">
                        </td>
                        <td>
                            <div style="position: relative;">
                                <input type="text" name="regimens[${rowIndex}][schedule_description]" class="form-control-modern regimen-schedule-input" placeholder="VD: Cách nhau 6 tháng" autocomplete="off">
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <button type="button" class="btn-action-sm btn-action-danger" onclick="removeRegimenRow(this)" style="background:#fee2e2; color:#b91c1c; border:none; padding:6px 12px; border-radius:4px; cursor:pointer;">Xóa</button>
                        </td>
                    `;
                    tbody.appendChild(tr);

                    const ageInput = tr.querySelector('.regimen-age-group-input');
                    const schedInput = tr.querySelector('.regimen-schedule-input');
                    if (typeof initAutocompleteForInput === 'function') {
                        initAutocompleteForInput(ageInput, 'regimen_age_group', 'độ tuổi phác đồ');
                        initAutocompleteForInput(schedInput, 'regimen_schedule_description', 'lịch tiêm');
                    }
                    
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                    rowIndex++;
                });
            }
        }
    });

    function removeRegimenRow(btn) {
        const tr = btn.closest('tr');
        if (tr) {
            tr.remove();
        }
    }
</script>
@endsection
