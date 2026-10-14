@echo off
title Jalankan Presensi UNPAM Lokal
echo ========================================================
echo       MENJALANKAN PRESENSI UNPAM DI LOCALHOST
echo ========================================================
echo.

cd /d "%~dp0"

:: 1. Cek Database MySQL di port 3306
netstat -ano | findstr /R ":3306 " >nul 2>&1
if %ERRORLEVEL% EQU 0 goto mysql_ok

echo [INFO] Mendeteksi MySQL belum aktif, mencoba menyalakan MySQL...
for /d %%d in ("C:\laragon\bin\mysql\mysql-*") do (
    if exist "%%d\bin\mysqld.exe" (
        start /B "" "%%d\bin\mysqld.exe" --defaults-file="%%d\my.ini" >nul 2>&1
    )
)
timeout /t 3 /nobreak >nul

netstat -ano | findstr /R ":3306 " >nul 2>&1
if %ERRORLEVEL% EQU 0 goto mysql_ok

echo.
echo ========================================================
echo  PERINGATAN: Database MySQL belum aktif di port 3306!
echo  Jika terjadi error koneksi database, silakan buka
echo  Laragon atau XAMPP dan klik "Start All".
echo ========================================================
echo.
goto check_php

:mysql_ok
echo [OK] Database MySQL aktif di port 3306.

:check_php
:: 2. Prioritaskan PHP bawaan di dalam folder paket ini
if exist "%~dp0php\php.exe" (
    set "PHP_BIN=%~dp0php\php.exe"
    set "PATH=%~dp0php;%PATH%"
    goto start_app
)

:: 3. Cek Laragon PHP 8.5
if exist "C:\laragon\bin\php\php-8.5.11-nts-Win32-vs17-x64\php.exe" (
    set "PHP_BIN=C:\laragon\bin\php\php-8.5.11-nts-Win32-vs17-x64\php.exe"
    set "PATH=C:\laragon\bin\php\php-8.5.11-nts-Win32-vs17-x64;%PATH%"
    goto start_app
)

:: 4. Cek Default PHP di sistem
set "PHP_BIN=php"

:start_app
echo [INFO] Menggunakan: %PHP_BIN%

if not exist ".env" (
    echo [INFO] Menyiapkan file konfigurasi lokal .env...
    copy .env.example .env >nul
    "%PHP_BIN%" artisan key:generate
)

echo.
echo [1/2] Membersihkan cache aplikasi...
"%PHP_BIN%" artisan config:clear >nul 2>&1
"%PHP_BIN%" artisan view:clear >nul 2>&1
"%PHP_BIN%" artisan route:clear >nul 2>&1

echo.
echo [2/2] Menyalakan Web Server Laravel...
echo ========================================================
echo  Server Aktif di: http://127.0.0.1:8000
echo  Browser Anda akan terbuka secara otomatis dalam 2 detik.
echo.
echo  AKUN LOGIN LOKAL:
echo  - Admin     : admin     / admin123
echo  - Dosen     : dosen     / dosen123
echo  - Mahasiswa : 2211001   / mhs12345
echo ========================================================
echo  (Tekan Ctrl+C untuk menghentikan server jika selesai)
echo ========================================================
echo.

:: Otomatis buka web browser
start "" cmd /c "timeout /t 2 /nobreak >nul & start http://127.0.0.1:8000"

:: Jalankan server Laravel
"%PHP_BIN%" artisan serve --port=8000
pause
