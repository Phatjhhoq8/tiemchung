@extends('vaccine::layouts.app')

@section('title', 'Lỗi 429 - Quá nhiều yêu cầu')

@section('content')
<div class="container mx-auto" style="max-width: 1200px; padding: 80px 15px; text-align: center; min-height: 60vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
    <div style="font-size: 120px; font-weight: 900; color: #8b5cf6; line-height: 1; text-shadow: 4px 4px 0px #ede9fe;">429</div>
    <h1 style="font-size: 32px; font-weight: 700; color: #334155; margin-top: 24px; margin-bottom: 16px;">Quá nhiều yêu cầu</h1>
    <p style="font-size: 16px; color: #64748b; max-width: 500px; margin: 0 auto 32px auto; line-height: 1.6;">
        Hệ thống nhận thấy có quá nhiều yêu cầu từ bạn trong thời gian ngắn. Vui lòng chờ một lát trước khi thử lại.
    </p>
    <a href="{{ url('/') }}" style="display: inline-flex; align-items: center; gap: 8px; background-color: #0d9488; color: white; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px; transition: background-color 0.3s; box-shadow: 0 4px 6px -1px rgba(13, 148, 136, 0.2), 0 2px 4px -1px rgba(13, 148, 136, 0.1);" onmouseover="this.style.backgroundColor='#0f766e'" onmouseout="this.style.backgroundColor='#0d9488'">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Trở về trang chủ
    </a>
</div>
@endsection
