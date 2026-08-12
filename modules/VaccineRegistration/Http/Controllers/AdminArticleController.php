<?php

/**
 * Chức năng: AdminArticleController quản lý CRUD bài viết, tin tức y khoa và kiến thức tiêm chủng.
 * Lý do tạo: Phục vụ quản trị nội dung phần Tin tức trên trang chủ từ Admin Panel (Đáp ứng Mục 10).
 */

namespace Modules\VaccineRegistration\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Rules\SafeImageFile;
use App\Services\Security\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\VaccineRegistration\Models\Article;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminArticleController extends Controller
{
    public function __construct()
    {
        // Protected by route middleware 'super.admin' and explicit abort_unless checks
    }

    public function index(Request $request)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền quản lý bài viết.');

        $query = Article::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('summary', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', 'like', '%'.$request->input('category').'%');
        }

        if ($request->filled('is_published')) {
            $query->where('is_published', $request->boolean('is_published'));
        }

        $articles = $query->latest()->paginate(10)->withQueryString();

        return view('vaccine::admin.articles.index', compact('articles'));
    }

    public function create()
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền tạo bài viết.');

        return view('vaccine::admin.articles.create');
    }

    public function store(Request $request)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền tạo bài viết.');
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'category' => 'required|string',
            'image' => 'nullable|string',
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048', new SafeImageFile],
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        if (isset($validated['content'])) {
            $validated['content'] = HtmlSanitizer::clean($validated['content']);
        }

        // Xử lý tải lên hình ảnh từ file
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'article_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('images/vaccines'), $filename);
            $validated['image'] = $filename;
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_published'] = $request->has('is_published');
        $validated['is_featured'] = $request->has('is_featured');

        Article::create($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Thêm bài viết mới thành công!');
    }

    public function edit($id)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền chỉnh sửa bài viết.');
        $article = Article::findOrFail($id);

        return view('vaccine::admin.articles.edit', compact('article'));
    }

    public function update(Request $request, $id)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền chỉnh sửa bài viết.');
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'category' => 'required|string',
            'image' => 'nullable|string',
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048', new SafeImageFile],
        ]);

        if (isset($validated['content'])) {
            $validated['content'] = HtmlSanitizer::clean($validated['content']);
        }

        // Xử lý tải lên hình ảnh từ file
        if ($request->hasFile('image_file')) {
            // Xóa ảnh cũ nếu có
            if ($article->image && ! in_array($article->image, ['default_package.jpg', 'default_vaccine.jpg'])) {
                $oldPath = public_path('images/vaccines/'.$article->image);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $file = $request->file('image_file');
            $filename = 'article_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('images/vaccines'), $filename);
            $validated['image'] = $filename;
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_published'] = $request->has('is_published');
        $validated['is_featured'] = $request->has('is_featured');

        $article->update($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    /**
     * Bật / Tắt trạng thái Ẩn / Hiện (Ẩn mềm) bài viết.
     */
    public function toggleStatus($id)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền thay đổi trạng thái bài viết.');
        $article = Article::findOrFail($id);
        $article->is_published = ! $article->is_published;
        $article->is_active = $article->is_published;
        $article->save();

        $statusText = $article->is_published ? 'Hiển thị' : 'Đã ẩn';

        return redirect()->back()->with('success', "Đã chuyển bài viết \"{$article->title}\" sang trạng thái: {$statusText}!");
    }

    /**
     * Xóa cứng (Hard Delete) bài viết vĩnh viễn khỏi CSDL.
     */
    public function destroy($id)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền xóa bài viết.');
        $article = Article::findOrFail($id);

        // Xóa file ảnh đính kèm nếu không phải ảnh mặc định
        if ($article->image && ! in_array($article->image, ['default_package.jpg', 'default_vaccine.jpg', 'hexaxim.jpg'])) {
            $oldPath = public_path('images/vaccines/'.$article->image);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $title = $article->title;
        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', "Đã xóa vĩnh viễn bài viết \"{$title}\" thành công!");
    }

    /**
     * API tải lên hình ảnh cho trình soạn thảo TinyMCE.
     */
    public function uploadEditorImage(Request $request)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền tải ảnh lên bài viết.');
        $request->validate([
            'file' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048', new SafeImageFile],
        ], [
            'file.mimes' => 'Tệp không đúng định dạng ảnh (chỉ chấp nhận jpg, jpeg, png, webp).',
            'file.image' => 'Tệp tải lên phải là hình ảnh hợp lệ.',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension());
            $mime = strtolower($file->getMimeType());

            if ($ext === 'svg' || str_contains($mime, 'svg')) {
                return response()->json(['error' => 'Không được phép tải lên tệp SVG.'], 400);
            }

            $filename = 'editor_'.time().'_'.uniqid().'.'.$ext;
            $file->move(public_path('images/vaccines'), $filename);

            $location = asset('images/vaccines/'.$filename);

            return response()->json(['location' => $location]);
        }

        return response()->json(['error' => 'Không tìm thấy tệp tải lên.'], 400);
    }
}
