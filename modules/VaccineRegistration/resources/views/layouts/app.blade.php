@php
    $site_name = \Modules\VaccineRegistration\Models\Setting::get('site_name', 'Medicare');
    $hotline = \Modules\VaccineRegistration\Models\Setting::get('hotline', '0938 60 38 39');
    $hotline_2 = \Modules\VaccineRegistration\Models\Setting::get('hotline_2', '0932 477 184');
    $email = \Modules\VaccineRegistration\Models\Setting::get('email', 'cskh@medicare.vn');
    $address = \Modules\VaccineRegistration\Models\Setting::get('address', 'Chi nhánh 1: Cổng Bệnh viện Quân Dân Y TP Cần Thơ, Ấp Thới Bình, Xã Cờ Đỏ, TP. Cần Thơ');
    $footer_text = \Modules\VaccineRegistration\Models\Setting::get('footer_text', '© 2026 Medicare - Hệ Thống Tiêm Chủng Vắc Xin Trẻ Em và Người Lớn.');
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hệ Thống Tiêm Chủng Medicare')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Dark Mode Check -->
    <script>
        document.documentElement.classList.remove('dark');
    </script>
    @yield('styles')
</head>
<body>
    <!-- Topbar liên hệ nhanh hệ thống nhiều chi nhánh -->
    <div class="top-bar">
        <div class="topbar-container">
            <div class="topbar-info" style="display: flex; gap: 16px; align-items: center; font-size: 13px;">
                <a href="{{ route('contact') }}" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <i data-lucide="map-pin" style="color: var(--secondary-color);"></i> <strong>Chi nhánh 1:</strong> Cờ Đỏ (Hotline: 0938 60 38 39)
                </a>
                <span class="divider">|</span>
                <a href="{{ route('contact') }}" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <i data-lucide="map-pin" style="color: #0284c7;"></i> <strong>Chi nhánh 2:</strong> Thới Lai (Hotline: 0932 477 184)
                </a>
            </div>
            <div class="topbar-social">
                <a href="{{ route('contact') }}" style="color: #ffffff; text-decoration: none; font-size: 12.5px; font-weight: 700; background: rgba(255,255,255,0.15); padding: 4px 12px; border-radius: 12px;">
                    Mạng lưới chi nhánh Medicare →
                </a>
            </div>
        </div>
    </div>

    <!-- Header chính -->
    <header class="app-header">
        <div class="header-container">
            <a href="{{ route('home') }}" class="logo" style="display: flex; align-items: center; padding: 5px 0;">
                <img src="{{ asset('images/logo.png') }}" alt="{{ $site_name }}" style="max-height: 48px; width: auto; object-fit: contain;">
            </a>
            
            <nav class="nav-menu">
                <a href="{{ route('home') }}" class="nav-link {{ Route::currentRouteName() === 'home' ? 'active' : '' }}">Trang Chủ</a>
                <a href="{{ route('about') }}" class="nav-link {{ Route::currentRouteName() === 'about' ? 'active' : '' }}">Giới Thiệu</a>
                <a href="{{ route('vaccine.index') }}" class="nav-link {{ Route::currentRouteName() === 'vaccine.index' ? 'active' : '' }}">Bảng Giá Vắc Xin</a>
                <a href="{{ route('services') }}" class="nav-link {{ Route::currentRouteName() === 'services' ? 'active' : '' }}">Dịch Vụ</a>
                <a href="{{ route('news.index') }}" class="nav-link {{ str_contains(Route::currentRouteName(), 'news') ? 'active' : '' }}">Tin Tức</a>
                <a href="{{ route('contact') }}" class="nav-link {{ Route::currentRouteName() === 'contact' ? 'active' : '' }}">Liên Hệ</a>
            </nav>
            
            <div class="header-actions" style="display: flex; align-items: center; gap: 12px;">
                <a href="tel:{{ str_replace(' ', '', $hotline) }}" class="hotline-btn">
                    <i data-lucide="phone-call"></i>
                    <span>Tư vấn: {{ $hotline }}</span>
                </a>
                <a href="{{ route('register.show') }}" class="btn-primary-header" style="background-color: var(--primary-color); color: #ffffff; padding: 10px 18px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 4px 12px rgba(200, 16, 46, 0.15);" onmouseover="this.style.backgroundColor='var(--primary-hover)'; this.style.transform='translateY(-1px)';" onmouseout="this.style.backgroundColor='var(--primary-color)'; this.style.transform='translateY(0)';">
                    <i data-lucide="calendar-plus" style="width: 16px; height: 16px;"></i>
                    <span>Đăng ký tiêm</span>
                </a>
            </div>
        </div>
    </header>


    <!-- Main Content -->
    <main class="app-main {{ Route::currentRouteName() === 'home' ? 'home-main' : '' }}">
        @if(session('success') || session('error') || session('warning') || session('info'))
            <div class="flash-messages-container" style="max-width: 1200px; margin: 20px auto 0 auto; padding: 0 20px;">
                @if(session('success'))
                    <div class="alert alert-success" style="background-color: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 16px; border-radius: 8px; display: flex; align-items: center; gap: 12px; font-weight: 500; position: relative;">
                        <i data-lucide="check-circle" style="color: #10b981; width: 20px; height: 20px; flex-shrink: 0;"></i>
                        <span style="flex-grow: 1;">{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #065f46; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0;"><i data-lucide="x" style="width: 18px; height: 18px;"></i></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger" style="background-color: #fef2f2; border: 1px solid #ef4444; color: #991b1b; padding: 16px; border-radius: 8px; display: flex; align-items: center; gap: 12px; font-weight: 500; position: relative;">
                        <i data-lucide="x-circle" style="color: #ef4444; width: 20px; height: 20px; flex-shrink: 0;"></i>
                        <span style="flex-grow: 1;">{{ session('error') }}</span>
                        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #991b1b; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0;"><i data-lucide="x" style="width: 18px; height: 18px;"></i></button>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning" style="background-color: #fffbeb; border: 1px solid #f59e0b; color: #92400e; padding: 16px; border-radius: 8px; display: flex; align-items: center; gap: 12px; font-weight: 500; position: relative;">
                        <i data-lucide="alert-circle" style="color: #f59e0b; width: 20px; height: 20px; flex-shrink: 0;"></i>
                        <span style="flex-grow: 1;">{{ session('warning') }}</span>
                        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #92400e; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0;"><i data-lucide="x" style="width: 18px; height: 18px;"></i></button>
                    </div>
                @endif
                @if(session('info'))
                    <div class="alert alert-info" style="background-color: #eff6ff; border: 1px solid #3b82f6; color: #1e3a8a; padding: 16px; border-radius: 8px; display: flex; align-items: center; gap: 12px; font-weight: 500; position: relative;">
                        <i data-lucide="info" style="color: #3b82f6; width: 20px; height: 20px; flex-shrink: 0;"></i>
                        <span style="flex-grow: 1;">{{ session('info') }}</span>
                        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #1e3a8a; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0;"><i data-lucide="x" style="width: 18px; height: 18px;"></i></button>
                    </div>
                @endif
            </div>
        @endif
        @yield('content')
    </main>

    <!-- Footer chính -->
    <footer class="app-footer">
        <div class="footer-container">
            <div class="footer-info">
                <h3>{{ $site_name }}</h3>
                <p>Medicare Cờ Đỏ cung cấp dịch vụ tiêm chủng vắc xin trẻ em và người lớn chất lượng cao, an toàn và hiệu quả hàng đầu.</p>
                <div class="contact-details">
                    <p style="display: flex; align-items: flex-start; gap: 10px; line-height: 1.6;">
                        <i data-lucide="phone" style="flex-shrink: 0; margin-top: 3px; color: var(--secondary-color); width: 16px; height: 16px;"></i>
                        <span>
                            <strong>Hotline:</strong> 
                            <a href="tel:{{ str_replace([' ', '.', '-'], '', $hotline) }}" style="color: inherit; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--secondary-color)'" onmouseout="this.style.color='inherit'">{{ $hotline }}</a> - 
                            <a href="tel:{{ str_replace([' ', '.', '-'], '', $hotline_2) }}" style="color: inherit; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--secondary-color)'" onmouseout="this.style.color='inherit'">{{ $hotline_2 }}</a>
                        </span>
                    </p>
                    <p style="display: flex; align-items: flex-start; gap: 10px; line-height: 1.6;">
                        <i data-lucide="mail" style="flex-shrink: 0; margin-top: 3px; color: var(--secondary-color); width: 16px; height: 16px;"></i>
                        <span>
                            <strong>Email:</strong> 
                            <a href="mailto:{{ $email }}" style="color: inherit; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--secondary-color)'" onmouseout="this.style.color='inherit'">{{ $email }}</a>
                        </span>
                    </p>
                    <p style="display: flex; align-items: flex-start; gap: 10px; line-height: 1.6;">
                        <i data-lucide="map-pin" style="flex-shrink: 0; margin-top: 3px; color: var(--secondary-color); width: 16px; height: 16px;"></i>
                        <span>
                            <strong>Địa chỉ:</strong> 
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($address) }}" target="_blank" style="color: inherit; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--secondary-color)'" onmouseout="this.style.color='inherit'">{{ $address }}</a>
                        </span>
                    </p>
                </div>
            </div>
            
            <div class="footer-links">
                <h4>Dịch Vụ Tiêm Chủng</h4>
                <ul style="list-style: none; padding-left: 0;">
                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 0.8rem;">
                        <i data-lucide="chevron-right" style="width: 14px; height: 14px; color: var(--secondary-color); flex-shrink: 0;"></i>
                        <a href="{{ route('vaccine.index', ['type' => 'single']) }}">Vắc xin lẻ trẻ em & người lớn</a>
                    </li>
                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 0.8rem;">
                        <i data-lucide="chevron-right" style="width: 14px; height: 14px; color: var(--secondary-color); flex-shrink: 0;"></i>
                        <a href="{{ route('vaccine.index', ['type' => 'package']) }}">Gói vắc xin toàn diện</a>
                    </li>
                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 0.8rem;">
                        <i data-lucide="chevron-right" style="width: 14px; height: 14px; color: var(--secondary-color); flex-shrink: 0;"></i>
                        <a href="{{ route('register.show') }}">Đặt lịch tiêm trực tuyến</a>
                    </li>
                </ul>
            </div>
            
            <div class="footer-tagline">
                <p>{{ $footer_text }}</p>
            </div>
        </div>
    </footer>

    <!-- Floating Contact & Zalo Widget (Bong bóng Chat tư vấn góc dưới bên phải) -->
    <div class="floating-chat-widget" style="position: fixed; bottom: 28px; right: 28px; z-index: 99999; display: flex; flex-direction: column; gap: 12px; align-items: flex-end;">
        <!-- Nút Chat Zalo Bác Sĩ -->
        <a href="https://zalo.me/0938603839" target="_blank" rel="noopener noreferrer" style="display: flex; align-items: center; gap: 10px; background-color: #0068ff; color: #ffffff; padding: 12px 20px; border-radius: 30px; box-shadow: 0 8px 24px rgba(0, 104, 255, 0.4); text-decoration: none; font-weight: 700; font-size: 14px; transition: all 0.3s ease; border: 2px solid #ffffff;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'" title="Chat Zalo Tư Vấn Vắc Xin Tức Thì">
            <div style="width: 26px; height: 26px; background: #ffffff; color: #0068ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 11px; flex-shrink: 0;">Zalo</div>
            <span>Chat Zalo Bác Sĩ</span>
        </a>

        <!-- Nút Hotline Tư Vấn 24/7 -->
        <a href="tel:0938603839" style="display: flex; align-items: center; gap: 10px; background-color: var(--primary-color, #c8102e); color: #ffffff; padding: 12px 20px; border-radius: 30px; box-shadow: 0 8px 24px rgba(200, 16, 46, 0.4); text-decoration: none; font-weight: 700; font-size: 14px; transition: all 0.3s ease; border: 2px solid #ffffff;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'" title="Gọi Hotline 0938 60 38 39">
            <i data-lucide="phone-call" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
            <span>0938 60 38 39</span>
        </a>
    </div>

    <!-- JS Custom -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        // Khởi tạo các Lucide Icons
        lucide.createIcons();

        // Hàm cuộn mượt SPA (Single-Page Navigation)
        function smoothScrollTo(elementId, event) {
            if (event) {
                event.preventDefault();
            }
            const targetElement = document.getElementById(elementId);
            if (targetElement) {
                // Cập nhật trạng thái active trên menu
                document.querySelectorAll('.nav-menu .nav-link').forEach(link => {
                    link.classList.remove('active');
                });
                if (event && event.currentTarget) {
                    event.currentTarget.classList.add('active');
                }
                
                // Cuộn mượt màng đến section
                const headerOffset = 80;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            } else {
                // Nếu đang ở trang khác, chuyển về trang chủ kèm anchor
                window.location.href = "{{ route('home') }}#" + elementId;
            }
        }
    </script>
    @yield('scripts')
</body>
</html>
