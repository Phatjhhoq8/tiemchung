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
        'views',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'views' => 'integer',
    ];
}
