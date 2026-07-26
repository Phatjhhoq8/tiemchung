<!-- 4. Khuyến nghị quan trọng (CANH GIỮA MAX-W-7XL) -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <section class="recommendations-section space-y-8" id="recommendations-section" data-aos="fade-up">
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <span class="section-badge">
                Lời Khuyên Y Khoa
            </span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-black text-slate-900">
                {{ $settings['rec_section_title'] ?? 'Vắc Xin Khuyến Nghị Cho Người Cao Tuổi & Bệnh Nền' }}
            </h2>
            <p class="text-slate-600 text-sm md:text-base max-w-lg mx-auto">
                {{ $settings['rec_section_desc'] ?? 'Chủ động bảo vệ sức khỏe trước các tác nhân gây suy giảm hệ miễn dịch nguy hiểm.' }}
            </p>
        </div>

        <div class="space-y-5">
            <!-- Card 1: Cúm Mùa -->
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden group" data-aos="fade-up" data-aos-delay="100">
                <div class="grid grid-cols-1 md:grid-cols-12 items-center">
                    <div class="md:col-span-4 lg:col-span-3 aspect-[4/3] md:aspect-auto md:h-full w-full overflow-hidden bg-white flex items-center justify-center p-4">
                        <img src="{{ asset('images/vaccines/vaxigrip.jpg') }}" alt="Vắc xin Cúm Tứ Giá" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300" loading="lazy">
                    </div>
                    <div class="md:col-span-8 lg:col-span-9 p-6 md:p-8 space-y-3 flex flex-col justify-between h-full">
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="bg-[#c8102e] text-white font-bold text-[11px] uppercase px-2.5 py-0.5 rounded-md">Vắc Xin Cúm Mùa</span>
                                <span class="text-slate-500 text-xs font-semibold">Vaxigrip Tetra (Pháp) / Influvac Tetra (Hà Lan)</span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-800 group-hover:text-[#c8102e] transition-colors leading-tight mb-2">
                                Bảo Vệ Người Cao Tuổi & Người Có Bệnh Nền Tránh Biến Chứng Cúm
                            </h3>
                            <p class="text-slate-600 text-sm leading-relaxed line-clamp-2">
                                Bệnh cúm có thể dẫn đến các biến chứng nguy hiểm như viêm phổi suy hô hấp, đặc biệt ở người có bệnh tim mạch, tiểu đường, hen suyễn. Tiêm vắc xin cúm hàng năm giúp giảm 80% tỷ lệ nhập viện.
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-4 pt-3 border-t border-slate-100 mt-2">
                            <div class="flex items-center gap-4 text-xs font-semibold text-slate-700">
                                <span class="flex items-center gap-1 text-slate-600"><i data-lucide="user-check" class="w-4 h-4 text-[#c8102e]"></i> Đối tượng: Người từ 6 tháng tuổi</span>
                                <span class="flex items-center gap-1 text-slate-600"><i data-lucide="clock" class="w-4 h-4 text-[#eaaa00]"></i> Lịch tiêm: 1 mũi / năm</span>
                            </div>
                            <a href="{{ route('vaccine.index', ['disease' => 'Cúm']) }}" class="inline-flex items-center gap-1.5 text-[#c8102e] font-bold text-sm hover:underline">
                                Xem sản phẩm phù hợp <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Phế cầu -->
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden group" data-aos="fade-up" data-aos-delay="200">
                <div class="grid grid-cols-1 md:grid-cols-12 items-center">
                    <div class="md:col-span-4 lg:col-span-3 aspect-[4/3] md:aspect-auto md:h-full w-full overflow-hidden bg-white flex items-center justify-center p-4">
                        <img src="{{ asset('images/vaccines/prevenar13.jpg') }}" alt="Vắc xin Phế Cầu Prevenar 13" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300" loading="lazy">
                    </div>
                    <div class="md:col-span-8 lg:col-span-9 p-6 md:p-8 space-y-3 flex flex-col justify-between h-full">
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="bg-[#c8102e] text-white font-bold text-[11px] uppercase px-2.5 py-0.5 rounded-md">Vắc Xin Phế Cầu</span>
                                <span class="text-slate-500 text-xs font-semibold">Prevenar 13 (Bỉ - Mỹ) / Pneumovax 23</span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-800 group-hover:text-[#c8102e] transition-colors leading-tight mb-2">
                                Phòng Ngừa Viêm Phổi, Viêm Màng Não & Nhiễm Trùng Huyết Do Phế Cầu
                            </h3>
                            <p class="text-slate-600 text-sm leading-relaxed line-clamp-2">
                                Phế cầu khuẩn là thủ phạm hàng đầu gây viêm phổi nặng và tử văn ở người lớn tuổi. Chủ động tiêm vắc xin phế cầu giúp tạo lá chắn vững chắc bảo vệ phổi.
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-4 pt-3 border-t border-slate-100 mt-2">
                            <div class="flex items-center gap-4 text-xs font-semibold text-slate-700">
                                <span class="flex items-center gap-1 text-slate-600"><i data-lucide="user-check" class="w-4 h-4 text-[#c8102e]"></i> Đối tượng: Trẻ nhỏ & Người lớn</span>
                                <span class="flex items-center gap-1 text-slate-600"><i data-lucide="clock" class="w-4 h-4 text-[#eaaa00]"></i> Lịch tiêm: Theo phác đồ tuổi</span>
                            </div>
                            <a href="{{ route('vaccine.index', ['search' => 'Prevenar']) }}" class="inline-flex items-center gap-1.5 text-[#c8102e] font-bold text-sm hover:underline">
                                Xem sản phẩm phù hợp <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
