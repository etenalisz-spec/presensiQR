@extends('layouts.app', ['title' => 'Presensi Mahasiswa'])

@section('content')
<div class="unpam-banner">
    <h2>PRESENSI</h2>
    <p>Tahun Ajaran GANJIL 2026/2027 &bull; Kelas: <strong>{{ $mahasiswa->kelas->nama_kelas }}</strong> &bull; Mahasiswa: <strong>{{ $mahasiswa->nama_lengkap }} ({{ $mahasiswa->nim }})</strong></p>
</div>

<div class="card" style="padding: 0; overflow: hidden">
    <div class="table-responsive">
        <table class="unpam-table">
            <thead>
                <tr>
                    <th>KODE MATA KULIAH</th>
                    <th>NAMA MATA KULIAH</th>
                    <th>KELAS</th>
                    <th style="text-align: center; width: 100px">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwalList as $jadwal)
                <tr>
                    <td style="font-weight: 700; color: #0284C7">{{ $jadwal->mataKuliah->kode_mk }}</td>
                    <td style="font-weight: 700; color: #1E293B">
                        <a href="{{ route('mahasiswa.pertemuan', $jadwal->id) }}" style="text-decoration: none; color: inherit">
                            {{ $jadwal->mataKuliah->nama_mk }}
                        </a>
                        <div style="font-size: 12px; font-weight: 500; color: var(--text-muted); margin-top: 3px">
                            Dosen: {{ $jadwal->dosen->nama_lengkap }} &bull; {{ $jadwal->hari }}, {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} &bull; Ruang {{ $jadwal->ruang }}
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-info">{{ $jadwal->kelas->nama_kelas }}</span>
                    </td>
                    <td style="text-align: center">
                        <a href="{{ route('mahasiswa.pertemuan', $jadwal->id) }}" class="btn btn-outline btn-sm" title="Buka Pertemuan" style="border-radius: 50%; width: 34px; height: 34px; padding: 0">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 36px; color: var(--text-muted)">
                        Belum ada jadwal mata kuliah yang diatur untuk kelas Anda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
