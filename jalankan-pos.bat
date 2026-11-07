@echo off
title POS PRO SYSTEM ONLINE - LARAVEL 11
color 0A

echo ===================================================
echo     POS SYSTEM MULTI-GATEWAY ONLINE (LARAVEL 11)
echo ===================================================
echo.

echo [1/3] Memeriksa Layanan Database MySQL...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL daemon sedang berjalan.
) else (
    echo [INFO] Menjalankan MySQL XAMPP di background...
    start /B "" "D:\xampp\mysql\bin\mysqld.exe" --defaults-file="D:\xampp\mysql\bin\my.ini" --console
    timeout /t 3 /nobreak >nul
)

echo.
echo [2/3] Alamat IP Lokal Jaringan untuk Kasir / Perangkat Lain:
for /f "tokens=4" %%a in ('route print^|find " 0.0.0.0"') do set IP=%%a
echo       http://localhost:8000
echo       http://%IP%:8000 (Akses dari HP / Tablet / Laptop Kasir di Wi-Fi yang sama)
echo.

echo [3/3] Membuka Browser dan Menjalankan Server Laravel...
start http://localhost:8000
php artisan serve --host=0.0.0.0 --port=8000
pause
