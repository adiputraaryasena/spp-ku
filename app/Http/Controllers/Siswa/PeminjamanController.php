<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Support\JamOperasional;

/** FR-03 — pengajuan peminjaman & riwayat transaksi milik siswa. */
class PeminjamanController extends Controller
{
    public function index(Request $request): View
    {
        Peminjaman::tolakKedaluwarsa();

        $status = $request->query('status', 'semua');

        $menunggu = Peminjaman::with('buku')
            ->where('user_id', auth()->id())
            ->where('status', Peminjaman::STATUS_PENDING)
            ->oldest('created_at')
            ->get();

        $peminjamans = Peminjaman::with('buku', 'eksemplar')
            ->where('user_id', auth()->id())
            ->status($status)
            ->latest('tanggal_pengajuan')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('siswa.riwayat', compact('peminjamans', 'status', 'menunggu'));
    }

    public function store(Request $request, Buku $buku): RedirectResponse
    {
        $data = $request->validate([
            'catatan_siswa' => ['nullable', 'string', 'max:500'],
        ], [], [
            'catatan_siswa' => 'catatan',
        ]);

        $user = $request->user();

        // Pengajuan lama yang tidak diambil 24 jam sudah ditolak sebelum menghitung batas aktif.
        Peminjaman::tolakKedaluwarsa();

        if (! $buku->tersedia()) {
            return back()->with('gagal', 'Stok buku ini sedang habis. Coba lagi setelah ada yang dikembalikan.');
        }

        $sudahAda = $user->peminjamans()
            ->where('buku_id', $buku->id)
            ->whereIn('status', [Peminjaman::STATUS_PENDING, Peminjaman::STATUS_DIPINJAM])
            ->exists();

        if ($sudahAda) {
            return back()->with('gagal', 'Anda masih punya pengajuan aktif untuk buku ini.');
        }

        $aktif = $user->peminjamans()
            ->whereIn('status', [Peminjaman::STATUS_PENDING, Peminjaman::STATUS_DIPINJAM])
            ->count();

        if ($aktif >= (int) config('sppku.maks_pinjam_aktif')) {
            return back()->with('gagal', 'Batas '.config('sppku.maks_pinjam_aktif').' buku aktif tercapai. Kembalikan salah satu buku dulu.');
        }

        // Siswa wajib datang ke perpustakaan dalam batas_ambil_jam (24 jam). Bila perpustakaan
        // tidak buka sama sekali dalam rentang itu, pengajuan pasti kedaluwarsa — cegah sejak awal.
        $batasJam = (int) config('sppku.batas_ambil_jam');

        if (! JamOperasional::adaJamBukaDalam(now(), $batasJam)) {
            return back()->with('gagal', 'Perpustakaan tidak buka dalam '.$batasJam.' jam ke depan, sehingga buku tidak bisa diambil tepat waktu. '
                .'Silakan ajukan lagi pada jam buka.');
        }

        // Pengajuan selalu bertanggal hari ini; pengambilan buku di perpustakaan dalam 24 jam.
        $pengajuan = Carbon::today();

        $pinjam = DB::transaction(function () use ($user, $buku, $pengajuan, $data) {
            return Peminjaman::create([
                'kode_trx' => Peminjaman::buatKodeTrx(),
                'user_id' => $user->id,
                'buku_id' => $buku->id,
                'tanggal_pengajuan' => $pengajuan,
                'tanggal_jatuh_tempo' => $pengajuan->copy()->addDays((int) config('sppku.durasi_pinjam')),
                'status' => Peminjaman::STATUS_PENDING,
                'catatan_siswa' => $data['catatan_siswa'] ?? null,
            ]);
        });

        $batas = $pinjam->fresh()->batasAmbil();

        return redirect()->route('siswa.riwayat')
            ->with('sukses', 'Pengajuan terkirim. Datang ke perpustakaan sebelum '
                .$batas->translatedFormat('l, d F Y \\p\\u\\k\\u\\l H.i').' WIB untuk mengambil buku. '
                .'Jika tidak diambil dalam '.$batasJam.' jam, pengajuan otomatis ditolak.');
    }

    /** Perpanjangan mandiri satu kali, selama belum jatuh tempo. */
    public function perpanjang(Peminjaman $peminjaman): RedirectResponse
    {
        abort_unless($peminjaman->user_id === auth()->id(), 403);

        if (! $peminjaman->bolehDiperpanjang()) {
            return back()->with('gagal', 'Transaksi ini tidak bisa diperpanjang lagi. Hubungi petugas perpustakaan.');
        }

        $peminjaman->update([
            'tanggal_jatuh_tempo' => $peminjaman->tanggal_jatuh_tempo
                ->copy()->addDays((int) config('sppku.durasi_perpanjangan')),
            'jumlah_perpanjangan' => $peminjaman->jumlah_perpanjangan + 1,
        ]);

        return back()->with('sukses', 'Jatuh tempo diperpanjang sampai '
            .$peminjaman->fresh()->tanggal_jatuh_tempo->translatedFormat('d F Y').'. Kembalikan buku sebelum tanggal itu.');
    }

    /** Bukti peminjaman siap cetak (Ctrl+P atau Simpan sebagai PDF). */
    public function cetak(Peminjaman $peminjaman): View
    {
        abort_unless($peminjaman->user_id === auth()->id(), 403);
        $peminjaman->load('buku', 'user', 'eksemplar');

        return view('cetak.bukti', ['peminjaman' => $peminjaman]);
    }
}
