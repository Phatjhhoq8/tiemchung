@echo off
title Khoi Chay Du An Tiem Chung VNVC (Laravel 11)
cls

echo =====================================================================
echo    HE THONG TU DONG KHOI CHAY DU AN TIEM CHUNG VNVC (LARAVEL 11)
echo =====================================================================
echo.

:: 1. Phat hien va thiet lap duong dan PHP
where php >nul 2>nul
if %errorlevel% equ 0 (
    set PHP_CMD=php
    echo [+] Da tim thay PHP he thong!
) else (
    set PHP_PATH=%USERPROFILE%\.config\herd\bin\php84\php.exe
    if exist "%PHP_PATH%" (
        set PHP_CMD="%PHP_PATH%"
        echo [+] Da tim thay PHP 8.4 cua Laravel Herd!
    ) else (
        echo [LOI] Khong tim thay file php trong PATH he thong hoac Laravel Herd.
        echo Dang mo trang chu Laravel Herd de ban tai va cai dat...
        start https://herd.laravel.com
        pause
        exit
    )
)

:: 2. Phat hien va thiet lap duong dan Composer
where composer >nul 2>nul
if %errorlevel% equ 0 (
    set COMPOSER_CMD=composer
    echo [+] Da tim thay Composer he thong!
) else (
    set COMPOSER_PATH=%USERPROFILE%\.config\herd\bin\composer.phar
    if exist "%COMPOSER_PATH%" (
        set COMPOSER_CMD="%PHP_PATH%" "%COMPOSER_PATH%"
        echo [+] Da tim thay Composer cua Laravel Herd!
    ) else (
        if exist composer.phar (
            set COMPOSER_CMD="%PHP_PATH%" composer.phar
            echo [+] Da tim thay composer.phar trong thu muc hien tai!
        ) else (
            echo [LOI] Khong tim thay Composer trong he thong hoac Laravel Herd.
            pause
            exit
        )
    )
)
echo.

:: 3. Tu dong copy file cau hinh .env neu chua co
if not exist .env (
    echo [+] Dang tao file cau hinh .env tu file mau...
    copy .env.example .env >nul
)
:: 4. Tu dong tao database MySQL neu chua co
call %PHP_CMD% database/create_db.php
if errorlevel 1 (
    echo.
    echo [LOI] Khong the khoi tao database MySQL. Vui long dam bao MySQL (XAMPP/Herd) da duoc mo.
    pause
    exit
)


:: 5. Kiem tra va cai dat dependencies (thu muc vendor)
if not exist vendor (
    echo [+] Khong tim thay thu muc vendor. Dang tien hanh tai cac thu vien...
    echo     Tien trinh nay co the mat 1 den 2 phut, vui long cho...
    call %COMPOSER_CMD% install --no-scripts
    if errorlevel 1 (
        echo.
        echo [LOI] Co loi xay ra trong qua trinh chay "composer install".
        echo Vui long kiem tra ket noi mang va thu lai.
        pause
        exit
    )
)

:: 6. Tao key cho ung dung neu chua co
findstr /C:"APP_KEY=base64:" .env >nul
if errorlevel 1 (
    echo [+] Dang tao khoa bao mat APP_KEY cho ung dung...
    call %PHP_CMD% artisan key:generate --force
)

:: 7. Chay migration va seed du lieu vac xin neu CSDL moi tinh
echo [+] Dang cap nhat database va nap du lieu vac xin mau...
call %PHP_CMD% artisan migrate --seed --force

echo.
echo =====================================================================
echo   [THANH CONG] CAI DAT VA CAU HINH HOAN TAT!
echo   Cua so nay se duoc giu lai de duy tri Web Server hoat dong.
echo   Log truy cap cua ban se duoc in ra ben duoi.
echo =====================================================================
echo.

:: Tu dong mo trinh duyet truoc khi khoi chay server
start http://127.0.0.1:8000

:: 8. Khoi chay Web Server dung PHP Built-in server
:: (Tranh loi bind cong hoac loi process block cua artisan serve tren Windows)
call %PHP_CMD% -S 127.0.0.1:8000 -t public

echo.
echo Server da dung hoat dong.
pause
exit
