<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hệ thống Quản trị - Medicare Cờ Đỏ')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
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
    <style>
        /* ==========================================================================
           MODERN ADMIN UI DESIGN SYSTEM
           ========================================================================== */
        :root {
            font-size: clamp(14px, 0.15vw + 12.5px, 16.5px);
            
            --primary-color: #c8102e;       /* Medicare Red */
            --primary-hover: #a00d24;
            --secondary-color: #eaaa00;     /* Medicare Gold */
            --accent-color: #004b8f;        /* Medicare Navy */
            --accent-hover: #00386c;
            
            --bg-main: #fff7f7;             /* Warm red-tinted canvas */
            --bg-card: #ffffff;
            --bg-sidebar: #340711;          /* Deep red admin shell */
            --bg-sidebar-active: #5b1020;
            
            --text-primary: #0f172a;        /* Slate 900 */
            --text-muted: #475569;          /* Slate 600 */
            --text-light: #94a3b8;          /* Slate 400 */
            
            --border-color: #e2e8f0;        /* Slate 200 - clean neutral border */
            --border-focus: #004b8f;        /* Medicare Navy for focus border */
            
            --shadow-sm: 0 2px 8px rgba(127, 29, 29, 0.05);
            --shadow-md: 0 12px 32px rgba(127, 29, 29, 0.08);
            --shadow-lg: 0 22px 55px rgba(127, 29, 29, 0.13);
            
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            
            --font-display: 'Plus Jakarta Sans', 'Inter', sans-serif;
            --font-body: 'Roboto', sans-serif;
        }

        body {
            font-family: var(--font-body);
            background:
                radial-gradient(circle at top left, rgba(200, 16, 46, 0.08), transparent 28rem),
                linear-gradient(180deg, #fff7f7 0%, #fff 45%, #fff7f7 100%);
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
        }

        ::selection {
            background: rgba(200, 16, 46, 0.18);
            color: #7f1d1d;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* --- Sidebar Style --- */
        .admin-sidebar {
            width: 260px;
            background:
                linear-gradient(180deg, #3b0712 0%, #24050b 58%, #160307 100%);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 100;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 8px 0 34px rgba(127, 29, 29, 0.22);
        }
        .sidebar-brand {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }
        .sidebar-brand img {
            transition: transform 0.3s ease;
        }
        .sidebar-brand:hover img {
            transform: scale(1.02);
        }
        .sidebar-menu {
            list-style: none;
            padding: 24px 0;
            flex-grow: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(254, 202, 202, 0.45) transparent;
        }
        .sidebar-menu li {
            margin: 4px 16px;
        }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #fecaca;
            text-decoration: none;
            font-family: var(--font-display);
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: var(--radius-sm);
            transition: all 0.25s ease;
        }
        .sidebar-menu li a:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.08);
            transform: translateX(2px);
        }
        .sidebar-menu li.active a {
            color: #ffffff;
            background: linear-gradient(135deg, rgba(200, 16, 46, 0.95), rgba(153, 27, 27, 0.95));
            box-shadow: 0 10px 20px rgba(127, 29, 29, 0.24);
            position: relative;
            font-weight: 700;
        }
        .sidebar-menu li.active a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 15%;
            height: 70%;
            width: 4px;
            background-color: #ffffff;
            border-radius: 2px;
        }
        .sidebar-menu li a i {
            width: 18px;
            height: 18px;
            opacity: 0.8;
            transition: transform 0.25s ease;
        }
        .sidebar-menu li a:hover i {
            transform: translateX(2px);
            opacity: 1;
        }
        .sidebar-footer {
            padding: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }
        .btn-logout-sidebar {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            background-color: rgba(255, 255, 255, 0.08);
            color: #fecaca;
            border: 1px solid rgba(254, 202, 202, 0.22);
            border-radius: var(--radius-sm);
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .btn-logout-sidebar:hover {
            background-color: var(--primary-color);
            color: #ffffff;
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(200, 16, 46, 0.25);
        }

        /* --- Content Layout --- */
        .admin-content {
            margin-left: 260px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .admin-header {
            height: 74px;
            background-color: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(254, 202, 202, 0.75);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 36px;
            position: sticky;
            top: 0;
            z-index: 99;
        }
        .admin-title {
            font-family: var(--font-display);
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.02em;
        }
        .admin-user {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: var(--font-display);
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-muted);
            background: #ffffff;
            padding: 8px 14px;
            border-radius: 30px;
            border: 1px solid #fecaca;
            box-shadow: var(--shadow-sm);
        }
        .admin-user i {
            color: var(--accent-color);
        }
        .admin-body {
            padding: 32px 36px 44px;
            flex-grow: 1;
            max-width: 100%;
            width: 100%;
            box-sizing: border-box;
        }

        /* ================= ================= =================
           MODERN UI COMPONENTS (REUSABLE CLASS SYSTEM)
           ================= ================= ================= */
        
        /* 1. Cards System */
        .card-modern {
            background-color: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-md);
            padding: 26px;
            margin-bottom: 24px;
            transition: box-shadow 0.3s ease, transform 0.3s ease;
        }
        .card-modern:hover {
            box-shadow: var(--shadow-lg);
        }

        /* 2. Stat Cards */
        .stat-card-modern {
            background: #ffffff;
            border-radius: var(--radius-lg);
            padding: 22px;
            border: 1px solid #fee2e2;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .stat-card-modern:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: var(--border-focus);
        }
        .stat-card-icon-wrapper {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }
        .stat-card-modern:hover .stat-card-icon-wrapper {
            transform: scale(1.1);
        }
        .stat-card-title {
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: var(--font-display);
        }
        .stat-card-number {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-primary);
            font-family: var(--font-display);
            line-height: 1;
        }

        /* 3. Modern Table */
        .table-responsive-modern {
            width: 100%;
            overflow-x: auto;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            background: #ffffff;
        }
        .table-modern {
            width: 100%;
            min-width: 850px;
            border-collapse: separate;
            border-spacing: 0;
            text-align: left;
            font-size: 0.875rem;
        }
        .table-modern th {
            background-color: #fff1f2;
            padding: 16px 20px;
            font-weight: 700;
            color: #7f1d1d;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
            font-family: var(--font-display);
        }
        .table-modern td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
            vertical-align: middle;
            transition: background-color 0.2s ease;
        }
        .table-modern tr:last-child td {
            border-bottom: none;
        }
        .table-modern tbody tr:hover td {
            background-color: #fff7f7;
        }

        /* 4. Form Fields */
        .form-group-modern {
            margin-bottom: 20px;
        }
        .form-label-modern {
            display: block;
            margin-bottom: 8px;
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 0.875rem;
            color: #475569;
        }
        .form-control-modern {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e5b8c0;
            border-radius: 10px;
            outline: none;
            font-size: 0.9rem;
            color: var(--text-primary);
            background-color: #ffffff;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }
        .form-control-modern:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.12);
        }

        input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
        select,
        textarea,
        .form-control {
            border-color: #e5b8c0 !important;
            border-radius: 10px !important;
        }

        input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):focus,
        select:focus,
        textarea:focus,
        .form-control:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.12) !important;
        }

        /* 5. Buttons System */
        .btn-modern {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 10px;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 0.875rem;
            border: none;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
        }
        .btn-modern-primary {
            background-color: var(--primary-color);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(200, 16, 46, 0.15);
        }
        .btn-modern-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(200, 16, 46, 0.25);
        }
        .btn-modern-secondary {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .btn-modern-secondary:hover {
            background-color: #fff1f2;
            color: #991b1b;
            border-color: #fecaca;
            transform: translateY(-1px);
        }
        .btn-modern-danger {
            background-color: #fff1f2;
            color: var(--primary-color);
            border: 1px solid #fecdd3;
        }
        .btn-modern-danger:hover {
            background-color: var(--primary-color);
            color: #ffffff;
            border-color: var(--primary-color);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(200, 16, 46, 0.15);
        }
        
        /* Action Small Buttons in Tables */
        .btn-action-sm {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 0.8125rem;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            color: #7f1d1d;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
        }
        .btn-action-sm:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
            color: var(--primary-color);
        }
        .btn-action-sm.btn-action-danger {
            border-color: #fee2e2;
            background-color: #fff5f5;
            color: #c8102e;
        }
        .btn-action-sm.btn-action-danger:hover {
            background-color: #fce8e6;
            border-color: #fca5a5;
            color: #a00d24;
        }

        /* 6. Badge System */
        .badge-modern {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 12px;
            border-radius: 6px;
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 0.8125rem;
            text-transform: none;
            border: none;
            white-space: nowrap;
        }
        .badge-modern-success {
            background-color: #e6f4ea;
            color: #137333;
        }
        .badge-modern-warning {
            background-color: #fef7e0;
            color: #b06000;
        }
        .badge-modern-danger {
            background-color: #fce8e6;
            color: #c8102e;
        }
        .badge-modern-info {
            background-color: #e8f0fe;
            color: #004b8f;
        }

        .admin-section-hint {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            margin-bottom: 18px;
            color: #7f1d1d;
            background: #fff1f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            font-size: 0.85rem;
            line-height: 1.5;
        }

        /* Modern Image Upload Zone */
        .image-upload-zone {
            border: 2px dashed var(--border-color);
            border-radius: var(--radius-md);
            padding: 24px;
            text-align: center;
            background-color: #f8fafc;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }
        .image-upload-zone:hover {
            border-color: var(--accent-color);
            background-color: #f1f5f9;
        }
        .image-upload-preview-container {
            display: none;
            text-align: center;
        }
        .image-upload-preview-wrapper {
            position: relative;
            display: inline-block;
            margin-top: 10px;
        }
        .image-upload-preview {
            max-height: 150px;
            border-radius: var(--radius-sm);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            display: block;
        }
        .image-upload-remove-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
            transition: all 0.2s ease;
            z-index: 10;
        }
        .image-upload-remove-btn:hover {
            background-color: var(--primary-hover);
            transform: scale(1.1);
        }

        /* Alert styling */
        .alert-dismissible {
            position: relative;
            padding-right: 40px;
        }
        .alert-fade-out {
            opacity: 0 !important;
            max-height: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            border-width: 0 !important;
            overflow: hidden;
        }

        /* Form Grid System */
        .form-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }
        @media (max-width: 768px) {
            .form-grid-2 {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }

        /* Mobile Responsive adjustments */
        .mobile-sidebar-toggle {
            display: none;
            background: #ffffff;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }
        .mobile-sidebar-toggle:hover {
            background-color: #f8fafc;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(6, 19, 37, 0.4);
            backdrop-filter: blur(4px);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .sidebar-backdrop.show {
            display: block;
            opacity: 1;
        }

        @media (max-width: 1023px) {
            .mobile-sidebar-toggle {
                display: inline-flex;
            }
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 1000;
            }
            .admin-sidebar.sidebar-open {
                transform: translateX(0);
            }
            .admin-content {
                margin-left: 0 !important;
                width: 100%;
            }
            .admin-header {
                padding: 0 20px;
            }
            .admin-body {
                padding: 24px 20px;
            }
        }

        @media (max-width: 639px) {
            .admin-header {
                padding: 0 12px;
                height: 60px;
            }
            .admin-title {
                font-size: 16px;
            }
            .admin-user span {
                display: none;
            }
            .admin-body {
                padding: 16px 12px;
            }
            .card-modern {
                padding: 16px;
                border-radius: var(--radius-md);
            }
            .btn-modern {
                padding: 10px 16px;
            }
            .form-control-modern {
                padding: 10px 12px;
            }
            .table-modern th,
            .table-modern td {
                padding: 10px 12px;
            }
        }
    </style>
    <!-- Dark Mode Check -->
    <script>
        document.documentElement.classList.remove('dark');
    </script>
    @yield('styles')
</head>
<body>
    <div id="sidebarBackdrop" class="sidebar-backdrop"></div>
    <div class="admin-wrapper">
        <!-- Sidebar quản trị -->
        <aside class="admin-sidebar">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand" style="display: flex; align-items: center; justify-content: center; padding: 24px 20px;">
                <img src="{{ asset('images/logo.png') }}" alt="Medicare Logo" style="max-height: 38px; width: auto; object-fit: contain;">
            </a>
            
            <ul class="sidebar-menu">
                <li class="{{ Route::currentRouteName() === 'admin.dashboard' ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i data-lucide="layout-dashboard"></i> Dashboard
                    </a>
                </li>
                <li class="{{ Route::currentRouteName() === 'admin.live-editor' ? 'active' : '' }}">
                    <a href="{{ route('admin.live-editor') }}">
                        <i data-lucide="layout-template"></i> Chỉnh Sửa Trực Quan (Live)
                    </a>
                </li>
                <li class="{{ str_contains(Route::currentRouteName(), 'admin.vaccines') ? 'active' : '' }}">
                    <a href="{{ route('admin.vaccines.index') }}">
                        <i data-lucide="syringe"></i> Quản lý Vắc Xin
                    </a>
                </li>
                <li class="{{ str_contains(Route::currentRouteName(), 'admin.registrations') ? 'active' : '' }}">
                    <a href="{{ route('admin.registrations.index') }}">
                        <i data-lucide="clipboard-list"></i> Đơn Đăng Ký
                    </a>
                </li>
                <li class="{{ Route::currentRouteName() === 'admin.schedule' ? 'active' : '' }}">
                    <a href="{{ route('admin.schedule') }}">
                        <i data-lucide="calendar"></i> Lịch Hẹn Tuần
                    </a>
                </li>
                <li class="{{ str_contains(Route::currentRouteName(), 'admin.centers') ? 'active' : '' }}">
                    <a href="{{ route('admin.centers.index') }}">
                        <i data-lucide="map-pinned"></i> Quản lý Trung Tâm
                    </a>
                </li>
                <li class="{{ str_contains(Route::currentRouteName(), 'admin.banners') ? 'active' : '' }}">
                    <a href="{{ route('admin.banners.index') }}">
                        <i data-lucide="image"></i> Quản lý Banner
                    </a>
                </li>
                <li class="{{ str_contains(Route::currentRouteName(), 'admin.articles') ? 'active' : '' }}">
                    <a href="{{ route('admin.articles.index') }}">
                        <i data-lucide="newspaper"></i> Quản lý Bài Viết
                    </a>
                </li>
                <li class="{{ str_contains(Route::currentRouteName(), 'admin.settings') ? 'active' : '' }}">
                    <a href="{{ route('admin.settings.index') }}">
                        <i data-lucide="settings"></i> Cấu Hình Website
                    </a>
                </li>
                <li style="margin-top: 30px; border-top: 1px dashed #1b2e4c; padding-top: 10px;">
                    <a href="{{ route('home') }}">
                        <i data-lucide="arrow-left-right"></i> Xem Trang Khách
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <form action="{{ route('admin.logout') }}" method="POST" id="logoutForm">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar">
                        <i data-lucide="log-out"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </aside>

        <!-- Khung nội dung bên phải -->
        <div class="admin-content">
            <header class="admin-header">
                <div style="display: flex; align-items: center;">
                    <button id="sidebarToggle" class="mobile-sidebar-toggle" aria-label="Toggle navigation">
                        <i data-lucide="menu"></i>
                    </button>
                    <div class="admin-title">@yield('page_title', 'Bảng Điều Khiển')</div>
                </div>
                <div class="admin-user" style="display: flex; align-items: center; gap: 15px;">
                    <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle dark mode" style="display: none !important; background: none; border: 1px solid var(--border-color); color: var(--text-primary); cursor: pointer; padding: 6px; border-radius: 6px; align-items: center; justify-content: center; width: 32px; height: 32px; transition: all 0.2s;">
                        <i data-lucide="sun" class="sun-icon" style="width: 16px; height: 16px; display: none; color: #eaaa00;"></i>
                        <i data-lucide="moon" class="moon-icon" style="width: 16px; height: 16px;"></i>
                    </button>
                    <i data-lucide="circle-user"></i>
                    <span>Xin chào, Admin</span>
                </div>
            </header>
            
            <div class="admin-body">
                @if(session('success'))
                    <div class="alert alert-success" style="margin-bottom: 24px; padding: 16px; border-radius: 8px; background-color: #def7ec; color: #03543f; border: 1px solid #bcf0da; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="check-circle-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger" style="margin-bottom: 24px; padding: 16px; border-radius: 8px; background-color: #fde8e8; color: #9b1c1c; border: 1px solid #fbd5d5; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="alert-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('admin_content')
            </div>
        </div>
    </div>

    <!-- JS Custom -->
    <script>
        // Khởi tạo các Lucide Icons
        lucide.createIcons();

        // Mobile Sidebar Toggle Logic
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const adminSidebar = document.querySelector('.admin-sidebar');

        function toggleSidebar() {
            if (!adminSidebar) return;
            const isOpen = adminSidebar.classList.contains('sidebar-open');
            if (isOpen) {
                adminSidebar.classList.remove('sidebar-open');
                if (sidebarBackdrop) sidebarBackdrop.classList.remove('show');
            } else {
                adminSidebar.classList.add('sidebar-open');
                if (sidebarBackdrop) sidebarBackdrop.classList.add('show');
            }
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }
        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', toggleSidebar);
        }

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

        // Tự động ẩn các thông báo Alert sau 3 giây
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.transition = 'all 0.5s ease-out';
            alert.style.maxHeight = alert.scrollHeight + 'px';
            alert.style.overflow = 'hidden';
            
            setTimeout(() => {
                alert.classList.add('alert-fade-out');
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 3000);
        });
    </script>
    @yield('scripts')
</body>
</html>
