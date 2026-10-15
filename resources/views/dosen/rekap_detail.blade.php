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
            <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 3px">
                Tanda <strong style="color: #059669; font-size: 14px">✓</strong> = Hadir &bull; 
                Tanda <strong style="color: #DC2626; font-size: 14px">✗</strong> = Absen / Tidak Hadir &bull; 
                Tanda <span style="color: #94A3B8">-</span> = Belum Ada Sesi &bull;
                <span class="no-print" style="color: #0284C7; font-weight: 600"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align: -2px"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg> Klik simbol mana saja untuk edit/koreksi manual</span>
            </p>
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
                    <td style="font-weight: 700; white-space: nowrap; position: sticky; left: 135px; background: #fff; z-index: 2; cursor: pointer" onclick="showMhsDetail({{ $idx }})" title="Klik untuk lihat rincian pertemuan">
                        <span style="color: #1E293B; text-decoration: underline dotted #94A3B8">{{ $rm['mahasiswa']->nama_lengkap }}</span>
                    </td>
                    @foreach($jadwal->pertemuans as $ptm)
                        @php
                            $prs = $rm['presensis'][$ptm->id] ?? null;
                            $st = $prs ? $prs->status : null;
                        @endphp
                        <td class="cell-presensi" style="text-align: center; padding: 6px 2px; cursor: pointer"
                            onclick="openQuickEditPresensi({{ $ptm->id }}, {{ $ptm->pertemuan_ke }}, '{{ $ptm->tanggal_jadwal ? \Carbon\Carbon::parse($ptm->tanggal_jadwal)->format('d/m/Y') : '-' }}', {{ $rm['mahasiswa']->id }}, '{{ addslashes($rm['mahasiswa']->nama_lengkap) }}', '{{ $rm['mahasiswa']->nim }}', '{{ $st ?? 'Belum Presensi' }}', '{{ addslashes($prs->catatan ?? '') }}')"
                            title="Klik untuk koreksi presensi {{ $rm['mahasiswa']->nama_lengkap }} (P{{ $ptm->pertemuan_ke }})">
                            @if($st === 'Hadir')
                                <span style="display: inline-block; width: 22px; height: 22px; line-height: 22px; border-radius: 50%; background: #D1FAE5; color: #059669; font-weight: 900; font-size: 14px">✓</span>
                            @elseif($st === 'Absen')
                                <span style="display: inline-block; width: 22px; height: 22px; line-height: 22px; border-radius: 50%; background: #FEE2E2; color: #DC2626; font-weight: 900; font-size: 14px">✗</span>
                            @else
                                <span style="color: #94A3B8; font-size: 13px">-</span>
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

<!-- Modal Koreksi / Edit Presensi Manual oleh Dosen -->
<div id="quick-edit-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box" style="max-width: 480px">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px">
            <div>
                <h3 style="font-size: 18px; font-weight: 800; color: #1E293B; margin: 0">Koreksi Presensi Mahasiswa</h3>
                <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 3px">Ubah status kehadiran secara manual pada lembar presensi.</p>
            </div>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeQuickEditModal()">&times;</button>
        </div>

        <div style="background: #FAFBFD; border: 1px solid var(--border-line); border-radius: 10px; padding: 12px 14px; margin-bottom: 16px; font-size: 13px">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px">
                <span style="color: var(--text-muted)">Mahasiswa:</span>
                <strong id="qe-nama" style="color: #0284C7"></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px">
                <span style="color: var(--text-muted)">NIM:</span>
                <span id="qe-nim" style="font-family: monospace; font-weight: 700"></span>
            </div>
            <div style="display: flex; justify-content: space-between">
                <span style="color: var(--text-muted)">Pertemuan:</span>
                <strong id="qe-ptm" style="color: #1E293B"></strong>
            </div>
        </div>

        <form action="{{ route('dosen.presensi.quickUpdate') }}" method="POST">
            @csrf
            <input type="hidden" name="pertemuan_id" id="qe-pertemuan-id">
            <input type="hidden" name="mahasiswa_id" id="qe-mahasiswa-id">

            <div class="form-group" style="margin-bottom: 16px">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px; color: #334155">Pilih Status Presensi :</label>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px">
                    <label style="border: 1.5px solid #059669; border-radius: 8px; padding: 10px 4px; text-align: center; cursor: pointer; background: #F0FDF4; display: block">
                        <input type="radio" name="status" value="Hadir" id="qe-status-hadir" style="margin-right: 4px" required>
                        <strong style="color: #059669; font-size: 13px">Hadir (✓)</strong>
                    </label>
                    <label style="border: 1.5px solid #DC2626; border-radius: 8px; padding: 10px 4px; text-align: center; cursor: pointer; background: #FEF2F2; display: block">
                        <input type="radio" name="status" value="Absen" id="qe-status-absen" style="margin-right: 4px">
                        <strong style="color: #DC2626; font-size: 13px">Absen (✗)</strong>
                    </label>
                    <label style="border: 1.5px solid #94A3B8; border-radius: 8px; padding: 10px 4px; text-align: center; cursor: pointer; background: #F8FAFC; display: block">
                        <input type="radio" name="status" value="Belum Presensi" id="qe-status-belum" style="margin-right: 4px">
                        <strong style="color: #64748B; font-size: 13px">Belum (-)</strong>
                    </label>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 18px">
                <label for="qe-catatan" style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #334155">Catatan / Alasan (Opsional) :</label>
                <input type="text" id="qe-catatan" name="catatan" class="form-control" placeholder="Contoh: Izin surat dokter, hadir terlambat, dll">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid var(--border-line); padding-top: 14px">
                <button type="button" class="btn btn-outline" onclick="closeQuickEditModal()">Batal</button>
                <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<style>
.cell-presensi {
    transition: background-color 0.12s ease, transform 0.12s ease;
}
.cell-presensi:hover {
    background-color: #E0F2FE !important;
    transform: scale(1.08);
}
@media print {
    .no-print, header, aside, .unpam-banner, .float-back-btn, .sidebar-backdrop, .modal-backdrop {
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
            const catatan = (prs && prs.catatan) ? prs.catatan : '';
            let badgeClass = 'badge-warning';
            let statusText = 'Belum Presensi';

            if (status === 'Hadir') {
                badgeClass = 'badge-success';
                statusText = 'Hadir (✓)';
            } else if (status === 'Absen') {
                badgeClass = 'badge-danger';
                statusText = 'Absen (✗)';
            }

            const ptmTgl = ptm.tanggal_jadwal || '-';
            const safeNama = item.mahasiswa.nama_lengkap.replace(/'/g, "\\'");
            const safeCatatan = catatan.replace(/'/g, "\\'");

            html += `
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; border: 1px solid var(--border-line); border-radius: 10px; background: #fff; gap: 8px">
                    <div style="flex: 1">
                        <strong style="font-size: 13.5px; color: #1E293B">Pertemuan Ke - ${ptm.pertemuan_ke}</strong>
                        <div style="font-size: 12px; color: var(--text-muted)">${ptmTgl} • ${ptm.jenis_pertemuan} ${ptm.tema ? '• ' + ptm.tema : ''}</div>
                        ${catatan ? `<div style="font-size: 11.5px; color: #64748B; margin-top: 2px"><em>Catatan: ${catatan}</em></div>` : ''}
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px">
                        <span class="badge ${badgeClass}">${statusText}</span>
                        <button type="button" class="btn btn-outline btn-sm" style="padding: 4px 8px; font-size: 11.5px" onclick="openQuickEditPresensi(${ptm.id}, ${ptm.pertemuan_ke}, '${ptmTgl}', ${item.mahasiswa.id}, '${safeNama}', '${item.mahasiswa.nim}', '${status}', '${safeCatatan}')">
                            Edit
                        </button>
                    </div>
                </div>
            `;
        });

        document.getElementById('modal-pertemuan-list').innerHTML = html;
        document.getElementById('mhs-detail-modal').style.display = 'grid';
    }

    function closeMhsDetail() {
        document.getElementById('mhs-detail-modal').style.display = 'none';
    }

    function openQuickEditPresensi(ptmId, ptmKe, ptmTgl, mhsId, mhsNama, mhsNim, currentStatus, catatan) {
        document.getElementById('qe-pertemuan-id').value = ptmId;
        document.getElementById('qe-mahasiswa-id').value = mhsId;
        document.getElementById('qe-nama').textContent = mhsNama;
        document.getElementById('qe-nim').textContent = mhsNim;
        document.getElementById('qe-ptm').textContent = `Pertemuan ${ptmKe} (${ptmTgl})`;
        document.getElementById('qe-catatan').value = catatan || '';

        if (currentStatus === 'Hadir') {
            document.getElementById('qe-status-hadir').checked = true;
        } else if (currentStatus === 'Absen') {
            document.getElementById('qe-status-absen').checked = true;
        } else {
            document.getElementById('qe-status-belum').checked = true;
        }

        document.getElementById('quick-edit-modal').style.display = 'grid';
    }

    function closeQuickEditModal() {
        document.getElementById('quick-edit-modal').style.display = 'none';
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
