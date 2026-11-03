<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * FR-02 — CRUD master data buku.
 * Aturan unique pada nomor_buku mencegah data ganda (masalah utama di dokumen analisis).
 */
class BukuController extends Controller
{
    public function index(Request $request): View
    {
        $kata = $request->query('cari');

        $bukus = Buku::with(['kategori', 'eksemplars:id,buku_id,kode,prefix,urutan,status'])
            ->cari($kata)
            ->orderBy('nomor_buku')
            ->paginate(15)
            ->withQueryString();

        $kategoris = Kategori::orderBy('nama')->get();

        return view('admin.buku', compact('bukus', 'kategoris', 'kata'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        $request->validate([
            'kode_awal' => ['required', 'string', 'max:60'],
            'kode_akhir' => ['required', 'string', 'max:60'],
        ], [], [
            'kode_awal' => 'kode awal',
            'kode_akhir' => 'kode akhir',
        ]);

        $data['cover'] = $this->simpanCover($request);
        $data['jumlah_total'] = 0;
        $data['jumlah_tersedia'] = 0;

        // Buku dan seluruh kode eksemplarnya dibuat bersamaan; bila ada kode bentrok, tidak ada yang tersimpan.
        $jumlah = DB::transaction(function () use ($data, $request) {
            $buku = Buku::create($data);
            $jumlah = $buku->tambahRentang($request->input('kode_awal'), $request->input('kode_akhir'));
            $buku->sinkronStok();

            return $jumlah;
        });

        return back()->with('sukses', 'Buku "'.$data['judul'].'" ditambahkan dengan '.$jumlah.' eksemplar ('
            .trim($request->input('kode_awal')).' – '.trim($request->input('kode_akhir')).').');
    }

    public function update(Request $request, Buku $buku): RedirectResponse
    {
        // Jumlah & kode eksemplar tidak diubah di sini; kelola lewat halaman "Kode eksemplar".
        $data = $this->validasi($request, $buku);

        if ($cover = $this->simpanCover($request)) {
            $data['cover'] = $cover;
        }

        $buku->update($data);

        return back()->with('sukses', 'Data buku diperbarui.');
    }

    public function destroy(Buku $buku): RedirectResponse
    {
        if ($buku->peminjamans()->whereIn('status', ['pending', 'dipinjam'])->exists()) {
            return back()->with('gagal', 'Buku ini sedang dipinjam atau menunggu verifikasi. Selesaikan transaksinya dulu.');
        }

        if ($buku->peminjamans()->exists()) {
            return back()->with('gagal', 'Buku ini punya riwayat transaksi sehingga tidak bisa dihapus. Nonaktifkan seluruh kode eksemplarnya bila buku sudah tidak beredar.');
        }

        $buku->delete();

        return back()->with('sukses', 'Buku dihapus.');
    }

    private function validasi(Request $request, ?Buku $buku = null): array
    {
        return $request->validate([
            'nomor_buku' => [
                'required', 'string', 'max:40',
                Rule::unique('bukus', 'nomor_buku')->ignore($buku?->id),
            ],
            'judul' => ['required', 'string', 'max:200'],
            'pengarang' => ['nullable', 'string', 'max:120'],
            'penerbit' => ['nullable', 'string', 'max:120'],
            'tahun_terbit' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'rak' => ['nullable', 'string', 'max:30'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
        ], [
            'nomor_buku.unique' => 'Nomor buku ini sudah terdaftar. Gunakan nomor lain agar tidak terjadi data ganda.',
        ], [
            'nomor_buku' => 'nomor buku',
            'kategori_id' => 'kategori',
        ]);
    }

    private function simpanCover(Request $request): ?string
    {
        $request->validate([
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if (! $request->hasFile('cover')) {
            return null;
        }

        $file = $request->file('cover');
        $nama = Str::random(20).'.'.$file->getClientOriginalExtension();
        $file->move(public_path('uploads/cover'), $nama);

        return 'uploads/cover/'.$nama;
    }
}
