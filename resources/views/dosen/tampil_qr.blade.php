@extends('layouts.app', ['title' => 'Tampilkan QR Code'])

@section('content')
<div class="unpam-banner">
    <h2>QR CODE PRESENSI AKTIF</h2>
    <p>{{ $pertemuan->jadwalKuliah->mataKuliah->nama_mk }} &bull; Kelas: <strong>{{ $pertemuan->jadwalKuliah->kelas->nama_kelas }}</strong> &bull; Pertemuan Ke - {{ $pertemuan->pertemuan_ke }}</p>
</div>

<div class="card" style="max-width: 620px; margin: 0 auto; text-align: center; padding: 32px">
    @if(!$isExpired)
        <div style="display: inline-flex; align-items: center; gap: 8px; background: #E1F4EA; color: #0C7A52; padding: 6px 16px; border-radius: 20px; font-weight: 700; font-size: 13px; margin-bottom: 20px">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #0C7A52; display: inline-block; animation: pulse 1.5s infinite"></span>
            Sesi Presensi QR Sedang Aktif
        </div>

        <!-- QR Container -->
        <div id="qrcode-container" style="background: #ffffff; padding: 20px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 2px solid var(--border-line); width: min(320px, 90%); aspect-ratio: 1; margin: 0 auto 20px; display: grid; place-items: center">
            <!-- SVG QR code injected by JS -->
        </div>

        <!-- Timer Box -->
        <div style="margin-bottom: 24px">
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px; font-weight: 600">Sisa Waktu Berlaku QR Code:</div>
            <div id="timer-box" style="font-size: 42px; font-weight: 800; color: var(--unpam-blue); font-variant-numeric: tabular-nums">
                --:--
            </div>
            <small style="color: var(--text-muted)">Mahasiswa dapat memindai QR Code di atas melalui menu Presensi</small>
        </div>

        <!-- Tombol Akhiri QR Langsung Sesuai Request User -->
        <div style="margin-bottom: 24px">
            <form action="{{ route('dosen.pertemuan.akhiriQr', $pertemuan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin langsung mengakhiri sesi QR presensi sekarang? Mahasiswa tidak akan dapat melakukan presensi lagi setelah ini.')">
                @csrf
                <button type="submit" class="btn" style="background: #FDEBE9; color: #C2352B; border: 1.5px solid #F8C3BD; font-weight: 800; padding: 12px 24px; border-radius: 12px; width: 100%; max-width: 320px; cursor: pointer; transition: background 0.15s">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="vertical-align: text-bottom; margin-right: 6px"><circle cx="12" cy="12" r="10"/><rect x="9" y="9" width="6" height="6"/></svg>
                    Akhiri Sesi QR Presensi Sekarang
                </button>
            </form>
        </div>
    @else
        <div style="display: inline-flex; align-items: center; gap: 8px; background: #FDEBE9; color: #C2352B; padding: 6px 16px; border-radius: 20px; font-weight: 700; font-size: 13px; margin-bottom: 20px">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #C2352B; display: inline-block"></span>
            Sesi QR Presensi Telah Berakhir
        </div>

        <div style="padding: 30px 20px; background: #F8FAFC; border-radius: 16px; border: 1.5px dashed var(--border-line); margin-bottom: 24px">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--danger)" stroke-width="1.8" style="margin: 0 auto 12px; display: block"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <h4 style="font-size: 16px; font-weight: 800; color: #1E293B">Waktu Akses QR Code Telah Habis</h4>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px">
                Mahasiswa tidak dapat lagi melakukan presensi menggunakan QR code sebelumnya. Anda dapat membuat QR code baru jika diperlukan.
            </p>

            <button type="button" class="btn btn-primary" style="margin-top: 16px; font-weight: 700" onclick="document.getElementById('re-qr-modal').style.display = 'grid'">
                + Buat QR Code Baru (Pilih Durasi)
            </button>
        </div>
    @endif

    <!-- Informasi Detail Sesi -->
    <div style="background: #F8FAFC; border: 1px solid var(--border-line); border-radius: 12px; padding: 16px; margin-bottom: 20px; text-align: left; font-size: 13.5px; line-height: 1.6">
        <div><strong>Mata Kuliah:</strong> {{ $pertemuan->jadwalKuliah->mataKuliah->nama_mk }} ({{ $pertemuan->jadwalKuliah->mataKuliah->kode_mk }})</div>
        <div><strong>Kelas:</strong> {{ $pertemuan->jadwalKuliah->kelas->nama_kelas }} ({{ $pertemuan->jadwalKuliah->kelas->prodi }})</div>
        <div><strong>Pertemuan:</strong> Pertemuan Ke - {{ $pertemuan->pertemuan_ke }} ({{ $pertemuan->jenis_pertemuan }})</div>
        <div><strong>Tema Materi:</strong> {{ $pertemuan->tema }}</div>
        @if($pertemuan->qr_token)
            <div><strong>Token QR:</strong> <code style="background: #E2E8F0; padding: 2px 8px; border-radius: 4px; font-weight: 700">{{ $pertemuan->qr_token }}</code></div>
        @endif
    </div>

    <!-- Live Counter Mahasiswa Hadir -->
    <div style="background: var(--unpam-light-blue); color: var(--unpam-blue); border-radius: 12px; padding: 16px; margin-bottom: 24px">
        <div style="font-size: 13px; font-weight: 600">Kehadiran Mahasiswa Sesi Ini:</div>
        <div style="font-size: 26px; font-weight: 800; margin-top: 4px">
            <span id="live-hadir-count">{{ $hadirCount }}</span> dari {{ $totalMahasiswa }} Mahasiswa Hadir
        </div>
        <small style="color: #0284C7; font-weight: 600">*Data kehadiran diperbarui secara langsung saat mahasiswa memindai QR</small>
    </div>

    <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap">
        <a href="{{ route('dosen.presensi', $pertemuan->id) }}" class="btn btn-outline" style="font-weight: 600">
            Lihat & Edit Rekap Mahasiswa
        </a>
        <a href="{{ route('dosen.pertemuan', $pertemuan->jadwal_kuliah_id) }}" class="btn btn-primary" style="font-weight: 600">
            Kembali ke Daftar Pertemuan
        </a>
    </div>
</div>

<!-- Modal Buat QR Baru (Jika Sudah Kedaluwarsa) -->
<div id="re-qr-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box" style="max-width: 480px">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Buat QR Code Baru</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">
            Pilih durasi waktu aktif QR Code pertemuan ke-{{ $pertemuan->pertemuan_ke }}.
        </p>

        <form action="{{ route('dosen.pertemuan.generateQr', $pertemuan->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label style="font-weight: 700; margin-bottom: 12px; display: block">Durasi Waktu Berlaku (5 - 20 Menit):</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px">
                    <label style="border: 1.5px solid var(--border-line); border-radius: 12px; padding: 12px; cursor: pointer; display: flex; align-items: center; gap: 10px">
                        <input type="radio" name="durasi_menit" value="5">
                        <div>
                            <strong style="display: block; font-size: 14px">5 Menit</strong>
                            <small style="color: var(--text-muted)">Akses Cepat</small>
                        </div>
                    </label>
                    <label style="border: 1.5px solid var(--border-line); border-radius: 12px; padding: 12px; cursor: pointer; display: flex; align-items: center; gap: 10px">
                        <input type="radio" name="durasi_menit" value="10">
                        <div>
                            <strong style="display: block; font-size: 14px">10 Menit</strong>
                            <small style="color: var(--text-muted)">Standar</small>
                        </div>
                    </label>
                    <label style="border: 2px solid var(--unpam-blue); background: #F0F9FF; border-radius: 12px; padding: 12px; cursor: pointer; display: flex; align-items: center; gap: 10px">
                        <input type="radio" name="durasi_menit" value="15" checked>
                        <div>
                            <strong style="display: block; font-size: 14px; color: var(--unpam-blue)">15 Menit</strong>
                            <small style="color: var(--text-muted)">Rekomendasi</small>
                        </div>
                    </label>
                    <label style="border: 1.5px solid var(--border-line); border-radius: 12px; padding: 12px; cursor: pointer; display: flex; align-items: center; gap: 10px">
                        <input type="radio" name="durasi_menit" value="20">
                        <div>
                            <strong style="display: block; font-size: 14px">20 Menit</strong>
                            <small style="color: var(--text-muted)">Maksimal</small>
                        </div>
                    </label>
                </div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('re-qr-modal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary" style="font-weight: 700">Aktifkan QR Code</button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes pulse {
    0% { transform: scale(0.95); opacity: 0.8; }
    50% { transform: scale(1.2); opacity: 1; }
    100% { transform: scale(0.95); opacity: 0.8; }
}
</style>

@push('scripts')
<script>
    @if(!$isExpired && $pertemuan->qr_token)
        // Generate SVG QR Code dari token database
        const qrData = "{{ $pertemuan->qr_token }}";
        try {
            const qr = qrcode(0, 'M');
            qr.addData(qrData);
            qr.make();
            const moduleCount = qr.getModuleCount();
            let svg = `<svg viewBox="0 0 ${moduleCount + 4} ${moduleCount + 4}" style="width: 100%; height: 100%; display: block;" shape-rendering="crispEdges">`;
            svg += `<rect width="100%" height="100%" fill="#ffffff"/>`;
            for (let r = 0; r < moduleCount; r++) {
                for (let c = 0; c < moduleCount; c++) {
                    if (qr.isDark(r, c)) {
                        svg += `<rect x="${c + 2}" y="${r + 2}" width="1" height="1" fill="#0E1A3A"/>`;
                    }
                }
            }
            svg += `</svg>`;
            document.getElementById('qrcode-container').innerHTML = svg;
        } catch (e) {
            console.error(e);
        }

        // Countdown Timer Real-Time
        let remaining = Math.floor(Number({{ (int)$remainingSeconds }}));
        function updateTimer() {
            if (remaining <= 0) {
                document.getElementById('timer-box').textContent = "00:00 (HABIS)";
                document.getElementById('timer-box').style.color = "var(--danger)";
                setTimeout(() => location.reload(), 2000);
                return;
            }
            let m = Math.floor(remaining / 60);
            let s = Math.floor(remaining % 60);
            document.getElementById('timer-box').textContent = 
                String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            remaining--;
        }
        setInterval(updateTimer, 1000);
        updateTimer();
    @endif
</script>
@endpush
@endsection
