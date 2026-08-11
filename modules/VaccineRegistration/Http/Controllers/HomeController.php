<?php
/**
 * Chức năng: HomeController xử lý trang chủ của website Medicare Cờ Đỏ.
 * Lý do tạo: Tải động các banner quảng cáo và vắc xin nổi bật từ CSDL.
 */

namespace Modules\VaccineRegistration\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\VaccineRegistration\Models\Banner;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Models\Article;
use Modules\VaccineRegistration\Models\Setting;
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminLiveEditorController;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Support\CenterContext;

class HomeController extends Controller
{
    private const HOME_SECTIONS = [
        'quick_booking' => 'Form Đăng ký nhanh',
        'centers' => 'Hệ thống Trung tâm',
        'recommendations' => 'Khuyến nghị Y khoa',
        'qdenga_promo' => 'Vắc-xin nổi bật',
        'featured_vaccines' => 'Danh mục vắc-xin',
        'safe_process' => 'Quy trình 5 bước',
        'services' => 'Dịch vụ chính',
        'testimonials' => 'Đánh giá khách hàng',
        'news' => 'Tin tức y khoa',
        'faq' => 'Câu hỏi thường gặp',
    ];

    /**
     * Hiển thị trang chủ với dữ liệu động.
     */
    public function index()
    {
        $currentCenter = CenterContext::current();
        $activeCenters = CenterContext::activeCenters();

        // Lấy danh sách banner đang hoạt động và sắp xếp thứ tự
        $banners = Banner::active()->ordered()->get();

        // Lấy danh sách vắc xin nổi bật được cấu hình trong Admin, tự động điền thêm nếu thiếu
        $featuredVaccines = Vaccine::forCenter($currentCenter?->id)->featured()->orderBy('center_vaccines.sort_order', 'asc')->take(8)->get();
        if ($featuredVaccines->count() < 8) {
            $excludeIds = $featuredVaccines->pluck('id')->toArray();
            $extraVaccines = Vaccine::forCenter($currentCenter?->id)->whereNotIn('vaccines.id', $excludeIds)->orderBy('center_vaccines.sort_order', 'asc')->take(8 - $featuredVaccines->count())->get();
            $featuredVaccines = $featuredVaccines->merge($extraVaccines);
        }

        // Lấy 4 vắc xin nổi bật chiến dịch (để hiển thị lưới 2x2 ở phần qdenga_promo cũ)
        $campaignVaccines = Vaccine::forCenter($currentCenter?->id)->featured()->orderBy('center_vaccines.sort_order', 'asc')->take(4)->get();
        if ($campaignVaccines->count() < 4) {
            $excludeIds = $campaignVaccines->pluck('id')->toArray();
            $extraCampaign = Vaccine::forCenter($currentCenter?->id)->whereNotIn('vaccines.id', $excludeIds)->orderBy('center_vaccines.sort_order', 'asc')->take(4 - $campaignVaccines->count())->get();
            $campaignVaccines = $campaignVaccines->merge($extraCampaign);
        }

        // Lấy 4 bài viết tin tức / kiến thức y tế mới nhất từ CSDL (1 bài lớn + 3 bài nhỏ)
        $articles = Article::where('is_published', true)->latest()->take(4)->get();

        // Kiểm tra xem có đang ở chế độ xem thử giả lập hay không
        $isPreviewMode = request()->has('preview') && session('admin_logged_in') === true;
        $layoutConfig = $isPreviewMode
            ? AdminLiveEditorController::getLayoutConfig(true)
            : $this->publishedLayoutConfig();

        return view('vaccine::home', compact('banners', 'featuredVaccines', 'campaignVaccines', 'articles', 'layoutConfig', 'currentCenter', 'activeCenters', 'isPreviewMode'));
    }

    private function publishedLayoutConfig(): array
    {
        $stored = Setting::get('homepage_layout_config');
        $config = is_string($stored) ? json_decode($stored, true) : [];
        $config = is_array($config) ? $config : [];
        $sections = [];

        foreach (self::HOME_SECTIONS as $key => $name) {
            $defaultBackground = in_array($key, ['qdenga_promo', 'testimonials'], true) ? 'red' : 'white';
            $section = $config[$key] ?? [];
            $background = in_array($section['bg'] ?? $defaultBackground, ['red', 'dark', 'light-blue', 'white'], true)
                ? $section['bg'] ?? $defaultBackground
                : $defaultBackground;
            $padding = in_array($section['padding'] ?? 'standard', ['compact', 'standard', 'spacious'], true)
                ? $section['padding'] ?? 'standard'
                : 'standard';

            $sections[$key] = [
                'name' => $name,
                'order' => (int) ($section['order'] ?? (count($sections) + 1) * 10),
                'is_visible' => array_key_exists('is_visible', $section) ? (bool) $section['is_visible'] : true,
                'bg' => $background,
                'bg_class' => match ($background) {
                    'red' => 'section-style-red',
                    'dark' => 'section-style-dark',
                    'light-blue' => 'section-style-light-blue',
                    default => 'section-style-white',
                },
                'padding' => $padding,
                'padding_class' => match ($padding) {
                    'compact' => 'py-12',
                    'spacious' => 'py-28',
                    default => 'py-20',
                },
            ];
        }

        uasort($sections, fn (array $left, array $right) => $left['order'] <=> $right['order']);

        return $sections;
    }

    public function selectCenter(Request $request)
    {
        $validated = $request->validate([
            'center_id' => 'required|exists:centers,id',
        ]);

        $center = CenterContext::set((int) $validated['center_id']);

        if ($request->ajax() || $request->wantsJson()) {
            $cartState = CenterContext::resolveCart($center?->id);
            return response()->json([
                'success' => true,
                'center' => $center,
                'cart' => $cartState['cart'],
                'cart_count' => count($cartState['cart']),
                'total_price' => $cartState['total_price'],
                'unavailable_count' => $cartState['unavailable_count'],
            ]);
        }

        if ($request->input('redirect_to') === 'register') {
            return redirect()->route('register.show')->with('success', 'Đã chọn chi nhánh ' . $center->name . '.');
        }

        return redirect()->back()->with('success', 'Đã đổi chi nhánh hiện tại sang ' . $center->name . '.');
    }

    /**
     * Trang Giới Thiệu Phòng Khám riêng biệt.
     */
    public function about()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        return view('vaccine::about', compact('settings'));
    }

    /**
     * Trang Dịch Vụ Tiêm Chủng riêng biệt.
     */
    public function services()
    {
        return view('vaccine::services');
    }

    /**
     * Trang Liên Hệ & Bản Đồ riêng biệt.
     */
    public function contact()
    {
        $centers = CenterContext::activeCenters();
        $currentCenter = CenterContext::current();
        return view('vaccine::contact', compact('centers', 'currentCenter'));
    }
}
