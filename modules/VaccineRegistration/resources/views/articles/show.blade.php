@extends('vaccine::layouts.app')

@section('title', $article->title . ' - Medicare Cờ Đỏ')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/articles.css') }}">
@endsection

@section('content')
<div class="news-catalog-page article-detail-page">
    <!-- Breadcrumb Standard (Sleek Modern Medical Style) -->
    <nav class="news-breadcrumb-bar" data-aos="fade-down" aria-label="Đường dẫn điều hướng">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-separator"><i data-lucide="chevron-right"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('news.index') }}">Tin tức</a></li>
            <li class="breadcrumb-separator"><i data-lucide="chevron-right"></i></li>
            <li class="breadcrumb-item active" aria-current="page"><span>{{ Str::limit($article->title, 40) }}</span></li>
        </ol>
    </nav>

    <!-- Main Detail 2-Column Layout -->
    <div class="article-detail-layout">
        <!-- Left Main Article Content (70% Width) -->
        <article class="article-main-content" data-aos="fade-up">
            <!-- Category Badge & Header -->
            <div class="article-header">
                <span class="news-category-badge">{{ str_replace('&', 'và', $article->category) }}</span>
                <h1 class="article-detail-title">{{ $article->title }}</h1>
                <div class="article-meta-bar">
                    <span><i data-lucide="calendar"></i> {{ $article->created_at ? $article->created_at->format('d/m/Y') : '26/07/2026' }}</span>
                    <span><i data-lucide="eye"></i> {{ number_format($article->views) }} lượt xem</span>
                </div>
            </div>

            <!-- Featured Cover Image -->
            @if($article->image)
                @php
                    $detailImg = !str_contains($article->image, 'logo') ? $article->image : 'vaxigrip.jpg';
                @endphp
                <div class="article-cover-image">
                    <img src="{{ asset('images/vaccines/' . $detailImg) }}" alt="{{ $article->title }}" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/vaxigrip.jpg') }}';">
                </div>
            @endif

            <!-- Article Summary / Lead Paragraph (No colored box - Pure typography text-align justify) -->
            @if($article->summary)
                <p class="article-lead-text">{{ $article->summary }}</p>
            @endif

            <!-- Mobile Top TOC (Placed directly above article body for fast mobile reading) -->
            <div class="vaccine-toc-widget mobile-article-toc" id="mobileAutoTocWidget">
                <div class="widget-title" onclick="toggleMobileTocAccordion(event)" style="cursor: pointer; justify-content: space-between; display: flex; align-items: center;">
                    <span style="display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="list" style="width: 18px; height: 18px; color: var(--primary-color, #c8102e);"></i>
                        <span>Mục Lục Nội Dung</span>
                    </span>
                    <i data-lucide="chevron-down" id="mobileTocChevronIcon" style="width: 16px; height: 16px; color: #64748b; transition: transform 0.2s ease;"></i>
                </div>
                <nav style="display: flex; flex-direction: column;" id="mobileAutoTocNav">
                    <!-- Dynamic links generated via JS -->
                </nav>
            </div>

            <!-- Rich Text Body Content (Pure text-align justify) -->
            <div class="article-body-content">
                {!! $article->content !!}
            </div>

            <!-- Author Signature at Bottom Right of Article -->
            <div class="article-author-signature">
                <span>Theo Bác sĩ Chuyên khoa Medicare Cờ Đỏ</span>
            </div>
        </article>

        <!-- Right Sidebar Column (30% Width) -->
        <aside class="article-sidebar">
            <div class="sticky-sidebar-container">
                <!-- Widget 0: Dynamic TOC (Mục Lục Nội Dung) -->
                <div class="vaccine-toc-widget" id="autoTocWidget">
                    <div class="widget-title">
                        <i data-lucide="list" style="width: 18px; height: 18px; color: var(--primary-color, #c8102e);"></i>
                        <span>Mục Lục Nội Dung</span>
                    </div>
                    <nav style="display: flex; flex-direction: column;" id="autoTocNav">
                        <!-- Dynamic links generated via JS -->
                    </nav>
                </div>

                <!-- Widget 1: Related Category Articles -->
                <div class="sidebar-widget">
                    <div class="widget-title">
                        <i data-lucide="layers" style="width: 18px; height: 18px; color: var(--primary-color, #c8102e);"></i>
                        <span>Cùng Chuyên Mục</span>
                    </div>
                    @if(isset($relatedArticles) && $relatedArticles->isNotEmpty())
                        <div class="related-articles-list">
                            @foreach($relatedArticles->take(5) as $rel)
                                @php
                                    $relImg = $rel->image && !str_contains($rel->image, 'logo') ? $rel->image : 'vaxigrip.jpg';
                                @endphp
                                <a href="{{ route('news.show', $rel->slug) }}" class="related-article-card">
                                    <div class="related-article-img">
                                        <img src="{{ asset('images/vaccines/' . $relImg) }}" alt="{{ $rel->title }}" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/vaxigrip.jpg') }}';">
                                    </div>
                                    <div class="related-article-info">
                                        <h4 class="related-article-title">{{ Str::limit($rel->title, 55) }}</h4>
                                        <div class="news-meta-row">
                                            <span><i data-lucide="calendar"></i> {{ $rel->created_at ? $rel->created_at->format('d/m/Y') : '26/07/2026' }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Widget 2: CTA Register Button -->
                <div class="sidebar-cta-widget">
                    <i data-lucide="calendar-check" class="cta-widget-icon"></i>
                    <h3>Đặt Lịch Tiêm Vắc Xin</h3>
                    <p>Đăng ký lịch tiêm vắc xin trực tuyến để được ưu tiên khám sàng lọc miễn phí tại hệ thống Medicare Cờ Đỏ.</p>
                    <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="cta-widget-btn">
                        Đăng ký tiêm ngay →
                    </a>
                </div>
            </div>
        </aside>
    </div>


    <!-- Multi-Topic Recommended Articles Section Below Article (Phân trang đề xuất đa chủ đề) -->
    <section class="suggested-news-section" data-aos="fade-up" style="margin-top: 50px;">
        <div class="suggested-news-header">
            <h2>
                <i data-lucide="flame" style="width: 22px; height: 22px; color: var(--primary-color, #c8102e);"></i>
                Tin Mới và Đề Xuất Đa Chủ Đề
            </h2>
            <p>Khám phá thêm các bài viết y khoa hấp dẫn khác từ hệ thống tin tức Medicare Cờ Đỏ.</p>
        </div>

        <div class="news-horizontal-list">
            @foreach($suggestedArticles as $sug)
                @php
                    $sugImg = $sug->image && !str_contains($sug->image, 'logo') ? $sug->image : 'vaxigrip.jpg';
                @endphp
                <a href="{{ route('news.show', $sug->slug) }}" class="news-horizontal-card">
                    <div class="news-horizontal-media">
                        <img src="{{ asset('images/vaccines/' . $sugImg) }}" alt="{{ $sug->title }}" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/vaxigrip.jpg') }}';">
                    </div>
                    <div class="news-horizontal-content">
                        <div>
                            <span class="news-category-badge">{{ str_replace('&', 'và', $sug->category) }}</span>
                            <h3 class="news-horizontal-heading">{{ $sug->title }}</h3>
                            <p class="news-horizontal-snippet">{{ Str::limit($sug->summary, 160) }}</p>
                        </div>
                        <div class="news-horizontal-bottom-meta">
                            <span><i data-lucide="calendar"></i> {{ $sug->created_at ? $sug->created_at->format('d/m/Y') : '26/07/2026' }}</span>
                            <span><i data-lucide="eye"></i> {{ number_format($sug->views) }} lượt xem</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Dynamic Centered Pill-Shaped Pagination Links (Max 4 page numbers + ...) -->
        <div class="news-pagination">
            {{ $suggestedArticles->links('partials.pagination') }}
        </div>
    </section>

    <!-- Bottom Advice Banner -->
    <section class="catalog-advice-banner" style="margin-top: 50px;" data-aos="fade-up">
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
