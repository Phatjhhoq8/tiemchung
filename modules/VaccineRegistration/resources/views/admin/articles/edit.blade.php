@extends('vaccine::layouts.admin')

@section('title', 'Chỉnh Sửa Bài Viết')

@section('admin_content')
<div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <div style="margin-bottom: 24px;">
        <a href="{{ route('admin.articles.index') }}" style="color: #64748b; text-decoration: none; font-size: 14px; font-weight: 600;">← Quay lại danh sách</a>
        <h1 style="font-size: 24px; font-weight: 800; color: #1e293b; margin-top: 8px;">Chỉnh Sửa Bài Viết</h1>
    </div>

    <form action="{{ route('admin.articles.update', $article->id) }}" method="POST" style="background: #ffffff; padding: 32px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 8px;">Tiêu đề bài viết *</label>
            <input type="text" name="title" value="{{ old('title', $article->title) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 8px;">Chuyên mục *</label>
            <select name="category" required style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                <option value="Khuyến cáo Y tế" {{ old('category', $article->category) == 'Khuyến cáo Y tế' ? 'selected' : '' }}>Khuyến cáo Y tế</option>
                <option value="Vắc Xin Mới" {{ old('category', $article->category) == 'Vắc Xin Mới' ? 'selected' : '' }}>Vắc Xin Mới</option>
                <option value="Chăm Sóc Bé" {{ old('category', $article->category) == 'Chăm Sóc Bé' ? 'selected' : '' }}>Chăm Sóc Bé</option>
                <option value="Tin Tức Phòng Khám" {{ old('category', $article->category) == 'Tin Tức Phòng Khám' ? 'selected' : '' }}>Tin Tức Phòng Khám</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 8px;">Tên file hình ảnh (Ví dụ: 13. Vaxigrip Tetra.jpg)</label>
            <input type="text" name="image" value="{{ old('image', $article->image) }}" placeholder="13. Vaxigrip Tetra.jpg" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 8px;">Tóm tắt ngắn</label>
            <textarea name="summary" rows="3" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">{{ old('summary', $article->summary) }}</textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 8px;">Nội dung chi tiết</label>
            <textarea name="content" id="editor" rows="8" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">{{ old('content', $article->content) }}</textarea>
        </div>

        <div style="margin-bottom: 24px; display: flex; gap: 24px;">
            <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; color: #334155; cursor: pointer;">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $article->is_published) ? 'checked' : '' }}> Hiển thị trên website
            </label>
            <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; color: #334155; cursor: pointer;">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $article->is_featured) ? 'checked' : '' }}> Bài viết nổi bật
            </label>
        </div>

        <button type="submit" style="background-color: var(--primary-color, #c8102e); color: #ffffff; padding: 12px 24px; border-radius: 8px; border: none; font-weight: 700; font-size: 14px; cursor: pointer;">Lưu thay đổi</button>
    </form>
</div>

<!-- Load TinyMCE 6 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#editor',
        height: 450,
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor | table image media | code fullscreen preview',
        branding: false,
        promotion: false
    });
</script>
@endsection
