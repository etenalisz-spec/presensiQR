@extends('layouts.app', ['title' => count($jadwal->pertemuans) . ' Pertemuan Kelas'])

@section('content')
<div class="unpam-banner">
    <h2>{{ count($jadwal->pertemuans) }} PERTEMUAN KELAS</h2>
    <p>{{ $jadwal->mataKuliah->nama_mk }} &bull; Kelas: <strong>{{ $jadwal->kelas->nama_kelas }}</strong> &bull; Ruang: <strong>{{ $jadwal->ruang }}</strong></p>
</div>

<div class="card" style="margin-bottom: 20px">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px">
        <div>
            <h3 class="card-title" style="margin: 0">Pengelolaan Pertemuan</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px">
                Syarat generate QR Code: Klik <strong>Kelola / Edit</strong> pada pertemuan, isi tema, jenis, dan deskripsi lalu simpan.
            </p>
        </div>
        <a href="{{ route('dosen.kelas', $jadwal->mata_kuliah_id) }}" class="btn btn-outline btn-sm">
            &larr; Kembali ke Kelas
        </a>
    </div>
</div>

<div style="display: flex; flex-direction: column; gap: 16px">
    @foreach($jadwal->pertemuans as $ptm)
        @php
            $hadirCount = $ptm->presensis->where('status', 'Hadir')->count();
            $totalCount = $ptm->presensis->count();
            $isQrActive = $ptm->qr_token && $ptm->qr_expires_at && \Carbon\Carbon::now()->isBefore($ptm->qr_expires_at);
        @endphp
        <div class="card" style="margin: 0; padding: 20px; border-left: 5px solid {{ $ptm->is_open ? 'var(--unpam-blue)' : '#CBD5E1' }}">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 14px; margin-bottom: 14px">
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px; flex-wrap: wrap">
                        <h4 style="font-size: 16px; font-weight: 800; color: #1E293B">
                            Pertemuan Ke - {{ $ptm->pertemuan_ke }}
                        </h4>
                        <span class="badge {{ $ptm->jenis_pertemuan === 'E-learning' ? 'badge-warning' : 'badge-info' }}">
                            {{ $ptm->jenis_pertemuan }}
                        </span>
                        @if($ptm->is_open)
                            <span class="badge badge-success">Sesi Terbuka</span>
                        @else
                            <span class="badge badge-danger">Sesi Terkunci</span>
                        @endif

                        @if($isQrActive)
                            <span class="badge" style="background: #E0F2FE; color: #0369A1; font-weight: 700">QR Aktif</span>
                        @endif
                    </div>

                    <div style="font-size: 13px; color: var(--text-muted); line-height: 1.6">
                        <div><strong>Tanggal:</strong> {{ $ptm->tanggal_jadwal ? \Carbon\Carbon::parse($ptm->tanggal_jadwal)->translatedFormat('d F Y') : '-' }} &bull; {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WIB</div>
                        <div><strong>Tema Materi:</strong> {{ $ptm->tema ?? 'Belum ditentukan (Harap isi via tombol Kelola)' }}</div>
                        @if($ptm->deskripsi)
                            <div><strong>Deskripsi:</strong> {{ $ptm->deskripsi }}</div>
                        @endif
                    </div>
                </div>

                <div style="text-align: right">
                    <span style="font-size: 13px; font-weight: 700; color: #1E293B">Kehadiran:</span>
                    <div style="font-size: 16px; font-weight: 800; color: var(--unpam-blue)">
                        {{ $hadirCount }} / {{ $totalCount }} Mahasiswa
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi Pertemuan -->
            <div style="display: flex; gap: 10px; flex-wrap: wrap; padding-top: 14px; border-top: 1px solid var(--border-line)">
                <!-- 1. Tombol CRUD Kelola Pertemuan -->
                <button type="button" class="btn btn-outline btn-sm" onclick="openCrudModal({{ $ptm->id }}, '{{ addslashes($ptm->tema ?? '') }}', '{{ $ptm->jenis_pertemuan }}', '{{ addslashes($ptm->deskripsi ?? '') }}', '{{ $ptm->tanggal_jadwal }}')">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                    Kelola / Edit Materi
                </button>

                <!-- 2. Tombol Generate QR Code dengan Pilihan Durasi -->
                @if($ptm->is_open)
                    <button type="button" class="btn btn-primary btn-sm" onclick="openQrDurationModal({{ $ptm->id }}, {{ $ptm->pertemuan_ke }})">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="6" height="6" x="3" y="3" rx="1"/><rect width="6" height="6" x="15" y="3" rx="1"/><rect width="6" height="6" x="3" y="15" rx="1"/><path d="M15 15h2v2h-2zM19 15h2M15 19h2M19 19v2"/></svg>
                        {{ $isQrActive ? 'Lihat / Buat Ulang QR' : 'Generate QR Code' }}
                    </button>
                    @if($isQrActive)
                        <a href="{{ route('dosen.tampilQr', $ptm->id) }}" class="btn btn-sm" style="background: #EBF5FF; color: #0284C7; font-weight: 700; border: 1px solid #BAE6FD">
                            Buka Layar QR Aktif &rsaquo;
                        </a>
                    @endif
                @else
                    <button type="button" class="btn btn-outline btn-sm" disabled title="Buka dan simpan materi pertemuan terlebih dahulu" style="opacity: 0.6; cursor: not-allowed">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="6" height="6" x="3" y="3" rx="1"/><rect width="6" height="6" x="15" y="3" rx="1"/><rect width="6" height="6" x="3" y="15" rx="1"/></svg>
                        Generate QR (Terkunci)
                    </button>
                @endif

                <!-- 3. Tombol Rekap & Edit Manual Presensi -->
                <a href="{{ route('dosen.presensi', $ptm->id) }}" class="btn btn-outline btn-sm">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
                    Lihat & Edit Absensi
                </a>
            </div>
        </div>
    @endforeach
</div>

<!-- Modal CRUD Pertemuan -->
<div id="crud-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Kelola Sesi Pertemuan</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">
            Lengkapi data di bawah ini agar pertemuan terbuka dan mahasiswa dapat melakukan presensi.
        </p>

        <form id="crud-form" method="POST">
            @csrf
            <div class="form-group">
                <label for="m_tema">Tema :</label>
                <input type="text" id="m_tema" name="tema" class="form-control" placeholder="Contoh: Pengantar Query Relasional dan SQL" required>
            </div>

            <div class="form-group">
                <label for="m_jenis">Jenis Pertemuan :</label>
                <select id="m_jenis" name="jenis_pertemuan" class="form-control" required>
                    <option value="Tatap Muka">Tatap Muka (Offline)</option>
                    <option value="E-learning">E-learning (Online)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="m_tanggal">Tanggal Pertemuan :</label>
                <input type="date" id="m_tanggal" name="tanggal_jadwal" class="form-control">
            </div>

            <div class="form-group">
                <label for="m_deskripsi">Deskripsi :</label>
                <textarea id="m_deskripsi" name="deskripsi" class="form-control" rows="3" placeholder="Masukkan deskripsi capaian pembelajaran sesi ini..."></textarea>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('crud-modal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan & Buka Sesi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pilihan Durasi QR Code (5 - 20 Menit Sesuai Request User) -->
<div id="qr-duration-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box" style="max-width: 480px">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Generate QR Code Presensi</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">
            Tentukan durasi waktu aktif QR Code pertemuan ke-<span id="qr_ptm_label" style="font-weight: 700; color: #0284C7"></span>.
        </p>

        <form id="qr-duration-form" method="POST">
            @csrf
            <div class="form-group">
                <label style="font-weight: 700; margin-bottom: 12px; display: block">Pilih Durasi Waktu Berlaku (5 - 20 Menit):</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px">
                    <label style="border: 1.5px solid var(--border-line); border-radius: 12px; padding: 12px; cursor: pointer; display: flex; align-items: center; gap: 10px" onclick="selectDurationBox(this)">
                        <input type="radio" name="durasi_menit" value="5">
                        <div>
                            <strong style="display: block; font-size: 14px">5 Menit</strong>
                            <small style="color: var(--text-muted)">Akses Cepat</small>
                        </div>
                    </label>
                    <label style="border: 1.5px solid var(--border-line); border-radius: 12px; padding: 12px; cursor: pointer; display: flex; align-items: center; gap: 10px" onclick="selectDurationBox(this)">
                        <input type="radio" name="durasi_menit" value="10">
                        <div>
                            <strong style="display: block; font-size: 14px">10 Menit</strong>
                            <small style="color: var(--text-muted)">Standar</small>
                        </div>
                    </label>
                    <label style="border: 2px solid var(--unpam-blue); background: #F0F9FF; border-radius: 12px; padding: 12px; cursor: pointer; display: flex; align-items: center; gap: 10px" onclick="selectDurationBox(this)">
                        <input type="radio" name="durasi_menit" value="15" checked>
                        <div>
                            <strong style="display: block; font-size: 14px; color: var(--unpam-blue)">15 Menit</strong>
                            <small style="color: var(--text-muted)">Rekomendasi</small>
                        </div>
                    </label>
                    <label style="border: 1.5px solid var(--border-line); border-radius: 12px; padding: 12px; cursor: pointer; display: flex; align-items: center; gap: 10px" onclick="selectDurationBox(this)">
                        <input type="radio" name="durasi_menit" value="20">
                        <div>
                            <strong style="display: block; font-size: 14px">20 Menit</strong>
                            <small style="color: var(--text-muted)">Maksimal</small>
                        </div>
                    </label>
                </div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('qr-duration-modal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary" style="font-weight: 700">Buat & Buka QR Code</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openCrudModal(id, tema, jenis, deskripsi, tanggal) {
        document.getElementById('crud-form').action = "/dosen/pertemuan/" + id + "/update";
        document.getElementById('m_tema').value = tema;
        document.getElementById('m_jenis').value = jenis || 'Tatap Muka';
        document.getElementById('m_deskripsi').value = deskripsi;
        document.getElementById('m_tanggal').value = tanggal ? tanggal.split('T')[0] : '';
        document.getElementById('crud-modal').style.display = 'grid';
    }

    function openQrDurationModal(ptmId, ptmKe) {
        document.getElementById('qr-duration-form').action = "/dosen/pertemuan/" + ptmId + "/generate-qr";
        document.getElementById('qr_ptm_label').textContent = ptmKe;
        document.getElementById('qr-duration-modal').style.display = 'grid';
    }

    function selectDurationBox(elem) {
        document.querySelectorAll('#qr-duration-modal label').forEach(l => {
            l.style.border = '1.5px solid var(--border-line)';
            l.style.background = '#fff';
        });
        elem.style.border = '2px solid var(--unpam-blue)';
        elem.style.background = '#F0F9FF';
        const radio = elem.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    }
</script>
@endpush
@endsection
