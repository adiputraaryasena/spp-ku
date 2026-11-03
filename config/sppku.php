<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi Aturan Perpustakaan SPP-Ku
|--------------------------------------------------------------------------
| Nilai di sini dipakai oleh seluruh perhitungan transaksi. Ubah di .env
| tanpa perlu menyentuh kode program.
*/

return [
    // Denda keterlambatan per hari (Rupiah)
    'denda_per_hari' => (int) env('SPPKU_DENDA_PER_HARI', 2000),

    // Lama peminjaman standar (hari)
    'durasi_pinjam' => (int) env('SPPKU_DURASI_PINJAM', 7),

    // Lama perpanjangan (hari) dan batas maksimal perpanjangan
    'durasi_perpanjangan' => (int) env('SPPKU_DURASI_PERPANJANGAN', 7),
    'maks_perpanjangan' => (int) env('SPPKU_MAKS_PERPANJANGAN', 1),

    // Batas buku yang boleh dipinjam bersamaan oleh satu siswa
    'maks_pinjam_aktif' => (int) env('SPPKU_MAKS_PINJAM_AKTIF', 3),

    // Pengingat jatuh tempo: mulai berapa hari sebelum batas pengembalian siswa diberi tahu
    // agar memperpanjang atau mengembalikan buku.
    'pengingat_hari' => (int) env('SPPKU_PENGINGAT_HARI', 2),

    // Batas waktu siswa datang ke perpustakaan setelah mengajukan peminjaman (jam).
    // Lewat dari batas ini, pengajuan yang belum diambil otomatis ditolak.
    'batas_ambil_jam' => (int) env('SPPKU_BATAS_AMBIL_JAM', 24),

    // Jam operasional perpustakaan (24 jam, zona waktu mengikuti APP_TIMEZONE).
    // Kunci 1 = Senin ... 7 = Minggu. Isi ['buka', 'tutup'] atau null bila libur.
    'jam_operasional' => [
        1 => ['07:00', '15:00'],
        2 => ['07:00', '15:00'],
        3 => ['07:00', '15:00'],
        4 => ['07:00', '15:00'],
        5 => ['07:00', '11:30'],
        6 => null,
        7 => null,
    ],

    'sekolah' => [
        'nama' => 'SMK Negeri 5 Surakarta',
        'perpustakaan' => 'Perpustakaan SMKN 5 Surakarta',
    ],
];
