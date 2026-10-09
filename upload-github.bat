@echo off
title Push Update Presensi UNPAM ke GitHub
echo ========================================================
echo    UPLOAD UPDATE PROYEK KE GITHUB (presensiQR)
echo ========================================================
echo.

set PATH=C:\laragon\bin\git\cmd;%PATH%
cd /d "%~dp0"

echo [1/3] Memeriksa status Git...
git remote get-url origin >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    git remote add origin https://github.com/etenalisz-spec/presensiQR.git
)

echo [2/3] Mengemas seluruh perubahan...
git add .
git commit -m "fix: update composer.lock, trust proxies and enable tidb cloud ssl"

echo.
echo [3/3] Mengunggah (push) ke GitHub...
git push -u origin main

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================================
    echo  BERHASIL! Update sudah terunggah ke GitHub!
    echo ========================================================
) else (
    echo.
    echo ========================================================
    echo  Jika diminta login / token, silakan masukkan kredensial GitHub Anda.
    echo ========================================================
)

echo.
pause
