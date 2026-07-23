@extends('vaccine::layouts.app')

@section('title', 'Tin Tức & Kiến Thức Y Khoa - Medicare Cờ Đỏ')

@section('content')
<div style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    <!-- Breadcrumb -->
    <div style="margin-bottom: 24px; color: var(--text-muted); font-size: 14px;">
        <a href="{{ route('home') }}" style="color: var(--text-muted); text-decoration: none;">Trang chủ</a> / 
        <span style="color: var(--primary-color); font-weight: 600;">Tin tức & Kiến thức y khoa</span>
    </div>

    <div class="section-title-wrapper" style="text-align: center; margin-bottom: 40px;">
        <span class="section-badge">Góc Y Khoa</span>
        <h2>Kiến Thức Tiêm Chủng & Khuyến Cáo Y Tế</h2>
        <p>Thông tin y khoa chính thống giúp quý khách chủ động chăm sóc và bảo vệ sức khỏe cho cả gia đình.</p>
    </div>

    <!-- Thanh Lọc Chuyên Mục -->
    <div style="display: flex; gap: 12px; margin-bottom: 36px; flex-wrap: wrap; justify-content: center;">
        <a href="{{ route('news.index') }}" style="padding: 8px 18px; border-radius: 20px; text-decoration: none; font-size: 14px; font-weight: 700; background-color: {{ !request('category') ? 'var(--primary-color)' : 'var(--bg-card)' }}; color: {{ !request('category') ? '#ffffff' : 'var(--text-primary)' }}; border: 1px solid var(--border-color);">Tất cả bài viết</a>
        @foreach($categories as $cat)
            <a href="{{ route('news.index', ['category' => $cat]) }}" style="padding: 8px 18px; border-radius: 20px; text-decoration: none; font-size: 14px; font-weight: 700; background-color: {{ request('category') === $cat ? 'var(--primary-color)' : 'var(--bg-card)' }}; color: {{ request('category') === $cat ? '#ffffff' : 'var(--text-primary)' }}; border: 1px solid var(--border-color);">{{ $cat }}</a>
        @endforeach
    </div>

    <!-- Danh sách Bài Viết Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 32px;">
        @forelse($articles as $article)
            <article style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; display: flex; flex-direction: column;">
                <div style="height: 200px; overflow: hidden;">
                    <a href="{{ route('news.show', $article->slug) }}">
                        <img src="{{ asset('images/vaccines/' . ($article->image ?: 'vaxigrip.jpg')) }}" alt="{{ $article->title }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                    </a>
                </div>
                <div style="padding: 28px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <span style="font-size: 12px; color: var(--primary-color); font-weight: 700; text-transform: uppercase; background: rgba(200,16,46,0.08); padding: 4px 12px; border-radius: 4px;">{{ $article->category }}</span>
                        <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 14px 0 10px 0; line-height: 1.4;">
                            <a href="{{ route('news.show', $article->slug) }}" style="text-decoration: none; color: inherit;">{{ $article->title }}</a>
                        </h3>
                        <p style="color: #64748b; font-size: 14.5px; line-height: 1.6; margin-bottom: 0;">{{ $article->summary }}</p>
                    </div>
                    <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 13px; color: #94a3b8; display: flex; align-items: center; gap: 6px;"><i data-lucide="calendar" style="width: 14px; height: 14px;"></i> {{ $article->created_at ? $article->created_at->format('d/m/Y') : '21/07/2026' }}</span>
                        <a href="{{ route('news.show', $article->slug) }}" style="color: var(--primary-color); font-weight: 700; font-size: 14px; text-decoration: none;">Đọc bài viết →</a>
                    </div>
                </div>
            </article>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #94a3b8;">
                <p style="font-size: 16px;">Chưa tìm thấy bài viết phù hợp.</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 40px; display: flex; justify-content: center;">
        {{ $articles->links() }}
    </div>
</div>
@endsection
