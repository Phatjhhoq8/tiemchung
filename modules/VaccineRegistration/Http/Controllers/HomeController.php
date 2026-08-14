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
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Support\CenterContext;

use Modules\VaccineRegistration\Services\SiteContentService;
use Modules\VaccineRegistration\Support\SiteContentRegistry;

class HomeController extends Controller
{
    protected $contentService;

    public function __construct(SiteContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * Hiển thị trang chủ với dữ liệu động.
     */
    public function index(Request $request)
    {
        $currentCenter = CenterContext::current();
        $activeCenters = CenterContext::activeCenters();
        $isPreviewMode = $this->isPreviewMode($request);

        // Lấy danh sách banner đang hoạt động và sắp xếp thứ tự
        $banners = Banner::active()->ordered()->get();

        // Lấy danh sách vắc xin nổi bật được cấu hình trong Admin (tối đa 8 vắc xin)
        $featuredVaccines = collect();
        $campaignVaccines = collect();

        if ($currentCenter) {
            $featuredVaccines = Vaccine::forCenter($currentCenter->id)
                ->featured()
                ->orderBy('center_vaccines.sort_order', 'asc')
                ->take(8)
                ->get();

            if ($featuredVaccines->isEmpty()) {
                $featuredVaccines = Vaccine::forCenter($currentCenter->id)
                    ->orderBy('center_vaccines.sort_order', 'asc')
                    ->take(8)
                    ->get();
            }

            // Lấy 4 vắc xin nổi bật chiến dịch
            $campaignVaccines = Vaccine::forCenter($currentCenter->id)
                ->featured()
                ->orderBy('center_vaccines.sort_order', 'asc')
                ->take(4)
                ->get();

            if ($campaignVaccines->isEmpty()) {
                $campaignVaccines = Vaccine::forCenter($currentCenter->id)
                    ->orderBy('center_vaccines.sort_order', 'asc')
                    ->take(4)
                    ->get();
            }
        }

        // Lấy 4 bài viết tin tức / kiến thức y tế mới nhất từ CSDL (1 bài lớn + 3 bài nhỏ)
        $articles = Article::where('is_published', true)->latest()->take(4)->get();

        // Lấy cấu hình layout động
        $layoutConfig = $this->publishedLayoutConfig($isPreviewMode);
        
        // Tải cài đặt động
        $settings = $this->contentService->getAll($isPreviewMode);

        return view('vaccine::home', compact(
            'banners',
            'featuredVaccines',
            'campaignVaccines',
            'articles',
            'layoutConfig',
            'currentCenter',
            'activeCenters',
            'settings',
            'isPreviewMode'
        ));
    }

    private function publishedLayoutConfig(bool $isDraft = false): array
    {
        $key = $isDraft ? 'homepage_layout_config_draft' : 'homepage_layout_config';
        $stored = Setting::get($key) ?: Setting::get('homepage_layout_config');
        $config = is_string($stored) ? json_decode($stored, true) : [];
        $config = is_array($config) ? $config : [];
        $sections = [];

        foreach (SiteContentRegistry::$defaultSections as $sectionKey => $name) {
            $defaultBackground = in_array($sectionKey, ['qdenga_promo', 'testimonials'], true) ? 'red' : 'white';
            $section = $config[$sectionKey] ?? [];
            $background = in_array($section['bg'] ?? $defaultBackground, ['red', 'dark', 'light-blue', 'white'], true)
                ? $section['bg'] ?? $defaultBackground
                : $defaultBackground;
            $padding = in_array($section['padding'] ?? 'standard', ['compact', 'standard', 'spacious'], true)
                ? $section['padding'] ?? 'standard'
                : 'standard';

            $sections[$sectionKey] = [
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
    public function about(Request $request)
    {
        $isPreviewMode = $this->isPreviewMode($request);
        $settings = $this->contentService->getAll($isPreviewMode);
        return view('vaccine::about', compact('settings', 'isPreviewMode'));
    }

    /**
     * Trang Dịch Vụ Tiêm Chủng riêng biệt.
     */
    public function services(Request $request)
    {
        $isPreviewMode = $this->isPreviewMode($request);
        $settings = $this->contentService->getAll($isPreviewMode);
        return view('vaccine::services', compact('settings', 'isPreviewMode'));
    }

    /**
     * Trang Liên Hệ & Bản Đồ riêng biệt.
     */
    public function contact(Request $request)
    {
        $isPreviewMode = $this->isPreviewMode($request);
        $settings = $this->contentService->getAll($isPreviewMode);
        $centers = CenterContext::activeCenters();
        $currentCenter = CenterContext::current();
        return view('vaccine::contact', compact('centers', 'currentCenter', 'settings', 'isPreviewMode'));
    }

    /**
     * Kiểm tra chế độ xem thử (Preview Mode).
     */
    private function isPreviewMode(Request $request): bool
    {
        return $request->query('preview') == '1' && \Modules\VaccineRegistration\Support\AdminContext::user() !== null;
    }
}
