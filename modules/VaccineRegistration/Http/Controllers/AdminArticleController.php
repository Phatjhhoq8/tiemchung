<?php
/**
 * Chức năng: AdminArticleController quản lý CRUD bài viết, tin tức y khoa và kiến thức tiêm chủng.
 * Lý do tạo: Phục vụ quản trị nội dung phần Tin tức trên trang chủ từ Admin Panel (Đáp ứng Mục 10).
 */

namespace Modules\VaccineRegistration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Article;
use Illuminate\Support\Str;

class AdminArticleController extends Controller
{
    public function index()
    {
        $articles = Article::latest()->paginate(10);
        return view('vaccine::admin.articles.index', compact('articles'));
    }

    public function create()
    {
        return view('vaccine::admin.articles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'category' => 'required|string',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        // Xử lý tải lên hình ảnh từ file
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'article_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
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
        $article = Article::findOrFail($id);
        return view('vaccine::admin.articles.edit', compact('article'));
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'category' => 'required|string',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        // Xử lý tải lên hình ảnh từ file
        if ($request->hasFile('image_file')) {
            // Xóa ảnh cũ nếu có
            if ($article->image && !in_array($article->image, ['default_package.jpg', 'default_vaccine.jpg'])) {
                $oldPath = public_path('images/vaccines/' . $article->image);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $file = $request->file('image_file');
            $filename = 'article_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/vaccines'), $filename);
            $validated['image'] = $filename;
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_published'] = $request->has('is_published');
        $validated['is_featured'] = $request->has('is_featured');

        $article->update($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Xóa bài viết thành công!');
    }

    /**
     * API tải lên hình ảnh cho trình soạn thảo TinyMCE.
     */
    public function uploadEditorImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Validate sơ bộ định dạng ảnh
            if (!in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                return response()->json(['error' => 'File không đúng định dạng ảnh.'], 400);
            }
            
            $filename = 'editor_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/vaccines'), $filename);
            
            $location = asset('images/vaccines/' . $filename);
            
            return response()->json(['location' => $location]);
        }
        
        return response()->json(['error' => 'Không tìm thấy file tải lên.'], 400);
    }
}
