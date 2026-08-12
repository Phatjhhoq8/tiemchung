<!-- 8. Dịch vụ tiêm chủng (CANH GIỮA MAX-W-7XL) -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <section class="space-y-8" id="services-section" data-aos="fade-up">
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <span class="section-badge">
                Dịch Vụ Chính
            </span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-black text-slate-900">
                {{ $settings['services_hero_title'] ?? 'Dịch Vụ Tiêm Chủng Tại Phòng Khám' }}
            </h2>
            <p class="text-slate-600 text-sm md:text-base">
                {{ $settings['services_hero_desc'] ?? 'Giải pháp phòng ngừa bệnh tật toàn diện dành cho mọi lứa tuổi và gia đình.' }}
            </p>
        </div>

        @php
            $services = $settings['services_list'] ?? [];
            if (!is_array($services)) {
                $services = json_decode($services, true) ?: [];
            }
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($services as $index => $service)
                <a href="{{ route('services') }}" class="service-card-large bg-white rounded-2xl p-8 shadow-sm hover:shadow-md transition-all duration-200 flex items-start gap-6 group" data-aos="fade-up" data-aos-delay="{{ 100 * ($index + 1) }}">
                    <div class="w-16 h-16 bg-gradient-to-br from-[#c8102e] to-red-700 text-white rounded-2xl flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                        <i data-lucide="{{ $service['icon'] ?? 'syringe' }}" class="w-8 h-8 text-white" style="color: #ffffff !important;"></i>
                    </div>
                    <div class="space-y-2 flex-1">
                        <h3 class="font-bold text-slate-900 text-xl group-hover:text-[#c8102e] transition-colors">{{ $service['title'] ?? '' }}</h3>
                        <p class="text-sm text-slate-600 leading-relaxed text-justify">{{ $service['desc'] ?? '' }}</p>
                        <span class="inline-flex items-center gap-1.5 text-[#c8102e] font-bold text-sm group-hover:gap-2.5 transition-all">
                            Xem chi tiết dịch vụ <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
</div>
