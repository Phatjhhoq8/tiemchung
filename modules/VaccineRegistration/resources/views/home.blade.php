@extends('vaccine::layouts.app')

@section('title', 'Hệ Thống Tiêm Chủng Medicare - Đăng Ký Tiêm Chủng')
@section('meta_description', 'Medicare - Hệ thống tiêm chủng vắc xin an toàn, chất lượng hàng đầu tại Cần Thơ. Đặt lịch tiêm chủng trực tuyến cho trẻ em và người lớn.')

@section('content')
    @if(isset($isPreviewMode) && $isPreviewMode)
        <!-- Preview mode simulator bar -->
        <div class="bg-amber-500 text-white font-bold px-4 py-3 shadow-md flex items-center justify-between sticky top-[68px] z-50 animate-pulse">
            <div class="flex items-center gap-2">
                <i data-lucide="info" class="w-5 h-5"></i>
                <span class="text-sm">CHẾ ĐỘ GIẢ LẬP XEM THỬ - Bản nháp chưa xuất bản chính thức</span>
            </div>
            <a href="{{ route('admin.live-editor') }}" class="bg-white text-slate-800 hover:bg-slate-100 px-4 py-1.5 rounded-lg text-xs font-black shadow-sm transition-all">
                Quay lại Admin
            </a>
        </div>
    @endif

    <div class="home-container w-full overflow-hidden flex flex-col gap-16 pb-16">
        <!-- 1. Hero Banner Slider (Always first at the top, not part of reorderable sections) -->
        @include('vaccine::partials.home.hero_slider')

        @foreach($layoutConfig as $key => $section)
            @if($section['is_visible'])
                @if($key === 'quick_booking')
                    @include('vaccine::partials.home.' . $key)
                @else
                    @php
                        $paddingClass = 'py-20';
                        if (($section['padding'] ?? '') === 'compact') {
                            $paddingClass = 'py-12';
                        } elseif (($section['padding'] ?? '') === 'spacious') {
                            $paddingClass = 'py-28';
                        }
                    @endphp
                    <div class="homepage-section-wrapper {{ $section['bg_class'] }} {{ $paddingClass }} transition-all duration-300">
                        @include('vaccine::partials.home.' . $key)
                    </div>
                @endif
            @endif
        @endforeach
    </div>
@endsection

@section('scripts')
<script>
    // ========== FAQ Accordion ==========
    function toggleFaq(btn) {
        const item = btn.closest('.faq-item');
        const content = item.querySelector('.faq-content');
        const icon = item.querySelector('.faq-icon');
        const isOpen = !content.classList.contains('hidden');

        // Close all other FAQs
        document.querySelectorAll('.faq-item').forEach(otherItem => {
            if (otherItem !== item) {
                otherItem.querySelector('.faq-content').classList.add('hidden');
                otherItem.querySelector('.faq-icon').style.transform = 'rotate(0deg)';
            }
        });

        // Toggle current
        content.classList.toggle('hidden');
        icon.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
    }

    // ========== Counter Animation ==========
    function animateCounters() {
        const counters = document.querySelectorAll('.counter-number');
        counters.forEach(counter => {
            if (counter.dataset.animated === 'true') return;
            counter.dataset.animated = 'true';

            const target = parseInt(counter.getAttribute('data-target')) || 0;
            const suffix = counter.getAttribute('data-suffix') || (target === 40 || target === 10000 ? '+' : '');
            const duration = 1800;
            const step = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.floor(current).toLocaleString('vi-VN') + suffix;
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target.toLocaleString('vi-VN') + suffix;
                }
            };
            updateCounter();
        });
    }

    // Trigger counter animation on load & scroll
    document.addEventListener('DOMContentLoaded', () => {
        const counterSection = document.getElementById('trust-counter');
        if (counterSection) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting || entry.intersectionRatio > 0) {
                        animateCounters();
                        observer.disconnect();
                    }
                });
            }, { threshold: 0.05 });
            observer.observe(counterSection);

            // Immediate fallback check
            const rect = counterSection.getBoundingClientRect();
            if (rect.top < window.innerHeight && rect.bottom >= 0) {
                animateCounters();
            }
        }
    });

    // ========== Vaccine Catalog Tab Switcher ==========
    function switchVaccineTab(tabType) {
        const singleTab = document.getElementById('single-vaccines');
        const packageTab = document.getElementById('package-vaccines');
        const singleBtn = document.getElementById('tab-btn-single');
        const packageBtn = document.getElementById('tab-btn-package');

        if (tabType === 'single') {
            if (singleTab) singleTab.classList.remove('hidden');
            if (packageTab) packageTab.classList.add('hidden');
            if (singleBtn) {
                singleBtn.className = 'px-5 py-2.5 rounded-full font-bold text-xs uppercase tracking-wider bg-[#c8102e] text-white shadow-md transition-all';
            }
            if (packageBtn) {
                packageBtn.className = 'px-5 py-2.5 rounded-full font-bold text-xs uppercase tracking-wider bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all';
            }
        } else {
            if (singleTab) singleTab.classList.add('hidden');
            if (packageTab) packageTab.classList.remove('hidden');
            if (packageBtn) {
                packageBtn.className = 'px-5 py-2.5 rounded-full font-bold text-xs uppercase tracking-wider bg-[#c8102e] text-white shadow-md transition-all';
            }
            if (singleBtn) {
                singleBtn.className = 'px-5 py-2.5 rounded-full font-bold text-xs uppercase tracking-wider bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all';
            }
        }
    }
</script>
@endsection
