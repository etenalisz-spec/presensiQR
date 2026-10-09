@extends('layouts.app', ['title' => 'Mata Kuliah Diampu'])

@section('content')
<div class="unpam-banner">
    <h2>MATA KULIAH DIAMPU</h2>
    <p>Selamat datang, <strong>{{ $dosen->nama_lengkap }}</strong> &bull; NIDN: {{ $dosen->nidn }}</p>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px">
        <h3 class="card-title" style="margin: 0">Pilih Mata Kuliah untuk Mengelola Presensi</h3>
        <span class="badge badge-info">{{ count($matkulList) }} Mata Kuliah</span>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px">
        @forelse($matkulList as $item)
            @php $mk = $item['mata_kuliah']; @endphp
            <div class="card" style="margin: 0; display: flex; flex-direction: column; justify-content: space-between; border-color: #CBD5E1; transition: border-color 0.2s">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px">
                        <span class="badge badge-info">{{ $mk->kode_mk }}</span>
                        <span style="font-size: 12px; font-weight: 700; color: var(--text-muted)">{{ $mk->sks }} SKS</span>
                    </div>
                    <h4 style="font-size: 16px; font-weight: 700; color: #1E293B; margin-bottom: 8px">{{ $mk->nama_mk }}</h4>
                    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 14px">
                        Total Kelas yang Diajar: <strong>{{ $item['total_kelas'] }} Kelas</strong>
                    </p>
                </div>

                <a href="{{ route('dosen.kelas', $mk->id) }}" class="btn btn-primary btn-block">
                    Pilih Kelas Kuliah
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-muted)">
                Anda belum ditugaskan untuk mengampu mata kuliah apapun oleh Admin.
            </div>
        @endforelse
    </div>
</div>
@endsection
