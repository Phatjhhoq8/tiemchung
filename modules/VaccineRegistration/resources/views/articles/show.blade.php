@extends('vaccine::layouts.app')

@section('title', $article->title . ' - Medicare Cờ Đỏ')

@section('content')
<div style="max-width: 900px; margin: 40px auto; padding: 0 20px;">
    <!-- Breadcrumb -->
    <div style="margin-bottom: 24px; color: var(--text-muted); font-size: 14px;">
        <a href="{{ route('home') }}" style="color: var(--text-muted); text-decoration: none;">Trang chủ</a> / 
        <a href="{{ route('news.index') }}" style="color: var(--text-muted); text-decoration: none;">Tin tức y khoa</a> / 
        <span style="color: var(--primary-color); font-weight: 600;">{{ Str::limit($article->title, 40) }}</span>
    </div>

    <!-- Article Header -->
    <div style="margin-bottom: 32px;">
        <span style="font-size: 12px; color: var(--primary-color); font-weight: 700; text-transform: uppercase; background: rgba(200,16,46,0.08); padding: 4px 12px; border-radius: 4px; display: inline-block; margin-bottom: 12px;">{{ $article->category }}</span>
        <h1 style="font-family: 'Roboto', sans-serif; font-size: 32px; font-weight: 800; color: #1e293b; line-height: 1.3; margin-bottom: 16px;">{{ $article->title }}</h1>
        <div style="display: flex; gap: 20px; color: #64748b; font-size: 14px;">
            <span><i data-lucide="calendar" style="width: 16px; height: 16px; vertical-align: middle;"></i> {{ $article->created_at ? $article->created_at->format('d/m/Y') : '21/07/2026' }}</span>
            <span><i data-lucide="eye" style="width: 16px; height: 16px; vertical-align: middle;"></i> {{ $article->views }} lượt xem</span>
        </div>
    </div>

    <!-- Article Featured Image -->
    @if($article->image)
        <div style="border-radius: 16px; overflow: hidden; max-height: 420px; margin-bottom: 36px; border: 1px solid var(--border-color);">
            <img src="{{ asset('images/vaccines/' . $article->image) }}" alt="{{ $article->title }}" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    @endif

    <!-- Summary Box -->
    @if($article->summary)
        <div style="background-color: rgba(200, 16, 46, 0.04); border-left: 4px solid var(--primary-color); padding: 20px 24px; border-radius: 8px; margin-bottom: 36px;">
            <p style="font-size: 16px; font-weight: 600; color: #334155; line-height: 1.6; margin: 0;">{{ $article->summary }}</p>
        </div>
    @endif

    <!-- Article Main Content -->
    <div style="font-size: 16px; line-height: 1.8; color: #334155; margin-bottom: 50px;">
        {!! nl2br(e($article->content)) !!}
    </div>

    <!-- Call to Action Banner -->
    <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff; padding: 36px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
        <div>
            <h3 style="font-size: 20px; font-weight: 800; margin: 0 0 8px 0;">Cần Tư Vấn & Đặt Lịch Tiêm Vắc Xin?</h3>
            <p style="color: #94a3b8; font-size: 14.5px; margin: 0;">Liên hệ ngay Hotline 0938 60 38 39 để được bác sĩ tư vấn phác đồ tiêm chủng phù hợp.</p>
        </div>
        <a href="{{ route('register.show') }}" class="btn-primary" style="padding: 12px 24px;">Đăng ký tiêm ngay</a>
    </div>
</div>
@endsection
