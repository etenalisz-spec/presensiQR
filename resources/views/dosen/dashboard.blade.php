@extends('layouts.app', ['title' => 'Dashboard Dosen'])

@section('content')
<div class="unpam-banner">
    <h2>DASHBOARD DOSEN PENGAMPU</h2>
    <p>Selamat datang, <strong>{{ $dosen->nama_lengkap }}</strong> &bull; NIDN: {{ $dosen->nidn }} &bull; Universitas Pamulang</p>
</div>

<!-- 4 Kartu Metrik Riil Dosen -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px">
    <div class="card" style="margin: 0; padding: 20px; display: flex; align-items: center; gap: 16px">
        <div style="width: 50px; height: 50px; border-radius: 12px; background: var(--unpam-light-blue); color: var(--unpam-blue); display: grid; place-items: center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/></svg>
        </div>
        <div>
            <div style="font-size: 26px; font-weight: 800; color: #1E293B">{{ $totalMahasiswa }}</div>
            <div style="font-size: 13px; color: var(--text-muted); font-weight: 600">Mahasiswa Diajar</div>
        </div>
    </div>

    <div class="card" style="margin: 0; padding: 20px; display: flex; align-items: center; gap: 16px">
        <div style="width: 50px; height: 50px; border-radius: 12px; background: #FFF2D9; color: #95600A; display: grid; place-items: center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V8l7-4 7 4v13M9 21v-6h6v6M9 11h.01M15 11h.01"/></svg>
        </div>
        <div>
            <div style="font-size: 26px; font-weight: 800; color: #1E293B">{{ $totalKelas }}</div>
            <div style="font-size: 13px; color: var(--text-muted); font-weight: 600">Kelas Diampu</div>
        </div>
    </div>

    <div class="card" style="margin: 0; padding: 20px; display: flex; align-items: center; gap: 16px">
        <div style="width: 50px; height: 50px; border-radius: 12px; background: #E1F4EA; color: #0C7A52; display: grid; place-items: center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5z"/><path d="M4 19.5V21h16v-4"/></svg>
        </div>
        <div>
            <div style="font-size: 26px; font-weight: 800; color: #1E293B">{{ $totalMK }}</div>
            <div style="font-size: 13px; color: var(--text-muted); font-weight: 600">Mata Kuliah</div>
        </div>
    </div>

    <div class="card" style="margin: 0; padding: 20px; display: flex; align-items: center; gap: 16px">
        <div style="width: 50px; height: 50px; border-radius: 12px; background: #FDEBE9; color: #C2352B; display: grid; place-items: center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div>
            <div style="font-size: 26px; font-weight: 800; color: #1E293B">{{ $hadirHariIni }}</div>
            <div style="font-size: 13px; color: var(--text-muted); font-weight: 600">Hadir Hari Ini</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 24px">
    <!-- Chart Persentase Kehadiran Kelas yang Diampu -->
    <div class="card" style="margin: 0">
        <h3 class="card-title">Persentase Kehadiran Keseluruhan</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px">Akumulasi seluruh kelas dan mata kuliah yang Anda ampu.</p>
        <div style="display: flex; align-items: center; justify-content: center; flex-wrap: wrap; gap: 24px">
            <div style="width: 150px; height: 150px; position: relative; flex-shrink: 0">
                <canvas id="dosenChart"></canvas>
            </div>
            <div>
                <div style="font-size: 32px; font-weight: 800; color: var(--unpam-blue)">{{ $pctHadir }}%</div>
                <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px">Rata-rata Mahasiswa Hadir</div>
                <div style="display: flex; gap: 8px">
                    <span class="badge badge-success">{{ $totalHadir }} Hadir</span>
                    <span class="badge badge-danger">{{ $totalAbsen }} Absen</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Jalan Pintas Cepat -->
    <div class="card" style="margin: 0; display: flex; flex-direction: column; justify-content: space-between">
        <div>
            <h3 class="card-title">Aksi Cepat Presensi</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px">Akses langsung sesi perkuliahan hari ini.</p>
            <div style="display: flex; flex-direction: column; gap: 10px">
                <a href="{{ route('dosen.matakuliah') }}" class="btn btn-primary" style="justify-content: flex-start">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="6" height="6" x="3" y="3" rx="1"/><rect width="6" height="6" x="15" y="3" rx="1"/><rect width="6" height="6" x="3" y="15" rx="1"/><path d="M15 15h2v2h-2zM19 15h2M15 19h2M19 19v2"/></svg>
                    Buka Sesi & Generate QR Code
                </a>
                <a href="{{ route('dosen.mhs.matakuliah') }}" class="btn btn-outline" style="justify-content: flex-start">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    Lihat Data Mahasiswa Kelas
                </a>
                <a href="{{ route('dosen.rekap.matakuliah') }}" class="btn btn-outline" style="justify-content: flex-start">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Lihat Rekap Presensi Kelas
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Aktivitas Presensi Terbaru -->
<div class="card">
    <h3 class="card-title">Aktivitas Presensi Terbaru</h3>
    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px">Daftar mahasiswa yang baru saja tercatat hadir di mata kuliah Anda.</p>

    <div class="table-responsive">
        <table class="unpam-table">
            <thead>
                <tr>
                    <th>MAHASISWA</th>
                    <th>NIM</th>
                    <th>MATA KULIAH</th>
                    <th>KELAS</th>
                    <th>PERTEMUAN</th>
                    <th>WAKTU PRESENSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($aktivitasTerbaru as $akt)
                <tr>
                    <td style="font-weight: 700">{{ $akt->mahasiswa->nama_lengkap }}</td>
                    <td style="color: #0284C7; font-weight: 600">{{ $akt->mahasiswa->nim }}</td>
                    <td>{{ $akt->pertemuan->jadwalKuliah->mataKuliah->nama_mk }}</td>
                    <td><span class="badge badge-info">{{ $akt->pertemuan->jadwalKuliah->kelas->nama_kelas }}</span></td>
                    <td>Ke - {{ $akt->pertemuan->pertemuan_ke }}</td>
                    <td>{{ $akt->waktu_presensi ? $akt->waktu_presensi->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 24px; color: var(--text-muted)">
                        Belum ada aktivitas presensi terbaru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    const ctx = document.getElementById('dosenChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Absen'],
            datasets: [{
                data: [{{ $totalHadir }}, {{ $totalAbsen }}],
                backgroundColor: ['#10B981', '#EF4444'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endpush
@endsection
