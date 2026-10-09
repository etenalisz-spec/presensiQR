<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalKuliah;
use App\Models\Pertemuan;
use App\Models\Presensi;
use Carbon\Carbon;

class MahasiswaController extends Controller
{
    private function getMahasiswa()
    {
        return Auth::user()->mahasiswa;
    }

    // 1. Dashboard Mahasiswa Riil
    public function dashboard()
    {
        $mahasiswa = $this->getMahasiswa();
        if (!$mahasiswa) abort(403, 'Profil mahasiswa tidak ditemukan.');

        $jadwalIds = JadwalKuliah::where('kelas_id', $mahasiswa->kelas_id)->pluck('id');
        $pertemuanIds = Pertemuan::whereIn('jadwal_kuliah_id', $jadwalIds)->pluck('id');

        $presensis = Presensi::with(['pertemuan.jadwalKuliah.mataKuliah'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('pertemuan_id', $pertemuanIds)
            ->get();

        $totalHadir = $presensis->where('status', 'Hadir')->count();
        $totalAbsen = $presensis->where('status', 'Absen')->count();
        $totalSelesai = $totalHadir + $totalAbsen;
        $persentaseHadir = $totalSelesai > 0 ? round(($totalHadir / $totalSelesai) * 100, 1) : 0;

        // Riwayat presensi aktivitas terbaru
        $riwayatTerbaru = $presensis->where('status', '!=', 'Belum Presensi')
            ->sortByDesc('waktu_presensi')
            ->take(5);

        return view('mahasiswa.dashboard', compact(
            'mahasiswa', 'totalHadir', 'totalAbsen', 'persentaseHadir', 'riwayatTerbaru'
        ));
    }

    // 2. Menu Presensi: Daftar Mata Kuliah (PERSIS SCREENSHOT 1)
    public function presensi()
    {
        $mahasiswa = $this->getMahasiswa();
        if (!$mahasiswa) abort(403, 'Profil mahasiswa tidak ditemukan.');

        $jadwalList = JadwalKuliah::with(['mataKuliah', 'kelas', 'dosen'])
            ->where('kelas_id', $mahasiswa->kelas_id)
            ->get();

        return view('mahasiswa.presensi', compact('mahasiswa', 'jadwalList'));
    }

    // 3. Klik Mata Kuliah: Tampil 14 Pertemuan (PERSIS SCREENSHOT 2)
    public function pertemuan($jadwal_id)
    {
        $mahasiswa = $this->getMahasiswa();
        $jadwal = JadwalKuliah::with(['mataKuliah', 'kelas', 'dosen', 'pertemuans'])
            ->where('kelas_id', $mahasiswa->kelas_id)
            ->where('id', $jadwal_id)
            ->firstOrFail();

        $presensiMap = Presensi::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('pertemuan_id', $jadwal->pertemuans->pluck('id'))
            ->get()
            ->keyBy('pertemuan_id');

        return view('mahasiswa.pertemuan', compact('mahasiswa', 'jadwal', 'presensiMap'));
    }

    // 4. Scanner Halaman Kamera Nyata
    public function scanView($pertemuan_id)
    {
        $mahasiswa = $this->getMahasiswa();
        $pertemuan = Pertemuan::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.kelas'])->findOrFail($pertemuan_id);

        if ($pertemuan->jadwalKuliah->kelas_id !== $mahasiswa->kelas_id) {
            return redirect()->route('mahasiswa.presensi')->withErrors(['error' => 'Anda tidak terdaftar di kelas mata kuliah ini.']);
        }

        $presensi = Presensi::where('pertemuan_id', $pertemuan->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        return view('mahasiswa.scan', compact('mahasiswa', 'pertemuan', 'presensi'));
    }

    // 5. Proses Validasi QR Scan Nyata
    public function prosesScan(Request $request)
    {
        $request->validate([
            'pertemuan_id' => 'required|exists:pertemuans,id',
            'qr_token' => 'required|string',
        ]);

        $mahasiswa = $this->getMahasiswa();
        $pertemuan = Pertemuan::with('jadwalKuliah')->findOrFail($request->pertemuan_id);

        if ($pertemuan->jadwalKuliah->kelas_id !== $mahasiswa->kelas_id) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code ini bukan untuk kelas Anda.',
            ], 400);
        }

        if (!$pertemuan->is_open) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi presensi untuk pertemuan ini belum dibuka oleh dosen.',
            ], 400);
        }

        if ($pertemuan->qr_token !== $request->qr_token) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau bukan untuk pertemuan ini.',
            ], 400);
        }

        if ($pertemuan->qr_expires_at && Carbon::now()->isAfter($pertemuan->qr_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code sudah kedaluwarsa. Silakan minta dosen me-refresh QR Code.',
            ], 400);
        }

        $presensi = Presensi::updateOrCreate(
            [
                'pertemuan_id' => $pertemuan->id,
                'mahasiswa_id' => $mahasiswa->id,
            ],
            [
                'status' => 'Hadir',
                'waktu_presensi' => Carbon::now(),
                'metode' => 'QR Scan',
                'catatan' => 'Presensi berhasil melalui QR Scanner',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil dicatat! Status Anda kini HADIR.',
            'waktu' => $presensi->waktu_presensi->translatedFormat('d F Y, H:i') . ' WIB',
        ]);
    }

    // 6. Menu Profil (Read-Only Data + Modal Ubah Password)
    public function profil()
    {
        $mahasiswa = $this->getMahasiswa()->load(['user', 'kelas']);
        return view('mahasiswa.profil', compact('mahasiswa'));
    }
}
