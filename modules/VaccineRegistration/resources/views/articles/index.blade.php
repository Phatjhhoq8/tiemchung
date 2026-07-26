@extends('vaccine::layouts.app')

@section('title', 'Tin Tức và Kiến Thức Y Khoa - Medicare Cờ Đỏ')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/articles.css') }}">
@endsection

@section('content')
<div class="news-catalog-page">
    <!-- Breadcrumb -->
    <div class="news-breadcrumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i data-lucide="chevron-right"></i>
        <span>Tin tức và Kiến thức y khoa</span>
    </div>

    <!-- TẦNG 1: BÁO MỚI HERO BANNER SECTION (LUÔN CỐ ĐỊNH Ở ĐẦU TRANG, 70% Left Main Featured, 30% Right Hot News Column) -->
    @if($hotNews->isNotEmpty())
        @php
            $featuredMain = $hotNews->first();
            $subFeatured = $hotNews->slice(1, 3);
        @endphp

        <section class="news-hero-section">
            <!-- Left 70% Column -->
            <div class="news-hero-left">
                <!-- Main Hero Banner Card -->
                <a href="{{ route('news.show', $featuredMain->slug) }}" class="hero-main-card">
                    <div class="hero-main-image">
                        <img src="{{ asset('images/vaccines/' . ($featuredMain->image ?: 'vaxigrip.jpg')) }}" alt="{{ $featuredMain->title }}" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                    </div>
                    <div class="hero-main-content">
                        <div>
                            <span class="news-category-badge">{{ str_replace('&', 'và', $featuredMain->category) }}</span>
                            <h1 class="hero-main-title">{{ $featuredMain->title }}</h1>
                            <p class="hero-main-excerpt">{{ Str::limit($featuredMain->summary, 140) }}</p>
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
                            <a href="{{ route('news.show', $sub->slug) }}" class="hero-sub-card">
                                <div class="hero-sub-img">
                                    <img src="{{ asset('images/vaccines/' . ($sub->image ?: 'vaxigrip.jpg')) }}" alt="{{ $sub->title }}" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                                </div>
                                <span class="news-category-badge" style="font-size: 10px; padding: 2px 6px; margin-bottom: 4px;">{{ str_replace('&', 'và', $sub->category) }}</span>
                                <h3 class="hero-sub-title">{{ Str::limit($sub->title, 50) }}</h3>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right 30% Hot News Stream Column (Exact 5 Items for Perfect Level Alignment) -->
            <div class="news-hero-right">
                <h2 class="hot-news-header">
                    <i data-lucide="flame" style="width: 18px; height: 18px; color: var(--primary-color, #c8102e);"></i>
                    Tin Nóng và Nổi Bật
                </h2>
                <div class="hot-news-list">
                    @foreach($hotNews->take(5) as $hot)
                        <a href="{{ route('news.show', $hot->slug) }}" class="hot-news-item">
                            <h3 class="hot-news-title">{{ $hot->title }}</h3>
                            <div class="hot-news-meta">
                                <span><i data-lucide="clock" style="width: 12px; height: 12px;"></i> {{ $hot->created_at ? $hot->created_at->format('d/m/Y') : '26/07/2026' }}</span>
                                <span><i data-lucide="eye" style="width: 12px; height: 12px;"></i> {{ number_format($hot->views) }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- TẦNG 2: THANH MENU NAV CHUYÊN MỤC VÀ THANH TÌM KIẾM THÔNG MINH -->
    <section class="news-nav-bar-container">
        <!-- Search Row -->
        <div class="news-search-row">
            <form action="{{ route('news.index') }}" method="GET" class="news-search-form">
                <i data-lucide="search" style="width: 18px; height: 18px; color: #94a3b8;"></i>
                <input type="text" name="search" placeholder="Nhập từ khóa tìm kiếm bài viết y khoa..." value="{{ request('search') }}">
                <button type="submit" class="news-search-btn">
                    <span>Tìm kiếm</span>
                </button>
            </form>

            @if(request('search') || request('category'))
                <a href="{{ route('news.index') }}" class="news-reset-btn">
                    <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                    <span>Xóa bộ lọc</span>
                </a>
            @endif
        </div>

        <div class="news-nav-divider"></div>

        <!-- Sub-Page Navigation Tabs (No '&' character) -->
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

    <!-- TẦNG 3: LƯỚI BÀI VIẾT CHÍNH (12 Bài / Trang, 100% Đồng bộ style với thẻ Vắc Xin) -->
    <section>
        <div class="news-grid-catalog">
            @forelse($articles as $article)
                <a href="{{ route('news.show', $article->slug) }}" class="news-card-consistent">
                    <div class="news-card-media">
                        <img src="{{ asset('images/vaccines/' . ($article->image ?: 'vaxigrip.jpg')) }}" alt="{{ $article->title }}" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                    </div>
                    <div class="news-card-content">
                        <div>
                            <span class="news-card-cat-label">{{ str_replace('&', 'và', $article->category) }}</span>
                            <h3 class="news-card-heading">{{ $article->title }}</h3>
                            <p class="news-card-snippet">{{ Str::limit($article->summary, 100) }}</p>
                        </div>
                        <div class="news-card-bottom-meta">
                            <span><i data-lucide="calendar" style="width: 13px; height: 13px;"></i> {{ $article->created_at ? $article->created_at->format('d/m/Y') : '26/07/2026' }}</span>
                            <span><i data-lucide="eye" style="width: 13px; height: 13px;"></i> {{ number_format($article->views) }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #94a3b8; background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <i data-lucide="file-search" style="width: 40px; height: 40px; color: #cbd5e1; margin-bottom: 12px;"></i>
                    <p style="font-size: 15px; margin: 0; font-weight: 500;">Không tìm thấy bài viết phù hợp với từ khóa hoặc chuyên mục hiện tại.</p>
                </div>
            @endforelse
        </div>

        <!-- Dynamic Pagination (100% Consistent with Catalog Pagination) -->
        <div class="catalog-pagination">
            {{ $articles->links() }}
        </div>
    </section>

    <!-- TẦNG 4: BANNER TƯ VẤN MEDICARE CỜ ĐỎ (Đồng bộ 100% với trang sản phẩm) -->
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
