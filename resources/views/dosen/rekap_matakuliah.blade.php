@extends('layouts.app', ['title' => 'Pilih Mata Kuliah - Rekap Presensi'])

@section('content')
<div class="unpam-banner">
    <h2>REKAP PRESENSI KELAS</h2>
    <p>Langkah 1: Pilih Mata Kuliah &bull; Dosen: <strong>{{ $dosen->nama_lengkap }}</strong></p>
</div>

<div class="card">
    <h3 class="card-title">Pilih Mata Kuliah untuk Melihat Rekap</h3>
    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">Pantau statistik kehadiran mahasiswa per kelas pada mata kuliah Anda.</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px">
        @foreach($matkulList as $item)
            @php $mk = $item['mata_kuliah']; @endphp
            <div class="card" style="margin: 0; border: 1.5px solid var(--border-line)">
                <span class="badge badge-info" style="margin-bottom: 8px">{{ $mk->kode_mk }}</span>
                <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 8px">{{ $mk->nama_mk }}</h4>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px">
                    {{ $item['jadwals']->count() }} Kelas Kuliah
                </p>
                <a href="{{ route('dosen.rekap.kelas', $mk->id) }}" class="btn btn-primary btn-block">
                    Pilih Kelas Rekap
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
