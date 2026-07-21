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
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ]);

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
        ]);

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
}
