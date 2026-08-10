@extends('vaccine::layouts.admin')

@section('title', 'Đăng ký nhanh tại quầy')
@section('page_title', 'Đăng Ký Nhanh Tại Quầy')

@section('admin_content')
<div style="max-width:1000px; margin:0 auto; display:grid; gap:24px;">
    <div class="card-modern" style="display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; align-items:center;">
        <div>
            <h3 style="margin:0 0 5px;">Tạo phiếu cho khách đến trực tiếp</h3>
            <p style="margin:0; color:var(--text-muted);">Giá và khung giờ được chốt theo chi nhánh đã chọn.</p>
        </div>
        <a href="{{ route('admin.registrations.index') }}" class="btn-action-sm">Quay lại danh sách</a>
    </div>

    @if($isSuperAdmin ?? false)
        <form method="GET" action="{{ route('admin.registrations.create') }}" class="card-modern" style="display:flex; gap:12px; flex-wrap:wrap; align-items:end;">
            <div style="flex:1 1 260px;">
                <label class="form-label-modern" for="center_switcher">Chi nhánh tạo phiếu</label>
                <select id="center_switcher" name="center_id" class="form-control-modern" onchange="this.form.submit()">
                    <option value="">-- Chọn chi nhánh --</option>
                    @foreach($centers as $item)
                        <option value="{{ $item->id }}" {{ $center?->id === $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    @endif

    @if(!$center)
        <div class="card-modern" style="color:var(--text-muted); font-weight:600;">Vui lòng chọn một chi nhánh cụ thể để tạo phiếu tại quầy.</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger"><ul style="margin:0; padding-left:20px;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    @if($center)
    <form method="POST" action="{{ route('admin.registrations.store') }}" class="card-modern" style="display:grid; gap:22px;">
        @csrf
        <input type="hidden" name="center_id" value="{{ $center->id }}">
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
            <div>
                <label class="form-label-modern" for="patient_name">Họ tên người tiêm</label>
                <input class="form-control-modern" id="patient_name" name="patient_name" value="{{ old('patient_name') }}" autocomplete="name" required>
            </div>
            <div>
                <label class="form-label-modern" for="patient_phone">Số điện thoại</label>
                <input class="form-control-modern" id="patient_phone" name="patient_phone" value="{{ old('patient_phone') }}" inputmode="tel" autocomplete="tel" placeholder="0912345678" required>
            </div>
            <div>
                <label class="form-label-modern" for="booking_status">Trạng thái lịch</label>
                <select class="form-control-modern" id="booking_status" name="booking_status">
                    <option value="confirmed" {{ old('booking_status', 'confirmed') === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                    <option value="pending" {{ old('booking_status') === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                </select>
            </div>
        </div>

        <div>
            <label class="form-label-modern" for="slot_id">Ngày và khung giờ tại {{ $center->name }}</label>
            <select class="form-control-modern" id="slot_id" name="slot_id" required>
                <option value="">-- Chọn khung giờ còn chỗ --</option>
                @foreach($slots->groupBy(fn ($slot) => $slot->schedule->date->format('d/m/Y')) as $date => $dateSlots)
                    <optgroup label="{{ $date }}">
                        @foreach($dateSlots as $slot)
                            <option value="{{ $slot->id }}" {{ (string) old('slot_id') === (string) $slot->id ? 'selected' : '' }}>{{ $slot->start_at }} - {{ $slot->end_at }} (còn {{ $slot->capacity - $slot->reserved_count }} chỗ)</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @if($slots->isEmpty())<small style="display:block; margin-top:6px; color:#b91c1c;">Chi nhánh chưa có khung giờ còn chỗ.</small>@endif
        </div>

        @php
            $origins = $vaccines->map(fn($cv) => $cv->vaccine->origin)->filter()->unique()->sort();
            $ageGroups = $vaccines->map(fn($cv) => $cv->vaccine->age_group)->filter()->unique()->sort();
            $categories = $vaccines->map(fn($cv) => $cv->vaccine->category)->filter()->unique()->sort();
        @endphp
        <div>
            <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:12px;">
                <label class="form-label-modern" style="margin:0;">Vắc xin đăng ký <span class="required">*</span></label>
                
                <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; background:#f1f5f9; padding:10px; border-radius:10px;">
                    <input type="text" id="vaccine_search" placeholder="Tìm tên vắc xin..." style="flex:1 1 200px; padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box; height: 36px;">
                    
                    <select id="filter_type" style="padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; height: 36px; background:#fff; min-width:120px;">
                        <option value="all">Tất cả loại</option>
                        <option value="single">Vắc xin lẻ</option>
                        <option value="package">Gói vắc xin</option>
                    </select>

                    <select id="filter_category" style="padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; height: 36px; background:#fff; min-width:120px;">
                        <option value="all">Mọi nhóm bệnh</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>

                    <select id="filter_origin" style="padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; height: 36px; background:#fff; min-width:120px;">
                        <option value="all">Mọi xuất xứ</option>
                        @foreach($origins as $origin)
                            <option value="{{ $origin }}">{{ $origin }}</option>
                        @endforeach
                    </select>

                    <select id="filter_age_group" style="padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; height: 36px; background:#fff; min-width:120px;">
                        <option value="all">Mọi độ tuổi</option>
                        @foreach($ageGroups as $ageGroup)
                            <option value="{{ $ageGroup }}">{{ $ageGroup }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Vùng hiển thị vắc xin đã chọn -->
            <div id="selected_vaccines_summary" style="display:none; margin-bottom:14px; padding:12px; background:#fffbeb; border:1px solid #fde68a; border-radius:10px;">
                <strong style="font-size:12.5px; color:#b45309; display:block; margin-bottom:8px;">Danh sách vắc xin đã chọn (<span id="selected_count">0</span>):</strong>
                <div id="selected_vaccines_list" style="display:flex; flex-wrap:wrap; gap:6px;"></div>
            </div>

            <div id="vaccine_list_container" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:10px; max-height:420px; overflow:auto; padding:4px;">
                @foreach($vaccines as $centerVaccine)
                    @php($price = $centerVaccine->hasSalePrice() ? $centerVaccine->sale_price : $centerVaccine->price)
                    <label class="vaccine-search-item" 
                           data-name="{{ strtolower($centerVaccine->vaccine->name) }} {{ strtolower($centerVaccine->vaccine->origin) }}"
                           data-type="{{ $centerVaccine->vaccine->type }}"
                           data-category="{{ $centerVaccine->vaccine->category }}"
                           data-origin="{{ $centerVaccine->vaccine->origin }}"
                           data-age-group="{{ $centerVaccine->vaccine->age_group }}"
                           style="display:flex; gap:10px; align-items:center; border:1px solid #e2e8f0; border-radius:10px; padding:12px; cursor:pointer; background:#fff; transition:all 0.2s;">
                        <input type="checkbox" name="vaccine_ids[]" value="{{ $centerVaccine->vaccine_id }}" {{ in_array($centerVaccine->vaccine_id, old('vaccine_ids', [])) ? 'checked' : '' }} onchange="onVaccineCheckboxChange(this, {{ $centerVaccine->vaccine_id }})">
                        <span style="flex:1;">
                            <strong>{{ $centerVaccine->vaccine->name }}</strong>
                            <span style="display:flex; gap:8px; align-items:center; margin-top:3px; font-size:12px;">
                                <small style="color:var(--text-muted);">{{ $centerVaccine->vaccine->origin }}</small>
                                <span style="width:3px; height:3px; border-radius:50%; background:#94a3b8;"></span>
                                <small style="font-weight:700; color:{{ $centerVaccine->stock_quantity <= 5 ? '#b91c1c' : '#15803d' }};">Tồn: {{ $centerVaccine->stock_quantity }}</small>
                            </span>
                        </span>
                        <div style="display:flex; align-items:center; gap:4px;" onclick="event.stopPropagation();">
                            <span style="font-size:12px; color:var(--text-muted);">SL:</span>
                            <input type="number" id="qty_{{ $centerVaccine->vaccine_id }}" name="quantities[{{ $centerVaccine->vaccine_id }}]" 
                                   value="{{ old('quantities.'.$centerVaccine->vaccine_id, 1) }}" 
                                   min="1" max="{{ $centerVaccine->stock_quantity }}" 
                                   style="width: 55px; padding: 4px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; text-align: center; height:28px;"
                                   {{ in_array($centerVaccine->vaccine_id, old('vaccine_ids', [])) ? '' : 'disabled' }}>
                        </div>
                        <strong style="color:var(--primary-color); white-space:nowrap; margin-left:8px;">{{ number_format($price) }} đ</strong>
                    </label>
                @endforeach
            </div>
            @if($vaccines->isEmpty())<small style="display:block; margin-top:6px; color:#b91c1c;">Chi nhánh chưa có vắc xin khả dụng.</small>@endif
        </div>

        <div style="display:flex; justify-content:flex-end; gap:12px; flex-wrap:wrap;">
            <a href="{{ route('admin.registrations.index') }}" class="btn-modern btn-modern-secondary">Hủy</a>
            <button type="submit" class="btn-modern btn-modern-primary" {{ $slots->isEmpty() || $vaccines->isEmpty() ? 'disabled' : '' }}>Tạo phiếu tại quầy</button>
        </div>
    </form>
    @endif
</div>

<script>
    function onVaccineCheckboxChange(checkbox, id) {
        toggleQtyInput(checkbox, id);
        updateItemHighlight(checkbox);
        renderSelectedSummary();
    }

    function toggleQtyInput(checkbox, id) {
        const input = document.getElementById('qty_' + id);
        if (input) {
            input.disabled = !checkbox.checked;
            if (checkbox.checked) {
                input.focus();
            }
        }
    }

    function updateItemHighlight(checkbox) {
        const item = checkbox.closest('.vaccine-search-item');
        if (item) {
            if (checkbox.checked) {
                item.style.borderColor = 'var(--primary-color, #c8102e)';
                item.style.backgroundColor = '#fef2f2';
                item.style.boxShadow = '0 0 0 1px var(--primary-color, #c8102e)';
            } else {
                item.style.borderColor = '#e2e8f0';
                item.style.backgroundColor = '#fff';
                item.style.boxShadow = 'none';
            }
        }
    }

    function renderSelectedSummary() {
        const summaryContainer = document.getElementById('selected_vaccines_summary');
        const listContainer = document.getElementById('selected_vaccines_list');
        const countSpan = document.getElementById('selected_count');
        if (!summaryContainer || !listContainer || !countSpan) return;

        const checkboxes = document.querySelectorAll('input[name="vaccine_ids[]"]:checked');
        countSpan.textContent = checkboxes.length;

        if (checkboxes.length === 0) {
            summaryContainer.style.display = 'none';
            listContainer.innerHTML = '';
            return;
        }

        summaryContainer.style.display = 'block';
        listContainer.innerHTML = '';

        checkboxes.forEach(cb => {
            const item = cb.closest('.vaccine-search-item');
            if (item) {
                const name = item.querySelector('strong').textContent;
                const qtyInput = document.getElementById('qty_' + cb.value);
                const qty = qtyInput ? qtyInput.value : 1;
                
                const pill = document.createElement('div');
                pill.style.cssText = 'display:inline-flex; align-items:center; gap:6px; background:#fff; border:1px solid #cbd5e1; padding:4px 10px; border-radius:20px; font-size:12.5px; font-weight:700; color:#334155;';
                
                pill.innerHTML = `
                    <span>${name} (x${qty})</span>
                    <button type="button" onclick="removeSelectedPill(${cb.value})" style="border:none; background:none; color:#dc2626; cursor:pointer; font-weight:800; display:inline-flex; align-items:center; justify-content:center; padding:0 2px; font-size:14px; line-height:1;">&times;</button>
                `;
                listContainer.appendChild(pill);
            }
        });
    }

    function removeSelectedPill(id) {
        const cb = document.querySelector(`input[name="vaccine_ids[]"][value="${id}"]`);
        if (cb) {
            cb.checked = false;
            onVaccineCheckboxChange(cb, id);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('vaccine_search');
        const typeSelect = document.getElementById('filter_type');
        const categorySelect = document.getElementById('filter_category');
        const originSelect = document.getElementById('filter_origin');
        const ageGroupSelect = document.getElementById('filter_age_group');
        const items = document.querySelectorAll('.vaccine-search-item');

        function filterVaccines() {
            const query = searchInput ? searchInput.value.toLowerCase().trim()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd') : '';
            
            const selectedType = typeSelect ? typeSelect.value : 'all';
            const selectedCategory = categorySelect ? categorySelect.value : 'all';
            const selectedOrigin = originSelect ? originSelect.value : 'all';
            const selectedAgeGroup = ageGroupSelect ? ageGroupSelect.value : 'all';

            items.forEach(item => {
                const name = item.getAttribute('data-name')
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd');
                const type = item.getAttribute('data-type');
                const category = item.getAttribute('data-category');
                const origin = item.getAttribute('data-origin');
                const ageGroup = item.getAttribute('data-age-group');

                const matchesSearch = !query || name.includes(query);
                const matchesType = selectedType === 'all' || type === selectedType;
                const matchesCategory = selectedCategory === 'all' || category === selectedCategory;
                const matchesOrigin = selectedOrigin === 'all' || origin === selectedOrigin;
                const matchesAgeGroup = selectedAgeGroup === 'all' || ageGroup === selectedAgeGroup;

                if (matchesSearch && matchesType && matchesCategory && matchesOrigin && matchesAgeGroup) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        [searchInput, typeSelect, categorySelect, originSelect, ageGroupSelect].forEach(el => {
            if (el) {
                el.addEventListener('input', filterVaccines);
                el.addEventListener('change', filterVaccines);
            }
        });

        // Khởi tạo highlight & số lượng & summary khi load trang
        document.querySelectorAll('input[name="vaccine_ids[]"]').forEach(cb => {
            updateItemHighlight(cb);
            const qtyInput = document.getElementById('qty_' + cb.value);
            if (qtyInput) {
                qtyInput.addEventListener('input', renderSelectedSummary);
            }
        });
        renderSelectedSummary();
    });
</script>
@endsection
