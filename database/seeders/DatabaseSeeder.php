<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\JadwalKuliah;
use App\Models\Pertemuan;
use App\Models\Presensi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        User::create([
            'name' => 'Admin Program Studi',
            'username' => 'admin',
            'email' => 'admin.si@unpam.ac.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // 2. Akun & Data Dosen Ibu Gusmayeni
        $userDosen = User::create([
            'name' => 'Gusmayeni, S.Kom., M.Kom.',
            'username' => 'dosen',
            'email' => 'gusmayeni@unpam.ac.id',
            'password' => Hash::make('dosen123'),
            'role' => 'dosen',
        ]);

        $dosenGusmayeni = Dosen::create([
            'user_id' => $userDosen->id,
            'nidn' => '0412058001',
            'nama_lengkap' => 'Gusmayeni, S.Kom., M.Kom.',
            'gelar' => 'M.Kom.',
            'no_telp' => '081234567890',
        ]);

        // Tambahan Dosen Lain
        $dosenLainData = [
            ['nama' => 'Sri Wahyuni, M.T.', 'nidn' => '0308078602', 'user' => 'dosen2'],
            ['nama' => 'Bambang Setiawan, M.Kom.', 'nidn' => '0921118503', 'user' => 'dosen3'],
            ['nama' => 'Ratna Kusumawati, M.Cs.', 'nidn' => '0115098804', 'user' => 'dosen4'],
        ];

        $dosenList = [$dosenGusmayeni];
        foreach ($dosenLainData as $dl) {
            $u = User::create([
                'name' => $dl['nama'],
                'username' => $dl['user'],
                'email' => $dl['user'].'@unpam.ac.id',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
            ]);
            $dosenList[] = Dosen::create([
                'user_id' => $u->id,
                'nidn' => $dl['nidn'],
                'nama_lengkap' => $dl['nama'],
                'no_telp' => '0812' . rand(10000000, 99999999),
            ]);
        }

        // 3. Kelas (sesuai Screenshot: 05SIFE001)
        $kelasUtama = Kelas::create([
            'kode_kelas' => '05SIFE001',
            'nama_kelas' => '05SIFE001',
            'prodi' => 'Sistem Informasi',
            'semester' => 5,
            'tahun_ajaran' => 'GANJIL 2026/2027',
        ]);

        $kelas2 = Kelas::create([
            'kode_kelas' => '05SIFE002',
            'nama_kelas' => '05SIFE002',
            'prodi' => 'Sistem Informasi',
            'semester' => 5,
            'tahun_ajaran' => 'GANJIL 2026/2027',
        ]);

        // 4. Mahasiswa Kelas 05SIFE001 (Akun utama: Aulia Rahmawati NIM 2211001)
        $userMhs = User::create([
            'name' => 'Aulia Rahmawati',
            'username' => '2211001',
            'email' => 'aulia@mhs.unpam.ac.id',
            'password' => Hash::make('mhs12345'),
            'role' => 'mahasiswa',
        ]);

        $mhsAulia = Mahasiswa::create([
            'user_id' => $userMhs->id,
            'kelas_id' => $kelasUtama->id,
            'nim' => '2211001',
            'nama_lengkap' => 'Aulia Rahmawati',
            'no_telp' => '081298765432',
            'status' => 'Aktif',
        ]);

        // Mahasiswa teman sekelas di 05SIFE001
        $temanSekelas = [
            'Bagas Saputra', 'Citra Lestari', 'Dimas Prakoso', 'Elisa Nurhaliza', 
            'Fajar Ramadhan', 'Gita Permata', 'Hafiz Maulana', 'Intan Puspita', 'Joko Santoso'
        ];

        $allMhsKelasUtama = [$mhsAulia];
        foreach ($temanSekelas as $idx => $nama) {
            $nim = '22110' . str_pad($idx + 2, 2, '0', STR_PAD_LEFT);
            $u = User::create([
                'name' => $nama,
                'username' => $nim,
                'email' => strtolower(explode(' ', $nama)[0]) . '@mhs.unpam.ac.id',
                'password' => Hash::make('mhs12345'),
                'role' => 'mahasiswa',
            ]);
            $allMhsKelasUtama[] = Mahasiswa::create([
                'user_id' => $u->id,
                'kelas_id' => $kelasUtama->id,
                'nim' => $nim,
                'nama_lengkap' => $nama,
                'no_telp' => '0813876543' . str_pad($idx + 1, 2, '0', STR_PAD_LEFT),
                'status' => 'Aktif',
            ]);
        }

        // 5. Daftar 8 Mata Kuliah (PERSIS SESUAI SCREENSHOT 1)
        $daftarMatkul = [
            ['kode' => '22SIF0273', 'nama' => 'BAHASA QUERY TERSTRUKTUR', 'sks' => 3, 'dosen_idx' => 0], // Ibu Gusmayeni
            ['kode' => '22SIF0323', 'nama' => 'CLOUD COMPUTING', 'sks' => 3, 'dosen_idx' => 1],
            ['kode' => '22SIF0312', 'nama' => 'INTERAKSI MANUSIA DAN KOMPUTER', 'sks' => 3, 'dosen_idx' => 3],
            ['kode' => '22SIF0252', 'nama' => 'KECERDASAN BISNIS', 'sks' => 3, 'dosen_idx' => 2],
            ['kode' => '22SIF0332', 'nama' => 'Kewirausahaan (Technopreneurship)', 'sks' => 2, 'dosen_idx' => 1],
            ['kode' => '22SIF0293', 'nama' => 'MANAJEMEN PROYEK PERANGKAT LUNAK', 'sks' => 3, 'dosen_idx' => 0], // Ibu Gusmayeni
            ['kode' => '22SIF0262', 'nama' => 'MANAJEMEN SAINS', 'sks' => 2, 'dosen_idx' => 2],
            ['kode' => '22SIF0283', 'nama' => 'METODOLOGI RISET', 'sks' => 3, 'dosen_idx' => 3],
        ];

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jamList = [
            ['07:40:00', '09:20:00'],
            ['09:30:00', '11:10:00'],
            ['13:00:00', '14:40:00'],
            ['15:00:00', '16:40:00']
        ];

        foreach ($daftarMatkul as $index => $dm) {
            $mk = MataKuliah::create([
                'kode_mk' => $dm['kode'],
                'nama_mk' => $dm['nama'],
                'sks' => $dm['sks'],
                'semester' => 5,
            ]);

            $dosenPengampu = $dosenList[$dm['dosen_idx']] ?? $dosenGusmayeni;
            $jam = $jamList[$index % count($jamList)];

            // Jadwalkan untuk kelas 05SIFE001
            $jadwal = JadwalKuliah::create([
                'mata_kuliah_id' => $mk->id,
                'kelas_id' => $kelasUtama->id,
                'dosen_id' => $dosenPengampu->id,
                'hari' => $hariList[$index % count($hariList)],
                'jam_mulai' => $jam[0],
                'jam_selesai' => $jam[1],
                'ruang' => 'V.' . (401 + $index),
            ]);

            // 6. Buat Sesi PERTEMUAN (2 SKS = 14 Pertemuan, 3 SKS = 21 Pertemuan)
            $totalPertemuan = $dm['sks'] == 3 ? 21 : 14;
            $startDate = Carbon::create(2026, 8, 31); // 31 Agustus 2026
            for ($p = 1; $p <= $totalPertemuan; $p++) {
                $tgl = (clone $startDate)->addWeeks($p - 1);
                $isElearning = in_array($p, [3, 7, 10, 15, 18]);
                $jenis = $isElearning ? 'E-learning' : 'Tatap Muka';

                // Pertemuan 1 s/d 6 sudah lewat & dibuka dosen
                $isOpen = $p <= 6;
                $tema = $isOpen ? "Materi Pokok Pertemuan Ke-{$p}: Pembahasan Modul {$p}" : null;
                $desk = $isOpen ? "Membahas konsep dan latihan praktikum pertemuan {$p} secara mendalam." : null;

                $pertemuan = Pertemuan::create([
                    'jadwal_kuliah_id' => $jadwal->id,
                    'pertemuan_ke' => $p,
                    'tanggal_jadwal' => $tgl->format('Y-m-d'),
                    'tema' => $tema,
                    'jenis_pertemuan' => $jenis,
                    'deskripsi' => $desk,
                    'is_open' => $isOpen,
                ]);

                // Dummy data presensi untuk mahasiswa di pertemuan 1-6 (Persis Screenshot 2)
                if ($p <= 6) {
                    foreach ($allMhsKelasUtama as $mhs) {
                        $isAulia = $mhs->id === $mhsAulia->id;
                        // Di screenshot 2: Pertemuan 2 Aulia "Absen", pertemuan 1, 3, 4, 5, 6 "Hadir"
                        $status = ($isAulia && $p === 2) ? 'Absen' : (($p % 4 === 0 && !$isAulia) ? 'Absen' : 'Hadir');
                        $waktu = $status === 'Hadir' ? $tgl->copy()->setTime(rand(7, 8), rand(40, 59)) : null;

                        Presensi::create([
                            'pertemuan_id' => $pertemuan->id,
                            'mahasiswa_id' => $mhs->id,
                            'status' => $status,
                            'waktu_presensi' => $waktu,
                            'metode' => $status === 'Hadir' ? 'QR Scan' : null,
                        ]);
                    }
                } else {
                    // Pertemuan setelahnya: status Belum Presensi
                    foreach ($allMhsKelasUtama as $mhs) {
                        Presensi::create([
                            'pertemuan_id' => $pertemuan->id,
                            'mahasiswa_id' => $mhs->id,
                            'status' => 'Belum Presensi',
                        ]);
                    }
                }
            }
        }
    }
}
