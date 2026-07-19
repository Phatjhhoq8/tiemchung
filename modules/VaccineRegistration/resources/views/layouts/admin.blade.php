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
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <style>
        /* CSS nội bộ bổ trợ cho Admin Layout (các class riêng) */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
            background-color: #f4f6f9;
        }
        .admin-sidebar {
            width: 260px;
            background-color: #1e2229;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 24px;
            font-family: 'Roboto', sans-serif;
            font-size: 20px;
            font-weight: 700;
            border-bottom: 1px solid #2d323e;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #ffffff;
            text-decoration: none;
        }
        .sidebar-brand i {
            color: #eaaa00;
        }
        .sidebar-brand span {
            font-weight: 400;
            font-size: 14px;
            color: #a0aec0;
        }
        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
            flex-grow: 1;
        }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: #a0aec0;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .sidebar-menu li a:hover, .sidebar-menu li.active a {
            color: #ffffff;
            background-color: #2d323e;
            border-left: 4px solid #c8102e;
        }
        .sidebar-menu li a i {
            width: 20px;
            height: 20px;
        }
        .sidebar-footer {
            padding: 20px 24px;
            border-top: 1px solid #2d323e;
        }
        .btn-logout-sidebar {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            background-color: #c8102e;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .btn-logout-sidebar:hover {
            background-color: #a00d24;
        }
        .admin-content {
            margin-left: 260px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .admin-header {
            height: 70px;
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            position: sticky;
            top: 0;
            z-index: 99;
        }
        .admin-title {
            font-family: 'Roboto', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
        }
        .admin-user {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            color: #64748b;
        }
        .admin-body {
            padding: 40px;
            flex-grow: 1;
        }
        .alert-dismissible {
            position: relative;
            padding-right: 40px;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar quản trị -->
        <aside class="admin-sidebar">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                <i data-lucide="activity"></i>
                Medicare <span>Admin</span>
            </a>
            
            <ul class="sidebar-menu">
                <li class="{{ Route::currentRouteName() === 'admin.dashboard' ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i data-lucide="layout-dashboard"></i> Dashboard
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
                <li class="{{ str_contains(Route::currentRouteName(), 'admin.centers') ? 'active' : '' }}">
                    <a href="{{ route('admin.centers.index') }}">
                        <i data-lucide="map-pinned"></i> Quản lý Trung Tâm
                    </a>
                </li>
                <li class="{{ str_contains(Route::currentRouteName(), 'admin.settings') ? 'active' : '' }}">
                    <a href="{{ route('admin.settings.index') }}">
                        <i data-lucide="settings"></i> Cấu Hình Website
                    </a>
                </li>
                <li style="margin-top: 30px; border-top: 1px dashed #2d323e; padding-top: 10px;">
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
                <div class="admin-title">@yield('page_title', 'Bảng Điều Khiển')</div>
                <div class="admin-user">
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
    </script>
    @yield('scripts')
</body>
</html>
