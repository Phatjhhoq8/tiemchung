<!-- 9. ⭐ Testimonials / Đánh giá khách hàng (FULL WIDTH - TRÀN VIỀN 100%) -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center max-w-2xl mx-auto space-y-3 mb-10">
        <span class="section-badge">
            Khách Hàng Nói Gì
        </span>
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-black {{ ($section['bg'] ?? 'red') === 'white' ? 'text-slate-900' : 'text-white' }}">
            Phụ Huynh & Khách Hàng Tin Tưởng Medicare
        </h2>
        <p class="{{ ($section['bg'] ?? 'red') === 'white' ? 'text-slate-600' : 'text-slate-300' }} text-sm md:text-base">
            Hàng nghìn gia đình đã lựa chọn Medicare cho hành trình tiêm chủng an toàn.
        </p>
    </div>

    @php
        $testimonials = $settings['home_testimonials'] ?? [];
        if (!is_array($testimonials)) {
            $testimonials = json_decode($testimonials, true) ?: [];
        }
    @endphp

    <style>
        .testimonial-grid-flex {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 24px;
        }
        .testimonial-card-flex {
            width: 100%;
        }
        @media (min-width: 768px) {
            .testimonial-card-flex {
                width: calc(50% - 12px);
            }
        }
        @media (min-width: 1024px) {
            .testimonial-card-flex {
                width: calc(33.333% - 16px);
            }
        }
    </style>
    <div class="testimonial-grid-flex">
        @foreach($testimonials as $index => $item)
            @php
                $avatar = $item['avatar'] ?? '/images/logo.png';
                if (!str_starts_with($avatar, 'http') && !str_starts_with($avatar, 'data:') && !str_starts_with($avatar, '/')) {
                    $avatar = asset($avatar);
                }
                
                // Trực quan hóa tên viết tắt làm avatar nếu rỗng hoặc mặc định
                $nameParts = explode(' ', trim($item['name'] ?? 'K H'));
                $initials = '';
                if (count($nameParts) >= 2) {
                    $initials = mb_substr($nameParts[count($nameParts)-2], 0, 1) . mb_substr($nameParts[count($nameParts)-1], 0, 1);
                } else {
                    $initials = mb_substr($nameParts[0], 0, 2);
                }
                $initials = mb_strtoupper($initials);
            @endphp
            <div class="testimonial-card {{ ($section['bg'] ?? 'red') === 'white' ? 'bg-slate-50 border-slate-100' : 'bg-white/5 border-white/10' }} backdrop-blur-sm border rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 testimonial-card-flex" data-aos="fade-up" data-aos-delay="{{ 100 * ($index + 1) }}">
                <div class="flex gap-1 mb-4">
                    @for($i = 0; $i < 5; $i++)
                        <i data-lucide="star" class="w-4 h-4 fill-[#eaaa00] text-[#eaaa00]"></i>
                    @endfor
                </div>
                <p class="{{ ($section['bg'] ?? 'red') === 'white' ? 'text-slate-700' : 'text-slate-200' }} text-sm leading-relaxed mb-6 italic text-justify">
                    "{{ $item['content'] ?? '' }}"
                </p>
                <div class="flex items-center gap-3">
                    @if(!empty($item['avatar']) && $item['avatar'] !== '/images/logo.png' && $item['avatar'] !== 'images/logo.png' && !str_contains($item['avatar'], 'logo.png') && !str_starts_with($item['avatar'], 'data:image/svg+xml'))
                        <img src="{{ $avatar }}" class="w-10 h-10 rounded-full object-cover border-2 border-white/20" alt="{{ $item['name'] }}">
                    @else
                        <div class="w-10 h-10 rounded-full bg-white text-[#c8102e] flex items-center justify-center font-bold text-sm shadow-sm">{{ $initials }}</div>
                    @endif
                    <div>
                        <div class="{{ ($section['bg'] ?? 'red') === 'white' ? 'text-slate-900' : 'text-white' }} font-bold text-sm">{{ $item['name'] ?? '' }}</div>
                        <div class="{{ ($section['bg'] ?? 'red') === 'white' ? 'text-slate-500' : 'text-slate-400' }} text-xs">{{ $item['role'] ?? '' }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
