@extends('vaccine::layouts.app')

@section('title', 'Danh Sách Chi Nhánh & Thông Tin Liên Hệ - Medicare')

@section('content')
<!-- CATALOG HERO BANNER (Tràn viền) -->
<section class="catalog-hero" style="background: linear-gradient(135deg, rgba(200, 16, 46, 0.93) 0%, rgba(145, 10, 33, 0.90) 100%), url('https://images.unsplash.com/photo-1516549655169-df83a0774514?auto=format&fit=crop&w=1600&q=80') no-repeat center center / cover; margin-top: -2rem;">
    <div class="catalog-hero-container">
        <div class="catalog-hero-content">
            <!-- Breadcrumb -->
            <div class="catalog-breadcrumb">
                <a href="{{ route('home') }}">Trang chủ</a>
                <i data-lucide="chevron-right"></i>
                <span>Liên hệ</span>
            </div>
            <h1>Thông tin liên hệ & Chi nhánh</h1>
            <p>Hệ thống Medicare tự hào phục vụ quý khách tại các trung tâm tiêm chủng hiện đại, an toàn và dịch vụ chăm sóc tận tâm.</p>
        </div>
        <div class="catalog-hero-visual" aria-hidden="true">
            <svg viewBox="0 0 300 220" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="contactGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#ffffff" stop-opacity="0.25" />
                        <stop offset="100%" stop-color="#ffffff" stop-opacity="0.05" />
                    </linearGradient>
                </defs>
                <circle cx="150" cy="110" r="85" fill="#ffffff" opacity="0.08" stroke="#ffffff" stroke-width="1.5" stroke-dasharray="4 4"/>
                <!-- Map card -->
                <g transform="translate(90, 50)">
                    <rect x="10" y="10" width="100" height="90" rx="16" fill="url(#contactGrad)" stroke="#ffffff" stroke-width="2.5" />
                    <!-- Map path representation -->
                    <path d="M 20,40 Q 50,60 80,30 T 100,80" stroke="#ffffff" stroke-width="1.5" stroke-dasharray="2 4" fill="none" opacity="0.4" />
                    <!-- Location pin -->
                    <g transform="translate(50, 25)">
                        <path d="M 12,2 C 6.5,2 2,6.5 2,12 C 2,18.5 12,28 12,28 C 12,28 22,18.5 22,12 C 22,6.5 17.5,2 12,2 Z" fill="var(--primary-color)" stroke="#ffffff" stroke-width="2" />
                        <circle cx="12" cy="11" r="4" fill="#ffffff" />
                    </g>
                </g>
                <g transform="translate(180, 110)">
                    <circle cx="20" cy="20" r="22" fill="#ffffff" opacity="0.15" />
                    <circle cx="20" cy="20" r="18" fill="var(--secondary-color)" />
                    <!-- Phone receiver icon -->
                    <path d="M 13,13 C 13,18 17,22 22,22 C 24,22 25,21 26,20 L 23,17 L 22,18 C 20,17 18,15 17,13 L 18,12 L 15,9 C 14,10 13,11 13,13 Z" fill="#ffffff" stroke="#ffffff" stroke-width="1" />
                </g>
            </svg>
        </div>
    </div>
</section>

<div class="contact-wrapper" style="max-width: 1200px; margin: 0 auto 40px; padding: 0 20px;">

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
        <div style="background: var(--bg-card); border: 2px solid #dc2626; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 16px rgba(220, 38, 38, 0.08);">
            <div style="padding: 32px;">
                <span style="background-color: #dc2626; color: #ffffff; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; margin-bottom: 16px;">Chi Nhánh 2</span>
                <h3 style="font-size: 22px; font-weight: 800; color: #1e293b; margin-top: 0; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="building-2" style="color: #dc2626; width: 26px; height: 26px;"></i>
                    Medicare Thới Lai
                </h3>
                
                <div style="display: flex; flex-direction: column; gap: 16px; font-size: 15px; color: #475569;">
                    <p style="margin: 0; display: flex; align-items: flex-start; gap: 12px; line-height: 1.6;">
                        <i data-lucide="map-pin" style="color: #dc2626; width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;"></i>
                        <span><strong>Địa chỉ:</strong> Thị trấn Thới Lai, Huyện Thới Lai, TP. Cần Thơ</span>
                    </p>
                    <p style="margin: 0; display: flex; align-items: flex-start; gap: 12px; line-height: 1.6;">
                        <i data-lucide="phone-call" style="color: #dc2626; width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;"></i>
                        <span><strong>Hotline / Zalo:</strong> <a href="tel:0932477184" style="color: #dc2626; font-weight: 800; text-decoration: none; font-size: 16px;">0932 477 184</a></span>
                    </p>
                    <p style="margin: 0; display: flex; align-items: flex-start; gap: 12px; line-height: 1.6;">
                        <i data-lucide="clock" style="color: #dc2626; width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;"></i>
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
                <a href="{{ route('register.show') }}" class="btn-primary" style="flex: 1; text-align: center; justify-content: center; padding: 12px; background-color: #dc2626;">
                    <i data-lucide="calendar"></i> Hẹn tiêm tại Thới Lai
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
