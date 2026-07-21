<?php
/**
 * Chức năng: ArticleController xử lý hiển thị trang danh sách tin tức y học và trang chi tiết bài viết cho khách hàng.
 * Lý do tạo: Phục vụ chia trang Tin Tức riêng biệt theo yêu cầu người dùng.
 */

namespace Modules\VaccineRegistration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Article;

class ArticleController extends Controller
{
    /**
     * Hiển thị trang danh sách tin tức & kiến thức y học.
     */
    public function index(Request $request)
    {
        $query = Article::where('is_published', true);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $articles = $query->latest()->paginate(6);
        $categories = Article::where('is_published', true)->distinct()->pluck('category');

        return view('vaccine::articles.index', compact('articles', 'categories'));
    }

    /**
     * Hiển thị chi tiết bài viết tin tức.
     */
    public function show($slug)
    {
        $article = Article::where('slug', $slug)->where('is_published', true)->firstOrFail();
        $article->increment('views');

        $relatedArticles = Article::where('is_published', true)
            ->where('id', '!=', $article->id)
            ->take(3)
            ->get();

        return view('vaccine::articles.show', compact('article', 'relatedArticles'));
    }
}
