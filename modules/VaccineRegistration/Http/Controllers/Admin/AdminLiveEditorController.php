<?php
/**
 * Chức năng: AdminLiveEditorController quản lý trình chỉnh sửa trực quan Live Editor toàn bộ các trang.
 * Lý do chỉnh sửa: Mở rộng Live Editor cho tất cả các trang (Home, About, Services, Contact, Vaccines, News, Global Shell),
 *                   thu gọn các thành phần trùng lặp (Header, Topbar, Footer, Chat Zalo) thành nhóm Cấu hình chung.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Banner;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Models\Article;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\Setting;

class AdminLiveEditorController extends Controller
{
    public static $defaultSections = [
        'quick_booking' => 'Form Đăng ký nhanh',
        'centers' => 'Hệ thống Trung tâm',
        'recommendations' => 'Khuyến nghị Y khoa',
        'qdenga_promo' => 'Vắc-xin nổi bật',
        'featured_vaccines' => 'Danh mục vắc-xin',
        'safe_process' => 'Quy trình 5 bước',
        'services' => 'Dịch vụ chính',
        'testimonials' => 'Đánh giá khách hàng',
        'news' => 'Tin tức y khoa',
        'faq' => 'Câu hỏi thường gặp'
    ];

    public static function getLayoutConfig($isDraft = true)
    {
        $key = $isDraft ? 'homepage_layout_config_draft' : 'homepage_layout_config';
        $stored = Setting::get($key);
        $config = $stored ? json_decode($stored, true) : [];
        
        $merged = [];
        $index = 1;
        foreach (self::$defaultSections as $keyName => $displayName) {
            if (isset($config[$keyName])) {
                $merged[$keyName] = $config[$keyName];
            } else {
                $merged[$keyName] = [
                    'order' => $index * 10,
                    'is_visible' => true,
                    'bg' => ($keyName === 'qdenga_promo' || $keyName === 'testimonials') ? 'red' : 'white',
                    'padding' => 'standard'
                ];
            }
            
            $merged[$keyName]['name'] = $displayName;
            
            if ($merged[$keyName]['bg'] === 'red') {
                $merged[$keyName]['bg_class'] = 'section-style-red';
            } elseif ($merged[$keyName]['bg'] === 'dark') {
                $merged[$keyName]['bg_class'] = 'section-style-dark';
            } else {
                $merged[$keyName]['bg_class'] = 'section-style-white';
            }
            
            if ($merged[$keyName]['padding'] === 'compact') {
                $merged[$keyName]['padding_class'] = 'py-12';
            } elseif ($merged[$keyName]['padding'] === 'spacious') {
                $merged[$keyName]['padding_class'] = 'py-28';
            } else {
                $merged[$keyName]['padding_class'] = 'py-20';
            }
            
            $index++;
        }
        
        uasort($merged, function($a, $b) {
            return $a['order'] - $b['order'];
        });
        
        return $merged;
    }

    /**
     * Hiển thị trình chỉnh sửa trực quan toàn hệ thống (Universal Live Page Customizer).
     */
    public function index(Request $request)
    {
        $currentPage = $request->get('page', 'home');

        $banners = Banner::ordered()->get();
        $featuredVaccines = Vaccine::where('is_featured', true)->get();
        $allVaccines = Vaccine::orderBy('name', 'asc')->get();
        $articles = Article::orderBy('created_at', 'desc')->take(6)->get();
        $centers = Center::where('is_active', true)->orderBy('id', 'asc')->get();
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $layoutConfig = self::getLayoutConfig(true); // load draft config for edit

        return view('vaccine::admin.live_editor', compact(
            'currentPage',
            'banners',
            'featuredVaccines',
            'allVaccines',
            'articles',
            'centers',
            'settings',
            'layoutConfig'
        ));
    }

    /**
     * Cập nhật Banner từ Live Editor.
     */
    public function updateBanner(Request $request)
    {
        $request->validate([
            'banner_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'link_url' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'image_existing' => 'nullable|string',
        ]);

        $banner = Banner::findOrFail($request->banner_id);
        $imagePath = $banner->image_url;

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'banner_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/banners'), $filename);
            $imagePath = 'images/banners/' . $filename;
        } elseif ($request->filled('image_existing')) {
            $imagePath = $request->image_existing;
        }

        $banner->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'link_url' => $request->link_url,
            'image_url' => $imagePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật Banner thành công!',
            'banner' => $banner
        ]);
    }

    /**
     * Cập nhật Sản phẩm / Vắc Xin từ Live Editor.
     */
    public function updateVaccine(Request $request)
    {
        $request->validate([
            'vaccine_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'sale_price' => 'nullable|integer|min:0',
            'disease_prevention' => 'required|string|max:255',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'image_existing' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        $vaccine = Vaccine::findOrFail($request->vaccine_id);
        $imageName = $vaccine->image;

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'vac_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/vaccines'), $filename);
            $imageName = $filename;
        } elseif ($request->filled('image_existing')) {
            $imageName = basename($request->image_existing);
        }

        $vaccine->update([
            'name' => $request->name,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'disease_prevention' => $request->disease_prevention,
            'image' => $imageName,
            'is_featured' => $request->has('is_featured') ? (bool)$request->is_featured : $vaccine->is_featured,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật Vắc xin thành công!',
            'vaccine' => $vaccine
        ]);
    }

    /**
     * Cập nhật Cài Đặt Động Toàn Hệ Thống (Settings) cho từng Trang & Khung Chung.
     */
    public function updateSettings(Request $request)
    {
        $settingsData = $request->except(['_token', 'page']);

        foreach ($settingsData as $key => $value) {
            if ($value !== null) {
                if ($key === 'about_team_members') {
                    $members = json_decode($value, true);
                    if (!is_array($members)) {
                        $members = [];
                    }

                    $members = collect($members)
                        ->filter(function ($member) {
                            return is_array($member)
                                && trim($member['name'] ?? '') !== ''
                                && trim($member['role'] ?? '') !== '';
                        })
                        ->map(function ($member) {
                            return [
                                'name' => trim($member['name'] ?? ''),
                                'role' => trim($member['role'] ?? ''),
                                'avatar' => $this->storeTeamAvatar(trim($member['avatar'] ?? '')),
                                'zalo' => trim($member['zalo'] ?? ''),
                            ];
                        })
                        ->values()
                        ->all();

                    $value = json_encode($members, JSON_UNESCAPED_UNICODE);
                }

                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Lưu cài đặt trực quan thành công!'
        ]);
    }

    private function storeTeamAvatar(string $avatar): string
    {
        if (!str_starts_with($avatar, 'data:image/')) {
            return $avatar;
        }

        if (!preg_match('/^data:image\/(jpeg|jpg|png|webp|gif);base64,(.+)$/', $avatar, $matches)) {
            return '';
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $imageData = base64_decode($matches[2], true);
        if ($imageData === false || strlen($imageData) > 4 * 1024 * 1024) {
            return '';
        }

        $directory = public_path('images/team');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = 'team_' . time() . '_' . uniqid() . '.' . $extension;
        file_put_contents($directory . DIRECTORY_SEPARATOR . $filename, $imageData);

        return '/images/team/' . $filename;
    }

    /**
     * Lưu cấu hình sắp xếp và phong cách nháp cho Trang Chủ.
     */
    public function saveLayoutConfig(Request $request)
    {
        $layoutData = $request->input('layout', []);
        
        $config = [];
        foreach (self::$defaultSections as $keyName => $displayName) {
            if (isset($layoutData[$keyName])) {
                $config[$keyName] = [
                    'order' => (int)($layoutData[$keyName]['order'] ?? 100),
                    'is_visible' => isset($layoutData[$keyName]['is_visible']) && ($layoutData[$keyName]['is_visible'] == '1' || $layoutData[$keyName]['is_visible'] == 'true'),
                    'bg' => $layoutData[$keyName]['bg'] ?? 'white',
                    'padding' => $layoutData[$keyName]['padding'] ?? 'standard'
                ];
            }
        }
        
        Setting::set('homepage_layout_config_draft', json_encode($config));
        
        return response()->json([
            'success' => true,
            'message' => 'Lưu cấu hình nháp trang chủ thành công!'
        ]);
    }
    
    /**
     * Xuất bản cấu hình nháp thành chính thức.
     */
    public function publishLayoutConfig(Request $request)
    {
        $layoutData = $request->input('layout', []);
        
        $config = [];
        foreach (self::$defaultSections as $keyName => $displayName) {
            if (isset($layoutData[$keyName])) {
                $config[$keyName] = [
                    'order' => (int)($layoutData[$keyName]['order'] ?? 100),
                    'is_visible' => isset($layoutData[$keyName]['is_visible']) && ($layoutData[$keyName]['is_visible'] == '1' || $layoutData[$keyName]['is_visible'] == 'true'),
                    'bg' => $layoutData[$keyName]['bg'] ?? 'white',
                    'padding' => $layoutData[$keyName]['padding'] ?? 'standard'
                ];
            }
        }
        
        $json = json_encode($config);
        Setting::set('homepage_layout_config', $json);
        Setting::set('homepage_layout_config_draft', $json);
        
        return response()->json([
            'success' => true,
            'message' => 'Áp dụng cấu hình và xuất bản trang chủ thành công!'
        ]);
    }

    /**
     * Khôi phục cấu hình nháp về cấu hình chính thức (Reset).
     */
    public function resetLayoutConfig(Request $request)
    {
        $live = Setting::get('homepage_layout_config');
        if ($live) {
            Setting::set('homepage_layout_config_draft', $live);
            return response()->json([
                'success' => true,
                'message' => 'Đã khôi phục cấu hình nháp về cấu hình đang hiển thị chính thức!'
            ]);
        }
        
        // If there's no live config yet, clear the draft to restore system defaults
        Setting::updateOrCreate(
            ['key' => 'homepage_layout_config_draft'],
            ['value' => null]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Đã đặt lại cấu hình nháp về mặc định hệ thống!'
        ]);
    }
}
