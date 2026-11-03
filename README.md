# SPP-Ku — Sistem Peminjaman & Pengembalian Buku

Aplikasi perpustakaan digital untuk **Perpustakaan SMK Negeri 5 Surakarta**, dibangun dengan
**Laravel 11 + MySQL (XAMPP)**. Proyek Akhir Semester Gasal —
**Adiputra Arya Sena, XII RPL C (No. 2)**.

---

## 1. Yang harus ada di komputer

| Kebutuhan | Versi minimal | Catatan |
|---|---|---|
| XAMPP (Apache + MySQL) | PHP 8.2 | Untuk database dan phpMyAdmin |
| Composer | 2.x | Unduh di https://getcomposer.org |
| VS Code | — | Untuk membuka folder proyek |

Ekstensi PHP yang wajib aktif di `php.ini`: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`.
Di XAMPP biasanya sudah aktif. Bila `extension=fileinfo` masih diawali titik koma, hapus titik
koma itu lalu restart Apache.

---

## 2. Cara menjalankan

### Langkah 1 — Impor database

1. Nyalakan **Apache** dan **MySQL** di XAMPP Control Panel.
2. Buka **http://localhost/phpmyadmin**.
3. Klik tab **Import** di bagian atas.
4. **Choose File** → pilih `database/spp_ku.sql` dari folder proyek ini.
5. Klik **Import** (gulir ke bawah).

Database `spp_ku` beserta seluruh tabel dan data contoh akan dibuat otomatis —
tidak perlu membuat database secara manual lebih dulu.

### Langkah 2 — Siapkan aplikasi

Buka folder `spp-ku` di VS Code, lalu buka terminal (**Ctrl + `**):

```bash
composer install
copy .env.example .env        ::  Mac/Linux: cp .env.example .env
php artisan key:generate
php artisan serve
```

Buka **http://localhost:8000**.

> `composer install` mengunduh folder `vendor/` dan **butuh internet satu kali saja**.
> Setelah itu aplikasi berjalan sepenuhnya offline — CSS dan ikon ditulis sendiri tanpa CDN.

### Akun bawaan

| Peran | NIS / Kode | Kata sandi |
|---|---|---|
| Petugas / Admin | `ADM001` | `admin123` |
| Siswa | `2024001` … `2024005` | `siswa123` *(sementara)* |

Saat pertama kali masuk, akun siswa langsung diarahkan ke halaman **"Buat kata sandi Anda
sendiri"** dan tidak bisa membuka halaman lain sampai kata sandinya diganti. Lihat bagian 4.

---

## 3. Isi database yang diimpor

| Tabel | Isi |
|---|---|
| `users` | 6 akun — 1 petugas, 5 siswa (hash bcrypt; kelima siswa ditandai wajib ganti kata sandi) |
| `kategoris` | 6 kategori koleksi |
| `bukus` | 20 judul buku dengan stok dan lokasi rak |
| `peminjamans` | 12 transaksi: 4 dipinjam, 3 pending, 1 ditolak, 3 selesai |
| `migrations` | Sudah terisi, sehingga Laravel tahu struktur tabel tidak perlu dibuat ulang |

Tanggal transaksi memakai `CURDATE()`, jadi data contoh selalu relevan dengan hari saat
berkas diimpor — ada yang sedang berjalan, ada yang mendekati jatuh tempo, dan satu
transaksi sengaja dibuat terlambat 5 hari agar kalkulasi denda Rp 10.000 langsung terlihat.

**Ingin mengulang dari data bersih?** Impor ulang `database/spp_ku.sql`; berkas itu menghapus
dan membuat ulang seluruh tabel. Alternatifnya jalankan `php artisan migrate:fresh --seed`,
yang menghasilkan data identik lewat seeder Laravel.

---

## 4. Keamanan akun siswa

Kata sandi bawaan `siswa123` sama untuk semua siswa dan tertulis di panduan ini, jadi selama
belum diganti, siapa pun yang tahu NIS bisa masuk. Sistem menutup celah itu dengan alur berikut:

1. **Wajib ganti saat pertama masuk.** Kolom `users.harus_ganti_password` menandai akun yang
   masih memakai kata sandi sementara. Middleware `PaksaGantiPassword` mengunci seluruh halaman
   dan mengarahkan ke `/ganti-password` sampai siswa menetapkan kata sandinya sendiri.
2. **Aturan kata sandi.** Minimal 8 karakter, memuat huruf dan angka, serta ditolak bila sama
   dengan NIS, potongan nama sendiri, kata sandi sementara, atau kata sandi umum. Aturannya ada
   di satu tempat (`app/Support/AturanKataSandi.php`) sehingga halaman wajib-ganti dan menu
   profil tidak pernah berbeda standar.
3. **Tersimpan terenkripsi.** Kata sandi disimpan sebagai hash bcrypt satu arah. Petugas, guru,
   maupun siapa pun yang membuka phpMyAdmin tidak bisa membaca kata sandi asli siswa.
4. **Sesi lama diputus.** Setelah kata sandi diganti, session ID diperbarui sehingga perangkat
   lain yang masih memakai kata sandi lama ikut terlempar keluar.
5. **Reset lewat petugas tetap aman.** Bila siswa lupa, petugas hanya bisa memberi kata sandi
   *sementara* yang baru — akun otomatis ditandai wajib-ganti lagi, sehingga petugas tidak bisa
   diam-diam memakai akun siswa tanpa ketahuan pemiliknya.

Status tiap akun terlihat di menu **Pengguna** milik petugas: badge *Sementara* berarti belum
diganti sendiri, badge *Pribadi* berarti sudah, lengkap dengan tanggal penggantiannya.

> Akun petugas `ADM001` sengaja tidak diwajibkan mengganti, agar Anda bisa langsung menguji
> sisi admin. Untuk pemakaian sungguhan, ubah `harus_ganti_password` menjadi `1` pada baris
> admin di `database/spp_ku.sql`.

---

## 5. Struktur menu

**Siswa** — Beranda (ringkasan pinjaman + rekomendasi) · Katalog (pencarian + filter kategori) ·
Detail buku dan formulir pengajuan · Riwayat transaksi (tab status, perpanjang, cetak bukti) · Profil.

**Petugas** — Dashboard statistik · Data buku · Kategori · Pengguna ·
Approval peminjaman · Pengembalian & denda · Laporan (cetak PDF / export Excel).

---

## 6. Pemenuhan kebutuhan fungsional

| Kode | Kebutuhan | Diterapkan pada |
|---|---|---|
| FR-01 | Login terenkripsi, logout, ubah kata sandi | `AuthController`, `ProfilController`, `KataSandiController`, middleware `PaksaGantiPassword`, cast `hashed` pada model `User` |
| FR-02 | CRUD buku, kategori, pengguna | `Admin\BukuController`, `KategoriController`, `PenggunaController` |
| FR-03 | Pencarian katalog & formulir pengajuan | `Siswa\KatalogController`, `Siswa\PeminjamanController` |
| FR-04 | Approve / reject disertai catatan | `Admin\ApprovalController` |
| FR-05 | Statistik, denda otomatis, cetak & export | `Admin\DashboardController`, `PengembalianController`, `LaporanController` |

**Masalah data ganda** yang disebut di dokumen analisis diselesaikan di tingkat basis data:
kolom `bukus.nomor_buku` dan `users.kode` diberi indeks `UNIQUE`, sehingga nomor yang sama
ditolak MySQL sebelum tersimpan — bukan sekadar dicegah lewat tampilan. Perubahan stok saat
approval dan pengembalian dibungkus `DB::transaction` agar jumlah eksemplar tidak pernah melenceng.

**Kebutuhan non-fungsional**: middleware `role` untuk otorisasi multi-peran, validasi server pada
seluruh formulir, tampilan responsif sampai lebar layar ponsel, dan CSS tanpa CDN demi kecepatan
muat di jaringan lokal sekolah.

---

## 7. Aturan perpustakaan yang bisa diubah

Semua lewat `.env`, tanpa menyentuh kode:

```
SPPKU_DENDA_PER_HARI=2000     # denda keterlambatan per hari
SPPKU_DURASI_PINJAM=7         # lama pinjam standar (hari)
```

Nilai lain (lama dan batas perpanjangan, batas buku aktif per siswa) ada di `config/sppku.php`.

---

## 8. Peta folder

```
app/Http/Controllers/     Admin/ dan Siswa/ dipisah sesuai aktor
app/Models/               User, Kategori, Buku, Peminjaman
app/Http/Middleware/      RoleMiddleware (otorisasi peran) & PaksaGantiPassword
app/Support/              AturanKataSandi — aturan kata sandi terpusat
config/sppku.php          Aturan denda & durasi peminjaman
database/spp_ku.sql       >>> BERKAS UNTUK DIIMPOR KE PHPMYADMIN <<<
database/migrations/      Struktur tabel versi Laravel
database/seeders/         Data contoh versi Laravel
resources/views/          siswa/, admin/, cetak/, layouts/, partials/
public/css/app.css        Style guide (Navy #1E3A8A, Inter, radius 8/12px)
routes/web.php            Seluruh rute aplikasi
```

---

## 9. Bila ada kendala

| Pesan | Penyebab & solusi |
|---|---|
| `Could not open input file: artisan` | Terminal belum di folder `spp-ku`. Jalankan `cd spp-ku`. |
| `No application encryption key has been specified` | Belum menjalankan `php artisan key:generate`. |
| `SQLSTATE[HY000] [1049] Unknown database 'spp_ku'` | Berkas `database/spp_ku.sql` belum diimpor. |
| `SQLSTATE[HY000] [2002]` | MySQL di XAMPP belum dinyalakan. |
| Impor phpMyAdmin gagal karena ukuran | Naikkan `upload_max_filesize` di `php.ini`; berkas ini hanya ±40 KB jadi jarang terjadi. |
| Login selalu ditolak | Pastikan `BCRYPT_ROUNDS=10` di `.env` lalu jalankan `php artisan config:clear`. |
| Selalu kembali ke halaman ganti kata sandi | Memang disengaja: selesaikan dulu penggantian kata sandi. Untuk melewatinya saat menguji, set `harus_ganti_password = 0` pada baris pengguna di phpMyAdmin. |
| Siswa lupa kata sandinya | Petugas membuka menu **Pengguna** → **Ubah** → isi kata sandi sementara baru. Siswa akan diminta membuat kata sandi pribadi lagi saat masuk. |
| `failed to open stream: Permission denied` | Beri izin tulis pada folder `storage/` dan `bootstrap/cache/`. |
| Tampilan polos tanpa warna | Akses lewat `php artisan serve`, bukan membuka berkas `.blade.php` langsung. |

Setelah mengubah `.env`, jalankan `php artisan config:clear`.

---

## 6. Pembaruan terbaru

**Tampilan responsif.** Seluruh halaman menyesuaikan ponsel, tablet, laptop, hingga layar lebar.
Di ponsel, menu petugas menjadi menu geser (tombol ☰), dan tabel berubah menjadi kartu sehingga
tidak perlu digeser ke samping.

**Web terbuka untuk umum.** Alamat utama (`/`) kini menampilkan beranda publik, dan katalog serta
detail buku bisa dilihat tanpa login. Login baru diminta saat menekan **"Masuk untuk meminjam"**;
setelah masuk, pengguna kembali ke buku yang dipilih.

**Jam operasional.** Tampil di beranda, footer semua halaman, halaman login, dan formulir pengajuan,
lengkap dengan status *Sedang buka / tutup*. Jadwal diubah di `config/sppku.php` pada kunci
`jam_operasional` (1 = Senin ... 7 = Minggu, `null` = libur). Bawaan: Senin–Kamis 07.00–15.00,
Jumat 07.00–11.30, Sabtu–Minggu tutup — **sesuaikan dengan jadwal sebenarnya.**

**Ambil buku dalam 24 jam.** Setelah mengajukan, siswa menerima pemberitahuan dan pengingat (di
formulir, riwayat, dan beranda) agar datang ke perpustakaan dalam 24 jam. Pengajuan yang belum
diambil lewat batas itu **ditolak otomatis** oleh sistem (catatan: "Ditolak otomatis ..."), tanpa
perlu cron. Batasnya bisa diubah lewat `SPPKU_BATAS_AMBIL_JAM` di `.env`. Pengajuan juga ditolak
di awal bila perpustakaan tidak buka sama sekali dalam 24 jam ke depan (mis. Jumat siang).

**Impor akun massal.** Menu **Pengguna → Impor massal**: unggah CSV atau tempel daftar dari Excel
(urutan: NIS, Nama, Kelas, Email, Telepon; hanya NIS dan Nama wajib). Contoh berkas tersedia di
tombol unduh. Semua akun dibuat sebagai siswa dengan kata sandi sementara yang sama dan wajib
diganti saat login pertama. NIS/email ganda otomatis dilewati dan dilaporkan per baris.
Maksimal 500 akun sekali impor.

**Profil.** Nama kini tidak bisa diubah sendiri (hanya petugas lewat menu Pengguna). Siswa dan
petugas bisa menambahkan **email** dan **nomor telepon** di menu Profil.

### Database
Impor `database/spp_ku.sql` (sudah memuat kolom `users.telepon` dan data terbaru Anda). Bila database
sudah terlanjur diimpor dan datanya ingin dipertahankan, cukup jalankan `database/tambah_telepon.sql`
sekali di phpMyAdmin, atau `php artisan migrate`.

### Tampilan seragam
Beranda kini **satu halaman yang sama** untuk tamu dan siswa (ucapan selamat datang, pencarian, jam
operasional, kategori, rekomendasi). Siswa yang sudah login hanya mendapat tambahan blok
"Pinjaman saya"; tamu tetap bisa menelusuri semuanya, tetapi meminjam mewajibkan login. Halaman
login dan ganti kata sandi memakai kerangka (menu atas + footer) yang sama dengan seluruh situs, dan
panel petugas memiliki tautan "Lihat situs perpustakaan". Alamat lama `/dashboard` otomatis
mengarah ke beranda.

---

## 7. Kode eksemplar & pengingat jatuh tempo

**Kode unik per fisik buku.** Satu judul kini punya rentang kode, mis. `BK-101-01` s.d. `BK-101-10`
(10 eksemplar). Kode bersifat **unik di seluruh perpustakaan** (dijaga juga oleh database), sehingga
satu kode tidak mungkin dipakai dua kali.

- **Judul baru:** di Master data buku → *Tambah buku baru*, isi *Dari kode* dan *Sampai kode*
  (mis. `RPL-008-001` s.d. `RPL-008-010`). Jumlah eksemplar dihitung otomatis; kode yang bentrok
  dengan buku mana pun ditolak dan tidak ada yang tersimpan.
- **Kelola kode:** tombol **Kode** pada tiap buku → tambah rentang kode, nonaktifkan (rusak/hilang),
  hapus kode yang belum pernah dipinjam, dan lihat siapa yang sedang memegang tiap kode.
- **Dipinjam dari kode terkecil:** saat petugas menyetujui pengajuan, sistem otomatis memilih kode
  tersedia yang terkecil (urut awalan, lalu angka) dan menampilkannya agar petugas menyerahkan
  buku yang tepat. Pada halaman persetujuan, kode berikutnya sudah terlihat sebelum disetujui.
- **Kode tampil di mana-mana:** riwayat dan beranda siswa, bukti peminjaman, kuitansi, halaman
  pengembalian (bisa dicari dengan kode eksemplar), laporan di layar, cetak, dan Excel/CSV.
- Katalog menampilkan rentang kode tiap judul.

**Data lama.** Buku yang sudah ada otomatis diberi kode `NOMOR-BUKU-01`, `-02`, dst. sebanyak
jumlah eksemplarnya, dan peminjaman yang sedang berjalan dikaitkan ke kode terkecil. Ubah ke
penomoran fisik Anda lewat tombol **Kode** (hapus kode otomatis yang belum dipakai, lalu tambahkan
rentang kode asli). Transaksi lama yang sudah selesai tidak punya kode (tampil "—").

**Pengingat jatuh tempo.** Mulai 2 hari sebelum batas pengembalian (atur `SPPKU_PENGINGAT_HARI`
di `.env`), siswa melihat spanduk di semua halaman: judul buku, kodenya, sisa hari, tombol
**Perpanjang** bila masih boleh, atau peringatan untuk mengembalikan. Bila batas jatuh pada hari
perpustakaan tutup, siswa diingatkan mengembalikan di hari buka sebelumnya. Buku yang terlambat
tampil merah beserta denda berjalan.

### Database
Impor `database/spp_ku.sql` terbaru (sudah memuat tabel `eksemplars` dan kode untuk 35 buku Anda),
**atau** bila database sudah ada dan ingin dipertahankan, jalankan `php artisan migrate` sekali —
kolom telepon, tabel kode eksemplar, dan pengisian kode otomatis dilakukan sekaligus.

### Tampilan ponsel
Di layar ponsel (≤ 700 px) situs memakai menu bawah ala aplikasi (Beranda, Katalog, Riwayat, Akun/Masuk),
bilah atas ringkas, hero dan kartu berujung membulat, angka ringkas tiga kolom, katalog dua kolom,
kategori yang bisa digeser, tabel menjadi kartu, dan jendela isian muncul sebagai lembar dari bawah.
Semua aturan ini khusus layar sempit di bagian akhir `public/css/app.css`; tampilan laptop/PC tidak berubah.
