@extends('layouts.app', ['title' => 'Data Dosen'])

@section('content')
<div class="unpam-banner">
    <h2>DATA DOSEN PENGAJAR</h2>
    <p>Kelola Akun & Penugasan Dosen Universitas Pamulang &bull; Mata Kuliah yang Diampu</p>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px">
        <h3 class="card-title" style="margin: 0">Daftar Dosen</h3>
        <div style="display: flex; gap: 10px">
            <button type="button" class="btn btn-outline" onclick="openDropModal('dosen')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Drag & Drop Import
            </button>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('add-dosen-modal').style.display = 'grid'">
                + Tambah Akun Dosen Baru
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="unpam-table">
            <thead>
                <tr>
                    <th>NIDN</th>
                    <th>NAMA LENGKAP & GELAR</th>
                    <th>EMAIL</th>
                    <th>NO TELEPON / WA</th>
                    <th>MATA KULIAH YANG DIAMPU</th>
                    <th style="text-align: center; width: 140px">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dosenList as $d)
                <tr>
                    <td style="font-weight: 700; color: #0284C7">{{ $d->nidn }}</td>
                    <td style="font-weight: 700">
                        {{ $d->nama_lengkap }}
                        @if($d->gelar && !str_contains($d->nama_lengkap, $d->gelar))
                            <span style="color: var(--text-muted); font-size: 12px">({{ $d->gelar }})</span>
                        @endif
                    </td>
                    <td>{{ $d->user->email }}</td>
                    <td>{{ $d->no_telp ?? '-' }}</td>
                    <td>
                        @php
                            $matkuls = $d->jadwalKuliahs->map(function($j) {
                                return [
                                    'id' => $j->mata_kuliah_id,
                                    'nama' => $j->mataKuliah->nama_mk,
                                    'sks' => $j->mataKuliah->sks,
                                    'ptm' => $j->mataKuliah->sks == 3 ? 21 : 14,
                                    'kelas' => $j->kelas->nama_kelas
                                ];
                            })->unique('id');
                        @endphp
                        @forelse($matkuls as $mk)
                            <span class="badge badge-info" style="margin: 2px 0; display: inline-block">
                                {{ $mk['nama'] }} ({{ $mk['sks'] }} SKS - {{ $mk['ptm'] }} Ptm &bull; {{ $mk['kelas'] }})
                            </span>
                        @empty
                            <span style="color: var(--text-muted); font-size: 12px">Belum ada mata kuliah yang diampu</span>
                        @endforelse
                    </td>
                    <td style="text-align: center">
                        <button type="button" class="btn btn-outline btn-sm" onclick="openEditDosenModal({{ $d->id }}, '{{ addslashes($d->nidn) }}', '{{ addslashes($d->nama_lengkap) }}', '{{ addslashes($d->gelar ?? '') }}', '{{ addslashes($d->no_telp ?? '') }}', {{ json_encode($d->jadwalKuliahs->pluck('mata_kuliah_id')->toArray()) }}, {{ $d->jadwalKuliahs->first()?->kelas_id ?? 'null' }})">
                            Edit
                        </button>
                        <form action="{{ route('admin.dosen.delete', $d->id) }}" method="POST" style="display: inline" onsubmit="return confirm('Hapus dosen ini beserta akunnya?')">
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

<!-- Modal Tambah Akun Dosen (PERSIS SCREENSHOT 1 + PILIHAN MATA KULIAH) -->
<div id="add-dosen-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box" style="max-width: 540px">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Tambah Akun Dosen</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">Masukkan NIDN dan data profil dosen.</p>

        <form action="{{ route('admin.dosen.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>NIDN</label>
                <input type="text" name="nidn" class="form-control" placeholder="Contoh: 0412058005" required>
            </div>
            <div class="form-group">
                <label>Nama Lengkap dan Gelar</label>
                <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: Gusmayeni, S.Kom., M.Kom." required>
            </div>
            <div class="form-group">
                <label>Gelar</label>
                <input type="text" name="gelar" class="form-control" placeholder="Contoh: M.Kom.">
            </div>
            <div class="form-group">
                <label>No. Telepon / WhatsApp</label>
                <input type="text" name="no_telp" class="form-control" placeholder="Contoh: 081234567890">
            </div>
            <div class="form-group">
                <label>Email (Opsional)</label>
                <input type="email" name="email" class="form-control" placeholder="default: nama@unpam.ac.id">
            </div>
            <div class="form-group">
                <label>Password Awal</label>
                <input type="password" name="password" class="form-control" value="dosen123" required>
            </div>

            <!-- Pilih Mata Kuliah yang Diampu -->
            <div class="form-group">
                <label>Pilih Mata Kuliah yang Diampu Dosen Ini</label>
                <div style="max-height: 140px; overflow-y: auto; border: 1.5px solid var(--border-line); border-radius: 10px; padding: 10px; background: #FAFBFD">
                    @foreach($mataKuliahList as $mk)
                        <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 13px; cursor: pointer">
                            <input type="checkbox" name="mata_kuliah_ids[]" value="{{ $mk->id }}">
                            <span><strong>{{ $mk->kode_mk }}</strong> &bull; {{ $mk->nama_mk }} ({{ $mk->sks }} SKS - {{ $mk->sks == 3 ? '21' : '14' }} Pertemuan)</span>
                        </label>
                    @endforeach
                </div>
                <small style="color: var(--text-muted)">Centang mata kuliah yang ditugaskan kepada dosen ini.</small>
            </div>

            <div class="form-group">
                <label>Plot ke Kelas</label>
                <select name="kelas_id" class="form-control">
                    <option value="">-- Pilih Kelas Perkuliahan --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->prodi }} - Semester {{ $k->semester }})</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('add-dosen-modal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Dosen</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Dosen -->
<div id="edit-dosen-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box" style="max-width: 540px">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Edit Data Dosen</h3>
        <form id="edit-dosen-form" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>NIDN</label>
                <input type="text" id="ed_nidn" name="nidn" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Nama Lengkap dan Gelar</label>
                <input type="text" id="ed_nama" name="nama_lengkap" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Gelar</label>
                <input type="text" id="ed_gelar" name="gelar" class="form-control">
            </div>
            <div class="form-group">
                <label>No. Telepon / WhatsApp</label>
                <input type="text" id="ed_telp" name="no_telp" class="form-control">
            </div>

            <!-- Perbarui Mata Kuliah yang Diampu -->
            <div class="form-group">
                <label>Pilih Mata Kuliah yang Diampu</label>
                <div id="edit_mk_checkboxes" style="max-height: 140px; overflow-y: auto; border: 1.5px solid var(--border-line); border-radius: 10px; padding: 10px; background: #FAFBFD">
                    @foreach($mataKuliahList as $mk)
                        <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 13px; cursor: pointer">
                            <input type="checkbox" name="mata_kuliah_ids[]" value="{{ $mk->id }}" class="edit-mk-chk" data-mkid="{{ $mk->id }}">
                            <span><strong>{{ $mk->kode_mk }}</strong> &bull; {{ $mk->nama_mk }} ({{ $mk->sks }} SKS - {{ $mk->sks == 3 ? '21' : '14' }} Pertemuan)</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label>Plot ke Kelas</label>
                <select id="ed_kelas" name="kelas_id" class="form-control">
                    <option value="">-- Tetap / Pilih Kelas --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->prodi }})</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('edit-dosen-modal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Drag and Drop Import Batch -->
<div id="drop-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box" style="max-width: 550px">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Drag & Drop Import Data Dosen</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px">Tarik dan jatuhkan file JSON atau CSV berisi daftar data dosen.</p>

        <div id="drop-zone" style="border: 2px dashed var(--unpam-blue); border-radius: 14px; padding: 36px 20px; text-align: center; background: #F8FAFC; cursor: pointer; transition: background 0.15s">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--unpam-blue)" stroke-width="1.8" style="margin: 0 auto 10px; display: block"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <strong style="color: #1E293B; display: block; font-size: 14px">Tarik file ke sini atau klik untuk memilih file</strong>
            <small style="color: var(--text-muted); display: block; margin-top: 4px">Format: .json atau .csv (kolom: nidn, nama_lengkap, gelar, no_telp)</small>
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
    function openEditDosenModal(id, nidn, nama, gelar, telp, assignedMks, kelasId) {
        document.getElementById('edit-dosen-form').action = "/admin/dosen/" + id;
        document.getElementById('ed_nidn').value = nidn;
        document.getElementById('ed_nama').value = nama;
        document.getElementById('ed_gelar').value = gelar;
        document.getElementById('ed_telp').value = telp;
        if (kelasId && document.getElementById('ed_kelas')) {
            document.getElementById('ed_kelas').value = kelasId;
        }

        // Checklist assigned mata kuliah
        const chks = document.querySelectorAll('.edit-mk-chk');
        chks.forEach(chk => {
            const mkId = parseInt(chk.getAttribute('data-mkid'));
            chk.checked = assignedMks && assignedMks.includes(mkId);
        });

        document.getElementById('edit-dosen-modal').style.display = 'grid';
    }

    let activeEntity = 'dosen';
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
