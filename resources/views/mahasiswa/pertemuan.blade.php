@extends('layouts.app', ['title' => 'Pertemuan Mata Kuliah'])

@section('content')
<!-- Banner UNPAM (Sesuai Screenshot 2) -->
<div class="unpam-banner">
    <h2>PRESENSI</h2>
    <p>Pertemuan Mata Kuliah &bull; Dosen: <strong>{{ $jadwal->dosen->nama_lengkap }}</strong></p>
</div>

<!-- Card Utama Judul Mata Kuliah (Sesuai Screenshot 2) -->
<div class="card" style="margin-bottom: 20px">
    <h2 style="font-size: 20px; font-weight: 800; text-transform: uppercase; color: #1E293B">
        {{ $jadwal->mataKuliah->nama_mk }}
    </h2>
    <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px">
        Kode MK: <strong>{{ $jadwal->mataKuliah->kode_mk }}</strong> &bull; Kelas: <strong>{{ $jadwal->kelas->nama_kelas }}</strong> &bull; Ruang: <strong>{{ $jadwal->ruang }}</strong>
    </p>
</div>

<!-- Daftar Kartu Pertemuan 1 - 14 (PERSIS SCREENSHOT 2) -->
<div style="display: flex; flex-direction: column; gap: 16px">
    @foreach($jadwal->pertemuans as $ptm)
        @php
            $presensi = $presensiMap->get($ptm->id);
            $status = $presensi ? $presensi->status : 'Belum Presensi';
            $tglFormatted = $ptm->tanggal_jadwal ? \Carbon\Carbon::parse($ptm->tanggal_jadwal)->translatedFormat('d F Y') : '-';
            $jamFormatted = substr($jadwal->jam_mulai, 0, 5) . ' - ' . substr($jadwal->jam_selesai, 0, 5);
        @endphp

        <div class="card" style="padding: 18px 20px; border-radius: 14px">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px">
                <!-- Kolom Kiri: Jadwal Perkuliahan -->
                <div>
                    <h4 style="font-size: 14px; font-weight: 700; color: #1E293B; margin-bottom: 8px">Jadwal Perkuliahan</h4>
                    <ul style="list-style: none; font-size: 13px; color: #475569; display: flex; flex-direction: column; gap: 4px">
                        <li>&bull; {{ $ptm->jenis_pertemuan }} Pertemuan Ke - {{ $ptm->pertemuan_ke }}</li>
                        <li>&bull; {{ $tglFormatted }}</li>
                        <li>&bull; {{ $jamFormatted }}</li>
                        @if($ptm->tema)
                            <li style="margin-top: 4px; font-size: 12px; color: #0284C7; font-weight: 600">Tema: {{ $ptm->tema }}</li>
                        @endif
                    </ul>
                </div>

                <!-- Kolom Kanan: Status Presensi -->
                <div>
                    <h4 style="font-size: 14px; font-weight: 700; color: #1E293B; margin-bottom: 8px">Status Presensi</h4>
                    <ul style="list-style: none; font-size: 13px; display: flex; flex-direction: column; gap: 4px">
                        @if($status === 'Hadir')
                            <li style="font-weight: 700; color: #10B981">&bull; Hadir</li>
                            @if($presensi && $presensi->waktu_presensi)
                                <li style="color: #475569">&bull; {{ $presensi->waktu_presensi->translatedFormat('d F Y') }}</li>
                                <li style="color: #475569">&bull; {{ $presensi->waktu_presensi->format('H:i') }} WIB</li>
                            @endif
                        @elseif($status === 'Absen')
                            <li style="font-weight: 700; color: #EF4444">&bull; Absen</li>
                            @if($presensi && $presensi->waktu_presensi)
                                <li style="color: #475569">&bull; {{ $presensi->waktu_presensi->translatedFormat('d F Y') }}</li>
                                <li style="color: #475569">&bull; {{ $presensi->waktu_presensi->format('H:i') }} WIB</li>
                            @endif
                        @else
                            <li style="font-weight: 700; color: #D97706">&bull; Belum Presensi</li>
                            <li style="color: #94A3B8; font-size: 12px">&bull; Silakan scan QR dosen untuk presensi</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Tombol Biru Lebar SCAN QR (Persis Screenshot 2) -->
            @if($status === 'Hadir')
                <button type="button" class="btn btn-outline btn-block" style="background: #F0FDF4; border-color: #86EFAC; color: #15803D; cursor: default">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    SUDAH HADIR PADA PERTEMUAN INI
                </button>
            @else
                <a href="{{ route('mahasiswa.scan', $ptm->id) }}" class="btn btn-primary btn-block" style="height: 44px; font-weight: 700; letter-spacing: 0.5px">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><circle cx="12" cy="12" r="3"/></svg>
                    SCAN QR
                </a>
            @endif
        </div>
    @endforeach
</div>

<!-- Tombol Kuning Mengambang Kembali (Persis Screenshot 2) -->
<a href="{{ route('mahasiswa.presensi') }}" class="float-back-btn" title="Kembali ke Daftar Mata Kuliah">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
    </svg>
</a>
@endsection
