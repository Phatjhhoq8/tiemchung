<?php
/**
 * Chức năng: Model Article đại diện cho bài viết, tin tức y khoa và kiến thức tiêm chủng.
 * Lý do tạo: Phục vụ quản lý dữ liệu bài viết động từ CSDL MySQL.
 */

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'articles';

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'image',
        'category',
        'is_published',
        'is_featured',
        'is_active',
        'views',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'views' => 'integer',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Article $article) {
            $article->is_active = false;
            $article->is_published = false;
            $article->save();
            return false; // Prevent hard deletion
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
