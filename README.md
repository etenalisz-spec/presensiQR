# SISTEM PRESENSI MAHASISWA BERBASIS DYNAMIC QR CODE
### Universitas Pamulang (UNPAM) - Program Studi Sistem Informasi

Aplikasi Sistem Presensi Mahasiswa Berbasis Web dengan Dynamic QR Code interaktif, timer kedaluwarsa otomatis (5-20 menit), multi-role authentication (Admin, Dosen, Mahasiswa), scanner kamera terintegrasi, rekapitulasi grafik, dan arsitektur database MySQL.

---

## 📌 Fitur Utama

### 1. Panel Admin (Master Control & Akademik)
- **Dashboard Ringkasan Real-Time**: Statistik total mahasiswa, dosen, mata kuliah, kelas, dan aktivitas presensi hari ini.
- **Satu Pintu "Mata Kuliah & Jadwal"**: Alur hierarki interaktif: Pilih Mata Kuliah &rarr; Pilih Dosen &rarr; Kartu Rincian Kelas, Ruangan, Jam, Hari, SKS, dan Jumlah Mahasiswa.
- **Manajemen Akun Terpusat**: Admin membuatkan akun mahasiswa (NIM) dan dosen (NIDN).
- **CRUD Lengkap & Drag-and-Drop File Import**: Mendukung penambahan cepat data mahasiswa, dosen, mata kuliah, dan kelas.
- **Otomatisasi SKS & Pertemuan**:
  - Pilihan 2 SKS otomatis mengalokasikan 14 pertemuan.
  - Pilihan 3 SKS otomatis mengalokasikan 21 pertemuan.
- **Pengaturan Gelar Dosen**: Field spesifik untuk gelar depan dan gelar belakang dosen (contoh: *S.Kom., M.Kom.*).

### 2. Panel Dosen (Pengajar & Sesi Kelas)
- **Dashboard Dosen**: Menampilkan mata kuliah dan kelas yang diampu secara langsung.
- **Generate Dynamic QR Code**:
  - Pilihan durasi QR Code aktif (5 menit hingga 20 menit).
  - Countdown timer presisi (menit:detik) waktu nyata.
  - Tombol **"Langsung Akhiri QR Absen"** untuk menutup sesi lebih cepat.
  - Token QR Code terenkripsi acak dan kadaluarsa otomatis saat timer habis.
- **Pengelola Pertemuan**: Sesuai SKS (1 s/d 14 atau 1 s/d 21 pertemuan) dengan kontrol status sesi (Belum Dibuka, Berlangsung, Selesai).
- **Data Mahasiswa Per Kelas**: Akses cepat data mahasiswa per mata kuliah dan kelas (NIM, Nama, No HP, Email).
- **Rekap Presensi & Grafik Interaktif**:
  - Diagram lingkaran (Donut Chart) persentase kehadiran kelas.
  - Tabel kehadiran tiap mahasiswa lengkap dengan persentase hadir.
  - Modal **Detail Pertemuan**: Rincian status hadir/absen per pertemuan dari sesi 1 hingga akhir.
  - Tombol **Cetak / Export PDF** langsung dari browser.
- **Kelola Profil & Ganti Password**: Fitur keamanan mandiri bagi dosen.

### 3. Panel Mahasiswa (Presensi Mandiri)
- **Dashboard Mahasiswa**: Ringkasan mata kuliah aktif, jadwal hari ini, dan persentase kehadiran.
- **Live QR Scanner (Kamera Perangkat)**:
  - Mengakses kamera smartphone, laptop, maupun webcam secara native (`Html5-Qrcode`).
  - Validasi instan token QR: mencatat kehadiran mahasiswa, mencegah duplikasi scan, dan menolak scan di luar kelas atau setelah QR expired.
- **Riwayat Presensi**: Riwayat kehadiran lengkap per mata kuliah dan per pertemuan.

---

## 🛠️ Tech Stack & Persyaratan Sistem

- **Framework**: Laravel 11.x
- **Bahasa Pemrograman**: PHP 8.2+ (disarankan PHP 8.3 / 8.4)
- **Database**: MySQL 8.x / MariaDB 10.x (atau SQLite untuk demo lokal)
- **Frontend**: Blade Templating, Responsive Vanilla CSS (Tema Resmi UNPAM Biru-Kuning), Chart.js, FontAwesome, Html5-QRCode Scanner
- **Web Server**: Apache / Nginx / PHP Built-in Server

---

## 🚀 Panduan Instalasi Lokal (Localhost / Laragon / XAMPP)

1. **Clone atau Extract Repositori**:
   ```bash
   git clone <URL_REPOSITORY_ANDA>
   cd presensi-qr
   ```

2. **Install Dependensi Composer**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment (`.env`)**:
   Salin file konfigurasi:
   ```bash
   cp .env.example .env
   ```
   Pastikan konfigurasi MySQL di file `.env` sudah sesuai:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=presensi_qr
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

5. **Migrasi Database & Seeding Data Awal**:
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Buka browser di: [http://localhost:8000](http://localhost:8000)

---

## 👥 Akun Bawaan (Default Seeders)

| Role | Username / Identifier | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `password` | Administrator Prodi Sistem Informasi |
| **Dosen** | `gusmayeni` / `0401018501` | `password` | Dosen Pengampu (Gusmayeni, S.Kom., M.Kom.) |
| **Mahasiswa** | `221011400001` | `password` | Mahasiswa (Aulia Rahmawati - Kelas 05SIFE001) |

---

## 📤 Panduan Upload ke GitHub

1. Inisialisasi Git di folder proyek:
   ```bash
   git init
   git add .
   git commit -m "feat: initial commit sistem presensi qr unpam"
   ```

2. Hubungkan ke repositori GitHub Anda:
   ```bash
   git branch -M main
   git remote add origin https://github.com/<username-github-anda>/<nama-repo>.git
   git push -u origin main
   ```

---

## ☁️ Panduan Deploy ke Vercel

Proyek ini telah dilengkapi dengan konfigurasi serverless Vercel:
- `vercel.json` (routing request PHP & static assets)
- `api/index.php` (entry point serverless function)
- `.vercelignore`

### Langkah-langkah Deploy:
1. Pastikan seluruh kode sudah di-push ke GitHub Anda.
2. Login ke akun [Vercel](https://vercel.com).
3. Klik **"Add New Project"** &rarr; **"Import"** repositori GitHub Anda.
4. Pada menu **Environment Variables**, tambahkan variabel berikut:
   - `APP_NAME`: `Presensi QR UNPAM`
   - `APP_ENV`: `production`
   - `APP_KEY`: *(Salin nilai `APP_KEY` dari file `.env` lokal Anda)*
   - `APP_DEBUG`: `false`
   - `APP_URL`: *(Domain Vercel Anda, contoh `https://presensi-unpam.vercel.app`)*
   - `DB_CONNECTION`: `mysql`
   - `DB_HOST`: *(Host MySQL Cloud publik, contoh dari Aiven, PlanetScale, TiDB Cloud, atau Supabase)*
   - `DB_PORT`: `3306`
   - `DB_DATABASE`: *(Nama database cloud)*
   - `DB_USERNAME`: *(Username database)*
   - `DB_PASSWORD`: *(Password database)*
   > **Catatan**: Karena Vercel merupakan arsitektur serverless di cloud, database MySQL `127.0.0.1` (localhost Laragon) tidak bisa diakses dari internet. Gunakan database cloud gratis seperti [Aiven MySQL](https://aiven.io), [TiDB Cloud](https://tidbcloud.com), atau [Neon/Supabase].
5. Klik **"Deploy"**. Vercel akan otomatis membangun dan merilis aplikasi Anda secara online.

---

## 📄 Lisensi & Hak Cipta
Dikembangkan untuk Universitas Pamulang (UNPAM) - Mata Kuliah Manajemen Proyek Perangkat Lunak (MPPL).
