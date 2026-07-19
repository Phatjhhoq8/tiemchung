@php
    $site_name = \Modules\VaccineRegistration\Models\Setting::get('site_name', 'Medicare Cờ Đỏ');
    $hotline = \Modules\VaccineRegistration\Models\Setting::get('hotline', '0938 60 38 39');
    $hotline_2 = \Modules\VaccineRegistration\Models\Setting::get('hotline_2', '0932 477 184');
    $email = \Modules\VaccineRegistration\Models\Setting::get('email', 'cskh@medicarecodo.vn');
    $address = \Modules\VaccineRegistration\Models\Setting::get('address', 'Ấp Thới Hòa, Thị trấn Cờ Đỏ, Huyện Cờ Đỏ, TP. Cần Thơ');
    $footer_text = \Modules\VaccineRegistration\Models\Setting::get('footer_text', '© 2026 Medicare Cờ Đỏ. Đảm bảo an toàn - chất lượng hàng đầu.');
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Phòng Tiêm Chủng Medicare Cờ Đỏ')</title>
    
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
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    @yield('styles')
</head>
<body>
    <!-- Topbar liên hệ nhanh -->
    <div class="top-bar">
        <div class="topbar-container">
            <div class="topbar-info">
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($address) }}" target="_blank" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: color 0.2s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='inherit'">
                    <i data-lucide="map-pin"></i> {{ $address }}
                </a>
                <span class="divider">|</span>
                <a href="mailto:{{ $email }}" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: color 0.2s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='inherit'">
                    <i data-lucide="mail"></i> {{ $email }}
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
                <a href="{{ route('vaccine.index') }}" class="nav-link {{ Route::currentRouteName() === 'vaccine.index' ? 'active' : '' }}">Bảng Giá Vắc Xin</a>
                <a href="{{ route('register.show') }}" class="nav-link {{ Route::currentRouteName() === 'register.show' ? 'active' : '' }}">Đăng Ký Tiêm</a>
            </nav>
            
            <div class="header-actions" style="display: flex; align-items: center; gap: 12px;">
                <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle dark mode" style="background: none; border: 1px solid var(--border-color); color: var(--text-primary); cursor: pointer; padding: 10px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; transition: all 0.2s;">
                    <i data-lucide="sun" class="sun-icon" style="width: 20px; height: 20px; display: none; color: #eaaa00;"></i>
                    <i data-lucide="moon" class="moon-icon" style="width: 20px; height: 20px;"></i>
                </button>
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

    <!-- JS Custom -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        // Khởi tạo các Lucide Icons
        lucide.createIcons();

        // Dark Mode Toggle Logic
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            const sunIcon = themeToggle.querySelector('.sun-icon');
            const moonIcon = themeToggle.querySelector('.moon-icon');

            function updateToggleIcons() {
                if (document.documentElement.classList.contains('dark')) {
                    sunIcon.style.display = 'block';
                    moonIcon.style.display = 'none';
                } else {
                    sunIcon.style.display = 'none';
                    moonIcon.style.display = 'block';
                }
            }

            updateToggleIcons();

            themeToggle.addEventListener('click', () => {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
                updateToggleIcons();
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
