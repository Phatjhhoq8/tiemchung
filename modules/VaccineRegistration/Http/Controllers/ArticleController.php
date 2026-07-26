<?php
/**
 * Chức năng: ArticleController xử lý hiển thị trang danh sách tin tức y học và trang chi tiết bài viết cho khách hàng.
 * Lý do tạo: Phục vụ chia trang Tin Tức riêng biệt theo yêu cầu người dùng - Đảm bảo dữ liệu hiển thị duy nhất không trùng lặp 100% trên mọi trang.
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
        // 1. Hot News stream for Hero Section (Top 5 latest articles)
        $hotNews = Article::where('is_published', true)->latest()->take(5)->get();
        $hotNewsIds = $hotNews->pluck('id')->toArray();

        // 2. Main Articles Feed Query
        $query = Article::where('is_published', true);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('summary', 'like', '%' . $searchTerm . '%');
            });
        }

        // Loại trừ 5 bài hotNews ra khỏi danh sách nằm ngang phía dưới trên TẤT CẢ CÁC TRANG để chống trùng lặp 100% và chuẩn hóa offset phân trang
        if (!$request->filled('category') && !$request->filled('search')) {
            $query->whereNotIn('id', $hotNewsIds);
        }

        $articles = $query->latest()->paginate(10)->withQueryString();
        
        // Dynamic category list without '&' character
        $rawCategories = Article::where('is_published', true)->whereNotNull('category')->distinct()->pluck('category');
        $categories = $rawCategories->map(function($cat) {
            return str_replace('&', 'và', $cat);
        })->unique()->values();

        return view('vaccine::articles.index', compact('articles', 'categories', 'hotNews'));
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
