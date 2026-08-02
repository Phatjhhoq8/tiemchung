@extends('vaccine::layouts.app')

@section('title', 'Danh Sách Chi Nhánh & Thông Tin Liên Hệ - Medicare')

@section('content')
<section class="catalog-hero" style="background: linear-gradient(135deg, rgba(200, 16, 46, 0.93) 0%, rgba(145, 10, 33, 0.90) 100%), url('https://images.unsplash.com/photo-1516549655169-df83a0774514?auto=format&fit=crop&w=1600&q=80') no-repeat center center / cover; margin-top: -2rem;">
    <div class="catalog-hero-container">
        <div class="catalog-hero-content">
            <div class="catalog-breadcrumb">
                <a href="{{ route('home') }}">Trang chủ</a>
                <i data-lucide="chevron-right"></i>
                <span>Liên hệ</span>
            </div>
            <h1>Thông tin liên hệ & Chi nhánh</h1>
            <p>Chi nhánh hiện tại: <strong>{{ $currentCenter?->name ?? 'Chưa chọn' }}</strong>. Bạn có thể đổi chi nhánh bên dưới.</p>
        </div>
    </div>
</section>

<style>
    .contact-branch-btn-select {
        background-color: #ffffff !important;
        color: #c8102e !important;
        border: 2px solid #c8102e !important;
        border-radius: 8px !important;
        font-weight: 700 !important;
        transition: all 0.2s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        width: 100%;
        padding: 12px;
        cursor: pointer;
        text-decoration: none;
    }
    .contact-branch-btn-select:hover {
        background-color: #c8102e !important;
        color: #ffffff !important;
        border-color: #c8102e !important;
    }
</style>

<div class="contact-wrapper" style="max-width: 1200px; margin: 0 auto 40px; padding: 0 20px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 28px; margin-bottom: 50px;">
        @foreach($centers as $center)
            @php
                $isSelected = $currentCenter?->id === $center->id;
                $phoneHref = \Modules\VaccineRegistration\Support\CenterContext::phoneHref($center->phone);
            @endphp
            <div style="background: var(--bg-card); border: 2px solid {{ $isSelected ? 'var(--primary-color)' : '#e2e8f0' }}; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 16px rgba(200, 16, 46, 0.08);">
                <div style="padding: 28px;">
                    <span style="background-color: {{ $isSelected ? 'var(--primary-color)' : '#64748b' }}; color: #ffffff; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; margin-bottom: 16px;">
                        {{ $isSelected ? 'Đang chọn' : 'Chi nhánh Medicare' }}
                    </span>
                    <h3 style="font-size: 22px; font-weight: 800; color: #1e293b; margin-top: 0; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="building-2" style="color: var(--primary-color); width: 26px; height: 26px;"></i>
                        {{ $center->name }}
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 16px; font-size: 15px; color: #475569;">
                        <p style="margin: 0; display: flex; align-items: flex-start; gap: 12px; line-height: 1.6;">
                            <i data-lucide="map-pin" style="color: var(--primary-color); width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;"></i>
                            <span><strong>Địa chỉ:</strong> {{ $center->address }}</span>
                        </p>
                        <p style="margin: 0; display: flex; align-items: flex-start; gap: 12px; line-height: 1.6;">
                            <i data-lucide="phone-call" style="color: var(--primary-color); width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;"></i>
                            <span><strong>Hotline / Zalo:</strong> <a href="tel:{{ $phoneHref }}" style="color: var(--primary-color); font-weight: 800; text-decoration: none; font-size: 16px;">{{ $center->phone }}</a></span>
                        </p>
                        <p style="margin: 0; display: flex; align-items: flex-start; gap: 12px; line-height: 1.6;">
                            <i data-lucide="clock" style="color: var(--primary-color); width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;"></i>
                            <span><strong>Giờ làm việc:</strong> {{ $center->working_hours ?: '7:00 – 17:00' }}</span>
                        </p>
                    </div>
                </div>

                @if($center->map_url)
                    <div style="height: 220px; width: 100%; border-top: 1px solid var(--border-color);">
                        <iframe src="{{ $center->map_url }}" width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy"></iframe>
                    </div>
                @endif

                <div style="padding: 20px; background: var(--bg-main); border-top: 1px solid var(--border-color); display: flex; gap: 12px; flex-wrap: wrap;">
                    @if(!$isSelected)
                        <form method="POST" action="{{ route('centers.select') }}" style="flex: 1; min-width: 160px; margin: 0;">
                            @csrf
                            <input type="hidden" name="center_id" value="{{ $center->id }}">
                            <button type="submit" class="contact-branch-btn-select">
                                <i data-lucide="check-circle"></i> Chọn chi nhánh này
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('centers.select') }}" style="flex: 1; min-width: 160px; margin: 0;">
                        @csrf
                        <input type="hidden" name="center_id" value="{{ $center->id }}">
                        <input type="hidden" name="redirect_to" value="register">
                        <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px;">Hẹn tiêm tại đây</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
