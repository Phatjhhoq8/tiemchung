@extends('vaccine::layouts.app')

@section('title', 'Danh Sách Chi Nhánh & Thông Tin Liên Hệ - Medicare')

@section('content')
<div style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    <!-- Breadcrumb -->
    <div style="margin-bottom: 24px; color: var(--text-muted); font-size: 14px;">
        <a href="{{ route('home') }}" style="color: var(--text-muted); text-decoration: none;">Trang chủ</a> / 
        <span style="color: var(--primary-color); font-weight: 600;">Thông tin liên hệ & Chi nhánh</span>
    </div>

    <div class="section-title-wrapper" style="text-align: center; margin-bottom: 40px;">
        <span class="section-badge">Mạng Lưới Trung Tâm</span>
        <h2>Danh Sách Chi Nhánh Tiêm Chủng Medicare</h2>
        <p>Medicare tự hào phục vụ quý khách hàng tại 2 chi nhánh trung tâm hiện đại với đầy đủ nguồn vắc xin chất lượng cao.</p>
    </div>

    <!-- Danh sách 2 Chi nhánh Chi Tiết -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 36px; margin-bottom: 50px;">
        
        <!-- CHI NHÁNH 1: MEDICARE CỜ ĐỎ -->
        <div style="background: var(--bg-card); border: 2px solid var(--primary-color); border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 16px rgba(200, 16, 46, 0.08);">
            <div style="padding: 32px;">
                <span style="background-color: var(--primary-color); color: #ffffff; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; margin-bottom: 16px;">Chi Nhánh 1</span>
                <h3 style="font-size: 22px; font-weight: 800; color: #1e293b; margin-top: 0; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="building-2" style="color: var(--primary-color); width: 26px; height: 26px;"></i>
                    Medicare Cờ Đỏ
                </h3>
                
                <div style="display: flex; flex-direction: column; gap: 16px; font-size: 15px; color: #475569;">
                    <p style="margin: 0; display: flex; align-items: flex-start; gap: 12px; line-height: 1.6;">
                        <i data-lucide="map-pin" style="color: var(--primary-color); width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;"></i>
                        <span><strong>Địa chỉ:</strong> Cổng Bệnh viện Quân Dân Y TP Cần Thơ, Ấp Thới Bình, Xã Cờ Đỏ, TP. Cần Thơ</span>
                    </p>
                    <p style="margin: 0; display: flex; align-items: flex-start; gap: 12px; line-height: 1.6;">
                        <i data-lucide="phone-call" style="color: var(--primary-color); width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;"></i>
                        <span><strong>Hotline / Zalo:</strong> <a href="tel:0938603839" style="color: var(--primary-color); font-weight: 800; text-decoration: none; font-size: 16px;">0938 60 38 39</a></span>
                    </p>
                    <p style="margin: 0; display: flex; align-items: flex-start; gap: 12px; line-height: 1.6;">
                        <i data-lucide="clock" style="color: var(--primary-color); width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;"></i>
                        <span><strong>Giờ làm việc:</strong> 7:00 – 17:00 (Tất cả các ngày trong tuần kể cả Thứ 7, Chủ Nhật & Ngày Lễ)</span>
                    </p>
                </div>
            </div>

            <!-- Khung bản đồ Google Maps Chi nhánh 1 -->
            <div style="height: 240px; width: 100%; border-top: 1px solid var(--border-color);">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15715.11438965902!2d105.4283187!3d10.034502!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a0860ed712613d%3A0xb3e6a9a7a6abeb53!2sC%C6%A1%20%C4%90%E1%BB%8F%2C%20C%C6%A1%20%C4%90%E1%BB%8F%2C%20C%E1%BA%A7n%20Th%C6%A1!5e0!3m2!1svi!2s!4v1700000000000!5m2!1svi!2s" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>

            <div style="padding: 20px; background: var(--bg-main); border-top: 1px solid var(--border-color); display: flex; gap: 12px;">
                <a href="{{ route('register.show') }}" class="btn-primary" style="flex: 1; text-align: center; justify-content: center; padding: 12px;">
                    <i data-lucide="calendar"></i> Hẹn tiêm tại Cờ Đỏ
                </a>
            </div>
        </div>

        <!-- CHI NHÁNH 2: MEDICARE THỚI LAI -->
        <div style="background: var(--bg-card); border: 2px solid #0284c7; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 16px rgba(2, 132, 199, 0.08);">
            <div style="padding: 32px;">
                <span style="background-color: #0284c7; color: #ffffff; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; margin-bottom: 16px;">Chi Nhánh 2</span>
                <h3 style="font-size: 22px; font-weight: 800; color: #1e293b; margin-top: 0; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="building-2" style="color: #0284c7; width: 26px; height: 26px;"></i>
                    Medicare Thới Lai
                </h3>
                
                <div style="display: flex; flex-direction: column; gap: 16px; font-size: 15px; color: #475569;">
                    <p style="margin: 0; display: flex; align-items: flex-start; gap: 12px; line-height: 1.6;">
                        <i data-lucide="map-pin" style="color: #0284c7; width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;"></i>
                        <span><strong>Địa chỉ:</strong> Thị trấn Thới Lai, Huyện Thới Lai, TP. Cần Thơ</span>
                    </p>
                    <p style="margin: 0; display: flex; align-items: flex-start; gap: 12px; line-height: 1.6;">
                        <i data-lucide="phone-call" style="color: #0284c7; width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;"></i>
                        <span><strong>Hotline / Zalo:</strong> <a href="tel:0932477184" style="color: #0284c7; font-weight: 800; text-decoration: none; font-size: 16px;">0932 477 184</a></span>
                    </p>
                    <p style="margin: 0; display: flex; align-items: flex-start; gap: 12px; line-height: 1.6;">
                        <i data-lucide="clock" style="color: #0284c7; width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;"></i>
                        <span><strong>Giờ làm việc:</strong> 7:00 – 17:00 (Tất cả các ngày trong tuần kể cả Thứ 7, Chủ Nhật & Ngày Lễ)</span>
                    </p>
                </div>
            </div>

            <!-- Khung bản đồ Google Maps Chi nhánh 2 -->
            <div style="height: 240px; width: 100%; border-top: 1px solid var(--border-color);">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15717.382897451512!2d105.5458925!3d10.0528014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a0890ed712613d%3A0xb3e6a9a7a6abeb53!2sTh%E1%BB%9Bi%20Lai%2C%20Th%E1%BB%9Bi%20Lai%2C%20C%E1%BA%A7n%20Th%C6%A1!5e0!3m2!1svi!2s!4v1700000000000!5m2!1svi!2s" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>

            <div style="padding: 20px; background: var(--bg-main); border-top: 1px solid var(--border-color); display: flex; gap: 12px;">
                <a href="{{ route('register.show') }}" class="btn-primary" style="flex: 1; text-align: center; justify-content: center; padding: 12px; background-color: #0284c7;">
                    <i data-lucide="calendar"></i> Hẹn tiêm tại Thới Lai
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
