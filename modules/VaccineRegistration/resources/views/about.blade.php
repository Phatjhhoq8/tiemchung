@extends('vaccine::layouts.app')

@section('title', 'Giới Thiệu Hệ Thống Tiêm Chủng Medicare')

@section('content')
<div class="about-wrapper" style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    <!-- Breadcrumb -->
    <div style="margin-bottom: 24px; color: var(--text-muted); font-size: 14px;" data-aos="fade-down">
        <a href="{{ route('home') }}" style="color: var(--text-muted); text-decoration: none;">Trang chủ</a> / 
        <span style="color: var(--primary-color); font-weight: 600;">Giới thiệu phòng khám</span>
    </div>

    <!-- Hero Banner -->
    <div class="about-hero" data-aos="fade-up">
        <span class="about-hero-tag">Về Chúng Tôi</span>
        <h1 class="about-hero-title">{{ $settings['about_hero_title'] ?? 'Giới Thiệu Hệ Thống Tiêm Chủng Medicare' }}</h1>
        <p class="about-hero-desc">{{ $settings['about_hero_desc'] ?? 'Đơn vị y tế uy tín hàng đầu chuyên cung cấp giải pháp phòng bệnh toàn diện bằng vắc xin chất lượng cao cho trẻ em và người lớn.' }}</p>
        <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="about-hero-btn">
            <i data-lucide="calendar-check" style="width: 18px; height: 18px;"></i>
            <span>Đăng ký tiêm ngay</span>
        </a>
    </div>

    <!-- Our Story Section -->
    <section class="about-section">
        <div class="about-grid-2">
            <div data-aos="fade-right">
                <span class="section-subtitle">Câu chuyện Medicare</span>
                <h2 class="section-title">{{ $settings['about_story_title'] ?? 'Hành trình Bảo vệ Sức khỏe Cộng đồng' }}</h2>
                <p class="text-content">
                    {{ $settings['about_story_desc'] ?? 'Được thành lập từ năm 2016, Medicare bắt đầu với sứ mệnh mang dịch vụ tiêm chủng an toàn, chất lượng cao và chi phí hợp lý đến gần hơn với người dân tại các huyện ngoại thành Cần Thơ như Cờ Đỏ và Thới Lai. Trải qua gần 10 năm phát triển, chúng tôi tự hào trở thành điểm tựa sức khỏe đáng tin cậy cho hàng chục ngàn gia đình.' }}
                </p>
                
                <!-- Stat Cards -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-number">{{ $settings['about_stat_exp'] ?? '10+' }}</div>
                        <div class="stat-label">{{ $settings['about_stat_exp_lbl'] ?? 'Năm Kinh Nghiệm' }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ $settings['about_stat_clients'] ?? '50,000+' }}</div>
                        <div class="stat-label">{{ $settings['about_stat_clients_lbl'] ?? 'Khách Hàng Tin Tưởng' }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ $settings['about_stat_branches'] ?? '02' }}</div>
                        <div class="stat-label">{{ $settings['about_stat_branches_lbl'] ?? 'Trung Tâm Tiêm Chủng' }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Graphic Illustration (Our Story) -->
            <div class="illustration-wrapper" data-aos="fade-left">
                <!-- Clean Inline Medical SVG Representation -->
                <svg viewBox="0 0 400 350" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="primaryGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="var(--primary-color)" />
                            <stop offset="100%" stop-color="#a00d24" />
                        </linearGradient>
                        <linearGradient id="accentGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="var(--accent-color)" />
                            <stop offset="100%" stop-color="#00386c" />
                        </linearGradient>
                    </defs>
                    <!-- Background blobs -->
                    <circle cx="200" cy="175" r="130" fill="var(--bg-main)" opacity="0.6"/>
                    <path d="M 280,100 C 330,120 350,180 320,230 C 290,280 210,310 160,280 C 110,250 90,180 120,130 C 150,80 230,80 280,100 Z" fill="rgba(0, 75, 143, 0.05)"/>
                    
                    <!-- Team Meeting Vector Graphic elements -->
                    <!-- Table -->
                    <ellipse cx="200" cy="240" rx="120" ry="25" fill="#e2e8f0" />
                    <ellipse cx="200" cy="235" rx="110" ry="20" fill="var(--bg-card)" stroke="var(--border-color)" stroke-width="2" />
                    
                    <!-- People representations (Stylized Medical/Team Staff) -->
                    <!-- Staff 1 (Left - Accent Color) -->
                    <path d="M 120,210 C 120,180 140,160 150,160 C 160,160 170,180 170,210 Z" fill="url(#accentGrad)" />
                    <circle cx="145" cy="135" r="18" fill="#cbd5e1" />
                    <!-- Doctor coat detail -->
                    <path d="M 140,160 L 145,185 L 150,160" stroke="#ffffff" stroke-width="2" fill="none"/>
                    
                    <!-- Staff 2 (Center - Primary Color) -->
                    <path d="M 175,230 C 175,190 195,170 210,170 C 225,170 245,190 245,230 Z" fill="url(#primaryGrad)" />
                    <circle cx="210" cy="140" r="22" fill="#e2e8f0" />
                    <!-- Stethoscope around neck representation -->
                    <path d="M 198,148 C 198,168 222,168 222,148" stroke="var(--secondary-color)" stroke-width="3" fill="none" />
                    
                    <!-- Staff 3 (Right - Dark Blue) -->
                    <path d="M 250,215 C 250,185 270,165 280,165 C 290,165 300,185 300,215 Z" fill="#0f172a" />
                    <circle cx="275" cy="140" r="18" fill="#cbd5e1" />
                    
                    <!-- Cross medical icon floating -->
                    <g transform="translate(195, 55)">
                        <circle cx="15" cy="15" r="25" fill="rgba(200, 16, 46, 0.08)" />
                        <path d="M 15,5 L 15,25 M 5,15 L 25,15" stroke="var(--primary-color)" stroke-width="6" stroke-linecap="round" />
                    </g>
                </svg>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="about-section">
        <div class="about-grid-2 reverse">
            <!-- Graphic Illustration (Cold chain storage GSP) -->
            <div class="illustration-wrapper" data-aos="fade-right">
                <!-- Clean GSP Cold Chain Storage SVG -->
                <svg viewBox="0 0 400 350" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                    <!-- Cabinet background -->
                    <rect x="120" y="50" width="160" height="250" rx="15" fill="var(--bg-card)" stroke="var(--border-color)" stroke-width="4" />
                    <!-- Glass door frame -->
                    <rect x="135" y="65" width="130" height="220" rx="8" fill="rgba(0, 75, 143, 0.04)" stroke="var(--border-color)" stroke-width="3" />
                    
                    <!-- Internal Shelves & Vaccine Box outlines -->
                    <!-- Shelf 1 -->
                    <line x1="135" y1="120" x2="265" y2="120" stroke="var(--border-color)" stroke-width="2" />
                    <rect x="150" y="90" width="30" height="25" rx="3" fill="var(--primary-color)" opacity="0.8" />
                    <rect x="190" y="95" width="25" height="20" rx="3" fill="var(--accent-color)" opacity="0.8" />
                    <rect x="225" y="90" width="25" height="25" rx="3" fill="var(--secondary-color)" opacity="0.8" />
                    
                    <!-- Shelf 2 -->
                    <line x1="135" y1="185" x2="265" y2="185" stroke="var(--border-color)" stroke-width="2" />
                    <rect x="145" y="155" width="35" height="25" rx="3" fill="var(--accent-color)" opacity="0.8" />
                    <rect x="195" y="150" width="30" height="30" rx="3" fill="var(--primary-color)" opacity="0.8" />
                    
                    <!-- Shelf 3 -->
                    <line x1="135" y1="245" x2="265" y2="245" stroke="var(--border-color)" stroke-width="2" />
                    <rect x="160" y="215" width="30" height="25" rx="3" fill="var(--secondary-color)" opacity="0.8" />
                    <rect x="205" y="215" width="40" height="25" rx="3" fill="var(--accent-color)" opacity="0.8" />
                    
                    <!-- Temperature display badge -->
                    <rect x="175" y="38" width="50" height="20" rx="5" fill="#0f172a" />
                    <text x="200" y="52" fill="#10b981" font-size="11" font-weight="900" text-anchor="middle">2.8 °C</text>
                    
                    <!-- Shield representation for security protection -->
                    <g transform="translate(245, 230)">
                        <circle cx="25" cy="25" r="30" fill="var(--bg-card)" stroke="var(--border-color)" stroke-width="2" />
                        <path d="M 25,12 L 38,17 L 38,28 C 38,37 32,43 25,46 C 18,43 12,37 12,28 L 12,17 Z" fill="var(--accent-color)" />
                        <!-- Check icon inside shield -->
                        <path d="M 19,25 L 23,29 L 31,20" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                    </g>
                </svg>
            </div>
            
            <div data-aos="fade-left">
                <span class="section-subtitle">Sứ Mệnh & Tầm Nhìn</span>
                <h2 class="section-title">Giá Trị Medicare Mang Lại Cho Gia Đình Bạn</h2>
                
                <!-- Mission Box -->
                <div class="mission-box">
                    <div class="mission-icon-wrapper">
                        <i data-lucide="target" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div class="mission-content">
                        <h3>{{ $settings['about_mission_title'] ?? 'Sứ Mệnh Của Chúng Tôi' }}</h3>
                        <p>{{ $settings['about_mission_desc'] ?? 'Mang lại dịch vụ tiêm chủng an toàn tuyệt đối, vaccine chính hãng chất lượng cao với chi phí hợp lý cho mọi gia đình. Giúp cộng đồng chủ động phòng bệnh truyền nhiễm nguy hiểm.' }}</p>
                    </div>
                </div>

                <!-- Vision Box -->
                <div class="mission-box">
                    <div class="mission-icon-wrapper" style="background-color: rgba(200, 16, 46, 0.06); color: var(--primary-color);">
                        <i data-lucide="eye" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div class="mission-content">
                        <h3>{{ $settings['about_vision_title'] ?? 'Tầm Nhìn Phát Triển' }}</h3>
                        <p>{{ $settings['about_vision_desc'] ?? 'Trở thành hệ thống tiêm chủng dịch vụ uy tín hàng đầu Cần Thơ và Đồng bằng sông Cửu Long, không ngừng cải tiến trang thiết bị và ứng dụng sổ tiêm điện tử thông minh.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values Section -->
    <section class="about-section" style="border-top: 1px solid var(--border-color); padding-top: 4rem;">
        <div style="text-align: center; max-width: 700px; margin: 0 auto;" data-aos="fade-up">
            <span class="section-subtitle">Cam kết từ Medicare</span>
            <h2 class="section-title">Sáu Giá Trị Cốt Lõi Vàng</h2>
            <p style="color: var(--text-muted);">{{ $settings['about_values_desc'] ?? 'Mọi hoạt động y tế của hệ thống tiêm chủng Medicare đều tuân thủ các chuẩn mực chất lượng khắt khe nhất để bảo vệ an toàn cho sức khỏe gia đình bạn.' }}</p>
        </div>

        <div class="values-grid">
            <!-- Value 1: An Toàn -->
            <div class="value-card" data-aos="fade-up" data-aos-delay="50">
                <div class="value-icon-box">
                    <i data-lucide="{{ $settings['about_val1_icon'] ?? 'shield-check' }}" style="width: 24px; height: 24px;"></i>
                </div>
                <h3 class="value-title">{{ $settings['about_val1_title'] ?? 'An Toàn Vượt Trội' }}</h3>
                <p class="value-desc">{{ $settings['about_val1_desc'] ?? 'Quy trình tiêm chủng an toàn 5 bước chuẩn Bộ Y tế, 100% bác sĩ khám sàng lọc cẩn thận trước tiêm và theo dõi chặt chẽ sau tiêm.' }}</p>
            </div>

            <!-- Value 2: Uy Tín -->
            <div class="value-card" data-aos="fade-up" data-aos-delay="100">
                <div class="value-icon-box">
                    <i data-lucide="{{ $settings['about_val2_icon'] ?? 'award' }}" style="width: 24px; height: 24px;"></i>
                </div>
                <h3 class="value-title">{{ $settings['about_val2_title'] ?? 'Uy Tín Hàng Đầu' }}</h3>
                <p class="value-desc">{{ $settings['about_val2_desc'] ?? 'Cam kết vắc xin nhập khẩu chính hãng từ các tập đoàn dược phẩm lớn trên thế giới như GSK, MSD, Sanofi Pasteur, Pfizer.' }}</p>
            </div>

            <!-- Value 3: Tận Tâm -->
            <div class="value-card" data-aos="fade-up" data-aos-delay="150">
                <div class="value-icon-box">
                    <i data-lucide="{{ $settings['about_val3_icon'] ?? 'heart' }}" style="width: 24px; height: 24px;"></i>
                </div>
                <h3 class="value-title">{{ $settings['about_val3_title'] ?? 'Tận Tâm Phục Vụ' }}</h3>
                <p class="value-desc">{{ $settings['about_val3_desc'] ?? 'Đội ngũ y bác sĩ, điều dưỡng ân cần, thấu hiểu tâm lý trẻ em và người lớn, tạo cảm giác thân thiện, nhẹ nhàng khi tiêm.' }}</p>
            </div>

            <!-- Value 4: Chất Lượng GSP -->
            <div class="value-card" data-aos="fade-up" data-aos-delay="200">
                <div class="value-icon-box">
                    <i data-lucide="{{ $settings['about_val4_icon'] ?? 'snowflake' }}" style="width: 24px; height: 24px;"></i>
                </div>
                <h3 class="value-title">{{ $settings['about_val4_title'] ?? 'Hệ Thống Lạnh GSP' }}</h3>
                <p class="value-desc">{{ $settings['about_val4_desc'] ?? 'Hệ thống kho lạnh và tủ bảo quản vắc xin đạt chuẩn GSP nghiêm ngặt từ 2 - 8°C giúp giữ trọn vẹn chất lượng và hiệu quả.' }}</p>
            </div>

            <!-- Value 5: Bình Ổn Giá -->
            <div class="value-card" data-aos="fade-up" data-aos-delay="250">
                <div class="value-icon-box">
                    <i data-lucide="{{ $settings['about_val5_icon'] ?? 'scale' }}" style="width: 24px; height: 24px;"></i>
                </div>
                <h3 class="value-title">{{ $settings['about_val5_title'] ?? 'Trách Nhiệm Xã Hội' }}</h3>
                <p class="value-desc">{{ $settings['about_val5_desc'] ?? 'Cung cấp vaccine với mức giá bình ổn, hỗ trợ tối đa người dân tại khu vực Cờ Đỏ & Thới Lai tiếp cận với y tế chất lượng cao.' }}</p>
            </div>

            <!-- Value 6: Công Nghệ Số -->
            <div class="value-card" data-aos="fade-up" data-aos-delay="300">
                <div class="value-icon-box">
                    <i data-lucide="{{ $settings['about_val6_icon'] ?? 'database' }}" style="width: 24px; height: 24px;"></i>
                </div>
                <h3 class="value-title">{{ $settings['about_val6_title'] ?? 'Sổ Tiêm Điện Tử' }}</h3>
                <p class="value-desc">{{ $settings['about_val6_desc'] ?? 'Quản lý lịch sử tiêm chủng đồng bộ trên hệ thống, tự động nhắn tin nhắc lịch tiêm chủng định kỳ cho trẻ đúng hẹn.' }}</p>
            </div>
        </div>
    </section>

    <!-- Meet Our Team Section -->
    <section class="about-section" style="border-top: 1px solid var(--border-color); padding-top: 4rem; margin-bottom: 2rem;">
        <div style="text-align: center; max-width: 700px; margin: 0 auto;" data-aos="fade-up">
            <span class="section-subtitle">Medicare Staff</span>
            <h2 class="section-title">Đội Ngũ Bác Sĩ & Chuyên Gia</h2>
            <p style="color: var(--text-muted);">Đội ngũ nhân sự y khoa chuyên môn cao, giàu kinh nghiệm và tận tâm bảo vệ sức khỏe gia đình bạn.</p>
        </div>

        @php
            $teamMembers = json_decode($settings['about_team_members'] ?? '[]', true);
        @endphp

        <div class="team-grid">
            @foreach($teamMembers as $index => $member)
                @php
                    $avatar = $member['avatar'] ?? 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=256&q=80';
                    if (!str_starts_with($avatar, 'http') && !str_starts_with($avatar, 'data:') && !str_starts_with($avatar, '/')) {
                        $avatar = asset($avatar);
                    }
                @endphp
                <div class="team-card" data-aos="fade-up" data-aos-delay="{{ 50 * (($index % 4) + 1) }}">
                    <div class="team-avatar-wrapper">
                        <img class="team-avatar" src="{{ $avatar }}" alt="{{ $member['name'] }}">
                    </div>
                    <h3 class="team-name">{{ $member['name'] }}</h3>
                    <span class="team-role">{{ $member['role'] }}</span>
                    <div class="team-socials">
                        @if(!empty($member['zalo']))
                        <a href="tel:{{ str_replace([' ', '-', '.'], '', $member['zalo']) }}" class="team-social-link" title="Gọi số điện thoại">
                            <i data-lucide="phone-call" style="width: 16px; height: 16px; stroke-width: 2.5;"></i>
                        </a>
                        @endif
                        <a href="#" class="team-social-link" title="Facebook">
                            <span class="team-facebook-letter">f</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
