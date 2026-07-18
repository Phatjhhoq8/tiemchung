<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hệ Thống Đăng Ký Tiêm Chủng VNVC')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <header class="app-header">
        <div class="header-container">
            <a href="{{ route('vaccine.index') }}" class="logo">
                <span class="logo-icon"><i data-lucide="shield-check"></i></span>
                <span class="logo-text">VNVC <span>Clone</span></span>
            </a>
            
            <nav class="nav-menu">
                <a href="{{ route('vaccine.index') }}" class="nav-link active">Đăng Ký Tiêm Chủng</a>
                <a href="#" class="nav-link">Bảng Giá Vắc Xin</a>
                <a href="#" class="nav-link">Cẩm Nang Y Khoa</a>
                <a href="#" class="nav-link">Hệ Thống Trung Tâm</a>
            </nav>
            
            <div class="header-actions">
                <a href="tel:18006595" class="hotline-btn">
                    <i data-lucide="phone-call"></i>
                    <span>Hotline: 1800 6595</span>
                </a>
            </div>
        </div>
    </header>

    <main class="app-main">
        @yield('content')
    </main>

    <footer class="app-footer">
        <div class="footer-container">
            <div class="footer-info">
                <h3>Hệ thống Tiêm chủng VNVC</h3>
                <p>Cung cấp dịch vụ tiêm chủng vắc xin chất lượng cao, an toàn và hiệu quả hàng đầu Việt Nam.</p>
                <div class="contact-details">
                    <p><i data-lucide="map-pin"></i> 180 Trường Chinh, P. Khương Thượng, Q. Đống Đa, Hà Nội</p>
                    <p><i data-lucide="mail"></i> cskh@vnvc.vn</p>
                </div>
            </div>
            <div class="footer-links">
                <h4>Liên kết nhanh</h4>
                <ul>
                    <li><a href="#">Quy trình tiêm chủng</a></li>
                    <li><a href="#">Danh mục vắc xin lẻ</a></li>
                    <li><a href="#">Gói vắc xin gia đình</a></li>
                    <li><a href="#">Chính sách bảo mật</a></li>
                </ul>
            </div>
            <div class="footer-tagline">
                <p>&copy; {{ date('Y') }} VNVC Clone Project. Thiết kế theo tiêu chuẩn Clean & Modular.</p>
            </div>
        </div>
    </footer>

    <!-- JS Custom -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        // Khởi tạo các Lucide Icons
        lucide.createIcons();
    </script>
    @yield('scripts')
</body>
</html>
