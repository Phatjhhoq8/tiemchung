<!-- 2. Quick Action Toolbar (CANH GIỮA MAX-W-7XL) -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <section class="relative z-20 -mt-8" data-aos="fade-up" data-aos-delay="100">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 bg-white p-4 rounded-2xl border border-slate-100 shadow-md">
            <a href="{{ route('vaccine.index') }}" class="flex items-center gap-4 p-4 rounded-xl bg-slate-50/50 border border-slate-100/50 hover:border-[#c8102e]/30 hover:bg-[#c8102e]/5 transition-all duration-200 group">
                <div class="w-12 h-12 rounded-xl bg-[#c8102e]/10 text-[#c8102e] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <i data-lucide="syringe" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="font-bold text-slate-900 text-sm group-hover:text-[#c8102e] transition-colors">Đặt Mua Vắc Xin Online</div>
                    <div class="text-xs text-slate-500 font-medium">Giữ vắc xin 100%, không lo thiếu mũi</div>
                </div>
            </a>

            <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="flex items-center gap-4 p-4 rounded-xl bg-slate-50/50 border border-slate-100/50 hover:border-[#c8102e]/30 hover:bg-[#c8102e]/5 transition-all duration-200 group">
                <div class="w-12 h-12 rounded-xl bg-[#c8102e]/10 text-[#c8102e] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <i data-lucide="calendar-check-2" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="font-bold text-slate-900 text-sm group-hover:text-[#c8102e] transition-colors">Đăng Ký Tiêm Chủng</div>
                    <div class="text-xs text-slate-500 font-medium">Chọn chi nhánh & ngày giờ hẹn trước</div>
                </div>
            </a>

            <a href="{{ route('vaccine.index') }}" class="flex items-center gap-4 p-4 rounded-xl bg-slate-50/50 border border-slate-100/50 hover:border-[#c8102e]/30 hover:bg-[#c8102e]/5 transition-all duration-200 group">
                <div class="w-12 h-12 rounded-xl bg-[#c8102e]/10 text-[#c8102e] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <i data-lucide="badge-percent" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="font-bold text-slate-900 text-sm group-hover:text-[#c8102e] transition-colors">Bảng Giá Vắc Xin</div>
                    <div class="text-xs text-slate-500 font-medium">Giá niêm yết công khai, bình ổn</div>
                </div>
            </a>

            <a href="{{ route('contact') }}" class="flex items-center gap-4 p-4 rounded-xl bg-slate-50/50 border border-slate-100/50 hover:border-[#c8102e]/30 hover:bg-[#c8102e]/5 transition-all duration-200 group">
                <div class="w-12 h-12 rounded-xl bg-[#c8102e]/10 text-[#c8102e] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <i data-lucide="map-pin" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="font-bold text-slate-900 text-sm group-hover:text-[#c8102e] transition-colors">Tìm Chi Nhánh Gần Bạn</div>
                    <div class="text-xs text-slate-500 font-medium">Chi nhánh Cờ Đỏ & Thới Lai</div>
                </div>
            </a>
        </div>
    </section>
</div>
