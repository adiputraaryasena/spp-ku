<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Eksemplar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Kode per eksemplar: satu judul punya rentang kode (mis. BK-101-001 s.d. BK-101-010).
 * Kode bersifat unik di seluruh perpustakaan sehingga tidak ada duplikat antar salinan.
 */
class EksemplarController extends Controller
{
    public function index(Buku $buku): View
    {
        $buku->load('kategori');

        $eksemplars = $buku->eksemplars()->with('peminjamanAktif.user')->withCount('peminjamans')->get();
        $buku->setRelation('eksemplars', $eksemplars);

        return view('admin.eksemplar', compact('buku', 'eksemplars'));
    }

    /**
     * Halaman cetak barcode: isi barcode = kode eksemplar.
     *   ?mode=lembar  -> beberapa barcode dalam satu lembar A4 (default)
     *   ?mode=kecil   -> satu barcode per label kecil (50 x 30 mm)
     *   ?eksemplar=ID -> cetak satu kode saja
     * Kode berstatus nonaktif (rusak/hilang) tidak ikut dicetak, kecuali dipilih langsung.
     */
    public function cetakBarcode(Request $request, Buku $buku): View
    {
        $mode = $request->query('mode') === 'kecil' ? 'kecil' : 'lembar';

        $buku->load('kategori');

        $query = $buku->eksemplars();

        if ($request->filled('eksemplar')) {
            $query->whereKey((int) $request->query('eksemplar'));
        } else {
            $query->where('status', '!=', Eksemplar::STATUS_NONAKTIF);
        }

        $eksemplars = $query->get();

        return view('cetak.barcode', [
            'buku' => $buku,
            'eksemplars' => $eksemplars,
            'mode' => $mode,
            'tunggal' => $request->filled('eksemplar'),
        ]);
    }

    public function store(Request $request, Buku $buku): RedirectResponse
    {
        $request->validate([
            'kode_awal' => ['required', 'string', 'max:60'],
            'kode_akhir' => ['required', 'string', 'max:60'],
        ], [], [
            'kode_awal' => 'kode awal',
            'kode_akhir' => 'kode akhir',
        ]);

        $jumlah = DB::transaction(function () use ($request, $buku) {
            $jumlah = $buku->tambahRentang($request->input('kode_awal'), $request->input('kode_akhir'));
            $buku->sinkronStok();

            return $jumlah;
        });

        return back()->with('sukses', $jumlah.' kode eksemplar ditambahkan ke "'.$buku->judul.'".');
    }

    /** Menonaktifkan (rusak/hilang) atau mengaktifkan kembali sebuah eksemplar. */
    public function update(Eksemplar $eksemplar): RedirectResponse
    {
        if ($eksemplar->status === Eksemplar::STATUS_DIPINJAM) {
            return back()->with('gagal', 'Kode '.$eksemplar->kode.' sedang dipinjam. Proses pengembaliannya dulu.');
        }

        $eksemplar->update([
            'status' => $eksemplar->status === Eksemplar::STATUS_NONAKTIF
                ? Eksemplar::STATUS_TERSEDIA
                : Eksemplar::STATUS_NONAKTIF,
        ]);

        $eksemplar->buku->sinkronStok();

        return back()->with('sukses', 'Kode '.$eksemplar->kode.($eksemplar->status === Eksemplar::STATUS_NONAKTIF
            ? ' dinonaktifkan (tidak akan dipinjamkan).'
            : ' diaktifkan kembali.'));
    }

    public function destroy(Eksemplar $eksemplar): RedirectResponse
    {
        if ($eksemplar->status === Eksemplar::STATUS_DIPINJAM || $eksemplar->peminjamans()->exists()) {
            return back()->with('gagal', 'Kode '.$eksemplar->kode.' punya riwayat peminjaman sehingga tidak bisa dihapus. Nonaktifkan saja bila sudah tidak beredar.');
        }

        $buku = $eksemplar->buku;
        $kode = $eksemplar->kode;
        $eksemplar->delete();
        $buku->sinkronStok();

        return back()->with('sukses', 'Kode '.$kode.' dihapus.');
    }
}
