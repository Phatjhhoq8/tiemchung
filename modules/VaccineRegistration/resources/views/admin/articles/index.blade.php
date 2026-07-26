@extends('vaccine::layouts.admin')

@section('title', 'Quản Lý Bài Viết & Tin Tức Y Tế')
@section('page_title', 'Quản Lý Bài Viết & Tin Tức Y Tế')

@section('admin_content')
<div class="card-modern">
    
    {{-- Header: Title + Nút thêm --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
        <div>
            <h2 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0;">
                Danh sách bài viết tin tức
                <span style="font-size: 14px; font-weight: 400; color: var(--text-muted);">({{ $articles->count() }} bài viết)</span>
            </h2>
        </div>
        <a href="{{ route('admin.articles.create') }}" class="btn-modern btn-modern-primary">
            <i data-lucide="plus-circle"></i> Thêm bài viết mới
        </a>
    </div>

    @if(session('success'))
        <div style="background-color: #dcfce7; color: #15803d; padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 600;">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive-modern">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 80px; text-align: center;">#ID</th>
                    <th style="width: 130px; text-align: center;">Hình ảnh</th>
                    <th>Tiêu đề bài viết</th>
                    <th style="width: 180px;">Chuyên mục</th>
                    <th style="width: 140px; text-align: center;">Trạng thái</th>
                    <th style="width: 180px; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles as $article)
                    <tr>
                        <td style="text-align: center; font-weight: 700; color: var(--text-muted);">#{{ $article->id }}</td>
                        <td style="text-align: center;">
                            <img src="{{ asset('images/vaccines/' . ($article->image ?: 'default_vaccine.jpg')) }}" alt="{{ $article->title }}" style="width: 90px; height: 60px; object-fit: cover; border-radius: 6px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);">
                        </td>
                        <td>
                            <strong style="color: var(--text-primary); display: block;">{{ $article->title }}</strong>
                        </td>
                        <td>
                            <span class="badge-modern badge-modern-info">{{ $article->category }}</span>
                        </td>
                        <td style="text-align: center;">
                            @if($article->is_published)
                                <span class="badge-modern badge-modern-success">Hiển thị</span>
                            @else
                                <span class="badge-modern badge-modern-danger">Ẩn</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div style="display: inline-flex; gap: 8px; justify-content: center; align-items: center; width: 100%;">
                                <a href="{{ route('admin.articles.edit', $article->id) }}" class="btn-action-sm">
                                    <i data-lucide="edit-2" style="width: 13px; height: 13px;"></i> Sửa
                                </a>
                                <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-sm btn-action-danger">
                                        <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 40px; text-align: center; color: var(--text-light);">Chưa có bài viết nào. Hãy bấm "Thêm bài viết mới" để tạo bài viết đầu tiên.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
