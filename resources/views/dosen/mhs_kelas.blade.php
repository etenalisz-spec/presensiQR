@extends('layouts.app', ['title' => 'Pilih Kelas - Data Mahasiswa'])

@section('content')
<div class="unpam-banner">
    <h2>DATA MAHASISWA</h2>
    <p>Langkah 2: Pilih Kelas &bull; {{ $mataKuliah->nama_mk }} ({{ $mataKuliah->kode_mk }})</p>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px">
        <div>
            <h3 class="card-title" style="margin: 0">Pilih Kelas Kuliah</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px">Pilih kelas untuk menampilkan seluruh data mahasiswa di kelas tersebut.</p>
        </div>
        <a href="{{ route('dosen.mhs.matakuliah') }}" class="btn btn-outline btn-sm">
            &larr; Kembali ke Daftar MK
        </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px">
        @foreach($jadwalList as $jadwal)
            <div class="card" style="margin: 0; border: 1.5px solid var(--border-line)">
                <span class="badge badge-info" style="font-size: 13px; margin-bottom: 8px">{{ $jadwal->kelas->nama_kelas }}</span>
                <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 6px">{{ $jadwal->kelas->prodi }}</h4>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 14px">
                    Total Mahasiswa: <strong>{{ $jadwal->kelas->mahasiswas->count() }} Orang</strong>
                </p>
                <a href="{{ route('dosen.mhs.list', $jadwal->id) }}" class="btn btn-primary btn-block">
                    Lihat Daftar Mahasiswa
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
