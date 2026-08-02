@php
    $settings = \Modules\VaccineRegistration\Models\Setting::values([
        'site_name' => 'Medicare',
        'hotline' => '0938 60 38 39',
        'email' => 'cskh@medicare.vn',
        'address' => 'Chi nhánh 1: Cổng Bệnh viện Quân Dân Y TP Cần Thơ, Ấp Thới Bình, Xã Cờ Đỏ, TP. Cần Thơ',
        'footer_text' => '© 2026 Medicare - Hệ Thống Tiêm Chủng Vắc Xin Trẻ Em và Người Lớn.',
    ]);
    $site_name = $settings['site_name'];
    $hotline = $settings['hotline'];
    $email = $settings['email'];
    $address = $settings['address'];
    $footer_text = $settings['footer_text'];
    $currentCenter = $currentCenter ?? \Modules\VaccineRegistration\Support\CenterContext::current();
    $activeCenters = $activeCenters ?? \Modules\VaccineRegistration\Support\CenterContext::activeCenters();
    if ($currentCenter) {
        $hotline = $currentCenter->phone ?: $hotline;
        $address = $currentCenter->address ?: $address;
    }
    $currentCenterPhoneHref = \Modules\VaccineRegistration\Support\CenterContext::phoneHref($hotline);
    $currentCenterZalo = \Modules\VaccineRegistration\Support\CenterContext::phoneHref($currentCenter?->zalo_phone ?: $hotline);
    $appJsVersion = file_exists(public_path('js/app.js')) ? filemtime(public_path('js/app.js')) : '1.0.0';
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'Medicare - Hệ thống tiêm chủng vắc xin an toàn, chất lượng hàng đầu tại Cần Thơ cho trẻ em và người lớn.')">
    <title>@yield('title', 'Hệ Thống Tiêm Chủng Medicare')</title>
    
    <script>
        window.Laravel = {
            baseUrl: "{{ url('/') }}",
            csrfToken: "{{ csrf_token() }}"
        };
    </script>
    
    <!-- Google Fonts (Roboto + Inter for headings) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@600;700;800;900&family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@0.468.0"></script>
    
    <!-- AOS Scroll Animation -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
    
    <!-- Vite & Flowbite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=1.0.0">
    
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
                    <i data-lucide="map-pin" style="color: var(--secondary-color);"></i> <strong>Đang chọn:</strong> <span id="topbarBranchText">{{ $currentCenter?->name ?? 'Medicare' }} (Hotline: {{ $hotline }})</span>
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
                <a href="{{ route('news.index') }}" class="nav-link {{ str_contains(Route::currentRouteName(), 'news') ? 'active' : '' }}">Tin Tức</a>
                <a href="{{ route('contact') }}" class="nav-link {{ Route::currentRouteName() === 'contact' ? 'active' : '' }}">Liên Hệ</a>
            </nav>
            
            <div class="header-actions" style="display: flex; align-items: center; gap: 12px;">
                <!-- 1. Nút Điện Thoại (Chỉ còn icon và số điện thoại, bỏ chữ 'Tư vấn:') -->
                <div class="header-branch-wrapper" style="position: relative;">
                    <button type="button" class="hotline-btn hotline-btn-desktop" id="headerBranchButton" onclick="toggleBranchDropdown(event)" title="Đổi chi nhánh hiện tại">
                        <i data-lucide="map-pin"></i>
                        <span id="headerBranchText">{{ $currentCenter?->name ?? 'Chi nhánh' }}<span class="hotline-phone-suffix"> - {{ $hotline }}</span></span>
                    </button>
                    <style>
                        .branch-item-btn {
                            width: 100%;
                            border: 0;
                            background: #ffffff;
                            text-align: left;
                            padding: 10px;
                            border-radius: 10px;
                            cursor: pointer;
                            display: flex;
                            gap: 8px;
                            align-items: flex-start;
                            color: #334155;
                            transition: all 0.2s ease;
                        }
                        .branch-item-btn:hover {
                            background-color: rgba(200, 16, 46, 0.05);
                            color: var(--primary-color, #c8102e);
                        }
                        .branch-item-btn:hover strong {
                            color: var(--primary-color, #c8102e) !important;
                        }
                        .branch-item-btn.active {
                            background-color: rgba(200, 16, 46, 0.08);
                            color: var(--primary-color, #c8102e);
                        }
                        .branch-item-btn.active:hover {
                            background-color: rgba(200, 16, 46, 0.12);
                        }
                        .branch-item-btn.active strong {
                            color: var(--primary-color, #c8102e) !important;
                        }
                    </style>
                    <div id="branchDropdown" class="hidden" style="position: absolute; right: 0; top: calc(100% + 10px); width: min(360px, 90vw); background: #fff; border: 1px solid #fecaca; box-shadow: 0 18px 45px rgba(127,29,29,.18); border-radius: 14px; padding: 10px; z-index: 9999;">
                        <div style="font-size: 13px; font-weight: 800; color: #0f172a; padding: 8px 10px 10px;">Chọn chi nhánh Medicare</div>
                        @foreach($activeCenters as $center)
                            <form method="POST" action="{{ route('centers.select') }}" style="margin: 0;">
                                @csrf
                                <input type="hidden" name="center_id" value="{{ $center->id }}">
                                <button type="submit" class="branch-item-btn {{ $currentCenter?->id === $center->id ? 'active' : '' }}">
                                    <i data-lucide="{{ $currentCenter?->id === $center->id ? 'check-circle' : 'map-pin' }}" style="width: 17px; height: 17px; color: var(--primary-color); margin-top: 2px;"></i>
                                    <span style="display: flex; flex-direction: column; gap: 3px;">
                                        <strong style="color: #0f172a;">{{ $center->name }} - {{ $center->phone }}</strong>
                                        <small style="line-height: 1.35;">{{ $center->address }}</small>
                                    </span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>

                <!-- 2. Nút Giỏ Hàng (Thiết kế Pill giống hệt & cùng màu nút Điện Thoại) -->
                @php
                    $layoutCartState = \Modules\VaccineRegistration\Support\CenterContext::resolveCart($currentCenter?->id);
                    $layoutCart = $layoutCartState['cart'];
                    $layoutCartCount = count($layoutCart);
                    $layoutTotalPrice = $layoutCartState['total_price'];
                @endphp
                <div class="header-cart-wrapper" id="headerCartWrapper">
                    <button type="button" class="hotline-btn header-cart-btn-pill" id="headerCartBtn" onclick="toggleHeaderCartDropdown(event)" title="Danh sách vắc xin đã chọn tiêm">
                        <i data-lucide="shopping-cart"></i>
                        <span>Giỏ hàng</span>
                        <span class="header-cart-badge-inline" id="cartCount">{{ $layoutCartCount }}</span>
                    </button>

                    <!-- Header Cart Dropdown Menu (Hạ xuống khi bấm) -->
                    <div class="header-cart-dropdown hidden" id="headerCartDropdown">
                        <div class="cart-drawer-header">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i data-lucide="shopping-bag" style="width: 18px; height: 18px; color: var(--primary-color, #c8102e);"></i>
                                <strong style="font-size: 14.5px; color: #0f172a;">Danh Sách Vắc Xin Đã Chọn</strong>
                            </div>
                            <button type="button" class="cart-drawer-close" onclick="toggleHeaderCartDropdown(event)">
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
                                            <div style="display: flex; gap: 8px; align-items: center; margin-top: 4px;">
                                                <span class="cart-item-price" style="font-weight: 700; color: var(--primary-color); font-size: 13.5px;">{{ number_format($item['price'], 0, ',', '.') }} đ</span>
                                                <span style="font-size: 12px; color: #64748b; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-weight: 600;">SL: {{ $item['quantity'] ?? 1 }}</span>
                                            </div>
                                            @if(!empty($item['unavailable_for_center']))
                                                <div style="margin-top: 6px; color: #b91c1c; font-size: 12px; font-weight: 700;">Sản phẩm này không có ở chi nhánh hiện tại</div>
                                            @endif
                                        </div>
                                        <button type="button" onclick="toggleCart({{ $id }}, true)" class="cart-item-remove" title="Xóa vắc xin">
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
                            <a href="{{ route('register.show') }}" class="btn-checkout-drawer">
                                <i data-lucide="calendar-check" style="width: 16px; height: 16px;"></i>
                                <span>Đặt lịch ngay</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 3. Nút Đặt Lịch (Màu đỏ Medicare Red) -->
                <a href="{{ route('register.show') }}" class="btn-primary-header">
                    <i data-lucide="calendar-plus" style="width: 16px; height: 16px;"></i>
                    <span>Đặt lịch</span>
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
            <a href="{{ route('news.index') }}" class="mobile-nav-link {{ str_contains(Route::currentRouteName(), 'news') ? 'active' : '' }}"><i data-lucide="newspaper" class="w-5 h-5"></i> Tin Tức</a>
            <a href="{{ route('contact') }}" class="mobile-nav-link {{ Route::currentRouteName() === 'contact' ? 'active' : '' }}"><i data-lucide="map-pin" class="w-5 h-5"></i> Liên Hệ</a>
        </div>
        <form method="POST" action="{{ route('centers.select') }}" style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
            @csrf
            <label for="mobile_center_id" style="display:block; font-size:13px; font-weight:700; margin-bottom:8px;">Chi nhánh đang chọn</label>
            <select id="mobile_center_id" name="center_id" onchange="this.form.submit()" style="width:100%; min-height:44px; border-radius:8px; padding:8px;">
                @foreach($activeCenters as $center)
                    <option value="{{ $center->id }}" {{ $currentCenter?->id === $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                @endforeach
            </select>
            <noscript><button type="submit" class="btn-secondary" style="margin-top:8px;">Đổi chi nhánh</button></noscript>
        </form>
        <div class="mobile-drawer-footer">
            <a href="{{ route('register.show') }}" class="mobile-cta-btn">
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
            <style>
                .flash-messages-container {
                    position: fixed;
                    top: 24px;
                    right: 24px;
                    z-index: 9999;
                    width: 380px;
                    max-width: calc(100vw - 48px);
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                    pointer-events: none;
                }
                .flash-messages-container .alert {
                    pointer-events: auto;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
                    backdrop-filter: blur(8px);
                    -webkit-backdrop-filter: blur(8px);
                    animation: toastSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                    transition: all 0.3s ease;
                    border-radius: 12px !important;
                    border: 1px solid transparent;
                }
                @keyframes toastSlideIn {
                    from {
                        transform: translateX(120%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                @keyframes toastFadeOut {
                    from {
                        transform: scale(1);
                        opacity: 1;
                    }
                    to {
                        transform: scale(0.95);
                        opacity: 0;
                    }
                }
            </style>

            <div class="flash-messages-container">
                @if(session('success'))
                    <div class="alert alert-success" style="background-color: rgba(236, 253, 245, 0.95); border-color: #10b981; color: #065f46; padding: 16px; display: flex; align-items: center; gap: 12px; font-weight: 500; position: relative;">
                        <i data-lucide="check-circle" style="color: #10b981; width: 20px; height: 20px; flex-shrink: 0;"></i>
                        <span style="flex-grow: 1;">{{ session('success') }}</span>
                        <button class="close-toast-btn" style="background: none; border: none; color: #065f46; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0;"><i data-lucide="x" style="width: 18px; height: 18px;"></i></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger" style="background-color: rgba(254, 242, 242, 0.95); border-color: #ef4444; color: #991b1b; padding: 16px; display: flex; align-items: center; gap: 12px; font-weight: 500; position: relative;">
                        <i data-lucide="x-circle" style="color: #ef4444; width: 20px; height: 20px; flex-shrink: 0;"></i>
                        <span style="flex-grow: 1;">{{ session('error') }}</span>
                        <button class="close-toast-btn" style="background: none; border: none; color: #991b1b; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0;"><i data-lucide="x" style="width: 18px; height: 18px;"></i></button>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning" style="background-color: rgba(255, 251, 235, 0.95); border-color: #f59e0b; color: #92400e; padding: 16px; display: flex; align-items: center; gap: 12px; font-weight: 500; position: relative;">
                        <i data-lucide="alert-circle" style="color: #f59e0b; width: 20px; height: 20px; flex-shrink: 0;"></i>
                        <span style="flex-grow: 1;">{{ session('warning') }}</span>
                        <button class="close-toast-btn" style="background: none; border: none; color: #92400e; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0;"><i data-lucide="x" style="width: 18px; height: 18px;"></i></button>
                    </div>
                @endif
                @if(session('info'))
                    <div class="alert alert-info" style="background-color: rgba(240, 249, 255, 0.95); border-color: #0ea5e9; color: #0369a1; padding: 16px; display: flex; align-items: center; gap: 12px; font-weight: 500; position: relative;">
                        <i data-lucide="info" style="color: #0ea5e9; width: 20px; height: 20px; flex-shrink: 0;"></i>
                        <span style="flex-grow: 1;">{{ session('info') }}</span>
                        <button class="close-toast-btn" style="background: none; border: none; color: #0369a1; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0;"><i data-lucide="x" style="width: 18px; height: 18px;"></i></button>
                    </div>
                @endif
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const alerts = document.querySelectorAll('.flash-messages-container .alert');
                    alerts.forEach(alert => {
                        // Tự động đóng sau 4 giây
                        const timeoutId = setTimeout(() => {
                            closeToast(alert);
                        }, 4000);

                        // Nút đóng thủ công
                        const closeBtn = alert.querySelector('.close-toast-btn');
                        if (closeBtn) {
                            closeBtn.addEventListener('click', function() {
                                clearTimeout(timeoutId);
                                closeToast(alert);
                            });
                        }
                    });

                    function closeToast(alertEl) {
                        alertEl.style.animation = 'toastFadeOut 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards';
                        setTimeout(() => {
                            alertEl.remove();
                            const container = document.querySelector('.flash-messages-container');
                            if (container && container.querySelectorAll('.alert').length === 0) {
                                container.remove();
                            }
                        }, 300);
                    }
                });
            </script>
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

        <!-- Footer Body: Branch Network & Legal Information -->
        <div class="footer-body-container">
            <!-- Footer Main Layout: Branch Network (Left) + Legal Information (Right) -->
            <div class="footer-main-layout">
                <div class="footer-left-col">
                    <div class="footer-branches-title">
                        HỆ THỐNG CHI NHÁNH TIÊM CHỦNG MEDICARE CẦN THƠ
                    </div>
                    <div class="footer-branch-list">
                        @foreach($activeCenters as $center)
                        <div class="footer-branch-item">
                            <h4>
                                {{ $center->name }}
                                @if($currentCenter?->id === $center->id)
                                    <span class="footer-branch-selected">Đang chọn</span>
                                @endif
                            </h4>
                            <div class="footer-branch-info">
                                <p><i data-lucide="map-pin" style="width: 14px; height: 14px; color: var(--secondary-color); flex-shrink: 0; margin-top: 3px;"></i> {{ $center->address }}</p>
                                <div class="footer-branch-meta">
                                    <span><i data-lucide="phone" style="width: 14px; height: 14px;"></i> Hotline: <strong>{{ $center->phone }}</strong></span>
                                    <span><i data-lucide="clock" style="width: 14px; height: 14px;"></i> {{ $center->working_hours ?: '7:00 – 17:00 (Tất cả các ngày trong tuần kể cả Thứ 7, Chủ Nhật & Ngày Lễ)' }}</span>
                                </div>
                            </div>
                            <div class="footer-branch-item-actions">
                                <a href="{{ route('contact') }}" class="footer-branch-link-map">
                                    <i data-lucide="navigation" style="width: 13px; height: 13px;"></i> Xem bản đồ & chỉ đường
                                </a>
                                <form method="POST" action="{{ route('centers.select') }}" style="margin:0;">
                                    @csrf
                                    <input type="hidden" name="center_id" value="{{ $center->id }}">
                                    <input type="hidden" name="redirect_to" value="register">
                                    <button type="submit" class="footer-branch-link-book" style="border:0; cursor:pointer;">Đặt lịch tại chi nhánh →</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="footer-right-col">
                    <div class="footer-legal-panel">
                        <div class="footer-policy-links">
                            <a href="{{ route('about') }}">Chính sách bảo mật</a>
                            <span>•</span>
                            <a href="{{ route('vaccine.index') }}">Chính sách thanh toán</a>
                            <span>•</span>
                            <a href="{{ route('contact') }}">Điều khoản sử dụng</a>
                        </div>
                        <div class="footer-company-details">
                            <h3>CÔNG TY CỔ PHẦN VẮC XIN MEDICARE</h3>
                            <p>Giấy chứng nhận ĐKKD số 0107631488 do Sở Kế hoạch và Đầu tư TP. Cần Thơ cấp ngày 11/11/2016</p>
                            @foreach($activeCenters as $center)
                                <p><strong>{{ $center->name }}:</strong> {{ $center->address }}</p>
                            @endforeach
                            <p><strong>Email:</strong> {{ $email }} | <strong>Số điện thoại:</strong> {{ $hotline }}</p>
                            <p>Chịu trách nhiệm nội dung: Ban Giám Đốc HỆ THỐNG TIÊM CHỦNG MEDICARE</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-copyright">Bản quyền ©2026 thuộc về CÔNG TY CỔ PHẦN VẮC XIN MEDICARE</div>

    </footer>

    <!-- Floating Contact & Zalo Widget Stack (Bên phải) -->
    <div class="floating-chat-widget">
        <!-- 1. Nút Chat Zalo Bác Sĩ -->
        <a href="https://zalo.me/{{ $currentCenterZalo }}" target="_blank" rel="noopener noreferrer" class="floating-btn-expandable" style="background-color: #0068ff; box-shadow: 0 8px 24px rgba(0, 104, 255, 0.35);" title="Chat Zalo {{ $currentCenter?->name ?? 'Medicare' }}">
            <div class="btn-icon">
                <div style="width: 26px; height: 26px; background: #ffffff; color: #0068ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 11px;">Zalo</div>
            </div>
            <span class="btn-text">Chat Zalo Bác Sĩ</span>
        </a>

        <!-- 2. Nút Hotline Tư Vấn 24/7 -->
        <a href="tel:{{ $currentCenterPhoneHref }}" class="floating-btn-expandable" style="background-color: var(--primary-color, #c8102e); box-shadow: 0 8px 24px rgba(200, 16, 46, 0.35);" title="Gọi Hotline {{ $hotline }}">
            <div class="btn-icon">
                <i data-lucide="phone-call" style="width: 20px; height: 20px;"></i>
            </div>
            <span class="btn-text">{{ $hotline }}</span>
        </a>
    </div>

    <!-- SPA Modals & Toast Container -->
    @include('vaccine::partials.modal-detail')
    <div id="toast-container" style="position: fixed; top: 24px; right: 24px; z-index: 9999999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;"></div>

    <!-- JS Custom -->
    <script src="{{ asset('js/app.js') }}?v={{ $appJsVersion }}"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        function toggleBranchDropdown(event) {
            if (event) event.stopPropagation();
            const dropdown = document.getElementById('branchDropdown');
            if (dropdown) dropdown.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('branchDropdown');
            const wrapper = document.querySelector('.header-branch-wrapper');
            if (dropdown && wrapper && !wrapper.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

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
