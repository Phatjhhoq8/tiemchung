@extends('vaccine::layouts.admin')

@section('title', 'Cấu Hình Trang Web - Medicare')
@section('page_title', 'Cấu Hình Hệ Thống Trang Web')

@section('admin_content')
<div class="card-modern">
    <h2 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--text-primary); margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="settings" style="color: var(--primary-color);"></i> Thay đổi thông tin hiển thị
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

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        {{-- Grid 2 cột cho các thông tin ngắn --}}
        <div class="form-grid-2" style="margin-bottom: 24px;">
            <!-- Logo Hệ Thống -->
            <div class="form-group-modern" style="grid-column: span 2;">
                <label class="form-label-modern">Logo hệ thống (Medicare)</label>
                <div style="display: flex; align-items: center; gap: 20px; background-color: #f8fafc; border: 1px solid var(--border-color); border-radius: 8px; padding: 12px 16px;">
                    <div style="width: 140px; height: 50px; border: 1px dashed #cbd5e1; border-radius: 6px; display: flex; align-items: center; justify-content: center; background: #ffffff; padding: 4px; overflow: hidden;">
                        <img src="{{ asset($settings['site_logo'] ?? 'images/logo.png') }}" alt="Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                    <div style="flex: 1;">
                        <input type="file" name="site_logo" id="site_logo" accept="image/*,.svg" class="form-control-modern" style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; width: auto; font-size: 13px;">
                        <div style="font-size: 11px; color: #64748b; margin-top: 5px;">Chấp nhận file ảnh định dạng jpeg, png, jpg, webp, svg (Dung lượng tối đa: 4MB)</div>
                    </div>
                </div>
            </div>

            <!-- Tên Website -->
            <div class="form-group-modern">
                <label for="site_name" class="form-label-modern">Tên thương hiệu hệ thống *</label>
                <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $settings['site_name']) }}" required class="form-control-modern">
            </div>

            <!-- Email CSKH -->
            <div class="form-group-modern">
                <label for="email" class="form-label-modern">Email hỗ trợ khách hàng *</label>
                <input type="email" name="email" id="email" value="{{ old('email', $settings['email']) }}" required class="form-control-modern">
            </div>

            <!-- Hotline chính -->
            <div class="form-group-modern">
                <label for="hotline" class="form-label-modern">Hotline liên hệ chính *</label>
                <input type="text" name="hotline" id="hotline" value="{{ old('hotline', $settings['hotline']) }}" required class="form-control-modern">
            </div>

            <!-- Hotline phụ -->
            <div class="form-group-modern">
                <label for="hotline_2" class="form-label-modern">Hotline liên hệ phụ (tùy chọn)</label>
                <input type="text" name="hotline_2" id="hotline_2" value="{{ old('hotline_2', $settings['hotline_2']) }}" class="form-control-modern">
            </div>
        </div>

        {{-- Cột dọc cho các thông tin dài (100% width) --}}
        <div style="display: grid; grid-template-columns: 1fr; gap: 24px; margin-bottom: 30px;">
            <!-- Địa chỉ trụ sở chính -->
            <div class="form-group-modern">
                <label for="address" class="form-label-modern">Địa chỉ trụ sở chính *</label>
                <input type="text" name="address" id="address" value="{{ old('address', $settings['address']) }}" required class="form-control-modern">
            </div>

            <!-- Footer Text -->
            <div class="form-group-modern">
                <label for="footer_text" class="form-label-modern">Thông tin chân trang (Footer Copyright) *</label>
                <textarea name="footer_text" id="footer_text" rows="3" required class="form-control-modern" style="font-family: inherit;">{{ old('footer_text', $settings['footer_text']) }}</textarea>
            </div>
        </div>

        <div style="border-top: 1px solid var(--border-color); padding-top: 24px; display: flex; justify-content: flex-end; margin-top: 30px;">
            <button type="submit" class="btn-modern btn-modern-primary">
                <i data-lucide="save"></i> Lưu thông số cấu hình
            </button>
        </div>
    </form>
</div>
@endsection
