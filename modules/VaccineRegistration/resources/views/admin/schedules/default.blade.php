@extends('vaccine::layouts.admin')

@section('title', 'Cấu hình Khung giờ Mặc định')
@section('page_title', 'Khung Giờ Mặc Định Theo Tuần')

@section('admin_content')
<div style="display:grid; gap:24px;">
    {{-- Header Navigation and Center Selector --}}
    <div class="card-modern" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
        <div>
            <h2 style="margin:0; font-family:var(--font-display); font-size:18px; font-weight:700;">Khung Giờ Mặc Định Theo Thứ</h2>
            <p style="margin:4px 0 0 0; color:var(--text-muted); font-size:13.5px;">Thiết lập khung giờ chuẩn chạy tự động hàng tuần cho chi nhánh.</p>
        </div>
        <div>
            <a href="{{ route('admin.schedules.index', ['center_id' => $selectedCenterId]) }}" class="btn-modern btn-modern-secondary" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                Quay lại quản lý lịch
            </a>
        </div>
    </div>

    @if($isSuperAdmin ?? false)
        <div class="card-modern">
            <form method="GET" action="{{ route('admin.default-slots.index') }}" style="display:flex; gap:12px; align-items:end; flex-wrap:wrap;">
                <div style="flex:1 1 280px;">
                    <label class="form-label-modern" for="center_id">Chi nhánh</label>
                    <select class="form-control-modern" id="center_id" name="center_id" onchange="this.form.submit()">
                        @foreach($centers as $center)
                            <option value="{{ $center->id }}" {{ (string) $selectedCenterId === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    @endif

    {{-- Main Tabbed Slot Settings Card --}}
    <div class="card-modern">
        {{-- Tabs --}}
        <div style="display:flex; gap:8px; border-bottom:2px solid var(--border-color); margin-bottom:24px; padding-bottom:0; flex-wrap:wrap;">
            @for($d = 1; $d <= 7; $d++)
                @php
                    $dayName = [
                        1 => 'Thứ Hai',
                        2 => 'Thứ Ba',
                        3 => 'Thứ Tư',
                        4 => 'Thứ Năm',
                        5 => 'Thứ Sáu',
                        6 => 'Thứ Bảy',
                        7 => 'Chủ Nhật'
                    ][$d];
                    $isActive = (int)request('tab', 1) === $d;
                @endphp
                <button type="button" class="tab-btn {{ $isActive ? 'active' : '' }}" data-day="{{ $d }}" style="padding:10px 16px; border:none; background:none; font-weight:700; font-size:14px; cursor:pointer; color:{{ $isActive ? 'var(--primary-color)' : 'var(--text-muted)' }}; border-bottom:3px solid {{ $isActive ? 'var(--primary-color)' : 'transparent' }}; margin-bottom:-2px; transition:all 0.2s;">
                    {{ $dayName }}
                </button>
            @endfor
        </div>

        {{-- Error or Success Alert inside form context --}}
        @if ($errors->any())
            <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 8px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; font-size: 13.5px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Tab Contents --}}
        @for($d = 1; $d <= 7; $d++)
            @php
                $dayName = [
                    1 => 'Thứ Hai',
                    2 => 'Thứ Ba',
                    3 => 'Thứ Tư',
                    4 => 'Thứ Năm',
                    5 => 'Thứ Sáu',
                    6 => 'Thứ Bảy',
                    7 => 'Chủ Nhật'
                ][$d];
                $slotsForDay = $defaultSlots->get($d, collect());
                $isActive = (int)request('tab', 1) === $d;
            @endphp
            <div class="tab-content" id="day-content-{{ $d }}" style="display: {{ $isActive ? 'block' : 'none' }};">
                <form method="POST" action="{{ route('admin.default-slots.update') }}">
                    @csrf
                    <input type="hidden" name="center_id" value="{{ $selectedCenterId }}">
                    <input type="hidden" name="day_of_week" value="{{ $d }}">
                    
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <h3 style="margin:0; font-family:var(--font-display); font-size:16px; font-weight:700;">Khung giờ hoạt động - {{ $dayName }}</h3>
                        <button type="button" class="btn-modern btn-modern-secondary add-slot-btn" data-day="{{ $d }}">Thêm khung giờ</button>
                    </div>

                    <div id="slot-rows-{{ $d }}" style="display:grid; gap:12px; margin-bottom:20px;">
                        @forelse($slotsForDay as $index => $slot)
                            <div class="slot-row" style="display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:12px; align-items:end; border-bottom:1px solid #f1f5f9; padding-bottom:12px;">
                                <div>
                                    <label class="form-label-modern">Bắt đầu</label>
                                    <input class="form-control-modern" type="time" name="slots[{{ $index }}][start_at]" value="{{ $slot->start_at }}" required>
                                </div>
                                <div>
                                    <label class="form-label-modern">Kết thúc</label>
                                    <input class="form-control-modern" type="time" name="slots[{{ $index }}][end_at]" value="{{ $slot->end_at }}" required>
                                </div>
                                <div>
                                    <label class="form-label-modern">Công suất</label>
                                    <input class="form-control-modern" type="number" name="slots[{{ $index }}][capacity]" min="1" value="{{ $slot->capacity }}" required>
                                </div>
                                <div>
                                    <button type="button" class="btn-modern btn-modern-secondary remove-slot-btn" style="padding:10px 14px; background:#fee2e2; color:#ef4444; border:none; cursor:pointer;">Xóa</button>
                                </div>
                            </div>
                        @empty
                            <p class="no-slots-msg" style="color:var(--text-muted); font-style:italic; margin:0 0 10px 0;">Không có khung giờ hoạt động nào. Trung tâm sẽ hiển thị nghỉ vào ngày này.</p>
                        @endforelse
                    </div>

                    <div style="display:flex; justify-content:flex-end; border-top:1px solid var(--border-color); padding-top:16px;">
                        <button type="submit" class="btn-modern btn-modern-primary">Lưu cấu hình {{ $dayName }}</button>
                    </div>
                </form>
            </div>
        @endfor
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tabs = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const day = tab.getAttribute('data-day');
                
                tabs.forEach(t => {
                    t.classList.remove('active');
                    t.style.color = 'var(--text-muted)';
                    t.style.borderBottomColor = 'transparent';
                });
                tab.classList.add('active');
                tab.style.color = 'var(--primary-color)';
                tab.style.borderBottomColor = 'var(--primary-color)';
                
                contents.forEach(c => c.style.display = 'none');
                document.getElementById(`day-content-${day}`).style.display = 'block';
                
                // Update URL parameter without reload
                const url = new URL(window.location);
                url.searchParams.set('tab', day);
                window.history.pushState({}, '', url);
            });
        });

        // Add Slot Row
        document.querySelectorAll('.add-slot-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const day = btn.getAttribute('data-day');
                const container = document.getElementById(`slot-rows-${day}`);
                if (!container) return;
                
                const noSlotsMsg = container.querySelector('.no-slots-msg');
                if (noSlotsMsg) noSlotsMsg.remove();
                
                const index = container.querySelectorAll('.slot-row').length + Date.now();
                const row = document.createElement('div');
                row.className = 'slot-row';
                row.style.cssText = 'display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:12px; align-items:end; border-bottom:1px solid #f1f5f9; padding-bottom:12px;';
                row.innerHTML = `
                    <div>
                        <label class="form-label-modern">Bắt đầu</label>
                        <input class="form-control-modern" type="time" name="slots[${index}][start_at]" value="08:00" required>
                    </div>
                    <div>
                        <label class="form-label-modern">Kết thúc</label>
                        <input class="form-control-modern" type="time" name="slots[${index}][end_at]" value="09:00" required>
                    </div>
                    <div>
                        <label class="form-label-modern">Công suất</label>
                        <input class="form-control-modern" type="number" name="slots[${index}][capacity]" min="1" value="10" required>
                    </div>
                    <div>
                        <button type="button" class="btn-modern btn-modern-secondary remove-slot-btn" style="padding:10px 14px; background:#fee2e2; color:#ef4444; border:none; cursor:pointer;">Xóa</button>
                    </div>
                `;
                container.appendChild(row);
            });
        });

        // Remove Slot Row
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-slot-btn')) {
                const row = e.target.closest('.slot-row');
                const container = row.parentElement;
                row.remove();
                
                if (container.querySelectorAll('.slot-row').length === 0) {
                    container.innerHTML = `<p class="no-slots-msg" style="color:var(--text-muted); font-style:italic; margin:0 0 10px 0;">Không có khung giờ hoạt động nào. Trung tâm sẽ hiển thị nghỉ vào ngày này.</p>`;
                }
            }
        });
    });
</script>
@endsection
