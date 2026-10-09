@extends('layouts.app', ['title' => 'Jadwal & Plotting'])

@section('content')
<div class="unpam-banner">
    <h2>JADWAL KULIAH & PLOTTING KELAS</h2>
    <p>Penugasan Mata Kuliah ke Kelas & Dosen Pengampu Serta Penjadwalan Ruang</p>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 20px">
        <div>
            <h3 class="card-title" style="margin: 0">Daftar Jadwal Perkuliahan</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px">
                Setiap jadwal yang dibuat otomatis mengenerate 14 sesi pertemuan perkuliahan.
            </p>
        </div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('add-jadwal-modal').style.display = 'grid'">
            + Buat Plotting Jadwal Baru
        </button>
    </div>

    <div class="table-responsive">
        <table class="unpam-table">
            <thead>
                <tr>
                    <th>MATA KULIAH</th>
                    <th>KELAS</th>
                    <th>DOSEN PENGAMPU</th>
                    <th>HARI & JAM</th>
                    <th>RUANG</th>
                    <th style="text-align: center; width: 140px">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jadwalList as $j)
                <tr>
                    <td style="font-weight: 700">
                        {{ $j->mataKuliah->nama_mk }}
                        <div style="font-size: 12px; color: #0284C7; font-weight: 600">{{ $j->mataKuliah->kode_mk }} ({{ $j->mataKuliah->sks }} SKS)</div>
                    </td>
                    <td>
                        <span class="badge badge-info">{{ $j->kelas->nama_kelas }}</span>
                    </td>
                    <td style="font-weight: 600">{{ $j->dosen->nama_lengkap }}</td>
                    <td>
                        {{ $j->hari }}, {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }} WIB
                    </td>
                    <td>
                        <span class="badge badge-warning">Ruang {{ $j->ruang }}</span>
                    </td>
                    <td style="text-align: center">
                        <button type="button" class="btn btn-outline btn-sm" onclick="openEditJadwalModal({{ $j->id }}, {{ $j->dosen_id }}, '{{ $j->hari }}', '{{ substr($j->jam_mulai, 0, 5) }}', '{{ substr($j->jam_selesai, 0, 5) }}', '{{ addslashes($j->ruang) }}')">
                            Edit
                        </button>
                        <form action="{{ route('admin.jadwal.delete', $j->id) }}" method="POST" style="display: inline" onsubmit="return confirm('Hapus jadwal ini beserta seluruh pertemuannya?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm" style="background: #FDEBE9; color: #C2352B; border: 1px solid #F8C3BD">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Jadwal & Plotting -->
<div id="add-jadwal-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Plotting Jadwal Mata Kuliah</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">Tentukan mata kuliah, kelas, dosen pengampu, dan jam kuliah.</p>

        <form action="{{ route('admin.jadwal.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Pilih Mata Kuliah</label>
                <select name="mata_kuliah_id" class="form-control" required>
                    @foreach($mataKuliahList as $mk)
                        <option value="{{ $mk->id }}">{{ $mk->kode_mk }} - {{ $mk->nama_mk }} ({{ $mk->sks }} SKS)</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Pilih Kelas</label>
                <select name="kelas_id" class="form-control" required>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->prodi }} - Semester {{ $k->semester }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Pilih Dosen Pengampu</label>
                <select name="dosen_id" class="form-control" required>
                    @foreach($dosenList as $d)
                        <option value="{{ $d->id }}">{{ $d->nama_lengkap }} ({{ $d->nidn }})</option>
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
                    <input type="text" name="ruang" class="form-control" placeholder="Contoh: V.401" value="V.401" required>
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
                <button type="submit" class="btn btn-primary">Simpan & Generate 14 Pertemuan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Jadwal -->
<div id="edit-jadwal-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Edit Jadwal Perkuliahan</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">Perbarui dosen pengampu, hari, jam, atau ruang perkuliahan.</p>

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

@push('scripts')
<script>
    function openEditJadwalModal(id, dosenId, hari, mulai, selesai, ruang) {
        document.getElementById('edit-jadwal-form').action = "/admin/jadwal/" + id;
        document.getElementById('ej_dosen').value = dosenId;
        document.getElementById('ej_hari').value = hari;
        document.getElementById('ej_mulai').value = mulai;
        document.getElementById('ej_selesai').value = selesai;
        document.getElementById('ej_ruang').value = ruang;
        document.getElementById('edit-jadwal-modal').style.display = 'grid';
    }
</script>
@endpush
@endsection
