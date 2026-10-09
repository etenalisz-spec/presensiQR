@extends('layouts.app', ['title' => 'Rekap Presensi Kelas'])

@section('content')
<div class="unpam-banner">
    <h2>REKAP PRESENSI KELAS {{ $jadwal->kelas->nama_kelas }}</h2>
    <p>{{ $jadwal->mataKuliah->nama_mk }} &bull; Dosen: <strong>{{ $dosen->nama_lengkap }}</strong></p>
</div>

<!-- 1. Ringkasan & Chart Keseluruhan Kelas (Persis Permintaan User) -->
<div class="card" style="margin-bottom: 24px">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px">
        <div>
            <h3 class="card-title" style="margin: 0">Grafik Kehadiran Keseluruhan Kelas</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px">Total presensi seluruh sesi perkuliahan kelas {{ $jadwal->kelas->nama_kelas }}.</p>
        </div>
        <div style="display: flex; gap: 8px">
            <button onclick="window.print()" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 6px">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Export PDF
            </button>
            <a href="{{ route('dosen.rekap.kelas', $jadwal->mata_kuliah_id) }}" class="btn btn-outline btn-sm">
                &larr; Kembali
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr); gap: 24px; align-items: center">
        <div style="max-height: 220px; position: relative">
            <canvas id="rekapKelasChart"></canvas>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px">
            <div style="background: #F8FAFC; padding: 14px 18px; border-radius: 12px; border: 1px solid var(--border-line)">
                <div style="font-size: 13px; color: var(--text-muted)">Persentase Kehadiran Kelas</div>
                <div style="font-size: 32px; font-weight: 800; color: var(--unpam-blue)">{{ $pctKelas }}%</div>
            </div>
            <div style="display: flex; gap: 12px">
                <div style="flex: 1; background: #E1F4EA; padding: 12px; border-radius: 10px; text-align: center">
                    <span style="font-size: 12px; font-weight: 700; color: #0C7A52">TOTAL HADIR</span>
                    <strong style="font-size: 20px; color: #0C7A52; display: block">{{ $totalHadirKelas }}</strong>
                </div>
                <div style="flex: 1; background: #FDEBE9; padding: 12px; border-radius: 10px; text-align: center">
                    <span style="font-size: 12px; font-weight: 700; color: #C2352B">TOTAL ABSEN</span>
                    <strong style="font-size: 20px; color: #C2352B; display: block">{{ $totalAbsenKelas }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. Tabel Persentase Tiap Mahasiswa + Tombol Detail (Persis Permintaan User) -->
<div class="card">
    <h3 class="card-title">Persentase Kehadiran Tiap Mahasiswa</h3>
    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">Klik tombol <strong>Detail</strong> untuk melihat daftar pertemuan 1 s/d 14 yang sudah dan belum diabsen.</p>

    <div class="table-responsive">
        <table class="unpam-table">
            <thead>
                <tr>
                    <th style="width: 50px">NO</th>
                    <th>NIM</th>
                    <th>NAMA MAHASISWA</th>
                    <th>HADIR</th>
                    <th>ABSEN</th>
                    <th>PERSENTASE</th>
                    <th style="text-align: center; width: 100px">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekapMahasiswa as $idx => $rm)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td style="font-weight: 700; color: #0284C7">{{ $rm['mahasiswa']->nim }}</td>
                    <td style="font-weight: 700">{{ $rm['mahasiswa']->nama_lengkap }}</td>
                    <td><span class="badge badge-success">{{ $rm['hadir'] }} Sesi</span></td>
                    <td><span class="badge badge-danger">{{ $rm['absen'] }} Sesi</span></td>
                    <td>
                        <span class="badge {{ $rm['persentase'] >= 75 ? 'badge-success' : 'badge-danger' }}" style="font-size: 13px">
                            {{ $rm['persentase'] }}%
                        </span>
                    </td>
                    <td style="text-align: center">
                        <button type="button" class="btn btn-outline btn-sm" onclick="showMhsDetail({{ $idx }})">
                            Detail
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Rincian Pertemuan 1 - 14 Tiap Mahasiswa -->
<div id="mhs-detail-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box" style="max-width: 650px; max-height: 85vh; overflow-y: auto">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px">
            <div>
                <h3 id="modal-mhs-nama" style="font-size: 18px; font-weight: 800; color: #1E293B"></h3>
                <p id="modal-mhs-nim" style="font-size: 13px; color: var(--text-muted)"></p>
            </div>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeMhsDetail()">Tutup</button>
        </div>

        <div style="background: #F8FAFC; border: 1px solid var(--border-line); border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center">
            <span style="font-weight: 700; font-size: 13.5px">Rincian Pertemuan 1 s/d 14:</span>
            <span id="modal-mhs-pct" class="badge"></span>
        </div>

        <div id="modal-pertemuan-list" style="display: flex; flex-direction: column; gap: 8px">
            <!-- Injected by JS -->
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Data JSON rekap mahasiswa untuk modal
    const rekapData = @json($rekapMahasiswa);
    const pertemuans = @json($jadwal->pertemuans);

    function showMhsDetail(index) {
        const item = rekapData[index];
        document.getElementById('modal-mhs-nama').textContent = item.mahasiswa.nama_lengkap;
        document.getElementById('modal-mhs-nim').textContent = "NIM: " + item.mahasiswa.nim + " • Kelas: {{ $jadwal->kelas->nama_kelas }}";
        
        const pctBadge = document.getElementById('modal-mhs-pct');
        pctBadge.textContent = "Kehadiran: " + item.persentase + "%";
        pctBadge.className = "badge " + (item.persentase >= 75 ? 'badge-success' : 'badge-danger');

        let html = '';
        pertemuans.forEach(ptm => {
            const prs = item.presensis[ptm.id];
            const status = prs ? prs.status : 'Belum Presensi';
            let badgeClass = 'badge-warning';
            let statusText = 'Belum Presensi';

            if (status === 'Hadir') {
                badgeClass = 'badge-success';
                statusText = 'Hadir';
            } else if (status === 'Absen') {
                badgeClass = 'badge-danger';
                statusText = 'Absen / Tidak Hadir';
            }

            html += `
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; border: 1px solid var(--border-line); border-radius: 10px; background: #fff">
                    <div>
                        <strong style="font-size: 13.5px; color: #1E293B">Pertemuan Ke - ${ptm.pertemuan_ke}</strong>
                        <div style="font-size: 12px; color: var(--text-muted)">${ptm.tanggal_jadwal || '-'} • ${ptm.jenis_pertemuan} ${ptm.tema ? '• ' + ptm.tema : ''}</div>
                    </div>
                    <span class="badge ${badgeClass}">${statusText}</span>
                </div>
            `;
        });

        document.getElementById('modal-pertemuan-list').innerHTML = html;
        document.getElementById('mhs-detail-modal').style.display = 'grid';
    }

    function closeMhsDetail() {
        document.getElementById('mhs-detail-modal').style.display = 'none';
    }

    // Chart Keseluruhan Kelas
    const ctx = document.getElementById('rekapKelasChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Total Hadir', 'Total Absen'],
            datasets: [{
                data: [{{ $totalHadirKelas }}, {{ $totalAbsenKelas }}],
                backgroundColor: ['#10B981', '#EF4444'],
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
</script>
@endpush
@endsection
