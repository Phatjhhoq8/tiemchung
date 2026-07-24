<!-- 7. Quy trình 5 bước (CANH GIỮA MAX-W-7XL) -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <section class="space-y-10" id="safe-process-section" data-aos="fade-up">
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <span class="section-badge">
                Chuẩn Y Tế An Toàn
            </span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-black text-slate-900">
                {{ $settings['process_section_title'] ?? 'Quy Trình Tiêm Chủng 5 Bước An Toàn' }}
            </h2>
            <p class="text-slate-600 text-sm md:text-base">
                {{ $settings['process_section_desc'] ?? 'Medicare áp dụng nghiêm ngặt quy trình tiêm chủng an toàn bảo vệ sức khỏe tối đa cho khách hàng.' }}
            </p>
        </div>

        <!-- Timeline Layout -->
        <div class="process-timeline relative max-w-3xl mx-auto">
            <!-- Connector line -->
            <div class="hidden md:block absolute left-8 top-0 bottom-0 w-0.5 bg-gradient-to-b from-[#c8102e] via-[#c8102e]/60 to-[#c8102e]/20"></div>

            <!-- Step 1 -->
            <div class="timeline-step flex gap-6 mb-8 relative" data-aos="fade-right" data-aos-delay="100">
                <div class="shrink-0 w-16 h-16 bg-[#c8102e] text-white rounded-2xl font-black text-xl flex items-center justify-center shadow-md border-4 border-white z-10 relative">1</div>
                <div class="flex-1 bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="clipboard-signature" class="w-5 h-5 text-[#c8102e]"></i>
                        <h4 class="font-bold text-slate-900 text-base">Đăng Ký & Khám Sàng Lọc</h4>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed">Bác sĩ khám sàng lọc cẩn thận miễn phí trước khi tiêm, kiểm tra thân nhiệt, huyết áp và tiền sử bệnh.</p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="timeline-step flex gap-6 mb-8 relative" data-aos="fade-right" data-aos-delay="200">
                <div class="shrink-0 w-16 h-16 bg-[#c8102e] text-white rounded-2xl font-black text-xl flex items-center justify-center shadow-md border-4 border-white z-10 relative">2</div>
                <div class="flex-1 bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="stethoscope" class="w-5 h-5 text-[#c8102e]"></i>
                        <h4 class="font-bold text-slate-900 text-base">Tư Vấn Phác Đồ</h4>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed">Tư vấn loại vắc xin phù hợp, liều lượng, lịch tiêm nhắc và chi phí niêm yết minh bạch.</p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="timeline-step flex gap-6 mb-8 relative" data-aos="fade-right" data-aos-delay="300">
                <div class="shrink-0 w-16 h-16 bg-[#c8102e] text-white rounded-2xl font-black text-xl flex items-center justify-center shadow-md border-4 border-white z-10 relative">3</div>
                <div class="flex-1 bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="syringe" class="w-5 h-5 text-[#c8102e]"></i>
                        <h4 class="font-bold text-slate-900 text-base">Tiêm Chuẩn GSP</h4>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed">Điều dưỡng thực hiện tiêm đúng kỹ thuật nhẹ nhàng, không đau. Vắc xin bảo quản đúng dây chuyền lạnh.</p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="timeline-step flex gap-6 mb-8 relative" data-aos="fade-right" data-aos-delay="400">
                <div class="shrink-0 w-16 h-16 bg-[#c8102e] text-white rounded-2xl font-black text-xl flex items-center justify-center shadow-md border-4 border-white z-10 relative">4</div>
                <div class="flex-1 bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="timer" class="w-5 h-5 text-[#c8102e]"></i>
                        <h4 class="font-bold text-slate-900 text-base">Theo Dõi 30 Phút</h4>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed">Theo dõi phản ứng sau tiêm 30 phút tại phòng chờ hiện đại, có y tá trực sẵn sàng xử lý.</p>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="timeline-step flex gap-6 relative" data-aos="fade-right" data-aos-delay="500">
                <div class="shrink-0 w-16 h-16 bg-[#c8102e] text-white rounded-2xl font-black text-xl flex items-center justify-center shadow-md border-4 border-white z-10 relative">5</div>
                <div class="flex-1 bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="shield-check" class="w-5 h-5 text-[#c8102e]"></i>
                        <h4 class="font-bold text-slate-900 text-base">Kiểm Tra & Ra Về An Toàn</h4>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed">Kiểm tra vết tiêm, đo lại thân nhiệt, dặn dò chăm sóc tại nhà và hẹn lịch tiêm nhắc tiếp theo.</p>
                </div>
            </div>
        </div>
    </section>
</div>
