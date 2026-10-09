@extends('layouts.app', ['title' => 'Data Kelas'])

@section('content')
<div class="unpam-banner">
    <h2>DATA KELAS PERKULIAHAN</h2>
    <p>Kelola Data Kelas Mahasiswa Universitas Pamulang</p>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px">
        <h3 class="card-title" style="margin: 0">Daftar Kelas</h3>
        <div style="display: flex; gap: 10px">
            <button type="button" class="btn btn-outline" onclick="openDropModal('kelas')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Drag & Drop Import
            </button>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('add-kelas-modal').style.display = 'grid'">
                + Tambah Kelas Baru
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="unpam-table">
            <thead>
                <tr>
                    <th>KODE KELAS</th>
                    <th>NAMA KELAS</th>
                    <th>PROGRAM STUDI</th>
                    <th>SEMESTER</th>
                    <th>TAHUN AJARAN</th>
                    <th>TOTAL MAHASISWA</th>
                    <th style="text-align: center; width: 140px">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kelasList as $k)
                <tr>
                    <td style="font-weight: 700; color: #0284C7">{{ $k->kode_kelas }}</td>
                    <td style="font-weight: 700">{{ $k->nama_kelas }}</td>
                    <td>{{ $k->prodi }}</td>
                    <td>Semester {{ $k->semester }}</td>
                    <td>{{ $k->tahun_ajaran }}</td>
                    <td>
                        <span class="badge badge-info">{{ $k->mahasiswas_count }} Mahasiswa</span>
                    </td>
                    <td style="text-align: center">
                        <button type="button" class="btn btn-outline btn-sm" onclick="openEditKelasModal({{ $k->id }}, '{{ addslashes($k->kode_kelas) }}', '{{ addslashes($k->nama_kelas) }}', '{{ addslashes($k->prodi) }}', {{ $k->semester }}, '{{ addslashes($k->tahun_ajaran) }}')">
                            Edit
                        </button>
                        <form action="{{ route('admin.kelas.delete', $k->id) }}" method="POST" style="display: inline" onsubmit="return confirm('Hapus kelas ini?')">
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

<!-- Modal Tambah Kelas -->
<div id="add-kelas-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Tambah Kelas Baru</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">Masukkan data kelas perkuliahan baru.</p>

        <form action="{{ route('admin.kelas.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Kode Kelas</label>
                <input type="text" name="kode_kelas" class="form-control" placeholder="Contoh: 05SIFE003" required>
            </div>
            <div class="form-group">
                <label>Nama Kelas</label>
                <input type="text" name="nama_kelas" class="form-control" placeholder="Contoh: 05SIFE003 / SI-5C" required>
            </div>
            <div class="form-group">
                <label>Program Studi</label>
                <input type="text" name="prodi" class="form-control" value="Sistem Informasi" required>
            </div>
            <div class="form-group">
                <label>Semester</label>
                <input type="number" name="semester" class="form-control" value="5" min="1" max="8" required>
            </div>
            <div class="form-group">
                <label>Tahun Ajaran</label>
                <input type="text" name="tahun_ajaran" class="form-control" value="GANJIL 2026/2027" required>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('add-kelas-modal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kelas</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kelas -->
<div id="edit-kelas-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Edit Data Kelas</h3>
        <form id="edit-kelas-form" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Kode Kelas</label>
                <input type="text" id="ek_kode" name="kode_kelas" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Nama Kelas</label>
                <input type="text" id="ek_nama" name="nama_kelas" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Program Studi</label>
                <input type="text" id="ek_prodi" name="prodi" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Semester</label>
                <input type="number" id="ek_semester" name="semester" class="form-control" min="1" max="8" required>
            </div>
            <div class="form-group">
                <label>Tahun Ajaran</label>
                <input type="text" id="ek_tahun" name="tahun_ajaran" class="form-control" required>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('edit-kelas-modal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Drag and Drop Import Batch -->
<div id="drop-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box" style="max-width: 550px">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Drag & Drop Import Data Kelas</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px">Tarik dan jatuhkan file JSON atau CSV berisi daftar data kelas.</p>

        <div id="drop-zone" style="border: 2px dashed var(--unpam-blue); border-radius: 14px; padding: 36px 20px; text-align: center; background: #F8FAFC; cursor: pointer; transition: background 0.15s">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--unpam-blue)" stroke-width="1.8" style="margin: 0 auto 10px; display: block"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <strong style="color: #1E293B; display: block; font-size: 14px">Tarik file ke sini atau klik untuk memilih file</strong>
            <small style="color: var(--text-muted); display: block; margin-top: 4px">Format yang didukung: .json, .csv</small>
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
    function openEditKelasModal(id, kode, nama, prodi, semester, tahun) {
        document.getElementById('edit-kelas-form').action = "/admin/kelas/" + id;
        document.getElementById('ek_kode').value = kode;
        document.getElementById('ek_nama').value = nama;
        document.getElementById('ek_prodi').value = prodi;
        document.getElementById('ek_semester').value = semester;
        document.getElementById('ek_tahun').value = tahun;
        document.getElementById('edit-kelas-modal').style.display = 'grid';
    }

    let activeEntity = 'kelas';
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
                    // Simple CSV parser
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
