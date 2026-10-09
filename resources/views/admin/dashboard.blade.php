@extends('layouts.app', ['title' => 'Dashboard Admin'])

@section('content')
<div class="unpam-banner">
    <h2>DASHBOARD ADMIN PROGRAM STUDI</h2>
    <p>Monitoring Presensi & Statistik Perkuliahan Universitas Pamulang</p>
</div>

<!-- 4 Statistik Kartu Ringkasan -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px">
    <div class="card" style="margin: 0; padding: 20px; display: flex; align-items: center; gap: 16px">
        <div style="width: 50px; height: 50px; border-radius: 12px; background: var(--unpam-light-blue); color: var(--unpam-blue); display: grid; place-items: center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/></svg>
        </div>
        <div>
            <div style="font-size: 26px; font-weight: 800; color: #1E293B">{{ $totalMahasiswa }}</div>
            <div style="font-size: 13px; color: var(--text-muted); font-weight: 600">Total Mahasiswa</div>
        </div>
    </div>

    <div class="card" style="margin: 0; padding: 20px; display: flex; align-items: center; gap: 16px">
        <div style="width: 50px; height: 50px; border-radius: 12px; background: #E1F4EA; color: #0C7A52; display: grid; place-items: center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div>
            <div style="font-size: 26px; font-weight: 800; color: #1E293B">{{ $totalDosen }}</div>
            <div style="font-size: 13px; color: var(--text-muted); font-weight: 600">Total Dosen</div>
        </div>
    </div>

    <div class="card" style="margin: 0; padding: 20px; display: flex; align-items: center; gap: 16px">
        <div style="width: 50px; height: 50px; border-radius: 12px; background: #FFF2D9; color: #95600A; display: grid; place-items: center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V8l7-4 7 4v13M9 21v-6h6v6M9 11h.01M15 11h.01"/></svg>
        </div>
        <div>
            <div style="font-size: 26px; font-weight: 800; color: #1E293B">{{ $totalKelas }}</div>
            <div style="font-size: 13px; color: var(--text-muted); font-weight: 600">Total Kelas</div>
        </div>
    </div>

    <div class="card" style="margin: 0; padding: 20px; display: flex; align-items: center; gap: 16px">
        <div style="width: 50px; height: 50px; border-radius: 12px; background: #FDEBE9; color: #C2352B; display: grid; place-items: center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5z"/><path d="M4 19.5V21h16v-4"/></svg>
        </div>
        <div>
            <div style="font-size: 26px; font-weight: 800; color: #1E293B">{{ $totalMataKuliah }}</div>
            <div style="font-size: 13px; color: var(--text-muted); font-weight: 600">Mata Kuliah</div>
        </div>
    </div>
</div>

<!-- Pemantauan Grafik Kehadiran Kelas (Persis Requirement User) -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 20px">
        <div>
            <h3 class="card-title" style="margin: 0">Grafik & Statistik Presensi Kelas</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px">Pantau perbandingan kehadiran mahasiswa berdasarkan kelas.</p>
        </div>

        <form method="GET" style="display: flex; gap: 10px; align-items: center">
            <label for="kelas_id" style="font-size: 13px; font-weight: 600">Pilih Kelas:</label>
            <select name="kelas_id" id="kelas_id" class="form-control" style="width: auto; min-width: 150px" onchange="this.form.submit()">
                @foreach($kelasList as $k)
                    <option value="{{ $k->id }}" {{ $selectedKelas && $selectedKelas->id == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }} ({{ $k->prodi }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if($selectedKelas && !empty($chartData))
    <div class="chart-layout-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; align-items: center">
        <!-- Canvas Chart.js -->
        <div style="height: 240px; position: relative">
            <canvas id="presensiChart"></canvas>
        </div>

        <!-- Rangkuman Angka -->
        <div style="display: flex; flex-direction: column; gap: 12px">
            <div style="background: #F8FAFC; padding: 14px 18px; border-radius: 12px; border: 1px solid var(--border-line)">
                <div style="font-size: 13px; color: var(--text-muted)">Persentase Kehadiran Kelas {{ $selectedKelas->nama_kelas }}</div>
                <div style="font-size: 28px; font-weight: 800; color: var(--unpam-blue)">{{ $chartData['persentase'] }}%</div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px">
                <div style="background: #E1F4EA; padding: 12px; border-radius: 10px; text-align: center">
                    <span style="font-size: 11px; font-weight: 700; color: #0C7A52; display: block">HADIR</span>
                    <strong style="font-size: 20px; color: #0C7A52">{{ $chartData['hadir'] }}</strong>
                </div>
                <div style="background: #FDEBE9; padding: 12px; border-radius: 10px; text-align: center">
                    <span style="font-size: 11px; font-weight: 700; color: #C2352B; display: block">ABSEN</span>
                    <strong style="font-size: 20px; color: #C2352B">{{ $chartData['absen'] }}</strong>
                </div>
                <div style="background: #FFF2D9; padding: 12px; border-radius: 10px; text-align: center">
                    <span style="font-size: 11px; font-weight: 700; color: #95600A; display: block">BELUM</span>
                    <strong style="font-size: 20px; color: #95600A">{{ $chartData['belum'] }}</strong>
                </div>
            </div>
        </div>
    </div>
    @else
    <p style="text-align: center; padding: 30px; color: var(--text-muted)">Belum ada data aktivitas presensi pada kelas ini.</p>
    @endif
</div>

@push('scripts')
<script>
    @if($selectedKelas && !empty($chartData))
    const ctx = document.getElementById('presensiChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Absen', 'Belum Presensi'],
            datasets: [{
                data: [{{ $chartData['hadir'] }}, {{ $chartData['absen'] }}, {{ $chartData['belum'] }}],
                backgroundColor: ['#10B981', '#EF4444', '#F59E0B'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
    @endif
</script>
@endpush
@endsection
