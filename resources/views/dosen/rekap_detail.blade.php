@extends('layouts.app', ['title' => 'Rekap Presensi Ruang Kelas'])

@section('content')
<div class="unpam-banner">
    <h2>REKAP & LEMBAR PRESENSI RUANG KELAS {{ $jadwal->kelas->nama_kelas }}</h2>
    <p>{{ $jadwal->mataKuliah->nama_mk }} ({{ $jadwal->mataKuliah->kode_mk }}) &bull; Dosen: <strong>{{ $dosen->nama_lengkap }}</strong> &bull; Ruang: <strong>{{ $jadwal->ruang }}</strong></p>
</div>

<!-- Informasi Header Perkuliahan (Sesuai Permintaan Dosen) -->
<div class="card" style="margin-bottom: 20px; border-left: 5px solid var(--unpam-blue)">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 14px; margin-bottom: 14px">
        <div>
            <h3 class="card-title" style="margin: 0; font-size: 18px">Lembar Presensi Lengkap Kelas</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px">Rincian kehadiran seluruh mahasiswa per pertemuan (1 s/d {{ count($jadwal->pertemuans) }}).</p>
        </div>
        <div style="display: flex; gap: 8px" class="no-print">
            <button onclick="window.print()" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 6px">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Export PDF Presensi
            </button>
            <a href="{{ route('dosen.rekap.kelas', $jadwal->mata_kuliah_id) }}" class="btn btn-outline btn-sm">
                &larr; Kembali
            </a>
        </div>
    </div>

    <!-- Metadata Grid Lengkap: Nama Kelas, Mata Kuliah, Kode MK, Dosen, NIM Dosen, Jam, Ruang -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; background: #FAFBFD; padding: 14px 18px; border-radius: 12px; border: 1px solid var(--border-line); font-size: 13px">
        <div>
            <div style="color: var(--text-muted); font-size: 11.5px; text-transform: uppercase; font-weight: 700">Ruang Kelas</div>
            <strong style="color: #0284C7; font-size: 14px">{{ $jadwal->kelas->nama_kelas }} ({{ $jadwal->kelas->kode_kelas }})</strong>
        </div>
        <div>
            <div style="color: var(--text-muted); font-size: 11.5px; text-transform: uppercase; font-weight: 700">Mata Kuliah</div>
            <strong>{{ $jadwal->mataKuliah->nama_mk }}</strong>
        </div>
        <div>
            <div style="color: var(--text-muted); font-size: 11.5px; text-transform: uppercase; font-weight: 700">Kode Mata Kuliah</div>
            <strong style="color: #0284C7">{{ $jadwal->mataKuliah->kode_mk }}</strong> ({{ $jadwal->mataKuliah->sks }} SKS)
        </div>
        <div>
            <div style="color: var(--text-muted); font-size: 11.5px; text-transform: uppercase; font-weight: 700">Dosen Pengampu</div>
            <strong>{{ $dosen->nama_lengkap }}</strong>
        </div>
        <div>
            <div style="color: var(--text-muted); font-size: 11.5px; text-transform: uppercase; font-weight: 700">NIDN / NIM Dosen</div>
            <strong>{{ $dosen->nidn }}</strong>
        </div>
        <div>
            <div style="color: var(--text-muted); font-size: 11.5px; text-transform: uppercase; font-weight: 700">Jadwal & Jam Perkuliahan</div>
            <strong>{{ $jadwal->hari }}, {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WIB</strong>
        </div>
        <div>
            <div style="color: var(--text-muted); font-size: 11.5px; text-transform: uppercase; font-weight: 700">Ruang Kelas</div>
            <span class="badge badge-warning" style="font-weight: 700">Ruang {{ $jadwal->ruang }}</span>
        </div>
        <div>
            <div style="color: var(--text-muted); font-size: 11.5px; text-transform: uppercase; font-weight: 700">Tahun Ajaran / Semester</div>
            <strong>{{ $jadwal->kelas->tahun_ajaran }} &bull; Smt {{ $jadwal->kelas->semester }}</strong>
        </div>
    </div>
</div>

<!-- 1. TABEL MATRIKS PRESENSI MENYAMPING (Sesuai Permintaan Dosen: Mahasiswa 1 Kelas x Pertemuan 1-14 Menyamping) -->
<div class="card" style="margin-bottom: 24px">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 8px">
        <div>
            <h3 class="card-title" style="margin: 0; font-size: 16px">Matriks Kehadiran Menyamping (Pertemuan 1 s/d {{ count($jadwal->pertemuans) }})</h3>
            <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 3px">Tanda <strong style="color: #059669; font-size: 14px">✓</strong> = Hadir &bull; Tanda <strong style="color: #DC2626; font-size: 14px">✗</strong> = Absen / Tidak Hadir &bull; Tanda <span style="color: #94A3B8">-</span> = Belum Ada Sesi</p>
        </div>
        <div style="display: flex; gap: 12px; font-size: 12px; align-items: center">
            <span style="display: inline-flex; align-items: center; gap: 4px"><strong style="color: #059669; font-size: 15px">✓</strong> = Hadir</span>
            <span style="display: inline-flex; align-items: center; gap: 4px"><strong style="color: #DC2626; font-size: 15px">✗</strong> = Absen</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="unpam-table matriks-table" style="font-size: 12.5px; border-collapse: collapse; width: 100%">
            <thead>
                <tr style="background: #F1F5F9; border-bottom: 2px solid var(--border-line)">
                    <th style="width: 35px; text-align: center; position: sticky; left: 0; background: #F1F5F9; z-index: 3">NO</th>
                    <th style="width: 100px; position: sticky; left: 35px; background: #F1F5F9; z-index: 3">NIM</th>
                    <th style="min-width: 170px; position: sticky; left: 135px; background: #F1F5F9; z-index: 3">NAMA MAHASISWA</th>
                    @foreach($jadwal->pertemuans as $ptm)
                        <th style="text-align: center; min-width: 58px; padding: 6px 4px" title="Pertemuan {{ $ptm->pertemuan_ke }} ({{ $ptm->tanggal_jadwal ? \Carbon\Carbon::parse($ptm->tanggal_jadwal)->format('d/m/Y') : '-' }})">
                            <div style="font-weight: 800; color: #1E293B">P{{ $ptm->pertemuan_ke }}</div>
                            <div style="font-size: 10px; font-weight: normal; color: var(--text-muted); white-space: nowrap">
                                {{ $ptm->tanggal_jadwal ? \Carbon\Carbon::parse($ptm->tanggal_jadwal)->format('d/m') : '-' }}
                            </div>
                        </th>
                    @endforeach
                    <th style="text-align: center; min-width: 50px; background: #E1F4EA; color: #0C7A52">H</th>
                    <th style="text-align: center; min-width: 50px; background: #FDEBE9; color: #C2352B">A</th>
                    <th style="text-align: center; min-width: 65px; background: #EBF5FF; color: #0088EA">%</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapMahasiswa as $idx => $rm)
                <tr style="border-bottom: 1px solid var(--border-line)">
                    <td style="text-align: center; font-weight: 600; position: sticky; left: 0; background: #fff; z-index: 2">{{ $idx + 1 }}</td>
                    <td style="font-weight: 700; color: #0284C7; position: sticky; left: 35px; background: #fff; z-index: 2">{{ $rm['mahasiswa']->nim }}</td>
                    <td style="font-weight: 700; white-space: nowrap; position: sticky; left: 135px; background: #fff; z-index: 2">{{ $rm['mahasiswa']->nama_lengkap }}</td>
                    @foreach($jadwal->pertemuans as $ptm)
                        @php
                            $prs = $rm['presensis'][$ptm.id] ?? null;
                            $st = $prs ? $prs->status : null;
                        @endphp
                        <td style="text-align: center; padding: 6px 2px">
                            @if($st === 'Hadir')
                                <span style="display: inline-block; width: 22px; height: 22px; line-height: 22px; border-radius: 50%; background: #D1FAE5; color: #059669; font-weight: 900; font-size: 14px" title="Pertemuan {{ $ptm->pertemuan_ke }}: Hadir">✓</span>
                            @elseif($st === 'Absen')
                                <span style="display: inline-block; width: 22px; height: 22px; line-height: 22px; border-radius: 50%; background: #FEE2E2; color: #DC2626; font-weight: 900; font-size: 14px" title="Pertemuan {{ $ptm->pertemuan_ke }}: Absen">✗</span>
                            @else
                                <span style="color: #94A3B8; font-size: 13px" title="Pertemuan {{ $ptm->pertemuan_ke }}: Belum Ada Sesi / Belum Presensi">-</span>
                            @endif
                        </td>
                    @endforeach
                    <td style="text-align: center; font-weight: 700; color: #059669; background: #F0FDF4">{{ $rm['hadir'] }}</td>
                    <td style="text-align: center; font-weight: 700; color: #DC2626; background: #FEF2F2">{{ $rm['absen'] }}</td>
                    <td style="text-align: center; font-weight: 800; color: {{ $rm['persentase'] >= 75 ? '#059669' : '#DC2626' }}; background: #F8FAFC">
                        {{ $rm['persentase'] }}%
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ count($jadwal->pertemuans) + 6 }}" style="text-align: center; padding: 24px; color: var(--text-muted)">
                        Belum ada mahasiswa yang terdaftar di ruang kelas ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- 2. Ringkasan & Chart Keseluruhan Kelas -->
<div class="card" style="margin-bottom: 24px">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px">
        <div>
            <h3 class="card-title" style="margin: 0">Grafik Kehadiran Keseluruhan Kelas</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px">Total presensi seluruh sesi perkuliahan kelas {{ $jadwal->kelas->nama_kelas }}.</p>
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

<!-- Modal Rincian Pertemuan 1 - 14 Tiap Mahasiswa -->
<div id="mhs-detail-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box" style="max-width: 650px; max-height: calc(100dvh - 32px); overflow-y: auto">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px">
            <div>
                <h3 id="modal-mhs-nama" style="font-size: 18px; font-weight: 800; color: #1E293B"></h3>
                <p id="modal-mhs-nim" style="font-size: 13px; color: var(--text-muted)"></p>
            </div>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeMhsDetail()">Tutup</button>
        </div>

        <div style="background: #F8FAFC; border: 1px solid var(--border-line); border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center">
            <span style="font-weight: 700; font-size: 13.5px">Rincian Pertemuan 1 s/d {{ count($jadwal->pertemuans) }}:</span>
            <span id="modal-mhs-pct" class="badge"></span>
        </div>

        <div id="modal-pertemuan-list" style="display: flex; flex-direction: column; gap: 8px">
            <!-- Injected by JS -->
        </div>
    </div>
</div>

@push('scripts')
<style>
@media print {
    .no-print, header, aside, .unpam-banner, .float-back-btn, .sidebar-backdrop {
        display: none !important;
    }
    main.main-content {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
    }
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
        padding: 12px !important;
        page-break-inside: avoid;
    }
    table.matriks-table {
        font-size: 10px !important;
    }
    table.matriks-table th, table.matriks-table td {
        border: 1px solid #333 !important;
        padding: 4px !important;
    }
}
</style>
<script>
    // Data JSON rekap mahasiswa untuk modal
    const rekapData = @json($rekapMahasiswa);
    const pertemuans = @json($jadwal->pertemuans);

    function showMhsDetail(index) {
        const item = rekapData[index];
        document.getElementById('modal-mhs-nama').textContent = item.mahasiswa.nama_lengkap;
        document.getElementById('modal-mhs-nim').textContent = "NIM: " + item.mahasiswa.nim + " • Ruang Kelas: {{ $jadwal->kelas->nama_kelas }}";
        
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
                statusText = 'Hadir (✓)';
            } else if (status === 'Absen') {
                badgeClass = 'badge-danger';
                statusText = 'Absen (✗)';
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
