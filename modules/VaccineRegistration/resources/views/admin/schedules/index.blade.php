@extends('vaccine::layouts.admin')

@section('title', 'Quản lý khung giờ')
@section('page_title', 'Lịch & Khung Giờ')

@section('admin_content')
<div style="display:grid; gap:24px;">
    <div class="card-modern">
        <h2 style="margin-top:0;">Mở lịch tiêm</h2>
        <form method="POST" action="{{ route('admin.schedules.store') }}" id="scheduleForm">
            @csrf
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:14px;">
                @if($isSuperAdmin ?? false)
                    <div>
                        <label class="form-label-modern" for="center_id">Chi nhánh</label>
                        <select class="form-control-modern" id="center_id" name="center_id" required>
                            @foreach($centers as $center)
                                <option value="{{ $center->id }}" {{ (string) old('center_id', $selectedCenterId) === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="center_id" value="{{ $adminUser->center_id }}">
                @endif
                <div>
                    <label class="form-label-modern" for="date">Ngày</label>
                    <input class="form-control-modern" id="date" type="date" name="date" min="{{ today()->toDateString() }}" value="{{ old('date', today()->addDay()->toDateString()) }}" required>
                </div>
                <div>
                    <label class="form-label-modern" for="note">Ghi chú</label>
                    <input class="form-control-modern" id="note" type="text" name="note" value="{{ old('note') }}" maxlength="1000">
                </div>
            </div>
            <div id="slotRows" style="display:grid; gap:10px; margin-top:18px;">
                <div class="slot-row" style="display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:10px; align-items:end;">
                    <div><label class="form-label-modern">Bắt đầu</label><input class="form-control-modern" type="time" name="slots[0][start_at]" value="08:00" required></div>
                    <div><label class="form-label-modern">Kết thúc</label><input class="form-control-modern" type="time" name="slots[0][end_at]" value="09:00" required></div>
                    <div><label class="form-label-modern">Công suất</label><input class="form-control-modern" type="number" name="slots[0][capacity]" min="1" value="10" required></div>
                    <button type="button" class="btn-modern btn-modern-secondary remove-slot" style="visibility:hidden;">Bỏ</button>
                </div>
            </div>
            <div style="display:flex; gap:12px; margin-top:18px; flex-wrap:wrap;">
                <button type="button" id="addSlot" class="btn-modern btn-modern-secondary">Thêm khung giờ</button>
                <button type="submit" class="btn-modern btn-modern-primary">Lưu lịch</button>
            </div>
        </form>
    </div>

    <div class="card-modern">
        <form method="GET" action="{{ route('admin.schedules.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:end; margin-bottom:20px;">
            @if($isSuperAdmin ?? false)
                <div><label class="form-label-modern">Chi nhánh</label><select class="form-control-modern" name="center_id"><option value="">Tất cả</option>@foreach($centers as $center)<option value="{{ $center->id }}" {{ (string) request('center_id') === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>@endforeach</select></div>
            @endif
            <div><label class="form-label-modern">Ngày</label><input class="form-control-modern" type="date" name="date" value="{{ request('date') }}"></div>
            <button class="btn-modern btn-modern-secondary" type="submit">Lọc</button>
        </form>
        @forelse($schedules as $schedule)
            <div style="padding:16px 0; border-top:1px solid var(--border-color);">
                <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap;"><strong>{{ $schedule->center->name }} - {{ $schedule->date->format('d/m/Y') }}</strong><span>{{ $schedule->is_active ? 'Đang mở' : 'Đã đóng' }}</span></div>
                @if($schedule->note)<small style="color:var(--text-muted);">{{ $schedule->note }}</small>@endif
                <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:10px;">@foreach($schedule->slots as $slot)<span style="padding:6px 10px; border-radius:6px; background:#f8fafc;">{{ $slot->start_at }}-{{ $slot->end_at }}: {{ $slot->reserved_count }}/{{ $slot->capacity }}</span>@endforeach</div>
            </div>
        @empty
            <p style="margin:0; color:var(--text-muted);">Chưa có lịch phù hợp.</p>
        @endforelse
        <div style="display:flex; justify-content:center; margin-top:20px;">{{ $schedules->links() }}</div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (() => {
        const rows = document.getElementById('slotRows');
        const add = document.getElementById('addSlot');
        if (!rows || !add) return;
        let index = 1;
        add.addEventListener('click', () => {
            const row = document.createElement('div');
            row.className = 'slot-row';
            row.style.cssText = 'display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:10px; align-items:end;';
            row.innerHTML = `<div><label class="form-label-modern">Bắt đầu</label><input class="form-control-modern" type="time" name="slots[${index}][start_at]" value="09:00" required></div><div><label class="form-label-modern">Kết thúc</label><input class="form-control-modern" type="time" name="slots[${index}][end_at]" value="10:00" required></div><div><label class="form-label-modern">Công suất</label><input class="form-control-modern" type="number" name="slots[${index}][capacity]" min="1" value="10" required></div><button type="button" class="btn-modern btn-modern-secondary remove-slot">Bỏ</button>`;
            rows.appendChild(row);
            index += 1;
        });
        rows.addEventListener('click', (event) => {
            if (event.target.closest('.remove-slot')) event.target.closest('.slot-row').remove();
        });
    })();
</script>
@endsection
