<?php
/**
 * Chức năng: AdminBannerController quản trị banner/slider trang chủ Medicare Cờ Đỏ.
 * Lý do tạo: Cho phép Admin thêm/sửa/xóa banner quảng bá trên slider trang chủ.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Banner;

use Modules\VaccineRegistration\Support\AdminContext;

class AdminBannerController extends Controller
{
    public function __construct()
    {
        // Protected by route middleware 'super.admin' and explicit abort_unless checks
    }

    /**
     * Danh sách banner.
     */
    public function index()
    {
        abort_unless(AdminContext::isSuperAdmin(), 403);
        $banners = Banner::ordered()->paginate(10);
        return view('vaccine::admin.banners.index', compact('banners'));
    }

    /**
     * Form thêm banner mới.
     */
    public function create()
    {
        $banner = new Banner();
        return view('vaccine::admin.banners.create', compact('banner'));
    }

    /**
     * Lưu banner mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image_url' => 'nullable|string|max:500',
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048', new \App\Rules\SafeImageFile()],
            'link_url' => [
                'nullable',
                'string',
                'max:500',
                function ($attribute, $value, $fail) {
                    $lowerVal = strtolower(trim($value));
                    if (preg_match('/^\s*(javascript|data|vbscript)\s*:/i', $value) || str_contains($lowerVal, 'javascript:') || str_contains($lowerVal, 'data:')) {
                        $fail('Đường dẫn link không hợp lệ.');
                    }
                }
            ],
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ], [
            'title.required' => 'Tiêu đề banner không được để trống.',
        ]);

        // Xử lý tải lên hình ảnh từ file
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'banner_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/banners'), $filename);
            $validated['image_url'] = '/images/banners/' . $filename;
        }

        if (empty($validated['image_url'])) {
            return redirect()->back()->withInput()->withErrors(['image_file' => 'Vui lòng chọn hình ảnh banner tải lên.']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Banner::create($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Thêm banner mới thành công.');
    }

    /**
     * Form sửa banner.
     */
    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('vaccine::admin.banners.edit', compact('banner'));
    }

    /**
     * Cập nhật banner.
     */
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image_url' => 'nullable|string|max:500',
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048', new \App\Rules\SafeImageFile()],
            'link_url' => [
                'nullable',
                'string',
                'max:500',
                function ($attribute, $value, $fail) {
                    $lowerVal = strtolower(trim($value));
                    if (preg_match('/^\s*(javascript|data|vbscript)\s*:/i', $value) || str_contains($lowerVal, 'javascript:') || str_contains($lowerVal, 'data:')) {
                        $fail('Đường dẫn link không hợp lệ.');
                    }
                }
            ],
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ], [
            'title.required' => 'Tiêu đề banner không được để trống.',
        ]);

        // Xử lý tải lên hình ảnh từ file
        if ($request->hasFile('image_file')) {
            // Xóa ảnh cũ nếu có
            if ($banner->image_url) {
                $oldFilename = basename($banner->image_url);
                $oldPath = public_path('images/banners/' . $oldFilename);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $file = $request->file('image_file');
            $filename = 'banner_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/banners'), $filename);
            $validated['image_url'] = '/images/banners/' . $filename;
        }

        if (empty($validated['image_url'])) {
            $validated['image_url'] = $banner->image_url;
        }

        $validated['is_active'] = $request->has('is_active');

        $banner->update($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật banner thành công.');
    }

    /**
     * Xóa banner (Soft deactivation).
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->is_active = false;
        $banner->save();

        return redirect()->route('admin.banners.index')->with('success', 'Vô hiệu hóa banner thành công.');
    }
}
