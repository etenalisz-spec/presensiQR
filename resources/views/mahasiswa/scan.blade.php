@extends('layouts.app', ['title' => 'Scan QR Presensi'])

@section('content')
<div class="unpam-banner">
    <h2>SCAN QR PRESENSI</h2>
    <p>{{ $pertemuan->jadwalKuliah->mataKuliah->nama_mk }} &bull; Pertemuan Ke - {{ $pertemuan->pertemuan_ke }} ({{ $pertemuan->jenis_pertemuan }})</p>
</div>

<div class="card" style="max-width: 560px; margin: 0 auto; text-align: center; padding: clamp(16px, 4vw, 28px)">
    <div style="display: inline-flex; align-items: center; gap: 8px; background: #EBF5FF; color: var(--unpam-blue); padding: 6px 16px; border-radius: 20px; font-weight: 700; font-size: 13px; margin-bottom: 16px">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="6" height="6" x="3" y="3" rx="1"/><rect width="6" height="6" x="15" y="3" rx="1"/><rect width="6" height="6" x="3" y="15" rx="1"/><path d="M15 15h2v2h-2zM19 15h2M15 19h2M19 19v2"/></svg>
        Pindai QR Code Dosen
    </div>

    <h3 style="font-size: 18px; font-weight: 800; color: #1E293B; margin-bottom: 6px">Arahkan Kamera ke QR Code</h3>
    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">
        Posisikan QR Code yang ditampilkan dosen di dalam kotak bidik pemindai.
    </p>

    <!-- Kamera Viewfinder Responsif -->
    <div style="position: relative; width: 100%; aspect-ratio: 4/3; max-height: 380px; background: #0B132B; border-radius: 20px; overflow: hidden; margin-bottom: 16px; display: grid; place-items: center; box-shadow: 0 10px 30px rgba(0,0,0,0.15)">
        <video id="webcam" playsinline autoplay muted style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover"></video>
        <canvas id="qr-canvas" style="display: none"></canvas>

        <!-- Garis Bidik Pemindai & Animasi Laser -->
        <div id="scanner-frame" style="position: relative; width: min(240px, 70%); aspect-ratio: 1; border: 3px solid rgba(255, 255, 255, 0.7); border-radius: 20px; box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5); pointer-events: none">
            <div id="scan-laser" style="position: absolute; left: 0; right: 0; height: 3px; background: #0088EA; box-shadow: 0 0 14px #0088EA; animation: scanLine 2s infinite ease-in-out"></div>
            
            <!-- Sudut Target Bidik -->
            <span style="position: absolute; top: -3px; left: -3px; width: 20px; height: 20px; border-top: 4px solid #0088EA; border-left: 4px solid #0088EA; border-top-left-radius: 12px"></span>
            <span style="position: absolute; top: -3px; right: -3px; width: 20px; height: 20px; border-top: 4px solid #0088EA; border-right: 4px solid #0088EA; border-top-right-radius: 12px"></span>
            <span style="position: absolute; bottom: -3px; left: -3px; width: 20px; height: 20px; border-bottom: 4px solid #0088EA; border-left: 4px solid #0088EA; border-bottom-left-radius: 12px"></span>
            <span style="position: absolute; bottom: -3px; right: -3px; width: 20px; height: 20px; border-bottom: 4px solid #0088EA; border-right: 4px solid #0088EA; border-bottom-right-radius: 12px"></span>
        </div>

        <div id="cam-status" style="position: absolute; bottom: 12px; background: rgba(0,0,0,0.75); color: #fff; padding: 6px 16px; border-radius: 20px; font-size: 12.5px; font-weight: 600; backdrop-filter: blur(4px)">
            Menghubungkan kamera...
        </div>
    </div>

    <!-- Kontrol Tombol Kamera (Switch / Nyalakan Ulang) -->
    <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-bottom: 16px">
        <button type="button" id="start-cam-btn" class="btn btn-outline btn-sm" onclick="initCamera()" style="font-weight: 600">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            Nyalakan Kamera
        </button>
        <button type="button" id="switch-cam-btn" class="btn btn-outline btn-sm" onclick="toggleCameraFacing()" style="font-weight: 600">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0-4.418-3.582-8-8-8s-8 3.582-8 8c0 2.21 1.79 4 4 4h4"/><polyline points="16 18 20 14 16 10"/></svg>
            Ganti Kamera (Depan / Belakang)
        </button>
    </div>

    <!-- Alert Status Hasil Pemindaian -->
    <div id="result-box" style="display: none; padding: 14px; border-radius: 12px; margin-bottom: 16px"></div>

    <!-- Input Alternatif Token Manual -->
    <div style="padding-top: 16px; border-top: 1px dashed var(--border-line); text-align: left">
        <small style="font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px">
            Alternatif Input Token (Jika Kamera Tidak Tersedia):
        </small>
        <div style="display: flex; gap: 8px">
            <input type="text" id="manual-token" class="form-control" placeholder="Tempel token QR di sini...">
            <button type="button" class="btn btn-primary" onclick="verifyQr(document.getElementById('manual-token').value)" style="font-weight: 700">Kirim</button>
        </div>
    </div>

    <div style="margin-top: 20px">
        <a href="{{ route('mahasiswa.pertemuan', $pertemuan->jadwal_kuliah_id) }}" class="btn btn-outline" style="width: 100%; font-weight: 600">
            &larr; Kembali ke Daftar Pertemuan
        </a>
    </div>
</div>

<style>
@keyframes scanLine {
    0% { top: 5%; opacity: 0.8; }
    50% { top: 92%; opacity: 1; }
    100% { top: 5%; opacity: 0.8; }
}
</style>

@push('scripts')
<script>
    let video = document.getElementById('webcam');
    let canvas = document.getElementById('qr-canvas');
    let ctx = canvas.getContext('2d');
    let statusText = document.getElementById('cam-status');
    let resultBox = document.getElementById('result-box');
    let stream = null;
    let scanning = true;
    let currentFacingMode = 'environment'; // Kamera belakang default

    // Sound Beep Effect via Web Audio API
    function playBeep() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, audioCtx.currentTime); // 880Hz (A5)
            gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.15);
        } catch (e) {
            console.log('Audio feedback not supported:', e);
        }
    }

    // Vibration feedback untuk HP
    function vibrate() {
        if (navigator.vibrate) {
            navigator.vibrate(200);
        }
    }

    async function initCamera() {
        scanning = true;
        statusText.textContent = "Meminta izin akses kamera...";
        try {
            if (stream) {
                stream.getTracks().forEach(t => t.stop());
            }

            const constraints = {
                video: {
                    facingMode: currentFacingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };

            try {
                stream = await navigator.mediaDevices.getUserMedia(constraints);
            } catch (err) {
                // Fallback jika environment camera tidak ada (misal di PC/laptop)
                stream = await navigator.mediaDevices.getUserMedia({ video: true });
            }

            video.srcObject = stream;
            video.setAttribute('playsinline', true);
            await video.play();
            statusText.textContent = "Kamera aktif. Arahkan ke QR Code dosen.";
            requestAnimationFrame(scanLoop);
        } catch (err) {
            console.error('Gagal akses kamera:', err);
            statusText.textContent = "Gagal mengakses kamera. Pastikan izin kamera telah diberikan.";
        }
    }

    function toggleCameraFacing() {
        currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
        initCamera();
    }

    function scanLoop() {
        if (!scanning) return;

        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            let imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);

            if (window.jsQR) {
                let code = jsQR(imgData.data, imgData.width, imgData.height, {
                    inversionAttempts: "dontInvert"
                });

                if (code && code.data && code.data.trim().length > 0) {
                    scanning = false;
                    playBeep();
                    vibrate();
                    verifyQr(code.data);
                    return;
                }
            }
        }
        requestAnimationFrame(scanLoop);
    }

    async function verifyQr(token) {
        if (!token) return;
        statusText.textContent = "Memverifikasi QR Code...";

        try {
            let res = await fetch("{{ route('mahasiswa.scan.proses') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    pertemuan_id: {{ $pertemuan->id }},
                    qr_token: token.trim()
                })
            });

            let data = await res.json();
            resultBox.style.display = "block";

            if (res.ok && data.success) {
                resultBox.className = "alert alert-success";
                resultBox.innerHTML = `<strong>${data.message}</strong><br><small>Tercatat pada: ${data.waktu}</small>`;
                statusText.textContent = "Presensi Berhasil Terverifikasi!";
                if (stream) stream.getTracks().forEach(t => t.stop());
                setTimeout(() => {
                    window.location.href = "{{ route('mahasiswa.pertemuan', $pertemuan->jadwal_kuliah_id) }}";
                }, 1800);
            } else {
                resultBox.className = "alert alert-danger";
                resultBox.textContent = data.message || "QR Code tidak valid atau telah kedaluwarsa.";
                statusText.textContent = "Presensi gagal.";
                setTimeout(() => {
                    scanning = true;
                    requestAnimationFrame(scanLoop);
                }, 2500);
            }
        } catch (err) {
            console.error(err);
            resultBox.style.display = "block";
            resultBox.className = "alert alert-danger";
            resultBox.textContent = "Terjadi kesalahan jaringan saat memproses presensi.";
            setTimeout(() => {
                scanning = true;
                requestAnimationFrame(scanLoop);
            }, 2500);
        }
    }

    // Inisialisasi otomatis kamera saat halaman dimuat
    window.addEventListener('DOMContentLoaded', initCamera);
</script>
@endpush
@endsection
