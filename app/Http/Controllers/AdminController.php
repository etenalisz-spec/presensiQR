<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\JadwalKuliah;
use App\Models\Pertemuan;
use App\Models\Presensi;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $totalMahasiswa = Mahasiswa::count();
        $totalDosen = Dosen::count();
        $totalKelas = Kelas::count();
        $totalMataKuliah = MataKuliah::count();

        $kelasList = Kelas::all();
        $selectedKelasId = $request->get('kelas_id', $kelasList->first()?->id);
        $selectedKelas = Kelas::find($selectedKelasId);

        $chartData = [];
        if ($selectedKelas) {
            $jadwalIds = JadwalKuliah::where('kelas_id', $selectedKelas->id)->pluck('id');
            $pertemuanIds = Pertemuan::whereIn('jadwal_kuliah_id', $jadwalIds)->pluck('id');
            
            $hadirCount = Presensi::whereIn('pertemuan_id', $pertemuanIds)->where('status', 'Hadir')->count();
            $absenCount = Presensi::whereIn('pertemuan_id', $pertemuanIds)->where('status', 'Absen')->count();
            $belumCount = Presensi::whereIn('pertemuan_id', $pertemuanIds)->where('status', 'Belum Presensi')->count();

            $totalSesi = $hadirCount + $absenCount;
            $persentaseHadir = $totalSesi > 0 ? round(($hadirCount / $totalSesi) * 100, 1) : 0;

            $chartData = [
                'hadir' => $hadirCount,
                'absen' => $absenCount,
                'belum' => $belumCount,
                'persentase' => $persentaseHadir,
            ];
        }

        return view('admin.dashboard', compact(
            'totalMahasiswa', 'totalDosen', 'totalKelas', 'totalMataKuliah',
            'kelasList', 'selectedKelas', 'chartData'
        ));
    }

    // --- KELOLA KELAS (CRUD LENGKAP) ---
    public function kelas()
    {
        $kelasList = Kelas::withCount('mahasiswas')->get();
        return view('admin.kelas', compact('kelasList'));
    }

    public function storeKelas(Request $request)
    {
        $request->validate([
            'kode_kelas' => 'required|unique:kelas,kode_kelas',
            'nama_kelas' => 'required',
            'prodi' => 'required',
            'semester' => 'required|integer',
        ]);

        Kelas::create($request->all());
        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function updateKelas(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $request->validate([
            'kode_kelas' => 'required|unique:kelas,kode_kelas,' . $id,
            'nama_kelas' => 'required',
            'prodi' => 'required',
            'semester' => 'required|integer',
            'tahun_ajaran' => 'required',
        ]);

        $kelas->update($request->all());
        return back()->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function deleteKelas($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();
        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    // --- KELOLA MAHASISWA & PENGELOMPOKAN KELAS (CRUD LENGKAP) ---
    public function mahasiswa(Request $request)
    {
        $kelasList = Kelas::all();
        $query = Mahasiswa::with(['user', 'kelas']);
        if ($request->has('kelas_id') && $request->kelas_id != '') {
            $query->where('kelas_id', $request->kelas_id);
        }
        $mahasiswaList = $query->paginate(15);
        return view('admin.mahasiswa', compact('mahasiswaList', 'kelasList'));
    }

    public function storeMahasiswa(Request $request)
    {
        $request->validate([
            'nim' => 'required|unique:mahasiswas,nim|unique:users,username',
            'nama_lengkap' => 'required',
            'kelas_id' => 'required|exists:kelas,id',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->nama_lengkap,
            'username' => $request->nim,
            'email' => $request->email ?: strtolower($request->nim).'@mhs.unpam.ac.id',
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa',
        ]);

        $mhs = Mahasiswa::create([
            'user_id' => $user->id,
            'kelas_id' => $request->kelas_id,
            'nim' => $request->nim,
            'nama_lengkap' => $request->nama_lengkap,
            'no_telp' => $request->filled('no_telp') ? $request->no_telp : ('08' . rand(1111111111, 9999999999)),
            'status' => 'Aktif',
        ]);

        $jadwals = JadwalKuliah::where('kelas_id', $request->kelas_id)->get();
        foreach ($jadwals as $jadwal) {
            foreach ($jadwal->pertemuans as $ptm) {
                Presensi::firstOrCreate([
                    'pertemuan_id' => $ptm->id,
                    'mahasiswa_id' => $mhs->id,
                ], [
                    'status' => 'Belum Presensi',
                ]);
            }
        }

        return back()->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function updateMahasiswa(Request $request, $id)
    {
        $mhs = Mahasiswa::findOrFail($id);
        $request->validate([
            'nama_lengkap' => 'required',
            'kelas_id' => 'required|exists:kelas,id',
            'status' => 'required|in:Aktif,Cuti,Nonaktif',
            'email' => 'nullable|email',
        ]);

        $mhs->update([
            'nama_lengkap' => $request->nama_lengkap,
            'kelas_id' => $request->kelas_id,
            'no_telp' => $request->no_telp,
            'status' => $request->status,
        ]);

        $userUpdate = ['name' => $request->nama_lengkap];
        if ($request->filled('email')) {
            $userUpdate['email'] = $request->email;
        }
        $mhs->user->update($userUpdate);

        return back()->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function deleteMahasiswa($id)
    {
        $mhs = Mahasiswa::findOrFail($id);
        $user = $mhs->user;
        $mhs->delete();
        $user->delete();
        return back()->with('success', 'Mahasiswa berhasil dihapus.');
    }

    // --- KELOLA DOSEN (CRUD LENGKAP) ---
    public function dosen()
    {
        $dosenList = Dosen::with(['user', 'jadwalKuliahs.mataKuliah', 'jadwalKuliahs.kelas'])->get();
        $mataKuliahList = MataKuliah::all();
        $kelasList = Kelas::all();
        return view('admin.dosen', compact('dosenList', 'mataKuliahList', 'kelasList'));
    }

    public function storeDosen(Request $request)
    {
        $request->validate([
            'nidn' => 'required|unique:dosens,nidn|unique:users,username',
            'nama_lengkap' => 'required',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->nama_lengkap,
            'username' => $request->nidn,
            'email' => $request->email ?: strtolower(explode(' ', $request->nama_lengkap)[0]).'@unpam.ac.id',
            'password' => Hash::make($request->password),
            'role' => 'dosen',
        ]);

        $dosen = Dosen::create([
            'user_id' => $user->id,
            'nidn' => $request->nidn,
            'nama_lengkap' => $request->nama_lengkap,
            'gelar' => $request->gelar,
            'no_telp' => $request->no_telp,
        ]);

        // Hubungkan mata kuliah yang diampu dosen secara otomatis
        if ($request->has('mata_kuliah_ids') && is_array($request->mata_kuliah_ids)) {
            $kelasId = $request->kelas_id ?: Kelas::first()?->id;
            if ($kelasId) {
                foreach ($request->mata_kuliah_ids as $mkId) {
                    $mk = MataKuliah::find($mkId);
                    if (!$mk) continue;

                    $jadwal = JadwalKuliah::firstOrCreate([
                        'mata_kuliah_id' => $mkId,
                        'kelas_id' => $kelasId,
                    ], [
                        'dosen_id' => $dosen->id,
                        'hari' => 'Senin',
                        'jam_mulai' => '07:40:00',
                        'jam_selesai' => '09:20:00',
                        'ruang' => 'V.401',
                    ]);

                    if ($jadwal->dosen_id != $dosen->id) {
                        $jadwal->update(['dosen_id' => $dosen->id]);
                    }

                    if ($jadwal->pertemuans()->count() == 0) {
                        $totalPertemuan = $mk->sks == 3 ? 21 : 14;
                        $startDate = Carbon::create(2026, 8, 31);
                        for ($p = 1; $p <= $totalPertemuan; $p++) {
                            $tgl = (clone $startDate)->addWeeks($p - 1);
                            $ptm = Pertemuan::create([
                                'jadwal_kuliah_id' => $jadwal->id,
                                'pertemuan_ke' => $p,
                                'tanggal_jadwal' => $tgl->format('Y-m-d'),
                                'jenis_pertemuan' => in_array($p, [3, 7, 10, 15, 18]) ? 'E-learning' : 'Tatap Muka',
                                'is_open' => false,
                            ]);

                            $mahasiswas = Mahasiswa::where('kelas_id', $kelasId)->get();
                            foreach ($mahasiswas as $mhs) {
                                Presensi::firstOrCreate([
                                    'pertemuan_id' => $ptm->id,
                                    'mahasiswa_id' => $mhs->id,
                                ], [
                                    'status' => 'Belum Presensi',
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return back()->with('success', 'Akun dosen berhasil dibuat dan mata kuliah pengampu telah ditautkan.');
    }

    public function updateDosen(Request $request, $id)
    {
        $dosen = Dosen::findOrFail($id);
        $request->validate([
            'nama_lengkap' => 'required',
            'nidn' => 'required|unique:dosens,nidn,' . $id,
        ]);

        $dosen->update([
            'nama_lengkap' => $request->nama_lengkap,
            'nidn' => $request->nidn,
            'gelar' => $request->gelar,
            'no_telp' => $request->no_telp,
        ]);
        $dosen->user->update([
            'name' => $request->nama_lengkap,
            'username' => $request->nidn,
            'email' => $request->email ?: $dosen->user->email,
        ]);

        // Perbarui plotting mata kuliah jika dipilih
        if ($request->has('mata_kuliah_ids') && is_array($request->mata_kuliah_ids)) {
            $kelasId = $request->kelas_id ?: ($dosen->jadwalKuliahs->first()?->kelas_id ?: Kelas::first()?->id);
            if ($kelasId) {
                foreach ($request->mata_kuliah_ids as $mkId) {
                    $mk = MataKuliah::find($mkId);
                    if (!$mk) continue;

                    $jadwal = JadwalKuliah::firstOrCreate([
                        'mata_kuliah_id' => $mkId,
                        'kelas_id' => $kelasId,
                    ], [
                        'dosen_id' => $dosen->id,
                        'hari' => 'Senin',
                        'jam_mulai' => '07:40:00',
                        'jam_selesai' => '09:20:00',
                        'ruang' => 'V.401',
                    ]);

                    if ($jadwal->dosen_id != $dosen->id) {
                        $jadwal->update(['dosen_id' => $dosen->id]);
                    }

                    if ($jadwal->pertemuans()->count() == 0) {
                        $totalPertemuan = $mk->sks == 3 ? 21 : 14;
                        $startDate = Carbon::create(2026, 8, 31);
                        for ($p = 1; $p <= $totalPertemuan; $p++) {
                            $tgl = (clone $startDate)->addWeeks($p - 1);
                            $ptm = Pertemuan::create([
                                'jadwal_kuliah_id' => $jadwal->id,
                                'pertemuan_ke' => $p,
                                'tanggal_jadwal' => $tgl->format('Y-m-d'),
                                'jenis_pertemuan' => in_array($p, [3, 7, 10, 15, 18]) ? 'E-learning' : 'Tatap Muka',
                                'is_open' => false,
                            ]);

                            $mahasiswas = Mahasiswa::where('kelas_id', $kelasId)->get();
                            foreach ($mahasiswas as $mhs) {
                                Presensi::firstOrCreate([
                                    'pertemuan_id' => $ptm->id,
                                    'mahasiswa_id' => $mhs->id,
                                ], [
                                    'status' => 'Belum Presensi',
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return back()->with('success', 'Data dosen & penugasan mata kuliah berhasil diperbarui.');
    }

    public function deleteDosen($id)
    {
        $dosen = Dosen::findOrFail($id);
        $user = $dosen->user;
        $dosen->delete();
        $user->delete();
        return back()->with('success', 'Data dosen berhasil dihapus.');
    }

    // --- FITUR TERPADU: MATA KULIAH & JADWAL BERJENJANG ---
    public function matakuliahJadwal(Request $request)
    {
        $mkId = $request->query('mk_id');
        $dosenId = $request->query('dosen_id');

        $mataKuliahList = MataKuliah::withCount('jadwalKuliahs')
            ->with(['jadwalKuliahs.dosen'])
            ->get();
        $dosenList = Dosen::all();
        $kelasList = Kelas::withCount('mahasiswas')->get();

        $selectedMk = null;
        $dosensPengampu = collect();
        $selectedDosen = null;
        $detailJadwals = collect();

        if ($mkId) {
            $selectedMk = MataKuliah::findOrFail($mkId);

            // Dosen-dosen yang mengampu mata kuliah ini
            $dosensPengampu = Dosen::whereHas('jadwalKuliahs', function($q) use ($mkId) {
                $q->where('mata_kuliah_id', $mkId);
            })->with(['jadwalKuliahs' => function($q) use ($mkId) {
                $q->where('mata_kuliah_id', $mkId)->with('kelas');
            }])->get();

            if ($dosenId) {
                $selectedDosen = Dosen::findOrFail($dosenId);
                $detailJadwals = JadwalKuliah::with(['mataKuliah', 'kelas.mahasiswas', 'dosen', 'pertemuans'])
                    ->where('mata_kuliah_id', $mkId)
                    ->where('dosen_id', $dosenId)
                    ->get();
            }
        }

        return view('admin.matakuliah_jadwal', compact(
            'mataKuliahList',
            'dosenList',
            'kelasList',
            'selectedMk',
            'dosensPengampu',
            'selectedDosen',
            'detailJadwals'
        ));
    }

    public function mataKuliah()
    {
        return redirect()->route('admin.matakuliah_jadwal');
    }

    public function storeMataKuliah(Request $request)
    {
        $request->validate([
            'kode_mk' => 'required|unique:mata_kuliahs,kode_mk',
            'nama_mk' => 'required',
            'sks' => 'required|in:2,3',
        ]);

        $mk = MataKuliah::create([
            'kode_mk' => $request->kode_mk,
            'nama_mk' => $request->nama_mk,
            'sks' => (int)$request->sks,
            'semester' => $request->semester ?: 5,
        ]);

        $totalPertemuan = $mk->sks == 3 ? 21 : 14;

        // Jika langsung ditugaskan ke dosen dan kelas
        if ($request->filled('dosen_id') && $request->filled('kelas_id')) {
            $jadwal = JadwalKuliah::create([
                'mata_kuliah_id' => $mk->id,
                'kelas_id' => $request->kelas_id,
                'dosen_id' => $request->dosen_id,
                'hari' => 'Senin',
                'jam_mulai' => '07:40:00',
                'jam_selesai' => '09:20:00',
                'ruang' => 'V.401',
            ]);

            $startDate = Carbon::create(2026, 8, 31);
            for ($p = 1; $p <= $totalPertemuan; $p++) {
                $tgl = (clone $startDate)->addWeeks($p - 1);
                $ptm = Pertemuan::create([
                    'jadwal_kuliah_id' => $jadwal->id,
                    'pertemuan_ke' => $p,
                    'tanggal_jadwal' => $tgl->format('Y-m-d'),
                    'jenis_pertemuan' => in_array($p, [3, 7, 10, 15, 18]) ? 'E-learning' : 'Tatap Muka',
                    'is_open' => false,
                ]);

                $mahasiswas = Mahasiswa::where('kelas_id', $request->kelas_id)->get();
                foreach ($mahasiswas as $mhs) {
                    Presensi::firstOrCreate([
                        'pertemuan_id' => $ptm->id,
                        'mahasiswa_id' => $mhs->id,
                    ], [
                        'status' => 'Belum Presensi',
                    ]);
                }
            }
        }

        return back()->with('success', "Mata kuliah berhasil ditambahkan dengan bobot {$mk->sks} SKS ({$totalPertemuan} Pertemuan).");
    }

    public function updateMataKuliah(Request $request, $id)
    {
        $mk = MataKuliah::findOrFail($id);
        $request->validate([
            'kode_mk' => 'required|unique:mata_kuliahs,kode_mk,' . $id,
            'nama_mk' => 'required',
            'sks' => 'required|in:2,3',
            'semester' => 'required|integer',
        ]);

        $mk->update([
            'kode_mk' => $request->kode_mk,
            'nama_mk' => $request->nama_mk,
            'sks' => (int)$request->sks,
            'semester' => $request->semester,
        ]);

        if ($request->filled('dosen_id') && $request->filled('kelas_id')) {
            $jadwal = JadwalKuliah::firstOrCreate([
                'mata_kuliah_id' => $mk->id,
                'kelas_id' => $request->kelas_id,
            ], [
                'dosen_id' => $request->dosen_id,
                'hari' => 'Senin',
                'jam_mulai' => '07:40:00',
                'jam_selesai' => '09:20:00',
                'ruang' => 'V.401',
            ]);
            $jadwal->update(['dosen_id' => $request->dosen_id]);
        }

        return back()->with('success', 'Data mata kuliah berhasil diperbarui.');
    }

    public function deleteMataKuliah($id)
    {
        $mk = MataKuliah::findOrFail($id);
        $mk->delete();
        return back()->with('success', 'Mata kuliah berhasil dihapus.');
    }

    // --- KELOLA JADWAL KULIAH & PLOTTING (CRUD LENGKAP) ---
    public function jadwal()
    {
        return redirect()->route('admin.matakuliah_jadwal');
    }

    public function storeJadwal(Request $request)
    {
        $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'kelas_id' => 'required|exists:kelas,id',
            'dosen_id' => 'required|exists:dosens,id',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'ruang' => 'required',
        ]);

        $jadwal = JadwalKuliah::create($request->all());
        $mk = $jadwal->mataKuliah;
        $totalPertemuan = ($mk && $mk->sks == 3) ? 21 : 14;

        $startDate = Carbon::create(2026, 8, 31);
        for ($p = 1; $p <= $totalPertemuan; $p++) {
            $tgl = (clone $startDate)->addWeeks($p - 1);
            $ptm = Pertemuan::create([
                'jadwal_kuliah_id' => $jadwal->id,
                'pertemuan_ke' => $p,
                'tanggal_jadwal' => $tgl->format('Y-m-d'),
                'jenis_pertemuan' => in_array($p, [3, 7, 10, 15, 18]) ? 'E-learning' : 'Tatap Muka',
                'is_open' => false,
            ]);

            $mahasiswas = Mahasiswa::where('kelas_id', $request->kelas_id)->get();
            foreach ($mahasiswas as $mhs) {
                Presensi::create([
                    'pertemuan_id' => $ptm->id,
                    'mahasiswa_id' => $mhs->id,
                    'status' => 'Belum Presensi',
                ]);
            }
        }

        return back()->with('success', "Jadwal kuliah berhasil dibuat dan {$totalPertemuan} pertemuan telah digenerate.");
    }

    public function updateJadwal(Request $request, $id)
    {
        $jadwal = JadwalKuliah::findOrFail($id);
        $request->validate([
            'dosen_id' => 'required|exists:dosens,id',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'ruang' => 'required',
        ]);

        $jadwal->update($request->only('dosen_id', 'hari', 'jam_mulai', 'jam_selesai', 'ruang'));
        return back()->with('success', 'Jadwal kuliah berhasil diperbarui.');
    }

    public function deleteJadwal($id)
    {
        $jadwal = JadwalKuliah::findOrFail($id);
        $jadwal->delete();
        return back()->with('success', 'Jadwal kuliah berhasil dihapus.');
    }

    // --- FITUR DRAG AND DROP BATCH IMPORT (JSON/CSV) ---
    public function importBatch(Request $request, $entity)
    {
        $data = $request->input('data');
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        if (!is_array($data) || empty($data)) {
            return response()->json(['success' => false, 'message' => 'Format file atau data tidak valid.'], 400);
        }

        $count = 0;
        switch ($entity) {
            case 'mahasiswa':
                foreach ($data as $item) {
                    if (empty($item['nim']) || empty($item['nama_lengkap'])) continue;
                    $kelas = Kelas::where('nama_kelas', $item['kelas'] ?? '')->orWhere('kode_kelas', $item['kelas'] ?? '')->first() ?? Kelas::first();
                    $user = User::firstOrCreate(['username' => $item['nim']], [
                        'name' => $item['nama_lengkap'],
                        'email' => $item['email'] ?? strtolower($item['nim']).'@mhs.unpam.ac.id',
                        'password' => Hash::make($item['password'] ?? 'mhs12345'),
                        'role' => 'mahasiswa',
                    ]);
                    Mahasiswa::firstOrCreate(['nim' => $item['nim']], [
                        'user_id' => $user->id,
                        'kelas_id' => $kelas->id,
                        'nama_lengkap' => $item['nama_lengkap'],
                        'no_telp' => $item['no_telp'] ?? $item['no_hp'] ?? $item['telepon'] ?? $item['hp'] ?? ('08' . rand(1111111111, 9999999999)),
                        'status' => 'Aktif',
                    ]);
                    $count++;
                }
                break;

            case 'dosen':
                foreach ($data as $item) {
                    if (empty($item['nidn']) || empty($item['nama_lengkap'])) continue;
                    $user = User::firstOrCreate(['username' => $item['nidn']], [
                        'name' => $item['nama_lengkap'],
                        'email' => $item['email'] ?? strtolower(explode(' ', $item['nama_lengkap'])[0]).'@unpam.ac.id',
                        'password' => Hash::make($item['password'] ?? 'dosen123'),
                        'role' => 'dosen',
                    ]);
                    Dosen::firstOrCreate(['nidn' => $item['nidn']], [
                        'user_id' => $user->id,
                        'nama_lengkap' => $item['nama_lengkap'],
                        'gelar' => $item['gelar'] ?? null,
                        'no_telp' => $item['no_telp'] ?? null,
                    ]);
                    $count++;
                }
                break;

            case 'matakuliah':
                foreach ($data as $item) {
                    if (empty($item['kode_mk']) || empty($item['nama_mk'])) continue;
                    MataKuliah::firstOrCreate(['kode_mk' => $item['kode_mk']], [
                        'nama_mk' => $item['nama_mk'],
                        'sks' => $item['sks'] ?? 3,
                        'semester' => $item['semester'] ?? 5,
                    ]);
                    $count++;
                }
                break;

            case 'kelas':
                foreach ($data as $item) {
                    if (empty($item['kode_kelas'])) continue;
                    Kelas::firstOrCreate(['kode_kelas' => $item['kode_kelas']], [
                        'nama_kelas' => $item['nama_kelas'] ?? $item['kode_kelas'],
                        'prodi' => $item['prodi'] ?? 'Sistem Informasi',
                        'semester' => $item['semester'] ?? 5,
                        'tahun_ajaran' => $item['tahun_ajaran'] ?? 'GANJIL 2026/2027',
                    ]);
                    $count++;
                }
                break;
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil menambahkan $count data $entity secara otomatis via drag and drop!"
        ]);
    }

    // --- SISTEM BUAT & KELOLA AKUN PENGGUNA (DOSEN & MAHASISWA) ---
    public function akun(Request $request)
    {
        $roleFilter = $request->query('role');
        $query = User::with(['dosen.jadwalKuliahs.mataKuliah', 'mahasiswa.kelas'])->whereIn('role', ['dosen', 'mahasiswa', 'admin']);

        if ($roleFilter && in_array($roleFilter, ['dosen', 'mahasiswa', 'admin'])) {
            $query->where('role', $roleFilter);
        }

        $usersList = $query->orderBy('role')->orderBy('name')->paginate(20);
        $kelasList = Kelas::all();
        $mataKuliahList = MataKuliah::all();

        return view('admin.akun', compact('usersList', 'kelasList', 'mataKuliahList', 'roleFilter'));
    }

    public function resetPasswordAkun(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|min:6',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', "Password untuk akun {$user->name} ({$user->username}) berhasil direset!");
    }

    public function deleteAkun($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus akun Anda sendiri saat sedang login.']);
        }

        if ($user->role === 'dosen' && $user->dosen) {
            $user->dosen->delete();
        } elseif ($user->role === 'mahasiswa' && $user->mahasiswa) {
            $user->mahasiswa->delete();
        }

        $user->delete();
        return back()->with('success', 'Akun berhasil dihapus dari sistem.');
    }
}
