<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === 'admin') return redirect()->route('admin.dashboard');
        if ($user->role === 'dosen') return redirect()->route('dosen.dashboard');
        return redirect()->route('mahasiswa.dashboard');
    }
    return redirect()->route('login');
});

// Autentikasi
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/password/update', [AuthController::class, 'updatePassword'])->name('password.update')->middleware('auth');

// Sisi ADMIN
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Sistem Buat & Kelola Akun Dosen & Mahasiswa
    Route::get('/akun', [AdminController::class, 'akun'])->name('akun');
    Route::post('/akun/reset-password/{id}', [AdminController::class, 'resetPasswordAkun'])->name('akun.resetPassword');
    Route::delete('/akun/{id}', [AdminController::class, 'deleteAkun'])->name('akun.delete');

    // CRUD Kelas
    Route::get('/kelas', [AdminController::class, 'kelas'])->name('kelas');
    Route::post('/kelas', [AdminController::class, 'storeKelas'])->name('kelas.store');
    Route::put('/kelas/{id}', [AdminController::class, 'updateKelas'])->name('kelas.update');
    Route::delete('/kelas/{id}', [AdminController::class, 'deleteKelas'])->name('kelas.delete');
    
    // CRUD Mahasiswa
    Route::get('/mahasiswa', [AdminController::class, 'mahasiswa'])->name('mahasiswa');
    Route::post('/mahasiswa', [AdminController::class, 'storeMahasiswa'])->name('mahasiswa.store');
    Route::put('/mahasiswa/{id}', [AdminController::class, 'updateMahasiswa'])->name('mahasiswa.update');
    Route::delete('/mahasiswa/{id}', [AdminController::class, 'deleteMahasiswa'])->name('mahasiswa.delete');

    // CRUD Dosen
    Route::get('/dosen', [AdminController::class, 'dosen'])->name('dosen');
    Route::post('/dosen', [AdminController::class, 'storeDosen'])->name('dosen.store');
    Route::put('/dosen/{id}', [AdminController::class, 'updateDosen'])->name('dosen.update');
    Route::delete('/dosen/{id}', [AdminController::class, 'deleteDosen'])->name('dosen.delete');

    // CRUD Mata Kuliah & Jadwal (Fitur Disatukan Berjenjang)
    Route::get('/matakuliah-jadwal', [AdminController::class, 'matakuliahJadwal'])->name('matakuliah_jadwal');
    Route::get('/matakuliah', function () { return redirect()->route('admin.matakuliah_jadwal'); })->name('matakuliah');
    Route::post('/matakuliah', [AdminController::class, 'storeMataKuliah'])->name('matakuliah.store');
    Route::put('/matakuliah/{id}', [AdminController::class, 'updateMataKuliah'])->name('matakuliah.update');
    Route::delete('/matakuliah/{id}', [AdminController::class, 'deleteMataKuliah'])->name('matakuliah.delete');

    // CRUD Jadwal & Plotting
    Route::get('/jadwal', function () { return redirect()->route('admin.matakuliah_jadwal'); })->name('jadwal');
    Route::post('/jadwal', [AdminController::class, 'storeJadwal'])->name('jadwal.store');
    Route::put('/jadwal/{id}', [AdminController::class, 'updateJadwal'])->name('jadwal.update');
    Route::delete('/jadwal/{id}', [AdminController::class, 'deleteJadwal'])->name('jadwal.delete');

    // Import Batch Drag and Drop
    Route::post('/import/{entity}', [AdminController::class, 'importBatch'])->name('import.batch');
});

// Sisi DOSEN
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/', [DosenController::class, 'dashboard'])->name('dashboard');
    Route::get('/matakuliah', [DosenController::class, 'mataKuliah'])->name('matakuliah');
    Route::get('/matakuliah/{mata_kuliah_id}/kelas', [DosenController::class, 'kelas'])->name('kelas');
    Route::get('/jadwal/{jadwal_id}/pertemuan', [DosenController::class, 'pertemuan'])->name('pertemuan');
    
    Route::post('/pertemuan/{pertemuan_id}/update', [DosenController::class, 'updatePertemuan'])->name('pertemuan.update');
    Route::post('/pertemuan/{pertemuan_id}/generate-qr', [DosenController::class, 'generateQr'])->name('pertemuan.generateQr');
    Route::post('/pertemuan/{pertemuan_id}/akhiri-qr', [DosenController::class, 'akhiriQr'])->name('pertemuan.akhiriQr');
    Route::get('/pertemuan/{pertemuan_id}/qr', [DosenController::class, 'tampilQr'])->name('tampilQr');

    Route::get('/pertemuan/{pertemuan_id}/presensi', [DosenController::class, 'presensi'])->name('presensi');
    Route::post('/presensi/{presensi_id}/manual', [DosenController::class, 'updatePresensiManual'])->name('presensi.manual');

    // Menu Data Mahasiswa Berjenjang
    Route::get('/data-mahasiswa', [DosenController::class, 'dataMahasiswaMatkul'])->name('mhs.matakuliah');
    Route::get('/data-mahasiswa/{mata_kuliah_id}/kelas', [DosenController::class, 'dataMahasiswaKelas'])->name('mhs.kelas');
    Route::get('/data-mahasiswa/jadwal/{jadwal_id}', [DosenController::class, 'dataMahasiswaList'])->name('mhs.list');

    // Menu Rekap Presensi Berjenjang
    Route::get('/rekap', [DosenController::class, 'rekapMatkul'])->name('rekap.matakuliah');
    Route::get('/rekap/{mata_kuliah_id}/kelas', [DosenController::class, 'rekapKelas'])->name('rekap.kelas');
    Route::get('/rekap/jadwal/{jadwal_id}', [DosenController::class, 'rekapDetailKelas'])->name('rekap.detail');

    // Profil Dosen
    Route::get('/profil', [DosenController::class, 'profil'])->name('profil');
});

// Sisi MAHASISWA
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/', [MahasiswaController::class, 'dashboard'])->name('dashboard');
    Route::get('/presensi', [MahasiswaController::class, 'presensi'])->name('presensi');
    Route::get('/presensi/{jadwal_id}', [MahasiswaController::class, 'pertemuan'])->name('pertemuan');
    
    Route::get('/scan/{pertemuan_id}', [MahasiswaController::class, 'scanView'])->name('scan');
    Route::post('/scan/proses', [MahasiswaController::class, 'prosesScan'])->name('scan.proses');

    Route::get('/profil', [MahasiswaController::class, 'profil'])->name('profil');
});
