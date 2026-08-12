<!-- 11. FAQ Accordion (CANH GIỮA MAX-W-7XL) -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <section class="space-y-8" id="faq-section" data-aos="fade-up">
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <span class="section-badge">
                Hỏi Đáp
            </span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-black text-slate-900">
                {{ $settings['home_faq_title'] ?? 'Câu Hỏi Thường Gặp' }}
            </h2>
            <p class="text-slate-600 text-sm md:text-base">
                {{ $settings['home_faq_desc'] ?? 'Giải đáp những thắc mắc phổ biến nhất về dịch vụ tiêm chủng tại Medicare.' }}
            </p>
        </div>

        @php
            $faqs = $settings['home_faqs'] ?? [];
            if (!is_array($faqs)) {
                $faqs = json_decode($faqs, true) ?: [];
            }
        @endphp

        <div class="max-w-3xl mx-auto space-y-3" id="faq-accordion">
            @foreach($faqs as $index => $faq)
                <div class="faq-item bg-white border border-slate-100/50 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">
                    <button class="faq-toggle w-full flex items-center justify-between p-5 text-left hover:bg-slate-50 transition-colors" onclick="toggleFaq(this)">
                        <span class="font-bold text-slate-900 text-sm md:text-base pr-4">{{ $faq['q'] ?? '' }}</span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-[#c8102e] shrink-0 faq-icon transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content hidden px-5 pb-5">
                        <p class="text-sm text-slate-600 leading-relaxed">{{ $faq['a'] ?? '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
