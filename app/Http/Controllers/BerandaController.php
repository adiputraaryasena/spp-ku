<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Illuminate\View\View;

/**
 * Beranda tunggal untuk semua pengunjung. Tampilannya sama bagi tamu dan siswa;
 * siswa yang sudah login hanya mendapat tambahan blok "Pinjaman saya".
 * Meminjam buku tetap mewajibkan login.
 */
class BerandaController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $statistik = [
            'judul' => Buku::count(),
            'eksemplar' => (int) Buku::sum('jumlah_tersedia'),
            'kategori' => Kategori::count(),
        ];

        $kategoris = Kategori::withCount('bukus')->orderBy('nama')->get();

        $rekomendasi = Buku::with('kategori')
            ->where('jumlah_tersedia', '>', 0)
            ->inRandomOrder()
            ->take(5)
            ->get();

        // Ringkasan pribadi hanya untuk siswa yang sudah masuk.
        $saya = null;

        if ($user && $user->isSiswa()) {
            Peminjaman::tolakKedaluwarsa();

            $hitung = fn (string $status) => Peminjaman::where('user_id', $user->id)->where('status', $status)->count();

            $saya = [
                'dipinjam' => $hitung(Peminjaman::STATUS_DIPINJAM),
                'pending' => $hitung(Peminjaman::STATUS_PENDING),
                'selesai' => $hitung(Peminjaman::STATUS_SELESAI),
                'menunggu' => Peminjaman::with('buku')
                    ->where('user_id', $user->id)
                    ->where('status', Peminjaman::STATUS_PENDING)
                    ->oldest('created_at')
                    ->get(),
                'jatuhTempo' => Peminjaman::with('buku', 'eksemplar')
                    ->where('user_id', $user->id)
                    ->where('status', Peminjaman::STATUS_DIPINJAM)
                    ->orderBy('tanggal_jatuh_tempo')
                    ->take(3)
                    ->get(),
            ];
        }

        return view('publik.beranda', compact('statistik', 'kategoris', 'rekomendasi', 'saya'));
    }
}
