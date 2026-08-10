<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('code') - @yield('title')</title>
    <style>
        :root { color-scheme: light; font-family: Arial, Helvetica, sans-serif; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f5f7fa; color: #172033; }
        main { width: min(90%, 42rem); padding: 3rem 1.5rem; text-align: center; }
        .code { margin: 0; color: #0f766e; font-size: clamp(4.5rem, 18vw, 8rem); font-weight: 800; line-height: 1; letter-spacing: -.06em; }
        h1 { margin: 1.25rem 0 .75rem; font-size: clamp(1.5rem, 5vw, 2rem); }
        p { margin: 0 auto 2rem; max-width: 36rem; color: #526074; font-size: 1rem; line-height: 1.7; }
        a { display: inline-block; border-radius: .5rem; background: #0f766e; color: #fff; padding: .75rem 1.15rem; font-weight: 700; text-decoration: none; }
        a:focus-visible { outline: 3px solid #5eead4; outline-offset: 3px; }
    </style>
</head>
<body>
    <main>
        <div class="code" aria-hidden="true">@yield('code')</div>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        <a href="/">Trở về trang chủ</a>
    </main>
</body>
</html>
