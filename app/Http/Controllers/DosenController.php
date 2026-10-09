<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\JadwalKuliah;
use App\Models\Pertemuan;
use App\Models\Presensi;
use App\Models\MataKuliah;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use Carbon\Carbon;

class DosenController extends Controller
{
    private function getDosen()
    {
        return Auth::user()->dosen;
    }

    // 1. Dashboard Dosen Riil
    public function dashboard()
    {
        $dosen = $this->getDosen();
        if (!$dosen) abort(403, 'Profil dosen tidak ditemukan.');

        $jadwals = JadwalKuliah::with(['mataKuliah', 'kelas.mahasiswas'])
            ->where('dosen_id', $dosen->id)
            ->get();

        $kelasIds = $jadwals->pluck('kelas_id')->unique();
        $totalMahasiswa = Mahasiswa::whereIn('kelas_id', $kelasIds)->count();
        $totalKelas = $kelasIds->count();
        $totalMK = $jadwals->pluck('mata_kuliah_id')->unique()->count();

        $jadwalIds = $jadwals->pluck('id');
        $pertemuanIds = Pertemuan::whereIn('jadwal_kuliah_id', $jadwalIds)->pluck('id');

        $hadirHariIni = Presensi::whereIn('pertemuan_id', $pertemuanIds)
            ->where('status', 'Hadir')
            ->whereDate('waktu_presensi', Carbon::today())
            ->count();

        $totalHadir = Presensi::whereIn('pertemuan_id', $pertemuanIds)->where('status', 'Hadir')->count();
        $totalAbsen = Presensi::whereIn('pertemuan_id', $pertemuanIds)->where('status', 'Absen')->count();
        $totalSelesai = $totalHadir + $totalAbsen;
        $pctHadir = $totalSelesai > 0 ? round(($totalHadir / $totalSelesai) * 100, 1) : 0;

        // Aktivitas terbaru
        $aktivitasTerbaru = Presensi::with(['mahasiswa', 'pertemuan.jadwalKuliah.mataKuliah', 'pertemuan.jadwalKuliah.kelas'])
            ->whereIn('pertemuan_id', $pertemuanIds)
            ->where('status', 'Hadir')
            ->orderBy('waktu_presensi', 'desc')
            ->take(6)
            ->get();

        return view('dosen.dashboard', compact(
            'dosen', 'totalMahasiswa', 'totalKelas', 'totalMK', 'hadirHariIni',
            'pctHadir', 'totalHadir', 'totalAbsen', 'aktivitasTerbaru'
        ));
    }

    // 2. Mata Kuliah yang Diampu
    public function mataKuliah()
    {
        $dosen = $this->getDosen();
        if (!$dosen) abort(403, 'Profil dosen tidak ditemukan.');

        $jadwals = JadwalKuliah::with(['mataKuliah', 'kelas'])
            ->where('dosen_id', $dosen->id)
            ->get();

        $matkulList = $jadwals->groupBy('mata_kuliah_id')->map(function ($group) {
            return [
                'mata_kuliah' => $group->first()->mataKuliah,
                'jadwals' => $group,
                'total_kelas' => $group->count(),
            ];
        });

        return view('dosen.matakuliah', compact('dosen', 'matkulList'));
    }

    // 3. Kelas per Mata Kuliah
    public function kelas($mata_kuliah_id)
    {
        $dosen = $this->getDosen();
        $mataKuliah = MataKuliah::findOrFail($mata_kuliah_id);

        $jadwalList = JadwalKuliah::with('kelas')
            ->where('dosen_id', $dosen->id)
            ->where('mata_kuliah_id', $mata_kuliah_id)
            ->get();

        return view('dosen.kelas', compact('dosen', 'mataKuliah', 'jadwalList'));
    }

    // 4. Pertemuan 1 s/d 14
    public function pertemuan($jadwal_id)
    {
        $dosen = $this->getDosen();
        $jadwal = JadwalKuliah::with(['mataKuliah', 'kelas', 'pertemuans.presensis'])
            ->where('dosen_id', $dosen->id)
            ->where('id', $jadwal_id)
            ->firstOrFail();

        return view('dosen.pertemuan', compact('dosen', 'jadwal'));
    }

    public function updatePertemuan(Request $request, $pertemuan_id)
    {
        $request->validate([
            'tema' => 'required|string',
            'jenis_pertemuan' => 'required|in:Tatap Muka,E-learning',
            'deskripsi' => 'nullable|string',
            'tanggal_jadwal' => 'nullable|date',
        ]);

        $pertemuan = Pertemuan::findOrFail($pertemuan_id);
        $pertemuan->update([
            'tema' => $request->tema,
            'jenis_pertemuan' => $request->jenis_pertemuan,
            'deskripsi' => $request->deskripsi,
            'tanggal_jadwal' => $request->tanggal_jadwal ?: $pertemuan->tanggal_jadwal,
            'is_open' => true,
        ]);

        return back()->with('success', "Pertemuan ke-{$pertemuan->pertemuan_ke} berhasil disimpan dan sesi presensi telah dibuka.");
    }

    public function generateQr(Request $request, $pertemuan_id)
    {
        $pertemuan = Pertemuan::findOrFail($pertemuan_id);
        if (!$pertemuan->is_open) {
            return back()->withErrors(['error' => 'Harap isi tema dan buka pertemuan terlebih dahulu sebelum membuat QR Code.']);
        }

        $durasi = (int) $request->input('durasi_menit', 15);
        if ($durasi < 5) $durasi = 5;
        if ($durasi > 20) $durasi = 20;

        $token = 'UNPAM-' . Str::upper(Str::random(12)) . '-P' . $pertemuan->pertemuan_ke;
        $expiresAt = Carbon::now()->addMinutes($durasi);

        $pertemuan->update([
            'qr_token' => $token,
            'qr_expires_at' => $expiresAt,
        ]);

        return redirect()->route('dosen.tampilQr', $pertemuan->id)->with('success', "QR Code berhasil dibuat dengan durasi aktif {$durasi} menit.");
    }

    public function akhiriQr($pertemuan_id)
    {
        $pertemuan = Pertemuan::findOrFail($pertemuan_id);
        $pertemuan->update([
            'qr_expires_at' => Carbon::now()->subSeconds(5),
        ]);

        return redirect()->route('dosen.pertemuan', $pertemuan->jadwal_kuliah_id)
            ->with('success', "Sesi QR Presensi Pertemuan ke-{$pertemuan->pertemuan_ke} telah berhasil diakhiri.");
    }

    public function tampilQr($pertemuan_id)
    {
        $pertemuan = Pertemuan::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.kelas', 'presensis.mahasiswa'])->findOrFail($pertemuan_id);

        $isExpired = !$pertemuan->qr_token || Carbon::now()->isAfter($pertemuan->qr_expires_at);
        $remainingSeconds = 0;
        if (!$isExpired && $pertemuan->qr_expires_at) {
            $remainingSeconds = (int) max(0, Carbon::now()->diffInSeconds($pertemuan->qr_expires_at, false));
        }

        $hadirCount = $pertemuan->presensis->where('status', 'Hadir')->count();
        $totalMahasiswa = $pertemuan->presensis->count();

        return view('dosen.tampil_qr', compact('pertemuan', 'hadirCount', 'totalMahasiswa', 'isExpired', 'remainingSeconds'));
    }

    public function presensi($pertemuan_id)
    {
        $pertemuan = Pertemuan::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.kelas', 'presensis.mahasiswa'])->findOrFail($pertemuan_id);
        return view('dosen.presensi', compact('pertemuan'));
    }

    public function updatePresensiManual(Request $request, $presensi_id)
    {
        $request->validate([
            'status' => 'required|in:Hadir,Absen,Belum Presensi',
            'catatan' => 'nullable|string',
        ]);

        $presensi = Presensi::findOrFail($presensi_id);
        $presensi->update([
            'status' => $request->status,
            'waktu_presensi' => $request->status === 'Hadir' ? Carbon::now() : null,
            'metode' => 'Manual Dosen',
            'catatan' => $request->catatan ?: 'Diverifikasi manual oleh dosen',
        ]);

        return back()->with('success', "Status presensi {$presensi->mahasiswa->nama_lengkap} berhasil diperbarui.");
    }

    // --- 5. MENU DATA MAHASISWA (BERJENJANG) ---
    public function dataMahasiswaMatkul()
    {
        $dosen = $this->getDosen();
        $jadwals = JadwalKuliah::with(['mataKuliah', 'kelas'])
            ->where('dosen_id', $dosen->id)
            ->get();

        $matkulList = $jadwals->groupBy('mata_kuliah_id')->map(function ($group) {
            return [
                'mata_kuliah' => $group->first()->mataKuliah,
                'jadwals' => $group,
            ];
        });

        return view('dosen.mhs_matakuliah', compact('dosen', 'matkulList'));
    }

    public function dataMahasiswaKelas($mata_kuliah_id)
    {
        $dosen = $this->getDosen();
        $mataKuliah = MataKuliah::findOrFail($mata_kuliah_id);
        $jadwalList = JadwalKuliah::with(['kelas.mahasiswas'])
            ->where('dosen_id', $dosen->id)
            ->where('mata_kuliah_id', $mata_kuliah_id)
            ->get();

        return view('dosen.mhs_kelas', compact('dosen', 'mataKuliah', 'jadwalList'));
    }

    public function dataMahasiswaList($jadwal_id)
    {
        $dosen = $this->getDosen();
        $jadwal = JadwalKuliah::with(['mataKuliah', 'kelas.mahasiswas.user'])
            ->where('dosen_id', $dosen->id)
            ->where('id', $jadwal_id)
            ->firstOrFail();

        $mahasiswaList = $jadwal->kelas->mahasiswas;
        return view('dosen.mhs_list', compact('dosen', 'jadwal', 'mahasiswaList'));
    }

    // --- 6. MENU REKAP PRESENSI (BERJENJANG + DETAIL PER MAHASISWA) ---
    public function rekapMatkul()
    {
        $dosen = $this->getDosen();
        $jadwals = JadwalKuliah::with(['mataKuliah', 'kelas'])
            ->where('dosen_id', $dosen->id)
            ->get();

        $matkulList = $jadwals->groupBy('mata_kuliah_id')->map(function ($group) {
            return [
                'mata_kuliah' => $group->first()->mataKuliah,
                'jadwals' => $group,
            ];
        });

        return view('dosen.rekap_matakuliah', compact('dosen', 'matkulList'));
    }

    public function rekapKelas($mata_kuliah_id)
    {
        $dosen = $this->getDosen();
        $mataKuliah = MataKuliah::findOrFail($mata_kuliah_id);
        $jadwalList = JadwalKuliah::with('kelas')
            ->where('dosen_id', $dosen->id)
            ->where('mata_kuliah_id', $mata_kuliah_id)
            ->get();

        return view('dosen.rekap_kelas', compact('dosen', 'mataKuliah', 'jadwalList'));
    }

    public function rekapDetailKelas($jadwal_id)
    {
        $dosen = $this->getDosen();
        $jadwal = JadwalKuliah::with(['mataKuliah', 'kelas.mahasiswas', 'pertemuans.presensis'])
            ->where('dosen_id', $dosen->id)
            ->where('id', $jadwal_id)
            ->firstOrFail();

        $pertemuanIds = $jadwal->pertemuans->pluck('id');
        $totalHadirKelas = Presensi::whereIn('pertemuan_id', $pertemuanIds)->where('status', 'Hadir')->count();
        $totalAbsenKelas = Presensi::whereIn('pertemuan_id', $pertemuanIds)->where('status', 'Absen')->count();
        $totalSelesaiKelas = $totalHadirKelas + $totalAbsenKelas;
        $pctKelas = $totalSelesaiKelas > 0 ? round(($totalHadirKelas / $totalSelesaiKelas) * 100, 1) : 0;

        // Hitung persentase kehadiran tiap mahasiswa
        $rekapMahasiswa = [];
        foreach ($jadwal->kelas->mahasiswas as $mhs) {
            $prsMhs = Presensi::whereIn('pertemuan_id', $pertemuanIds)->where('mahasiswa_id', $mhs->id)->get();
            $h = $prsMhs->where('status', 'Hadir')->count();
            $a = $prsMhs->where('status', 'Absen')->count();
            $tot = $h + $a;
            $pct = $tot > 0 ? round(($h / $tot) * 100, 1) : 0;

            $rekapMahasiswa[] = [
                'mahasiswa' => $mhs,
                'hadir' => $h,
                'absen' => $a,
                'persentase' => $pct,
                'presensis' => $prsMhs->keyBy('pertemuan_id'),
            ];
        }

        return view('dosen.rekap_detail', compact('dosen', 'jadwal', 'totalHadirKelas', 'totalAbsenKelas', 'pctKelas', 'rekapMahasiswa'));
    }

    // --- 7. PROFIL DOSEN ---
    public function profil()
    {
        $dosen = $this->getDosen()->load('user');
        return view('dosen.profil', compact('dosen'));
    }
}
