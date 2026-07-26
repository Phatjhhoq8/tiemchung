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
                    <span class="article-author-tag"><i data-lucide="shield-check"></i> Đội ngũ Bác sĩ Medicare Cờ Đỏ</span>
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

            <!-- Article Summary / Lead Paragraph Box -->
            @if($article->summary)
                <div class="article-lead-box">
                    <p>{{ $article->summary }}</p>
                </div>
            @endif

            <!-- Rich Text Body Content -->
            <div class="article-body-content">
                {!! $article->content !!}
            </div>

            <!-- Medical Disclaimer Box -->
            <div class="article-disclaimer-box">
                <i data-lucide="info" class="disclaimer-icon"></i>
                <div>
                    <h4>Khuyến Cáo Y Tế Từ Bác Sĩ Medicare Cờ Đỏ:</h4>
                    <p>Thông tin bài viết mang tính chất tham khảo kiến thức y khoa. Phác đồ tiêm chủng cụ thể cần được chỉ định trực tiếp sau khi bác sĩ khám sàng lọc sức khỏe cho người tiêm.</p>
                </div>
            </div>
        </article>

        <!-- Right Sidebar Column (30% Width) -->
        <aside class="article-sidebar" data-aos="fade-left">
            <!-- Widget 1: Related Articles -->
            <div class="sidebar-widget">
                <h3 class="widget-title">
                    <i data-lucide="bookmark" style="width: 18px; height: 18px; color: var(--primary-color, #c8102e);"></i>
                    Bài Viết Liên Quan
                </h3>
                <div class="related-articles-list">
                    @forelse($relatedArticles as $rel)
                        @php
                            $relImg = $rel->image && !str_contains($rel->image, 'logo') ? $rel->image : 'vaxigrip.jpg';
                        @endphp
                        <a href="{{ route('news.show', $rel->slug) }}" class="related-article-card">
                            <div class="related-article-img">
                                <img src="{{ asset('images/vaccines/' . $relImg) }}" alt="{{ $rel->title }}" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/vaxigrip.jpg') }}';">
                            </div>
                            <div class="related-article-info">
                                <span class="news-category-badge" style="font-size: 10px; padding: 2px 6px;">{{ str_replace('&', 'và', $rel->category) }}</span>
                                <h4 class="related-article-title">{{ Str::limit($rel->title, 55) }}</h4>
                                <div class="news-meta-row" style="font-size: 11.5px !important;">
                                    <span><i data-lucide="calendar"></i> {{ $rel->created_at ? $rel->created_at->format('d/m/Y') : '26/07/2026' }}</span>
                                    <span><i data-lucide="eye"></i> {{ number_format($rel->views) }}</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <p style="font-size: 13.5px; color: #94a3b8;">Chưa có bài viết liên quan.</p>
                    @endforelse
                </div>
            </div>

            <!-- Widget 2: Call to Action Booking Card -->
            <div class="sidebar-cta-widget">
                <i data-lucide="calendar-check" class="cta-widget-icon"></i>
                <h3>Đặt Lịch Tiêm Vắc Xin</h3>
                <p>Đăng ký lịch tiêm vắc xin trực tuyến để được ưu tiên khám sàng lọc miễn phí tại hệ thống Medicare Cờ Đỏ.</p>
                <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="cta-widget-btn">
                    Đăng ký tiêm ngay →
                </a>
            </div>
        </aside>
    </div>

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
