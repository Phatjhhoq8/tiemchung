@extends('vaccine::layouts.admin')

@section('title', 'Quản lý lịch & khung giờ tiêm')
@section('page_title', 'Lịch & Khung Giờ Tiêm Tuần')

@section('styles')
<style>
    .week-nav-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        background: #ffffff;
        padding: 16px 20px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        margin-bottom: 20px;
    }
    .week-nav-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .week-range-badge {
        font-family: var(--font-display);
        font-weight: 700;
        font-size: 1rem;
        color: var(--accent-color);
        background: #e8f0fe;
        padding: 8px 16px;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(0, 75, 143, 0.2);
    }
    .weekly-grid-wrapper {
        display: grid;
        grid-template-columns: repeat(7, minmax(185px, 1fr));
        gap: 14px;
        overflow-x: auto;
        padding-bottom: 12px;
    }
    .day-column-card {
        background: #ffffff;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        border-top: 4px solid var(--accent-color);
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        min-height: 480px;
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    .day-column-card:hover {
        box-shadow: var(--shadow-md);
    }
    .day-column-header {
        padding: 14px;
        border-bottom: 1px solid var(--border-color);
        background: #fafafa;
        border-top-left-radius: var(--radius-md);
        border-top-right-radius: var(--radius-md);
    }
    .day-name {
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.2;
    }
    .day-date {
        font-size: 0.8125rem;
        color: var(--text-muted);
        font-weight: 500;
        margin-top: 2px;
    }
    .day-status-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
    }
    .day-status-btn {
        border: none;
        background: none;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .day-status-btn.active {
        background: #e6f4ea;
        color: #137333;
        border: 1px solid #a8dab5;
    }
    .day-status-btn.inactive {
        background: #fce8e6;
        color: var(--primary-color);
        border: 1px solid #fca5a5;
    }
    .day-capacity-metric {
        font-size: 0.775rem;
        font-weight: 600;
        color: var(--text-muted);
    }
    .day-actions-toolbar {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed var(--border-color);
    }
    .btn-day-action {
        padding: 5px 8px;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: var(--text-primary);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        transition: all 0.2s ease;
    }
    .btn-day-action:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }
    .btn-day-action.btn-copy {
        color: var(--accent-color);
        border-color: rgba(0, 75, 143, 0.3);
    }
    .btn-day-action.btn-copy:hover {
        background: #e8f0fe;
    }
    .btn-day-action.btn-delete-day {
        color: var(--primary-color);
        border-color: #fca5a5;
        background: #fff5f5;
        grid-column: span 2;
    }
    .btn-day-action.btn-delete-day:hover {
        background: var(--primary-color);
        color: #ffffff;
    }
    .day-slots-body {
        padding: 12px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .slot-item-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-left: 3px solid var(--accent-color);
        border-radius: 8px;
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .slot-item-card:hover {
        border-color: var(--accent-color);
        box-shadow: 0 4px 10px rgba(0, 75, 143, 0.08);
    }
    .slot-item-card.inactive {
        opacity: 0.6;
        border-left-color: #94a3b8;
    }
    .slot-time-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .slot-time {
        font-family: var(--font-display);
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-primary);
    }
    .slot-capacity {
        font-size: 0.775rem;
        font-weight: 600;
        color: var(--text-muted);
    }
    .slot-capacity strong {
        color: var(--primary-color);
    }
    .slot-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2px;
    }
    .btn-edit-slot {
        background: none;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 3px 8px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--accent-color);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-edit-slot:hover {
        background: #e8f0fe;
        border-color: var(--accent-color);
    }
    .empty-day-hint {
        text-align: center;
        color: var(--text-light);
        font-size: 0.8125rem;
        padding: 24px 10px;
        background: #f8fafc;
        border: 1px dashed var(--border-color);
        border-radius: 8px;
        margin: "auto 0";
    }
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background-color: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .modal-card {
        background: #ffffff;
        width: 100%;
        max-width: 500px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        background: #fafafa;
    }
    .modal-title {
        font-family: var(--font-display);
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }
    .modal-close-btn {
        background: none;
        border: none;
        font-size: 24px;
        color: #94a3b8;
        cursor: pointer;
        line-height: 1;
    }
    .modal-body {
        padding: 20px;
    }
</style>
@endsection

@section('admin_content')
<div style="display: grid; gap: 20px;">

    <!-- Alert Container -->
    <div id="alertContainer">
        @if(session('success'))
            <div class="admin-section-hint" style="background:#e6f4ea; color:#137333; border-color:#a8dab5;">
                <i data-lucide="check-circle" style="width:18px; height:18px; flex-shrink:0;"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if($errors->any())
            <div class="admin-section-hint" style="background:#fce8e6; color:#c8102e; border-color:#fca5a5;">
                <i data-lucide="alert-triangle" style="width:18px; height:18px; flex-shrink:0;"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Top Week Navigation Bar -->
    <div class="week-nav-bar">
        <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
            <a href="{{ route('admin.default-slots.index', ['center_id' => $selectedCenterId]) }}" class="btn-modern btn-modern-secondary" style="padding: 8px 14px; font-size: 0.8125rem;">
                <i data-lucide="settings"></i> Cấu hình khung giờ mặc định
            </a>

            @if($isSuperAdmin ?? false)
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label class="form-label-modern" style="margin: 0; font-size: 0.8125rem;">Chi nhánh:</label>
                    <select id="centerSelect" class="form-control-modern" style="width: auto; padding: 6px 12px; font-size: 0.875rem;">
                        @foreach($centers as $center)
                            <option value="{{ $center->id }}" {{ (string)$selectedCenterId === (string)$center->id ? 'selected' : '' }}>
                                {{ $center->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="week-nav-controls">
            <a href="{{ route('admin.schedules.index', ['date' => $prevWeekDate, 'center_id' => $selectedCenterId]) }}" class="btn-modern btn-modern-secondary" style="padding: 8px 14px; font-size: 0.8125rem;">
                &laquo; Tuần trước
            </a>

            <div class="week-range-badge">
                <i data-lucide="calendar" style="width:16px; height:16px;"></i>
                <span>{{ $headerRange }}</span>
            </div>

            <a href="{{ route('admin.schedules.index', ['date' => $currentWeekDate, 'center_id' => $selectedCenterId]) }}" class="btn-modern btn-modern-secondary" style="padding: 8px 14px; font-size: 0.8125rem; font-weight:700;">
                Tuần hiện tại
            </a>

            <a href="{{ route('admin.schedules.index', ['date' => $nextWeekDate, 'center_id' => $selectedCenterId]) }}" class="btn-modern btn-modern-secondary" style="padding: 8px 14px; font-size: 0.8125rem;">
                Tuần sau &raquo;
            </a>

            <input type="date" id="weekDatePicker" class="form-control-modern" value="{{ $weekStart->toDateString() }}" style="width: auto; padding: 6px 10px; font-size: 0.85rem;" title="Chọn ngày trong tuần">
        </div>
    </div>

    <!-- 7 Parallel Columns Weekly Calendar Grid -->
    <div class="weekly-grid-wrapper">
        @foreach($weekGrid as $day)
            <div class="day-column-card" id="day-col-{{ $day['date'] }}">
                <div class="day-column-header">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <div class="day-name">{{ $day['day_name'] }}</div>
                            <div class="day-date">{{ $day['formatted_date'] }}</div>
                        </div>
                        <button type="button" 
                                class="day-status-btn {{ $day['is_active'] ? 'active' : 'inactive' }}"
                                onclick="toggleDayStatus('{{ $day['date'] }}', {{ $day['is_active'] ? 0 : 1 }})"
                                title="Nhấn để {{ $day['is_active'] ? 'đóng ngày' : 'mở ngày' }}">
                            {{ $day['is_active'] ? 'Mở cửa' : 'Đóng cửa' }}
                        </button>
                    </div>

                    <div class="day-status-row">
                        <div class="day-capacity-metric">
                            Đặt tiêm: <strong>{{ $day['total_reserved'] }}</strong>/{{ $day['total_capacity'] }}
                        </div>
                    </div>

                    <div class="day-actions-toolbar">
                        <button type="button" class="btn-day-action" onclick="openAddSlotModal('{{ $day['date'] }}', '{{ $day['formatted_date'] }}', {{ $day['schedule_id'] ?? 'null' }})">
                            <i data-lucide="plus" style="width:13px; height:13px;"></i> Thêm giờ
                        </button>
                        <button type="button" class="btn-day-action btn-copy" onclick="openCopyModal('{{ $day['date'] }}', '{{ $day['formatted_date'] }}', {{ count($day['slots']) }})">
                            <i data-lucide="copy" style="width:13px; height:13px;"></i> Sao chép
                        </button>
                        <button type="button" class="btn-day-action btn-delete-day" onclick="confirmDeleteDay('{{ $day['date'] }}', '{{ $day['formatted_date'] }}', {{ $day['total_reserved'] }})">
                            <i data-lucide="trash-2" style="width:13px; height:13px;"></i> Xóa lịch ngày
                        </button>
                    </div>
                </div>

                <div class="day-slots-body">
                    @forelse($day['slots'] as $slot)
                        <div class="slot-item-card {{ !$slot->is_active ? 'inactive' : '' }}">
                            <div class="slot-time-row">
                                <span class="slot-time">{{ $slot->start_at }} - {{ $slot->end_at }}</span>
                                <span class="slot-capacity">
                                    <strong>{{ $slot->reserved_count }}</strong>/{{ $slot->capacity }}
                                </span>
                            </div>
                            <div class="slot-actions">
                                <span style="font-size: 0.725rem; font-weight: 600; color: {{ $slot->is_active ? '#10b981' : '#94a3b8' }};">
                                    {{ $slot->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                                </span>
                                <button type="button" class="btn-edit-slot" 
                                        onclick="openEditSlotModal({{ $slot->id }}, '{{ $slot->start_at }}', '{{ $slot->end_at }}', {{ $slot->capacity }}, {{ $slot->reserved_count }}, {{ $slot->is_active ? 1 : 0 }})">
                                    <i data-lucide="edit-3" style="width:12px; height:12px;"></i> Sửa
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="empty-day-hint">Chưa có khung giờ</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Modal 1: Thêm Khung Giờ Mới -->
<div id="addSlotModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Thêm Khung Giờ Tiêm</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('addSlotModal')">&times;</button>
        </div>
        <form id="addSlotForm" onsubmit="submitAddSlot(event)" style="padding: 20px; display: grid; gap: 16px;">
            <input type="hidden" id="add_schedule_id" name="schedule_id">
            <input type="hidden" id="add_date" name="date">

            <div style="background: #e8f0fe; color: var(--accent-color); padding: 10px 14px; border-radius: 8px; font-weight: 600; font-size: 0.875rem;">
                Ngày mở khung giờ: <span id="add_date_display"></span>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label class="form-label-modern">Bắt đầu</label>
                    <input class="form-control-modern" type="time" id="add_start_at" name="start_at" value="08:00" required>
                </div>
                <div>
                    <label class="form-label-modern">Kết thúc</label>
                    <input class="form-control-modern" type="time" id="add_end_at" name="end_at" value="09:00" required>
                </div>
            </div>

            <div>
                <label class="form-label-modern">Công suất (số chỗ)</label>
                <input class="form-control-modern" type="number" id="add_capacity" name="capacity" min="1" value="10" required>
            </div>

            <div style="display: flex; gap: 8px; align-items: center;">
                <input type="checkbox" id="add_is_active" name="is_active" value="1" checked>
                <label for="add_is_active" style="font-size: 13.5px; font-weight: 600; cursor: pointer;">Khung giờ hoạt động</label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid var(--border-color); padding-top: 16px; margin-top: 10px;">
                <button type="button" class="btn-modern btn-modern-secondary" onclick="closeModal('addSlotModal')">Hủy</button>
                <button type="submit" class="btn-modern btn-modern-primary">Thêm khung giờ</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Chỉnh sửa / Xóa Khung Giờ -->
<div id="editSlotModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Chỉnh sửa Khung giờ</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('editSlotModal')">&times;</button>
        </div>
        <form id="editSlotForm" onsubmit="submitEditSlot(event)" style="padding: 20px; display: grid; gap: 16px;">
            <input type="hidden" id="edit_slot_id">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label class="form-label-modern">Bắt đầu</label>
                    <input class="form-control-modern" type="time" id="edit_start_at" name="start_at" required>
                </div>
                <div>
                    <label class="form-label-modern">Kết thúc</label>
                    <input class="form-control-modern" type="time" id="edit_end_at" name="end_at" required>
                </div>
            </div>

            <div>
                <label class="form-label-modern">Công suất</label>
                <input class="form-control-modern" type="number" id="edit_capacity" name="capacity" min="1" required>
                <small style="color: var(--text-muted); font-size: 11.5px; display: block; margin-top: 4px;">
                    Số chỗ đã đặt hiện tại: <strong id="edit_reserved_count">0</strong>
                </small>
            </div>

            <div style="display: flex; gap: 8px; align-items: center;">
                <input type="checkbox" id="edit_is_active" name="is_active" value="1">
                <label for="edit_is_active" style="font-size: 13.5px; font-weight: 600; cursor: pointer;">Khung giờ hoạt động</label>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 16px; margin-top: 10px;">
                <button type="button" class="btn-modern btn-modern-danger" onclick="submitDeleteSlot()">
                    Xóa khung giờ
                </button>
                <div style="display: flex; gap: 8px;">
                    <button type="button" class="btn-modern btn-modern-secondary" onclick="closeModal('editSlotModal')">Hủy</button>
                    <button type="submit" class="btn-modern btn-modern-primary">Lưu thay đổi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Sao chép lịch tiêm sang các ngày khác -->
<div id="copyScheduleModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Sao Chép Lịch Tiêm</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('copyScheduleModal')">&times;</button>
        </div>
        <form id="copyScheduleForm" onsubmit="submitCopySchedule(event)" style="padding: 20px; display: grid; gap: 16px;">
            <input type="hidden" id="copy_source_date" name="source_date">

            <div style="background: #e8f0fe; color: var(--accent-color); padding: 12px; border-radius: 8px; font-weight: 700; font-size: 0.9rem;">
                Nguồn: <span id="copy_source_date_display"></span>
            </div>

            <div style="background: #fff7f7; border: 1px solid #fecaca; padding: 12px; border-radius: 8px; color: #7f1d1d; font-size: 0.8125rem; line-height: 1.4;">
                <strong>Lưu ý bảo vệ dữ liệu:</strong> Thao tác sao chép chỉ cho phép chép đè sang các ngày <u>chưa có bệnh nhân đặt tiêm</u>. Nếu ngày đích đã có lượt đặt, hệ thống sẽ chặn sao chép để tránh mất dữ liệu.
            </div>

            <div>
                <label class="form-label-modern">Chọn các ngày đích trong tuần:</label>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <button type="button" onclick="selectAllTargets(true)" style="background:none; border:none; color:var(--accent-color); font-size:0.775rem; font-weight:700; cursor:pointer;">[Chọn tất cả]</button>
                    <button type="button" onclick="selectAllTargets(false)" style="background:none; border:none; color:var(--text-muted); font-size:0.775rem; font-weight:700; cursor:pointer;">[Bỏ chọn]</button>
                </div>
                <div id="copyTargetChecklist" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; max-height: 180px; overflow-y: auto; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;">
                    @foreach($weekGrid as $day)
                        <label class="target-day-checkbox-item" data-date="{{ $day['date'] }}" style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; cursor: pointer;">
                            <input type="checkbox" name="target_dates[]" value="{{ $day['date'] }}" class="target-date-cb">
                            <span>{{ $day['day_name'] }} ({{ $day['formatted_date'] }})</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid var(--border-color); padding-top: 16px; margin-top: 10px;">
                <button type="button" class="btn-modern btn-modern-secondary" onclick="closeModal('copyScheduleModal')">Hủy</button>
                <button type="submit" class="btn-modern btn-modern-primary">Thực hiện sao chép</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const currentCenterId = {{ $selectedCenterId ?? 'null' }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function showAlert(message, type = 'success') {
        const container = document.getElementById('alertContainer');
        const isSuccess = type === 'success';
        const bg = isSuccess ? '#e6f4ea' : '#fce8e6';
        const color = isSuccess ? '#137333' : '#c8102e';
        const border = isSuccess ? '#a8dab5' : '#fca5a5';
        const icon = isSuccess ? 'check-circle' : 'alert-triangle';

        container.innerHTML = `
            <div class="admin-section-hint" style="background:${bg}; color:${color}; border-color:${border}; margin-bottom: 16px;">
                <i data-lucide="${icon}" style="width:18px; height:18px; flex-shrink:0;"></i>
                <div>${message}</div>
            </div>
        `;
        if (window.lucide) lucide.createIcons();
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // --- Center Switch & Date Picker Events ---
    const centerSelect = document.getElementById('centerSelect');
    if (centerSelect) {
        centerSelect.addEventListener('change', (e) => {
            const url = new URL(window.location.href);
            url.searchParams.set('center_id', e.target.value);
            window.location.href = url.toString();
        });
    }

    const weekDatePicker = document.getElementById('weekDatePicker');
    if (weekDatePicker) {
        weekDatePicker.addEventListener('change', (e) => {
            const url = new URL(window.location.href);
            url.searchParams.set('date', e.target.value);
            window.location.href = url.toString();
        });
    }

    // --- Day Toggle Status ---
    async function toggleDayStatus(date, isActive) {
        try {
            const response = await fetch("{{ route('admin.schedules.toggle-day') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    center_id: currentCenterId,
                    date: date,
                    is_active: isActive
                })
            });
            const data = await response.json();
            if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                showAlert(data.message || 'Cập nhật thất bại.', 'error');
            }
        } catch (e) {
            showAlert('Đã có lỗi xảy ra khi cập nhật trạng thái ngày.', 'error');
        }
    }

    // --- Delete Entire Day Schedule ---
    async function confirmDeleteDay(date, formattedDate, reservedCount) {
        if (reservedCount > 0) {
            showAlert(`Không thể xóa lịch ngày ${formattedDate} vì đã có ${reservedCount} lượt đặt tiêm!`, 'error');
            return;
        }

        if (!await window.AppDialog.confirm(`Bạn có chắc chắn muốn xóa toàn bộ lịch và khung giờ ngày ${formattedDate}?`)) {
            return;
        }

        try {
            const response = await fetch("{{ route('admin.schedules.destroy-day') }}", {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    center_id: currentCenterId,
                    date: date
                })
            });

            const data = await response.json();
            if (response.ok && data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                const msg = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Xóa thất bại.');
                showAlert(msg, 'error');
            }
        } catch (e) {
            showAlert('Đã có lỗi xảy ra khi xóa lịch ngày.', 'error');
        }
    }

    // --- Open Add Slot Modal ---
    function openAddSlotModal(date, formattedDate, scheduleId) {
        document.getElementById('add_schedule_id').value = scheduleId || '';
        document.getElementById('add_date').value = date;
        document.getElementById('add_date_display').textContent = formattedDate;
        document.getElementById('addSlotModal').style.display = 'flex';
    }

    async function submitAddSlot(e) {
        e.preventDefault();
        const scheduleId = document.getElementById('add_schedule_id').value;
        const date = document.getElementById('add_date').value;
        const startAt = document.getElementById('add_start_at').value;
        const endAt = document.getElementById('add_end_at').value;
        const capacity = document.getElementById('add_capacity').value;
        const isActive = document.getElementById('add_is_active').checked ? 1 : 0;

        try {
            let url = scheduleId ? `/admin/schedules/${scheduleId}/slots` : "{{ route('admin.slots.store') }}";
            let bodyData = scheduleId ? { start_at: startAt, end_at: endAt, capacity: capacity, is_active: isActive }
                                      : { schedule_id: scheduleId, start_at: startAt, end_at: endAt, capacity: capacity, is_active: isActive };

            // If schedule does not exist yet for this day, create schedule first via store
            if (!scheduleId) {
                const createSchedResp = await fetch("{{ route('admin.schedules.store') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({
                        center_id: currentCenterId,
                        date: date,
                        is_active: 1,
                        slots: [{ start_at: startAt, end_at: endAt, capacity: parseInt(capacity), is_active: isActive }]
                    })
                });
                const schedData = await createSchedResp.json();
                if (createSchedResp.ok && schedData.success) {
                    closeModal('addSlotModal');
                    showAlert('Thêm khung giờ thành công.', 'success');
                    setTimeout(() => window.location.reload(), 600);
                    return;
                } else {
                    const msg = schedData.errors ? Object.values(schedData.errors).flat().join('<br>') : (schedData.message || 'Thêm thất bại.');
                    showAlert(msg, 'error');
                    return;
                }
            }

            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(bodyData)
            });
            const data = await response.json();
            if (response.ok && data.success) {
                closeModal('addSlotModal');
                showAlert('Thêm khung giờ thành công.', 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                const msg = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Thêm thất bại.');
                showAlert(msg, 'error');
            }
        } catch (err) {
            showAlert('Đã có lỗi xảy ra khi thêm khung giờ.', 'error');
        }
    }

    // --- Open Edit Slot Modal ---
    function openEditSlotModal(slotId, startAt, endAt, capacity, reservedCount, isActive) {
        document.getElementById('edit_slot_id').value = slotId;
        document.getElementById('edit_start_at').value = startAt;
        document.getElementById('edit_end_at').value = endAt;
        document.getElementById('edit_capacity').value = capacity;
        document.getElementById('edit_capacity').min = reservedCount;
        document.getElementById('edit_reserved_count').textContent = reservedCount;
        document.getElementById('edit_is_active').checked = isActive === 1;
        document.getElementById('editSlotModal').style.display = 'flex';
    }

    async function submitEditSlot(e) {
        e.preventDefault();
        const slotId = document.getElementById('edit_slot_id').value;
        const startAt = document.getElementById('edit_start_at').value;
        const endAt = document.getElementById('edit_end_at').value;
        const capacity = document.getElementById('edit_capacity').value;
        const isActive = document.getElementById('edit_is_active').checked ? 1 : 0;

        try {
            const response = await fetch(`/admin/slots/${slotId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ start_at: startAt, end_at: endAt, capacity: capacity, is_active: isActive })
            });
            const data = await response.json();
            if (response.ok && data.success) {
                closeModal('editSlotModal');
                showAlert(data.message, 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                const msg = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Cập nhật thất bại.');
                showAlert(msg, 'error');
            }
        } catch (err) {
            showAlert('Đã có lỗi xảy ra khi cập nhật khung giờ.', 'error');
        }
    }

    async function submitDeleteSlot() {
        const slotId = document.getElementById('edit_slot_id').value;
        const reservedCount = parseInt(document.getElementById('edit_reserved_count').textContent, 10);

        if (reservedCount > 0) {
            if (!await window.AppDialog.confirm(`Khung giờ này đang có ${reservedCount} lượt đặt hẹn. Bạn có chắc chắn muốn xóa?`)) return;
        } else {
            if (!await window.AppDialog.confirm('Bạn có chắc chắn muốn xóa khung giờ này?')) return;
        }

        try {
            const response = await fetch(`/admin/slots/${slotId}`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await response.json();
            if (response.ok && data.success) {
                closeModal('editSlotModal');
                showAlert(data.message, 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                showAlert(data.message || 'Xóa thất bại.', 'error');
            }
        } catch (err) {
            showAlert('Đã có lỗi xảy ra khi xóa khung giờ.', 'error');
        }
    }

    // --- Open Copy Schedule Modal ---
    function openCopyModal(sourceDate, formattedDate, slotCount) {
        if (slotCount === 0) {
            showAlert(`Ngày ${formattedDate} chưa có khung giờ nào để sao chép!`, 'error');
            return;
        }
        document.getElementById('copy_source_date').value = sourceDate;
        document.getElementById('copy_source_date_display').textContent = formattedDate;

        // Reset and hide source date from target list
        document.querySelectorAll('.target-day-checkbox-item').forEach(item => {
            const dateVal = item.getAttribute('data-date');
            const cb = item.querySelector('input[type="checkbox"]');
            if (dateVal === sourceDate) {
                item.style.display = 'none';
                cb.checked = false;
            } else {
                item.style.display = 'flex';
                cb.checked = false;
            }
        });

        document.getElementById('copyScheduleModal').style.display = 'flex';
    }

    function selectAllTargets(select) {
        document.querySelectorAll('.target-day-checkbox-item').forEach(item => {
            if (item.style.display !== 'none') {
                item.querySelector('input[type="checkbox"]').checked = select;
            }
        });
    }

    async function submitCopySchedule(e) {
        e.preventDefault();
        const sourceDate = document.getElementById('copy_source_date').value;
        const targetCBs = Array.from(document.querySelectorAll('input[name="target_dates[]"]:checked')).map(cb => cb.value);

        if (targetCBs.length === 0) {
            showAlert('Vui lòng chọn ít nhất 1 ngày đích để sao chép!', 'error');
            return;
        }

        try {
            const response = await fetch("{{ route('admin.schedules.copy') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({
                    center_id: currentCenterId,
                    source_date: sourceDate,
                    target_dates: targetCBs
                })
            });
            const data = await response.json();
            if (response.ok && data.success) {
                closeModal('copyScheduleModal');
                showAlert(data.message, 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                const msg = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Sao chép thất bại.');
                showAlert(msg, 'error');
            }
        } catch (err) {
            showAlert('Đã có lỗi xảy ra khi sao chép lịch.', 'error');
        }
    }
</script>
@endsection
