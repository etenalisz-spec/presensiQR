<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Sistem Presensi QR' }} - Universitas Pamulang</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>

    <style>
        :root {
            --unpam-blue: #0088EA;
            --unpam-dark-blue: #0066B3;
            --unpam-light-blue: #EBF5FF;
            --bg-body: #F4F7FB;
            --surface: #FFFFFF;
            --text-main: #1E293B;
            --text-muted: #64748B;
            --border-line: #E2E8F0;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Header (Sesuai Screenshot UNPAM) */
        header.unpam-header {
            background-color: var(--unpam-blue);
            color: #ffffff;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 2px 10px rgba(0, 136, 234, 0.2);
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .header-brand h1 {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .menu-toggle-btn {
            background: none;
            border: none;
            color: #ffffff;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
        }

        .user-top-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }

        .user-badge-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            text-decoration: none;
        }

        /* Layout Container */
        .app-layout {
            display: flex;
            flex: 1;
        }

        /* Sidebar */
        aside.app-sidebar {
            width: 250px;
            background: var(--surface);
            border-right: 1px solid var(--border-line);
            padding: 20px 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            color: var(--text-main);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.15s, color 0.15s;
        }

        .nav-item:hover {
            background: var(--unpam-light-blue);
            color: var(--unpam-blue);
        }

        .nav-item.active {
            background: var(--unpam-blue);
            color: #ffffff;
        }

        .nav-divider {
            height: 1px;
            background: var(--border-line);
            margin: 12px 6px;
        }

        /* Main Content */
        main.main-content {
            flex: 1;
            padding: 24px clamp(16px, 3vw, 36px);
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        /* UNPAM Blue Banner (Sesuai Screenshot) */
        .unpam-banner {
            background: linear-gradient(135deg, var(--unpam-blue), #00A3FF);
            color: #ffffff;
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 14px rgba(0, 136, 234, 0.18);
        }

        .unpam-banner h2 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .unpam-banner p {
            font-size: 13.5px;
            opacity: 0.9;
            margin-top: 4px;
        }

        /* Cards & Tables */
        .card {
            background: var(--surface);
            border: 1px solid var(--border-line);
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            padding: 22px;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 16px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table.unpam-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        table.unpam-table th {
            background: #F8FAFC;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-line);
            text-align: left;
        }

        table.unpam-table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-line);
            color: #334155;
            vertical-align: middle;
        }

        table.unpam-table tr:hover td {
            background-color: #F8FAFC;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 9px;
            font-size: 13.5px;
            font-weight: 600;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background-color: var(--unpam-blue);
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: var(--unpam-dark-blue);
        }

        .btn-outline {
            border-color: var(--border-line);
            background: #fff;
            color: var(--text-main);
        }

        .btn-outline:hover {
            background: var(--unpam-light-blue);
            border-color: var(--unpam-blue);
            color: var(--unpam-blue);
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12.5px;
            border-radius: 7px;
        }

        .btn-block {
            width: 100%;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-success { background: #E1F4EA; color: #0C7A52; }
        .badge-danger { background: #FDEBE9; color: #C2352B; }
        .badge-warning { background: #FFF2D9; color: #95600A; }
        .badge-info { background: var(--unpam-light-blue); color: var(--unpam-blue); }

        /* Floating Back Button (Persis Screenshot 2 pojok kanan bawah) */
        .float-back-btn {
            position: fixed;
            right: 24px;
            bottom: 24px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: #FBBF24;
            color: #ffffff;
            display: grid;
            place-items: center;
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4);
            border: none;
            cursor: pointer;
            text-decoration: none;
            z-index: 40;
            transition: transform 0.15s ease;
        }

        .float-back-btn:hover {
            transform: scale(1.08);
        }

        /* Modal */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            display: grid;
            place-items: center;
            z-index: 100;
            padding: 16px;
        }

        .modal-box {
            background: #fff;
            border-radius: 18px;
            padding: 24px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        /* Forms */
        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #334155;
        }

        .form-control {
            width: 100%;
            height: 42px;
            padding: 0 12px;
            border: 1px solid var(--border-line);
            border-radius: 9px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--unpam-blue);
            box-shadow: 0 0 0 3px rgba(0, 136, 234, 0.15);
        }

        textarea.form-control {
            height: auto;
            padding: 10px 12px;
        }

        /* Toast notifications */
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 16px;
        }
        .alert-success { background: #E1F4EA; color: #0C7A52; border: 1px solid #B8E4CD; }
        .alert-danger { background: #FDEBE9; color: #C2352B; border: 1px solid #F8C3BD; }

        @media (max-width: 768px) {
            aside.app-sidebar {
                display: none;
            }
            aside.app-sidebar.open {
                display: flex;
                position: fixed;
                top: 56px;
                left: 0;
                bottom: 0;
                z-index: 45;
                box-shadow: 10px 0 30px rgba(0,0,0,0.1);
            }
        }
    </style>
</head>
<body>

    <!-- Header UNPAM (Sesuai Screenshot 1 & 2) -->
    <header class="unpam-header">
        <div class="header-brand">
            <button class="menu-toggle-btn" onclick="document.querySelector('.app-sidebar').classList.toggle('open')">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
            </button>
            <h1>UNIVERSITAS PAMULANG</h1>
        </div>

        <div class="user-top-menu">
            @auth
                @if(Auth::user()->role === 'mahasiswa')
                    <a href="{{ route('mahasiswa.profil') }}" class="user-badge-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><circle cx="19" cy="8" r="2"/></svg>
                        <span>{{ Auth::user()->name }}</span>
                    </a>
                @elseif(Auth::user()->role === 'dosen')
                    <span class="user-badge-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span>{{ Auth::user()->dosen?->nama_lengkap ?? Auth::user()->name }}</span>
                    </span>
                @else
                    <span class="user-badge-btn">
                        <span>Admin Prodi</span>
                    </span>
                @endif
                <form action="{{ route('logout') }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="user-badge-btn" style="background: rgba(239, 68, 68, 0.3); border-color: rgba(239, 68, 68, 0.5)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </button>
                </form>
            @endauth
        </div>
    </header>

    <div class="app-layout">
        <!-- Sidebar Navigation -->
        @auth
        <aside class="app-sidebar">
            @if(Auth::user()->role === 'admin')
                <div style="font-size: 11px; font-weight: 800; color: var(--text-muted); padding: 0 12px; margin-bottom: 4px; text-transform: uppercase">Menu Admin</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                    Dashboard & Grafik
                </a>
                <a href="{{ route('admin.akun') }}" class="nav-item {{ request()->routeIs('admin.akun*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg>
                    Buat / Kelola Akun
                </a>
                <a href="{{ route('admin.kelas') }}" class="nav-item {{ request()->routeIs('admin.kelas') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V8l7-4 7 4v13M9 21v-6h6v6M9 11h.01M15 11h.01"/></svg>
                    Data Kelas
                </a>
                <a href="{{ route('admin.mahasiswa') }}" class="nav-item {{ request()->routeIs('admin.mahasiswa') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/></svg>
                    Data Mahasiswa
                </a>
                <a href="{{ route('admin.dosen') }}" class="nav-item {{ request()->routeIs('admin.dosen') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Data Dosen
                </a>
                <a href="{{ route('admin.matakuliah_jadwal') }}" class="nav-item {{ request()->routeIs('admin.matakuliah*') || request()->routeIs('admin.jadwal*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5z"/><path d="M4 19.5V21h16v-4"/><rect x="14" y="3" width="7" height="7" rx="1"/></svg>
                    Mata Kuliah & Jadwal
                </a>
            @elseif(Auth::user()->role === 'dosen')
                <div style="font-size: 11px; font-weight: 800; color: var(--text-muted); padding: 0 12px; margin-bottom: 4px; text-transform: uppercase">Menu Dosen</div>
                <a href="{{ route('dosen.dashboard') }}" class="nav-item {{ request()->routeIs('dosen.dashboard') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('dosen.matakuliah') }}" class="nav-item {{ request()->routeIs('dosen.matakuliah*') || request()->routeIs('dosen.kelas*') || request()->routeIs('dosen.pertemuan*') || request()->routeIs('dosen.tampilQr*') || request()->routeIs('dosen.presensi*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5z"/><path d="M4 19.5V21h16v-4"/></svg>
                    Mata Kuliah Diampu
                </a>
                <a href="{{ route('dosen.mhs.matakuliah') }}" class="nav-item {{ request()->routeIs('dosen.mhs.*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/></svg>
                    Data Mahasiswa
                </a>
                <a href="{{ route('dosen.rekap.matakuliah') }}" class="nav-item {{ request()->routeIs('dosen.rekap.*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    Rekap Presensi
                </a>
                <a href="{{ route('dosen.profil') }}" class="nav-item {{ request()->routeIs('dosen.profil') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Profil Saya
                </a>
            @else
                <div style="font-size: 11px; font-weight: 800; color: var(--text-muted); padding: 0 12px; margin-bottom: 4px; text-transform: uppercase">Menu Mahasiswa</div>
                <a href="{{ route('mahasiswa.dashboard') }}" class="nav-item {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('mahasiswa.presensi') }}" class="nav-item {{ request()->routeIs('mahasiswa.presensi*') || request()->routeIs('mahasiswa.pertemuan*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/><path d="m9 14 2 2 4-4"/></svg>
                    Presensi
                </a>
                <a href="{{ route('mahasiswa.profil') }}" class="nav-item {{ request()->routeIs('mahasiswa.profil') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Profil Saya
                </a>
            @endif

            <div class="nav-divider"></div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-item" style="width: 100%; border: none; background: none; color: var(--danger); text-align: left; cursor: pointer">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Keluar / Logout
                </button>
            </form>
        </aside>
        @endauth

        <main class="main-content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="padding-left: 18px">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
