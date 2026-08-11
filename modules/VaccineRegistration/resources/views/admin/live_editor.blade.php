@extends('vaccine::layouts.admin')

@section('title', 'Chỉnh Sửa Trực Quan Toàn Bộ Tất Cả Các Trang - Medicare')
@section('page_title', 'Chỉnh sửa nội dung website')

@section('styles')
<style>
    /* Page Switcher Navigation Tabs */
    .live-page-tabs {
        display: flex;
        gap: 10px;
        background: rgba(255, 255, 255, 0.92);
        padding: 10px;
        border-radius: 16px;
        border: 1px solid #fecaca;
        margin-bottom: 24px;
        flex-wrap: wrap;
        box-shadow: 0 6px 18px rgba(127, 29, 29, 0.06);
    }
    .live-page-tab {
        padding: 11px 18px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 13.5px;
        color: #7f1d1d;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        border: 1px solid #fee2e2;
        background: #fff7f7;
    }
    .live-page-tab:hover {
        background: #fff1f2;
        color: #991b1b;
        border-color: #fca5a5;
        transform: translateY(-1px);
    }
    .live-page-tab.active {
        background: var(--primary-color, #c8102e);
        color: #ffffff;
        border-color: var(--primary-color, #c8102e);
        box-shadow: 0 8px 18px rgba(200, 16, 46, 0.24);
    }
    .live-page-tab.active-global {
        background: #b91c1c;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(185, 28, 28, 0.25);
    }

    /* Facebook Customizer Overlay Frames */
    .live-edit-frame {
        position: relative;
        border: 1px solid #fecaca;
        border-left: 4px solid #dc2626;
        border-radius: 16px;
        transition: all 0.3s ease;
        cursor: pointer;
        margin-bottom: 20px;
        background: #ffffff;
        box-shadow: 0 8px 20px rgba(127, 29, 29, 0.06);
    }
    .live-edit-frame:hover {
        border-color: var(--primary-color, #c8102e);
        box-shadow: 0 16px 32px rgba(200, 16, 46, 0.13);
        transform: translateY(-2px);
    }
    .edit-frame-badge {
        position: absolute;
        top: 14px;
        right: 14px;
        background-color: #dc2626;
        color: #ffffff;
        padding: 7px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        z-index: 50;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 8px 18px rgba(200, 16, 46, 0.22);
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
        background-color: rgba(15, 23, 42, 0.58);
        backdrop-filter: blur(8px);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .fb-modal-content {
        background: #ffffff;
        width: 100%;
        max-width: 720px;
        border-radius: 18px;
        box-shadow: 0 24px 60px rgba(69, 10, 10, 0.32);
        overflow: hidden;
        animation: modalSlideUp 0.3s ease-out;
    }
    @keyframes modalSlideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .fb-modal-header {
        background: linear-gradient(135deg, #fff1f2, #ffffff);
        padding: 18px 24px;
        border-bottom: 1px solid #fecaca;
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
        background: #fff7f7;
        border-top: 1px solid #fecaca;
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
        Trang Chủ
    </a>
    <a href="{{ route('admin.live-editor', ['page' => 'layout']) }}" class="live-page-tab {{ $currentPage === 'layout' ? 'active' : '' }}">
        Sắp Xếp Trang Chủ
    </a>
    <a href="{{ route('admin.live-editor', ['page' => 'about']) }}" class="live-page-tab {{ $currentPage === 'about' ? 'active' : '' }}">
        Giới Thiệu
    </a>
    <a href="{{ route('admin.live-editor', ['page' => 'services']) }}" class="live-page-tab {{ $currentPage === 'services' ? 'active' : '' }}">
        Dịch Vụ
    </a>
    <a href="{{ route('admin.live-editor', ['page' => 'contact']) }}" class="live-page-tab {{ $currentPage === 'contact' ? 'active' : '' }}">
        Liên Hệ & Chi Nhánh
    </a>
    <a href="{{ route('admin.live-editor', ['page' => 'vaccines']) }}" class="live-page-tab {{ $currentPage === 'vaccines' ? 'active' : '' }}">
        Vắc Xin CSDL
    </a>
    <a href="{{ route('admin.live-editor', ['page' => 'global']) }}" class="live-page-tab {{ $currentPage === 'global' ? 'active-global' : '' }}" style="margin-left: auto; border: 1px solid #dc2626;">
        Cấu Hình Chung
    </a>
</div>

<div class="admin-section-hint">
    <i data-lucide="mouse-pointer-click" style="width: 18px; height: 18px; flex-shrink: 0; color: var(--primary-color);"></i>
    <div>Chọn trang cần chỉnh, bấm trực tiếp vào từng khung nội dung, sau đó kiểm tra lại và bấm lưu. Các thay đổi chỉ ghi vào database sau khi bạn xác nhận lưu.</div>
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
                    <strong style="color: #dc2626; font-size: 13.5px;">2. Đăng Ký Tiêm Chủng</strong>
                </div>
                <div style="padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc;">
                    <strong style="color: #eaaa00; font-size: 13.5px;">3. Danh Mục Sản Phẩm</strong>
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
            <h2 style="font-size: 26px; font-weight: 800; margin: 12px 0 8px 0; color: #fff;">{{ $settings['about_hero_title'] ?? 'Giới Thiệu Hệ Thống Tiêm Chủng Medicare' }}</h2>
            <p style="color: #94a3b8; font-size: 14.5px; max-width: 650px; margin: 0 auto;">{{ $settings['about_hero_desc'] ?? 'Đơn vị y tế uy tín hàng đầu chuyên cung cấp giải pháp phòng bệnh toàn diện bằng vắc xin chất lượng cao cho trẻ em và người lớn.' }}</p>
        </div>
    </div>

    <!-- Khung 2: Câu chuyện Medicare & Thống kê -->
    <div class="live-edit-frame" onclick="openSettingModal('about_story', 'Câu chuyện & Chỉ số Thống kê', ['about_story_title', 'about_story_desc', 'about_stat_exp', 'about_stat_exp_lbl', 'about_stat_clients', 'about_stat_clients_lbl', 'about_stat_branches', 'about_stat_branches_lbl'])">
        <div class="edit-frame-badge"><i data-lucide="edit-3"></i> Sửa Khung 2: Câu Chuyện & Chỉ Số</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 12px;">
            <h4 style="margin: 0 0 10px 0; color: var(--primary-color); font-size: 18px; font-weight: 700;">{{ $settings['about_story_title'] ?? 'Hành trình Bảo vệ Sức khỏe Cộng đồng' }}</h4>
            <p style="color: #64748b; font-size: 14.5px; margin: 0 0 16px 0; line-height: 1.6;">{{ $settings['about_story_desc'] ?? 'Được thành lập từ năm 2016, Medicare bắt đầu với sứ mệnh...' }}</p>
            <div style="display: flex; gap: 12px; border-top: 1px dashed var(--border-color); padding-top: 12px;">
                <div style="flex: 1; text-align: center; background: var(--bg-main); padding: 8px; border-radius: 8px;">
                    <div style="color: var(--primary-color); font-weight: 800; font-size: 18px;">{{ $settings['about_stat_exp'] ?? '10+' }}</div>
                    <div style="font-size: 11px; color: var(--text-muted);">{{ $settings['about_stat_exp_lbl'] ?? 'Năm Kinh Nghiệm' }}</div>
                </div>
                <div style="flex: 1; text-align: center; background: var(--bg-main); padding: 8px; border-radius: 8px;">
                    <div style="color: var(--primary-color); font-weight: 800; font-size: 18px;">{{ $settings['about_stat_clients'] ?? '50,000+' }}</div>
                    <div style="font-size: 11px; color: var(--text-muted);">{{ $settings['about_stat_clients_lbl'] ?? 'Khách Hàng Tin Tưởng' }}</div>
                </div>
                <div style="flex: 1; text-align: center; background: var(--bg-main); padding: 8px; border-radius: 8px;">
                    <div style="color: var(--primary-color); font-weight: 800; font-size: 18px;">{{ $settings['about_stat_branches'] ?? '02' }}</div>
                    <div style="font-size: 11px; color: var(--text-muted);">{{ $settings['about_stat_branches_lbl'] ?? 'Trung Tâm Tiêm Chủng' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Khung 3: Sứ Mệnh & Tầm Nhìn -->
    <div class="live-edit-frame" onclick="openSettingModal('about_mission', 'Sứ Mệnh & Tầm Nhìn', ['about_mission_title', 'about_mission_desc', 'about_vision_title', 'about_vision_desc'])">
        <div class="edit-frame-badge"><i data-lucide="edit-3"></i> Sửa Khung 3: Sứ Mệnh & Tầm Nhìn</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 12px; display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <h5 style="margin: 0 0 6px 0; color: var(--primary-color); font-size: 15px; font-weight: 700;">{{ $settings['about_mission_title'] ?? 'Sứ Mệnh Của Chúng Tôi' }}</h5>
                <p style="color: #64748b; font-size: 13px; margin: 0; line-height: 1.5;">{{ $settings['about_mission_desc'] ?? 'Mang lại dịch vụ tiêm chủng...' }}</p>
            </div>
            <div>
                <h5 style="margin: 0 0 6px 0; color: #b91c1c; font-size: 15px; font-weight: 700;">{{ $settings['about_vision_title'] ?? 'Tầm Nhìn Phát Triển' }}</h5>
                <p style="color: #64748b; font-size: 13px; margin: 0; line-height: 1.5;">{{ $settings['about_vision_desc'] ?? 'Trở thành hệ thống tiêm chủng...' }}</p>
            </div>
        </div>
    </div>

    <!-- Khung 4: Giá Trị Cốt Lõi 1-3 -->
    <div class="live-edit-frame" onclick="openSettingModal('about_values_1_3', 'Giá trị cốt lõi (1 - 3)', ['about_values_desc', 'about_val1_icon', 'about_val1_title', 'about_val1_desc', 'about_val2_icon', 'about_val2_title', 'about_val2_desc', 'about_val3_icon', 'about_val3_title', 'about_val3_desc'])">
        <div class="edit-frame-badge"><i data-lucide="edit-3"></i> Sửa Khung 4: Giá Trị Cốt Lõi (1 - 3)</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 12px;">
            <p style="color: #64748b; font-size: 13px; margin: 0 0 12px 0;">{{ $settings['about_values_desc'] ?? 'Mọi hoạt động y tế của hệ thống tiêm chủng Medicare...' }}</p>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                <div style="background: var(--bg-main); padding: 8px; border-radius: 8px; font-size: 12px;">
                    <i data-lucide="{{ $settings['about_val1_icon'] ?? 'shield-check' }}" style="width: 14px; height: 14px; color: var(--primary-color); display: inline-block; vertical-align: middle; margin-right: 4px;"></i>
                    <strong>1. {{ $settings['about_val1_title'] ?? 'An Toàn Vượt Trội' }}</strong>
                </div>
                <div style="background: var(--bg-main); padding: 8px; border-radius: 8px; font-size: 12px;">
                    <i data-lucide="{{ $settings['about_val2_icon'] ?? 'award' }}" style="width: 14px; height: 14px; color: var(--primary-color); display: inline-block; vertical-align: middle; margin-right: 4px;"></i>
                    <strong>2. {{ $settings['about_val2_title'] ?? 'Uy Tín Hàng Đầu' }}</strong>
                </div>
                <div style="background: var(--bg-main); padding: 8px; border-radius: 8px; font-size: 12px;">
                    <i data-lucide="{{ $settings['about_val3_icon'] ?? 'heart' }}" style="width: 14px; height: 14px; color: var(--primary-color); display: inline-block; vertical-align: middle; margin-right: 4px;"></i>
                    <strong>3. {{ $settings['about_val3_title'] ?? 'Tận Tâm Phục Vụ' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Khung 5: Giá Trị Cốt Lõi 4-6 -->
    <div class="live-edit-frame" onclick="openSettingModal('about_values_4_6', 'Giá trị cốt lõi (4 - 6)', ['about_val4_icon', 'about_val4_title', 'about_val4_desc', 'about_val5_icon', 'about_val5_title', 'about_val5_desc', 'about_val6_icon', 'about_val6_title', 'about_val6_desc'])">
        <div class="edit-frame-badge"><i data-lucide="edit-3"></i> Sửa Khung 5: Giá Trị Cốt Lõi (4 - 6)</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 12px;">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                <div style="background: var(--bg-main); padding: 8px; border-radius: 8px; font-size: 12px;">
                    <i data-lucide="{{ $settings['about_val4_icon'] ?? 'snowflake' }}" style="width: 14px; height: 14px; color: var(--primary-color); display: inline-block; vertical-align: middle; margin-right: 4px;"></i>
                    <strong>4. {{ $settings['about_val4_title'] ?? 'Hệ Thống Lạnh GSP' }}</strong>
                </div>
                <div style="background: var(--bg-main); padding: 8px; border-radius: 8px; font-size: 12px;">
                    <i data-lucide="{{ $settings['about_val5_icon'] ?? 'scale' }}" style="width: 14px; height: 14px; color: var(--primary-color); display: inline-block; vertical-align: middle; margin-right: 4px;"></i>
                    <strong>5. {{ $settings['about_val5_title'] ?? 'Trách Nhiệm Xã Hội' }}</strong>
                </div>
                <div style="background: var(--bg-main); padding: 8px; border-radius: 8px; font-size: 12px;">
                    <i data-lucide="{{ $settings['about_val6_icon'] ?? 'database' }}" style="width: 14px; height: 14px; color: var(--primary-color); display: inline-block; vertical-align: middle; margin-right: 4px;"></i>
                    <strong>6. {{ $settings['about_val6_title'] ?? 'Sổ Tiêm Điện Tử' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Khung 6: Quản Lý Đội Ngũ Bác Sĩ & Chuyên Gia -->
    <div class="live-edit-frame" onclick="openSettingModal('about_team', 'Đội Ngũ Bác Sĩ & Chuyên Gia', ['about_team_members'])">
        <div class="edit-frame-badge"><i data-lucide="edit-3"></i> Sửa Khung 6: Quản Lý Đội Ngũ Bác Sĩ & Chuyên Gia</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 12px;">
            <p style="color: #64748b; font-size: 13.5px; margin: 0 0 12px 0;">Nhấp vào để Thêm, Xóa, hoặc Sửa đổi thông tin chi tiết từng Bác sĩ / Điều dưỡng trong đội ngũ nhân sự.</p>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                @php
                    $liveTeam = json_decode($settings['about_team_members'] ?? '[]', true);
                @endphp
                @foreach($liveTeam as $member)
                    <span style="background: var(--bg-main); padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; color: var(--accent-color);">
                        {{ $member['name'] }} ({{ $member['role'] }})
                    </span>
                @endforeach
            </div>
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
                    <strong style="color: #dc2626;">📍 {{ $settings['branch2_name'] ?? 'Chi nhánh 2: Medicare Thới Lai' }}</strong>
                    <div style="font-size: 13px; color: #475569; margin-top: 4px;">Phone: {{ $settings['branch2_phone'] ?? '0932 477 184' }}</div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- ================= TAB KHUNG CHUNG SYSTEM SHELL ================= -->
@if($currentPage === 'global')
    <div class="live-edit-frame" onclick="openSettingModal('global_shell', 'Khung Chung Toàn Hệ Thống', ['site_name', 'brand_title', 'hotline', 'email', 'footer_text'])">
        <div class="edit-frame-badge" style="background: #dc2626;"><i data-lucide="settings-2"></i> Sửa Khung Dùng Chung System Shell</div>
        <div style="padding: 28px; background: #ffffff; border-radius: 12px;">
            <h4 style="margin: 0 0 16px 0; color: #b91c1c; font-size: 13px; text-transform: uppercase; font-weight: 800;">[Khung Dùng Chung: Header, Topbar, Footer, Bong bóng Chat Zalo]</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                <div style="padding: 14px; background: #fff1f2; border: 1px solid #fecaca; border-radius: 8px;">
                    <strong style="color: #991b1b; display: block; margin-bottom: 4px;">Tên Thương Hiệu:</strong>
                    <span>{{ $settings['brand_title'] ?? 'Hệ Thống Tiêm Chủng Medicare' }}</span>
                </div>
                <div style="padding: 14px; background: #fff1f2; border: 1px solid #fecaca; border-radius: 8px;">
                    <strong style="color: #991b1b; display: block; margin-bottom: 4px;">Hotline Tổng:</strong>
                    <span>{{ $settings['hotline'] ?? '0938 60 38 39' }}</span>
                </div>
                <div style="padding: 14px; background: #fff1f2; border: 1px solid #fecaca; border-radius: 8px;">
                    <strong style="color: #991b1b; display: block; margin-bottom: 4px;">Bản Quyền Footer:</strong>
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
                <button type="button" onclick="saveSettingsSubmit()" class="btn-primary" style="padding: 9px 20px; border-radius: 8px; background: #dc2626; color: #fff; border: none; font-weight: 700;">Lưu Cấu Hình</button>
            </div>
        </form>
    </div>
</div>

@include('vaccine::admin.live_editor_modals')

@endsection

@section('scripts')
<script>
    const settingsData = @json($settings);
    let currentTeamMembers = [];
    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function avatarSrc(value) {
        if (!value) {
            return 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=256&q=80';
        }

        if (value.startsWith('http://') || value.startsWith('https://') || value.startsWith('data:') || value.startsWith('/')) {
            return value;
        }

        return '/' + value;
    }

    function isCoreValueIconField(field) {
        return /^about_val[1-6]_icon$/.test(field);
    }

    function defaultCoreValueIcon(field) {
        const defaults = {
            about_val1_icon: 'shield-check',
            about_val2_icon: 'award',
            about_val3_icon: 'heart',
            about_val4_icon: 'snowflake',
            about_val5_icon: 'scale',
            about_val6_icon: 'database'
        };

        return defaults[field] || 'shield-check';
    }

    function renderIconOptions(selectedValue) {
        const options = {
            'shield-check': 'Khiên an toàn',
            'award': 'Huy chương uy tín',
            'heart': 'Trái tim tận tâm',
            'snowflake': 'Kho lạnh',
            'scale': 'Cân bằng giá',
            'database': 'Dữ liệu điện tử',
            'syringe': 'Ống tiêm',
            'stethoscope': 'Ống nghe',
            'user-check': 'Bác sĩ kiểm tra',
            'clock': 'Đúng hẹn',
            'phone-call': 'Tư vấn điện thoại',
            'hospital': 'Cơ sở y tế'
        };

        return Object.entries(options).map(([value, label]) => {
            return `<option value="${value}" ${selectedValue === value ? 'selected' : ''}>${label} (${value})</option>`;
        }).join('');
    }

    function renderTeamItems() {
        const itemsContainer = document.getElementById('teamItemsContainer');
        if (!itemsContainer) {
            return;
        }
        itemsContainer.innerHTML = '';

        if (currentTeamMembers.length === 0) {
            itemsContainer.innerHTML = `
                <div style="padding: 16px; text-align: center; border: 1px dashed #cbd5e1; border-radius: 8px; color: #64748b; font-size: 13px; background: #ffffff;">
                    Chưa có bác sĩ / nhân viên nào. Bấm "Thêm mới" để tạo dữ liệu.
                </div>
            `;
        }
        
        currentTeamMembers.forEach((member, idx) => {
            const itemDiv = document.createElement('div');
            itemDiv.style.background = '#fff';
            itemDiv.style.border = '1px solid #fecaca';
            itemDiv.style.borderRadius = '8px';
            itemDiv.style.padding = '14px';
            itemDiv.style.marginBottom = '12px';
            itemDiv.style.position = 'relative';
            
            if (member._isEditing) {
                // Edit Mode / Add Mode
                itemDiv.innerHTML = `
                    <div style="font-weight: 700; font-size: 13px; color: #b91c1c; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                        <span>${member._isNew ? 'THÊM BÁC SĨ / NHÂN VIÊN' : 'SỬA THÔNG TIN BÁC SĨ / NHÂN VIÊN'}</span>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
                        <div>
                            <label style="font-size:11px; font-weight:700; color:#475569; display:block; margin-bottom:2px;">Họ tên *</label>
                            <input type="text" id="edit_name_${idx}" value="${escapeHtml(member.name)}" style="width:100%; padding:6px; border-radius:4px; border:1px solid #cbd5e1; font-size:12px;" placeholder="Ví dụ: BS. Nguyễn Văn A" required>
                        </div>
                        <div>
                            <label style="font-size:11px; font-weight:700; color:#475569; display:block; margin-bottom:2px;">Chức vụ *</label>
                            <input type="text" id="edit_role_${idx}" value="${escapeHtml(member.role)}" style="width:100%; padding:6px; border-radius:4px; border:1px solid #cbd5e1; font-size:12px;" placeholder="Ví dụ: Giám Đốc Y Khoa" required>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:12px;">
                        <div>
                            <label style="font-size:11px; font-weight:700; color:#475569; display:block; margin-bottom:2px;">Ảnh đại diện (URL)</label>
                            <input type="text" id="edit_avatar_${idx}" value="${escapeHtml(member.avatar)}" style="width:100%; padding:6px; border-radius:4px; border:1px solid #cbd5e1; font-size:12px;" placeholder="URL hình ảnh...">
                        </div>
                        <div>
                            <label style="font-size:11px; font-weight:700; color:#475569; display:block; margin-bottom:2px;">Zalo / Phone</label>
                            <input type="text" id="edit_zalo_${idx}" value="${escapeHtml(member.zalo)}" style="width:100%; padding:6px; border-radius:4px; border:1px solid #cbd5e1; font-size:12px;" placeholder="Ví dụ: 0987654321">
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:90px 1fr; gap:10px; align-items:center; background:#fff1f2; border:1px dashed #fca5a5; border-radius:8px; padding:10px; margin-bottom:12px;">
                        <img id="edit_avatar_preview_${idx}" src="${escapeHtml(avatarSrc(member.avatar))}" style="width:72px; height:72px; border-radius:50%; object-fit:cover; border:2px solid #dc2626; background:#fff;">
                        <div>
                            <label style="font-size:11px; font-weight:700; color:#991b1b; display:block; margin-bottom:5px;">Hoặc chọn ảnh từ thiết bị</label>
                            <input type="file" id="edit_avatar_file_${idx}" accept="image/png,image/jpeg,image/webp,image/gif" onchange="previewTeamAvatarFile(${idx})" style="font-size:12px; max-width:100%;">
                            <div style="font-size:11px; color:#64748b; margin-top:4px;">Hỗ trợ JPG, PNG, WEBP, GIF. Tối đa 4MB.</div>
                        </div>
                    </div>
                    <div style="display:flex; gap:8px; justify-content:flex-end;">
                        <button type="button" onclick="cancelEditTeamItem(${idx})" style="background:#e2e8f0; color:#334155; border:none; padding:5px 12px; border-radius:4px; font-weight:700; font-size:11px; cursor:pointer;">
                            Hủy
                        </button>
                        <button type="button" onclick="${member._isNew ? `confirmAddTeamItem(${idx})` : `saveTeamItemChanges(${idx})`}" style="background:#dc2626; color:#fff; border:none; padding:5px 12px; border-radius:4px; font-weight:700; font-size:11px; cursor:pointer;">
                            ${member._isNew ? 'Xác nhận thêm' : 'Xác nhận sửa'}
                        </button>
                    </div>
                `;
            } else {
                // View Mode
                itemDiv.innerHTML = `
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <img src="${escapeHtml(avatarSrc(member.avatar))}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 1px solid #e2e8f0; background: #f1f5f9;">
                        <div style="flex: 1;">
                            <h4 style="margin: 0 0 2px 0; font-size: 13.5px; font-weight: 700; color: #1e293b;">${escapeHtml(member.name || 'Chưa nhập tên')}</h4>
                            <p style="margin: 0 0 2px 0; font-size: 11.5px; font-weight: 600; color: #b91c1c;">${escapeHtml(member.role || 'Chưa nhập chức vụ')}</p>
                            <p style="margin: 0; font-size: 11px; color: #64748b;">Zalo: ${escapeHtml(member.zalo || 'Chưa nhập')}</p>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <button type="button" onclick="editTeamItem(${idx})" style="background:#dc2626; color:#fff; border:none; padding:4px 10px; border-radius:4px; font-weight:700; font-size:11px; cursor:pointer; text-align:center;">
                                Sửa
                            </button>
                            <button type="button" onclick="deleteTeamItem(event, ${idx})" style="background:#ef4444; color:#fff; border:none; padding:4px 10px; border-radius:4px; font-weight:700; font-size:11px; cursor:pointer; text-align:center;">
                                Xóa
                            </button>
                        </div>
                    </div>
                `;
            }
            
            itemsContainer.appendChild(itemDiv);
        });

        // Cập nhật giá trị vào hidden input
        updateHiddenInput();
    }
    window.renderTeamItems = renderTeamItems;

    function updateHiddenInput() {
        const hiddenInput = document.getElementById('about_team_members_input');
        if (hiddenInput) {
            // Chỉ lưu những phần tử không đang ở trạng thái Add/Edit dở dang và lọc bỏ các trường UI tạm thời
            const filteredList = currentTeamMembers
                .filter(member => !member._isNew && !member._isEditing)
                .map(member => ({
                    name: member.name || '',
                    role: member.role || '',
                    avatar: member.avatar || '',
                    zalo: member.zalo || ''
                }));
            hiddenInput.value = JSON.stringify(filteredList);
        }
    }

    function addTeamItem(e) {
        if (e) e.preventDefault();
        
        // Kiểm tra xem có dòng nào đang ở Edit/Add mode dở dang không
        const isEditingAny = currentTeamMembers.some(m => m._isEditing);
        if (isEditingAny) {
            alert("Vui lòng hoàn thành hoặc hủy bỏ thao tác chỉnh sửa hiện tại trước khi thêm mới!");
            return;
        }

        currentTeamMembers.push({
            name: '',
            role: '',
            avatar: '',
            zalo: '',
            _isEditing: true,
            _isNew: true
        });
        renderTeamItems();
    }
    window.addTeamItem = addTeamItem;

    function previewTeamAvatarFile(idx) {
        const input = document.getElementById(`edit_avatar_file_${idx}`);
        const preview = document.getElementById(`edit_avatar_preview_${idx}`);
        if (!input || !preview || !input.files || !input.files[0]) {
            return;
        }

        const file = input.files[0];
        if (!file.type.startsWith('image/')) {
            alert("Vui lòng chọn đúng file hình ảnh.");
            input.value = '';
            return;
        }

        if (file.size > 4 * 1024 * 1024) {
            alert("Ảnh không được vượt quá 4MB.");
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
    window.previewTeamAvatarFile = previewTeamAvatarFile;

    function readTeamAvatarInput(idx, fallbackValue) {
        const input = document.getElementById(`edit_avatar_file_${idx}`);
        if (!input || !input.files || !input.files[0]) {
            return Promise.resolve(fallbackValue);
        }

        const file = input.files[0];
        if (!file.type.startsWith('image/')) {
            alert("Vui lòng chọn đúng file hình ảnh.");
            return Promise.reject();
        }

        if (file.size > 4 * 1024 * 1024) {
            alert("Ảnh không được vượt quá 4MB.");
            return Promise.reject();
        }

        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = e => resolve(e.target.result);
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    function editTeamItem(idx) {
        // Kiểm tra xem có dòng nào đang ở Edit/Add mode dở dang không
        const isEditingAny = currentTeamMembers.some(m => m._isEditing);
        if (isEditingAny) {
            alert("Vui lòng hoàn thành hoặc hủy bỏ thao tác chỉnh sửa hiện tại trước!");
            return;
        }

        currentTeamMembers[idx]._isEditing = true;
        renderTeamItems();
    }
    window.editTeamItem = editTeamItem;

    function cancelEditTeamItem(idx) {
        if (currentTeamMembers[idx]._isNew) {
            // Nếu là dòng mới thì xóa hẳn đi
            currentTeamMembers.splice(idx, 1);
        } else {
            // Nếu là dòng cũ thì khôi phục về trạng thái xem tĩnh
            currentTeamMembers[idx]._isEditing = false;
        }
        renderTeamItems();
    }
    window.cancelEditTeamItem = cancelEditTeamItem;

    async function confirmAddTeamItem(idx) {
        const nameVal = document.getElementById(`edit_name_${idx}`).value.trim();
        const roleVal = document.getElementById(`edit_role_${idx}`).value.trim();
        const avatarVal = document.getElementById(`edit_avatar_${idx}`).value.trim();
        const zaloVal = document.getElementById(`edit_zalo_${idx}`).value.trim();

        if (!nameVal || !roleVal) {
            alert("Vui lòng nhập đầy đủ Họ tên và Chức vụ!");
            return;
        }

        const confirmMsg = `Bạn có đồng ý THÊM bác sĩ mới với thông tin sau?\n\n` +
                           `- Họ tên: ${nameVal}\n` +
                           `- Chức vụ: ${roleVal}\n` +
                           `- Zalo/Phone: ${zaloVal || 'Chưa nhập'}\n` +
                            `- URL ảnh: ${avatarVal || 'Chưa nhập'}`;

        if (confirm(confirmMsg)) {
            let avatarValue;
            try {
                avatarValue = await readTeamAvatarInput(idx, avatarVal);
            } catch (e) {
                return;
            }

            currentTeamMembers[idx].name = nameVal;
            currentTeamMembers[idx].role = roleVal;
            currentTeamMembers[idx].avatar = avatarValue;
            currentTeamMembers[idx].zalo = zaloVal;
            currentTeamMembers[idx]._isEditing = false;
            currentTeamMembers[idx]._isNew = false;
            
            renderTeamItems();
        }
    }
    window.confirmAddTeamItem = confirmAddTeamItem;

    async function saveTeamItemChanges(idx) {
        const nameVal = document.getElementById(`edit_name_${idx}`).value.trim();
        const roleVal = document.getElementById(`edit_role_${idx}`).value.trim();
        const avatarVal = document.getElementById(`edit_avatar_${idx}`).value.trim();
        const zaloVal = document.getElementById(`edit_zalo_${idx}`).value.trim();

        if (!nameVal || !roleVal) {
            alert("Vui lòng nhập đầy đủ Họ tên và Chức vụ!");
            return;
        }

        const oldMember = currentTeamMembers[idx];
        
        const confirmMsg = `Bạn có đồng ý CẬP NHẬT thông tin bác sĩ này?\n\n` +
                           `[Thông Tin Cũ]:\n` +
                           `- Họ tên: ${oldMember.name || '(Trống)'}\n` +
                           `- Chức vụ: ${oldMember.role || '(Trống)'}\n` +
                           `- Zalo/Phone: ${oldMember.zalo || '(Trống)'}\n\n` +
                           `[Thông Tin Mới]:\n` +
                           `- Họ tên: ${nameVal}\n` +
                           `- Chức vụ: ${roleVal}\n` +
                           `- Zalo/Phone: ${zaloVal || '(Trống)'}`;

        if (confirm(confirmMsg)) {
            let avatarValue;
            try {
                avatarValue = await readTeamAvatarInput(idx, avatarVal);
            } catch (e) {
                return;
            }

            currentTeamMembers[idx].name = nameVal;
            currentTeamMembers[idx].role = roleVal;
            currentTeamMembers[idx].avatar = avatarValue;
            currentTeamMembers[idx].zalo = zaloVal;
            currentTeamMembers[idx]._isEditing = false;
            
            renderTeamItems();
        }
    }
    window.saveTeamItemChanges = saveTeamItemChanges;

    function deleteTeamItem(e, idx) {
        if (e) e.preventDefault();
        const member = currentTeamMembers[idx];
        const confirmMsg = `Bạn có chắc chắn muốn XÓA bác sĩ này khỏi đội ngũ?\n\n` +
                           `- Họ tên: ${member.name || 'Chưa nhập tên'}\n` +
                           `- Chức vụ: ${member.role || 'Chưa nhập chức vụ'}`;

        if (confirm(confirmMsg)) {
            currentTeamMembers.splice(idx, 1);
            renderTeamItems();
        }
    }
    window.deleteTeamItem = deleteTeamItem;

    function openSettingModal(type, title, fields) {
        document.getElementById('settingModalTitle').innerText = 'Chỉnh Sửa Live: ' + title;
        const container = document.getElementById('settingModalFields');
        container.innerHTML = '';

        fields.forEach(field => {
            const val = settingsData[field] || '';
            const fieldGroup = document.createElement('div');
            fieldGroup.style.marginBottom = '14px';

            if (field === 'about_team_members') {
                currentTeamMembers = [];
                try {
                    const parsed = JSON.parse(val || '[]');
                    currentTeamMembers = Array.isArray(parsed) ? parsed.map(m => ({
                        name: m.name || '',
                        role: m.role || '',
                        avatar: m.avatar || '',
                        zalo: m.zalo || '',
                        _isEditing: false,
                        _isNew: false
                    })) : [];
                } catch(e) {
                    currentTeamMembers = [];
                }

                fieldGroup.innerHTML = `
                    <label style="display:block; font-weight:700; font-size:13px; margin-bottom:10px; color:#334155;">Quản lý danh sách bác sĩ & chuyên gia</label>
                    <div id="teamBuilderWrapper" style="border:1px solid #fecaca; border-radius:8px; padding:12px; background:#fff7f7; margin-bottom:14px; max-height: 440px; overflow-y: auto;">
                        <div id="teamItemsContainer"></div>
                        <button type="button" onclick="addTeamItem(event)" style="background:#dc2626; color:#fff; border:none; padding:8px 16px; border-radius:6px; font-weight:700; font-size:12px; cursor:pointer; margin-top:10px; display:inline-flex; align-items:center; gap:4px;">
                            + Thêm mới
                        </button>
                    </div>
                    <p style="margin: -4px 0 12px 0; font-size: 12px; color: #64748b;">Sau khi thêm/sửa/xóa, bấm "Lưu Cấu Hình" để ghi dữ liệu vào database.</p>
                    <input type="hidden" name="about_team_members" id="about_team_members_input">
                `;
                container.appendChild(fieldGroup);
                renderTeamItems();
            } else if (isCoreValueIconField(field)) {
                const selectedIcon = val || defaultCoreValueIcon(field);
                fieldGroup.innerHTML = `
                    <label style="display:block; font-weight:700; font-size:13px; margin-bottom:4px; color:#334155;">Icon [${escapeHtml(field)}]:</label>
                    <select name="${escapeHtml(field)}" class="form-control" style="width:100%; padding:10px; border-radius:8px; border:1px solid #cbd5e1;">
                        ${renderIconOptions(selectedIcon)}
                    </select>
                    <p style="margin:6px 0 0 0; color:#64748b; font-size:12px;">Chọn icon hiển thị trên thẻ giá trị cốt lõi.</p>
                `;
                container.appendChild(fieldGroup);
            } else {
                fieldGroup.innerHTML = `
                    <label style="display:block; font-weight:700; font-size:13px; margin-bottom:4px; color:#334155;">Nội dung [${escapeHtml(field)}]:</label>
                    <textarea name="${escapeHtml(field)}" class="form-control" rows="2" style="width:100%; padding:10px; border-radius:8px; border:1px solid #cbd5e1;">${escapeHtml(val)}</textarea>
                `;
                container.appendChild(fieldGroup);
            }
        });

        document.getElementById('settingModal').style.display = 'flex';
    }

    function saveSettingsSubmit() {
        if (currentTeamMembers.some(member => member._isEditing)) {
            alert("Vui lòng xác nhận hoặc hủy thao tác thêm/sửa đang thực hiện trước khi lưu cấu hình.");
            return;
        }

        updateHiddenInput();

        if (!confirm("Bạn có chắc chắn muốn lưu lại toàn bộ cấu hình thay đổi này không?")) {
            return;
        }
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
                alert(data.message);
                closeModal('settingModal');
                window.location.reload();
            } else {
                alert(data.message || "Không thể lưu cấu hình.");
            }
        })
        .catch(() => {
            alert("Đã xảy ra lỗi khi lưu cấu hình.");
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
