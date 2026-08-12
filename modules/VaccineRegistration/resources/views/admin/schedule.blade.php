@extends('vaccine::layouts.admin')

@section('title', 'Lịch Hẹn Tuần - Medicare')
@section('page_title', 'Lịch Hẹn Tiêm Chủng Theo Tuần')

@section('admin_content')
<style>
    .day-appointments-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .day-appointments-scroll::-webkit-scrollbar-track {
        background: #f8fafc;
        border-radius: 4px;
    }
    .day-appointments-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .day-appointments-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
<div class="card-modern">
    <!-- Top Week Navigation Bar (Separated Display & Controls) -->
    <div class="week-nav-bar" style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; background: #ffffff; padding: 14px 20px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); margin-bottom: 24px;">
        <!-- Left: Display Info & Branch Filter -->
        <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
            <div class="week-range-title" style="display: flex; align-items: center; gap: 8px; font-size: 15px; font-weight: 700; color: #004b8f; background: #eff6ff; padding: 6px 14px; border-radius: 8px; border: 1px solid #bfdbfe;">
                <i data-lucide="calendar" style="width: 17px; height: 17px; color: #004b8f;"></i>
                <span>Tuần từ {{ $startOfWeek->format('d/m/Y') }} đến {{ $startOfWeek->copy()->endOfWeek()->format('d/m/Y') }}</span>
            </div>

            @if($isSuperAdmin ?? false)
                <form id="scheduleCenterForm" method="GET" action="{{ route('admin.schedule') }}" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                    <input type="hidden" name="week" value="{{ $startOfWeek->toDateString() }}">
                    <label class="form-label-modern" for="schedule_center_id" style="margin: 0; font-size: 0.8125rem;">Chi nhánh:</label>
                    <select class="form-control-modern" id="schedule_center_id" name="center_id" style="width: auto; height: 38px; padding: 0 12px; font-size: 0.875rem;" onchange="this.form.submit()">
                        <option value="" {{ $selectedCenterId === null ? 'selected' : '' }}>Tất cả chi nhánh</option>
                        @foreach($centers as $center)
                            <option value="{{ $center->id }}" {{ (string) $selectedCenterId === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>

        <!-- Right: Segmented Week Navigation Buttons & Date Picker -->
        <div class="week-nav-controls" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-left: auto;">
            <div style="display: inline-flex; border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; background: #ffffff;">
                <a href="{{ route('admin.schedule', ['week' => $startOfWeek->copy()->subWeek()->toDateString(), 'center_id' => $selectedCenterId]) }}" class="week-nav-btn" style="border: none; border-right: 1px solid #cbd5e1; border-radius: 0; height: 38px; padding: 0 14px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; font-size: 13.5px; font-weight: 600; color: #1e293b; background: #f8fafc;">
                    <i data-lucide="chevron-left" style="width:15px; height:15px;"></i>
                    <span>Tuần trước</span>
                </a>
                <a href="{{ route('admin.schedule', ['center_id' => $selectedCenterId]) }}" class="week-nav-btn-current" style="border: none; border-right: 1px solid #cbd5e1; border-radius: 0; height: 38px; padding: 0 16px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; font-size: 13.5px; font-weight: 700; color: #004b8f; background: #eff6ff;">
                    <span>Tuần hiện tại</span>
                </a>
                <a href="{{ route('admin.schedule', ['week' => $startOfWeek->copy()->addWeek()->toDateString(), 'center_id' => $selectedCenterId]) }}" class="week-nav-btn" style="border: none; border-radius: 0; height: 38px; padding: 0 14px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; font-size: 13.5px; font-weight: 600; color: #1e293b; background: #f8fafc;">
                    <span>Tuần sau</span>
                    <i data-lucide="chevron-right" style="width:15px; height:15px;"></i>
                </a>
            </div>

            <input type="date" id="scheduleWeekDatePicker" class="form-control-modern" value="{{ $startOfWeek->toDateString() }}" style="width: auto; height: 38px; padding: 0 10px; font-size: 0.85rem;" title="Chọn ngày trong tuần" onchange="window.location.href='{{ route('admin.schedule') }}?week=' + this.value + '{{ $selectedCenterId ? '&center_id='.$selectedCenterId : '' }}'">
        </div>
    </div>

    <!-- Layout Lịch 7 Ngày trong Tuần -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 24px;">
        @foreach($daysOfWeek as $dateStr => $day)
            @php
                $hasItems = $day['items']->isNotEmpty();
                $isToday = $dateStr === date('Y-m-d');
                $scheduleStatusLabels = [
                    'pending' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'completed' => 'Đã hoàn tất',
                    'no_show' => 'Không đến', 'cancelled' => 'Đã hủy', 'paid' => 'Đã thanh toán',
                    'Đã thanh toán' => 'Đã thanh toán', 'Đã tiêm' => 'Đã tiêm', 'Đã hủy' => 'Đã hủy',
                    'Đã tư vấn' => 'Đã tư vấn', 'Chờ tư vấn' => 'Chờ tư vấn',
                ];
            @endphp
            <div style="background: #ffffff; border-radius: 12px; border: 1px solid {{ $isToday ? 'var(--primary-color)' : 'var(--border-color)' }}; overflow: hidden; box-shadow: var(--shadow-sm); transition: all 0.3s ease; {{ $isToday ? 'box-shadow: 0 4px 16px rgba(200,16,46,0.1);' : '' }}">
                <!-- Day Header -->
                <div style="background: {{ $isToday ? 'linear-gradient(135deg, #c8102e, #a00d24)' : ($hasItems ? 'linear-gradient(135deg, #f8fafc, #f1f5f9)' : '#f8fafc') }}; color: {{ $isToday ? '#ffffff' : 'var(--text-primary)' }}; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-family: var(--font-display); font-weight: 800; font-size: 16px;">{{ $day['day_name'] }}</span>
                        <span style="font-size: 14px; opacity: 0.85; font-weight: 500;">({{ $day['date'] }})</span>
                        @if($isToday)
                            <span style="background: #eaaa00; color: #340711; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; text-transform: uppercase;">Hôm nay</span>
                        @endif
                    </div>
                    <span style="font-family: var(--font-display); font-weight: 700; font-size: 13px; background: {{ $isToday ? 'rgba(255,255,255,0.2)' : ($hasItems ? 'rgba(200,16,46,0.1)' : '#e2e8f0') }}; color: {{ $isToday ? '#ffffff' : ($hasItems ? 'var(--primary-color)' : '#64748b') }}; padding: 4px 10px; border-radius: 6px;">
                        {{ $day['items']->count() }} lịch hẹn
                    </span>
                </div>

                <!-- Day Body (Danh sách lịch hẹn) -->
                <div class="day-appointments-scroll" style="padding: 20px; max-height: 480px; overflow-y: auto;">
                    @if(!$hasItems)
                        <div style="text-align: center; padding: 20px; color: var(--text-muted); font-size: 13.5px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                            <i data-lucide="calendar-check" style="width: 16px; height: 16px; color: var(--text-light);"></i>
                            Không có lịch hẹn tiêm chủng trong ngày này.
                        </div>
                    @else
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                            @foreach($day['items'] as $item)
                                @php($scheduleStatusLabel = $scheduleStatusLabels[$item->status] ?? 'Không xác định')
                                <div class="schedule-appointment-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; display: flex; flex-direction: column; justify-content: space-between; transition: all 0.2s; position: relative; box-shadow: 0 2px 4px rgba(0,0,0,0.01);" onmouseover="this.style.borderColor='var(--primary-color)'; this.style.boxShadow='0 4px 12px rgba(200,16,46,0.05)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.01)';">
                                    
                                    <!-- Info block -->
                                    <div style="margin-bottom: 12px;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                            <a href="{{ route('admin.registrations.show', $item->id) }}" style="font-weight: 700; color: var(--primary-color); text-decoration: none; font-size: 13.5px; font-family: var(--font-display);">
                                                {{ $item->registration_code }} <i data-lucide="external-link" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle;"></i>
                                            </a>
                                            <span class="badge-modern 
                                                @if(in_array($scheduleStatusLabel, ['Đã thanh toán', 'Đã tư vấn', 'Đã hoàn tất'], true)) badge-modern-success
                                                @elseif(in_array($scheduleStatusLabel, ['Đã tiêm', 'Đã xác nhận'], true)) badge-modern-info
                                                @elseif($scheduleStatusLabel === 'Đã hủy') badge-modern-danger
                                                @elseif(in_array($scheduleStatusLabel, ['Chờ tư vấn', 'Chờ xác nhận'], true)) badge-modern-warning
                                                @else badge-modern-warning @endif" style="font-size: 12px; padding: 4px 8px; border-radius: 4px;">
                                                 {{ $scheduleStatusLabel }}
                                            </span>
                                        </div>
                                        
                                        <div style="font-weight: 600; font-size: 14.5px; color: var(--text-primary); margin-bottom: 4px;">
                                            {{ $item->patient_name }}
                                        </div>
                                        <div style="font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 4px; margin-bottom: 8px;">
                                            <i data-lucide="phone" style="width: 12px; height: 12px;"></i> {{ $item->patient_phone }}
                                        </div>
                                        
                                        <!-- Vaccines list -->
                                        <div style="background: #f8fafc; border-radius: 6px; padding: 8px 12px; margin-top: 8px; border: 1px solid #f1f5f9;">
                                            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px; display: flex; align-items: center; gap: 4px;">
                                                <i data-lucide="syringe" style="width: 10px; height: 10px;"></i> Vắc xin chọn tiêm:
                                            </div>
                                            @if($item->vaccines->isEmpty())
                                                <span style="font-size: 12px; color: #ef4444; font-style: italic;">Không có sản phẩm</span>
                                            @else
                                                <ul class="vaccine-list" data-registration-id="{{ $item->id }}" style="margin: 0; padding-left: 14px; font-size: 12.5px; color: #334155; line-height: 1.4;">
                                                    @foreach($item->vaccines as $index => $vac)
                                                        <li class="vaccine-item {{ $index >= 3 ? 'hidden-vaccine' : '' }}" style="{{ $index >= 3 ? 'display: none;' : '' }}">{{ $vac->name }}</li>
                                                    @endforeach
                                                </ul>
                                                @if($item->vaccines->count() > 3)
                                                    <button type="button" class="btn-toggle-vaccines" data-registration-id="{{ $item->id }}" style="background: none; border: none; color: var(--primary-color, #c8102e); font-size: 12px; font-weight: 700; padding: 4px 0 0 0; cursor: pointer; display: flex; align-items: center; gap: 2px; margin-top: 4px; font-family: var(--font-display);">
                                                        <span>Xem thêm {{ $item->vaccines->count() - 3 }} vắc xin</span>
                                                        <i data-lucide="chevron-down" class="toggle-icon" style="width: 14px; height: 14px; transition: transform 0.2s;"></i>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Footer block -->
                                    <div style="border-top: 1px solid #f1f5f9; padding-top: 10px; display: flex; justify-content: space-between; align-items: center; font-size: 12.5px; color: var(--text-muted);">
                                        <span style="display: flex; align-items: center; gap: 4px;">
                                            <i data-lucide="map-pin" style="width: 12px; height: 12px; color: var(--accent-color);"></i>
                                            {{ Str::limit(str_replace('Hệ thống Tiêm Chủng ', '', $item->center_name), 15) }}
                                        </span>
                                        <strong style="color: var(--primary-color); font-size: 13.5px;">
                                            {{ number_format($item->total_price, 0, ',', '.') }} đ
                                        </strong>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleButtons = document.querySelectorAll('.btn-toggle-vaccines');
        
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const regId = this.getAttribute('data-registration-id');
                const list = document.querySelector(`.vaccine-list[data-registration-id="${regId}"]`);
                if (!list) return;
                
                const hiddenItems = list.querySelectorAll('.hidden-vaccine');
                const textSpan = this.querySelector('span');
                const icon = this.querySelector('.toggle-icon');
                
                const isCurrentlyHidden = hiddenItems[0].style.display === 'none';
                
                hiddenItems.forEach(item => {
                    item.style.display = isCurrentlyHidden ? 'list-item' : 'none';
                });
                
                if (isCurrentlyHidden) {
                    textSpan.textContent = 'Thu gọn';
                    if (icon) icon.style.transform = 'rotate(180deg)';
                } else {
                    const count = hiddenItems.length;
                    textSpan.textContent = `Xem thêm ${count} vắc xin`;
                    if (icon) icon.style.transform = 'rotate(0deg)';
                }
            });
        });
    });
</script>
@endsection
