@extends('layouts.app', ['title' => 'Sistem Buat & Kelola Akun'])

@section('content')
<div class="unpam-banner">
    <h2>SISTEM BUAT & KELOLA AKUN PENGGUNA</h2>
    <p>Kelola Kredensial & Pembuatan Akun Baru untuk Dosen dan Mahasiswa Universitas Pamulang</p>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 20px">
        <!-- Filter Role -->
        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center">
            <span style="font-size: 13px; font-weight: 700; color: #475569">Filter Akun:</span>
            <a href="{{ route('admin.akun') }}" class="btn btn-sm {{ empty($roleFilter) ? 'btn-primary' : 'btn-outline' }}">Semua ({{ \App\Models\User::count() }})</a>
            <a href="{{ route('admin.akun', ['role' => 'dosen']) }}" class="btn btn-sm {{ $roleFilter === 'dosen' ? 'btn-primary' : 'btn-outline' }}">Dosen ({{ \App\Models\User::where('role', 'dosen')->count() }})</a>
            <a href="{{ route('admin.akun', ['role' => 'mahasiswa']) }}" class="btn btn-sm {{ $roleFilter === 'mahasiswa' ? 'btn-primary' : 'btn-outline' }}">Mahasiswa ({{ \App\Models\User::where('role', 'mahasiswa')->count() }})</a>
        </div>

        <!-- Tombol Buat Akun -->
        <div style="display: flex; gap: 10px; flex-wrap: wrap">
            <button type="button" class="btn btn-outline" style="border-color: #0284C7; color: #0284C7; font-weight: 700" onclick="document.getElementById('modal-buat-dosen').style.display = 'grid'">
                + Buat Akun Dosen
            </button>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('modal-buat-mahasiswa').style.display = 'grid'">
                + Buat Akun Mahasiswa
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="unpam-table">
            <thead>
                <tr>
                    <th>USERNAME (NIM / NIDN)</th>
                    <th>NAMA LENGKAP</th>
                    <th>PERAN / ROLE</th>
                    <th>INFORMASI / PENEMPATAN</th>
                    <th>EMAIL</th>
                    <th style="text-align: center; width: 170px">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usersList as $u)
                <tr>
                    <td style="font-weight: 700; color: #0284C7">{{ $u->username }}</td>
                    <td style="font-weight: 700">
                        {{ $u->name }}
                        @if($u->role === 'dosen' && $u->dosen?->gelar && !str_contains($u->name, $u->dosen->gelar))
                            <span style="font-size: 12px; color: var(--text-muted)">({{ $u->dosen->gelar }})</span>
                        @endif
                    </td>
                    <td>
                        @if($u->role === 'admin')
                            <span class="badge badge-warning" style="font-weight: 700">Admin Prodi</span>
                        @elseif($u->role === 'dosen')
                            <span class="badge badge-info" style="font-weight: 700">Dosen</span>
                        @else
                            <span class="badge badge-success" style="font-weight: 700">Mahasiswa</span>
                        @endif
                    </td>
                    <td>
                        @if($u->role === 'mahasiswa')
                            <span class="badge badge-info">{{ $u->mahasiswa?->kelas?->nama_kelas ?? 'Belum ada kelas' }}</span>
                            @if($u->mahasiswa?->no_telp)
                                <div style="font-size: 11px; color: #059669; font-weight: 600; margin-top: 4px">
                                    💬 {{ $u->mahasiswa->no_telp }}
                                </div>
                            @endif
                        @elseif($u->role === 'dosen')
                            @php
                                $mks = $u->dosen && $u->dosen->jadwalKuliahs ? $u->dosen->jadwalKuliahs->pluck('mataKuliah.nama_mk')->filter()->unique() : collect();
                            @endphp
                            @if($mks->count() > 0)
                                <small style="color: #334155; font-weight: 600">{{ $mks->first() }}</small>
                                @if($mks->count() > 1)
                                    <span class="badge badge-secondary">+{{ $mks->count() - 1 }} MK lain</span>
                                @endif
                            @else
                                <span style="font-size: 12px; color: var(--text-muted)">Belum ada MK diplot</span>
                            @endif
                            @if($u->dosen?->no_telp)
                                <div style="font-size: 11px; color: #059669; font-weight: 600; margin-top: 4px">
                                    💬 {{ $u->dosen->no_telp }}
                                </div>
                            @endif
                        @else
                            <span style="font-size: 12px; color: var(--text-muted)">Administrator Sistem</span>
                        @endif
                    </td>
                    <td>{{ $u->email }}</td>
                    <td style="text-align: center">
                        <button type="button" class="btn btn-outline btn-sm" onclick="openResetModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->username) }}')" title="Reset Password">
                            Reset Pass
                        </button>
                        @if($u->id !== Auth::id())
                        <form action="{{ route('admin.akun.delete', $u->id) }}" method="POST" style="display: inline" onsubmit="return confirm('Hapus akun {{ addslashes($u->name) }} ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm" style="background: #FDEBE9; color: #C2352B; border: 1px solid #F8C3BD">
                                Hapus
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px">
                        Tidak ada akun ditemukan untuk filter ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px">
        {{ $usersList->withQueryString()->links('pagination.custom') }}
    </div>
</div>

<!-- Modal 1: Buat Akun Dosen Baru -->
<div id="modal-buat-dosen" class="modal-backdrop" style="display: none">
    <div class="modal-box" style="max-width: 550px">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Buat Akun Dosen Baru</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">Data akun dosen akan otomatis terdaftar dan dapat digunakan untuk login.</p>

        <form action="{{ route('admin.dosen.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>NIDN (Username Login Dosen)</label>
                <input type="text" name="nidn" class="form-control" placeholder="Contoh: 0412058005" required>
            </div>
            <div class="form-group">
                <label>Nama Lengkap dan Gelar</label>
                <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: Gusmayeni, S.Kom., M.Kom." required>
            </div>
            <div class="form-group">
                <label>Gelar Khusus</label>
                <input type="text" name="gelar" class="form-control" placeholder="Contoh: M.Kom.">
            </div>
            <div class="form-group">
                <label>Password Akun Dosen</label>
                <input type="password" name="password" class="form-control" value="dosen123" required>
            </div>
            <div class="form-group">
                <label>No. Telepon / WhatsApp</label>
                <input type="text" name="no_telp" class="form-control" placeholder="Contoh: 081234567890">
            </div>
            <div class="form-group">
                <label>Email (Opsional)</label>
                <input type="email" name="email" class="form-control" placeholder="default: nama@unpam.ac.id">
            </div>

            <!-- Pilih Mata Kuliah yang Diampu Dosen -->
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
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-buat-dosen').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary">Buat Akun Dosen</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Buat Akun Mahasiswa Baru -->
<div id="modal-buat-mahasiswa" class="modal-backdrop" style="display: none">
    <div class="modal-box" style="max-width: 520px">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Buat Akun Mahasiswa Baru</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">Data akun mahasiswa akan otomatis dikelompokkan ke kelas yang dipilih.</p>

        <form action="{{ route('admin.mahasiswa.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>NIM (Username Login Mahasiswa)</label>
                <input type="text" name="nim" class="form-control" placeholder="Contoh: 2211012" required>
            </div>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: Rian Hidayat" required>
            </div>
            <div class="form-group">
                <label>Pilih Kelas Mahasiswa</label>
                <select name="kelas_id" class="form-control" required>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->prodi }} - Semester {{ $k->semester }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Password Akun</label>
                <input type="password" name="password" class="form-control" value="mhs12345" required>
            </div>
            <div class="form-group">
                <label>No. Telepon / WhatsApp (Opsional)</label>
                <input type="text" name="no_telp" class="form-control" placeholder="Contoh: 081298765432">
            </div>
            <div class="form-group">
                <label>Email (Opsional)</label>
                <input type="email" name="email" class="form-control" placeholder="default: nim@mhs.unpam.ac.id">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-buat-mahasiswa').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary">Buat Akun Mahasiswa</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Reset Password Akun -->
<div id="modal-reset-pass" class="modal-backdrop" style="display: none">
    <div class="modal-box" style="max-width: 440px">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px">Reset Password Akun</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px">
            Atur password baru untuk akun <strong id="reset_user_label" style="color: #0284C7"></strong>.
        </p>

        <form id="form-reset-pass" method="POST">
            @csrf
            <div class="form-group">
                <label>Password Baru</label>
                <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-reset-pass').style.display = 'none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openResetModal(userId, userName, username) {
        document.getElementById('form-reset-pass').action = "/admin/akun/reset-password/" + userId;
        document.getElementById('reset_user_label').textContent = userName + ' (' + username + ')';
        document.getElementById('modal-reset-pass').style.display = 'grid';
    }
</script>
@endpush
@endsection
