@extends('layouts.app', ['title' => 'Pilih Kelas'])

@section('content')
<div class="unpam-banner">
    <h2>KELAS YANG DIAJAR</h2>
    <p>{{ $mataKuliah->nama_mk }} ({{ $mataKuliah->kode_mk }}) &bull; Dosen: <strong>{{ $dosen->nama_lengkap }}</strong></p>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px">
        <div>
            <h3 class="card-title" style="margin: 0">Daftar Kelas Mata Kuliah: {{ $mataKuliah->nama_mk }}</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px">Pilih kelas di bawah untuk membuka sesi pertemuan.</p>
        </div>
        <a href="{{ route('dosen.matakuliah') }}" class="btn btn-outline btn-sm">
            &larr; Kembali ke Daftar MK
        </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px">
        @foreach($jadwalList as $jadwal)
            <div class="card" style="margin: 0; border: 1.5px solid var(--border-line)">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px">
                    <span class="badge badge-info" style="font-size: 13px; padding: 6px 12px">{{ $jadwal->kelas->nama_kelas }}</span>
                    <span style="font-size: 12px; color: var(--text-muted); font-weight: 600">{{ $jadwal->kelas->prodi }}</span>
                </div>
                <div style="font-size: 13.5px; color: #475569; margin-bottom: 16px; line-height: 1.6">
                    <div><strong>Jadwal:</strong> {{ $jadwal->hari }}, {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</div>
                    <div><strong>Ruang:</strong> {{ $jadwal->ruang }}</div>
                    <div><strong>Tahun Ajaran:</strong> {{ $jadwal->kelas->tahun_ajaran }}</div>
                </div>

                <a href="{{ route('dosen.pertemuan', $jadwal->id) }}" class="btn btn-primary btn-block">
                    Buka Pertemuan
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
