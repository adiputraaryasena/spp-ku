<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** FR-03 — katalog digital (OPAC) yang bisa dicari siswa secara mandiri. */
class KatalogController extends Controller
{
    public function index(Request $request): View
    {
        $kata = $request->query('cari');
        $slug = $request->query('kategori', 'semua');

        $bukus = Buku::with('kategori')
            ->cari($kata)
            ->kategori($slug)
            ->orderBy('judul')
            ->paginate(12)
            ->withQueryString();

        $kategoris = Kategori::withCount('bukus')->orderBy('nama')->get();

        return view('siswa.katalog', compact('bukus', 'kategoris', 'kata', 'slug'));
    }

    public function show(Buku $buku): View
    {
        $buku->load('kategori', 'eksemplars');

        // Tamu (belum login) boleh melihat detail buku; pengajuan tetap wajib login.
        $user = auth()->user();
        $pengajuanAktif = $user && $user->isSiswa()
            && $user->peminjamans()
                ->where('buku_id', $buku->id)
                ->whereIn('status', ['pending', 'dipinjam'])
                ->exists();

        $serupa = Buku::where('kategori_id', $buku->kategori_id)
            ->whereKeyNot($buku->id)
            ->take(4)
            ->get();

        return view('siswa.buku-detail', compact('buku', 'pengajuanAktif', 'serupa'));
    }

    /**
     * Tujuan tombol "Masuk untuk meminjam". Rute ini berada di grup auth, sehingga tamu
     * diarahkan ke login lebih dulu lalu dikembalikan ke sini, dan diteruskan ke detail buku.
     */
    public function pinjam(Buku $buku): RedirectResponse
    {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('siswa.buku', $buku);
    }
}
