<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Quản Trị - Medicare Cờ Đỏ</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c8102e 150%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .login-card {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border: 1px solid #e2e8f0;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo {
            font-size: 40px;
            color: #c8102e;
            margin-bottom: 10px;
        }
        .login-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            margin: 0 0 8px 0;
        }
        .login-header p {
            color: #64748b;
            font-size: 14px;
            margin: 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #475569;
            font-size: 14px;
        }
        .input-group {
            position: relative;
        }
        .input-group i, .input-group svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            width: 18px;
            height: 18px;
            pointer-events: none;
        }
        .input-group input {
            width: 100%;
            padding: 12px 16px 12px 42px !important;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            transition: all 0.2s ease;
        }
        .input-group input:focus {
            border-color: #c8102e;
            box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.15);
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: #c8102e;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
        }
        .btn-login:hover {
            background-color: #a00d24;
        }
        .alert {
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-danger {
            background-color: #fde8e8;
            color: #9b1c1c;
            border: 1px solid #fbd5d5;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo"><i data-lucide="shield-check"></i></div>
            <h1>Đăng Nhập Admin</h1>
            <p>Hệ thống quản trị Medicare Cờ Đỏ</p>
        </div>

        @if($errors->has('auth_failed'))
            <div class="alert alert-danger">
                <i data-lucide="alert-circle"></i>
                <span>{{ $errors->first('auth_failed') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i data-lucide="alert-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('admin.login') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <div class="input-group">
                    <i data-lucide="user"></i>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" placeholder="Tên đăng nhập admin" required autofocus autocomplete="off">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <div class="input-group">
                    <i data-lucide="lock"></i>
                    <input type="password" name="password" id="password" placeholder="Mật khẩu bảo mật" required>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <span>Đăng nhập</span> <i data-lucide="arrow-right"></i>
            </button>
        </form>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
