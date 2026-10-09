@extends('layouts.app', ['title' => 'Presensi Mahasiswa'])

@section('content')
<div class="unpam-banner">
    <h2>PRESENSI PERTEMUAN KE - {{ $pertemuan->pertemuan_ke }}</h2>
    <p>{{ $pertemuan->jadwalKuliah->mataKuliah->nama_mk }} &bull; Kelas: <strong>{{ $pertemuan->jadwalKuliah->kelas->nama_kelas }}</strong></p>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px">
        <div>
            <h3 class="card-title" style="margin: 0">Daftar Kehadiran Mahasiswa</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px">
                Dosen dapat mengubah status presensi secara manual jika mahasiswa terkendala teknis atau izin.
            </p>
        </div>
        <div style="display: flex; gap: 10px">
            @if($pertemuan->is_open)
                <a href="{{ route('dosen.tampilQr', $pertemuan->id) }}" class="btn btn-primary btn-sm">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="6" height="6" x="3" y="3" rx="1"/><rect width="6" height="6" x="15" y="3" rx="1"/><rect width="6" height="6" x="3" y="15" rx="1"/></svg>
                    Buka Layar QR
                </a>
            @endif
            <a href="{{ route('dosen.pertemuan', $pertemuan->jadwal_kuliah_id) }}" class="btn btn-outline btn-sm">
                &larr; Kembali
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="unpam-table">
            <thead>
                <tr>
                    <th style="width: 50px">NO</th>
                    <th>NIM</th>
                    <th>NAMA MAHASISWA</th>
                    <th>STATUS</th>
                    <th>WAKTU PRESENSI</th>
                    <th>METODE</th>
                    <th style="text-align: center; width: 100px">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pertemuan->presensis as $idx => $prs)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td style="font-weight: 700; color: #0284C7">{{ $prs->mahasiswa->nim }}</td>
                    <td style="font-weight: 600">{{ $prs->mahasiswa->nama_lengkap }}</td>
                    <td>
                        @if($prs->status === 'Hadir')
                            <span class="badge badge-success">Hadir</span>
                        @elseif($prs->status === 'Absen')
                            <span class="badge badge-danger">Absen</span>
                        @else
                            <span class="badge badge-warning">Belum Presensi</span>
                        @endif
                    </td>
                    <td>
                        {{ $prs->waktu_presensi ? $prs->waktu_presensi->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
                    </td>
                    <td>
                        <span style="font-size: 12px; color: var(--text-muted)">{{ $prs->metode ?? '-' }}</span>
                    </td>
                    <td style="text-align: center">
                        <button type="button" class="btn btn-outline btn-sm" onclick="openManualModal({{ $prs->id }}, '{{ addslashes($prs->mahasiswa->nama_lengkap) }}', '{{ $prs->status }}', '{{ addslashes($prs->catatan ?? '') }}')">
                            Edit
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit Manual Presensi oleh Dosen -->
<div id="manual-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Koreksi Absensi Mahasiswa</h3>
        <p id="m_mhs_nama" style="font-size: 13.5px; color: var(--text-muted); margin-bottom: 20px"></p>

        <form id="manual-form" method="POST">
            @csrf
            <div class="form-group">
                <label for="m_status">Status Presensi :</label>
                <select id="m_status" name="status" class="form-control" required>
                    <option value="Hadir">Hadir</option>
                    <option value="Absen">Absen / Tidak Hadir</option>
                    <option value="Belum Presensi">Belum Presensi</option>
                </select>
            </div>

            <div class="form-group">
                <label for="m_catatan">Catatan / Alasan Perubahan :</label>
                <textarea id="m_catatan" name="catatan" class="form-control" rows="3" placeholder="Contoh: Sakit dengan surat keterangan dokter, atau kamera error"></textarea>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px">
                <button type="button" class="btn btn-outline" onclick="closeManualModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Status</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openManualModal(id, nama, status, catatan) {
        document.getElementById('manual-form').action = "/dosen/presensi/" + id + "/manual";
        document.getElementById('m_mhs_nama').textContent = "Mahasiswa: " + nama;
        document.getElementById('m_status').value = status;
        document.getElementById('m_catatan').value = catatan || '';
        document.getElementById('manual-modal').style.display = 'grid';
    }

    function closeManualModal() {
        document.getElementById('manual-modal').style.display = 'none';
    }
</script>
@endpush
@endsection
