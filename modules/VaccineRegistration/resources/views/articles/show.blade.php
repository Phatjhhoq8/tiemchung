@extends('vaccine::layouts.app')

@section('title', $article->title . ' - Medicare Cờ Đỏ')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/articles.css') }}">
@endsection

@section('content')
<div class="news-catalog-page article-detail-page">
    <!-- Breadcrumb Standard -->
    <div class="news-breadcrumb" data-aos="fade-down">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i data-lucide="chevron-right"></i>
        <a href="{{ route('news.index') }}">Tin tức</a>
        <i data-lucide="chevron-right"></i>
        <span>{{ Str::limit($article->title, 45) }}</span>
    </div>

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
                        Mục Lục Nội Dung
                    </div>
                    <nav style="display: flex; flex-direction: column;" id="autoTocNav">
                        <!-- Generates dynamic links via JS -->
                    </nav>
                </div>

                <!-- Widget 1: Call to Action Booking Card (Justified text) -->
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

    <!-- Option A: Related Articles Grid Section (Bài viết cùng chuyên mục - Enclosed in White Card Container) -->
    @if(isset($relatedArticles) && $relatedArticles->isNotEmpty())
        <section class="suggested-news-section related-card-container" data-aos="fade-up" style="margin-top: 40px;">
            <div class="suggested-news-header" style="margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid var(--primary-color, #c8102e);">
                <h2 style="display: flex; align-items: center; gap: 8px; font-size: 20px; font-weight: 800; color: #0f172a; margin: 0;">
                    <i data-lucide="layers" style="width: 22px; height: 22px; color: var(--accent-color, #004b8f);"></i>
                    Bài Viết Cùng Chuyên Mục: <span style="color: var(--primary-color, #c8102e);">{{ $article->category }}</span>
                </h2>
                <p style="margin: 6px 0 0 0; color: #64748b; font-size: 14px;">Khám phá thêm các bài viết y khoa hữu ích liên quan trực tiếp đến chuyên mục bạn vừa xem.</p>
            </div>

            <div class="related-carousel-grid">
                @foreach($relatedArticles as $rel)
                    @php
                        $relImg = $rel->image && !str_contains($rel->image, 'logo') ? $rel->image : 'vaxigrip.jpg';
                    @endphp
                    <a href="{{ route('news.show', $rel->slug) }}" class="related-carousel-card">
                        <div class="related-carousel-media">
                            <img src="{{ asset('images/vaccines/' . $relImg) }}" alt="{{ $rel->title }}" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/vaxigrip.jpg') }}';">
                            <span class="related-badge">Cùng Chuyên Mục</span>
                        </div>
                        <div class="related-carousel-body">
                            <h3 class="related-carousel-title">{{ Str::limit($rel->title, 55) }}</h3>
                            <div class="related-carousel-meta">
                                <span><i data-lucide="calendar" style="width: 12px; height: 12px;"></i> {{ $rel->created_at ? $rel->created_at->format('d/m/Y') : '26/07/2026' }}</span>
                                <span><i data-lucide="eye" style="width: 12px; height: 12px;"></i> {{ number_format($rel->views) }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

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

