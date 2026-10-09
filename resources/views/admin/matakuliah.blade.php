@extends('layouts.app', ['title' => 'Data Mata Kuliah'])

@section('content')
<div class="unpam-banner">
    <h2>MASTER DATA MATA KULIAH</h2>
    <p>Kelola Daftar Mata Kuliah Program Studi Sistem Informasi &bull; Bobot SKS & Pertemuan</p>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px">
        <h3 class="card-title" style="margin: 0">Daftar Mata Kuliah</h3>
        <div style="display: flex; gap: 10px">
            <button type="button" class="btn btn-outline" onclick="openDropModal('matakuliah')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Drag & Drop Import
            </button>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('add-mk-modal').style.display = 'grid'">
                + Tambah Mata Kuliah Baru
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="unpam-table">
            <thead>
                <tr>
                    <th>KODE MK</th>
                    <th>NAMA MATA KULIAH</th>
                    <th>BOBOT SKS</th>
                    <th>TOTAL PERTEMUAN</th>
                    <th>SEMESTER</th>
                    <th>DOSEN PENGAMPU</th>
                    <th style="text-align: center; width: 140px">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mataKuliahList as $mk)
                <tr>
                    <td style="font-weight: 700; color: #0284C7">{{ $mk->kode_mk }}</td>
                    <td style="font-weight: 700">{{ $mk->nama_mk }}</td>
                    <td>
                        <span class="badge {{ $mk->sks == 3 ? 'badge-primary' : 'badge-info' }}">{{ $mk->sks }} SKS</span>
                    </td>
                    <td>
                        <strong style="color: #0C7A52">{{ $mk->sks == 3 ? '21' : '14' }} Pertemuan</strong>
                    </td>
                    <td>Semester {{ $mk->semester }}</td>
                    <td>
                        @php
                            $dosens = $mk->jadwalKuliahs->pluck('dosen.nama_lengkap')->unique();
                        @endphp
                        @forelse($dosens as $dn)
                            <span class="badge badge-warning" style="margin: 2px 0">{{ $dn }}</span>
                        @empty
                            <span style="color: var(--text-muted); font-size: 12px">Belum diplot</span>
                        @endforelse
                    </td>
                    <td style="text-align: center">
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
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Mata Kuliah (Sesuai Screenshot 2 & Aturan SKS) -->
<div id="add-mk-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Tambah Mata Kuliah</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">Masukkan kode dan nama mata kuliah baru.</p>

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

            <!-- Pilihan SKS (Hanya 2 SKS dan 3 SKS) -->
            <div class="form-group">
                <label>Bobot SKS</label>
                <select name="sks" id="add_sks_select" class="form-control" onchange="updateTotalPertemuanDisplay(this.value, 'add_pertemuan_display')" required>
                    <option value="2">2 SKS (14 Pertemuan)</option>
                    <option value="3" selected>3 SKS (21 Pertemuan)</option>
                </select>
            </div>

            <!-- Total Pertemuan Otomatis Muncul di Bawahnya -->
            <div class="form-group">
                <label>Total Pertemuan Perkuliahan (Otomatis)</label>
                <input type="text" id="add_pertemuan_display" class="form-control" value="21 Pertemuan" readonly style="background: #F1F5F9; font-weight: 800; color: #0284C7">
            </div>

            <div class="form-group">
                <label>Semester</label>
                <input type="number" name="semester" class="form-control" value="5" min="1" max="8" required>
            </div>

            <!-- Opsi Penugasan Dosen & Kelas Otomatis -->
            <div class="form-group">
                <label>Tugaskan ke Dosen Pengampu (Opsional)</label>
                <select name="dosen_id" class="form-control">
                    <option value="">-- Pilih Dosen Pengampu (Opsional) --</option>
                    @foreach($dosenList as $d)
                        <option value="{{ $d->id }}">{{ $d->nama_lengkap }} ({{ $d->nidn }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Plot ke Kelas (Opsional)</label>
                <select name="kelas_id" class="form-control">
                    <option value="">-- Pilih Kelas (Opsional) --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->prodi }})</option>
                    @endforeach
                </select>
                <small style="color: var(--text-muted)">Jika dosen & kelas dipilih, jadwal & sesi pertemuan akan otomatis dibuat dan tersimpan ke akun dosen.</small>
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

            <div class="form-group">
                <label>Perbarui Dosen Pengampu (Opsional)</label>
                <select name="dosen_id" class="form-control">
                    <option value="">-- Tetap / Jangan Ubah Dosen --</option>
                    @foreach($dosenList as $d)
                        <option value="{{ $d->id }}">{{ $d->nama_lengkap }} ({{ $d->nidn }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Plot ke Kelas (Opsional)</label>
                <select name="kelas_id" class="form-control">
                    <option value="">-- Tetap / Jangan Ubah Kelas --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->prodi }})</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('edit-mk-modal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Drag and Drop Import Batch -->
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

    function parseCSV(text) {
        if (text.charCodeAt(0) === 0xFEFF) text = text.slice(1);
        let firstLine = text.split(/\r\n|\n|\r/)[0] || '';
        let delimiter = ',';
        let semiCount = (firstLine.match(/;/g) || []).length;
        let commaCount = (firstLine.match(/,/g) || []).length;
        let tabCount = (firstLine.match(/\t/g) || []).length;
        if (semiCount > commaCount && semiCount > tabCount) delimiter = ';';
        else if (tabCount > commaCount && tabCount > semiCount) delimiter = '\t';

        let lines = text.split(/\r\n|\n|\r/).filter(l => l.trim().length > 0);
        if (lines.length < 2) return [];

        function splitRow(row) {
            let pattern = new RegExp(
                '(\\' + delimiter + '|\\r?\\n|\\r|^)' +
                '(?:"([^"]*(?:""[^"]*)*)"|([^"\\' + delimiter + '\\r\\n]*))',
                'gi'
            );
            let rowData = [];
            let match = null;
            while ((match = pattern.exec(row))) {
                let value = match[2] ? match[2].replace(/""/g, '"') : match[3];
                rowData.push((value || '').trim());
            }
            return rowData;
        }

        let rawHeaders = splitRow(lines[0]);
        let cleanHeaders = rawHeaders.map(h => 
            h.toLowerCase()
             .replace(/[\ufeff"\']/g, '')
             .replace(/\s+/g, '_')
             .replace(/[^a-z0-9_]/g, '')
             .trim()
        );

        let result = [];
        for (let i = 1; i < lines.length; i++) {
            let rowCols = splitRow(lines[i]);
            if (rowCols.length === 0 || rowCols.every(c => c === '')) continue;
            let obj = {};
            cleanHeaders.forEach((h, idx) => {
                if (h) obj[h] = rowCols[idx] !== undefined ? rowCols[idx] : '';
            });
            result.push(obj);
        }
        return result;
    }

    function handleFile(file) {
        const st = document.getElementById('drop-status');
        st.style.display = 'block';
        st.className = 'alert alert-info';
        st.textContent = 'Membaca dan memproses file, mohon tunggu sebentar...';

        const reader = new FileReader();
        reader.onload = async (e) => {
            let content = e.target.result;
            let payload = [];
            try {
                if (file.name.endsWith('.json')) {
                    payload = JSON.parse(content);
                } else {
                    payload = parseCSV(content);
                }

                if (!payload || payload.length === 0) {
                    st.className = 'alert alert-danger';
                    st.textContent = 'File kosong atau baris data tidak terbaca.';
                    return;
                }

                st.textContent = `Mengirim ${payload.length} data ke server...`;

                const res = await fetch("/admin/import/" + activeEntity, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ data: payload })
                });
                const resData = await res.json();
                st.className = res.ok ? 'alert alert-success' : 'alert alert-danger';
                st.textContent = resData.message;
                if (res.ok) setTimeout(() => location.reload(), 1500);
            } catch (err) {
                st.className = 'alert alert-danger';
                st.textContent = 'Gagal memproses file: ' + err.message;
            }
        };
        reader.readAsText(file);
    }
</script>
@endpush
@endsection
