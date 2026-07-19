@extends('vaccine::layouts.admin')

@section('title', 'Cấu hình Website - Medicare Cờ Đỏ')
@section('page_title', 'Cấu Hình Hệ Thống Website')

@section('admin_content')
<div style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; padding: 40px; max-width: 700px; margin: 0 auto;">
    <h2 style="font-family: 'Roboto', sans-serif; font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 24px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; display: flex; align-items: center; gap: 8px;"><i data-lucide="settings" style="color:var(--primary-color);"></i> Thay đổi thông tin hiển thị</h2>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 24px; padding: 16px; border-radius: 8px; background-color: #fde8e8; color: #9b1c1c; border: 1px solid #fbd5d5;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr; gap: 24px; margin-bottom: 30px;">
            <!-- Tên Website -->
            <div class="form-group">
                <label for="site_name" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Tên thương hiệu hệ thống *</label>
                <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $settings['site_name']) }}" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
            </div>

            <!-- Hotline chính -->
            <div class="form-group">
                <label for="hotline" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Hotline liên hệ chính *</label>
                <input type="text" name="hotline" id="hotline" value="{{ old('hotline', $settings['hotline']) }}" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
            </div>

            <!-- Hotline phụ -->
            <div class="form-group">
                <label for="hotline_2" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Hotline liên hệ phụ (tùy chọn)</label>
                <input type="text" name="hotline_2" id="hotline_2" value="{{ old('hotline_2', $settings['hotline_2']) }}" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
            </div>

            <!-- Email CSKH -->
            <div class="form-group">
                <label for="email" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Email hỗ trợ khách hàng *</label>
                <input type="email" name="email" id="email" value="{{ old('email', $settings['email']) }}" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
            </div>

            <!-- Địa chỉ trụ sở chính -->
            <div class="form-group">
                <label for="address" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Địa chỉ trụ sở chính *</label>
                <input type="text" name="address" id="address" value="{{ old('address', $settings['address']) }}" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
            </div>

            <!-- Footer Text -->
            <div class="form-group">
                <label for="footer_text" style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Thông tin chân trang (Footer Copyright) *</label>
                <textarea name="footer_text" id="footer_text" rows="3" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; font-family: inherit;">{{ old('footer_text', $settings['footer_text']) }}</textarea>
            </div>
        </div>

        <div style="border-top: 1px solid #e2e8f0; padding-top: 24px; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn-primary" style="padding: 12px 30px; border-radius: 8px; border: none; color: #ffffff; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="save"></i> Lưu thông số cấu hình
            </button>
        </div>
    </form>
</div>
@endsection
