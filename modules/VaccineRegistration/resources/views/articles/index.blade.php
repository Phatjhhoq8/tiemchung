@extends('vaccine::layouts.app')

@section('title', 'Tin Tức & Kiến Thức Y Khoa - Medicare Cờ Đỏ')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/articles.css') }}">
@endsection

@section('content')
<div class="news-container">
    <!-- Breadcrumb -->
    <div style="margin-bottom: 24px; color: #64748b; font-size: 14px;">
        <a href="{{ route('home') }}" style="color: #64748b; text-decoration: none;">Trang chủ</a> / 
        <span style="color: var(--primary-color); font-weight: 600;">Tin tức & Kiến thức y khoa</span>
    </div>

    <!-- Header Section -->
    <div class="news-header">
        <h1 class="news-header-title">
            <i data-lucide="newspaper" style="width: 28px; height: 28px; color: var(--primary-color);"></i>
            Tin Tức & Kiến Thức Y Khoa
        </h1>
        <p class="news-header-subtitle">Thông tin tiêm chủng chính thống, khuyến cáo phòng bệnh được tham vấn bởi đội ngũ bác sĩ chuyên khoa Medicare Cờ Đỏ.</p>
    </div>

    <!-- Category Pill Filters (Pure PHP Links) -->
    <div class="news-categories-nav">
        <a href="{{ route('news.index') }}" class="category-pill {{ !request('category') ? 'active' : '' }}">
            Tất cả bài viết
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('news.index', ['category' => $cat]) }}" class="category-pill {{ request('category') === $cat ? 'active' : '' }}">
                {{ $cat }}
            </a>
        @endforeach
    </div>

    <!-- Medical Notice Box -->
    <div class="medical-notice-box">
        <i data-lucide="info"></i>
        <p>
            <strong>Thông báo từ Hội đồng Y khoa Medicare:</strong> Kiến thức y học và khuyến cáo tiêm chủng tại đây được biên soạn dựa trên Hướng dẫn chính thức của Bộ Y tế Việt Nam và Tổ chức Y tế Thế giới (WHO). Mọi thắc mắc cụ thể về thể trạng cá nhân xin vui lòng liên hệ hotline <strong>0938 60 38 39</strong> để được bác sĩ tư vấn trực tiếp.
        </p>
    </div>

    <!-- Featured Article (Displayed on page 1 when no category filter or search) -->
    @if($articles->count() > 0 && !request('category') && !request('search') && $articles->currentPage() == 1)
        @php
            $featured = $articles->first();
        @endphp
        <a href="{{ route('news.show', $featured->slug) }}" class="featured-article-card">
            <div class="featured-image-box">
                <img src="{{ asset('images/vaccines/' . ($featured->image ?: 'vaxigrip.jpg')) }}" alt="{{ $featured->title }}" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
            </div>
            <div class="featured-content-box">
                <div>
                    <span class="featured-badge">{{ $featured->category }}</span>
                    <h2 class="featured-title">{{ $featured->title }}</h2>
                    <p class="featured-summary">{{ Str::limit($featured->summary, 180) }}</p>
                </div>
                <div class="featured-meta">
                    <span><i data-lucide="calendar" style="width: 14px; height: 14px;"></i> {{ $featured->created_at ? $featured->created_at->format('d/m/Y') : '26/07/2026' }}</span>
                    <span><i data-lucide="eye" style="width: 14px; height: 14px;"></i> {{ number_format($featured->views) }} lượt xem</span>
                    <span style="margin-left: auto; color: var(--primary-color); font-weight: 700;">Xem chi tiết →</span>
                </div>
            </div>
        </a>
    @endif

    <!-- 3-Column News Grid -->
    <div class="news-grid">
        @php
            // Skip first article if featured banner is displayed above
            $displayArticles = ($articles->count() > 0 && !request('category') && !request('search') && $articles->currentPage() == 1) ? $articles->slice(1) : $articles;
        @endphp

        @forelse($displayArticles as $article)
            <a href="{{ route('news.show', $article->slug) }}" class="news-card">
                <div class="news-card-image">
                    <img src="{{ asset('images/vaccines/' . ($article->image ?: 'vaxigrip.jpg')) }}" alt="{{ $article->title }}" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                </div>
                <div class="news-card-body">
                    <div>
                        <span class="news-card-category">{{ $article->category }}</span>
                        <h3 class="news-card-title">{{ $article->title }}</h3>
                        <p class="news-card-excerpt">{{ Str::limit($article->summary, 100) }}</p>
                    </div>
                    <div class="news-card-footer">
                        <span><i data-lucide="calendar" style="width: 14px; height: 14px;"></i> {{ $article->created_at ? $article->created_at->format('d/m/Y') : '26/07/2026' }}</span>
                        <span><i data-lucide="eye" style="width: 14px; height: 14px;"></i> {{ number_format($article->views) }}</span>
                    </div>
                </div>
            </a>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #94a3b8; background: #ffffff; border-radius: 4px; border: 1px solid #e2e8f0;">
                <p style="font-size: 16px; margin: 0;">Chưa tìm thấy bài viết phù hợp trong chuyên mục này.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination Wrapper -->
    <div class="news-pagination-wrapper">
        {{ $articles->links() }}
    </div>
</div>
@endsection
