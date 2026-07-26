@extends('vaccine::layouts.admin')

@section('title', 'Chỉnh Sửa Trực Quan Toàn Bộ Tất Cả Các Trang - Medicare')
@section('page_title', 'Trình Chỉnh Sửa Trực Quan Toàn Bộ Các Trang (Universal All-Page Live Editor)')

@section('styles')
<style>
    /* Page Switcher Navigation Tabs */
    .live-page-tabs {
        display: flex;
        gap: 8px;
        background: #ffffff;
        padding: 8px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .live-page-tab {
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13.5px;
        color: #475569;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    .live-page-tab:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .live-page-tab.active {
        background: var(--primary-color, #c8102e);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(200, 16, 46, 0.25);
    }
    .live-page-tab.active-global {
        background: #0284c7;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }

    /* Facebook Customizer Overlay Frames */
    .live-edit-frame {
        position: relative;
        border: 2px dashed #0284c7;
        border-radius: 14px;
        transition: all 0.3s ease;
        cursor: pointer;
        margin-bottom: 24px;
        background-color: rgba(2, 132, 199, 0.02);
    }
    .live-edit-frame:hover {
        border-color: var(--primary-color, #c8102e);
        box-shadow: 0 0 20px rgba(200, 16, 46, 0.2);
        background-color: rgba(200, 16, 46, 0.03);
    }
    .edit-frame-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background-color: #0284c7;
        color: #ffffff;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        z-index: 50;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        pointer-events: none;
    }
    .live-edit-frame:hover .edit-frame-badge {
        background-color: var(--primary-color, #c8102e);
    }

    /* Modal Styling */
    .fb-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(4px);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .fb-modal-content {
        background: #ffffff;
        width: 100%;
        max-width: 650px;
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        overflow: hidden;
        animation: modalSlideUp 0.3s ease-out;
    }
    @keyframes modalSlideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .fb-modal-header {
        background: #f8fafc;
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .fb-modal-body {
        padding: 24px;
        max-height: 75vh;
        overflow-y: auto;
    }
    .fb-modal-footer {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }
</style>
@endsection

@section('admin_content')
<!-- BAR CHUYỂN ĐỔI CÁC TRANG CẦN CHỈNH SỬA LIVE -->
<div class="live-page-tabs">
    <a href="{{ route('admin.live-editor', ['page' => 'home']) }}" class="live-page-tab {{ $currentPage === 'home' ? 'active' : '' }}">
        Trang Chủ (7 Khung)
    </a>
    <a href="{{ route('admin.live-editor', ['page' => 'layout']) }}" class="live-page-tab {{ $currentPage === 'layout' ? 'active' : '' }}" style="border: 1px dashed #eaaa00;">
        Sắp Xếp Trang Chủ
    </a>
    <a href="{{ route('admin.live-editor', ['page' => 'about']) }}" class="live-page-tab {{ $currentPage === 'about' ? 'active' : '' }}">
        Giới Thiệu (3 Khung)
    </a>
    <a href="{{ route('admin.live-editor', ['page' => 'services']) }}" class="live-page-tab {{ $currentPage === 'services' ? 'active' : '' }}">
        Dịch Vụ (2 Khung)
    </a>
    <a href="{{ route('admin.live-editor', ['page' => 'contact']) }}" class="live-page-tab {{ $currentPage === 'contact' ? 'active' : '' }}">
        Liên Hệ & Chi Nhánh
    </a>
    <a href="{{ route('admin.live-editor', ['page' => 'vaccines']) }}" class="live-page-tab {{ $currentPage === 'vaccines' ? 'active' : '' }}">
        Vắc Xin CSDL
    </a>
    <a href="{{ route('admin.live-editor', ['page' => 'global']) }}" class="live-page-tab {{ $currentPage === 'global' ? 'active-global' : '' }}" style="margin-left: auto; border: 1px solid #0284c7;">
        Khung Chung System Shell
    </a>
</div>

<!-- ================= 1. TAB TRANG CHỦ ================= -->
@if($currentPage === 'home')
    <!-- Khung 1: Hero Banner Slider -->
    <div class="live-edit-frame" onclick="openBannerModal()">
        <div class="edit-frame-badge"><i data-lucide="edit-3"></i> Sửa Hero Banner Slider</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 10px;">
            <h4 style="margin: 0 0 12px 0; color: #475569; font-size: 13px; text-transform: uppercase; font-weight: 700;">[Khung 1: Hero Banner Slider Trang Chủ]</h4>
            @php $firstBanner = $banners->first(); @endphp
            <div style="position: relative; height: 180px; border-radius: 12px; overflow: hidden; background: #000;">
                <img src="{{ asset($firstBanner ? $firstBanner->image_url : 'images/banners/banner_family.jpg') }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.85;">
                <div style="position: absolute; bottom: 20px; left: 20px; color: #fff;">
                    <h3 style="margin: 0 0 6px 0; font-size: 22px; font-weight: 800;">{{ $firstBanner->title ?? 'Hệ Thống Tiêm Chủng Medicare' }}</h3>
                    <p style="margin: 0; font-size: 13px; opacity: 0.9;">{{ $firstBanner->subtitle ?? 'Chăm sóc sức khỏe gia đình' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Khung 2: Thanh 4 Ô Tiện Ích Thao Tác Nhanh (Quick Action Toolbar) -->
    <div class="live-edit-frame" onclick="openSettingModal('quick_toolbar', 'Thanh 4 Tiện Ích Nhanh', ['quick_t1_title', 'quick_t1_sub', 'quick_t2_title', 'quick_t2_sub'])">
        <div class="edit-frame-badge"><i data-lucide="edit-3"></i> Sửa Khung 2: Bảng 4 Tiện Ích Nhanh</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 10px;">
            <h4 style="margin: 0 0 14px 0; color: #475569; font-size: 13px; text-transform: uppercase; font-weight: 700;">[Khung 2: Thanh Bảng 4 Tiện Ích Nhanh Nổi Bật]</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">
                <div style="padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc;">
                    <strong style="color: var(--primary-color); font-size: 13.5px;">1. Đặt Mua Vắc Xin Online</strong>
                </div>
                <div style="padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc;">
                    <strong style="color: #0284c7; font-size: 13.5px;">2. Đăng Ký Tiêm Chủng</strong>
                </div>
                <div style="padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc;">
                    <strong style="color: #eaaa00; font-size: 13.5px;">3. Bảng Giá Vắc Xin</strong>
                </div>
                <div style="padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc;">
                    <strong style="color: #16a34a; font-size: 13.5px;">4. Tìm Chi Nhánh Gần Bạn</strong>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- ================= 2. TAB TRANG GIỚI THIỆU (/about) ================= -->
@if($currentPage === 'about')
    <!-- Khung 1: Hero Banner Giới Thiệu -->
    <div class="live-edit-frame" onclick="openSettingModal('about_hero', 'Banner Đầu Trang Giới Thiệu', ['about_hero_title', 'about_hero_desc'])">
        <div class="edit-frame-badge"><i data-lucide="edit-3"></i> Sửa Khung 1: Banner Giới Thiệu</div>
        <div style="padding: 28px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff; border-radius: 12px; text-align: center;">
            <span style="background-color: var(--primary-color); color: #ffffff; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase;">Về Chúng Tôi</span>
            <h2 style="font-size: 26px; font-weight: 800; margin: 12px 0 8px 0; color: #fff;">{{ $settings['about_hero_title'] ?? 'Phòng Tiêm Chủng Vắc Xin Medicare' }}</h2>
            <p style="color: #94a3b8; font-size: 14.5px; max-width: 650px; margin: 0 auto;">{{ $settings['about_hero_desc'] ?? 'Đơn vị y tế uy tín hàng đầu cung cấp giải pháp phòng bệnh toàn diện bằng vắc xin chất lượng cao cho trẻ em và người lớn tại Cờ Đỏ và Thới Lai.' }}</p>
        </div>
    </div>

    <!-- Khung 2: Sứ Mệnh Bảo Vệ Sức Khỏe -->
    <div class="live-edit-frame" onclick="openSettingModal('about_mission', 'Sứ Mệnh Bảo Vệ Sức Khỏe', ['about_mission_title', 'about_mission_desc'])">
        <div class="edit-frame-badge"><i data-lucide="edit-3"></i> Sửa Khung 2: Sứ Mệnh Bảo Vệ Sức Khỏe</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 12px;">
            <h4 style="margin: 0 0 10px 0; color: var(--primary-color); font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 8px;"><i data-lucide="target" style="width: 20px; height: 20px;"></i> {{ $settings['about_mission_title'] ?? 'Sứ Mệnh Bảo Vệ Sức Khỏe' }}</h4>
            <p style="color: #64748b; font-size: 14.5px; margin: 0; line-height: 1.6;">{{ $settings['about_mission_desc'] ?? 'Mang lại dịch vụ tiêm chủng an toàn tuyệt đối, nhanh chóng và tiếp cận dễ dàng cho mọi gia đình. Giúp cộng đồng chủ động phòng ngừa bệnh truyền nhiễm.' }}</p>
        </div>
    </div>

    <!-- Khung 3: Dây Chuyền Dược Kho Lạnh GSP -->
    <div class="live-edit-frame" onclick="openSettingModal('about_gsp', 'Kho Lạnh GSP Đạt Chuẩn', ['about_gsp_title', 'about_gsp_desc'])">
        <div class="edit-frame-badge"><i data-lucide="edit-3"></i> Sửa Khung 3: Kho Lạnh GSP</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 12px;">
            <h4 style="margin: 0 0 10px 0; color: #0284c7; font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 8px;"><i data-lucide="shield" style="width: 20px; height: 20px;"></i> {{ $settings['about_gsp_title'] ?? 'Kho Lạnh GSP Đạt Chuẩn' }}</h4>
            <p style="color: #64748b; font-size: 14.5px; margin: 0; line-height: 1.6;">{{ $settings['about_gsp_desc'] ?? '100% vắc xin lưu trữ trong kho lạnh dây chuyền lạnh GSP đạt tiêu chuẩn Bộ Y tế, duy trì nhiệt độ chuẩn 2 - 8°C cho chất lượng vắc xin tối đa.' }}</p>
        </div>
    </div>
@endif

<!-- ================= 3. TAB TRANG DỊCH VỤ (/services) ================= -->
@if($currentPage === 'services')
    <div class="live-edit-frame" onclick="openSettingModal('services_hero', 'Banner Đầu Trang Dịch Vụ', ['services_hero_title', 'services_hero_desc'])">
        <div class="edit-frame-badge"><i data-lucide="edit-3"></i> Sửa Khung 1: Banner Dịch Vụ</div>
        <div style="padding: 28px; background: #ffffff; border-radius: 12px;">
            <h2 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0 0 8px 0;">{{ $settings['services_hero_title'] ?? 'Dịch Vụ Tiêm Chủng Toàn Diện' }}</h2>
            <p style="color: #64748b; font-size: 15px; margin: 0;">{{ $settings['services_hero_desc'] ?? 'Cung cấp đầy đủ gói tiêm vắc xin cho Trẻ em, Người lớn, Phụ nữ chuẩn bị mang thai và Tiêm chủng lưu động doanh nghiệp.' }}</p>
        </div>
    </div>
@endif

<!-- ================= 4. TAB TRANG LIÊN HỆ (/contact) ================= -->
@if($currentPage === 'contact')
    <div class="live-edit-frame" onclick="openSettingModal('contact_branches', 'Thông Tin 2 Chi Nhánh', ['branch1_name', 'branch1_phone', 'branch2_name', 'branch2_phone'])">
        <div class="edit-frame-badge"><i data-lucide="edit-3"></i> Sửa Thông Tin 2 Chi Nhánh</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 12px;">
            <h4 style="margin: 0 0 14px 0; color: #475569; font-size: 13px; text-transform: uppercase; font-weight: 700;">[Khung: Chi Nhánh Medicare Cờ Đỏ & Thới Lai]</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div style="padding: 14px; border: 1px solid #cbd5e1; border-radius: 8px; background: #f8fafc;">
                    <strong style="color: var(--primary-color);">📍 {{ $settings['branch1_name'] ?? 'Chi nhánh 1: Medicare Cờ Đỏ' }}</strong>
                    <div style="font-size: 13px; color: #475569; margin-top: 4px;">Phone: {{ $settings['branch1_phone'] ?? '0938 60 38 39' }}</div>
                </div>
                <div style="padding: 14px; border: 1px solid #cbd5e1; border-radius: 8px; background: #f8fafc;">
                    <strong style="color: #0284c7;">📍 {{ $settings['branch2_name'] ?? 'Chi nhánh 2: Medicare Thới Lai' }}</strong>
                    <div style="font-size: 13px; color: #475569; margin-top: 4px;">Phone: {{ $settings['branch2_phone'] ?? '0932 477 184' }}</div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- ================= TAB KHUNG CHUNG SYSTEM SHELL ================= -->
@if($currentPage === 'global')
    <div class="live-edit-frame" onclick="openSettingModal('global_shell', 'Khung Chung Toàn Hệ Thống', ['site_name', 'brand_title', 'hotline', 'email', 'footer_text'])">
        <div class="edit-frame-badge" style="background: #0284c7;"><i data-lucide="settings-2"></i> Sửa Khung Dùng Chung System Shell</div>
        <div style="padding: 28px; background: #ffffff; border-radius: 12px;">
            <h4 style="margin: 0 0 16px 0; color: #0284c7; font-size: 13px; text-transform: uppercase; font-weight: 800;">[Khung Dùng Chung: Header, Topbar, Footer, Bong bóng Chat Zalo]</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                <div style="padding: 14px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px;">
                    <strong style="color: #0369a1; display: block; margin-bottom: 4px;">Tên Thương Hiệu:</strong>
                    <span>{{ $settings['brand_title'] ?? 'Hệ Thống Tiêm Chủng Medicare' }}</span>
                </div>
                <div style="padding: 14px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px;">
                    <strong style="color: #0369a1; display: block; margin-bottom: 4px;">Hotline Tổng:</strong>
                    <span>{{ $settings['hotline'] ?? '0938 60 38 39' }}</span>
                </div>
                <div style="padding: 14px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px;">
                    <strong style="color: #0369a1; display: block; margin-bottom: 4px;">Bản Quyền Footer:</strong>
                    <span style="font-size: 12.5px;">{{ $settings['footer_text'] ?? '© 2026 Medicare' }}</span>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- ================= TAB SẮP XẾP TRANG CHỦ ================= -->
@if($currentPage === 'layout')
    <style>
        .editor-btn-secondary {
            padding: 10px 18px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        .editor-btn-secondary:hover:not(:disabled) {
            background: #e2e8f0;
            color: #334155;
            border-color: #94a3b8;
            transform: translateY(-1px);
        }
        .editor-btn-secondary:active:not(:disabled) {
            transform: translateY(0);
        }

        .editor-btn-secondary-outline {
            padding: 10px 18px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        .editor-btn-secondary-outline:hover:not(:disabled) {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
            transform: translateY(-1px);
        }
        .editor-btn-secondary-outline:active:not(:disabled) {
            transform: translateY(0);
        }

        .editor-btn-primary {
            padding: 10px 18px;
            border-radius: 8px;
            background: #c8102e;
            color: #fff;
            border: none;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 10px rgba(200, 16, 46, 0.15);
            transition: all 0.2s ease-in-out;
        }
        .editor-btn-primary:hover:not(:disabled) {
            background: #a00d24;
            box-shadow: 0 6px 14px rgba(200, 16, 46, 0.25);
            transform: translateY(-1px);
        }
        .editor-btn-primary:active:not(:disabled) {
            transform: translateY(0);
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin {
            animation: spin 1s linear infinite;
        }
    </style>

    <div style="background: #ffffff; padding: 28px; border-radius: 16px; border: 1px solid #cbd5e1; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
        <div style="display: flex; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 24px;">
            <div>
                <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 8px;"><i data-lucide="layout-grid" style="width: 20px; height: 20px; color: var(--primary-color);"></i> Sắp xếp & Cấu hình các phần Trang Chủ</h3>
                <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Thay đổi thứ tự hiển thị, bật/tắt (ghim), đổi màu nền và khoảng giãn của từng phần trên trang chủ.</p>
            </div>
            <div style="display: flex; gap: 8px; margin-left: auto;">
                <button type="button" onclick="resetLayoutConfig()" class="editor-btn-secondary" id="btn-reset-layout">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Khôi Phục
                </button>
                <button type="button" onclick="previewLayoutConfig()" class="editor-btn-secondary-outline" id="btn-preview-layout">
                    <i data-lucide="eye" class="w-4 h-4"></i> Xem Giả Lập
                </button>
                <button type="button" onclick="publishLayoutConfigLive()" class="editor-btn-primary" id="btn-publish-layout">
                    <i data-lucide="send" class="w-4 h-4"></i> Áp Dụng
                </button>
            </div>
        </div>

        <form id="homepageLayoutForm">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div style="display: grid; grid-template-columns: 80px 1fr 150px 180px 150px; gap: 16px; padding: 12px 16px; background: #f8fafc; border-radius: 8px; font-weight: 700; color: #475569; font-size: 13px;">
                    <div>Thứ Tự</div>
                    <div>Tên Phân Phần (Section)</div>
                    <div>Trạng Thái</div>
                    <div>Màu Nền</div>
                    <div>Khoảng Cách</div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 12px;" id="sortable-sections-list">
                    @foreach($layoutConfig as $key => $section)
                        <div class="layout-section-row" data-key="{{ $key }}" draggable="true" style="display: grid; grid-template-columns: 80px 1fr 150px 180px 150px; gap: 16px; align-items: center; padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; transition: all 0.2s; cursor: grab;">
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <button type="button" onclick="moveSectionRow(this, 'up')" class="p-1.5 hover:bg-slate-100 rounded text-slate-600 transition-colors" title="Di chuyển lên">
                                    <i data-lucide="chevron-up" class="w-4 h-4"></i>
                                </button>
                                <button type="button" onclick="moveSectionRow(this, 'down')" class="p-1.5 hover:bg-slate-100 rounded text-slate-600 transition-colors" title="Di chuyển xuống">
                                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                </button>
                            </div>
                            <div style="font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                                <i data-lucide="grip-vertical" class="w-4 h-4 text-slate-400 cursor-move"></i>
                                <span>{{ $section['name'] }}</span>
                                <span style="font-family: monospace; font-size: 11px; font-weight: 500; color: #94a3b8; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">{{ $key }}</span>
                            </div>
                            <input type="hidden" class="section-order-input" name="layout[{{ $key }}][order]" value="{{ $section['order'] }}">
                            <div>
                                <select name="layout[{{ $key }}][is_visible]" style="width: 100%; padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12.5px; font-weight: 600;">
                                    <option value="1" {{ $section['is_visible'] ? 'selected' : '' }}>🟢 Hiện</option>
                                    <option value="0" {{ !$section['is_visible'] ? 'selected' : '' }}>🔴 Ẩn</option>
                                </select>
                            </div>
                            <div>
                                @if($key === 'hero_slider' || $key === 'quick_booking')
                                    <span style="font-size: 12px; color: #94a3b8; font-style: italic;">Mặc định phần</span>
                                    <input type="hidden" name="layout[{{ $key }}][bg]" value="{{ $section['bg'] }}">
                                @else
                                    <select name="layout[{{ $key }}][bg]" style="width: 100%; padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12.5px; font-weight: 600;">
                                        <option value="white" {{ $section['bg'] === 'white' ? 'selected' : '' }}>⚪ Trắng</option>
                                        <option value="red" {{ $section['bg'] === 'red' ? 'selected' : '' }}>🔴 Đỏ</option>
                                        <option value="dark" {{ $section['bg'] === 'dark' ? 'selected' : '' }}>🔵 Tối</option>
                                    </select>
                                @endif
                            </div>
                            <div>
                                @if($key === 'hero_slider' || $key === 'quick_booking')
                                    <span style="font-size: 12px; color: #94a3b8; font-style: italic;">Mặc định phần</span>
                                    <input type="hidden" name="layout[{{ $key }}][padding]" value="{{ $section['padding'] }}">
                                @else
                                    <select name="layout[{{ $key }}][padding]" style="width: 100%; padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12.5px; font-weight: 600;">
                                        <option value="compact" {{ $section['padding'] === 'compact' ? 'selected' : '' }}>Hẹp</option>
                                        <option value="standard" {{ $section['padding'] === 'standard' ? 'selected' : '' }}>Vừa</option>
                                        <option value="spacious" {{ $section['padding'] === 'spacious' ? 'selected' : '' }}>Rộng</option>
                                    </select>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </form>
    </div>
@endif

<!-- MODAL CẤU HÌNH -->
<div id="settingModal" class="fb-modal-overlay">
    <div class="fb-modal-content">
        <div class="fb-modal-header">
            <h3 id="settingModalTitle" style="margin: 0; font-size: 17px; font-weight: 700; color: #1e293b;">Chỉnh Sửa Trực Quan Cài Đặt</h3>
            <button onclick="closeModal('settingModal')" style="background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
        </div>
        <form id="settingForm">
            @csrf
            <div class="fb-modal-body" id="settingModalFields"></div>
            <div class="fb-modal-footer">
                <button type="button" onclick="closeModal('settingModal')" class="btn-secondary" style="padding: 9px 16px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff;">Hủy</button>
                <button type="button" onclick="saveSettingsSubmit()" class="btn-primary" style="padding: 9px 20px; border-radius: 8px; background: #0284c7; color: #fff; border: none; font-weight: 700;">Lưu Cấu Hình</button>
            </div>
        </form>
    </div>
</div>

@include('vaccine::admin.live_editor_modals')

@endsection

@section('scripts')
<script>
    const settingsData = @json($settings);

    function openSettingModal(type, title, fields) {
        document.getElementById('settingModalTitle').innerText = 'Chỉnh Sửa Live: ' + title;
        const container = document.getElementById('settingModalFields');
        container.innerHTML = '';

        fields.forEach(field => {
            const val = settingsData[field] || '';
            const fieldGroup = document.createElement('div');
            fieldGroup.style.marginBottom = '14px';
            fieldGroup.innerHTML = `
                <label style="display:block; font-weight:700; font-size:13px; margin-bottom:4px; color:#334155;">Nội dung [${field}]:</label>
                <textarea name="${field}" class="form-control" rows="2" style="width:100%; padding:10px; border-radius:8px; border:1px solid #cbd5e1;">${val}</textarea>
            `;
            container.appendChild(fieldGroup);
        });

        document.getElementById('settingModal').style.display = 'flex';
    }

    function saveSettingsSubmit() {
        const form = document.getElementById('settingForm');
        const formData = new FormData(form);

        fetch("{{ route('admin.live-editor.settings') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("🎉 " + data.message);
                closeModal('settingModal');
                window.location.reload();
            }
        });
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function saveLayoutConfigDraft() {
        const form = document.getElementById('homepageLayoutForm');
        const formData = new FormData(form);

        fetch("{{ route('admin.live-editor.layout.save') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("🎉 " + data.message);
                window.location.reload();
            } else {
                alert("❌ Lỗi: " + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert("❌ Đã xảy ra lỗi khi lưu cấu hình nháp.");
        });
    }

    function previewLayoutConfig() {
        const btn = document.getElementById('btn-preview-layout');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.style.opacity = '0.7';
        btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Đang tải...';
        if (window.lucide) { lucide.createIcons(); }

        const form = document.getElementById('homepageLayoutForm');
        const formData = new FormData(form);

        fetch("{{ route('admin.live-editor.layout.save') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.innerHTML = originalHtml;
            if (window.lucide) { lucide.createIcons(); }
            if (data.success) {
                window.open("{{ url('/?preview=1') }}", "_blank");
            } else {
                alert("❌ Lỗi lưu cấu hình: " + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert("❌ Đã xảy ra lỗi khi lưu cấu hình nháp trước khi xem giả lập.");
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.innerHTML = originalHtml;
            if (window.lucide) { lucide.createIcons(); }
        });
    }

    function resetLayoutConfig() {
        const btn = document.getElementById('btn-reset-layout');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.style.opacity = '0.7';
        btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Đang khôi phục...';
        if (window.lucide) { lucide.createIcons(); }

        fetch("{{ route('admin.live-editor.layout.reset') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> Thành công!';
                if (window.lucide) { lucide.createIcons(); }
                setTimeout(() => {
                    window.location.reload();
                }, 600);
            } else {
                alert("❌ Lỗi: " + data.message);
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.innerHTML = originalHtml;
                if (window.lucide) { lucide.createIcons(); }
            }
        })
        .catch(err => {
            console.error(err);
            alert("❌ Đã xảy ra lỗi khi khôi phục cấu hình.");
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.innerHTML = originalHtml;
            if (window.lucide) { lucide.createIcons(); }
        });
    }

    function publishLayoutConfigLive() {
        const btn = document.getElementById('btn-publish-layout');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.style.opacity = '0.7';
        btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Đang áp dụng...';
        if (window.lucide) { lucide.createIcons(); }

        const form = document.getElementById('homepageLayoutForm');
        const formData = new FormData(form);

        fetch("{{ route('admin.live-editor.layout.publish') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> Thành công!';
                if (window.lucide) { lucide.createIcons(); }
                setTimeout(() => {
                    window.location.reload();
                }, 600);
            } else {
                alert("❌ Lỗi: " + data.message);
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.innerHTML = originalHtml;
                if (window.lucide) { lucide.createIcons(); }
            }
        })
        .catch(err => {
            console.error(err);
            alert("❌ Đã xảy ra lỗi khi áp dụng cấu hình.");
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.innerHTML = originalHtml;
            if (window.lucide) { lucide.createIcons(); }
        });
    }

    // Recalculate order values sequentially (10, 20, 30...) to prevent out-of-bound errors
    function recalculateOrderValues() {
        const list = document.getElementById('sortable-sections-list');
        if (!list) return;
        const rows = list.querySelectorAll('.layout-section-row');
        rows.forEach((row, index) => {
            const orderInput = row.querySelector('.section-order-input');
            if (orderInput) {
                orderInput.value = (index + 1) * 10;
            }
        });
    }

    // Move section row up or down and swap
    function moveSectionRow(btn, direction) {
        const row = btn.closest('.layout-section-row');
        if (!row) return;

        if (direction === 'up') {
            const prev = row.previousElementSibling;
            if (prev && prev.classList.contains('layout-section-row')) {
                prev.before(row);
            }
        } else if (direction === 'down') {
            const next = row.nextElementSibling;
            if (next && next.classList.contains('layout-section-row')) {
                next.after(row);
            }
        }
        recalculateOrderValues();
    }

    // Initialize HTML5 Drag & Drop sorting
    document.addEventListener('DOMContentLoaded', () => {
        const list = document.getElementById('sortable-sections-list');
        if (!list) return;
        
        let draggingEl = null;

        list.addEventListener('dragstart', (e) => {
            const row = e.target.closest('.layout-section-row');
            if (!row) return;
            draggingEl = row;
            e.dataTransfer.effectAllowed = 'move';
            row.style.opacity = '0.5';
        });

        list.addEventListener('dragover', (e) => {
            e.preventDefault();
            const row = e.target.closest('.layout-section-row');
            if (!row || row === draggingEl) return;

            const bounding = row.getBoundingClientRect();
            const offset = e.clientY - bounding.top - (bounding.height / 2);

            if (offset > 0) {
                row.after(draggingEl);
            } else {
                row.before(draggingEl);
            }
        });

        list.addEventListener('dragend', () => {
            if (draggingEl) {
                draggingEl.style.opacity = '';
                draggingEl = null;
                recalculateOrderValues();
            }
        });
    });
</script>
@endsection
