@extends('vaccine::layouts.admin')

@section('title', 'Cấu Hình Tích Điểm - Medicare')
@section('page_title', 'Cấu Hình Tích Điểm ' . ($centerId ? 'Chi Nhánh' : 'Hệ Thống'))

@section('admin_content')
<div style="display: grid; gap: 24px;">
    {{-- Banner Cảnh báo lệch cấu hình --}}
    @if($centerId && ($hasSyncWarning ?? false))
        <div class="card-modern" style="background: #fff8eb; border: 1px solid #ffe2b3; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; padding: 20px; border-radius: var(--radius-md);">
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <i data-lucide="alert-triangle" style="color: #b06000; flex-shrink: 0; width: 24px; height: 24px;"></i>
                <div>
                    <strong style="color: #b06000; display: block; font-family: var(--font-display); font-size: 15px; margin-bottom: 4px;">Cấu hình hệ thống đã cập nhật mới</strong>
                    <span style="font-size: 0.875rem; color: #64748b;">
                        Admin toàn hệ thống vừa cập nhật cấu hình chung vào lúc <strong>{{ $systemUpdatedAt ? $systemUpdatedAt->format('d/m/Y H:i') : '' }}</strong>. Chi nhánh của bạn đang chạy hệ số cũ hoặc cấu hình riêng biệt khác.
                    </span>
                </div>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" id="open_sync_modal_btn" class="btn-action-sm btn-action-success" style="font-weight: 700;">
                    <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i> Đồng bộ ngay
                </button>
                <form action="{{ route('admin.settings.loyalty.reject-sync') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-action-sm" style="font-weight: 600;">
                        Từ chối đồng bộ
                    </button>
                </form>
            </div>
        </div>
    @endif

    <div class="card-modern">
        <h2 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--text-primary); margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="coins" style="color: var(--primary-color);"></i> 
            Thiết lập cơ chế tích điểm - {{ $centerId ? 'Cấu hình chi nhánh' : 'Cấu hình chung hệ thống' }}
        </h2>


        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 24px; padding: 16px; border-radius: 8px; background-color: #fde8e8; color: #9b1c1c; border: 1px solid #fbd5d5;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.settings.loyalty.update') }}" method="POST">
            @csrf
            
            {{-- Lựa chọn cấu hình Chi nhánh --}}
            @if($centerId)
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 16px; border-radius: var(--radius-sm); margin-bottom: 24px;">
                    <label class="form-label-modern" style="margin-bottom: 12px; font-weight: 700; color: var(--accent-color);">Lựa chọn cấu hình áp dụng cho chi nhánh này *</label>
                    <div style="display: flex; gap: 24px; align-items: center;">
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; cursor: pointer; font-weight: 500;">
                            <input type="radio" name="use_system_settings" value="1" {{ $settings['use_system_settings'] ? 'checked' : '' }} style="accent-color: var(--primary-color); width: 18px; height: 18px;">
                            Sử dụng cấu hình chung của toàn hệ thống
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; cursor: pointer; font-weight: 500;">
                            <input type="radio" name="use_system_settings" value="0" {{ !$settings['use_system_settings'] ? 'checked' : '' }} style="accent-color: var(--primary-color); width: 18px; height: 18px;">
                            Sử dụng cấu hình riêng của chi nhánh
                        </label>
                    </div>
                </div>
            @endif

            <div id="loyalty_settings_fields" style="{{ ($centerId && $settings['use_system_settings']) ? 'display: none;' : '' }}">
                <h3 style="font-family: var(--font-display); font-size: 15px; font-weight: 700; color: var(--accent-color); margin-bottom: 16px; display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="info" style="width: 18px; height: 18px;"></i> Cấu hình tích lũy & quy đổi điểm
                </h3>

                <div style="margin-bottom: 24px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                    <!-- Trạng thái hoạt động -->
                    <div class="form-group-modern">
                        <label for="enabled" class="form-label-modern">Trạng thái tích điểm *</label>
                        <select name="enabled" id="enabled" class="form-control-modern">
                            <option value="1" {{ old('enabled', $settings['enabled']) ? 'selected' : '' }}>Kích hoạt</option>
                            <option value="0" {{ !old('enabled', $settings['enabled']) ? 'selected' : '' }}>Tạm dừng</option>
                        </select>
                    </div>

                    <!-- Tỷ lệ tích điểm (%) -->
                    <div class="form-group-modern">
                        <label for="earn_percent_input" class="form-label-modern">Tỷ lệ tích điểm (%) *</label>
                        <input type="number" step="0.001" id="earn_percent_input" value="0.1" min="0.001" max="100" required class="form-control-modern">
                        <small style="color: var(--text-muted); display: block; margin-top: 4px;">Ví dụ: Nhập <strong>0.1</strong> để tích được 0.1% đơn hàng.</small>
                    </div>

                    <!-- Phần trăm giảm tối đa -->
                    <div class="form-group-modern">
                        <label for="max_redeem_percent" class="form-label-modern">Phần trăm thanh toán tối đa bằng điểm (%) *</label>
                        <input type="number" name="max_redeem_percent" id="max_redeem_percent" value="{{ old('max_redeem_percent', $settings['max_redeem_percent']) }}" min="1" max="100" required class="form-control-modern">
                        <small style="color: var(--text-muted); display: block; margin-top: 4px;">Ví dụ: Nhập <strong>50</strong> để được thanh toán tối đa 50% đơn.</small>
                    </div>
                </div>

                {{-- Khối chứa các input ẩn nhận giá trị mặc định của hệ thống --}}
                <div style="display: none;">
                    <!-- VND tương đương 1 điểm (Sẽ do JS tự động cập nhật dựa trên earn_percent_input) -->
                    <input type="number" name="vnd_per_earned_point" id="vnd_per_earned_point" value="{{ old('vnd_per_earned_point', $settings['vnd_per_earned_point']) }}">

                    <!-- Hạn dùng điểm -->
                    <input type="number" name="point_expiry_months" id="point_expiry_months" value="{{ old('point_expiry_months', $settings['point_expiry_months']) }}">

                    <!-- Giá trị đơn hàng tối thiểu để được tích điểm -->
                    <input type="number" name="min_order_value_to_earn" id="min_order_value_to_earn" value="{{ old('min_order_value_to_earn', $settings['min_order_value_to_earn']) }}">

                    <!-- Kiểu quy đổi điểm -->
                    <select name="redeem_value_type" id="redeem_value_type">
                        <option value="vnd" selected>Số tiền cố định (VND)</option>
                    </select>

                    <!-- Giá trị quy đổi VND (Đặt mặc định là 1 VND = 1 Điểm) -->
                    <input type="number" name="redeem_vnd_per_point" id="redeem_vnd_per_point" value="{{ old('redeem_vnd_per_point', $settings['redeem_vnd_per_point']) }}">

                    <!-- Đơn hàng tối thiểu sử dụng điểm -->
                    <input type="number" name="min_order_value_to_redeem" id="min_order_value_to_redeem" value="{{ old('min_order_value_to_redeem', $settings['min_order_value_to_redeem']) }}">

                    <!-- Số tiền giảm tối đa -->
                    <input type="number" name="max_redeem_amount" id="max_redeem_amount" value="{{ old('max_redeem_amount', $settings['max_redeem_amount']) }}">

                    <!-- Hệ số sinh nhật -->
                    <input type="number" step="0.01" name="birthday_multiplier" id="birthday_multiplier" value="1.0">

                    {{-- Ẩn các mốc hạng thành viên và chiến dịch để tối giản hóa cơ chế điểm theo yêu cầu --}}
                    <!-- 3. Mốc hạng thành viên -->
                    <div id="tiers_container">
                        @foreach($settings['tiers'] ?? [] as $index => $tier)
                            <input type="hidden" name="tiers[{{ $index }}][name]" value="{{ $tier['name'] }}">
                            <input type="hidden" name="tiers[{{ $index }}][min_points]" value="{{ $tier['min_points'] }}">
                            <input type="hidden" name="tiers[{{ $index }}][multiplier]" value="{{ $tier['multiplier'] }}">
                        @endforeach
                    </div>

                    <!-- 4. Chiến dịch ưu đãi & Tăng điểm theo dịp -->
                    <div id="campaigns_container">
                        @foreach($settings['campaigns'] ?? [] as $index => $campaign)
                            <input type="hidden" name="campaigns[{{ $index }}][name]" value="{{ $campaign['name'] }}">
                            <input type="hidden" name="campaigns[{{ $index }}][start_date]" value="{{ $campaign['start_date'] }}">
                            <input type="hidden" name="campaigns[{{ $index }}][end_date]" value="{{ $campaign['end_date'] }}">
                            <input type="hidden" name="campaigns[{{ $index }}][multiplier]" value="{{ $campaign['multiplier'] }}">
                        @endforeach
                    </div>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 24px; display: flex; justify-content: flex-end; margin-top: 30px;">
                <button type="submit" class="btn-modern btn-modern-primary">
                    <i data-lucide="save"></i> Lưu thông số cấu hình
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal đồng bộ từng phần --}}
@if($centerId && ($hasSyncWarning ?? false))
    <div id="sync_modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 1100; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
        <div class="card-modern" style="max-width: 500px; width: 100%; border: 1px solid var(--border-color); padding: 24px; box-shadow: var(--shadow-lg);">
            <h3 style="margin-top: 0; font-family: var(--font-display); font-size: 16px; font-weight: 700; color: var(--text-primary); border-bottom: 1px solid var(--border-color); padding-bottom: 12px; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="refresh-cw" style="color: var(--primary-color);"></i> Tùy chọn đồng bộ cấu hình hệ thống
            </h3>
            
            <form action="{{ route('admin.settings.loyalty.sync') }}" method="POST">
                @csrf
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 18px; line-height: 1.5;">
                    Vui lòng chọn các mục cấu hình bạn muốn chép đè từ hệ thống sang chi nhánh:
                </p>

                <div style="display: grid; gap: 12px; margin-bottom: 24px;">
                    <label style="display: flex; align-items: center; gap: 10px; font-size: 0.875rem; cursor: pointer; padding: 6px 0;">
                        <input type="checkbox" name="sync_fields[]" value="basic" checked style="accent-color: var(--primary-color); width: 16px; height: 16px;">
                        Đồng bộ Cấu hình tích lũy cơ bản (Số tiền/điểm, đơn tối thiểu, hạn dùng)
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; font-size: 0.875rem; cursor: pointer; padding: 6px 0;">
                        <input type="checkbox" name="sync_fields[]" value="redeem" checked style="accent-color: var(--primary-color); width: 16px; height: 16px;">
                        Đồng bộ Cấu hình quy đổi điểm & thanh toán
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; font-size: 0.875rem; cursor: pointer; padding: 6px 0;">
                        <input type="checkbox" name="sync_fields[]" value="tiers" checked style="accent-color: var(--primary-color); width: 16px; height: 16px;">
                        Đồng bộ Mốc hạng thành viên (Rank)
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; font-size: 0.875rem; cursor: pointer; padding: 6px 0;">
                        <input type="checkbox" name="sync_fields[]" value="campaigns" checked style="accent-color: var(--primary-color); width: 16px; height: 16px;">
                        Đồng bộ Chiến dịch & Sự kiện ưu đãi
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; font-size: 0.875rem; cursor: pointer; padding: 6px 0;">
                        <input type="checkbox" name="sync_fields[]" value="birthday" checked style="accent-color: var(--primary-color); width: 16px; height: 16px;">
                        Đồng bộ Ưu đãi dịp sinh nhật bệnh nhân
                    </label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid var(--border-color); padding-top: 16px;">
                    <button type="button" id="close_sync_modal_btn" class="btn-modern btn-modern-secondary" style="padding: 8px 16px;">
                        Hủy bỏ
                    </button>
                    <button type="submit" class="btn-modern btn-modern-primary" style="padding: 8px 16px;">
                        Xác nhận áp dụng
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection

@section('styles')
<style>
    .remove-row-btn {
        margin: 0 auto;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- JS Ẩn/Hiện form cấu hình chi nhánh ---
        const radioUseSystem = document.querySelectorAll('input[name="use_system_settings"]');
        const settingsFields = document.getElementById('loyalty_settings_fields');

        if (radioUseSystem && settingsFields) {
            radioUseSystem.forEach(r => {
                r.addEventListener('change', function() {
                    if (this.value === '1') {
                        settingsFields.style.display = 'none';
                    } else {
                        settingsFields.style.display = 'block';
                    }
                });
            });
        }

        // --- JS Modal Đồng bộ hóa ---
        const openModalBtn = document.getElementById('open_sync_modal_btn');
        const closeModalBtn = document.getElementById('close_sync_modal_btn');
        const syncModal = document.getElementById('sync_modal');

        if (openModalBtn && syncModal) {
            openModalBtn.addEventListener('click', function() {
                syncModal.style.display = 'flex';
            });
        }

        if (closeModalBtn && syncModal) {
            closeModalBtn.addEventListener('click', function() {
                syncModal.style.display = 'none';
            });
        }

        // --- JS Tự động tính toán tỷ lệ tích điểm và vnd_per_earned_point ---
        const earnPercentInput = document.getElementById('earn_percent_input');
        const vndPerPointInput = document.getElementById('vnd_per_earned_point');
        const expiryMonthsInput = document.getElementById('point_expiry_months');
        const minEarnInput = document.getElementById('min_order_value_to_earn');
        const redeemVndInput = document.getElementById('redeem_vnd_per_point');
        const minRedeemInput = document.getElementById('min_order_value_to_redeem');

        if (earnPercentInput && vndPerPointInput) {
            // Khi load trang: tính earn_percent dựa trên vnd_per_earned_point
            const initVnd = parseInt(vndPerPointInput.value) || 1000;
            const initPercent = Math.round((100 / initVnd) * 1000) / 1000;
            earnPercentInput.value = initPercent;

            // Đặt các giá trị mặc định hệ thống tối giản
            if (expiryMonthsInput) expiryMonthsInput.value = 0;
            if (minEarnInput) minEarnInput.value = 0;
            if (redeemVndInput) redeemVndInput.value = 1;
            if (minRedeemInput) minRedeemInput.value = 0;

            // Lắng nghe sự kiện thay đổi tỷ lệ % tích điểm
            earnPercentInput.addEventListener('input', function() {
                const pct = parseFloat(earnPercentInput.value) || 0.1;
                if (pct > 0) {
                    const vnd = Math.round(100 / pct);
                    vndPerPointInput.value = vnd;
                }
            });
        }
    });
</script>
@endsection
