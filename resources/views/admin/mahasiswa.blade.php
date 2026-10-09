@extends('layouts.app', ['title' => 'Data Mahasiswa'])

@section('content')
<div class="unpam-banner">
    <h2>PENGELOLAAN DATA MAHASISWA</h2>
    <p>Kelola Akun & Penempatan Mahasiswa Berdasarkan Kelas</p>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 20px">
        <form method="GET" style="display: flex; gap: 10px; align-items: center">
            <label for="filter_kelas" style="font-size: 13px; font-weight: 600">Filter Kelas:</label>
            <select name="kelas_id" id="filter_kelas" class="form-control" style="width: auto; min-width: 160px" onchange="this.form.submit()">
                <option value="">-- Semua Kelas --</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }} ({{ $k->prodi }})
                    </option>
                @endforeach
            </select>
        </form>

        <div style="display: flex; gap: 10px">
            <button type="button" class="btn btn-outline" onclick="openDropModal('mahasiswa')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Drag & Drop Import
            </button>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('add-mhs-modal').style.display = 'grid'">
                + Buat Akun Mahasiswa Baru
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="unpam-table">
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>NAMA LENGKAP</th>
                    <th>KELAS</th>
                    <th>NO HP / WHATSAPP</th>
                    <th>EMAIL</th>
                    <th>STATUS</th>
                    <th style="text-align: center; width: 140px">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mahasiswaList as $mhs)
                <tr>
                    <td style="font-weight: 700; color: #0284C7">{{ $mhs->nim }}</td>
                    <td style="font-weight: 600">{{ $mhs->nama_lengkap }}</td>
                    <td>
                        <span class="badge badge-info">{{ $mhs->kelas?->nama_kelas ?? 'Belum Ditentukan' }}</span>
                    </td>
                    <td>
                        @if(!empty($mhs->no_telp))
                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $mhs->no_telp)) }}" target="_blank" style="color: #059669; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px">
                                💬 {{ $mhs->no_telp }}
                            </a>
                        @else
                            <span style="color: var(--text-muted)">-</span>
                        @endif
                    </td>
                    <td>{{ $mhs->user?->email ?? ($mhs->email ?? '-') }}</td>
                    <td>
                        <span class="badge {{ $mhs->status === 'Aktif' ? 'badge-success' : 'badge-warning' }}">{{ $mhs->status }}</span>
                    </td>
                    <td style="text-align: center">
                        <button type="button" class="btn btn-outline btn-sm" onclick="openEditMhsModal({{ $mhs->id }}, '{{ addslashes($mhs->nama_lengkap) }}', {{ $mhs->kelas_id ?? 'null' }}, '{{ $mhs->status }}', '{{ addslashes($mhs->no_telp ?? '') }}', '{{ addslashes($mhs->user?->email ?? '') }}')">
                            Edit
                        </button>
                        <form action="{{ route('admin.mahasiswa.delete', $mhs->id) }}" method="POST" style="display: inline" onsubmit="return confirm('Hapus mahasiswa ini beserta akunnya?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm" style="background: #FDEBE9; color: #C2352B; border: 1px solid #F8C3BD">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: var(--text-muted)">
                        Tidak ada data mahasiswa.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px">
        {{ $mahasiswaList->links('pagination.custom') }}
    </div>
</div>

<!-- Modal Tambah Mahasiswa -->
<div id="add-mhs-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Tambah Akun Mahasiswa</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">Mahasiswa otomatis mendapatkan jadwal mata kuliah sesuai kelas yang dipilih.</p>

        <form action="{{ route('admin.mahasiswa.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>NIM (Nomor Induk Mahasiswa)</label>
                <input type="text" name="nim" class="form-control" placeholder="Contoh: 2211025" required>
            </div>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama lengkap mahasiswa" required>
            </div>
            <div class="form-group">
                <label>Penempatan Kelas</label>
                <select name="kelas_id" class="form-control" required>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->prodi }} - Semester {{ $k->semester }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>No. HP / WhatsApp Mahasiswa</label>
                <input type="text" name="no_telp" class="form-control" placeholder="Contoh: 081298765432">
            </div>
            <div class="form-group">
                <label>Email Mahasiswa (Opsional)</label>
                <input type="email" name="email" class="form-control" placeholder="default: nim@mhs.unpam.ac.id">
            </div>
            <div class="form-group">
                <label>Password Awal</label>
                <input type="password" name="password" class="form-control" value="mhs12345" required>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('add-mhs-modal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Mahasiswa</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Mahasiswa -->
<div id="edit-mhs-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Edit Data Mahasiswa</h3>
        <form id="edit-mhs-form" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" id="edit_nama" name="nama_lengkap" class="form-control" required>
            </div>
            <div class="form-group">
                <label>No. HP / WhatsApp</label>
                <input type="text" id="edit_no_telp" name="no_telp" class="form-control" placeholder="Contoh: 081298765432">
            </div>
            <div class="form-group">
                <label>Email Mahasiswa</label>
                <input type="email" id="edit_email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <label>Pindah Kelas</label>
                <select id="edit_kelas" name="kelas_id" class="form-control" required>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->prodi }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Status Akademik</label>
                <select id="edit_status" name="status" class="form-control" required>
                    <option value="Aktif">Aktif</option>
                    <option value="Cuti">Cuti</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('edit-mhs-modal').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Drag and Drop Import Batch -->
<div id="drop-modal" class="modal-backdrop" style="display: none">
    <div class="modal-box" style="max-width: 550px">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Drag & Drop Import Data Mahasiswa</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px">Tarik dan jatuhkan file JSON atau CSV berisi daftar data mahasiswa (kolom: nim, nama_lengkap, kelas, email, no_telp).</p>

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
    function openEditMhsModal(id, nama, kelasId, status, noTelp, email) {
        document.getElementById('edit-mhs-form').action = "/admin/mahasiswa/" + id;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_no_telp').value = noTelp || '';
        document.getElementById('edit_email').value = email || '';
        document.getElementById('edit_kelas').value = kelasId;
        document.getElementById('edit_status').value = status;
        document.getElementById('edit-mhs-modal').style.display = 'grid';
    }

    let activeEntity = 'mahasiswa';
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
