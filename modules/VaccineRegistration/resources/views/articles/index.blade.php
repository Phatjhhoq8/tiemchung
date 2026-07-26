@extends('vaccine::layouts.app')

@section('title', 'Tin Tức và Kiến Thức Y Khoa - Medicare Cờ Đỏ')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/articles.css') }}">
@endsection

@section('content')
<div class="news-catalog-page">
    <!-- CATALOG HERO BANNER (Phông nền Đỏ Medicare Red - Đã xóa hoàn toàn hình tròn mờ ::after) -->
    <section class="catalog-hero">
        <div class="catalog-hero-content">
            <!-- Dòng 1: Breadcrumb Chuẩn -->
            <div class="catalog-breadcrumb">
                <a href="{{ route('home') }}">Trang chủ</a>
                <i data-lucide="chevron-right"></i>
                <span>Tin tức và Kiến thức y khoa</span>
            </div>
            
            <!-- Dòng 2: Tag Eyebrow riêng biệt nằm trên H1 -->
            <div>
                <span class="news-hero-eyebrow">Y HỌC VÀ TIÊM CHỦNG CHÍNH THỐNG</span>
            </div>

            <!-- Dòng 3: Tiêu đề H1 -->
            <h1>Tin tức và Kiến thức y khoa</h1>
            
            <!-- Dòng 4: Dòng mô tả -->
            <p>Tra cứu tin tức y học tiêm chủng chính thống, khuyến cáo phòng ngừa bệnh từ đội ngũ bác sĩ chuyên khoa Medicare Cờ Đỏ.</p>
            
            <!-- Dòng 5: Ô tìm kiếm viên thuốc căn giữa -->
            <div class="search-bar-container catalog-search-box">
                <form action="{{ route('news.index') }}" method="GET" class="search-form">
                    <i data-lucide="search" class="search-icon"></i>
                    <input type="text" name="search" placeholder="Nhập từ khóa tìm kiếm bài viết y khoa..." value="{{ request('search') }}">
                    <button type="submit" class="search-btn">Tìm kiếm</button>
                </form>
            </div>
        </div>

        <!-- Cột Phải: Typography Cloud + Hình Mũi Tiêm Chủng & Khiên Y Tế Vector SVG -->
        <div class="news-hero-cloud-visual" aria-label="Đám mây chuyên mục y khoa">
            <!-- Inline Medical Syringe & Shield SVG Graphic Background -->
            <svg class="news-hero-svg-bg" viewBox="0 0 350 250" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Medical Shield Contour -->
                <path d="M175 25C220 25 280 40 300 65C300 140 260 210 175 235C90 210 50 140 50 65C70 40 130 25 175 25Z" fill="#ffffff" opacity="0.08" stroke="#ffffff" stroke-width="2" stroke-dasharray="4 4"/>
                <!-- Syringe Vector Silhouette -->
                <g stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" opacity="0.6">
                    <line x1="175" y1="50" x2="175" y2="75"/>
                    <rect x="160" y="75" width="30" height="90" rx="4" fill="rgba(255,255,255,0.1)"/>
                    <line x1="150" y1="75" x2="200" y2="75"/>
                    <line x1="165" y1="165" x2="185" y2="165"/>
                    <line x1="175" y1="165" x2="175" y2="205"/>
                    <polygon points="175,205 171,215 179,215" fill="#ffffff"/>
                    <!-- Vaccine Drop -->
                    <circle cx="175" cy="40" r="3" fill="var(--secondary-color, #eaaa00)"/>
                </g>
            </svg>

            <!-- Typography Cloud 8 Chuyên Mục Floating -->
            <div class="news-type-cloud">
                @php
                    $cloudWeights = [
                        'cloud-size-xl', 'cloud-size-lg', 'cloud-size-md', 'cloud-size-xl',
                        'cloud-size-lg', 'cloud-size-sm', 'cloud-size-md', 'cloud-size-sm'
                    ];
                @endphp
                @foreach($categories as $index => $cat)
                    @php $sizeClass = $cloudWeights[$index % count($cloudWeights)]; @endphp
                    <a href="{{ route('news.index', ['category' => $cat]) }}" class="news-cloud-item {{ $sizeClass }} {{ request('category') === $cat ? 'active' : '' }}">
                        {{ str_replace('&', 'và', $cat) }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- SUB-PAGE NAVIGATION NAV BAR (8 Chuyên mục căn đều 100%) -->
    <section class="news-nav-bar-container">
        <nav class="news-nav-tabs">
            <a href="{{ route('news.index', array_filter(['search' => request('search')])) }}" class="news-nav-tab {{ !request('category') ? 'active' : '' }}">
                Tất cả bài viết
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('news.index', array_filter(['category' => $cat, 'search' => request('search')])) }}" class="news-nav-tab {{ request('category') === $cat ? 'active' : '' }}">
                    {{ str_replace('&', 'và', $cat) }}
                </a>
            @endforeach
        </nav>
    </section>

    <!-- TẦNG 1: BÁO MỚI HERO SECTION (CỘT BÀI CHÍNH 73% / CỘT TIN NÓNG 27%) -->
    @if($hotNews->isNotEmpty() && !request('search') && !request('category') && $articles->currentPage() == 1)
        @php
            $featuredMain = $hotNews->first();
            $subFeatured = $hotNews->slice(1, 3);
        @endphp

        <section class="news-hero-section">
            <!-- Left Dominant Story Column (73% Width) -->
            <div class="news-hero-left">
                <!-- Main Hero Banner Card -->
                <a href="{{ route('news.show', $featuredMain->slug) }}" class="hero-main-card">
                    <div class="hero-main-image">
                        @php
                            $imgName = $featuredMain->image && !str_contains($featuredMain->image, 'logo') ? $featuredMain->image : 'vaxigrip.jpg';
                        @endphp
                        <img src="{{ asset('images/vaccines/' . $imgName) }}" alt="{{ $featuredMain->title }}" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/vaxigrip.jpg') }}';">
                    </div>
                    <div class="hero-main-content">
                        <div>
                            <span class="news-category-badge">{{ str_replace('&', 'và', $featuredMain->category) }}</span>
                            <h1 class="hero-main-title">{{ $featuredMain->title }}</h1>
                            <p class="hero-main-excerpt">{{ Str::limit($featuredMain->summary, 220) }}</p>
                        </div>
                        <div class="news-meta-row">
                            <span><i data-lucide="calendar" style="width: 13px; height: 13px;"></i> {{ $featuredMain->created_at ? $featuredMain->created_at->format('d/m/Y') : '26/07/2026' }}</span>
                            <span><i data-lucide="eye" style="width: 13px; height: 13px;"></i> {{ number_format($featuredMain->views) }} lượt xem</span>
                            <span style="margin-left: auto; color: var(--primary-color, #c8102e); font-weight: 700;">Đọc tiếp →</span>
                        </div>
                    </div>
                </a>

                <!-- 3 Sub-Featured Cards Grid -->
                @if($subFeatured->isNotEmpty())
                    <div class="hero-sub-grid">
                        @foreach($subFeatured as $sub)
                            @php
                                $subImg = $sub->image && !str_contains($sub->image, 'logo') ? $sub->image : ($loop->index == 0 ? 'hexaxim.jpg' : ($loop->index == 1 ? 'rotarix.jpg' : 'vaxigrip.jpg'));
                            @endphp
                            <a href="{{ route('news.show', $sub->slug) }}" class="hero-sub-card">
                                <div class="hero-sub-img">
                                    <img src="{{ asset('images/vaccines/' . $subImg) }}" alt="{{ $sub->title }}" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/vaxigrip.jpg') }}';">
                                </div>
                                <span class="news-category-badge" style="font-size: 10px; padding: 2px 6px; margin-bottom: 4px;">{{ str_replace('&', 'và', $sub->category) }}</span>
                                <h3 class="hero-sub-title">{{ Str::limit($sub->title, 50) }}</h3>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right Compact Hot News Column (27% Width, 300px Max Width with Ranking Numbers 01..05) -->
            <div class="news-hero-right">
                <h2 class="hot-news-header">
                    <i data-lucide="flame" style="width: 16px; height: 16px; color: var(--primary-color, #c8102e);"></i>
                    Tin Nóng và Nổi Bật
                </h2>
                <div class="hot-news-list">
                    @foreach($hotNews->take(5) as $hot)
                        <a href="{{ route('news.show', $hot->slug) }}" class="hot-news-item">
                            <span class="hot-news-rank">0{{ $loop->iteration }}</span>
                            <div class="hot-news-item-content">
                                <h3 class="hot-news-title">{{ $hot->title }}</h3>
                                <div class="hot-news-meta">
                                    <span><i data-lucide="clock" style="width: 11px; height: 11px;"></i> {{ $hot->created_at ? $hot->created_at->format('d/m/Y') : '26/07/2026' }}</span>
                                    <span><i data-lucide="eye" style="width: 11px; height: 11px;"></i> {{ number_format($hot->views) }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- TẦNG 2: DANH SÁCH BÀI VIẾT THẺ CHỮ NHẬT NẰM NGANG (HORIZONTAL CARDS CHUẨN BÁO MỚI) -->
    <section>
        <div class="news-horizontal-list">
            @forelse($articles as $article)
                @php
                    $cardImg = $article->image && !str_contains($article->image, 'logo') ? $article->image : 'vaxigrip.jpg';
                @endphp
                <a href="{{ route('news.show', $article->slug) }}" class="news-horizontal-card">
                    <div class="news-horizontal-media">
                        <img src="{{ asset('images/vaccines/' . $cardImg) }}" alt="{{ $article->title }}" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/vaxigrip.jpg') }}';">
                    </div>
                    <div class="news-horizontal-content">
                        <div>
                            <span class="news-category-badge">{{ str_replace('&', 'và', $article->category) }}</span>
                            <h2 class="news-horizontal-heading">{{ $article->title }}</h2>
                            <p class="news-horizontal-snippet">{{ Str::limit($article->summary, 160) }}</p>
                        </div>
                        <div class="news-horizontal-bottom-meta">
                            <span><i data-lucide="calendar" style="width: 13.5px; height: 13.5px;"></i> {{ $article->created_at ? $article->created_at->format('d/m/Y') : '26/07/2026' }}</span>
                            <span><i data-lucide="eye" style="width: 13.5px; height: 13.5px;"></i> {{ number_format($article->views) }} lượt xem</span>
                        </div>
                    </div>
                </a>
            @empty
                <div style="text-align: center; padding: 60px; color: #94a3b8; background: #ffffff; border-radius: var(--radius-md, 12px); border: 1px solid #e2e8f0;">
                    <i data-lucide="file-search" style="width: 40px; height: 40px; color: #cbd5e1; margin-bottom: 12px;"></i>
                    <p style="font-size: 15px; margin: 0; font-weight: 500;">Không tìm thấy bài viết phù hợp với từ khóa hoặc chuyên mục hiện tại.</p>
                </div>
            @endforelse
        </div>

        <!-- Dynamic Centered Pill-Shaped Pagination -->
        <div class="news-pagination">
            {{ $articles->links() }}
        </div>
    </section>

    <!-- TẦNG 3: BANNER TƯ VẤN MEDICARE CỜ ĐỎ (Đồng bộ 100% với trang sản phẩm) -->
    <section class="catalog-advice-banner" style="margin-top: 50px;">
        <div>
            <span>Tư vấn y tế và tiêm chủng</span>
            <h2>Bạn cần tư vấn thêm về phác đồ và bài viết?</h2>
            <p>Liên hệ ngay đội ngũ bác sĩ chuyên khoa Medicare Cờ Đỏ để được hỗ trợ trực tiếp và giải đáp mọi thắc mắc.</p>
        </div>
        <a href="tel:0938603839" class="catalog-advice-btn">
            Gọi Hotline 0938 60 38 39 <i data-lucide="phone-call"></i>
        </a>
    </section>
</div>
@endsection
