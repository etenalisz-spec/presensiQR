@extends('layouts.app', ['title' => 'Profil Mahasiswa'])

@section('content')
<div class="unpam-banner">
    <h2>PROFIL SAYA</h2>
    <p>Informasi Data Akademik Mahasiswa Universitas Pamulang</p>
</div>

<div class="card" style="max-width: 680px; margin: 0 auto">
    <div style="display: flex; align-items: center; gap: 18px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border-line)">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--unpam-light-blue); color: var(--unpam-blue); display: grid; place-items: center; font-size: 24px; font-weight: 800">
            {{ strtoupper(substr($mahasiswa->nama_lengkap, 0, 2)) }}
        </div>
        <div>
            <h3 style="font-size: 18px; font-weight: 700; color: #1E293B">{{ $mahasiswa->nama_lengkap }}</h3>
            <p style="font-size: 13.5px; color: var(--text-muted)">NIM: <strong>{{ $mahasiswa->nim }}</strong> &bull; Kelas: <strong>{{ $mahasiswa->kelas->nama_kelas }}</strong></p>
        </div>
    </div>

    <!-- Data Profil Read-Only (Mahasiswa Tidak Bisa Mengubahnya) -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px">
        <div class="form-group">
            <label>NIM</label>
            <input type="text" class="form-control" value="{{ $mahasiswa->nim }}" readonly style="background: #F8FAFC; color: #64748B">
        </div>
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" class="form-control" value="{{ $mahasiswa->nama_lengkap }}" readonly style="background: #F8FAFC; color: #64748B">
        </div>
        <div class="form-group">
            <label>Program Studi</label>
            <input type="text" class="form-control" value="{{ $mahasiswa->kelas->prodi }}" readonly style="background: #F8FAFC; color: #64748B">
        </div>
        <div class="form-group">
            <label>Kelas</label>
            <input type="text" class="form-control" value="{{ $mahasiswa->kelas->nama_kelas }}" readonly style="background: #F8FAFC; color: #64748B">
        </div>
        <div class="form-group">
            <label>Semester</label>
            <input type="text" class="form-control" value="{{ $mahasiswa->kelas->semester }}" readonly style="background: #F8FAFC; color: #64748B">
        </div>
        <div class="form-group">
            <label>Status Akademik</label>
            <input type="text" class="form-control" value="{{ $mahasiswa->status }}" readonly style="background: #F8FAFC; color: #0C7A52; font-weight: 700">
        </div>
        <div class="form-group" style="grid-column: span 2">
            <label>Email Kampus</label>
            <input type="text" class="form-control" value="{{ $mahasiswa->user->email }}" readonly style="background: #F8FAFC; color: #64748B">
        </div>
    </div>

    <!-- Tombol Khusus Ubah Password -->
    <div style="padding-top: 16px; border-top: 1px solid var(--border-line); display: flex; justify-content: flex-end">
        <button type="button" class="btn btn-primary" onclick="document.getElementById('pwd-modal').style.display = 'grid'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Ubah Password
        </button>
    </div>
</div>

<!-- Modal Ubah Password -->
<div id="pwd-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Ubah Password Akun</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">Masukkan password saat ini dan password baru Anda.</p>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Password Saat Ini</label>
                <input type="password" name="current_password" class="form-control" placeholder="Masukkan password lama" required>
            </div>
            <div class="form-group">
                <label>Password Baru (Minimal 6 karakter)</label>
                <input type="password" name="new_password" class="form-control" placeholder="Masukkan password baru" required>
            </div>
            <div class="form-group">
                <label>Konfirmasi Password Baru</label>
                <input type="password" name="new_password_confirmation" class="form-control" placeholder="Ulangi password baru" required>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('pwd-modal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
            </div>
        </form>
    </div>
</div>
@endsection
