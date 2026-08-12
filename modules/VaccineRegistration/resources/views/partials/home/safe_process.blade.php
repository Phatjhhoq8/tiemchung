<!-- 7. Quy trình 5 bước (CANH GIỮA MAX-W-7XL) -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <section class="space-y-10" id="safe-process-section" data-aos="fade-up">
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <span class="section-badge">
                Chuẩn Y Tế An Toàn
            </span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-black text-slate-900">
                {{ $settings['home_safe_process_title'] ?? 'Quy Trình Tiêm Chủng 5 Bước An Toàn' }}
            </h2>
            <p class="text-slate-600 text-sm md:text-base">
                {{ $settings['home_safe_process_desc'] ?? 'Medicare áp dụng nghiêm ngặt quy trình tiêm chủng an toàn bảo vệ sức khỏe tối đa cho khách hàng.' }}
            </p>
        </div>

        @php
            $processSteps = $settings['home_safe_process'] ?? [];
            if (!is_array($processSteps)) {
                $processSteps = json_decode($processSteps, true) ?: [];
            }
            $stepIcons = [
                1 => 'clipboard-signature',
                2 => 'stethoscope',
                3 => 'syringe',
                4 => 'timer',
                5 => 'shield-check'
            ];
        @endphp

        <!-- Timeline Layout -->
        <div class="process-timeline relative max-w-3xl mx-auto">
            <!-- Connector line -->
            <div class="hidden md:block absolute left-8 top-0 bottom-0 w-0.5 bg-gradient-to-b from-[#c8102e] via-[#c8102e]/60 to-[#c8102e]/20"></div>

            @foreach($processSteps as $index => $step)
                @php 
                    $stepNum = $step['step'] ?? ($index + 1);
                    $icon = $stepIcons[$stepNum] ?? 'check-circle-2';
                @endphp
                <div class="timeline-step flex gap-6 mb-8 relative" data-aos="fade-right" data-aos-delay="{{ 100 * ($index + 1) }}">
                    <div class="shrink-0 w-16 h-16 bg-[#c8102e] text-white rounded-2xl font-black text-xl flex items-center justify-center shadow-md border-4 border-white z-10 relative">
                        {{ $stepNum }}
                    </div>
                    <div class="flex-1 bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-all duration-200">
                        <div class="flex items-center gap-3 mb-2">
                            <i data-lucide="{{ $icon }}" class="w-5 h-5 text-[#c8102e]"></i>
                            <h4 class="font-bold text-slate-900 text-base">{{ $step['title'] ?? '' }}</h4>
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed text-justify">{{ $step['desc'] ?? '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
