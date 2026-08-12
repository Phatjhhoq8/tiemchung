@extends('vaccine::layouts.app')

@section('title', $settings['services_hero_title'] ?? 'Dịch Vụ Tiêm Chủng Toàn Diện - Medicare')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/articles.css') }}">
    <style>
        .services-grid-4 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
            margin-top: 32px;
        }
        .service-card-detailed {
            background: var(--bg-card, #ffffff);
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: 16px;
            padding: 28px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .service-card-detailed:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 75, 143, 0.08);
            border-color: var(--accent-color, #004b8f);
        }
        .service-card-detailed .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(200, 16, 46, 0.08);
            color: var(--primary-color, #c8102e);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .service-card-detailed h3 {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .service-card-detailed p {
            font-size: 14px;
            color: #475569;
            line-height: 1.6;
            text-align: justify;
            flex-grow: 1;
        }
        .promo-banner-detailed {
            background: linear-gradient(135deg, rgba(0, 75, 143, 0.05) 0%, rgba(200, 16, 46, 0.03) 100%);
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: 20px;
            padding: 40px;
            margin-top: 48px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        @media (max-width: 768px) {
            .promo-banner-detailed {
                grid-template-columns: 1fr;
                padding: 24px;
            }
        }
        .promo-item h4 {
            font-size: 16px;
            font-weight: 800;
            color: var(--primary-color, #c8102e);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .promo-item p {
            font-size: 14px;
            color: #334155;
            line-height: 1.6;
            text-align: justify;
        }
        .commit-section {
            border-top: 1px solid var(--border-color, #e2e8f0);
            padding-top: 48px;
            margin-top: 48px;
        }
        .commit-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 32px;
            margin-top: 32px;
        }
        .commit-card {
            display: flex;
            gap: 16px;
        }
        .commit-card .commit-icon {
            color: #10b981;
            shrink: 0;
        }
        .commit-card h4 {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }
        .commit-card p {
            font-size: 13.5px;
            color: #475569;
            line-height: 1.6;
            text-align: justify;
        }
    </style>
@endsection

@section('content')
<!-- Hero Banner -->
<section class="catalog-hero" data-aos="fade-up" style="background: linear-gradient(135deg, rgba(200, 16, 46, 0.93) 0%, rgba(145, 10, 33, 0.90) 100%), url('https://images.unsplash.com/photo-1579684389782-64d84b5e901a?auto=format&fit=crop&w=1600&q=80') no-repeat center center / cover; margin-top: -2rem;">
    <div class="catalog-hero-container">
        <div class="catalog-hero-content" style="text-align: left;">
            <!-- Breadcrumb inside Hero -->
            <div class="catalog-breadcrumb" style="margin-bottom: 1.2rem; justify-content: flex-start;">
                <a href="{{ route('home') }}">Trang chủ</a>
                <i data-lucide="chevron-right"></i>
                <span>Dịch vụ</span>
            </div>
            <h1 style="color: #fff; font-size: clamp(2rem, 3vw, 2.7rem); font-weight: 800; line-height: 1.25; margin-bottom: 1rem;">
                {{ $settings['services_hero_title'] ?? 'Dịch Vụ Tiêm Chủng Toàn Diện' }}
            </h1>
            <p style="color: rgba(255, 255, 255, 0.94); font-size: 1.05rem; line-height: 1.6; max-width: 680px; margin: 0 0 2rem 0; text-align: justify;">
                {{ $settings['services_hero_desc'] ?? 'Medicare cung cấp giải pháp bảo vệ sức khỏe tối ưu bằng hệ thống vắc xin chính hãng chất lượng cao.' }}
            </p>
            <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="about-hero-btn" style="margin-bottom: 0;">
                <i data-lucide="calendar-check" style="width: 18px; height: 18px;"></i>
                <span>Đặt lịch hẹn tiêm chủng</span>
            </a>
        </div>

        <!-- Cột Phải: Visual Minh họa Y tế (phong cách tin tức) -->
        <div class="catalog-hero-visual" aria-hidden="true" style="display: flex; align-items: center; justify-content: center; height: 100%;">
            <svg viewBox="0 0 300 220" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="shieldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#ffffff" stop-opacity="0.25" />
                        <stop offset="100%" stop-color="#ffffff" stop-opacity="0.05" />
                    </linearGradient>
                </defs>
                <circle cx="150" cy="110" r="85" fill="#ffffff" opacity="0.08" stroke="#ffffff" stroke-width="1.5" stroke-dasharray="4 4"/>
                <g transform="translate(110, 60)">
                    <path d="M 40,5 L 75,15 L 75,45 C 75,65 60,82 40,88 C 20,82 5,65 5,45 L 5,15 Z" fill="url(#shieldGrad)" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M 40,25 L 40,55 M 25,40 L 55,40" stroke="#ffffff" stroke-width="6.5" stroke-linecap="round" />
                    <path d="M 40,25 L 40,55 M 25,40 L 55,40" stroke="var(--primary-color)" stroke-width="3" stroke-linecap="round" />
                </g>
                <g transform="translate(60, 40)">
                    <circle cx="15" cy="15" r="15" fill="#ffffff" opacity="0.2" />
                    <circle cx="15" cy="15" r="12" fill="#ffffff" />
                    <path d="M 11,15 L 14,18 L 20,12" stroke="var(--primary-color)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                </g>
            </svg>
        </div>
    </div>
</section>

<div style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    <!-- Dịch vụ của chúng tôi -->
    <section data-aos="fade-up">
        <div style="text-align: center; max-width: 700px; margin: 0 auto;">
            <span class="section-subtitle">Danh mục dịch vụ</span>
            <h2 class="section-title">Các Dịch Vụ Chính Tại Medicare</h2>
            <p style="color: var(--text-muted);">Chúng tôi thiết kế các gói dịch vụ đa dạng để đáp ứng mọi nhu cầu tiêm chủng bảo vệ sức khỏe của gia đình bạn.</p>
        </div>

        @php
            $servicesList = $settings['services_list'] ?? [];
        @endphp

        <div class="services-grid-4">
            @foreach($servicesList as $service)
                <div class="service-card-detailed">
                    <div class="icon-box">
                        <i data-lucide="{{ $service['icon'] ?? 'syringe' }}" style="width: 24px; height: 24px;"></i>
                    </div>
                    <h3>{{ $service['title'] ?? 'Dịch vụ tiêm chủng' }}</h3>
                    <p>{{ $service['desc'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Các chính sách ưu đãi -->
    @php
        $promos = $settings['services_promos'] ?? [];
    @endphp
    @if(count($promos) > 0)
        <section class="promo-banner-detailed" data-aos="fade-up">
            @foreach($promos as $promo)
                <div class="promo-item">
                    <h4>
                        <i data-lucide="gift" style="width: 20px; height: 20px;"></i>
                        <span>{{ $promo['title'] ?? 'Ưu đãi Medicare' }}</span>
                    </h4>
                    <p>{{ $promo['desc'] ?? '' }}</p>
                </div>
            @endforeach
        </section>
    @endif

    <!-- Cam kết chất lượng -->
    <section class="commit-section" data-aos="fade-up">
        <div style="text-align: center; max-width: 700px; margin: 0 auto;">
            <span class="section-subtitle">Chất lượng hàng đầu</span>
            <h2 class="section-title">Cam Kết Y Khoa Từ Medicare</h2>
        </div>

        @php
            $commitments = $settings['services_commitments'] ?? [];
        @endphp

        <div class="commit-grid">
            @foreach($commitments as $commit)
                <div class="commit-card">
                    <div class="commit-icon">
                        <i data-lucide="check-circle-2" style="width: 24px; height: 24px; stroke-width: 2.5;"></i>
                    </div>
                    <div>
                        <h4>{{ $commit['title'] ?? '' }}</h4>
                        <p>{{ $commit['desc'] ?? '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
