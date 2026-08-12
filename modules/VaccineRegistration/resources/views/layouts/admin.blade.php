@php
    $adminUser = $adminUser ?? \Modules\VaccineRegistration\Support\AdminContext::user();
    $isSuperAdmin = $isSuperAdmin ?? ($adminUser?->isSuperAdmin() ?? false);
    $adminCenters = $adminCenters ?? ($isSuperAdmin
        ? \Modules\VaccineRegistration\Models\Center::active()->orderBy('sort_order')->orderBy('id')->get(['id', 'name'])
        : collect());
    $adminSelectedCenterId = $adminSelectedCenterId ?? \Modules\VaccineRegistration\Support\AdminContext::selectedCenterId();

    // Logic gom nhóm route cho thanh điều hướng
    $currentRoute = Route::currentRouteName();
    
    // Nhóm 1: Tiêm chủng & Lịch trình
    $groupVaccine = [
        'admin.vaccines.index', 'admin.vaccines.create', 'admin.vaccines.edit',
        'admin.schedules.index', 'admin.default-slots.index',
        'admin.schedule'
    ];
    
    // Nhóm 2: Đăng ký & Khách hàng
    $groupCustomer = [
        'admin.registrations.index', 'admin.registrations.show', 'admin.registrations.create',
        'admin.patients.index', 'admin.patients.show',
        'admin.customers.index', 'admin.customers.show'
    ];
    
    // Nhóm 3: Nội dung Website
    $groupWebsite = [
        'admin.articles.index', 'admin.articles.create', 'admin.articles.edit',
        'admin.banners.index', 'admin.banners.create', 'admin.banners.edit',
        'admin.live-editor'
    ];
    
    // Nhóm 4: Hệ thống & Thiết lập
    $groupSystem = [
        'admin.settings.loyalty',
        'admin.centers.index', 'admin.centers.create', 'admin.centers.edit',
        'admin.users.index', 'admin.users.create', 'admin.users.edit',
        'admin.audit-logs.index', 'admin.audit-logs.show'
    ];
    
    $activeGroup = null;
    $subNavigation = [];
    
    if (in_array($currentRoute, $groupVaccine)) {
        $activeGroup = 'vaccine';
        $subNavigation = [
            ['route' => 'admin.vaccines.index', 'label' => 'Quản lý Vắc Xin', 'icon' => 'syringe'],
            ['route' => 'admin.schedules.index', 'label' => 'Khung Giờ Tiêm', 'icon' => 'clock'],
            ['route' => 'admin.schedule', 'label' => 'Lịch Hẹn Tuần', 'icon' => 'calendar'],
        ];
    } elseif (in_array($currentRoute, $groupCustomer)) {
        $activeGroup = 'customer';
        $subNavigation = [
            ['route' => 'admin.registrations.index', 'label' => 'Đơn Đăng Ký', 'icon' => 'clipboard-list'],
            ['route' => 'admin.patients.index', 'label' => 'Hồ Sơ Bệnh Nhân', 'icon' => 'contact'],
            ['route' => 'admin.customers.index', 'label' => 'Khách Hàng & Điểm', 'icon' => 'users'],
        ];
    } elseif (in_array($currentRoute, $groupWebsite) && $isSuperAdmin) {
        $activeGroup = 'website';
        $subNavigation = [
            ['route' => 'admin.articles.index', 'label' => 'Quản lý Bài Viết', 'icon' => 'newspaper'],
            ['route' => 'admin.banners.index', 'label' => 'Quản lý Banner', 'icon' => 'image'],
            ['route' => 'admin.live-editor', 'label' => 'Chỉnh Sửa Trực Quan', 'icon' => 'layout-template'],
        ];
    } elseif (in_array($currentRoute, $groupSystem)) {
        $activeGroup = 'system';
        $subNavigation = [];
        
        $subNavigation[] = ['route' => 'admin.settings.loyalty', 'label' => 'Cấu Hình Tích Điểm', 'icon' => 'coins'];
        if ($isSuperAdmin) {
            $subNavigation[] = ['route' => 'admin.centers.index', 'label' => 'Quản lý Trung Tâm', 'icon' => 'map-pinned'];
            $subNavigation[] = ['route' => 'admin.users.index', 'label' => 'Tài Khoản Chi Nhánh', 'icon' => 'users'];
            $subNavigation[] = ['route' => 'admin.audit-logs.index', 'label' => 'Nhật Ký Hệ Thống', 'icon' => 'scroll-text'];
        }
    }
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hệ thống Quản trị - Medicare')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Cropper.js (Image Cropper Library) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    
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
            display: table !important;
            width: 100% !important;
            min-width: 950px !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            text-align: left;
            font-size: 0.875rem;
        }
        .table-modern thead {
            display: table-header-group !important;
        }
        .table-modern tbody {
            display: table-row-group !important;
        }
        .table-modern tr {
            display: table-row !important;
        }
        .table-modern th,
        .table-modern td {
            display: table-cell !important;
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
        input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
        select,
        textarea,
        .form-control,
        .form-control-modern {
            border-color: #cbd5e1 !important;
            border-radius: 8px !important;
        }

        input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):focus,
        select:focus,
        textarea:focus,
        .form-control:focus,
        .form-control-modern:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.12) !important;
        }

        /* Datepicker & Calendar Customization - Medicare Red Theme */
        input[type="date"] {
            accent-color: #c8102e !important;
            color-scheme: light;
            font-family: inherit;
            cursor: pointer;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            padding: 4px;
            margin-right: -2px;
            border-radius: 6px;
            filter: invert(18%) sepia(85%) saturate(5451%) hue-rotate(345deg) brightness(85%) contrast(98%) !important;
            transition: background-color 0.2s ease, transform 0.15s ease;
        }

        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            background-color: #fee2e2 !important;
            transform: scale(1.1);
        }

        /* Standardized Minimalist Select Controls Across Admin */
        select,
        select.form-control-modern {
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            padding-right: 36px !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 12px center !important;
            background-size: 14px 14px !important;
            cursor: pointer;
        }

        select:focus,
        select.form-control-modern:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.12) !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23c8102e' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='18 15 12 9 6 15'%3E%3C/polyline%3E%3C/svg%3E") !important;
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
        .btn-action-sm.btn-action-success {
            border-color: #dcfce7;
            background-color: #f0fdf4;
            color: #16a34a;
        }
        .btn-action-sm.btn-action-success:hover {
            background-color: #dcfce7;
            border-color: #86efac;
            color: #15803d;
        }
        .btn-action-sm.btn-action-warning {
            border-color: #fef3c7;
            background-color: #fffbeb;
            color: #d97706;
        }
        .btn-action-sm.btn-action-warning:hover {
            background-color: #fef3c7;
            border-color: #fcd34d;
            color: #b45309;
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
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
            }
            .admin-title {
                font-size: 13.5px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 140px;
            }
            .admin-user {
                padding: 4px 8px;
                gap: 6px;
            }
            .admin-user select {
                max-width: 110px !important;
                font-size: 11px;
                padding: 4px 6px;
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

        /* Modern Action Dropdown Styles */
        .action-dropdown-wrapper {
            position: relative;
            display: inline-block;
            text-align: left;
        }
        .btn-action-trigger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s ease;
            outline: none;
            padding: 0;
        }
        .btn-action-trigger:hover {
            background-color: #f1f5f9;
            border-color: #94a3b8;
            color: var(--primary-color);
        }
        .action-dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 6px);
            z-index: 999;
            min-width: 170px;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            padding: 6px;
            display: none;
            animation: dropdownSlideUp 0.15s ease-out;
        }
        .action-dropdown-menu.active {
            display: block;
        }
        .action-dropdown-menu.dropup {
            bottom: calc(100% + 6px) !important;
            top: auto !important;
        }
        @keyframes dropdownSlideUp {
            from {
                opacity: 0;
                transform: translateY(4px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .dropdown-item-action {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 8px 12px;
            font-size: 0.8125rem;
            font-weight: 550;
            color: #334155;
            border: none;
            background: transparent;
            border-radius: 6px;
            text-align: left;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.15s ease;
        }
        .dropdown-item-action:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .dropdown-item-action.danger {
            color: #dc2626;
        }
        .dropdown-item-action.danger:hover {
            background-color: #fef2f2;
            color: #b91c1c;
        }

        /* ==========================================================================
           SUB-NAVIGATION SYSTEM
           ========================================================================== */
        .admin-sub-nav-wrapper {
            background: #ffffff;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            padding: 0 20px;
            margin-bottom: 28px;
            box-shadow: var(--shadow-sm);
        }
        .admin-sub-nav {
            display: flex;
            gap: 28px;
            overflow-x: auto;
            scrollbar-width: none; /* Firefox */
        }
        .admin-sub-nav::-webkit-scrollbar {
            display: none; /* Safari and Chrome */
        }
        .sub-nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 4px;
            color: var(--text-muted);
            text-decoration: none;
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 0.95rem;
            border-bottom: 3px solid transparent;
            transition: all 0.25s ease;
            white-space: nowrap;
        }
        .sub-nav-item:hover {
            color: var(--primary-color);
        }
        .sub-nav-item.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
        .sub-nav-item i {
            width: 18px;
            height: 18px;
            opacity: 0.85;
            transition: transform 0.2s ease;
        }
        .sub-nav-item:hover i {
            transform: translateY(-1px);
            opacity: 1;
        }
        .sub-nav-item.active i {
            opacity: 1;
            color: var(--primary-color);
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
                <img src="{{ asset('images/logo.png') }}" alt="Biểu trưng Medicare" style="max-height: 38px; width: auto; object-fit: contain;">
            </a>
            
            <ul class="sidebar-menu">
                <li class="{{ Route::currentRouteName() === 'admin.dashboard' ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i data-lucide="layout-dashboard"></i> Bảng điều khiển
                    </a>
                </li>
                <li class="{{ $activeGroup === 'vaccine' ? 'active' : '' }}">
                    <a href="{{ route('admin.vaccines.index') }}">
                        <i data-lucide="syringe"></i> Tiêm Chủng & Lịch Trình
                    </a>
                </li>
                <li class="{{ $activeGroup === 'customer' ? 'active' : '' }}">
                    <a href="{{ route('admin.registrations.index') }}">
                        <i data-lucide="clipboard-list"></i> Đăng Ký & Khách Hàng
                    </a>
                </li>
                @if($isSuperAdmin ?? false)
                <li class="{{ $activeGroup === 'website' ? 'active' : '' }}">
                    <a href="{{ route('admin.articles.index') }}">
                        <i data-lucide="newspaper"></i> Nội Dung Website
                    </a>
                </li>
                @endif
                <li class="{{ $activeGroup === 'system' ? 'active' : '' }}">
                    <a href="{{ route('admin.settings.loyalty') }}">
                        <i data-lucide="settings"></i> Hệ Thống & Thiết Lập
                    </a>
                </li>
                <li style="margin-top: 30px; border-top: 1px dashed #1b2e4c; padding-top: 10px;">
                    <a href="{{ route('admin.password.edit') }}">
                        <i data-lucide="key-round"></i> Đổi mật khẩu
                    </a>
                </li>
                <li>
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
                    <button id="sidebarToggle" class="mobile-sidebar-toggle" aria-label="Bật hoặc tắt thanh điều hướng">
                        <i data-lucide="menu"></i>
                    </button>
                    <div class="admin-title">@yield('page_title', 'Bảng Điều Khiển')</div>
                </div>
                <div class="admin-user" style="display: flex; align-items: center; gap: 15px;">
                    <button id="theme-toggle" class="theme-toggle-btn" aria-label="Bật hoặc tắt chế độ tối" style="display: none !important; background: none; border: 1px solid var(--border-color); color: var(--text-primary); cursor: pointer; padding: 6px; border-radius: 6px; align-items: center; justify-content: center; width: 32px; height: 32px; transition: all 0.2s;">
                        <i data-lucide="sun" class="sun-icon" style="width: 16px; height: 16px; display: none; color: #eaaa00;"></i>
                        <i data-lucide="moon" class="moon-icon" style="width: 16px; height: 16px;"></i>
                    </button>
                    <i data-lucide="circle-user"></i>
                    <span>Xin chào, {{ $adminUser?->name ?? 'Quản trị viên' }}{{ $adminUser?->isBranchAdmin() && $adminUser?->center ? ' · ' . $adminUser->center->name : '' }}</span>
                    @if($isSuperAdmin ?? false)
                        <form method="POST" action="{{ route('admin.context.center') }}" style="margin:0;">
                            @csrf
                            <select name="center_id" onchange="this.form.submit()" aria-label="Ngữ cảnh chi nhánh" style="max-width:240px; padding:6px 8px; border-radius:6px; border:1px solid var(--border-color);">
                                <option value="" {{ $adminSelectedCenterId === null ? 'selected' : '' }}>Tất cả chi nhánh</option>
                                @foreach($adminCenters as $center)
                                    <option value="{{ $center->id }}" {{ (int) $adminSelectedCenterId === (int) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                                @endforeach
                            </select>
                        </form>
                    @endif
                </div>
            </header>
            
            <div class="admin-body">
                @if(!empty($subNavigation) && count($subNavigation) > 1)
                    <div class="admin-sub-nav-wrapper">
                        <div class="admin-sub-nav">
                            @foreach($subNavigation as $item)
                                @php
                                    $isItemActive = false;
                                    if ($currentRoute === $item['route']) {
                                        $isItemActive = true;
                                    } else {
                                        $itemPrefix = str_replace('.index', '', $item['route']);
                                        if ($item['route'] === 'admin.settings.index') {
                                            $isItemActive = ($currentRoute === 'admin.settings.index' || $currentRoute === 'admin.settings.update');
                                        } elseif ($item['route'] === 'admin.schedules.index') {
                                            $isItemActive = (str_starts_with($currentRoute, 'admin.schedules.') || str_starts_with($currentRoute, 'admin.default-slots.'));
                                        } else {
                                            $isItemActive = str_starts_with($currentRoute, $itemPrefix . '.');
                                        }
                                    }
                                @endphp
                                <a href="{{ route($item['route']) }}" class="sub-nav-item {{ $isItemActive ? 'active' : '' }}">
                                    <i data-lucide="{{ $item['icon'] }}"></i>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

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

    @include('vaccine::partials.app-dialog')
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

        // Xử lý đóng/mở Action Dropdown Menu
        window.toggleActionMenu = function(btn, event) {
            event.stopPropagation();
            const wrapper = btn.closest('.action-dropdown-wrapper');
            const menu = wrapper ? wrapper.querySelector('.action-dropdown-menu') : null;
            
            // Đóng tất cả các menu khác
            document.querySelectorAll('.action-dropdown-menu').forEach(m => {
                if (m !== menu) {
                    m.classList.remove('active');
                }
            });
            
            if (menu) {
                const isActive = menu.classList.toggle('active');
                if (isActive) {
                    const rect = btn.getBoundingClientRect();
                    const viewportHeight = window.innerHeight;
                    // If near bottom of the viewport (less than 180px available), display upwards
                    if (rect.bottom > viewportHeight - 180) {
                        menu.classList.add('dropup');
                    } else {
                        menu.classList.remove('dropup');
                    }
                }
            }
        };

        // Click ra ngoài đóng menu
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.action-dropdown-wrapper')) {
                document.querySelectorAll('.action-dropdown-menu').forEach(m => {
                    m.classList.remove('active');
                });
            }
        });

        // ===== GLOBAL MEDICARE CUSTOM DATEPICKER INITIALIZER =====
        function initGlobalMedicareDatePickers() {
            document.querySelectorAll('input[type="date"]').forEach(inputEl => {
                if (inputEl.classList.contains('medicare-dp-processed')) {
                    if (typeof inputEl.updateMedicareDisplay === 'function') {
                        inputEl.updateMedicareDisplay();
                    }
                    return;
                }
                inputEl.classList.add('medicare-dp-processed');
                
                inputEl.style.display = 'none';
                
                const wrapper = document.createElement('div');
                wrapper.className = 'medicare-datepicker-wrapper';
                wrapper.style.position = 'relative';
                wrapper.style.display = inputEl.style.display === 'none' ? 'inline-block' : 'block';
                if (inputEl.style.width) wrapper.style.width = inputEl.style.width;
                
                inputEl.parentNode.insertBefore(wrapper, inputEl);
                wrapper.appendChild(inputEl);
                
                const trigger = document.createElement('div');
                trigger.className = 'form-control-modern';
                trigger.style.display = 'flex';
                trigger.style.alignItems = 'center';
                trigger.style.justifyContent = 'space-between';
                trigger.style.gap = '8px';
                trigger.style.padding = '0 12px';
                trigger.style.height = '42px';
                trigger.style.boxSizing = 'border-box';
                trigger.style.background = '#ffffff';
                trigger.style.transition = 'border-color 0.2s, box-shadow 0.2s';
                if (inputEl.disabled) {
                    trigger.style.backgroundColor = '#f1f5f9';
                    trigger.style.cursor = 'not-allowed';
                }
                
                if (inputEl.getAttribute('style')) {
                    const inlineStyle = inputEl.getAttribute('style');
                    if (inlineStyle.includes('height:')) {
                        const match = inlineStyle.match(/height:\s*([^;]+)/);
                        if (match) trigger.style.height = match[1];
                    }
                    if (inlineStyle.includes('width:')) {
                        const match = inlineStyle.match(/width:\s*([^;]+)/);
                        if (match) trigger.style.width = match[1];
                    }
                }

                const textInput = document.createElement('input');
                textInput.type = 'text';
                textInput.className = 'medicare-datepicker-input';
                textInput.placeholder = 'dd/mm/yyyy';
                textInput.maxLength = 10;
                textInput.autocomplete = 'off';
                textInput.style.border = 'none';
                textInput.style.outline = 'none';
                textInput.style.background = 'transparent';
                textInput.style.width = '100%';
                textInput.style.fontSize = '14px';
                textInput.style.color = '#0f172a';
                textInput.style.fontWeight = '500';
                textInput.style.padding = '0';
                textInput.style.margin = '0';
                textInput.style.boxSizing = 'border-box';
                textInput.style.fontFamily = 'inherit';
                if (inputEl.disabled) {
                    textInput.disabled = true;
                    textInput.style.cursor = 'not-allowed';
                }

                textInput.addEventListener('focus', function() {
                    trigger.style.borderColor = '#c8102e';
                    trigger.style.boxShadow = '0 0 0 3px rgba(200, 16, 46, 0.15)';
                });
                textInput.addEventListener('blur', function() {
                    trigger.style.borderColor = '#cbd5e1';
                    trigger.style.boxShadow = 'none';
                });
                
                function updateDisplay() {
                    const val = inputEl.value;
                    if (val) {
                        const parts = val.split('-');
                        if (parts.length === 3) {
                            textInput.value = `${parts[2]}/${parts[1]}/${parts[0]}`;
                        } else {
                            textInput.value = val;
                        }
                    } else {
                        textInput.value = '';
                    }
                }
                inputEl.updateMedicareDisplay = updateDisplay;
                updateDisplay();

                const iconBtn = document.createElement('button');
                iconBtn.type = 'button';
                iconBtn.tabIndex = -1;
                iconBtn.className = 'medicare-datepicker-icon-btn';
                iconBtn.style.display = 'flex';
                iconBtn.style.alignItems = 'center';
                iconBtn.style.justifyContent = 'center';
                iconBtn.style.background = 'none';
                iconBtn.style.border = 'none';
                iconBtn.style.padding = '2px';
                iconBtn.style.cursor = inputEl.disabled ? 'not-allowed' : 'pointer';
                iconBtn.style.flexShrink = '0';
                iconBtn.innerHTML = '<svg style="width: 16px; height: 16px; color: #94a3b8; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>';

                trigger.appendChild(textInput);
                trigger.appendChild(iconBtn);
                wrapper.appendChild(trigger);

                if (inputEl.disabled) return;

                const popup = document.createElement('div');
                popup.style.display = 'none';
                popup.style.position = 'absolute';
                popup.style.top = 'calc(100% + 4px)';
                popup.style.left = '0';
                popup.style.background = '#ffffff';
                popup.style.border = '1px solid #cbd5e1';
                popup.style.borderRadius = '12px';
                popup.style.boxShadow = '0 15px 30px -5px rgba(0,0,0,0.15)';
                popup.style.zIndex = '1050';
                popup.style.padding = '16px';
                popup.style.width = '280px';
                popup.style.boxSizing = 'border-box';
                popup.style.userSelect = 'none';

                const maxStr = inputEl.getAttribute('max');
                const minStr = inputEl.getAttribute('min');

                let maxDateObj = maxStr ? new Date(maxStr + 'T00:00:00') : null;
                let minDateObj = minStr ? new Date(minStr + 'T00:00:00') : null;

                const todayObj = new Date();
                todayObj.setHours(0, 0, 0, 0);

                let viewYear = todayObj.getFullYear();
                let viewMonth = todayObj.getMonth();

                if (inputEl.value) {
                    const parts = inputEl.value.split('-');
                    if (parts.length === 3) {
                        viewYear = parseInt(parts[0], 10);
                        viewMonth = parseInt(parts[1], 10) - 1;
                    }
                }

                const monthNames = [
                    'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                    'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
                ];

                popup.innerHTML = `
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <button type="button" class="gdp-prev" style="background: none; border: none; font-size: 18px; color: #475569; cursor: pointer; padding: 2px 8px; border-radius: 6px;">‹</button>
                        <span class="gdp-title" style="font-weight: 700; font-size: 14px; color: #0f172a; font-family: var(--font-display);"></span>
                        <button type="button" class="gdp-next" style="background: none; border: none; font-size: 18px; color: #475569; cursor: pointer; padding: 2px 8px; border-radius: 6px;">›</button>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; text-align: center; font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 6px;">
                        <div>T2</div><div>T3</div><div>T4</div><div>T5</div><div>T6</div><div>T7</div><div style="color:#ef4444;">CN</div>
                    </div>
                    <div class="gdp-grid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 3px; text-align: center;"></div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 12px; padding-top: 10px; border-top: 1px solid #f1f5f9;">
                        <button type="button" class="gdp-clear" style="background: none; border: none; color: #64748b; font-size: 12.5px; font-weight: 600; cursor: pointer; padding: 4px 8px; border-radius: 4px;">Xóa</button>
                        <button type="button" class="gdp-today" style="background: none; border: none; color: #c8102e; font-size: 12.5px; font-weight: 700; cursor: pointer; padding: 4px 8px; border-radius: 4px;">Hôm nay</button>
                    </div>
                `;

                wrapper.appendChild(popup);

                const gdpTitle = popup.querySelector('.gdp-title');
                const gdpGrid = popup.querySelector('.gdp-grid');
                const gdpPrev = popup.querySelector('.gdp-prev');
                const gdpNext = popup.querySelector('.gdp-next');
                const gdpClear = popup.querySelector('.gdp-clear');
                const gdpToday = popup.querySelector('.gdp-today');

                function renderGdp() {
                    gdpTitle.textContent = `${monthNames[viewMonth]}, ${viewYear}`;
                    gdpGrid.innerHTML = '';

                    const firstDay = new Date(viewYear, viewMonth, 1);
                    let startDay = firstDay.getDay();
                    startDay = (startDay === 0) ? 6 : startDay - 1;

                    const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

                    for (let i = 0; i < startDay; i++) {
                        gdpGrid.appendChild(document.createElement('div'));
                    }

                    for (let day = 1; day <= daysInMonth; day++) {
                        const dayBtn = document.createElement('div');
                        dayBtn.textContent = day;
                        dayBtn.style.padding = '6px 0';
                        dayBtn.style.fontSize = '13px';
                        dayBtn.style.borderRadius = '8px';
                        dayBtn.style.cursor = 'pointer';
                        dayBtn.style.transition = 'all 0.15s';
                        dayBtn.style.color = '#334155';

                        const curDate = new Date(viewYear, viewMonth, day);
                        curDate.setHours(0, 0, 0, 0);

                        const yyyy = viewYear;
                        const mm = String(viewMonth + 1).padStart(2, '0');
                        const dd = String(day).padStart(2, '0');
                        const iso = `${yyyy}-${mm}-${dd}`;

                        const isTooLate = maxDateObj && curDate > maxDateObj;
                        const isTooEarly = minDateObj && curDate < minDateObj;
                        const isDisabledDate = isTooLate || isTooEarly;
                        const isToday = curDate.getTime() === todayObj.getTime();
                        const isSelected = inputEl.value === iso;

                        if (isDisabledDate) {
                            dayBtn.style.color = '#cbd5e1';
                            dayBtn.style.cursor = 'not-allowed';
                            dayBtn.style.opacity = '0.5';
                        } else if (isSelected) {
                            dayBtn.style.background = '#c8102e';
                            dayBtn.style.color = '#ffffff';
                            dayBtn.style.fontWeight = '700';
                        } else if (isToday) {
                            dayBtn.style.border = '1px solid #c8102e';
                            dayBtn.style.color = '#c8102e';
                            dayBtn.style.fontWeight = '700';
                        }

                        if (!isDisabledDate) {
                            dayBtn.addEventListener('mouseover', function() {
                                if (!isSelected) {
                                    this.style.background = '#fee2e2';
                                    this.style.color = '#991b1b';
                                }
                            });
                            dayBtn.addEventListener('mouseout', function() {
                                if (!isSelected) {
                                    this.style.background = 'transparent';
                                    this.style.color = isToday ? '#c8102e' : '#334155';
                                }
                            });
                            dayBtn.addEventListener('click', function(e) {
                                e.stopPropagation();
                                setGdpValue(iso);
                            });
                        }

                        gdpGrid.appendChild(dayBtn);
                    }
                }

                function setGdpValue(isoStr) {
                    inputEl.value = isoStr;
                    updateDisplay();
                    popup.style.display = 'none';
                    inputEl.dispatchEvent(new Event('change', { bubbles: true }));
                    inputEl.dispatchEvent(new Event('input', { bubbles: true }));
                }

                // Direct Keyboard Input Handling & Auto-Masking
                textInput.addEventListener('input', function(e) {
                    let val = textInput.value.replace(/[^0-9\/]/g, '');
                    if (e.inputType !== 'deleteContentBackward' && e.inputType !== 'deleteContentForward') {
                        const digits = val.replace(/\D/g, '');
                        if (digits.length >= 5) {
                            val = digits.slice(0, 2) + '/' + digits.slice(2, 4) + '/' + digits.slice(4, 8);
                        } else if (digits.length >= 3) {
                            val = digits.slice(0, 2) + '/' + digits.slice(2, 4);
                        } else if (digits.length >= 2 && !val.includes('/')) {
                            val = digits.slice(0, 2) + '/';
                        }
                    }
                    textInput.value = val;

                    const parts = val.split('/');
                    if (parts.length === 3 && parts[2].length === 4) {
                        const d = parseInt(parts[0], 10);
                        const m = parseInt(parts[1], 10);
                        const y = parseInt(parts[2], 10);
                        if (d >= 1 && d <= 31 && m >= 1 && m <= 12 && y >= 1900 && y <= 2100) {
                            const testDate = new Date(y, m - 1, d);
                            if (testDate.getFullYear() === y && testDate.getMonth() === m - 1 && testDate.getDate() === d) {
                                const iso = `${y}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                                if (inputEl.value !== iso) {
                                    inputEl.value = iso;
                                    inputEl.dispatchEvent(new Event('change', { bubbles: true }));
                                    inputEl.dispatchEvent(new Event('input', { bubbles: true }));
                                }
                                viewYear = y;
                                viewMonth = m - 1;
                                renderGdp();
                            }
                        }
                    } else if (val === '') {
                        if (inputEl.value !== '') {
                            inputEl.value = '';
                            inputEl.dispatchEvent(new Event('change', { bubbles: true }));
                            inputEl.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    }
                });

                textInput.addEventListener('blur', function() {
                    const val = textInput.value.trim();
                    if (!val) {
                        if (inputEl.value !== '') {
                            inputEl.value = '';
                            inputEl.dispatchEvent(new Event('change', { bubbles: true }));
                            inputEl.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                        return;
                    }
                    const parts = val.split('/');
                    if (parts.length === 3 && parts[2].length === 4) {
                        const d = parseInt(parts[0], 10);
                        const m = parseInt(parts[1], 10);
                        const y = parseInt(parts[2], 10);
                        if (d >= 1 && d <= 31 && m >= 1 && m <= 12 && y >= 1900 && y <= 2100) {
                            const testDate = new Date(y, m - 1, d);
                            if (testDate.getFullYear() === y && testDate.getMonth() === m - 1 && testDate.getDate() === d) {
                                const iso = `${y}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                                inputEl.value = iso;
                                inputEl.dispatchEvent(new Event('change', { bubbles: true }));
                                inputEl.dispatchEvent(new Event('input', { bubbles: true }));
                                return;
                            }
                        }
                    }
                    updateDisplay();
                });

                function togglePopup() {
                    const isExpanded = popup.style.display === 'block';
                    document.querySelectorAll('.medicare-datepicker-wrapper div[style*="position: absolute"]').forEach(p => p.style.display = 'none');
                    popup.style.display = isExpanded ? 'none' : 'block';
                    if (!isExpanded) {
                        const triggerRect = trigger.getBoundingClientRect();
                        const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
                        const scrollable = trigger.closest('.admin-main') || trigger.closest('.admin-modal-body');
                        const containerRect = scrollable ? scrollable.getBoundingClientRect() : { bottom: window.innerHeight, top: 0 };
                        const spaceBelow = containerRect.bottom - triggerRect.bottom;
                        const spaceAbove = triggerRect.top - containerRect.top;

                        if (spaceBelow < 280 && spaceAbove > spaceBelow) {
                            popup.style.top = 'auto';
                            popup.style.bottom = 'calc(100% + 4px)';
                        } else {
                            popup.style.bottom = 'auto';
                            popup.style.top = 'calc(100% + 4px)';
                        }

                        if (triggerRect.left + 290 > viewportWidth) {
                            popup.style.left = 'auto';
                            popup.style.right = '0';
                        } else {
                            popup.style.left = '0';
                            popup.style.right = 'auto';
                        }

                        if (inputEl.value) {
                            const parts = inputEl.value.split('-');
                            if (parts.length === 3) {
                                viewYear = parseInt(parts[0], 10);
                                viewMonth = parseInt(parts[1], 10) - 1;
                            }
                        }
                        renderGdp();
                    }
                }

                iconBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    togglePopup();
                });

                trigger.addEventListener('click', function(e) {
                    if (e.target !== textInput && e.target !== iconBtn && !iconBtn.contains(e.target)) {
                        e.stopPropagation();
                        textInput.focus();
                        togglePopup();
                    }
                });

                gdpPrev.addEventListener('click', function(e) {
                    e.stopPropagation();
                    viewMonth--;
                    if (viewMonth < 0) {
                        viewMonth = 11;
                        viewYear--;
                    }
                    renderGdp();
                });

                gdpNext.addEventListener('click', function(e) {
                    e.stopPropagation();
                    viewMonth++;
                    if (viewMonth > 11) {
                        viewMonth = 0;
                        viewYear++;
                    }
                    renderGdp();
                });

                gdpClear.addEventListener('click', function(e) {
                    e.stopPropagation();
                    setGdpValue('');
                });

                gdpToday.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const now = new Date();
                    const yyyy = now.getFullYear();
                    const mm = String(now.getMonth() + 1).padStart(2, '0');
                    const dd = String(now.getDate()).padStart(2, '0');
                    viewYear = yyyy;
                    viewMonth = now.getMonth();
                    setGdpValue(`${yyyy}-${mm}-${dd}`);
                });
            });
        }

        // ===== GLOBAL MEDICARE CUSTOM DROPDOWN INITIALIZER =====
        function initGlobalMedicareCustomDropdowns() {
            document.querySelectorAll('select:not(.no-custom-select)').forEach(selectEl => {
                if (selectEl.classList.contains('medicare-select-processed')) {
                    const oldWrapper = selectEl.closest('.medicare-select-wrapper');
                    if (oldWrapper && selectEl.updateMedicareCustomSelect) {
                        selectEl.updateMedicareCustomSelect();
                        return;
                    }
                    if (oldWrapper) {
                        oldWrapper.parentNode.insertBefore(selectEl, oldWrapper);
                        oldWrapper.remove();
                        selectEl.classList.remove('medicare-select-processed');
                    }
                }

                selectEl.classList.add('medicare-select-processed');
                
                selectEl.style.display = 'none';
                
                const wrapper = document.createElement('div');
                wrapper.className = 'medicare-select-wrapper';
                wrapper.style.position = 'relative';
                wrapper.style.display = selectEl.style.display === 'none' ? 'inline-block' : 'block';
                if (selectEl.style.width) wrapper.style.width = selectEl.style.width;

                selectEl.parentNode.insertBefore(wrapper, selectEl);
                wrapper.appendChild(selectEl);

                const trigger = document.createElement('div');
                trigger.className = selectEl.className || 'form-control-modern';
                trigger.style.cursor = selectEl.disabled ? 'not-allowed' : 'pointer';
                trigger.style.display = 'flex';
                trigger.style.alignItems = 'center';
                trigger.style.justifyContent = 'space-between';
                trigger.style.userSelect = 'none';
                trigger.style.gap = '8px';
                trigger.style.textAlign = 'left';
                trigger.style.backgroundImage = 'none';
                if (selectEl.disabled) {
                    trigger.style.backgroundColor = '#f1f5f9';
                    trigger.style.cursor = 'not-allowed';
                }

                if (selectEl.getAttribute('style')) {
                    const inlineStyle = selectEl.getAttribute('style');
                    if (inlineStyle.includes('height:')) {
                        const match = inlineStyle.match(/height:\s*([^;]+)/);
                        if (match) trigger.style.height = match[1];
                    }
                    if (inlineStyle.includes('width:')) {
                        const match = inlineStyle.match(/width:\s*([^;]+)/);
                        if (match) trigger.style.width = match[1];
                    }
                    if (inlineStyle.includes('padding:')) {
                        const match = inlineStyle.match(/padding:\s*([^;]+)/);
                        if (match) trigger.style.padding = match[1];
                    }
                }

                const labelSpan = document.createElement('span');
                labelSpan.style.whiteSpace = 'nowrap';
                labelSpan.style.overflow = 'hidden';
                labelSpan.style.textOverflow = 'ellipsis';
                labelSpan.style.fontSize = '13.5px';
                labelSpan.style.fontWeight = '500';
                labelSpan.style.color = 'var(--text-primary)';
                labelSpan.style.textAlign = 'left';
                labelSpan.style.flex = '1 1 auto';
                labelSpan.style.minWidth = '0';

                const arrowSvg = document.createElement('div');
                arrowSvg.className = 'medicare-select-arrow-icon';
                arrowSvg.style.display = 'flex';
                arrowSvg.style.alignItems = 'center';
                arrowSvg.style.justifyContent = 'center';
                arrowSvg.style.transition = 'transform 0.2s ease';
                arrowSvg.style.marginLeft = 'auto';
                arrowSvg.style.flexShrink = '0';
                arrowSvg.style.width = '18px';
                arrowSvg.style.height = '18px';
                arrowSvg.style.minWidth = '18px';
                arrowSvg.innerHTML = '<svg style="width: 16px; height: 16px; min-width: 16px; color: #64748b; display: block;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';

                trigger.appendChild(labelSpan);
                trigger.appendChild(arrowSvg);
                wrapper.appendChild(trigger);

                const popup = document.createElement('div');
                popup.className = 'medicare-select-popup';
                popup.style.display = 'none';
                popup.style.position = 'absolute';
                popup.style.top = 'calc(100% + 4px)';
                popup.style.left = '0';
                popup.style.width = '100%';
                popup.style.minWidth = '100%';
                popup.style.background = '#ffffff';
                popup.style.border = '1px solid #e2e8f0';
                popup.style.borderRadius = '12px';
                popup.style.boxShadow = '0 12px 30px rgba(15, 23, 42, 0.12)';
                popup.style.zIndex = '1050';
                popup.style.padding = '6px';
                popup.style.maxHeight = '240px';
                popup.style.overflowY = 'auto';
                popup.style.scrollbarWidth = 'none';
                popup.style.msOverflowStyle = 'none';
                popup.style.boxSizing = 'border-box';
                popup.style.userSelect = 'none';
                wrapper.appendChild(popup);

                function updateState() {
                    if (selectEl.disabled) {
                        trigger.style.backgroundColor = '#f1f5f9';
                        trigger.style.cursor = 'not-allowed';
                    } else {
                        trigger.style.backgroundColor = '#ffffff';
                        trigger.style.cursor = 'pointer';
                    }
                }

                function updateAlignment() {
                    trigger.style.justifyContent = 'space-between';
                    labelSpan.style.textAlign = 'left';
                    labelSpan.style.flex = '1 1 auto';
                    labelSpan.style.minWidth = '0';
                    arrowSvg.style.marginLeft = 'auto';
                }

                function renderOptions() {
                    popup.innerHTML = '';
                    updateState();
                    const options = Array.from(selectEl.options || []);
                    const selectedOpt = (selectEl.options && selectEl.selectedIndex >= 0) ? selectEl.options[selectEl.selectedIndex] : (options[0] || null);
                    if (selectedOpt && selectedOpt.textContent) {
                        labelSpan.textContent = selectedOpt.textContent.trim();
                    } else {
                        labelSpan.textContent = '';
                    }
                    updateAlignment();

                    options.forEach(opt => {
                        const item = document.createElement('div');
                        item.className = 'medicare-select-item';
                        item.style.padding = '9px 12px';
                        item.style.borderRadius = '8px';
                        item.style.cursor = 'pointer';
                        item.style.fontSize = '13.5px';
                        item.style.lineHeight = '1.4';
                        item.style.transition = 'all 0.15s ease';
                        item.style.marginBottom = '2px';
                        item.style.textAlign = 'left';
                        item.style.wordBreak = 'break-word';
                        item.textContent = opt.textContent.trim();

                        const isSelected = opt.selected;
                        if (isSelected) {
                            item.style.background = 'rgba(200, 16, 46, 0.08)';
                            item.style.color = '#c8102e';
                            item.style.fontWeight = '700';
                        } else {
                            item.style.background = 'transparent';
                            item.style.color = '#1e293b';
                            item.style.fontWeight = '500';
                            item.addEventListener('mouseenter', () => { item.style.background = '#f8fafc'; });
                            item.addEventListener('mouseleave', () => { item.style.background = 'transparent'; });
                        }

                        item.addEventListener('click', (e) => {
                            e.stopPropagation();
                            selectEl.value = opt.value;
                            labelSpan.textContent = opt.textContent.trim();
                            popup.style.display = 'none';
                            arrowSvg.style.transform = 'rotate(0deg)';
                            renderOptions();
                            selectEl.dispatchEvent(new Event('change', { bubbles: true }));
                            if (selectEl.onchange) selectEl.onchange();
                        });

                        popup.appendChild(item);
                    });
                }

                selectEl.updateMedicareCustomSelect = renderOptions;
                renderOptions();

                trigger.addEventListener('click', function(e) {
                    if (selectEl.disabled) return;
                    e.stopPropagation();
                    const isExpanded = popup.style.display === 'block';
                    document.querySelectorAll('.medicare-select-wrapper div[style*="position: absolute"]').forEach(p => {
                        p.style.display = 'none';
                    });
                    document.querySelectorAll('.medicare-select-arrow-icon').forEach(icon => {
                        icon.style.transform = 'rotate(0deg)';
                    });

                    popup.style.display = isExpanded ? 'none' : 'block';
                    arrowSvg.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';

                    if (!isExpanded) {
                        renderOptions();
                        const triggerRect = trigger.getBoundingClientRect();
                        const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
                        const scrollable = trigger.closest('.admin-main') || trigger.closest('.admin-modal-body');
                        const containerRect = scrollable ? scrollable.getBoundingClientRect() : { bottom: window.innerHeight, top: 0 };
                        const spaceBelow = containerRect.bottom - triggerRect.bottom;
                        const spaceAbove = triggerRect.top - containerRect.top;

                        if (spaceBelow < 220 && spaceAbove > spaceBelow) {
                            popup.style.top = 'auto';
                            popup.style.bottom = 'calc(100% + 4px)';
                        } else {
                            popup.style.bottom = 'auto';
                            popup.style.top = 'calc(100% + 4px)';
                        }

                        if (triggerRect.left + 240 > viewportWidth) {
                            popup.style.left = 'auto';
                            popup.style.right = '0';
                        } else {
                            popup.style.left = '0';
                            popup.style.right = 'auto';
                        }
                    }
                });

                selectEl.addEventListener('change', function() {
                    renderOptions();
                });
            });
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.medicare-select-wrapper')) {
                document.querySelectorAll('.medicare-select-wrapper div[style*="position: absolute"]').forEach(p => {
                    p.style.display = 'none';
                });
                document.querySelectorAll('.medicare-select-arrow-icon').forEach(a => {
                    a.style.transform = 'rotate(0deg)';
                });
            }
        });

        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form || form.tagName !== 'FORM') return;

            // If the form has a custom confirm prompt but has not been confirmed yet,
            // let AppDialog handle it and do not block or disable buttons yet.
            if (form.hasAttribute('data-confirm') && form.dataset.appDialogConfirmed !== 'true') {
                return;
            }

            // If already submitting, block duplicate submission
            if (form.dataset.submitting === 'true') {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }

            form.dataset.submitting = 'true';

            // Disable submit buttons asynchronously to avoid canceling the submit action
            const submitBtns = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            setTimeout(() => {
                submitBtns.forEach(btn => {
                    btn.disabled = true;
                    btn.style.opacity = '0.7';
                    btn.style.cursor = 'not-allowed';
                });
            }, 0);

            // Re-enable after 8 seconds as safety timeout (e.g. if navigation is cancelled)
            setTimeout(() => {
                delete form.dataset.submitting;
                submitBtns.forEach(btn => {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btn.style.cursor = 'pointer';
                });
            }, 8000);
        }, true);

        document.addEventListener('DOMContentLoaded', function() {
            initGlobalMedicareDatePickers();
            initGlobalMedicareCustomDropdowns();
        });
    </script>
    @include('vaccine::admin.partials._image_cropper_modal')
    @yield('scripts')
</body>
</html>
