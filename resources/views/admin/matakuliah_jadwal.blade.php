@extends('layouts.app', ['title' => 'Mata Kuliah & Jadwal'])

@section('content')
<div class="unpam-banner">
    <h2>MATA KULIAH & JADWAL PERKULIAHAN</h2>
    <p>Kelola Data Mata Kuliah, Penugasan Dosen Pengampu, dan Detail Jadwal Kelas Secara Berjenjang</p>
</div>

<!-- Navigasi Alur Breadcrumb -->
<div class="card" style="margin-bottom: 20px; padding: 14px 20px">
    <div style="display: flex; align-items: center; gap: 10px; font-size: 13.5px; font-weight: 600; flex-wrap: wrap">
        <a href="{{ route('admin.matakuliah_jadwal') }}" style="color: {{ !$selectedMk ? 'var(--unpam-blue)' : '#64748B' }}; text-decoration: none">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: text-bottom"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5z"/><path d="M4 19.5V21h16v-4"/></svg>
            1. Daftar Mata Kuliah
        </a>

        @if($selectedMk)
            <span style="color: #94A3B8">&rsaquo;</span>
            <a href="{{ route('admin.matakuliah_jadwal', ['mk_id' => $selectedMk->id]) }}" style="color: {{ !$selectedDosen ? 'var(--unpam-blue)' : '#64748B' }}; text-decoration: none">
                2. Dosen Pengampu ({{ $selectedMk->nama_mk }})
            </a>
        @endif

        @if($selectedDosen)
            <span style="color: #94A3B8">&rsaquo;</span>
            <span style="color: var(--unpam-blue)">
                3. Detail Jadwal ({{ $selectedDosen->nama_lengkap }})
            </span>
        @endif
    </div>
</div>

@if(!$selectedMk)
    <!-- ==================== TAHAP 1: DAFTAR MATA KULIAH ==================== -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 20px">
            <div>
                <h3 class="card-title" style="margin: 0">Pilih Mata Kuliah</h3>
                <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px">
                    Klik pada kotak mata kuliah untuk melihat dosen pengampu dan detail jadwalnya.
                </p>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap">
                <button type="button" class="btn btn-outline" onclick="openDropModal('matakuliah')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Drag & Drop Import
                </button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('add-mk-modal').style.display = 'grid'">
                    + Tambah Mata Kuliah Baru
                </button>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 18px">
            @foreach($mataKuliahList as $mk)
                @php
                    $dosenCount = $mk->jadwalKuliahs->pluck('dosen_id')->unique()->count();
                    $kelasCount = $mk->jadwalKuliahs->pluck('kelas_id')->unique()->count();
                @endphp
                <div class="card" style="margin: 0; border: 1.5px solid var(--border-line); transition: all 0.2s ease; display: flex; flex-direction: column; justify-content: space-between">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px">
                            <span class="badge badge-info" style="font-weight: 700">{{ $mk->kode_mk }}</span>
                            <span class="badge {{ $mk->sks == 3 ? 'badge-primary' : 'badge-warning' }}">
                                {{ $mk->sks }} SKS &bull; {{ $mk->sks == 3 ? '21' : '14' }} Pertemuan
                            </span>
                        </div>

                        <h4 style="font-size: 16px; font-weight: 800; color: #1E293B; margin-bottom: 8px; line-height: 1.4">
                            {{ $mk->nama_mk }}
                        </h4>
                        
                        <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px">
                            <div>Semester: <strong>Semester {{ $mk->semester }}</strong></div>
                            <div style="margin-top: 4px">
                                Dosen Pengampu: 
                                <strong style="color: #0284C7">{{ $dosenCount }} Dosen</strong> &bull; 
                                Kelas: <strong>{{ $kelasCount }} Kelas</strong>
                            </div>
                        </div>
                    </div>

                    <div style="padding-top: 14px; border-top: 1px solid var(--border-line); display: flex; justify-content: space-between; align-items: center">
                        <div style="display: flex; gap: 6px">
                            <button type="button" class="btn btn-outline btn-sm" onclick="openEditMkModal({{ $mk->id }}, '{{ addslashes($mk->kode_mk) }}', '{{ addslashes($mk->nama_mk) }}', {{ $mk->sks }}, {{ $mk->semester }})">
                                Edit
                            </button>
                            <form action="{{ route('admin.matakuliah.delete', $mk->id) }}" method="POST" style="display: inline" onsubmit="return confirm('Hapus mata kuliah ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background: #FDEBE9; color: #C2352B; border: 1px solid #F8C3BD">
                                    Hapus
                                </button>
                            </form>
                        </div>

                        <a href="{{ route('admin.matakuliah_jadwal', ['mk_id' => $mk->id]) }}" class="btn btn-primary btn-sm" style="font-weight: 700">
                            Pilih Dosen &rsaquo;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@elseif($selectedMk && !$selectedDosen)
    <!-- ==================== TAHAP 2: DAFTAR DOSEN PENGAMPU ==================== -->
    <div class="card" style="margin-bottom: 20px">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px">
            <div>
                <span class="badge badge-info" style="font-size: 12px">{{ $selectedMk->kode_mk }}</span>
                <h3 style="font-size: 20px; font-weight: 800; color: #1E293B; margin-top: 4px">
                    {{ $selectedMk->nama_mk }}
                </h3>
                <p style="font-size: 13px; color: var(--text-muted); margin-top: 2px">
                    Bobot: <strong>{{ $selectedMk->sks }} SKS ({{ $selectedMk->sks == 3 ? '21' : '14' }} Pertemuan)</strong> &bull; Semester {{ $selectedMk->semester }}
                </p>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap">
                <a href="{{ route('admin.matakuliah_jadwal') }}" class="btn btn-outline btn-sm">
                    &larr; Kembali ke Semua MK
                </a>
                <button type="button" class="btn btn-primary btn-sm" onclick="openAddJadwalForMk({{ $selectedMk->id }})">
                    + Plot Dosen / Kelas Baru
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title" style="margin-bottom: 6px">Pilih Dosen Pengampu</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">
            Klik pada dosen pengampu untuk melihat deskripsi lengkap jadwal, kelas, ruang, dan mahasiswa.
        </p>

        @if($dosensPengampu->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 18px">
                @foreach($dosensPengampu as $d)
                    @php
                        $kelasListDosen = $d->jadwalKuliahs->pluck('kelas.nama_kelas')->unique();
                    @endphp
                    <div class="card" style="margin: 0; border: 1.5px solid var(--border-line); display: flex; flex-direction: column; justify-content: space-between">
                        <div>
                            <div style="display: flex; gap: 14px; align-items: center; margin-bottom: 12px">
                                <div style="width: 48px; height: 48px; border-radius: 50%; background: #EBF5FF; color: var(--unpam-blue); display: grid; place-items: center; font-weight: 800; font-size: 18px">
                                    {{ substr($d->nama_lengkap, 0, 2) }}
                                </div>
                                <div>
                                    <h4 style="font-size: 15px; font-weight: 800; color: #1E293B">
                                        {{ $d->nama_lengkap }}
                                    </h4>
                                    <small style="color: var(--text-muted); font-weight: 600">NIDN: {{ $d->nidn }}</small>
                                </div>
                            </div>

                            <div style="font-size: 13px; color: #475569; margin-bottom: 14px">
                                <div><strong>Kelas yang diajar:</strong></div>
                                <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-top: 6px">
                                    @foreach($kelasListDosen as $kn)
                                        <span class="badge badge-info">{{ $kn }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div style="padding-top: 14px; border-top: 1px solid var(--border-line)">
                            <a href="{{ route('admin.matakuliah_jadwal', ['mk_id' => $selectedMk->id, 'dosen_id' => $d->id]) }}" class="btn btn-primary btn-block">
                                Buka Detail Jadwal Lengkap &rsaquo;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 40px 20px; background: #F8FAFC; border-radius: 12px; border: 1px dashed var(--border-line)">
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="1.6" style="margin: 0 auto 10px; display: block"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <h4 style="font-size: 16px; font-weight: 700; color: #1E293B">Belum Ada Dosen Ditugaskan</h4>
                <p style="font-size: 13px; color: var(--text-muted); margin: 6px auto 16px; max-width: 400px">
                    Mata kuliah ini belum memiliki dosen pengampu dan plotting kelas.
                </p>
                <button type="button" class="btn btn-primary" onclick="openAddJadwalForMk({{ $selectedMk->id }})">
                    + Tugaskan Dosen & Kelas Sekarang
                </button>
            </div>
        @endif
    </div>

@elseif($selectedMk && $selectedDosen)
    <!-- ==================== TAHAP 3: DESKRIPSI LENGKAP JADWAL & PLOTTING ==================== -->
    <div class="card" style="margin-bottom: 20px">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px">
            <div>
                <span class="badge badge-info" style="font-size: 12px">DETAIL JADWAL KULIAH</span>
                <h3 style="font-size: 20px; font-weight: 800; color: #1E293B; margin-top: 4px">
                    {{ $selectedMk->nama_mk }}
                </h3>
                <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 2px">
                    Dosen Pengampu: <strong style="color: #0284C7">{{ $selectedDosen->nama_lengkap }}</strong> &bull; NIDN: <strong>{{ $selectedDosen->nidn }}</strong>
                </p>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap">
                <a href="{{ route('admin.matakuliah_jadwal', ['mk_id' => $selectedMk->id]) }}" class="btn btn-outline btn-sm">
                    &larr; Kembali ke Daftar Dosen
                </a>
                <button type="button" class="btn btn-primary btn-sm" onclick="openAddJadwalForMkDosen({{ $selectedMk->id }}, {{ $selectedDosen->id }})">
                    + Tambah Kelas Lain Untuk Dosen Ini
                </button>
            </div>
        </div>
    </div>

    <!-- Deskripsi Lengkap Semua Kelas yang Diajar Dosen untuk Mata Kuliah Ini -->
    <div style="display: flex; flex-direction: column; gap: 18px">
        @foreach($detailJadwals as $jadwal)
            <div class="card" style="margin: 0; padding: 24px; border-left: 5px solid var(--unpam-blue)">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 14px; margin-bottom: 20px">
                    <div>
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px">
                            <span class="badge badge-info" style="font-size: 14px; padding: 6px 14px">{{ $jadwal->kelas->nama_kelas }}</span>
                            <span style="font-size: 13px; color: var(--text-muted); font-weight: 600">{{ $jadwal->kelas->prodi }}</span>
                        </div>
                        <h4 style="font-size: 18px; font-weight: 800; color: #1E293B">
                            {{ $selectedMk->nama_mk }} ({{ $selectedMk->kode_mk }})
                        </h4>
                    </div>

                    <div style="display: flex; gap: 8px">
                        <button type="button" class="btn btn-outline btn-sm" onclick="openEditJadwalModal({{ $jadwal->id }}, {{ $jadwal->dosen_id }}, '{{ $jadwal->hari }}', '{{ substr($jadwal->jam_mulai, 0, 5) }}', '{{ substr($jadwal->jam_selesai, 0, 5) }}', '{{ addslashes($jadwal->ruang) }}')">
                            Edit Jadwal / Ruang
                        </button>
                        <form action="{{ route('admin.jadwal.delete', $jadwal->id) }}" method="POST" style="display: inline" onsubmit="return confirm('Hapus jadwal ini beserta sesi pertemuannya?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm" style="background: #FDEBE9; color: #C2352B; border: 1px solid #F8C3BD">
                                Hapus Plotting
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Grid Rincian Deskripsi Lengkap Sesuai Permintaan User -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; background: #F8FAFC; padding: 18px; border-radius: 12px; margin-bottom: 16px">
                    <div>
                        <span style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted)">Mata Kuliah & Kode</span>
                        <div style="font-size: 14px; font-weight: 700; color: #1E293B; margin-top: 4px">
                            {{ $selectedMk->nama_mk }}
                            <div style="font-size: 12px; color: #0284C7">{{ $selectedMk->kode_mk }}</div>
                        </div>
                    </div>

                    <div>
                        <span style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted)">Bobot SKS & Pertemuan</span>
                        <div style="font-size: 14px; font-weight: 700; color: #0C7A52; margin-top: 4px">
                            {{ $selectedMk->sks }} SKS &bull; {{ count($jadwal->pertemuans) }} Pertemuan
                        </div>
                    </div>

                    <div>
                        <span style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted)">Dosen Pengampu</span>
                        <div style="font-size: 14px; font-weight: 700; color: #1E293B; margin-top: 4px">
                            {{ $selectedDosen->nama_lengkap }}
                            <div style="font-size: 12px; color: var(--text-muted)">NIDN: {{ $selectedDosen->nidn }}</div>
                        </div>
                    </div>

                    <div>
                        <span style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted)">Kelas & Jumlah Mahasiswa</span>
                        <div style="font-size: 14px; font-weight: 700; color: #1E293B; margin-top: 4px">
                            {{ $jadwal->kelas->nama_kelas }}
                            <div style="font-size: 12px; color: #0284C7; font-weight: 700">
                                {{ $jadwal->kelas->mahasiswas->count() }} Mahasiswa Terdaftar
                            </div>
                        </div>
                    </div>

                    <div>
                        <span style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted)">Hari & Jam Perkuliahan</span>
                        <div style="font-size: 14px; font-weight: 700; color: #1E293B; margin-top: 4px">
                            {{ $jadwal->hari }}
                            <div style="font-size: 12px; color: #475569">
                                {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WIB
                            </div>
                        </div>
                    </div>

                    <div>
                        <span style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted)">Ruang Kelas</span>
                        <div style="font-size: 14px; font-weight: 700; color: #D97706; margin-top: 4px">
                            Ruang {{ $jadwal->ruang }}
                        </div>
                    </div>
                </div>

                <!-- Status Sesi Pertemuan (14 / 21) -->
                @php
                    $terbukaCount = $jadwal->pertemuans->where('is_open', true)->count();
                    $totalCount = $jadwal->pertemuans->count();
                @endphp
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: var(--text-muted)">
                    <span>Status Sesi Pertemuan: <strong>{{ $terbukaCount }} dari {{ $totalCount }} Sesi Terbuka</strong></span>
                    <span class="badge badge-success">Jadwal Aktif</span>
                </div>
            </div>
        @endforeach
    </div>
@endif

<!-- ==================== MODALS ==================== -->

<!-- Modal Tambah Mata Kuliah -->
<div id="add-mk-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Tambah Mata Kuliah</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">Masukkan data mata kuliah dan tentukan bobot SKS.</p>

        <form action="{{ route('admin.matakuliah.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Kode Mata Kuliah</label>
                <input type="text" name="kode_mk" class="form-control" placeholder="Contoh: 22SIF0350" required>
            </div>
            <div class="form-group">
                <label>Nama Mata Kuliah</label>
                <input type="text" name="nama_mk" class="form-control" placeholder="Contoh: KEAMANAN SISTEM INFORMASI" required>
            </div>
            <div class="form-group">
                <label>Bobot SKS</label>
                <select name="sks" class="form-control" onchange="updateTotalPertemuanDisplay(this.value, 'add_pertemuan_display')" required>
                    <option value="2">2 SKS (14 Pertemuan)</option>
                    <option value="3" selected>3 SKS (21 Pertemuan)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Total Pertemuan Perkuliahan (Otomatis)</label>
                <input type="text" id="add_pertemuan_display" class="form-control" value="21 Pertemuan" readonly style="background: #F1F5F9; font-weight: 800; color: #0284C7">
            </div>
            <div class="form-group">
                <label>Semester</label>
                <input type="number" name="semester" class="form-control" value="5" min="1" max="8" required>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('add-mk-modal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Mata Kuliah</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Mata Kuliah -->
<div id="edit-mk-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Edit Mata Kuliah</h3>
        <form id="edit-mk-form" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Kode Mata Kuliah</label>
                <input type="text" id="emk_kode" name="kode_mk" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Nama Mata Kuliah</label>
                <input type="text" id="emk_nama" name="nama_mk" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Bobot SKS</label>
                <select id="emk_sks" name="sks" class="form-control" onchange="updateTotalPertemuanDisplay(this.value, 'edit_pertemuan_display')" required>
                    <option value="2">2 SKS (14 Pertemuan)</option>
                    <option value="3">3 SKS (21 Pertemuan)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Total Pertemuan Perkuliahan (Otomatis)</label>
                <input type="text" id="edit_pertemuan_display" class="form-control" value="21 Pertemuan" readonly style="background: #F1F5F9; font-weight: 800; color: #0284C7">
            </div>
            <div class="form-group">
                <label>Semester</label>
                <input type="number" id="emk_semester" name="semester" class="form-control" min="1" max="8" required>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('edit-mk-modal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Plotting Jadwal -->
<div id="add-jadwal-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Plotting Jadwal & Dosen</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">Tugaskan dosen ke kelas dan atur jadwal perkuliahan.</p>

        <form action="{{ route('admin.jadwal.store') }}" method="POST">
            @csrf
            <input type="hidden" id="aj_mk_id" name="mata_kuliah_id">

            <div class="form-group">
                <label>Dosen Pengampu</label>
                <select id="aj_dosen_id" name="dosen_id" class="form-control" required>
                    @foreach($dosenList as $d)
                        <option value="{{ $d->id }}">{{ $d->nama_lengkap }} ({{ $d->nidn }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Pilih Kelas</label>
                <select name="kelas_id" class="form-control" required>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->prodi }} - {{ $k->mahasiswas_count }} Mahasiswa)</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px">
                <div class="form-group">
                    <label>Hari Perkuliahan</label>
                    <select name="hari" class="form-control" required>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Ruang Kelas</label>
                    <input type="text" name="ruang" class="form-control" value="V.401" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px">
                <div class="form-group">
                    <label>Jam Mulai</label>
                    <input type="time" name="jam_mulai" class="form-control" value="07:40" required>
                </div>
                <div class="form-group">
                    <label>Jam Selesai</label>
                    <input type="time" name="jam_selesai" class="form-control" value="09:20" required>
                </div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('add-jadwal-modal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Plotting</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Jadwal -->
<div id="edit-jadwal-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Edit Jadwal Perkuliahan</h3>
        <form id="edit-jadwal-form" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Dosen Pengampu</label>
                <select id="ej_dosen" name="dosen_id" class="form-control" required>
                    @foreach($dosenList as $d)
                        <option value="{{ $d->id }}">{{ $d->nama_lengkap }} ({{ $d->nidn }})</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px">
                <div class="form-group">
                    <label>Hari Perkuliahan</label>
                    <select id="ej_hari" name="hari" class="form-control" required>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Ruang Kelas</label>
                    <input type="text" id="ej_ruang" name="ruang" class="form-control" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px">
                <div class="form-group">
                    <label>Jam Mulai</label>
                    <input type="time" id="ej_mulai" name="jam_mulai" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Jam Selesai</label>
                    <input type="time" id="ej_selesai" name="jam_selesai" class="form-control" required>
                </div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('edit-jadwal-modal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Drag & Drop Import -->
<div id="drop-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box" style="max-width: 550px">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Drag & Drop Import Mata Kuliah</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px">Tarik dan jatuhkan file JSON atau CSV berisi daftar data mata kuliah.</p>

        <div id="drop-zone" style="border: 2px dashed var(--unpam-blue); border-radius: 14px; padding: 36px 20px; text-align: center; background: #F8FAFC; cursor: pointer; transition: background 0.15s">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--unpam-blue)" stroke-width="1.8" style="margin: 0 auto 10px; display: block"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <strong style="color: #1E293B; display: block; font-size: 14px">Tarik file ke sini atau klik untuk memilih file</strong>
            <small style="color: var(--text-muted); display: block; margin-top: 4px">Format: .json atau .csv (kolom: kode_mk, nama_mk, sks, semester)</small>
            <input type="file" id="drop-file-input" accept=".json,.csv" style="display: none">
        </div>

        <div id="drop-status" style="margin-top: 14px; font-size: 13px; display: none"></div>

        <div style="display: flex; justify-content: flex-end; margin-top: 20px">
            <button type="button" class="btn btn-outline" onclick="document.getElementById('drop-modal').style.display = 'none'">Tutup</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateTotalPertemuanDisplay(sks, displayElementId) {
        const el = document.getElementById(displayElementId);
        if (sks == 2) {
            el.value = "14 Pertemuan";
        } else {
            el.value = "21 Pertemuan";
        }
    }

    function openEditMkModal(id, kode, nama, sks, semester) {
        document.getElementById('edit-mk-form').action = "/admin/matakuliah/" + id;
        document.getElementById('emk_kode').value = kode;
        document.getElementById('emk_nama').value = nama;
        document.getElementById('emk_sks').value = sks;
        updateTotalPertemuanDisplay(sks, 'edit_pertemuan_display');
        document.getElementById('emk_semester').value = semester;
        document.getElementById('edit-mk-modal').style.display = 'grid';
    }

    function openAddJadwalForMk(mkId) {
        document.getElementById('aj_mk_id').value = mkId;
        document.getElementById('add-jadwal-modal').style.display = 'grid';
    }

    function openAddJadwalForMkDosen(mkId, dosenId) {
        document.getElementById('aj_mk_id').value = mkId;
        document.getElementById('aj_dosen_id').value = dosenId;
        document.getElementById('add-jadwal-modal').style.display = 'grid';
    }

    function openEditJadwalModal(id, dosenId, hari, mulai, selesai, ruang) {
        document.getElementById('edit-jadwal-form').action = "/admin/jadwal/" + id;
        document.getElementById('ej_dosen').value = dosenId;
        document.getElementById('ej_hari').value = hari;
        document.getElementById('ej_mulai').value = mulai;
        document.getElementById('ej_selesai').value = selesai;
        document.getElementById('ej_ruang').value = ruang;
        document.getElementById('edit-jadwal-modal').style.display = 'grid';
    }

    let activeEntity = 'matakuliah';
    function openDropModal(entity) {
        activeEntity = entity;
        document.getElementById('drop-status').style.display = 'none';
        document.getElementById('drop-modal').style.display = 'grid';
    }

    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('drop-file-input');

    dropZone.onclick = () => fileInput.click();
    dropZone.ondragover = (e) => { e.preventDefault(); dropZone.style.background = '#EBF5FF'; };
    dropZone.ondragleave = () => { dropZone.style.background = '#F8FAFC'; };
    dropZone.ondrop = (e) => {
        e.preventDefault();
        dropZone.style.background = '#F8FAFC';
        if (e.dataTransfer.files.length) handleFile(e.dataTransfer.files[0]);
    };
    fileInput.onchange = () => {
        if (fileInput.files.length) handleFile(fileInput.files[0]);
    };

    function handleFile(file) {
        const reader = new FileReader();
        reader.onload = async (e) => {
            let content = e.target.result;
            let payload = [];
            try {
                if (file.name.endsWith('.json')) {
                    payload = JSON.parse(content);
                } else {
                    let lines = content.split('\n').filter(l => l.trim().length);
                    let headers = lines[0].split(',').map(h => h.trim().toLowerCase());
                    for (let i = 1; i < lines.length; i++) {
                        let cols = lines[i].split(',').map(c => c.trim());
                        let obj = {};
                        headers.forEach((h, idx) => obj[h] = cols[idx]);
                        payload.push(obj);
                    }
                }

                const res = await fetch("/admin/import/" + activeEntity, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ data: payload })
                });
                const resData = await res.json();
                const st = document.getElementById('drop-status');
                st.style.display = 'block';
                st.className = res.ok ? 'alert alert-success' : 'alert alert-danger';
                st.textContent = resData.message;
                if (res.ok) setTimeout(() => location.reload(), 1500);
            } catch (err) {
                alert("Gagal membaca file: format file tidak sesuai.");
            }
        };
        reader.readAsText(file);
    }
</script>
@endpush
@endsection
