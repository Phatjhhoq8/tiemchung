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
    <meta name="description" content="@yield('meta_description', 'Medicare - Hệ thống tiêm chủng vắc xin an toàn, chất lượng hàng đầu tại Cần Thơ cho trẻ em và người lớn.')">
    <title>@yield('title', 'Hệ Thống Tiêm Chủng Medicare')</title>
    
    <!-- Google Fonts (Roboto + Inter for headings) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@600;700;800;900&family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- AOS Scroll Animation -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
    
    <!-- Vite & Flowbite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom CSS -->
    @php
        $css_ver = '1.0.0';
        try {
            if (file_exists(public_path('css/style.css'))) {
                $css_ver = filemtime(public_path('css/style.css'));
            }
        } catch (\Exception $e) {}
    @endphp
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ $css_ver }}">
    
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
                    <i data-lucide="map-pin" style="color: var(--secondary-color);"></i> <strong>Chi nhánh 2:</strong> Thới Lai (Hotline: 0932 477 184)
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
            
            <nav class="nav-menu" id="nav-menu">
                <a href="{{ route('home') }}" class="nav-link {{ Route::currentRouteName() === 'home' ? 'active' : '' }}">Trang Chủ</a>
                <a href="{{ route('about') }}" class="nav-link {{ Route::currentRouteName() === 'about' ? 'active' : '' }}">Giới Thiệu</a>
                <a href="{{ route('vaccine.index') }}" class="nav-link {{ Route::currentRouteName() === 'vaccine.index' ? 'active' : '' }}">Danh Mục Sản Phẩm</a>
                <a href="{{ route('services') }}" class="nav-link {{ Route::currentRouteName() === 'services' ? 'active' : '' }}">Dịch Vụ</a>
                <a href="{{ route('news.index') }}" class="nav-link {{ str_contains(Route::currentRouteName(), 'news') ? 'active' : '' }}">Tin Tức</a>
                <a href="{{ route('contact') }}" class="nav-link {{ Route::currentRouteName() === 'contact' ? 'active' : '' }}">Liên Hệ</a>
            </nav>
            
            <div class="header-actions" style="display: flex; align-items: center; gap: 12px;">
                <a href="tel:{{ str_replace(' ', '', $hotline) }}" class="hotline-btn hotline-btn-desktop">
                    <i data-lucide="phone-call"></i>
                    <span>Tư vấn: {{ $hotline }}</span>
                </a>
                <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="btn-primary-header">
                    <i data-lucide="calendar-plus" style="width: 16px; height: 16px;"></i>
                    <span>Đăng ký tiêm</span>
                </a>
                <!-- Mobile Hamburger Button -->
                <button class="mobile-menu-toggle" id="mobile-menu-toggle" onclick="toggleMobileMenu()" aria-label="Menu">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Drawer Menu -->
    <div class="mobile-menu-overlay" id="mobile-menu-overlay" onclick="toggleMobileMenu()"></div>
    <nav class="mobile-drawer" id="mobile-drawer">
        <div class="mobile-drawer-header">
            <a href="{{ route('home') }}" class="logo" style="display: flex; align-items: center;">
                <img src="{{ asset('images/logo.png') }}" alt="{{ $site_name }}" style="max-height: 40px; width: auto;">
            </a>
            <button onclick="toggleMobileMenu()" class="mobile-close-btn" aria-label="Đóng menu">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div class="mobile-drawer-links">
            <a href="{{ route('home') }}" class="mobile-nav-link {{ Route::currentRouteName() === 'home' ? 'active' : '' }}"><i data-lucide="home" class="w-5 h-5"></i> Trang Chủ</a>
            <a href="{{ route('about') }}" class="mobile-nav-link {{ Route::currentRouteName() === 'about' ? 'active' : '' }}"><i data-lucide="info" class="w-5 h-5"></i> Giới Thiệu</a>
            <a href="{{ route('vaccine.index') }}" class="mobile-nav-link {{ Route::currentRouteName() === 'vaccine.index' ? 'active' : '' }}"><i data-lucide="syringe" class="w-5 h-5"></i> Danh Mục Sản Phẩm</a>
            <a href="{{ route('services') }}" class="mobile-nav-link {{ Route::currentRouteName() === 'services' ? 'active' : '' }}"><i data-lucide="briefcase-medical" class="w-5 h-5"></i> Dịch Vụ</a>
            <a href="{{ route('news.index') }}" class="mobile-nav-link {{ str_contains(Route::currentRouteName(), 'news') ? 'active' : '' }}"><i data-lucide="newspaper" class="w-5 h-5"></i> Tin Tức</a>
            <a href="{{ route('contact') }}" class="mobile-nav-link {{ Route::currentRouteName() === 'contact' ? 'active' : '' }}"><i data-lucide="map-pin" class="w-5 h-5"></i> Liên Hệ</a>
        </div>
        <div class="mobile-drawer-footer">
            <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event); toggleMobileMenu();" class="mobile-cta-btn">
                <i data-lucide="calendar-plus" class="w-5 h-5"></i> Đăng ký tiêm chủng
            </a>
            <a href="tel:{{ str_replace(' ', '', $hotline) }}" class="mobile-hotline-btn">
                <i data-lucide="phone-call" class="w-5 h-5"></i> Hotline: {{ $hotline }}
            </a>
        </div>
    </nav>


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
                    <div class="alert alert-info" style="background-color: #fff1f2; border: 1px solid #f87171; color: #991b1b; padding: 16px; border-radius: 8px; display: flex; align-items: center; gap: 12px; font-weight: 500; position: relative;">
                        <i data-lucide="info" style="color: #dc2626; width: 20px; height: 20px; flex-shrink: 0;"></i>
                        <span style="flex-grow: 1;">{{ session('info') }}</span>
                        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #991b1b; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0;"><i data-lucide="x" style="width: 18px; height: 18px;"></i></button>
                    </div>
                @endif
            </div>
        @endif
        @yield('content')
    </main>

    <!-- Footer chính — VNVC Style Mẫu Hình 2 -->
    <footer class="app-footer-vnvc">
        <!-- Footer Top Header Bar -->
        <div class="footer-top-header">
            <div class="footer-header-container">
                <div class="footer-brand-title">
                    <a href="{{ route('home') }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                        <img src="{{ asset('images/logo.png') }}" alt="{{ $site_name }}" style="max-height: 40px; width: auto; filter: brightness(0) invert(1);">
                    </a>
                    <h3>HỆ THỐNG TRUNG TÂM TIÊM CHỦNG VẮC XIN CHO TRẺ EM & NGƯỜI LỚN AN TOÀN – UY TÍN – CHẤT LƯỢNG HÀNG ĐẦU VIỆT NAM</h3>
                </div>
                <div class="footer-top-actions">
                    <a href="{{ route('contact') }}" class="footer-top-item">
                        <i data-lucide="map-pin" style="width: 16px; height: 16px; color: var(--secondary-color);"></i>
                        <span>Tìm trung tâm Medicare</span>
                    </a>
                    <span style="opacity: 0.3;">|</span>
                    <a href="tel:{{ str_replace([' ', '.', '-'], '', $hotline) }}" class="footer-top-item">
                        <i data-lucide="phone-call" style="width: 16px; height: 16px; color: var(--secondary-color);"></i>
                        <span>Hotline: <strong>{{ $hotline }}</strong></span>
                    </a>
                    <span style="opacity: 0.3;">|</span>
                    <div class="footer-top-item">
                        <i data-lucide="clock" style="width: 16px; height: 16px; color: var(--secondary-color);"></i>
                        <span>Mở cửa 7:30 – 17:00 (không nghỉ trưa)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Body: Branch Network & Legal Info -->
        <div class="footer-body-container">
            <!-- Mạng Lưới Chi Nhánh -->
            <div class="footer-branches-title">
                HỆ THỐNG CHI NHÁNH TIÊM CHỦNG MEDICARE CẦN THƠ
            </div>
            
            <div class="footer-branch-list">
                <!-- Chi nhánh 1 -->
                <div class="footer-branch-item">
                    <h4>Medicare Cờ Đỏ (Chi nhánh 1)</h4>
                    <div class="footer-branch-info">
                        <p><i data-lucide="map-pin" style="width: 14px; height: 14px; color: var(--secondary-color); flex-shrink: 0; margin-top: 3px;"></i> Cổng Bệnh viện Quân Dân Y TP Cần Thơ, Ấp Thới Bình, Xã Cờ Đỏ, TP. Cần Thơ</p>
                        <p>
                            <i data-lucide="phone" style="width: 14px; height: 14px; color: var(--secondary-color); flex-shrink: 0; margin-top: 3px;"></i> Hotline: <strong>0938 60 38 39</strong>
                            <span style="opacity: 0.3; margin: 0 10px;">|</span>
                            <i data-lucide="clock" style="width: 14px; height: 14px; color: var(--secondary-color); flex-shrink: 0; margin-top: 3px;"></i> Thứ 2 – Thứ 7: 7:30 – 17:00
                        </p>
                    </div>
                    <div class="footer-branch-item-actions">
                        <a href="{{ route('contact') }}" class="footer-branch-link-map">
                            <i data-lucide="navigation" style="width: 13px; height: 13px;"></i> Xem bản đồ & chỉ đường
                        </a>
                        <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="footer-branch-link-book">
                            Đặt lịch tại CN1 →
                        </a>
                    </div>
                </div>

                <!-- Chi nhánh 2 -->
                <div class="footer-branch-item">
                    <h4>Medicare Thới Lai (Chi nhánh 2)</h4>
                    <div class="footer-branch-info">
                        <p><i data-lucide="map-pin" style="width: 14px; height: 14px; color: var(--secondary-color); flex-shrink: 0; margin-top: 3px;"></i> Trung tâm Y tế Huyện Thới Lai, Thị trấn Thới Lai, Huyện Thới Lai, TP. Cần Thơ</p>
                        <p>
                            <i data-lucide="phone" style="width: 14px; height: 14px; color: var(--secondary-color); flex-shrink: 0; margin-top: 3px;"></i> Hotline: <strong>0932 477 184</strong>
                            <span style="opacity: 0.3; margin: 0 10px;">|</span>
                            <i data-lucide="clock" style="width: 14px; height: 14px; color: var(--secondary-color); flex-shrink: 0; margin-top: 3px;"></i> Thứ 2 – Thứ 7: 7:30 – 17:00
                        </p>
                    </div>
                    <div class="footer-branch-item-actions">
                        <a href="{{ route('contact') }}" class="footer-branch-link-map">
                            <i data-lucide="navigation" style="width: 13px; height: 13px;"></i> Xem bản đồ & chỉ đường
                        </a>
                        <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="footer-branch-link-book">
                            Đặt lịch tại CN2 →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer Main Layout: Legal Info & Policy Links (Left) + QR Code (Right) -->
            <div class="footer-main-layout">
                <div class="footer-left-col">
                    <div class="footer-policy-links">
                        <a href="{{ route('about') }}">Chính sách bảo mật</a>
                        <span>•</span>
                        <a href="{{ route('services') }}">Khảo sát tiêm chủng</a>
                        <span>•</span>
                        <a href="{{ route('vaccine.index') }}">Chính sách thanh toán</a>
                        <span>•</span>
                        <a href="{{ route('contact') }}">Điều khoản sử dụng</a>
                    </div>
                    <div class="footer-company-details">
                        <h3>CÔNG TY CỔ PHẦN VẮC XIN MEDICARE</h3>
                        <p>Giấy chứng nhận ĐKKD số 0107631488 do Sở Kế hoạch và Đầu tư TP. Cần Thơ cấp ngày 11/11/2016</p>
                        <p><strong>Chi Nhánh 1:</strong> Cổng Bệnh viện Quân Dân Y TP Cần Thơ, Ấp Thới Bình, Xã Cờ Đỏ, TP. Cần Thơ</p>
                        <p><strong>Chi Nhánh 2:</strong> Trung tâm Y tế Huyện Thới Lai, Thị trấn Thới Lai, Huyện Thới Lai, TP. Cần Thơ</p>
                        <p><strong>Email:</strong> {{ $email }} | <strong>Số điện thoại:</strong> {{ $hotline }} - {{ $hotline_2 }}</p>
                        <p>Chịu trách nhiệm nội dung: Ban Giám Đốc HỆ THỐNG TIÊM CHỦNG MEDICARE</p>
                        <p style="margin-top: 10px; opacity: 0.75; font-size: 0.8rem;">Bản quyền ©2026 thuộc về CÔNG TY CỔ PHẦN VẮC XIN MEDICARE</p>
                    </div>
                </div>

                <div class="footer-right-col" style="display: flex; justify-content: flex-end;">
                    <div class="footer-qr-card">
                        <div class="footer-qr-title">SỬ DỤNG SỔ TIÊM CHỦNG ĐIỆN TỬ</div>
                        <div class="footer-qr-img-wrapper">
                            <!-- Clean SVG QR Code Representation -->
                            <svg viewBox="0 0 100 100" width="140" height="140" fill="#0f172a" xmlns="http://www.w3.org/2000/svg">
                                <rect width="100" height="100" fill="#ffffff" />
                                <rect x="8" y="8" width="28" height="28" fill="#0f172a" />
                                <rect x="12" y="12" width="20" height="20" fill="#ffffff" />
                                <rect x="16" y="16" width="12" height="12" fill="#c8102e" />
                                
                                <rect x="64" y="8" width="28" height="28" fill="#0f172a" />
                                <rect x="68" y="12" width="20" height="20" fill="#ffffff" />
                                <rect x="72" y="16" width="12" height="12" fill="#c8102e" />
                                
                                <rect x="8" y="64" width="28" height="28" fill="#0f172a" />
                                <rect x="12" y="68" width="20" height="20" fill="#ffffff" />
                                <rect x="16" y="72" width="12" height="12" fill="#c8102e" />
                                
                                <rect x="42" y="10" width="14" height="6" fill="#0f172a" />
                                <rect x="42" y="20" width="8" height="16" fill="#0f172a" />
                                <rect x="10" y="42" width="18" height="8" fill="#0f172a" />
                                <rect x="34" y="42" width="16" height="16" fill="#b91c1c" />
                                <rect x="56" y="42" width="12" height="8" fill="#0f172a" />
                                <rect x="74" y="42" width="16" height="16" fill="#0f172a" />
                                <rect x="42" y="64" width="8" height="28" fill="#0f172a" />
                                <rect x="56" y="64" width="16" height="8" fill="#0f172a" />
                                <rect x="78" y="64" width="14" height="14" fill="#c8102e" />
                                <rect x="56" y="78" width="16" height="14" fill="#0f172a" />
                            </svg>
                        </div>
                        <div class="footer-qr-subtext">Quét mã để tra cứu lịch tiêm & đăng ký tiêm chủng online</div>
                    </div>
                </div>
            </div>
        </div>

    </footer>

    <!-- Floating Contact, Cart & Zalo Widget Stack -->
    @php
        $layoutCart = session()->get('cart', []);
        $layoutCartCount = count($layoutCart);
        $layoutTotalPrice = collect($layoutCart)->sum(fn($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1));
    @endphp

    <div class="floating-chat-widget">
        <!-- 1. Nút Giỏ Hàng Tròn (Nằm ở TRÊN CÙNG trong stack nút nổi) -->
        <div class="floating-cart-wrapper" id="floatingCartWrapper">
            <!-- Popup Drawer Chi Tiết Giỏ Hàng (Mở lên khi bấm) -->
            <div class="cart-drawer-popup hidden" id="cartDrawerPopup">
                <div class="cart-drawer-header">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="shopping-bag" style="width: 18px; height: 18px; color: var(--primary-color, #c8102e);"></i>
                        <strong style="font-size: 14.5px; color: #0f172a;">Danh Sách Tiêm Đã Chọn</strong>
                    </div>
                    <button type="button" class="cart-drawer-close" onclick="toggleCartDrawerPopup(event)">
                        <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                    </button>
                </div>
                <div class="cart-drawer-body" id="cartItemsList">
                    @if(empty($layoutCart))
                        <div style="text-align: center; padding: 24px 12px; color: #94a3b8; font-size: 13.5px;">
                            <i data-lucide="shopping-cart" style="width: 32px; height: 32px; margin-bottom: 8px; opacity: 0.5;"></i>
                            <p style="margin: 0;">Chưa có vắc xin nào trong danh sách tiêm</p>
                        </div>
                    @else
                        @foreach($layoutCart as $id => $item)
                            <div class="cart-item-row" data-id="{{ $id }}">
                                <div class="cart-item-info">
                                    <strong class="cart-item-name">{{ $item['name'] }}</strong>
                                    <span class="cart-item-price">{{ number_format($item['price'], 0, ',', '.') }} đ</span>
                                </div>
                                <button type="button" onclick="toggleCart({{ $id }})" class="cart-item-remove" title="Xóa vắc xin">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                </button>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="cart-drawer-footer">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="font-size: 13px; color: #64748b;">Tổng tiền niêm yết:</span>
                        <strong id="drawerTotalPrice" style="font-size: 17px; color: var(--primary-color, #c8102e); font-weight: 800;">{{ number_format($layoutTotalPrice, 0, ',', '.') }} đ</strong>
                    </div>
                    <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="btn-checkout-drawer">
                        <i data-lucide="calendar-check" style="width: 16px; height: 16px;"></i>
                        <span>Đăng ký tiêm ngay</span>
                    </a>
                </div>
            </div>

            <!-- Nút Giỏ Hàng Tròn (Đồng bộ kích thước 48px với Zalo/Hotline, có huy hiệu số lượng) -->
            <button type="button" class="floating-btn-expandable floating-cart-trigger {{ empty($layoutCart) ? 'hidden' : '' }}" id="floatingCartBtn" onclick="toggleCartDrawerPopup(event)" style="background: linear-gradient(135deg, #c8102e 0%, #a00d24 100%); box-shadow: 0 8px 24px rgba(200, 16, 46, 0.4);" title="Danh sách vắc xin đã chọn tiêm">
                <div class="btn-icon" style="position: relative;">
                    <i data-lucide="shopping-cart" style="width: 20px; height: 20px;"></i>
                    <span class="cart-badge-count" id="cartCount">{{ $layoutCartCount }}</span>
                </div>
                <span class="btn-text">Danh Sách Tiêm</span>
            </button>
        </div>

        <!-- 2. Nút Chat Zalo Bác Sĩ -->
        <a href="https://zalo.me/0938603839" target="_blank" rel="noopener noreferrer" class="floating-btn-expandable" style="background-color: #0068ff; box-shadow: 0 8px 24px rgba(0, 104, 255, 0.35);" title="Chat Zalo Tư Vấn Vắc Xin Tức Thì">
            <div class="btn-icon">
                <div style="width: 26px; height: 26px; background: #ffffff; color: #0068ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 11px;">Zalo</div>
            </div>
            <span class="btn-text">Chat Zalo Bác Sĩ</span>
        </a>

        <!-- 3. Nút Hotline Tư Vấn 24/7 -->
        <a href="tel:0938603839" class="floating-btn-expandable" style="background-color: var(--primary-color, #c8102e); box-shadow: 0 8px 24px rgba(200, 16, 46, 0.35);" title="Gọi Hotline 0938 60 38 39">
            <div class="btn-icon">
                <i data-lucide="phone-call" style="width: 20px; height: 20px;"></i>
            </div>
            <span class="btn-text">0938 60 38 39</span>
        </a>
    </div>

    <!-- SPA Modals & Toast Container -->
    @include('vaccine::partials.modal-detail')
    @include('vaccine::partials.modal-register')
    <div id="toast-container" style="position: fixed; top: 24px; right: 24px; z-index: 9999999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;"></div>

    <!-- JS Custom -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Khởi tạo các Lucide Icons an toàn
        if (typeof lucide !== 'undefined') {
            try {
                lucide.createIcons();
            } catch (e) {
                console.error('Lỗi khởi tạo Lucide icons:', e);
            }
        }

        // Khởi tạo AOS Scroll Animation an toàn
        if (typeof AOS !== 'undefined') {
            try {
                AOS.init({
                    once: true,
                    offset: 50,
                    easing: 'ease-out-cubic'
                });
            } catch (e) {
                console.error('Lỗi khởi tạo AOS:', e);
            }
        }

        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const drawer = document.getElementById('mobile-drawer');
            const overlay = document.getElementById('mobile-menu-overlay');
            drawer.classList.toggle('open');
            overlay.classList.toggle('open');
            document.body.style.overflow = drawer.classList.contains('open') ? 'hidden' : '';
            // Re-init lucide icons for drawer
            setTimeout(() => lucide.createIcons(), 100);
        }

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
