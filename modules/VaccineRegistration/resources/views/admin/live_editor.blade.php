@extends('vaccine::layouts.admin')

@section('title', 'Chỉnh Sửa Trực Quan Toàn Bộ Các Trang - Medicare')
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
        outline: none;
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
    /* active-global style merged into active for theme color consistency */

    /* Visual Overlay Frames */
    .live-edit-frame {
        position: relative;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        transition: all 0.2s ease;
        cursor: pointer;
        margin-bottom: 24px;
        background-color: #ffffff;
    }
    .live-edit-frame:hover {
        border-color: var(--primary-color, #c8102e);
        box-shadow: 0 0 20px rgba(200, 16, 46, 0.1);
        background-color: rgba(200, 16, 46, 0.02);
    }
    .edit-frame-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background-color: var(--primary-color, #c8102e);
        color: #ffffff;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 700;
        z-index: 50;
        box-shadow: 0 2px 6px rgba(200, 16, 46, 0.15);
        pointer-events: none;
        transition: background-color 0.2s ease;
    }
    .live-edit-frame:hover .edit-frame-badge {
        background-color: #a00d24;
    }

    /* Modal Styling */
    .fb-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(4px);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .fb-modal-content {
        background: #ffffff;
        width: 100%;
        max-width: 800px;
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.2);
        overflow: hidden;
        animation: modalSlideUp 0.3s ease-out;
        display: flex;
        flex-direction: column;
        max-height: 90vh;
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
        overflow-y: auto;
        flex-grow: 1;
    }
    .fb-modal-footer {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }
    .editor-custom-select {
        width: 100%;
        padding: 10px 32px 10px 12px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        background: #ffffff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") no-repeat right 12px center;
        background-size: 16px;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        cursor: pointer;
        color: #0f172a;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .editor-custom-select:focus {
        outline: none;
        border-color: #004b8f;
        box-shadow: 0 0 0 3px rgba(0, 75, 143, 0.1);
    }

    /* Dynamic Form Cards for JSON Arrays */
    .json-array-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 16px;
        position: relative;
    }
    .json-array-item .btn-remove {
        position: absolute;
        top: 12px;
        right: 12px;
        color: #ef4444;
        cursor: pointer;
        background: none;
        border: none;
        font-weight: 700;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .json-array-item .btn-remove:hover {
        text-decoration: underline;
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
    <a href="{{ route('admin.live-editor', ['page' => 'global']) }}" class="live-page-tab {{ $currentPage === 'global' ? 'active' : '' }}" style="margin-left: auto;">
        Cấu Hình Chung
    </a>
</div>

<!-- ================= TOP ACTION BAR (LƯU TẠM / XEM THỬ / XUẤT BẢN / RESET) ================= -->
<div style="background: #ffffff; padding: 16px 24px; border-radius: 12px; border: 1px solid #cbd5e1; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
    <div>
        <h4 style="margin: 0; font-size: 15px; font-weight: 800; color: #1e293b;">Bảng Điều Khiển Live Editor</h4>
        <p style="margin: 2px 0 0 0; font-size: 12.5px; color: #64748b;">Mọi chỉnh sửa bên dưới đều được tự động lưu vào bản nháp tạm thời.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="button" onclick="actionSettings('reset')" class="btn-secondary" style="padding: 10px 18px; border-radius: 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 6px; cursor: pointer;">
            <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i> Khôi Phục Nháp
        </button>
        <a href="{{ url('/?preview=1') }}" target="_blank" class="btn-secondary-outline" style="padding: 10px 18px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; font-weight: 700; color: #0f172a; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <i data-lucide="eye" style="width: 16px; height: 16px; color: #ca8a04;"></i> Xem Thử (Preview)
        </a>
        <button type="button" onclick="publishAllSettings()" class="btn-primary" style="padding: 10px 22px; border-radius: 8px; background: #c8102e; border: none; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 6px; cursor: pointer; box-shadow: 0 4px 10px rgba(200,16,46,0.15);">
            <i data-lucide="send" style="width: 16px; height: 16px;"></i> Xuất Bản Chính Thức
        </button>
    </div>
</div>

<!-- ================= 1. TAB TRANG CHỦ ================= -->
@if($currentPage === 'home')
    <!-- Khung 2: Thanh 4 Ô Tiện Ích Thao Tác Nhanh (Quick Action Toolbar) -->
    <div class="live-edit-frame" onclick="openSettingModal('quick_toolbar', 'Thanh 4 Tiện Ích Nhanh', ['quick_t1_title', 'quick_t1_sub', 'quick_t2_title', 'quick_t2_sub', 'quick_t3_title', 'quick_t3_sub', 'quick_t4_title', 'quick_t4_sub'])">
        <div class="edit-frame-badge">Sửa Tiện Ích Nhanh</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 10px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
                <div style="padding: 14px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc;">
                    <strong style="color: var(--primary-color, #c8102e);">{{ $settings['quick_t1_title'] }}</strong>
                    <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;">{{ $settings['quick_t1_sub'] }}</div>
                </div>
                <div style="padding: 14px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc;">
                    <strong style="color: #004b8f;">{{ $settings['quick_t2_title'] }}</strong>
                    <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;">{{ $settings['quick_t2_sub'] }}</div>
                </div>
                <div style="padding: 14px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc;">
                    <strong style="color: var(--primary-color, #c8102e);">{{ $settings['quick_t3_title'] }}</strong>
                    <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;">{{ $settings['quick_t3_sub'] }}</div>
                </div>
                <div style="padding: 14px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc;">
                    <strong style="color: #004b8f;">{{ $settings['quick_t4_title'] }}</strong>
                    <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;">{{ $settings['quick_t4_sub'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Khung 3: Quy Trình Tiêm Chủng 5 Bước An Toàn -->
    <div class="live-edit-frame" onclick="openSettingModal('safe_process', 'Quy Trình Tiêm Chủng An Toàn', ['home_safe_process_title', 'home_safe_process_desc', 'home_safe_process'])">
        <div class="edit-frame-badge">Sửa Quy Trình 5 Bước</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 10px;">
            <h4 style="margin: 0 0 10px 0; color: #1e293b; font-size: 17px; font-weight: 800;">{{ $settings['home_safe_process_title'] }}</h4>
            <p style="color: #64748b; font-size: 13.5px; margin: 0;">{{ $settings['home_safe_process_desc'] }}</p>
        </div>
    </div>

    <!-- Khung 4: Ý Kiến Đánh Giá Khách Hàng (Testimonials) -->
    <div class="live-edit-frame" onclick="openSettingModal('testimonials', 'Đánh Giá Của Khách Hàng', ['home_testimonials'])">
        <div class="edit-frame-badge">Sửa Đánh Giá Khách Hàng</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 10px;">
            <h4 style="margin: 0; color: #1e293b; font-size: 17px; font-weight: 800;">Phản hồi & Đánh giá từ Khách hàng</h4>
        </div>
    </div>

    <!-- Khung 5: Hỏi Đáp Thường Gặp (FAQ) -->
    <div class="live-edit-frame" onclick="openSettingModal('faqs', 'Hỏi Đáp Thường Gặp', ['home_faq_title', 'home_faq_desc', 'home_faqs'])">
        <div class="edit-frame-badge">Sửa Hỏi Đáp FAQs</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 10px;">
            <h4 style="margin: 0 0 10px 0; color: #1e293b; font-size: 17px; font-weight: 800;">{{ $settings['home_faq_title'] }}</h4>
            <p style="color: #64748b; font-size: 13.5px; margin: 0;">{{ $settings['home_faq_desc'] }}</p>
        </div>
    </div>
@endif

<!-- ================= 2. TAB TRANG GIỚI THIỆU (/about) ================= -->
@if($currentPage === 'about')
    <!-- Khung 1: Hero Banner Giới Thiệu -->
    <div class="live-edit-frame" onclick="openSettingModal('about_hero', 'Banner Đầu Trang Giới Thiệu', ['about_hero_title', 'about_hero_desc'])">
        <div class="edit-frame-badge">Sửa Banner Giới Thiệu</div>
        <div style="padding: 28px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff; border-radius: 12px; text-align: center;">
            <span style="background-color: var(--primary-color); color: #ffffff; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase;">Về Chúng Tôi</span>
            <h2 style="font-size: 26px; font-weight: 800; margin: 12px 0 8px 0; color: #fff;">{{ $settings['about_hero_title'] }}</h2>
            <p style="color: #94a3b8; font-size: 14.5px; max-width: 650px; margin: 0 auto;">{{ $settings['about_hero_desc'] }}</p>
        </div>
    </div>

    <!-- Khung 2: Câu Chuyện Medicare -->
    <div class="live-edit-frame" onclick="openSettingModal('about_story', 'Câu Chuyện Medicare', ['about_story_title', 'about_story_desc'])">
        <div class="edit-frame-badge">Sửa Câu Chuyện Lịch Sử</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 12px;">
            <h4 style="margin: 0 0 10px 0; color: var(--primary-color); font-size: 18px; font-weight: 700;">{{ $settings['about_story_title'] }}</h4>
            <p style="color: #64748b; font-size: 14px; margin: 0; line-height: 1.6; text-align: justify;">{{ $settings['about_story_desc'] }}</p>
        </div>
    </div>

    <!-- Khung 3: Số Liệu Thống Kê -->
    <div class="live-edit-frame" onclick="openSettingModal('about_stats', 'Chỉ Số Thống Kê', ['about_stat_exp', 'about_stat_exp_lbl', 'about_stat_clients', 'about_stat_clients_lbl', 'about_stat_branches', 'about_stat_branches_lbl'])">
        <div class="edit-frame-badge">Sửa Chỉ Số Thống Kê</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 12px;">
            <h4 style="margin: 0 0 16px 0; color: #475569; font-size: 13.5px; font-weight: 700;">Chỉ Số Thống Kê</h4>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; text-align: center;">
                <div style="padding: 12px; background: #f8fafc; border-radius: 8px;">
                    <div style="font-size: 24px; font-weight: 900; color: var(--primary-color);">{{ $settings['about_stat_exp'] }}</div>
                    <div style="font-size: 12px; color: #64748b;">{{ $settings['about_stat_exp_lbl'] }}</div>
                </div>
                <div style="padding: 12px; background: #f8fafc; border-radius: 8px;">
                    <div style="font-size: 24px; font-weight: 900; color: var(--primary-color);">{{ $settings['about_stat_clients'] }}</div>
                    <div style="font-size: 12px; color: #64748b;">{{ $settings['about_stat_clients_lbl'] }}</div>
                </div>
                <div style="padding: 12px; background: #f8fafc; border-radius: 8px;">
                    <div style="font-size: 24px; font-weight: 900; color: var(--primary-color);">{{ $settings['about_stat_branches'] }}</div>
                    <div style="font-size: 12px; color: #64748b;">{{ $settings['about_stat_branches_lbl'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Khung 4: Sứ Mệnh & Tầm Nhìn & Sáu Giá Trị Vàng -->
    <div class="live-edit-frame" onclick="openSettingModal('about_mission_vision', 'Sứ Mệnh & Tầm Nhìn', ['about_mission_title', 'about_mission_desc', 'about_vision_title', 'about_vision_desc', 'about_values_desc', 'about_val1_title', 'about_val1_desc', 'about_val2_title', 'about_val2_desc', 'about_val3_title', 'about_val3_desc', 'about_val4_title', 'about_val4_desc', 'about_val5_title', 'about_val5_desc', 'about_val6_title', 'about_val6_desc'])">
        <div class="edit-frame-badge">Sửa Sứ Mệnh & Giá Trị</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 12px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <h5 style="font-weight: 800; color: var(--primary-color); margin: 0 0 6px 0;">{{ $settings['about_mission_title'] }}</h5>
                    <p style="font-size: 13px; color: #64748b; margin: 0;">{{ $settings['about_mission_desc'] }}</p>
                </div>
                <div>
                    <h5 style="font-weight: 800; color: var(--primary-color); margin: 0 0 6px 0;">{{ $settings['about_vision_title'] }}</h5>
                    <p style="font-size: 13px; color: #64748b; margin: 0;">{{ $settings['about_vision_desc'] }}</p>
                </div>
            </div>
            <div style="margin-top: 20px; border-top: 1px dashed #cbd5e1; padding-top: 16px;">
                <h6 style="margin: 0 0 10px 0; font-size: 12.5px; text-transform: uppercase; color: #475569; font-weight: 700; letter-spacing: 0.5px;">Sáu Giá Trị Cốt Lõi Vàng</h6>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                    <div style="padding: 10px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <strong style="font-size: 12px; color: var(--primary-color); display: block;">1. {{ $settings['about_val1_title'] }}</strong>
                        <span style="font-size: 11px; color: #64748b; display: block; margin-top: 2px;">{{ Str::limit($settings['about_val1_desc'], 45) }}</span>
                    </div>
                    <div style="padding: 10px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <strong style="font-size: 12px; color: var(--primary-color); display: block;">2. {{ $settings['about_val2_title'] }}</strong>
                        <span style="font-size: 11px; color: #64748b; display: block; margin-top: 2px;">{{ Str::limit($settings['about_val2_desc'], 45) }}</span>
                    </div>
                    <div style="padding: 10px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <strong style="font-size: 12px; color: var(--primary-color); display: block;">3. {{ $settings['about_val3_title'] }}</strong>
                        <span style="font-size: 11px; color: #64748b; display: block; margin-top: 2px;">{{ Str::limit($settings['about_val3_desc'], 45) }}</span>
                    </div>
                    <div style="padding: 10px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <strong style="font-size: 12px; color: var(--primary-color); display: block;">4. {{ $settings['about_val4_title'] }}</strong>
                        <span style="font-size: 11px; color: #64748b; display: block; margin-top: 2px;">{{ Str::limit($settings['about_val4_desc'], 45) }}</span>
                    </div>
                    <div style="padding: 10px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <strong style="font-size: 12px; color: var(--primary-color); display: block;">5. {{ $settings['about_val5_title'] }}</strong>
                        <span style="font-size: 11px; color: #64748b; display: block; margin-top: 2px;">{{ Str::limit($settings['about_val5_desc'], 45) }}</span>
                    </div>
                    <div style="padding: 10px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <strong style="font-size: 12px; color: var(--primary-color); display: block;">6. {{ $settings['about_val6_title'] }}</strong>
                        <span style="font-size: 11px; color: #64748b; display: block; margin-top: 2px;">{{ Str::limit($settings['about_val6_desc'], 45) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Khung 5: Đội Ngũ Bác Sĩ & Chuyên Gia -->
    <div class="live-edit-frame" onclick="openSettingModal('about_team', 'Đội Ngũ Bác Sĩ & Chuyên Gia', ['about_team_members'])">
        <div class="edit-frame-badge">Sửa Đội Ngũ Bác Sĩ</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 12px;">
            <h4 style="margin: 0; color: #1e293b; font-size: 17px; font-weight: 800;">Ban điều hành & Đội ngũ y bác sĩ Medicare</h4>
        </div>
    </div>
@endif

<!-- ================= 3. TAB TRANG DỊCH VỤ (/services) ================= -->
@if($currentPage === 'services')
    <!-- Khung 1: Banner & Giới thiệu dịch vụ -->
    <div class="live-edit-frame" onclick="openSettingModal('services_hero', 'Banner Đầu Trang Dịch Vụ', ['services_hero_title', 'services_hero_desc'])">
        <div class="edit-frame-badge">Sửa Banner Dịch Vụ</div>
        <div style="padding: 28px; background: #ffffff; border-radius: 12px;">
            <h2 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0 0 8px 0;">{{ $settings['services_hero_title'] }}</h2>
            <p style="color: #64748b; font-size: 15px; margin: 0;">{{ $settings['services_hero_desc'] }}</p>
        </div>
    </div>

    <!-- Khung 2: Các danh sách dịch vụ y tế chính, ưu đãi và cam kết -->
    <div class="live-edit-frame" onclick="openSettingModal('services_lists', 'Danh Sách Dịch Vụ & Ưu Đãi', ['services_list', 'services_promos', 'services_commitments'])">
        <div class="edit-frame-badge">Sửa Gói Dịch Vụ & Cam Kết</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 12px;">
            <h4 style="margin: 0; color: #1e293b; font-size: 17px; font-weight: 800;">Chi tiết danh mục dịch vụ tiêm chủng Medicare</h4>
        </div>
    </div>
@endif

<!-- ================= 4. TAB TRANG LIÊN HỆ (/contact) ================= -->
@if($currentPage === 'contact')
    <div class="live-edit-frame" onclick="openSettingModal('contact_hero', 'Thông Tin Đầu Trang Liên Hệ', ['contact_hero_title', 'contact_hero_desc'])">
        <div class="edit-frame-badge">Sửa Banner Liên Hệ</div>
        <div style="padding: 24px; background: #ffffff; border-radius: 12px;">
            <h2 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0 0 8px 0;">{{ $settings['contact_hero_title'] }}</h2>
            <p style="color: #64748b; font-size: 15px; margin: 0;">{{ $settings['contact_hero_desc'] }}</p>
        </div>
    </div>
@endif

@if($currentPage === 'global')
    <div class="live-edit-frame" onclick="openSettingModal('global_shell', 'Khung Chung Toàn Hệ Thống & Footer', ['site_logo', 'site_name', 'brand_title', 'email', 'footer_text', 'footer_company_name', 'footer_sub_title', 'footer_content_manager', 'footer_working_hours', 'footer_info_lines'])">
        <div class="edit-frame-badge">Sửa Cấu Hình Chung & Footer</div>
        <div style="padding: 28px; background: #ffffff; border-radius: 12px;">
            <h4 style="margin: 0 0 16px 0; color: #004b8f; font-size: 13.5px; font-weight: 800;">Thông Tin Dùng Chung Hệ Thống & Footer</h4>
            
            <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 16px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 8px; max-width: 320px;">
                <div style="width: 140px; height: 50px; border: 1px dashed #cbd5e1; border-radius: 6px; display: flex; align-items: center; justify-content: center; background: #ffffff; padding: 4px; overflow: hidden;">
                    <img src="{{ asset($settings['site_logo'] ?? 'images/logo.png') }}" alt="Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
                <div>
                    <strong style="color: #0f172a; font-size: 13.5px; display: block;">Logo hệ thống</strong>
                    <span style="font-size: 11px; color: #64748b;">Đang hoạt động</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                <div style="padding: 14px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px;">
                    <strong style="color: #0369a1; display: block; margin-bottom: 4px;">Tên Thương Hiệu:</strong>
                    <span>{{ $settings['brand_title'] }}</span>
                </div>
                <div style="padding: 14px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px;">
                    <strong style="color: #0369a1; display: block; margin-bottom: 4px;">Bản Quyền Footer:</strong>
                    <span style="font-size: 12.5px;">{{ $settings['footer_text'] }}</span>
                </div>
                <div style="padding: 14px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px;">
                    <strong style="color: #0369a1; display: block; margin-bottom: 4px;">Tên Công Ty Footer:</strong>
                    <span>{{ $settings['footer_company_name'] ?? '' }}</span>
                </div>
                <div style="padding: 14px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px;">
                    <strong style="color: #0369a1; display: block; margin-bottom: 4px;">Giờ Mở Cửa Footer:</strong>
                    <span>{{ $settings['footer_working_hours'] ?? '' }}</span>
                </div>
                <div style="padding: 14px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; grid-column: span 2;">
                    <strong style="color: #0369a1; display: block; margin-bottom: 4px;">Tiêu Đề Phụ Footer:</strong>
                    <span>{{ $settings['footer_sub_title'] ?? '' }}</span>
                </div>
                <div style="padding: 14px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; grid-column: span 2;">
                    <strong style="color: #0369a1; display: block; margin-bottom: 4px;">Chịu Trách Nhiệm Nội Dung:</strong>
                    <span>{{ $settings['footer_content_manager'] ?? '' }}</span>
                </div>
            </div>
            
            <div style="margin-top: 20px; padding: 16px; background: #fafafa; border: 1px dashed #e2e8f0; border-radius: 8px;">
                <strong style="color: #475569; display: block; margin-bottom: 8px; font-size: 13px;">Dòng Thông Tin Pháp Lý Chân Trang (Footer Lines):</strong>
                @php
                    $infoLines = $settings['footer_info_lines'] ?? [];
                    if (!is_array($infoLines)) {
                        $infoLines = json_decode($infoLines, true) ?: [];
                    }
                @endphp
                <ul style="margin: 0; padding-left: 20px; color: #475569; font-size: 13px; line-height: 1.6;">
                    @foreach($infoLines as $line)
                        <li>
                            <code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #0f172a; margin-right: 6px;">{{ $line['icon'] ?? 'shield-check' }}</code>
                            <span>{!! $line['text'] ?? '' !!}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<!-- ================= TAB SẮP XẾP TRANG CHỦ ================= -->
@if($currentPage === 'layout')
    <!-- Chèn lại layout sắp xếp của User trước đó hoạt động hoàn hảo -->
    @include('vaccine::admin.live_editor_layout_tab')
@endif

<!-- MODAL CẤU HÌNH -->
<div id="settingModal" class="fb-modal-overlay">
    <form id="settingForm" class="fb-modal-content" style="display: flex; flex-direction: column; max-height: 90vh; overflow: hidden;">
        @csrf
        <input type="hidden" name="action" id="settingFormAction" value="draft">
        <div class="fb-modal-header">
            <h3 id="settingModalTitle" style="margin: 0; font-size: 17px; font-weight: 700; color: #1e293b;">Chỉnh Sửa Trực Quan Cài Đặt</h3>
            <button type="button" onclick="closeModal('settingModal')" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #94a3b8; font-weight: 500;">&times;</button>
        </div>
        <div class="fb-modal-body" id="settingModalFields"></div>
        <div class="fb-modal-footer">
            <button type="button" onclick="closeModal('settingModal')" class="btn-secondary" style="padding: 10px 18px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; font-weight: 700; color: #475569; cursor: pointer;">Hủy</button>
            <button type="button" onclick="submitSettingsForm('draft')" class="btn-secondary-outline" style="padding: 10px 18px; border-radius: 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 700; color: #004b8f; cursor: pointer;">Lưu Bản Nháp</button>
            <button type="button" onclick="submitSettingsForm('publish')" class="btn-primary" style="padding: 10px 22px; border-radius: 8px; background: #c8102e; border: none; font-weight: 700; color: #fff; cursor: pointer;">Xuất Bản Ngay</button>
        </div>
    </form>
</div>

@include('vaccine::admin.live_editor_modals')

@endsection

@section('scripts')
<script>
    const settingsData = @json($settings);
    const defaultAvatar = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23cbd5e1"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        if (typeof str !== 'string') str = String(str);
        return str.replace(/&/g, '&amp;')
                  .replace(/</g, '&lt;')
                  .replace(/>/g, '&gt;')
                  .replace(/"/g, '&quot;')
                  .replace(/'/g, '&#039;');
    }

    // Schema của các mảng JSON giúp vẽ giao diện nhập liệu trực quan
    const jsonSchemas = {
        'home_safe_process': {
            title: 'Quy trình tiêm chủng an toàn',
            itemTitle: 'Bước',
            fields: [
                { key: 'step', label: 'Thứ tự bước', type: 'text', placeholder: 'Ví dụ: 1' },
                { key: 'title', label: 'Tên bước', type: 'text', placeholder: 'Ví dụ: Khám sàng lọc' },
                { key: 'desc', label: 'Mô tả chi tiết', type: 'textarea', placeholder: 'Nhập nội dung...' }
            ]
        },
        'home_testimonials': {
            title: 'Nhận xét từ khách hàng',
            itemTitle: 'Khách hàng',
            fields: [
                { key: 'name', label: 'Họ tên khách hàng', type: 'text', placeholder: 'Ví dụ: Chị Nguyễn Thảo Vy' },
                { key: 'role', label: 'Mô tả / Vai trò', type: 'text', placeholder: 'Ví dụ: Phụ huynh bé Min (3 tháng)' },
                { key: 'content', label: 'Ý kiến / Nhận xét', type: 'textarea', placeholder: 'Nhập ý kiến đánh giá...' },
                { key: 'avatar', label: 'Ảnh đại diện', type: 'image', placeholder: 'Định dạng jpeg, png, jpg, webp' }
            ]
        },
        'home_faqs': {
            title: 'Câu hỏi thường gặp (FAQ)',
            itemTitle: 'Câu hỏi',
            fields: [
                { key: 'q', label: 'Câu hỏi', type: 'text', placeholder: 'Ví dụ: Trẻ bao nhiêu tháng tuổi bắt đầu tiêm chủng?' },
                { key: 'a', label: 'Câu trả lời giải đáp', type: 'textarea', placeholder: 'Nhập nội dung giải đáp y tế...' }
            ]
        },
        'about_team_members': {
            title: 'Đội ngũ bác sĩ & chuyên gia y khoa',
            itemTitle: 'Thành viên',
            fields: [
                { key: 'name', label: 'Họ và tên bác sĩ', type: 'text', placeholder: 'Ví dụ: ThS. BS. Nguyễn Minh Đức' },
                { key: 'role', label: 'Chức vụ / Chuyên khoa', type: 'text', placeholder: 'Ví dụ: Giám đốc chuyên môn tiêm chủng' },
                { key: 'avatar', label: 'Ảnh đại diện', type: 'image', placeholder: 'Định dạng jpeg, png, jpg, webp' },
                { key: 'zalo', label: 'Số hotline / Zalo liên hệ', type: 'text', placeholder: 'Ví dụ: 0938603839' }
            ]
        },
        'services_list': {
            title: 'Danh sách dịch vụ chính',
            itemTitle: 'Dịch vụ',
            fields: [
                { key: 'title', label: 'Tên dịch vụ', type: 'text', placeholder: 'Ví dụ: Tiêm vắc xin lẻ' },
                { key: 'desc', label: 'Mô tả chi tiết dịch vụ', type: 'textarea', placeholder: 'Nhập nội dung...' },
                { 
                    key: 'icon', 
                    label: 'Biểu tượng hiển thị (Icon)', 
                    type: 'select', 
                    options: [
                        { value: 'syringe', label: 'Ống tiêm (Syringe)' },
                        { value: 'package', label: 'Hộp / Gói vắc xin (Package)' },
                        { value: 'calendar-check', label: 'Lịch hẹn tiêm (Calendar Check)' },
                        { value: 'truck', label: 'Xe tiêm chủng lưu động (Truck)' },
                        { value: 'user-check', label: 'Khám kiểm tra (User Check)' },
                        { value: 'heart', label: 'Trái tim / Tận tâm (Heart)' },
                        { value: 'award', label: 'Giải thưởng / Uy tín (Award)' }
                    ]
                }
            ]
        },
        'services_promos': {
            title: 'Chính sách ưu đãi và hỗ trợ',
            itemTitle: 'Ưu đãi',
            fields: [
                { key: 'title', label: 'Tiêu đề ưu đãi', type: 'text', placeholder: 'Ví dụ: Miễn phí khám sàng lọc' },
                { key: 'desc', label: 'Mô tả ưu đãi chi tiết', type: 'textarea', placeholder: 'Nhập nội dung...' }
            ]
        },
        'services_commitments': {
            title: 'Cam kết chất lượng y khoa',
            itemTitle: 'Cam kết',
            fields: [
                { key: 'title', label: 'Tiêu đề cam kết', type: 'text', placeholder: 'Ví dụ: 100% Vắc xin chính hãng' },
                { key: 'desc', label: 'Nội dung cam kết chi tiết', type: 'textarea', placeholder: 'Nhập nội dung...' }
            ]
        },
        'footer_info_lines': {
            title: 'Dòng thông tin chân trang (Footer)',
            itemTitle: 'Dòng thông tin',
            fields: [
                { 
                    key: 'icon', 
                    label: 'Biểu tượng hiển thị (Icon)', 
                    type: 'select', 
                    options: [
                        { value: 'shield-check', label: 'Giấy phép (Shield Check)' },
                        { value: 'building', label: 'Trụ sở / Địa chỉ (Building)' },
                        { value: 'mail', label: 'Thư điện tử / Email (Mail)' },
                        { value: 'info', label: 'Thông tin chung (Info)' },
                        { value: 'globe', label: 'Website / Địa chỉ mạng (Globe)' }
                    ]
                },
                { key: 'text', label: 'Nội dung dòng', type: 'text', placeholder: 'Ví dụ: Giấy chứng nhận ĐKKD số 0107631488...' }
            ]
        }
    };

    const fieldLabels = {
        'site_logo': 'Logo chính thức hệ thống (site_logo)',
        'site_name': 'Tên viết tắt hệ thống (Site Name)',
        'brand_title': 'Tên thương hiệu chính (Brand Title)',
        'email': 'Email hỗ trợ chung',
        'footer_text': 'Bản quyền chân trang (Footer Copyright)',
        'footer_company_name': 'Tên công ty hiển thị ở chân trang (Footer)',
        'footer_sub_title': 'Tiêu đề phụ / Slogan chân trang (Footer)',
        'footer_content_manager': 'Thông tin người chịu trách nhiệm nội dung (Footer)',
        'footer_working_hours': 'Giờ mở cửa hiển thị ở chân trang (Footer)'
    };

    function openSettingModal(type, title, fields) {
        document.getElementById('settingModalTitle').innerText = 'Chỉnh Sửa Live: ' + title;
        const container = document.getElementById('settingModalFields');
        container.innerHTML = '';

        fields.forEach(field => {
            const val = settingsData[field];

            if (jsonSchemas[field]) {
                // Render giao diện mảng JSON trực quan (Thêm/Xóa dòng)
                renderJsonArrayField(container, field, jsonSchemas[field], val);
            } else {
                // Render ô nhập text/textarea hoặc logo upload thông thường
                const isLongText = field.includes('desc') || field.includes('text') || field.includes('address') || field.includes('values');
                const fieldGroup = document.createElement('div');
                fieldGroup.style.marginBottom = '18px';
                
                let inputHtml = '';
                if (field === 'site_logo') {
                    inputHtml = `
                        <div style="display:flex; align-items:center; gap:16px; background:#f8fafc; border:1px solid #cbd5e1; padding:12px; border-radius:8px;">
                            <div style="width:120px; height:50px; border:1px dashed #cbd5e1; border-radius:6px; background:#fff; display:flex; align-items:center; justify-content:center; overflow:hidden; padding:4px;">
                                <img id="logo-preview-live" src="${val ? (val.startsWith('http') ? val : '/' + val) : '/images/logo.png'}" style="max-width:100%; max-height:100%; object-fit:contain;">
                            </div>
                            <div style="flex:1; display:flex; flex-direction:column; gap:4px;">
                                <input type="file" onchange="uploadSingleLogoLive(this)" accept="image/*" style="font-size:12px; width:100%;">
                                <input type="hidden" name="site_logo" id="site_logo_value_live" value="${val || 'images/logo.png'}">
                            </div>
                        </div>
                    `;
                } else if (isLongText) {
                    inputHtml = `<textarea name="${field}" class="form-control" rows="4" style="width:100%; padding:10px; border-radius:8px; border:1px solid #cbd5e1; font-size:14px; line-height:1.5;">${val || ''}</textarea>`;
                } else {
                    inputHtml = `<input type="text" name="${field}" value="${val || ''}" class="form-control" style="width:100%; padding:10px; border-radius:8px; border:1px solid #cbd5e1; font-size:14px;">`;
                }

                fieldGroup.innerHTML = `
                    <label style="display:block; font-weight:750; font-size:13.5px; margin-bottom:6px; color:#1e293b;">
                        ${fieldLabels[field] || 'Nội dung cấu hình [' + field + ']'}:
                    </label>
                    ${inputHtml}
                `;
                container.appendChild(fieldGroup);
            }
        });

        document.getElementById('settingModal').style.display = 'flex';
        if (window.lucide) { lucide.createIcons(); }
    }

    function renderJsonArrayField(container, fieldName, schema, value) {
        let items = [];
        try {
            if (value) {
                items = typeof value === 'string' ? JSON.parse(value) : value;
            }
        } catch (e) {
            console.error('Lỗi parse JSON field ' + fieldName, e);
        }
        if (!Array.isArray(items)) {
            items = [];
        }

        const wrapper = document.createElement('div');
        wrapper.style.marginBottom = '20px';
        wrapper.innerHTML = `
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; border-bottom:1.5px solid #cbd5e1; padding-bottom:8px;">
                <label style="font-weight:800; font-size:14.5px; color:#0f172a;">${schema.title}</label>
                <button type="button" onclick="addJsonArrayRow('${fieldName}')" class="editor-btn-secondary" style="padding:6px 12px; font-size:12.5px; border-radius:6px; background:#f1f5f9; border:1px solid #cbd5e1; color:#0f172a; font-weight:700; cursor:pointer;">
                    + Thêm ${schema.itemTitle}
                </button>
            </div>
            <div id="json-array-container-${fieldName}"></div>
        `;
        container.appendChild(wrapper);

        const listContainer = document.getElementById(`json-array-container-${fieldName}`);
        items.forEach((item, index) => {
            renderJsonRow(listContainer, fieldName, schema, item, index);
        });
    }

    function renderJsonRow(container, fieldName, schema, itemData, index) {
        const row = document.createElement('div');
        row.className = `json-array-item json-item-row-${fieldName}`;
        row.style.position = 'relative';
        row.style.background = '#f8fafc';
        row.style.border = '1px solid #e2e8f0';
        row.style.borderRadius = '10px';
        row.style.padding = '16px';
        row.style.marginBottom = '12px';

        let fieldsHtml = '';
        schema.fields.forEach(f => {
            const val = itemData ? (itemData[f.key] || '') : '';
            if (f.type === 'textarea') {
                fieldsHtml += `
                    <div style="margin-bottom:10px;">
                        <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:4px;">${f.label}</label>
                        <textarea data-field="${f.key}" class="json-input-${fieldName}" rows="2" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1; font-size:13px;" placeholder="${f.placeholder}">${escapeHtml(val)}</textarea>
                    </div>
                `;
            } else if (f.type === 'select') {
                let optionsHtml = '';
                f.options.forEach(opt => {
                    const selected = val === opt.value ? 'selected' : '';
                    optionsHtml += `<option value="${opt.value}" ${selected}>${opt.label}</option>`;
                });
                fieldsHtml += `
                    <div style="margin-bottom:10px;">
                        <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:4px;">${f.label}</label>
                        <select data-field="${f.key}" class="json-input-${fieldName} editor-custom-select">
                            ${optionsHtml}
                        </select>
                    </div>
                `;
            } else if (f.type === 'image') {
                fieldsHtml += `
                    <div style="margin-bottom:10px;">
                        <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:4px;">${f.label}</label>
                        <div style="display:flex; align-items:center; gap:12px; background:#fff; border:1px solid #cbd5e1; padding:8px; border-radius:6px;">
                            <div style="width:50px; height:50px; border-radius:6px; border:1px solid #cbd5e1; background:#f8fafc; display:flex; align-items:center; justify-content:center; overflow:hidden; padding:2px;">
                                <img class="image-preview-${fieldName}-${f.key}" src="${val || defaultAvatar}" style="max-width:100%; max-height:100%; object-fit:cover;">
                            </div>
                            <div style="flex:1; display:flex; flex-direction:column; gap:4px;">
                                <input type="file" onchange="uploadLiveEditorImage(this, '${fieldName}', '${f.key}')" accept="image/*" style="font-size:12px; width:100%;">
                                <input type="hidden" data-field="${f.key}" value="${escapeHtml(val)}" class="json-input-${fieldName} image-value-${fieldName}-${f.key}">
                            </div>
                        </div>
                    </div>
                `;
            } else {
                fieldsHtml += `
                    <div style="margin-bottom:10px;">
                        <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:4px;">${f.label}</label>
                        <input type="text" data-field="${f.key}" value="${escapeHtml(val)}" class="json-input-${fieldName}" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1; font-size:13px;" placeholder="${f.placeholder}">
                    </div>
                `;
            }
        });

        row.innerHTML = `
            <button type="button" class="btn-remove" onclick="removeJsonArrayRow(this)">
                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Xóa dòng
            </button>
            ${fieldsHtml}
        `;
        container.appendChild(row);
        if (window.lucide) { lucide.createIcons(); }
        return row;
    }

    function addJsonArrayRow(fieldName) {
        const schema = jsonSchemas[fieldName];
        const container = document.getElementById(`json-array-container-${fieldName}`);
        if (!container || !schema) return;
        
        const index = container.children.length;
        const newRow = renderJsonRow(container, fieldName, schema, null, index);
        if (newRow) {
            newRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            const firstInput = newRow.querySelector('input[type="text"], textarea');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 300);
            }
        }
    }

    async function removeJsonArrayRow(btn) {
        const row = btn.closest('.json-array-item');
        if (row) {
            if (await window.AppDialog.confirm('Bạn có chắc chắn muốn xóa dòng này không?')) {
                row.remove();
            }
        }
    }

    function submitSettingsForm(action) {
        document.getElementById('settingFormAction').value = action;
        const form = document.getElementById('settingForm');
        const formData = new FormData(form);

        // Đóng gói dữ liệu JSON động thu được từ form list
        for (const fieldName in jsonSchemas) {
            const container = document.getElementById(`json-array-container-${fieldName}`);
            if (container) {
                const items = [];
                const rows = container.querySelectorAll(`.json-item-row-${fieldName}`);
                rows.forEach(row => {
                    const item = {};
                    const inputs = row.querySelectorAll(`.json-input-${fieldName}`);
                    inputs.forEach(input => {
                        const key = input.getAttribute('data-field');
                        item[key] = input.value;
                    });
                    items.push(item);
                });
                
                // Ghi đè hoặc nối dữ liệu dạng JSON String vào form data để submit lên controller
                formData.set(fieldName, JSON.stringify(items));
            }
        }

        fetch("{{ route('admin.live-editor.settings') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        })
        .then(res => {
            if (res.status === 422) {
                return res.json().then(errData => {
                    throw errData;
                });
            }
            return res.json();
        })
        .then(async data => {
            if (data.success) {
                await window.AppDialog.alert(data.message);
                closeModal('settingModal');
                window.location.reload();
            }
        })
        .catch(async err => {
            console.error(err);
            if (err.errors) {
                let msg = 'Dữ liệu nhập không hợp lệ:\n';
                for (const key in err.errors) {
                    msg += `- ${err.errors[key].join(', ')}\n`;
                }
                await window.AppDialog.alert(msg);
            } else {
                await window.AppDialog.alert("❌ Đã xảy ra lỗi khi lưu cấu hình.");
            }
        });
    }

    async function actionSettings(action, skipConfirm = false) {
        if (!skipConfirm && !await window.AppDialog.confirm('Bạn có chắc chắn muốn thực hiện hành động này không?')) {
            return;
        }

        // Nếu đang ở trang sắp xếp layout, gọi các API layout config tương ứng
        if ("{{ $currentPage }}" === 'layout') {
            let url = "{{ route('admin.live-editor.layout.reset') }}";
            let body = new FormData();
            
            if (action === 'publish') {
                url = "{{ route('admin.live-editor.layout.publish') }}";
                const form = document.getElementById('layoutConfigForm');
                if (form) {
                    body = new FormData(form);
                }
            }

            fetch(url, {
                method: "POST",
                body: body,
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(async data => {
                if (data.success) {
                    await window.AppDialog.alert(data.message);
                    window.location.reload();
                }
            });
            return;
        }

        const formData = new FormData();
        formData.append('action', action);

        fetch("{{ route('admin.live-editor.settings') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        })
        .then(res => res.json())
        .then(async data => {
            if (data.success) {
                await window.AppDialog.alert(data.message);
                window.location.reload();
            }
        });
    }

    async function publishAllSettings() {
        if (await window.AppDialog.confirm('Bạn có chắc chắn muốn xuất bản tất cả cấu hình trang chủ/sắp xếp hiện tại lên trang chính thức?')) {
            await actionSettings('publish', true);
        }
    }

    function uploadLiveEditorImage(fileInput, fieldName, fieldKey) {
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        const row = fileInput.closest('.json-array-item');
        const previewImg = row.querySelector(`.image-preview-${fieldName}-${fieldKey}`);
        const hiddenInput = row.querySelector(`.image-value-${fieldName}-${fieldKey}`);
        
        previewImg.style.opacity = '0.5';

        fetch("{{ route('admin.articles.upload-image') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        })
        .then(res => {
            if (!res.ok) {
                throw new Error('Tải lên ảnh thất bại.');
            }
            return res.json();
        })
        .then(data => {
            if (data.location) {
                previewImg.src = data.location;
                previewImg.style.opacity = '1';
                hiddenInput.value = data.location;
            } else {
                alert('Có lỗi xảy ra: ' + (data.error || 'Không rõ lỗi.'));
                previewImg.style.opacity = '1';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Lỗi tải ảnh lên máy chủ.');
            previewImg.style.opacity = '1';
        });
    }

    function uploadSingleLogoLive(fileInput) {
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        const previewImg = document.getElementById('logo-preview-live');
        const hiddenInput = document.getElementById('site_logo_value_live');
        
        previewImg.style.opacity = '0.5';

        fetch("{{ route('admin.articles.upload-image') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        })
        .then(res => {
            if (!res.ok) {
                throw new Error('Tải lên ảnh thất bại.');
            }
            return res.json();
        })
        .then(data => {
            if (data.location) {
                previewImg.src = data.location;
                previewImg.style.opacity = '1';
                // Lấy đường dẫn tương đối để lưu
                const url = new URL(data.location);
                let path = url.pathname;
                if (path.startsWith('/')) {
                    path = path.substring(1);
                }
                hiddenInput.value = path;
            } else {
                alert('Có lỗi xảy ra: ' + (data.error || 'Không rõ lỗi.'));
                previewImg.style.opacity = '1';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Lỗi tải ảnh lên máy chủ.');
            previewImg.style.opacity = '1';
        });
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
</script>
@endsection