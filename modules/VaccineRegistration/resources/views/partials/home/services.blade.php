<!-- 8. Dịch vụ tiêm chủng (CANH GIỮA MAX-W-7XL) -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <section class="space-y-8" id="services-section" data-aos="fade-up">
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <span class="section-badge">
                Dịch Vụ Chính
            </span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-black text-slate-900">
                {{ $settings['service_section_title'] ?? 'Dịch Vụ Tiêm Chủng Tại Phòng Khám' }}
            </h2>
            <p class="text-slate-600 text-sm md:text-base">
                {{ $settings['service_section_desc'] ?? 'Giải pháp phòng ngừa bệnh tật toàn diện dành cho mọi lứa tuổi và gia đình.' }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="{{ route('vaccine.index', ['age_group' => 'Trẻ']) }}" class="service-card-large bg-white rounded-2xl p-8 shadow-sm hover:shadow-md transition-all duration-200 flex items-start gap-6 group" data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 bg-gradient-to-br from-[#c8102e] to-red-700 text-white rounded-2xl flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                    <i data-lucide="baby" class="w-8 h-8 text-white" style="color: #ffffff !important;"></i>
                </div>
                <div class="space-y-2 flex-1">
                    <h3 class="font-bold text-slate-900 text-xl group-hover:text-[#c8102e] transition-colors">Tiêm Chủng Trẻ Em</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Cung cấp đầy đủ các loại vắc xin quan trọng cho bé từ sơ sinh đến 6 tuổi với quy trình tiêm nhẹ nhàng, giảm đau.</p>
                    <span class="inline-flex items-center gap-1.5 text-[#c8102e] font-bold text-sm group-hover:gap-2.5 transition-all">
                        Xem vắc xin trẻ em <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </span>
                </div>
            </a>

            <a href="{{ route('vaccine.index', ['age_group' => 'người lớn']) }}" class="service-card-large bg-white rounded-2xl p-8 shadow-sm hover:shadow-md transition-all duration-200 flex items-start gap-6 group" data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 bg-gradient-to-br from-[#c8102e] to-red-700 text-white rounded-2xl flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                    <i data-lucide="user-check" class="w-8 h-8 text-white" style="color: #ffffff !important;"></i>
                </div>
                <div class="space-y-2 flex-1">
                    <h3 class="font-bold text-slate-900 text-xl group-hover:text-[#c8102e] transition-colors">Tiêm Chủng Người Lớn</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Bảo vệ người trưởng thành và người cao tuổi trước Cúm mùa, Phế cầu, Zona thần kinh, Sốt xuất huyết Qdenga.</p>
                    <span class="inline-flex items-center gap-1.5 text-[#c8102e] font-bold text-sm group-hover:gap-2.5 transition-all">
                        Xem vắc xin người lớn <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </span>
                </div>
            </a>

            <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="service-card-large bg-white rounded-2xl p-8 shadow-sm hover:shadow-md transition-all duration-200 flex items-start gap-6 group" data-aos="fade-up" data-aos-delay="400">
                <div class="w-16 h-16 bg-gradient-to-br from-[#c8102e] to-[#a00d24] text-white rounded-2xl flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                    <i data-lucide="stethoscope" class="w-8 h-8 text-white" style="color: #ffffff !important;"></i>
                </div>
                <div class="space-y-2 flex-1">
                    <h3 class="font-bold text-slate-900 text-xl group-hover:text-[#c8102e] transition-colors">Tư Vấn & Khám Sàng Lọc</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Miễn phí khám sàng lọc bởi bác sĩ chuyên khoa trước khi tiêm và theo dõi sức khỏe cẩn thận sau tiêm.</p>
                    <span class="inline-flex items-center gap-1.5 text-[#c8102e] font-bold text-sm group-hover:gap-2.5 transition-all">
                        Đặt lịch hẹn ngay <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </span>
                </div>
            </a>
        </div>
    </section>
</div>
