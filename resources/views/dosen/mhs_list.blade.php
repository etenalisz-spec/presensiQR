@extends('layouts.app', ['title' => 'Daftar Mahasiswa Kelas'])

@section('content')
<div class="unpam-banner">
    <h2>DATA MAHASISWA RUANG KELAS {{ $jadwal->kelas->nama_kelas }}</h2>
    <p>{{ $jadwal->mataKuliah->nama_mk }} &bull; Ruang: <strong>{{ $jadwal->ruang }}</strong> &bull; Semester {{ $jadwal->kelas->semester }} &bull; Total: <strong>{{ $mahasiswaList->count() }} Mahasiswa</strong></p>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px">
        <div>
            <h3 class="card-title" style="margin: 0">Daftar Mahasiswa Ruang Kelas {{ $jadwal->kelas->nama_kelas }}</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px">Data mahasiswa yang terdaftar di ruang kelas {{ $jadwal->kelas->nama_kelas }} (Ruang {{ $jadwal->ruang }}).</p>
        </div>
        <a href="{{ route('dosen.mhs.kelas', $jadwal->mata_kuliah_id) }}" class="btn btn-outline btn-sm">
            &larr; Kembali ke Pilihan Kelas
        </a>
    </div>

    <div class="table-responsive">
        <table class="unpam-table">
            <thead>
                <tr>
                    <th style="width: 50px">NO</th>
                    <th>NIM</th>
                    <th>NAMA LENGKAP</th>
                    <th>KODE KELAS</th>
                    <th>RUANG KELAS</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mahasiswaList as $idx => $mhs)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td style="font-weight: 700; color: #0284C7">{{ $mhs->nim }}</td>
                    <td style="font-weight: 700">{{ $mhs->nama_lengkap }}</td>
                    <td>
                        <span class="badge badge-info" style="font-weight: 700">{{ $jadwal->kelas->kode_kelas }}</span>
                    </td>
                    <td>
                        <span class="badge badge-warning" style="font-weight: 700">Ruang {{ $jadwal->ruang }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $mhs->status === 'Aktif' ? 'badge-success' : 'badge-warning' }}">{{ $mhs->status }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 24px; color: var(--text-muted)">
                        Belum ada mahasiswa yang terdaftar di kelas ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
