@echo off
title Push Update Presensi UNPAM ke GitHub
echo ========================================================
echo       UPLOAD UPDATE PROYEK KE GITHUB (presensiQR)
echo ========================================================
echo.

:: Tambahkan path Git dari berbagai kemungkinan instalasi
if exist "C:\laragon\bin\git\cmd\git.exe" set "PATH=C:\laragon\bin\git\cmd;%PATH%"
if exist "C:\Program Files\Git\cmd\git.exe" set "PATH=C:\Program Files\Git\cmd;%PATH%"
if exist "C:\Users\%USERNAME%\AppData\Local\Programs\Git\cmd\git.exe" set "PATH=C:\Users\%USERNAME%\AppData\Local\Programs\Git\cmd;%PATH%"

cd /d "%~dp0"

:: Cek keberadaan Git
where git >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Git tidak ditemukan di komputer!
    echo Silakan pastikan Laragon aktif atau install Git for Windows.
    echo.
    pause
    exit /b 1
)

:: Cek folder .git
if not exist ".git" (
    echo [INFO] Menginisialisasi repositori Git lokal...
    git init
    git branch -M main
    git remote add origin https://github.com/etenalisz-spec/presensiQR.git
)

:: Pastikan remote terhubung ke repositori GitHub yang benar
git remote get-url origin >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    git remote add origin https://github.com/etenalisz-spec/presensiQR.git
)

echo [1/3] Menyiapkan file perubahan...
git add .

echo.
echo [2/3] Membuat catatan commit...
git commit -m "fix(evaluasi): perbaiki overflow form modal admin, matriks presensi P1-P14 dosen, dan kolom ruang kelas"

echo.
echo [3/3] Mengunggah (push) ke GitHub main...
git push -u origin main

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================================
    echo  BERHASIL! Seluruh perubahan berhasil di-push ke GitHub!
    echo ========================================================
) else (
    echo.
    echo ========================================================
    echo  PERHATIAN: Push belum berhasil atau memerlukan autentikasi.
    echo  Jika diminta login / Personal Access Token GitHub,
    echo  silakan login ke akun GitHub Anda.
    echo ========================================================
)

echo.
pause
