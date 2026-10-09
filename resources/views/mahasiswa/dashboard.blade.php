@extends('layouts.app', ['title' => 'Dashboard Mahasiswa'])

@section('content')
<div class="unpam-banner">
    <h2>DASHBOARD MAHASISWA</h2>
    <p>Selamat datang, <strong>{{ $mahasiswa->nama_lengkap }}</strong> &bull; NIM: <strong>{{ $mahasiswa->nim }}</strong> &bull; Kelas: <strong>{{ $mahasiswa->kelas->nama_kelas }}</strong></p>
</div>

<!-- Kartu Profil Singkat Mahasiswa (Seperti Prototipe Awal Tapi Data Riil) -->
<div class="card" style="margin-bottom: 20px">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px">
        <div style="display: flex; gap: 16px; align-items: center">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--unpam-light-blue); color: var(--unpam-blue); display: grid; place-items: center; font-size: 22px; font-weight: 800">
                {{ strtoupper(substr($mahasiswa->nama_lengkap, 0, 2)) }}
            </div>
            <div>
                <h3 style="font-size: 18px; font-weight: 800; color: #1E293B">{{ $mahasiswa->nama_lengkap }}</h3>
                <p style="font-size: 13px; color: var(--text-muted); margin-top: 2px">
                    NIM: <strong>{{ $mahasiswa->nim }}</strong> &bull; Program Studi {{ $mahasiswa->kelas->prodi }}
                </p>
                <span class="badge badge-info" style="margin-top: 6px">Kelas {{ $mahasiswa->kelas->nama_kelas }}</span>
            </div>
        </div>

        <div>
            <a href="{{ route('mahasiswa.presensi') }}" class="btn btn-primary" style="height: 44px; padding: 0 20px; font-weight: 700">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/><path d="m9 14 2 2 4-4"/></svg>
                Buka Menu Presensi
            </a>
        </div>
    </div>
</div>

<!-- 3 Kartu Metrik Kehadiran Riil -->
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px">
    <div class="card" style="margin: 0; padding: 20px">
        <span style="font-size: 12px; font-weight: 700; color: #0C7A52; text-transform: uppercase">Jumlah Kehadiran</span>
        <div style="font-size: 32px; font-weight: 800; color: #0C7A52; margin-top: 6px">{{ $totalHadir }}</div>
        <small style="color: var(--text-muted)">Total sesi terkonfirmasi hadir</small>
    </div>

    <div class="card" style="margin: 0; padding: 20px">
        <span style="font-size: 12px; font-weight: 700; color: #C2352B; text-transform: uppercase">Jumlah Ketidakhadiran</span>
        <div style="font-size: 32px; font-weight: 800; color: #C2352B; margin-top: 6px">{{ $totalAbsen }}</div>
        <small style="color: var(--text-muted)">Total sesi absen / terlewat</small>
    </div>

    <div class="card" style="margin: 0; padding: 20px">
        <span style="font-size: 12px; font-weight: 700; color: var(--unpam-blue); text-transform: uppercase">Persentase Kehadiran</span>
        <div style="font-size: 32px; font-weight: 800; color: var(--unpam-blue); margin-top: 6px">{{ $persentaseHadir }}%</div>
        <small style="color: var(--text-muted)">Dari total sesi yang telah selesai</small>
    </div>
</div>

<!-- Batas Kehadiran Minimal 75% Syarat UAS (Persis Prototipe Awal) -->
<div class="card" style="margin-bottom: 20px">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px">
        <div>
            <h3 class="card-title" style="margin: 0">Batas Kehadiran Minimal Perkuliahan</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px">Syarat mengikuti Ujian Akhir Semester (UAS) adalah minimal 75% kehadiran.</p>
        </div>
        <span class="badge {{ $persentaseHadir >= 75 ? 'badge-success' : 'badge-danger' }}" style="font-size: 14px; padding: 6px 14px">
            {{ $persentaseHadir >= 75 ? 'Kehadiran Aman' : 'Di Bawah Syarat 75%' }}
        </span>
    </div>

    <!-- Progress Bar Visual -->
    <div style="position: relative; height: 16px; background: #E2E8F0; border-radius: 20px; overflow: hidden; margin-bottom: 8px">
        <div style="height: 100%; width: {{ min(100, $persentaseHadir) }}%; background: {{ $persentaseHadir >= 75 ? '#10B981' : '#EF4444' }}; border-radius: 20px; transition: width 0.8s ease"></div>
    </div>
    <div style="display: flex; justify-content: space-between; font-size: 12px; color: var(--text-muted); font-weight: 600">
        <span>0%</span>
        <span style="color: #0284C7">&uarr; Batas Minimal 75%</span>
        <span>100%</span>
    </div>
</div>

<!-- Riwayat Aktivitas Presensi Terbaru -->
<div class="card">
    <h3 class="card-title">Riwayat Presensi Terbaru Anda</h3>
    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px">Daftar kehadiran yang baru tercatat di akun Anda.</p>

    <div class="table-responsive">
        <table class="unpam-table">
            <thead>
                <tr>
                    <th>MATA KULIAH</th>
                    <th>PERTEMUAN</th>
                    <th>TANGGAL & WAKTU PRESENSI</th>
                    <th>METODE</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatTerbaru as $rw)
                <tr>
                    <td style="font-weight: 700">{{ $rw->pertemuan->jadwalKuliah->mataKuliah->nama_mk }}</td>
                    <td>Pertemuan Ke - {{ $rw->pertemuan->pertemuan_ke }}</td>
                    <td>{{ $rw->waktu_presensi ? $rw->waktu_presensi->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</td>
                    <td><span style="font-size: 12px; color: var(--text-muted)">{{ $rw->metode ?? 'Sistem' }}</span></td>
                    <td>
                        <span class="badge {{ $rw->status === 'Hadir' ? 'badge-success' : 'badge-danger' }}">{{ $rw->status }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 24px; color: var(--text-muted)">
                        Belum ada riwayat presensi yang selesai.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
