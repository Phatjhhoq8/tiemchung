@if($vaccines->isEmpty())
    <div class="empty-vaccines" style="text-align: center; padding: 60px 20px; background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color);">
        <i data-lucide="alert-circle" class="empty-icon" style="width: 48px; height: 48px; color: var(--text-muted); margin-bottom: 16px;"></i>
        <p style="font-size: 16px; color: var(--text-muted); margin-bottom: 16px;">Không tìm thấy vắc xin nào phù hợp với bộ lọc hiện tại.</p>
        <button onclick="resetVaccineFilters()" class="btn-primary" style="background: var(--primary-color); color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer;">Xem tất cả vắc xin</button>
    </div>
@else
    <div class="vaccines-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); gap: 20px;">
        @foreach($vaccines as $vaccine)
            <div class="vaccine-card {{ isset($cart[$vaccine->id]) ? 'selected' : '' }}" data-id="{{ $vaccine->id }}" style="display: flex; flex-direction: column; justify-content: space-between; overflow: hidden; padding: 0; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; transition: all 0.2s ease-in-out;">
                <div onclick="openVaccineDetailModal({{ $vaccine->id }})" style="cursor: pointer;">
                    <div class="vaccine-card-img" style="height: 160px; width: 100%; background: var(--bg-main); display: flex; align-items: center; justify-content: center; overflow: hidden; border-bottom: 1px solid var(--border-color); position: relative;">
                        <img src="{{ asset('images/vaccines/' . ($vaccine->image ?: 'hexaxim.jpg')) }}" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/hexaxim.jpg') }}';" alt="{{ $vaccine->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        <span style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.65); color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                            <i data-lucide="eye" style="width: 12px; height: 12px;"></i> Chi tiết
                        </span>
                    </div>
                    <div class="vaccine-card-body" style="padding: 16px 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span class="vaccine-type-tag" style="background-color: {{ $vaccine->type === 'package' ? '#e0f2fe' : '#fee2e2' }}; color: {{ $vaccine->type === 'package' ? '#0369a1' : '#b91c1c' }}; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700;">
                                {{ $vaccine->type === 'package' ? 'Gói vắc xin' : 'Vắc xin lẻ' }}
                            </span>
                            <span style="font-size: 12px; color: var(--text-muted);">{{ $vaccine->origin }}</span>
                        </div>
                        <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; line-height: 1.4; height: 44px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">{{ $vaccine->name }}</h3>
                        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 0; height: 36px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;"><strong>Phòng bệnh:</strong> {{ $vaccine->disease_prevention }}</p>
                    </div>
                </div>
                <div style="padding: 14px 20px 20px 20px; border-top: 1px dashed var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <span style="font-size: 11px; color: var(--text-muted); display: block;">Giá tiêm:</span>
                        <span style="font-size: 17px; font-weight: 800; color: var(--primary-color);">{{ number_format($vaccine->price, 0, ',', '.') }} đ</span>
                    </div>
                    <button class="btn-select-vaccine {{ isset($cart[$vaccine->id]) ? 'btn-selected' : '' }}" onclick="toggleCart({{ $vaccine->id }})" style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; border: none; display: flex; align-items: center; gap: 6px; transition: all 0.2s;">
                        <i data-lucide="{{ isset($cart[$vaccine->id]) ? 'check' : 'plus' }}"></i>
                        <span>{{ isset($cart[$vaccine->id]) ? 'Đã chọn' : 'Chọn tiêm' }}</span>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif
