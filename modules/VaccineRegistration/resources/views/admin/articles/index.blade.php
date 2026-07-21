@extends('vaccine::layouts.admin')

@section('title', 'Quản Lý Bài Viết & Tin Tức Y Tế')

@section('admin_content')
<div class="admin-articles-container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0;">Quản Lý Bài Viết & Tin Tức Y Tế (Mục 10)</h1>
            <p style="color: #64748b; font-size: 14px; margin-top: 4px;">Cập nhật các bài viết tin tức y khoa hiển thị trên trang chủ.</p>
        </div>
        <a href="{{ route('admin.articles.create') }}" style="background-color: var(--primary-color, #c8102e); color: #ffffff; padding: 10px 20px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i> Thêm bài viết mới
        </a>
    </div>

    @if(session('success'))
        <div style="background-color: #dcfce7; color: #15803d; padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 600;">
            {{ session('success') }}
        </div>
    @endif

    <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
            <thead>
                <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 700;">
                    <th style="padding: 16px 20px;">#ID</th>
                    <th style="padding: 16px 20px;">Hình ảnh</th>
                    <th style="padding: 16px 20px;">Tiêu đề bài viết</th>
                    <th style="padding: 16px 20px;">Chuyên mục</th>
                    <th style="padding: 16px 20px;">Trạng thái</th>
                    <th style="padding: 16px 20px; text-align: right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles as $article)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 16px 20px; font-weight: 700; color: #64748b;">#{{ $article->id }}</td>
                        <td style="padding: 16px 20px;">
                            <img src="{{ asset('images/vaccines/' . ($article->image ?: 'default_vaccine.jpg')) }}" alt="{{ $article->title }}" style="width: 60px; height: 40px; object-fit: cover; border-radius: 6px;">
                        </td>
                        <td style="padding: 16px 20px;">
                            <strong style="color: #1e293b; display: block;">{{ $article->title }}</strong>
                            <span style="font-size: 12px; color: #94a3b8;">/{{ $article->slug }}</span>
                        </td>
                        <td style="padding: 16px 20px;">
                            <span style="background-color: #f1f5f9; color: #334155; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">{{ $article->category }}</span>
                        </td>
                        <td style="padding: 16px 20px;">
                            @if($article->is_published)
                                <span style="background-color: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 700;">Hiển thị</span>
                            @else
                                <span style="background-color: #f1f5f9; color: #64748b; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 700;">Ẩn</span>
                            @endif
                        </td>
                        <td style="padding: 16px 20px; text-align: right;">
                            <div style="display: inline-flex; gap: 8px;">
                                <a href="{{ route('admin.articles.edit', $article->id) }}" style="color: #0284c7; padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; text-decoration: none; font-weight: 600; font-size: 13px;">Sửa</a>
                                <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="color: #dc2626; padding: 6px 12px; border-radius: 6px; border: 1px solid #fca5a5; background: #fef2f2; font-weight: 600; font-size: 13px; cursor: pointer;">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 40px; text-align: center; color: #94a3b8;">Chưa có bài viết nào. Hãy bấm "Thêm bài viết mới" để tạo bài viết đầu tiên.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
