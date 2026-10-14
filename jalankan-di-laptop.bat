@echo off
title Jalankan Presensi UNPAM Lokal
echo ========================================================
echo       MENJALANKAN PRESENSI UNPAM DI LOCALHOST
echo ========================================================
echo.

cd /d "%~dp0"

:: 1. Prioritaskan PHP bawaan di dalam folder paket ini
if exist "%~dp0php\php.exe" (
    set "PHP_BIN=%~dp0php\php.exe"
    set "PATH=%~dp0php;%PATH%"
    goto start_app
)

:: 2. Cek Laragon PHP 8.5
if exist "C:\laragon\bin\php\php-8.5.11-nts-Win32-vs17-x64\php.exe" (
    set "PHP_BIN=C:\laragon\bin\php\php-8.5.11-nts-Win32-vs17-x64\php.exe"
    set "PATH=C:\laragon\bin\php\php-8.5.11-nts-Win32-vs17-x64;%PATH%"
    goto start_app
)

:: 3. Cek Default PHP di sistem
set "PHP_BIN=php"

:start_app
echo [INFO] Menggunakan: %PHP_BIN%

if not exist ".env" (
    echo [INFO] Menyiapkan file konfigurasi lokal .env...
    copy .env.example .env >nul
    "%PHP_BIN%" artisan key:generate
)

echo.
echo [1/2] Memastikan database dan cache siap...
"%PHP_BIN%" artisan config:clear >nul 2>&1
"%PHP_BIN%" artisan view:clear >nul 2>&1
"%PHP_BIN%" artisan route:clear >nul 2>&1

echo.
echo [2/2] Menyalakan Web Server Laravel...
echo ========================================================
echo  Server Aktif di: http://127.0.0.1:8000
echo  Buka browser Anda dan akses link di atas.
echo.
echo  AKUN LOGIN LOKAL:
echo  - Admin     : admin     / admin123
echo  - Dosen     : dosen     / dosen123
echo  - Mahasiswa : 2211001   / mhs12345
echo ========================================================
echo  (Tekan Ctrl+C untuk menghentikan server jika selesai)
echo ========================================================
echo.

"%PHP_BIN%" artisan serve --port=8000
pause
