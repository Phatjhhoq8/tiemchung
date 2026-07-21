<?php
/**
 * Chức năng: AdminLiveEditorController quản lý trình chỉnh sửa trực quan Live Editor chuẩn Facebook.
 * Lý do tạo: Cho phép Admin xem trước trang chủ dạng khung tương tác, bấm vào khung để chọn hình/sản phẩm có sẵn hoặc tải ảnh từ máy lên, nhập mô tả.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Banner;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Models\Article;
use Modules\VaccineRegistration\Models\Setting;

class AdminLiveEditorController extends Controller
{
    /**
     * Hiển thị trình chỉnh sửa trực quan (Live Page Customizer).
     */
    public function index()
    {
        $banners = Banner::ordered()->get();
        $featuredVaccines = Vaccine::where('is_featured', true)->get();
        $allVaccines = Vaccine::orderBy('name', 'asc')->get();
        $articles = Article::orderBy('created_at', 'desc')->take(6)->get();
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('vaccine::admin.live_editor', compact(
            'banners',
            'featuredVaccines',
            'allVaccines',
            'articles',
            'settings'
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

        // Ưu tiên tải ảnh từ máy lên
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
     * Cập nhật Sản phẩm / Vắc Xin Nổi Bật từ Live Editor.
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

        // Ưu tiên tải ảnh từ máy
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
}
