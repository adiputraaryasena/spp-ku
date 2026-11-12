<?php

use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\Admin\EksemplarController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\PengembalianController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\KataSandiController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboard;
use App\Http\Controllers\Siswa\KatalogController;
use App\Http\Controllers\Siswa\PeminjamanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute SPP-Ku
|--------------------------------------------------------------------------
| Tamu   -> / (beranda publik), /katalog, /katalog/{buku}, /login
| Siswa  -> /dashboard, /riwayat, pengajuan peminjaman (wajib login)
| Admin  -> /admin/*
*/

// ==========================================
// 1. RUTE PUBLIK (TAMU / BELUM LOGIN)
// ==========================================
Route::get('/', [BerandaController::class, 'index'])->name('home');
Route::get('/katalog', [KatalogController::class, 'index'])->name('siswa.katalog');
Route::get('/katalog/{buku}', [KatalogController::class, 'show'])->name('siswa.buku')->whereNumber('buku');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
});

// ==========================================
// 2. RUTE UNTUK SEMUA YANG SUDAH LOGIN (SISWA & ADMIN)
// ==========================================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/beranda', [AuthController::class, 'beranda'])->name('beranda');

    // Wajib ganti kata sandi bawaan sebelum bisa memakai halaman lain
    Route::get('/ganti-password', [KataSandiController::class, 'form'])->name('password.wajib');
    Route::post('/ganti-password', [KataSandiController::class, 'simpan'])->name('password.wajib.simpan');

    // Tombol "Masuk untuk meminjam": tamu diminta login dulu, lalu kembali ke halaman buku.
    Route::get('/katalog/{buku}/pinjam', [KatalogController::class, 'pinjam'])->name('siswa.pinjam')->whereNumber('buku');

    Route::get('/profil', [ProfilController::class, 'edit'])->name('profil');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [ProfilController::class, 'ubahPassword'])->name('profil.password');
});

// ==========================================
// 3. RUTE KHUSUS SISWA
// ==========================================
Route::middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('/dashboard', [SiswaDashboard::class, 'index'])->name('siswa.dashboard');

    Route::get('/riwayat', [PeminjamanController::class, 'index'])->name('siswa.riwayat');
    Route::post('/katalog/{buku}/ajukan', [PeminjamanController::class, 'store'])->name('siswa.ajukan');
    Route::post('/riwayat/{peminjaman}/perpanjang', [PeminjamanController::class, 'perpanjang'])->name('siswa.perpanjang');
    Route::get('/riwayat/{peminjaman}/cetak', [PeminjamanController::class, 'cetak'])->name('siswa.cetak');
});

// ==========================================
// 4. RUTE KHUSUS ADMIN / PETUGAS
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');

    // Master data Buku
    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
    Route::post('/buku', [BukuController::class, 'store'])->name('buku.store');
    Route::put('/buku/{buku}', [BukuController::class, 'update'])->name('buku.update');
    Route::delete('/buku/{buku}', [BukuController::class, 'destroy'])->name('buku.destroy');

    // Kode eksemplar per judul
    Route::get('/buku/{buku}/eksemplar', [EksemplarController::class, 'index'])->name('buku.eksemplar');
    Route::post('/buku/{buku}/eksemplar', [EksemplarController::class, 'store'])->name('buku.eksemplar.store');
    Route::get('/buku/{buku}/barcode', [EksemplarController::class, 'cetakBarcode'])->name('buku.barcode');
    Route::put('/eksemplar/{eksemplar}', [EksemplarController::class, 'update'])->name('eksemplar.update');
    Route::delete('/eksemplar/{eksemplar}', [EksemplarController::class, 'destroy'])->name('eksemplar.destroy');

    // Master data Kategori
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::put('/kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

    // Master data Pengguna
    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
    Route::get('/pengguna/template', [PenggunaController::class, 'template'])->name('pengguna.template');
    Route::post('/pengguna/impor', [PenggunaController::class, 'impor'])->name('pengguna.impor');
    Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
    Route::put('/pengguna/{pengguna}', [PenggunaController::class, 'update'])->name('pengguna.update');
    Route::delete('/pengguna/{pengguna}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');

    // Transaksi: Approval Peminjaman
    Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');
    Route::post('/approval/{peminjaman}/setujui', [ApprovalController::class, 'setujui'])->name('approval.setujui');
    Route::post('/approval/{peminjaman}/tolak', [ApprovalController::class, 'tolak'])->name('approval.tolak');

    // Transaksi: Pengembalian
    Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
    Route::post('/pengembalian/{peminjaman}', [PengembalianController::class, 'konfirmasi'])->name('pengembalian.konfirmasi');
    Route::get('/pengembalian/{peminjaman}/kuitansi', [PengembalianController::class, 'kuitansi'])->name('pengembalian.kuitansi');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
    Route::get('/laporan/excel', [LaporanController::class, 'excel'])->name('laporan.excel');
});