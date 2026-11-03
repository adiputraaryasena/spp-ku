<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Eksemplar;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Pengembalian buku + kalkulasi denda otomatis.
 * Denda = hari terlambat x tarif harian (config/sppku.php).
 */
class PengembalianController extends Controller
{
    public function index(Request $request): View
    {
        $kode = trim((string) $request->query('kode', ''));
        $ditemukan = null;

        if ($kode !== '') {
            $ditemukan = Peminjaman::with('user', 'buku', 'eksemplar')
                ->where('status', Peminjaman::STATUS_DIPINJAM)
                ->where(function ($q) use ($kode) {
                    $q->where('kode_trx', $kode)
                        ->orWhereHas('eksemplar', fn ($e) => $e->where('kode', $kode))
                        ->orWhereHas('buku', fn ($b) => $b->where('nomor_buku', $kode));
                })
                ->first();
        }

        $sedangDipinjam = Peminjaman::with('user', 'buku', 'eksemplar')
            ->where('status', Peminjaman::STATUS_DIPINJAM)
            ->orderBy('tanggal_jatuh_tempo')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengembalian', compact('kode', 'ditemukan', 'sedangDipinjam'));
    }

    public function konfirmasi(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        $data = $request->validate([
            'tanggal_kembali' => ['required', 'date'],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ], [], [
            'tanggal_kembali' => 'tanggal pengembalian',
        ]);

        if ($peminjaman->status !== Peminjaman::STATUS_DIPINJAM) {
            return back()->with('gagal', 'Transaksi ini tidak dalam status dipinjam.');
        }

        $kembali = Carbon::parse($data['tanggal_kembali'])->startOfDay();

        if ($kembali->lessThan($peminjaman->tanggal_pengajuan)) {
            return back()->with('gagal', 'Tanggal pengembalian tidak boleh mendahului tanggal peminjaman.');
        }

        $denda = $peminjaman->hitungDenda($kembali);

        DB::transaction(function () use ($peminjaman, $kembali, $denda, $data, $request) {
            // Kode buku yang dikembalikan kembali tersedia (kecuali sudah dinonaktifkan petugas).
            if ($peminjaman->eksemplar && $peminjaman->eksemplar->status === Eksemplar::STATUS_DIPINJAM) {
                $peminjaman->eksemplar->update(['status' => Eksemplar::STATUS_TERSEDIA]);
            }

            $peminjaman->buku->sinkronStok();

            $peminjaman->update([
                'status' => Peminjaman::STATUS_SELESAI,
                'tanggal_kembali' => $kembali,
                'denda' => $denda,
                'catatan_admin' => $data['catatan_admin'] ?: $peminjaman->catatan_admin,
                'diverifikasi_oleh' => $request->user()->id,
                'diverifikasi_pada' => now(),
            ]);
        });

        $pesan = $denda > 0
            ? 'Pengembalian dicatat. Denda keterlambatan Rp '.number_format($denda, 0, ',', '.').' ditagihkan.'
            : 'Pengembalian dicatat tepat waktu, tanpa denda.';

        return redirect()->route('admin.pengembalian.index')->with('sukses', $pesan);
    }

    /** Kuitansi pengembalian siap cetak. */
    public function kuitansi(Peminjaman $peminjaman): View
    {
        $peminjaman->load('user', 'buku', 'petugas', 'eksemplar');

        return view('cetak.kuitansi', ['peminjaman' => $peminjaman]);
    }
}
