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
                    <i data-lucide="info" style="width: 18px; height: 18px;"></i> 1. Cấu hình tích lũy cơ bản
                </h3>
                
                <div class="form-grid-2" style="margin-bottom: 24px;">
                    <!-- Trạng thái hoạt động -->
                    <div class="form-group-modern">
                        <label for="enabled" class="form-label-modern">Trạng thái tích điểm *</label>
                        <select name="enabled" id="enabled" class="form-control-modern">
                            <option value="1" {{ old('enabled', $settings['enabled']) ? 'selected' : '' }}>Kích hoạt</option>
                            <option value="0" {{ !old('enabled', $settings['enabled']) ? 'selected' : '' }}>Tạm dừng</option>
                        </select>
                    </div>

                    <!-- Hạn dùng điểm -->
                    <div class="form-group-modern">
                        <label for="point_expiry_months" class="form-label-modern">Hạn dùng điểm (Tháng) *</label>
                        <input type="number" name="point_expiry_months" id="point_expiry_months" value="{{ old('point_expiry_months', $settings['point_expiry_months']) }}" min="0" required class="form-control-modern">
                        <small style="color: var(--text-muted); display: block; margin-top: 4px;">Đặt bằng <strong>0</strong> để cấu hình điểm có hạn dùng <strong>Vô hạn</strong>.</small>
                    </div>

                    <!-- VND tương đương 1 điểm -->
                    <div class="form-group-modern">
                        <label for="vnd_per_earned_point" class="form-label-modern">Số tiền để tích được 1 điểm (VND) *</label>
                        <input type="number" name="vnd_per_earned_point" id="vnd_per_earned_point" value="{{ old('vnd_per_earned_point', $settings['vnd_per_earned_point']) }}" min="1" required class="form-control-modern">
                    </div>

                    <!-- Giá trị đơn hàng tối thiểu để được tích điểm -->
                    <div class="form-group-modern">
                        <label for="min_order_value_to_earn" class="form-label-modern">Giá trị đơn tối thiểu để tích điểm (VND) *</label>
                        <input type="number" name="min_order_value_to_earn" id="min_order_value_to_earn" value="{{ old('min_order_value_to_earn', $settings['min_order_value_to_earn']) }}" min="0" required class="form-control-modern">
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 30px 0;">

                <h3 style="font-family: var(--font-display); font-size: 15px; font-weight: 700; color: var(--accent-color); margin-bottom: 16px; display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="banknote" style="width: 18px; height: 18px;"></i> 2. Cấu hình quy đổi điểm & thanh toán
                </h3>

                <div class="form-grid-2" style="margin-bottom: 24px;">
                    <!-- Kiểu quy đổi điểm -->
                    <div class="form-group-modern">
                        <label for="redeem_value_type" class="form-label-modern">Phương thức quy đổi điểm *</label>
                        <select name="redeem_value_type" id="redeem_value_type" class="form-control-modern">
                            <option value="vnd" {{ old('redeem_value_type', $settings['redeem_value_type']) === 'vnd' ? 'selected' : '' }}>Số tiền cố định (VND)</option>
                            <option value="percent" {{ old('redeem_value_type', $settings['redeem_value_type']) === 'percent' ? 'selected' : '' }}>Phần trăm đơn hàng (%)</option>
                        </select>
                    </div>

                    <!-- Giá trị quy đổi VND -->
                    <div class="form-group-modern" id="redeem_vnd_container">
                        <label for="redeem_vnd_per_point" class="form-label-modern">Số tiền quy đổi trên mỗi 1 điểm (VND) *</label>
                        <input type="number" name="redeem_vnd_per_point" id="redeem_vnd_per_point" value="{{ old('redeem_vnd_per_point', $settings['redeem_vnd_per_point'] ?? 100) }}" min="0" required class="form-control-modern">
                    </div>

                    <!-- Giá trị quy đổi % (Basis Points) -->
                    <div class="form-group-modern" id="redeem_percent_container" style="display:none;">
                        <label for="redeem_percent_display" class="form-label-modern">Phần trăm quy đổi trên mỗi 1 điểm (%) *</label>
                        <input type="number" step="0.001" id="redeem_percent_display" value="{{ old('redeem_percent_display', isset($settings['redeem_percent_bps_per_point']) ? ($settings['redeem_percent_bps_per_point'] / 100) : 0.1) }}" min="0" class="form-control-modern">
                        <input type="hidden" name="redeem_percent_bps_per_point" id="redeem_percent_bps_per_point" value="{{ old('redeem_percent_bps_per_point', $settings['redeem_percent_bps_per_point'] ?? 10) }}">
                        <small style="color: var(--text-muted); display: block; margin-top: 4px;">Ví dụ: Nhập <strong>0.1%</strong> (tương đương 10 điểm cơ bản BPS).</small>
                    </div>

                    <div style="grid-column: span 2; margin-top: -12px;">
                        <small id="redeem_value_hint" style="color: var(--primary-color); display: block; font-weight: 600;"></small>
                    </div>

                    <!-- Đơn hàng tối thiểu sử dụng điểm -->
                    <div class="form-group-modern">
                        <label for="min_order_value_to_redeem" class="form-label-modern">Giá trị đơn tối thiểu để dùng điểm (VND) *</label>
                        <input type="number" name="min_order_value_to_redeem" id="min_order_value_to_redeem" value="{{ old('min_order_value_to_redeem', $settings['min_order_value_to_redeem']) }}" min="0" required class="form-control-modern">
                    </div>

                    <!-- Phần trăm giảm tối đa -->
                    <div class="form-group-modern">
                        <label for="max_redeem_percent" class="form-label-modern">Phần trăm giảm tối đa của đơn hàng (%) *</label>
                        <input type="number" name="max_redeem_percent" id="max_redeem_percent" value="{{ old('max_redeem_percent', $settings['max_redeem_percent']) }}" min="1" max="100" required class="form-control-modern">
                    </div>

                    <!-- Số tiền giảm tối đa -->
                    <div class="form-group-modern">
                        <label for="max_redeem_amount" class="form-label-modern">Số tiền giảm giá tối đa (VND - Tùy chọn)</label>
                        <input type="number" name="max_redeem_amount" id="max_redeem_amount" value="{{ old('max_redeem_amount', $settings['max_redeem_amount']) }}" min="0" class="form-control-modern">
                        <small style="color: var(--text-muted); display: block; margin-top: 4px;">Để trống để không giới hạn số tiền giảm tối đa.</small>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 30px 0;">

                <!-- 3. Mốc hạng thành viên -->
                <div style="margin-bottom: 30px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h3 style="font-family: var(--font-display); font-size: 15px; font-weight: 700; color: var(--accent-color); margin: 0; display: flex; align-items: center; gap: 6px;">
                            <i data-lucide="award" style="width: 18px; height: 18px;"></i> 3. Mốc tích lũy & Hạng thành viên (Shopee Style)
                        </h3>
                        <button type="button" id="add_tier_btn" class="btn-action-sm btn-action-success">
                            Thêm hạng mới
                        </button>
                    </div>
                    
                    <div class="table-responsive-modern">
                        <table class="table-modern" id="tiers_table">
                            <thead>
                                <tr>
                                    <th>Tên hạng</th>
                                    <th>Mốc điểm tích lũy tối thiểu</th>
                                    <th>Hệ số tích điểm</th>
                                    <th style="width: 100px; text-align: center;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings['tiers'] as $index => $tier)
                                    <tr class="tier-row" data-index="{{ $index }}">
                                        <td>
                                            <input type="text" name="tiers[{{ $index }}][name]" value="{{ $tier['name'] }}" required class="form-control-modern" placeholder="Ví dụ: Vàng">
                                        </td>
                                        <td>
                                            <input type="number" name="tiers[{{ $index }}][min_points]" value="{{ $tier['min_points'] }}" min="0" required class="form-control-modern">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="tiers[{{ $index }}][multiplier]" value="{{ $tier['multiplier'] }}" min="1" required class="form-control-modern">
                                        </td>
                                        <td style="text-align: center;">
                                            <button type="button" class="btn-action-sm btn-action-danger remove-row-btn">Xóa</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="no-tiers-row">
                                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 24px;">Chưa cấu hình mốc hạng thành viên. Nhấn nút "Thêm hạng mới" để bắt đầu.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 30px 0;">

                <!-- 4. Chiến dịch ưu đãi & Tăng điểm theo dịp -->
                <div style="margin-bottom: 30px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h3 style="font-family: var(--font-display); font-size: 15px; font-weight: 700; color: var(--accent-color); margin: 0; display: flex; align-items: center; gap: 6px;">
                            <i data-lucide="calendar" style="width: 18px; height: 18px;"></i> 4. Sự kiện & Chiến dịch tăng điểm ưu đãi
                        </h3>
                        <button type="button" id="add_campaign_btn" class="btn-action-sm btn-action-success">
                            Thêm sự kiện
                        </button>
                    </div>
                    
                    <div class="table-responsive-modern">
                        <table class="table-modern" id="campaigns_table">
                            <thead>
                                <tr>
                                    <th>Tên sự kiện</th>
                                    <th>Ngày bắt đầu</th>
                                    <th>Ngày kết thúc</th>
                                    <th>Hệ số nhân ưu đãi</th>
                                    <th style="width: 100px; text-align: center;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings['campaigns'] as $index => $campaign)
                                    <tr class="campaign-row" data-index="{{ $index }}">
                                        <td>
                                            <input type="text" name="campaigns[{{ $index }}][name]" value="{{ $campaign['name'] }}" required class="form-control-modern" placeholder="Ví dụ: Tết Trung Thu">
                                        </td>
                                        <td>
                                            <input type="date" name="campaigns[{{ $index }}][start_date]" value="{{ $campaign['start_date'] }}" required class="form-control-modern">
                                        </td>
                                        <td>
                                            <input type="date" name="campaigns[{{ $index }}][end_date]" value="{{ $campaign['end_date'] }}" required class="form-control-modern">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="campaigns[{{ $index }}][multiplier]" value="{{ $campaign['multiplier'] }}" min="1" required class="form-control-modern">
                                        </td>
                                        <td style="text-align: center;">
                                            <button type="button" class="btn-action-sm btn-action-danger remove-row-btn">Xóa</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="no-campaigns-row">
                                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 24px;">Chưa cấu hình chiến dịch ưu đãi. Nhấn nút "Thêm sự kiện" để bắt đầu.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 30px 0;">

                <!-- 5. Tích điểm sinh nhật -->
                <h3 style="font-family: var(--font-display); font-size: 15px; font-weight: 700; color: var(--accent-color); margin-bottom: 16px; display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="cake" style="width: 18px; height: 18px;"></i> 5. Ưu đãi dịp sinh nhật bệnh nhân
                </h3>
                
                <div style="display: grid; grid-template-columns: 1fr; gap: 24px; margin-bottom: 30px;">
                    <div class="form-group-modern" style="max-width: 480px;">
                        <label for="birthday_multiplier" class="form-label-modern">Hệ số nhân dịp sinh nhật bệnh nhân *</label>
                        <input type="number" step="0.01" name="birthday_multiplier" id="birthday_multiplier" value="{{ old('birthday_multiplier', $settings['birthday_multiplier']) }}" min="1" required class="form-control-modern">
                        <small style="color: var(--text-muted); display: block; margin-top: 4px;">Đặt bằng <strong>1.0</strong> để không áp dụng hệ số nhân ưu đãi vào ngày sinh nhật.</small>
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

        // --- Gợi ý phương thức quy đổi ---
        const typeSelect = document.getElementById('redeem_value_type');
        const vndContainer = document.getElementById('redeem_vnd_container');
        const percentContainer = document.getElementById('redeem_percent_container');
        const vndInput = document.getElementById('redeem_vnd_per_point');
        const percentDisplayInput = document.getElementById('redeem_percent_display');
        const percentBpsInput = document.getElementById('redeem_percent_bps_per_point');
        const valHint = document.getElementById('redeem_value_hint');

        function updateRedeemFields() {
            if (!typeSelect || !vndContainer || !percentContainer || !valHint) return;
            const type = typeSelect.value;
            if (type === 'percent') {
                vndContainer.style.display = 'none';
                percentContainer.style.display = 'block';
                
                const pct = parseFloat(percentDisplayInput.value) || 0;
                const bps = Math.round(pct * 100);
                percentBpsInput.value = bps;
                
                valHint.innerHTML = `Mỗi 1 điểm khách hàng sử dụng sẽ được giảm <strong>${pct}%</strong> tổng giá trị đơn hàng (tương đương ${bps} BPS).`;
            } else {
                vndContainer.style.display = 'block';
                percentContainer.style.display = 'none';
                
                const val = parseInt(vndInput.value) || 0;
                valHint.innerHTML = `Mỗi 1 điểm khách hàng sử dụng sẽ được giảm trực tiếp <strong>${val.toLocaleString()} đ</strong>.`;
            }
        }

        if (typeSelect) {
            typeSelect.addEventListener('change', updateRedeemFields);
            if (vndInput) vndInput.addEventListener('input', updateRedeemFields);
            if (percentDisplayInput) percentDisplayInput.addEventListener('input', updateRedeemFields);
            updateRedeemFields();
        }

        // --- JS Thêm/Xóa mốc Rank hạng thành viên ---
        const addTierBtn = document.getElementById('add_tier_btn');
        const tiersTable = document.getElementById('tiers_table');
        const tiersBody = tiersTable ? tiersTable.querySelector('tbody') : null;

        if (addTierBtn && tiersBody) {
            addTierBtn.addEventListener('click', function() {
                const noTiersRow = tiersBody.querySelector('.no-tiers-row');
                if (noTiersRow) {
                    noTiersRow.remove();
                }

                const rows = tiersBody.querySelectorAll('.tier-row');
                let nextIdx = 0;
                rows.forEach(r => {
                    const idx = parseInt(r.getAttribute('data-index')) || 0;
                    if (idx >= nextIdx) {
                        nextIdx = idx + 1;
                    }
                });

                const newRow = document.createElement('tr');
                newRow.className = 'tier-row';
                newRow.setAttribute('data-index', nextIdx);
                newRow.innerHTML = `
                    <td>
                        <input type="text" name="tiers[${nextIdx}][name]" required class="form-control-modern" placeholder="Ví dụ: Vàng">
                    </td>
                    <td>
                        <input type="number" name="tiers[${nextIdx}][min_points]" value="0" min="0" required class="form-control-modern">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="tiers[${nextIdx}][multiplier]" value="1.0" min="1" required class="form-control-modern">
                    </td>
                    <td style="text-align: center;">
                        <button type="button" class="btn-action-sm btn-action-danger remove-row-btn">Xóa</button>
                    </td>
                `;
                
                tiersBody.appendChild(newRow);
                lucide.createIcons();
            });
        }

        // --- JS Thêm/Xóa chiến dịch sự kiện ---
        const addCampaignBtn = document.getElementById('add_campaign_btn');
        const campaignsTable = document.getElementById('campaigns_table');
        const campaignsBody = campaignsTable ? campaignsTable.querySelector('tbody') : null;

        if (addCampaignBtn && campaignsBody) {
            addCampaignBtn.addEventListener('click', function() {
                const noCampaignsRow = campaignsBody.querySelector('.no-campaigns-row');
                if (noCampaignsRow) {
                    noCampaignsRow.remove();
                }

                const rows = campaignsBody.querySelectorAll('.campaign-row');
                let nextIdx = 0;
                rows.forEach(r => {
                    const idx = parseInt(r.getAttribute('data-index')) || 0;
                    if (idx >= nextIdx) {
                        nextIdx = idx + 1;
                    }
                });

                const todayStr = new Date().toISOString().split('T')[0];

                const newRow = document.createElement('tr');
                newRow.className = 'campaign-row';
                newRow.setAttribute('data-index', nextIdx);
                newRow.innerHTML = `
                    <td>
                        <input type="text" name="campaigns[${nextIdx}][name]" required class="form-control-modern" placeholder="Ví dụ: Lễ Tết">
                    </td>
                    <td>
                        <input type="date" name="campaigns[${nextIdx}][start_date]" value="${todayStr}" required class="form-control-modern">
                    </td>
                    <td>
                        <input type="date" name="campaigns[${nextIdx}][end_date]" value="${todayStr}" required class="form-control-modern">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="campaigns[${nextIdx}][multiplier]" value="1.0" min="1" required class="form-control-modern">
                    </td>
                    <td style="text-align: center;">
                        <button type="button" class="btn-action-sm btn-action-danger remove-row-btn">Xóa</button>
                    </td>
                `;
                
                campaignsBody.appendChild(newRow);
                lucide.createIcons();
                
                // Khởi tạo Custom Datepicker cho các ô date mới vừa được thêm
                if (typeof window.initGlobalMedicareDatePickers === 'function') {
                    window.initGlobalMedicareDatePickers();
                }
            });
        }

        // Lắng nghe sự kiện click nút Xóa dòng ở cả 2 bảng
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove-row-btn')) {
                const tr = e.target.closest('tr');
                const tbody = tr.parentNode;
                tr.remove();

                if (tbody.children.length === 0) {
                    if (tbody.parentNode.id === 'tiers_table') {
                        tbody.innerHTML = `
                            <tr class="no-tiers-row">
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 24px;">Chưa cấu hình mốc hạng thành viên. Nhấn nút "Thêm hạng mới" để bắt đầu.</td>
                            </tr>
                        `;
                    } else if (tbody.parentNode.id === 'campaigns_table') {
                        tbody.innerHTML = `
                            <tr class="no-campaigns-row">
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 24px;">Chưa cấu hình chiến dịch ưu đãi. Nhấn nút "Thêm sự kiện" để bắt đầu.</td>
                            </tr>
                        `;
                    }
                }
            }
        });
    });
</script>
@endsection
