@echo off
title Khoi Chay Du An Tiem Chung VNVC (Laravel 11)
cls

echo =====================================================================
echo    HE THONG TU DONG KHOI CHAY DU AN TIEM CHUNG VNVC (LARAVEL 11)
echo =====================================================================
echo.

:: Dat duong dan tuyet doi truc tiep den file thuc thi php.exe cua Herd
set PHP_PATH=%USERPROFILE%\.config\herd\bin\php84\php.exe
set COMPOSER_PATH=%USERPROFILE%\.config\herd\bin\composer.phar

:: 1. Kiem tra xem file php.exe cua Herd co ton tai khong
if not exist "%PHP_PATH%" (
    echo [LOI] Khong tim thay file php.exe cua Laravel Herd tai duong dan:
    echo "%PHP_PATH%"
    echo.
    echo Vui long dam bao ban da cai dat va khoi dong Laravel Herd de no tu dong tai PHP.
    echo Dang mo trang chu Laravel Herd de ban kiem tra...
    start https://herd.laravel.com
    echo.
    echo Nhan phim bat ky de dong cua so nay.
    pause
    exit
)

echo [+] Da tim thay PHP 8.4 cua Laravel Herd!
echo.

:: 2. Tu dong copy file cau hinh .env neu chua co
if not exist .env (
    echo [+] Dang tao file cau hinh .env tu file mau...
    copy .env.example .env >nul
)

:: 3. Tu dong tao file database SQLite neu chua co
if not exist database\database.sqlite (
    echo [+] Dang khoi tao file co so du lieu SQLite...
    type nul > database\database.sqlite
)

:: 4. Kiem tra va cai dat dependencies (thu muc vendor)
if not exist vendor (
    echo [+] Khong tim thay thu muc vendor. Dang tien hanh tai cac thu vien...
    echo     Tien trinh nay co the mat 1 den 2 phut, vui long cho...
    "%PHP_PATH%" "%COMPOSER_PATH%" install --no-scripts
    if errorlevel 1 (
        echo.
        echo [LOI] Co loi xay ra trong qua trinh chay "composer install".
        echo Vui long kiem tra ket noi mang va thu lai.
        pause
        exit
    )
)

:: 5. Tao key cho ung dung neu chua co
findstr /C:"APP_KEY=base64:" .env >nul
if errorlevel 1 (
    echo [+] Dang tao khoa bao mat APP_KEY cho ung dung...
    "%PHP_PATH%" artisan key:generate --force
)

:: 6. Chay migration va seed du lieu vac xin neu CSDL moi tinh
echo [+] Dang cap nhat database va nap du lieu vac xin mau...
"%PHP_PATH%" artisan migrate --seed --force

echo.
echo =====================================================================
echo   [THANH CONG] CAI DAT VA CAU HINH HOAN TAT!
echo   Cua so nay se duoc giu lai de duy tri Web Server hoat dong.
echo   Log truy cap cua ban se duoc in ra ben duoi.
echo =====================================================================
echo.

:: Tu dong mo trinh duyet truoc khi khoi chay server
start http://127.0.0.1:8000

:: 7. Khoi chay truc tiep Web Server de giu cua so luon mo va hien thi log/loi
"%PHP_PATH%" artisan serve

echo.
echo Server da dung hoat dong.
pause
exit
