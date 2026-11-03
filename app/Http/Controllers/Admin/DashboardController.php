<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\View\View;

/** FR-05 — ringkasan statistik perpustakaan. */
class DashboardController extends Controller
{
    public function index(): View
    {
        $statistik = [
            'judul_buku' => Buku::count(),
            'eksemplar' => (int) Buku::sum('jumlah_total'),
            'siswa' => User::where('role', 'siswa')->count(),
            'perlu_verifikasi' => Peminjaman::where('status', Peminjaman::STATUS_PENDING)->count(),
            'sedang_dipinjam' => Peminjaman::where('status', Peminjaman::STATUS_DIPINJAM)->count(),
            'total_transaksi' => Peminjaman::count(),
            'denda_terkumpul' => (int) Peminjaman::sum('denda'),
        ];

        $antrean = Peminjaman::with('user', 'buku')
            ->where('status', Peminjaman::STATUS_PENDING)
            ->oldest('tanggal_pengajuan')
            ->take(8)
            ->get();

        $terlambat = Peminjaman::with('user', 'buku', 'eksemplar')
            ->where('status', Peminjaman::STATUS_DIPINJAM)
            ->whereDate('tanggal_jatuh_tempo', '<', now()->toDateString())
            ->orderBy('tanggal_jatuh_tempo')
            ->take(5)
            ->get();

        $bukuPopuler = Buku::withCount('peminjamans')
            ->orderByDesc('peminjamans_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('statistik', 'antrean', 'terlambat', 'bukuPopuler'));
    }
}
