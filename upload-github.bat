@echo off
title Upload Presensi UNPAM ke GitHub
echo ========================================================
echo    PANDUAN OTOMATIS UPLOAD PROYEK KE GITHUB
echo ========================================================
echo.

set PATH=C:\laragon\bin\git\cmd;%PATH%

cd /d "%~dp0"

echo [1/4] Memeriksa status Git...
git init
git config --global user.name "Pengembang UNPAM"
git config --global user.email "developer@unpam.ac.id"

echo.
echo [2/4] Menambahkan seluruh file proyek...
git add .
git commit -m "feat: initial release presensi unpam"

echo.
echo ========================================================
echo Masukkan URL Repositori GitHub baru Anda
echo Contoh: https://github.com/username/nama-repo.git
echo ========================================================
set /p REPO_URL="URL GitHub: "

if "%REPO_URL%"=="" (
    echo [ERROR] URL GitHub tidak boleh kosong!
    pause
    exit /b
)

git remote remove origin >nul 2>&1
git remote add origin %REPO_URL%
git branch -M main

echo.
echo [3/4] Mengunggah (push) ke GitHub...
git push -u origin main

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================================
    echo  BERHASIL! Proyek Anda sudah terunggah ke GitHub!
    echo ========================================================
) else (
    echo.
    echo ========================================================
    echo  Jika gagal autentikasi, gunakan GitHub Desktop 
    echo  atau buat Personal Access Token di github.com/settings/tokens
    echo ========================================================
)

echo.
pause
