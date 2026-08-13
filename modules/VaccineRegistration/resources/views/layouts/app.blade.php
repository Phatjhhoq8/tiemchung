@php
    $globalSettings = \Modules\VaccineRegistration\Models\Setting::values([
        'site_name' => 'Medicare',
        'hotline' => '0938 60 38 39',
        'email' => 'cskh@medicare.vn',
        'footer_text' => '© 2026 Medicare - Hệ Thống Tiêm Chủng Vắc Xin Trẻ Em và Người Lớn.',
        'footer_company_name' => 'CÔNG TY CỔ PHẦN VẮC XIN MEDICARE',
        'footer_sub_title' => 'HỆ THỐNG TRUNG TÂM TIÊM CHỦNG VẮC XIN CHO TRẺ EM & NGƯỜI LỚN AN TOÀN – UY TÍN – CHẤT LƯỢNG HÀNG ĐẦU VIỆT NAM',
        'footer_content_manager' => 'Chịu trách nhiệm nội dung: Ban Giám Đốc HỆ THỐNG TIÊM CHỦNG MEDICARE',
        'footer_working_hours' => 'Mở cửa 7:30 – 17:00 (không nghỉ trưa)',
        'footer_info_lines' => '[{"icon":"shield-check","text":"Giấy chứng nhận ĐKKD số 0107631488 do Sở KH&ĐT TP. Cần Thơ cấp ngày 11/11/2016"},{"icon":"building","text":"Trụ sở: Cổng Bệnh viện Quân Dân Y TP Cần Thơ, Ấp Thới Thuận, Xã Cờ Đỏ, TP. Cần Thơ"},{"icon":"mail","text":"Email liên hệ: cskh@medicare.vn"}]',
    ]);
    $site_name = $globalSettings['site_name'];
    $hotline = $globalSettings['hotline'];
    $email = $globalSettings['email'];
    $footer_text = $globalSettings['footer_text'];
    $footer_company_name = $globalSettings['footer_company_name'];
    $footer_sub_title = $globalSettings['footer_sub_title'];
    $footer_content_manager = $globalSettings['footer_content_manager'];
    $footer_working_hours = $globalSettings['footer_working_hours'];
    $footer_info_lines = json_decode($globalSettings['footer_info_lines'], true) ?: [];
    $currentCenter = $currentCenter ?? \Modules\VaccineRegistration\Support\CenterContext::current();
    $activeCenters = $activeCenters ?? \Modules\VaccineRegistration\Support\CenterContext::activeCenters();
    if ($currentCenter) {
        $hotline = $currentCenter->phone ?: $hotline;
    }
    $currentCenterPhoneHref = \Modules\VaccineRegistration\Support\CenterContext::phoneHref($hotline);
    $currentCenterZalo = \Modules\VaccineRegistration\Support\CenterContext::phoneHref($currentCenter?->zalo_phone ?: $hotline);
    $appJsVersion = file_exists(public_path('js/app.js')) ? filemtime(public_path('js/app.js')) : '1.0.0';
    $isPreviewMode = $isPreviewMode ?? (request()->query('preview') == '1');
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
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=1.0.4">
    @yield('styles')
    
    <!-- Dark Mode Check & Initial Center Context Injection -->
    <script>
        document.documentElement.classList.remove('dark');
        window.lastCurrentCenter = @json($currentCenter);
        window.lastFetchCenters = @json($activeCenters);
    </script>
    @yield('styles')
</head>
<body class="{{ $isPreviewMode ? 'preview-mode-active' : '' }}">
    @if($isPreviewMode)
        <!-- Sticky Top Preview Warning Banner -->
        <div style="background: #fef08a; border-bottom: 1px solid #fde047; padding: 8px 16px; text-align: center; font-weight: 700; color: #854d0e; font-size: 13.5px; position: sticky; top: 0; z-index: 999999; display: flex; align-items: center; justify-content: center; gap: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <i data-lucide="eye" style="width: 16px; height: 16px; color: #ca8a04;"></i>
                <span>Bạn đang ở chế độ xem thử bản nháp. Quy trình đặt tiêm bị tạm khóa trong chế độ này.</span>
            </div>
            <a href="{{ route('admin.live-editor') }}" style="background-color: var(--primary-color, #c8102e); color: #ffffff; padding: 4px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; transition: background-color 0.2s; border: none; box-shadow: 0 2px 4px rgba(200, 16, 46, 0.15);" onmouseover="this.style.backgroundColor='#a00d24'" onmouseout="this.style.backgroundColor='var(--primary-color, #c8102e)'">
                Quay về Admin
            </a>
        </div>
    @endif
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
                <a href="{{ route('booking.lookup') }}" class="nav-link {{ Route::currentRouteName() === 'booking.lookup' ? 'active' : '' }}">Tra Cứu Lịch Hẹn</a>
            </nav>
            
            <div class="header-actions" style="display: flex; align-items: center; gap: 10px;">
                <style>
                    /* Unified Header Action Pill System (100% Identical Size, Style & Color Scheme) */
                    .header-action-pill {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        gap: 8px;
                        height: 40px;
                        padding: 0 16px;
                        border-radius: 50px;
                        background-color: #ffffff;
                        color: #0f172a !important;
                        border: 1px solid #fecaca;
                        font-size: 13.5px;
                        font-weight: 700;
                        cursor: pointer;
                        white-space: nowrap;
                        text-decoration: none !important;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
                        transition: all 0.25s ease;
                        box-sizing: border-box;
                    }
                    .header-action-pill i,
                    .header-action-pill svg {
                        width: 16px;
                        height: 16px;
                        color: var(--primary-color, #c8102e);
                        flex-shrink: 0;
                        transition: color 0.2s ease;
                    }
                    .header-action-pill .header-cart-badge-inline {
                        background-color: var(--primary-color, #c8102e);
                        color: #ffffff;
                        font-size: 11.5px;
                        font-weight: 800;
                        width: 20px;
                        height: 20px;
                        border-radius: 50%;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        margin-left: 2px;
                        transition: all 0.2s ease;
                    }
                    .header-action-pill:hover {
                        background-color: var(--primary-color, #c8102e);
                        border-color: var(--primary-color, #c8102e);
                        color: #ffffff !important;
                        transform: translateY(-2px);
                        box-shadow: 0 6px 16px rgba(200, 16, 46, 0.3);
                    }
                    .header-action-pill:hover i,
                    .header-action-pill:hover svg {
                        color: #ffffff !important;
                    }
                    .header-action-pill:hover .header-cart-badge-inline {
                        background-color: #ffffff;
                        color: var(--primary-color, #c8102e);
                    }
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
                    @media (max-width: 768px) {
                        .header-actions {
                            gap: 6px !important;
                        }
                        .header-action-pill {
                            padding: 0 10px !important;
                            height: 36px !important;
                            font-size: 12px !important;
                        }
                        .header-action-pill.hide-mobile-text span:not(.header-cart-badge-inline) {
                            display: none !important;
                        }
                        .header-actions .btn-primary-header span {
                            display: none !important;
                        }
                        .header-actions .btn-primary-header {
                            padding: 0 10px !important;
                            height: 36px !important;
                            min-width: 36px !important;
                            border-radius: 50px !important;
                        }
                        .mobile-menu-toggle {
                            flex-shrink: 0 !important;
                        }
                    }
                    /* Hide floating contact buttons when mobile drawer menu is open */
                    body.mobile-drawer-open .floating-chat-widget,
                    .mobile-drawer.open ~ .floating-chat-widget,
                    .mobile-menu-overlay.open ~ .floating-chat-widget {
                        display: none !important;
                        opacity: 0 !important;
                        visibility: hidden !important;
                        pointer-events: none !important;
                    }
                    .mobile-drawer-branch-card {
                        width: 100%;
                        border: 1px solid #e2e8f0;
                        background: #ffffff;
                        text-align: left;
                        padding: 10px 12px;
                        border-radius: 10px;
                        cursor: pointer;
                        display: flex;
                        gap: 10px;
                        align-items: flex-start;
                        transition: all 0.2s ease;
                        text-decoration: none;
                        box-sizing: border-box;
                    }
                    .mobile-drawer-branch-card:hover {
                        background-color: rgba(200, 16, 46, 0.04);
                        border-color: rgba(200, 16, 46, 0.4);
                    }
                    .mobile-drawer-branch-card.active {
                        border-color: var(--primary-color, #c8102e);
                        background-color: rgba(200, 16, 46, 0.06);
                    }

                    /* Mobile Branch Modal Styles - Inline for Safe Render & Cache Prevention */
                    .mobile-branch-modal-overlay {
                        position: fixed;
                        inset: 0;
                        background: rgba(15, 23, 42, 0.6);
                        z-index: 99998;
                        opacity: 0;
                        visibility: hidden;
                        transition: opacity 0.25s ease, visibility 0.25s ease;
                    }
                    .mobile-branch-modal-overlay.open {
                        opacity: 1;
                        visibility: visible;
                    }
                    .mobile-branch-modal {
                        position: fixed;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -45%) scale(0.95);
                        width: min(92vw, 420px);
                        max-height: 85vh;
                        background: #ffffff;
                        border-radius: 20px;
                        z-index: 99999;
                        opacity: 0;
                        visibility: hidden;
                        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease, visibility 0.25s ease;
                        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.22);
                        display: flex;
                        flex-direction: column;
                        overflow: hidden;
                    }
                    .mobile-branch-modal.open {
                        opacity: 1;
                        visibility: visible;
                        transform: translate(-50%, -50%) scale(1);
                    }
                    .mobile-branch-modal-header {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 16px 20px;
                        border-bottom: 1px solid #e2e8f0;
                        background: #f8fafc;
                    }
                    .mobile-branch-modal-header h3 {
                        margin: 0;
                        font-size: 15px;
                        font-weight: 800;
                        color: #0f172a;
                        display: flex;
                        align-items: center;
                        gap: 8px;
                    }
                    .mobile-branch-modal-header h3 i,
                    .mobile-branch-modal-header h3 svg {
                        width: 18px;
                        height: 18px;
                        color: var(--primary-color, #c8102e);
                    }
                    .mobile-branch-modal-header .modal-close-btn {
                        border: none;
                        background: #e2e8f0;
                        width: 30px;
                        height: 30px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        cursor: pointer;
                    }
                    .mobile-branch-modal-body {
                        padding: 16px;
                        overflow-y: auto;
                        display: flex;
                        flex-direction: column;
                        gap: 10px;
                        max-height: 65vh;
                    }
                    .modal-branch-option {
                        width: 100%;
                        border: 1px solid #e2e8f0;
                        background: #ffffff;
                        border-radius: 14px;
                        padding: 12px 14px;
                        display: flex;
                        align-items: flex-start;
                        gap: 12px;
                        text-align: left;
                        cursor: pointer;
                        transition: all 0.2s ease;
                        position: relative;
                        box-sizing: border-box;
                    }
                    .modal-branch-option:hover {
                        border-color: rgba(200, 16, 46, 0.4);
                        background: #fff7f7;
                    }
                    .modal-branch-option.active {
                        border-color: var(--primary-color, #c8102e);
                        background: rgba(200, 16, 46, 0.05);
                    }
                    .modal-branch-option .branch-option-icon {
                        width: 32px;
                        height: 32px;
                        border-radius: 50%;
                        background: #f1f5f9;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        flex-shrink: 0;
                        color: #64748b;
                        margin-top: 2px;
                    }
                    .modal-branch-option.active .branch-option-icon {
                        background: rgba(200, 16, 46, 0.12);
                        color: var(--primary-color, #c8102e);
                    }
                    .modal-branch-option .branch-option-content {
                        display: flex;
                        flex-direction: column;
                        gap: 2px;
                        flex: 1;
                    }
                    .modal-branch-option .branch-option-content strong {
                        font-size: 13px;
                        font-weight: 700;
                        color: #0f172a;
                    }
                    .modal-branch-option.active .branch-option-content strong {
                        color: var(--primary-color, #c8102e);
                    }
                    .modal-branch-option .branch-option-content small {
                        font-size: 11.5px;
                        color: #64748b;
                        line-height: 1.35;
                    }
                    .modal-branch-option .active-badge {
                        font-size: 10px;
                        font-weight: 800;
                        color: var(--primary-color, #c8102e);
                        background: rgba(200, 16, 46, 0.1);
                        padding: 3px 8px;
                        border-radius: 12px;
                        white-space: nowrap;
                        align-self: flex-start;
                    }
                </style>

                <!-- 1. Nút Chọn Chi Nhánh (Pill Standard) -->
                <div class="header-branch-wrapper" style="position: relative;">
                    <button type="button" class="header-action-pill" id="headerBranchButton" onclick="toggleBranchDropdown(event)" title="Đổi chi nhánh tiêm chủng">
                        <i data-lucide="map-pin"></i>
                        <span id="headerBranchText">{{ $currentCenter?->name ?? 'Chi nhánh' }}</span>
                        <i data-lucide="chevron-down" style="width: 14px; height: 14px; margin-left: 2px;"></i>
                    </button>

                    <div id="branchDropdown" class="hidden" style="position: absolute; left: 50%; transform: translateX(-50%); top: calc(100% + 10px); width: min(360px, 90vw); background: #fff; border: 1px solid #fecaca; box-shadow: 0 18px 45px rgba(127,29,29,.18); border-radius: 14px; padding: 10px; z-index: 9999;">
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
                        <div style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #e2e8f0; text-align: center;">
                            <a href="{{ route('contact') }}" style="display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 13px; font-weight: 700; color: var(--primary-color, #c8102e); text-decoration: none; padding: 8px 10px; border-radius: 8px; background: rgba(200,16,46,0.04); transition: all 0.2s ease;">
                                <i data-lucide="map-pin" style="width: 15px; height: 15px;"></i>
                                <span>Xem địa chỉ & bản đồ tất cả chi nhánh</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 2. Nút Hotline Gọi Điện Trực Tiếp (Pill Standard) -->
                <a href="tel:{{ preg_replace('/[^0-9]/', '', $hotline) }}" class="header-action-pill hide-mobile-text" title="Gọi tư vấn tiêm chủng">
                    <i data-lucide="phone-call"></i>
                    <span>{{ $hotline }}</span>
                </a>

                <!-- 3. Nút Giỏ Hàng (Pill Standard) -->
                @php
                    $layoutCartState = \Modules\VaccineRegistration\Support\CenterContext::resolveCart($currentCenter?->id);
                    $layoutCart = $layoutCartState['cart'];
                    $layoutCartCount = count($layoutCart);
                    $layoutTotalPrice = $layoutCartState['total_price'];
                @endphp
                <div class="header-cart-wrapper" id="headerCartWrapper">
                    <button type="button" class="header-action-pill hide-mobile-text" id="headerCartBtn" onclick="toggleHeaderCartDropdown(event)" title="Danh sách vắc xin đã chọn tiêm">
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
                            <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="btn-checkout-drawer">
                                <i data-lucide="calendar-check" style="width: 16px; height: 16px;"></i>
                                <span>Đặt lịch ngay</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 3. Nút Đặt Lịch (Màu đỏ Medicare Red) -->
                <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="btn-primary-header">
                    <i data-lucide="calendar-plus" style="width: 16px; height: 16px;"></i>
                    <span>Đặt lịch</span>
                </a>
                <!-- Mobile Hamburger Button -->
                <button class="mobile-menu-toggle" id="mobile-menu-toggle" onclick="toggleMobileMenu()" aria-label="Mở trình đơn">
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
            <a href="{{ route('home') }}" onclick="toggleMobileMenu()" class="mobile-nav-link {{ Route::currentRouteName() === 'home' ? 'active' : '' }}"><i data-lucide="home" class="w-5 h-5"></i> Trang Chủ</a>
            <a href="{{ route('about') }}" onclick="toggleMobileMenu()" class="mobile-nav-link {{ Route::currentRouteName() === 'about' ? 'active' : '' }}"><i data-lucide="info" class="w-5 h-5"></i> Giới Thiệu</a>
            <a href="{{ route('vaccine.index') }}" onclick="toggleMobileMenu()" class="mobile-nav-link {{ Route::currentRouteName() === 'vaccine.index' ? 'active' : '' }}"><i data-lucide="syringe" class="w-5 h-5"></i> Danh Mục Sản Phẩm</a>
            <a href="{{ route('news.index') }}" onclick="toggleMobileMenu()" class="mobile-nav-link {{ str_contains(Route::currentRouteName(), 'news') ? 'active' : '' }}"><i data-lucide="newspaper" class="w-5 h-5"></i> Tin Tức</a>
            <a href="{{ route('booking.lookup') }}" onclick="toggleMobileMenu()" class="mobile-nav-link {{ Route::currentRouteName() === 'booking.lookup' ? 'active' : '' }}"><i data-lucide="search" class="w-5 h-5"></i> Tra Cứu Lịch Hẹn</a>
            <a href="{{ route('contact') }}" onclick="toggleMobileMenu()" class="mobile-nav-link {{ Route::currentRouteName() === 'contact' ? 'active' : '' }}"><i data-lucide="map-pin" class="w-5 h-5"></i> Liên Hệ</a>
        </div>
        <div class="mobile-drawer-branches" style="padding: 14px 20px; border-top: 1px solid var(--border-color);">
            <div style="font-size: 13px; font-weight: 800; color: #0f172a; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                <i data-lucide="map-pin" style="width: 15px; height: 15px; color: var(--primary-color, #c8102e);"></i>
                <span>Chi nhánh tiêm chủng</span>
            </div>

            <!-- Trigger Button to Open Popup Modal -->
            <button type="button" class="mobile-drawer-branch-card active" onclick="openBranchModal()" style="width: 100%; justify-content: space-between; align-items: center; cursor: pointer;">
                <div style="display: flex; gap: 10px; align-items: center;">
                    <i data-lucide="check-circle" style="width: 16px; height: 16px; color: var(--primary-color, #c8102e); flex-shrink: 0;"></i>
                    <span style="display: flex; flex-direction: column; text-align: left; gap: 1px;">
                        <strong style="font-size: 12.5px; color: var(--primary-color, #c8102e); font-weight: 700;">{{ $currentCenter?->name ?? 'Medicare Cờ Đỏ' }} - {{ $hotline }}</strong>
                        <small style="font-size: 11px; color: #64748b;">Chạm để đổi chi nhánh khác</small>
                    </span>
                </div>
                <span style="font-size: 11px; font-weight: 800; color: var(--primary-color, #c8102e); background: rgba(200, 16, 46, 0.08); padding: 4px 8px; border-radius: 20px; white-space: nowrap;">Đổi ></span>
            </button>
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

    <!-- Mobile Branch Selection Popup Modal -->
    <div class="mobile-branch-modal-overlay" id="mobileBranchModalOverlay" onclick="closeBranchModal()"></div>
    <div class="mobile-branch-modal" id="mobileBranchModal">
        <div class="mobile-branch-modal-header">
            <h3>
                <i data-lucide="map-pin"></i>
                Chọn chi nhánh Medicare
            </h3>
            <button type="button" onclick="closeBranchModal()" class="modal-close-btn">
                <i data-lucide="x"></i>
            </button>
        </div>
        <div class="mobile-branch-modal-body">
            @foreach($activeCenters as $center)
                <form method="POST" action="{{ route('centers.select') }}" style="margin: 0;">
                    @csrf
                    <input type="hidden" name="center_id" value="{{ $center->id }}">
                    <button type="submit" class="modal-branch-option {{ $currentCenter?->id === $center->id ? 'active' : '' }}">
                        <div class="branch-option-icon">
                            <i data-lucide="{{ $currentCenter?->id === $center->id ? 'check-circle-2' : 'building-2' }}"></i>
                        </div>
                        <div class="branch-option-content">
                            <strong>{{ $center->name }} - {{ $center->phone }}</strong>
                            <small>{{ $center->address }}</small>
                        </div>
                        @if($currentCenter?->id === $center->id)
                            <span class="active-badge">Đang chọn</span>
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
    </div>


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
                    <h3>{{ $footer_sub_title }}</h3>
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
                        <span>{{ $footer_working_hours }}</span>
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
                        @php
                            $totalCenters = $activeCenters->count();
                            $displayCenters = $totalCenters > 4 ? $activeCenters->take(3) : $activeCenters->take(4);
                        @endphp

                        @foreach($displayCenters as $center)
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

                        @if($totalCenters > 4)
                        <a href="{{ route('contact') }}" class="footer-branch-item footer-branch-more-card" style="text-decoration: none;">
                            <h4>
                                HỆ THỐNG TẤT CẢ CHI NHÁNH
                            </h4>
                            <div class="footer-branch-info">
                                <p><i data-lucide="map-pin" style="width: 14px; height: 14px; color: var(--secondary-color); flex-shrink: 0; margin-top: 3px;"></i> Tra cứu địa chỉ, bản đồ và hướng dẫn chỉ đường toàn bộ các trung tâm tiêm chủng Medicare.</p>
                                <div class="footer-branch-meta">
                                    <span><i data-lucide="phone" style="width: 14px; height: 14px;"></i> Hotline tư vấn toàn hệ thống: <strong>0938 60 38 39</strong></span>
                                    <span><i data-lucide="clock" style="width: 14px; height: 14px;"></i> 7:00 – 17:00 (Tất cả các ngày trong tuần kể cả Thứ 7, Chủ Nhật & Ngày Lễ)</span>
                                </div>
                            </div>
                            <div class="footer-branch-item-actions">
                                <span class="footer-branch-link-map">
                                    <i data-lucide="navigation" style="width: 13px; height: 13px;"></i> Tra cứu bản đồ
                                </span>
                                <span class="footer-branch-link-book">Xem tất cả chi nhánh →</span>
                            </div>
                        </a>
                        @endif
                    </div>
                </div>

                <div class="footer-right-col">
                    <div class="footer-policy-links">
                        <a href="{{ route('about') }}">Giới thiệu</a>
                        <span>•</span>
                        <a href="{{ route('vaccine.index') }}">Danh mục sản phẩm</a>
                        <span>•</span>
                        <a href="{{ route('news.index') }}">Tin tức</a>
                        <span>•</span>
                        <a href="{{ route('booking.lookup') }}">Tra cứu lịch hẹn</a>
                    </div>
                    <div class="footer-legal-panel">
                        <div class="footer-company-details">
                            <h3>{{ $footer_company_name }}</h3>
                            @foreach($footer_info_lines as $line)
                            @php
                                $lineText = $line['text'] ?? '';
                                $lineIcon = $line['icon'] ?? 'shield-check';
                                
                                // 1. Escape HTML thô trước để chống người dùng nhập mã HTML trực tiếp
                                $lineText = e($lineText);
                                
                                // 2. Tự động in đậm mã số ĐKKD (chuỗi số liên tiếp gồm 9 đến 11 chữ số)
                                $lineText = preg_replace(
                                    '/\b\d{9,11}\b/',
                                    '<strong>$0</strong>',
                                    $lineText
                                );
                                
                                // 3. Tự động tìm email và tạo link mailto
                                if ($lineIcon === 'mail') {
                                    $lineText = preg_replace(
                                        '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/',
                                        '<a href="mailto:$0" style="color: #ffffff; text-decoration: underline;">$0</a>',
                                        $lineText
                                    );
                                }
                            @endphp
                            <div class="footer-legal-item">
                                <i data-lucide="{{ $lineIcon }}" style="width: 16px; height: 16px; color: var(--secondary-color); flex-shrink: 0; margin-top: 2px;"></i>
                                <span>{!! $lineText !!}</span>
                            </div>
                            @endforeach
                            <div class="footer-legal-item">
                                <i data-lucide="phone-call" style="width: 16px; height: 16px; color: var(--secondary-color); flex-shrink: 0; margin-top: 2px;"></i>
                                <span>Tổng đài Hotline: <a href="tel:{{ preg_replace('/\D+/', '', $hotline) }}" style="color: var(--secondary-color); font-weight: 700; text-decoration: none;">{{ $hotline }}</a></span>
                            </div>
                            <div style="margin-top: 14px; padding-top: 12px; border-top: 1px dashed rgba(255,255,255,0.12); font-size: 0.8rem; color: #94a3b8; line-height: 1.5;">
                                {{ $footer_content_manager }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-copyright">{{ $footer_text }}</div>

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
    @include('vaccine::partials.modal-register')
    @include('vaccine::partials.app-dialog')
    <div id="toast-container" style="position: fixed; top: 24px; right: 24px; z-index: 9999999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;"></div>

    <!-- JS Custom -->
    <script src="{{ asset('js/app.js') }}?v={{ time() }}"></script>
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
            const isOpen = drawer.classList.toggle('open');
            overlay.classList.toggle('open');
            document.body.classList.toggle('mobile-drawer-open', isOpen);
            document.body.style.overflow = isOpen ? 'hidden' : '';
            setTimeout(() => lucide.createIcons(), 100);
        }

        // Mobile Branch Accordion Toggle
        function toggleMobileBranchAccordion(event) {
            if (event) event.stopPropagation();
            const list = document.getElementById('mobileBranchAccordionList');
            const icon = document.getElementById('mobileBranchAccordionIcon');
            if (!list) return;
            const isHidden = list.classList.contains('hidden');
            if (isHidden) {
                list.classList.remove('hidden');
                if (icon) icon.style.transform = 'rotate(180deg)';
            } else {
                list.classList.add('hidden');
                if (icon) icon.style.transform = 'rotate(0deg)';
            }
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

        @if($isPreviewMode)
        // Preview Mode: Block all booking and purchase flows
        document.addEventListener('DOMContentLoaded', () => {
            const blockActions = (e) => {
                const target = e.target;
                const isBookingBtn = target.closest('a[href*="/register"], button[type="submit"], form[action*="/register"], button[onclick*="addToCart"], a[onclick*="addToCart"], button[onclick*="postRegister"], form[action*="/consultations"], form[action*="/leads"], button[onclick*="openSpaRegisterModal"], a[href*="/register"]');
                const isCartAction = target.closest('form[action*="/cart/add"], button[onclick*="addToCart"], a[onclick*="addToCart"], .header-action-pill[href*="/register"]');
                
                if (isBookingBtn || isCartAction) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('⚠️ Không thể thực hiện đặt tiêm hoặc gửi yêu cầu trong chế độ xem thử bản nháp!');
                    return false;
                }
            };
            document.addEventListener('click', blockActions, true);
            document.addEventListener('submit', (e) => {
                const form = e.target;
                const action = form.getAttribute('action') || '';
                if (action.includes('/register') || action.includes('/cart/add') || action.includes('/consultations') || action.includes('/leads') || action.includes('/vaccines/disease')) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('⚠️ Không thể gửi yêu cầu đặt tiêm hoặc tư vấn trong chế độ xem thử bản nháp!');
                    return false;
                }
            }, true);
        });
        @endif
    </script>
    @yield('scripts')
</body>
</html>
