@extends('vaccine::layouts.admin')

@section('title', 'Chỉnh Sửa Trực Quan Toàn Bộ Tất Cả Các Trang - Medicare')
@section('page_title', '🎨 Trình Chỉnh Sửa Trực Quan Toàn Bộ Các Trang (Universal All-Page Live Editor)')

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
        <i data-lucide="home" style="width: 16px; height: 16px;"></i> 🏠 Trang Chủ (7 Khung)
    </a>
    <a href="{{ route('admin.live-editor', ['page' => 'about']) }}" class="live-page-tab {{ $currentPage === 'about' ? 'active' : '' }}">
        <i data-lucide="building-2" style="width: 16px; height: 16px;"></i> 🏢 Giới Thiệu (3 Khung)
    </a>
    <a href="{{ route('admin.live-editor', ['page' => 'services']) }}" class="live-page-tab {{ $currentPage === 'services' ? 'active' : '' }}">
        <i data-lucide="stethoscope" style="width: 16px; height: 16px;"></i> 🛠️ Dịch Vụ (2 Khung)
    </a>
    <a href="{{ route('admin.live-editor', ['page' => 'contact']) }}" class="live-page-tab {{ $currentPage === 'contact' ? 'active' : '' }}">
        <i data-lucide="map-pin" style="width: 16px; height: 16px;"></i> 📍 Liên Hệ & Chi Nhánh
    </a>
    <a href="{{ route('admin.live-editor', ['page' => 'vaccines']) }}" class="live-page-tab {{ $currentPage === 'vaccines' ? 'active' : '' }}">
        <i data-lucide="syringe" style="width: 16px; height: 16px;"></i> 💉 Vắc Xin CSDL
    </a>
    <a href="{{ route('admin.live-editor', ['page' => 'global']) }}" class="live-page-tab {{ $currentPage === 'global' ? 'active-global' : '' }}" style="margin-left: auto; border: 1px solid #0284c7;">
        <i data-lucide="settings-2" style="width: 16px; height: 16px;"></i> ⚙️ Khung Chung System Shell
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
            <h4 style="margin: 0 0 10px 0; color: var(--primary-color); font-size: 18px; font-weight: 700;">🎯 {{ $settings['about_mission_title'] ?? 'Sứ Mệnh Bảo Vệ Sức Khỏe' }}</h4>
            <p style="color: #64748b; font-size: 14.5px; margin: 0; line-height: 1.6;">{{ $settings['about_mission_desc'] ?? 'Mang lại dịch vụ tiêm chủng an toàn tuyệt đối, nhanh chóng và tiếp cận dễ dàng cho mọi gia đình. Giúp cộng đồng chủ động phòng ngừa bệnh truyền nhiễm.' }}</p>
        </div>
    </div>

    <!-- Khung 3: Dây Chuyền Dược Kho Lạnh GSP -->
    <div class="live-edit-frame" onclick="openSettingModal('about_gsp', 'Kho Lạnh GSP Đạt Chuẩn', ['about_gsp_title', 'about_gsp_desc'])">
        <div class="edit-frame-badge"><i data-lucide="edit-3"></i> Sửa Khung 3: Kho Lạnh GSP</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 12px;">
            <h4 style="margin: 0 0 10px 0; color: #0284c7; font-size: 18px; font-weight: 700;">🛡️ {{ $settings['about_gsp_title'] ?? 'Kho Lạnh GSP Đạt Chuẩn' }}</h4>
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
</script>
@endsection
