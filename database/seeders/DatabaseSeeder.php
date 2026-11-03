<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Eksemplar;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Menyiapkan data awal SPP-Ku...');

        // ------------------------------------------------ Pengguna
        $admin = User::create([
            'kode' => 'ADM001',
            'nama' => 'Adiputra Arya Sena',
            'email' => 'petugas@smkn5solo.sch.id',
            'kelas' => null,
            'role' => 'admin',
            'password' => 'admin123',
            'harus_ganti_password' => false,
            'password_diganti_pada' => now(),
        ]);

        $siswaData = [
            ['2024001', 'Adiputra Arya Sena', 'XII RPL C'],
            ['2024002', 'Nadia Putri Ramadhani', 'XII RPL C'],
            ['2024003', 'Bagas Dwi Saputra', 'XI RPL A'],
            ['2024004', 'Salsabila Ayu Lestari', 'XI RPL B'],
            ['2024005', 'Rizky Maulana Hakim', 'X RPL A'],
        ];

        $siswa = collect($siswaData)->map(fn (array $row) => User::create([
            'kode' => $row[0],
            'nama' => $row[1],
            'kelas' => $row[2],
            'role' => 'siswa',
            'password' => 'siswa123',
            // Kata sandi bawaan: siswa wajib menggantinya saat pertama kali masuk
            'harus_ganti_password' => true,
        ]));

        // ------------------------------------------------ Kategori
        $kategoriNama = [
            'Rekayasa Perangkat Lunak' => 'Buku pemrograman, basis data, dan jaringan.',
            'Pelajaran Umum' => 'Buku mata pelajaran wajib kurikulum.',
            'Novel & Sastra' => 'Fiksi, kumpulan cerpen, dan puisi.',
            'Referensi' => 'Kamus, ensiklopedia, dan atlas.',
            'Sejarah & Biografi' => 'Sejarah Indonesia dan tokoh dunia.',
            'Agama' => 'Pendidikan agama dan budi pekerti.',
        ];

        $kategori = collect($kategoriNama)->mapWithKeys(fn ($ket, $nama) => [
            Str::slug($nama) => Kategori::create([
                'nama' => $nama,
                'slug' => Str::slug($nama),
                'keterangan' => $ket,
            ]),
        ]);

        // ------------------------------------------------ Buku
        $bukuData = [
            ['RPL-001', 'Dasar Pemrograman Web dengan PHP dan MySQL', 'Abdul Kadir', 'Andi Publisher', 2021, 'rekayasa-perangkat-lunak', 4, 'A-01'],
            ['RPL-002', 'Laravel untuk Pemula: Membangun Aplikasi Modern', 'Rahmat Hidayat', 'Elex Media', 2022, 'rekayasa-perangkat-lunak', 3, 'A-01'],
            ['RPL-003', 'Algoritma dan Struktur Data', 'Rinaldi Munir', 'Informatika Bandung', 2020, 'rekayasa-perangkat-lunak', 5, 'A-02'],
            ['RPL-004', 'Basis Data Relasional dan SQL', 'Janner Simarmata', 'Andi Publisher', 2019, 'rekayasa-perangkat-lunak', 3, 'A-02'],
            ['RPL-005', 'Pemrograman Berorientasi Objek dengan Java', 'Budi Raharjo', 'Informatika Bandung', 2021, 'rekayasa-perangkat-lunak', 2, 'A-03'],
            ['RPL-006', 'Jaringan Komputer untuk SMK', 'Iwan Sofana', 'Informatika Bandung', 2020, 'rekayasa-perangkat-lunak', 3, 'A-03'],
            ['RPL-007', 'Desain Antarmuka dan Pengalaman Pengguna', 'Dhani Prasetyo', 'Deepublish', 2023, 'rekayasa-perangkat-lunak', 2, 'A-04'],
            ['UMM-001', 'Matematika SMK Kelas XII', 'Tim Penyusun', 'Erlangga', 2022, 'pelajaran-umum', 8, 'B-01'],
            ['UMM-002', 'Bahasa Indonesia Kelas XII', 'Tim Penyusun', 'Erlangga', 2022, 'pelajaran-umum', 8, 'B-01'],
            ['UMM-003', 'Bahasa Inggris untuk SMK', 'Tim Penyusun', 'Yudhistira', 2021, 'pelajaran-umum', 6, 'B-02'],
            ['UMM-004', 'Projek Ilmu Pengetahuan Alam dan Sosial', 'Tim Penyusun', 'Kemendikbud', 2023, 'pelajaran-umum', 5, 'B-02'],
            ['NOV-001', 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 'novel-sastra', 3, 'C-01'],
            ['NOV-002', 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1980, 'novel-sastra', 2, 'C-01'],
            ['NOV-003', 'Hujan', 'Tere Liye', 'Gramedia Pustaka Utama', 2016, 'novel-sastra', 4, 'C-02'],
            ['NOV-004', 'Rumah Kaca', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1988, 'novel-sastra', 2, 'C-02'],
            ['REF-001', 'Kamus Besar Bahasa Indonesia Edisi V', 'Badan Bahasa', 'Balai Pustaka', 2016, 'referensi', 2, 'D-01'],
            ['REF-002', 'Kamus Istilah Teknologi Informasi', 'Tim Redaksi', 'Andi Publisher', 2018, 'referensi', 2, 'D-01'],
            ['SEJ-001', 'Sejarah Indonesia Modern 1200-2008', 'M.C. Ricklefs', 'Serambi', 2008, 'sejarah-biografi', 2, 'E-01'],
            ['SEJ-002', 'Habibie: Sebuah Biografi', 'A. Makmur Makka', 'Republika', 2019, 'sejarah-biografi', 2, 'E-01'],
            ['AGM-001', 'Pendidikan Agama Islam dan Budi Pekerti', 'Tim Penyusun', 'Kemendikbud', 2022, 'agama', 6, 'F-01'],
        ];

        $buku = collect($bukuData)->mapWithKeys(function (array $row) use ($kategori) {
            $model = Buku::create([
                'nomor_buku' => $row[0],
                'judul' => $row[1],
                'pengarang' => $row[2],
                'penerbit' => $row[3],
                'tahun_terbit' => $row[4],
                'kategori_id' => $kategori[$row[5]]->id,
                'jumlah_total' => $row[6],
                'jumlah_tersedia' => $row[6],
                'rak' => $row[7],
                'deskripsi' => 'Koleksi '.$kategori[$row[5]]->nama.' milik Perpustakaan SMKN 5 Surakarta. '
                    .'Buku ini dapat dipinjam selama '.config('sppku.durasi_pinjam').' hari dan '
                    .'dapat diperpanjang satu kali apabila tidak sedang diantre siswa lain.',
            ]);

            // Setiap eksemplar punya kode unik: NOMOR-BUKU-01, NOMOR-BUKU-02, ...
            $lebar = max(2, strlen((string) $row[6]));
            for ($n = 1; $n <= $row[6]; $n++) {
                Eksemplar::create([
                    'buku_id' => $model->id,
                    'kode' => $row[0].'-'.str_pad((string) $n, $lebar, '0', STR_PAD_LEFT),
                    'prefix' => $row[0].'-',
                    'urutan' => $n,
                    'status' => Eksemplar::STATUS_TERSEDIA,
                ]);
            }

            return [$row[0] => $model];
        });

        // ------------------------------------------------ Transaksi contoh
        $hariIni = Carbon::today();

        $transaksi = [
            // [kode buku, index siswa, status, hari lalu pengajuan, catatan siswa]
            ['RPL-002', 0, Peminjaman::STATUS_DIPINJAM, 3, 'Untuk referensi projek akhir semester.'],
            ['RPL-003', 0, Peminjaman::STATUS_DIPINJAM, 12, 'Latihan soal algoritma.'],
            ['NOV-001', 1, Peminjaman::STATUS_PENDING, 0, 'Tugas resensi Bahasa Indonesia.'],
            ['RPL-001', 1, Peminjaman::STATUS_DIPINJAM, 2, 'Belajar PHP dasar.'],
            ['UMM-001', 2, Peminjaman::STATUS_PENDING, 1, null],
            ['NOV-003', 2, Peminjaman::STATUS_SELESAI, 20, 'Bacaan mandiri.'],
            ['REF-001', 3, Peminjaman::STATUS_DITOLAK, 5, 'Butuh untuk lomba.'],
            ['SEJ-001', 3, Peminjaman::STATUS_SELESAI, 30, null],
            ['AGM-001', 4, Peminjaman::STATUS_PENDING, 0, 'Buku pegangan kelas.'],
            ['RPL-006', 4, Peminjaman::STATUS_DIPINJAM, 9, 'Praktik konfigurasi jaringan.'],
            ['NOV-002', 1, Peminjaman::STATUS_SELESAI, 45, null],
            ['RPL-005', 2, Peminjaman::STATUS_DIPINJAM, 1, null],
        ];

        foreach ($transaksi as $i => $row) {
            [$nomorBuku, $indexSiswa, $status, $hariLalu, $catatan] = $row;

            $bukuItem = $buku[$nomorBuku];
            $pengajuan = $hariIni->copy()->subDays($hariLalu);
            $jatuhTempo = $pengajuan->copy()->addDays((int) config('sppku.durasi_pinjam'));

            $pinjam = new Peminjaman([
                'kode_trx' => 'TRX-'.str_pad((string) ($i + 1), 9, '0', STR_PAD_LEFT),
                'user_id' => $siswa[$indexSiswa]->id,
                'buku_id' => $bukuItem->id,
                'tanggal_pengajuan' => $pengajuan,
                'tanggal_jatuh_tempo' => $jatuhTempo,
                'status' => $status,
                'catatan_siswa' => $catatan,
            ]);

            if ($status === Peminjaman::STATUS_DITOLAK) {
                $pinjam->catatan_admin = 'Eksemplar sedang dipakai untuk kegiatan lomba sekolah. Silakan ajukan minggu depan.';
                $pinjam->diverifikasi_oleh = $admin->id;
                $pinjam->diverifikasi_pada = $pengajuan->copy()->addHours(4);
            }

            if ($status === Peminjaman::STATUS_DIPINJAM) {
                $pinjam->catatan_admin = 'Pengajuan disetujui. Ambil buku di meja sirkulasi.';
                $pinjam->diverifikasi_oleh = $admin->id;
                $pinjam->diverifikasi_pada = $pengajuan->copy()->addHours(2);
                $bukuItem->decrement('jumlah_tersedia');

                // Kode terkecil yang tersedia dipinjamkan lebih dulu.
                $eksemplar = $bukuItem->eksemplarBerikutnya();
                $eksemplar->update(['status' => Eksemplar::STATUS_DIPINJAM]);
                $pinjam->eksemplar_id = $eksemplar->id;
            }

            if ($status === Peminjaman::STATUS_SELESAI) {
                $pinjam->eksemplar_id = $bukuItem->eksemplarBerikutnya()?->id;
                // satu transaksi sengaja dibuat terlambat agar denda terlihat di laporan
                $kembali = $i === 5
                    ? $jatuhTempo->copy()->addDays(5)
                    : $jatuhTempo->copy()->subDay();

                $pinjam->catatan_admin = 'Pengajuan disetujui.';
                $pinjam->diverifikasi_oleh = $admin->id;
                $pinjam->diverifikasi_pada = $pengajuan->copy()->addHours(2);
                $pinjam->tanggal_kembali = $kembali;
                $pinjam->denda = max(0, (int) round($jatuhTempo->diffInDays($kembali, false)))
                    * (int) config('sppku.denda_per_hari');
            }

            $pinjam->save();
        }

        $this->command->info('Selesai. Login admin: ADM001 / admin123 — siswa: 2024001 / siswa123');
        $this->command->info('Akun siswa akan diminta membuat kata sandi sendiri saat pertama kali masuk.');
    }
}
