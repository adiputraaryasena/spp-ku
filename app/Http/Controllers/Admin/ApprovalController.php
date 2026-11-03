<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Eksemplar;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/** FR-04 — verifikasi pengajuan: setujui atau tolak beserta catatan tanggapan. */
class ApprovalController extends Controller
{
    public function index(Request $request): View
    {
        Peminjaman::tolakKedaluwarsa();

        $status = $request->query('status', Peminjaman::STATUS_PENDING);

        $peminjamans = Peminjaman::with('user', 'buku', 'petugas', 'eksemplar')
            ->status($status)
            ->oldest('tanggal_pengajuan')
            ->paginate(12)
            ->withQueryString();

        $jumlahPending = Peminjaman::where('status', Peminjaman::STATUS_PENDING)->count();

        return view('admin.approval', compact('peminjamans', 'status', 'jumlahPending'));
    }

    public function setujui(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        $data = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        if ($peminjaman->kedaluwarsaAmbil()) {
            Peminjaman::tolakKedaluwarsa();

            return back()->with('gagal', 'Batas '.config('sppku.batas_ambil_jam').' jam pengambilan sudah lewat, pengajuan '
                .$peminjaman->kode_trx.' ditolak otomatis. Siswa perlu mengajukan ulang.');
        }

        if ($peminjaman->status !== Peminjaman::STATUS_PENDING) {
            return back()->with('gagal', 'Transaksi ini sudah diverifikasi sebelumnya.');
        }

        $buku = $peminjaman->buku;

        if (! $buku->tersedia()) {
            return back()->with('gagal', 'Stok "'.$buku->judul.'" habis. Tolak pengajuan atau tunggu pengembalian.');
        }

        // Kode yang dipinjamkan selalu yang terkecil dulu (urut awalan, lalu angka) dan dikunci
        // selama transaksi supaya dua persetujuan bersamaan tidak mendapat kode yang sama.
        $kode = DB::transaction(function () use ($peminjaman, $buku, $data, $request) {
            $eksemplar = Eksemplar::where('buku_id', $buku->id)
                ->where('status', Eksemplar::STATUS_TERSEDIA)
                ->urut()
                ->lockForUpdate()
                ->first();

            if (! $eksemplar) {
                return null;
            }

            $eksemplar->update(['status' => Eksemplar::STATUS_DIPINJAM]);

            $peminjaman->update([
                'eksemplar_id' => $eksemplar->id,
                'status' => Peminjaman::STATUS_DIPINJAM,
                'catatan_admin' => $data['catatan_admin'] ?: 'Pengajuan disetujui. Ambil buku di meja sirkulasi.',
                'diverifikasi_oleh' => $request->user()->id,
                'diverifikasi_pada' => now(),
            ]);

            $buku->sinkronStok();

            return $eksemplar->kode;
        });

        if ($kode === null) {
            $buku->sinkronStok();

            return back()->with('gagal', 'Tidak ada eksemplar "'.$buku->judul.'" yang tersedia. Tolak pengajuan atau tunggu pengembalian.');
        }

        return back()->with('sukses', 'Pengajuan '.$peminjaman->kode_trx.' disetujui. Serahkan buku berkode '.$kode.' kepada siswa.');
    }

    public function tolak(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        $data = $request->validate([
            'catatan_admin' => ['required', 'string', 'max:500'],
        ], [
            'catatan_admin.required' => 'Tulis alasan penolakan agar siswa tahu langkah berikutnya.',
        ], [
            'catatan_admin' => 'alasan penolakan',
        ]);

        if ($peminjaman->status !== Peminjaman::STATUS_PENDING) {
            return back()->with('gagal', 'Transaksi ini sudah diverifikasi sebelumnya.');
        }

        $peminjaman->update([
            'status' => Peminjaman::STATUS_DITOLAK,
            'catatan_admin' => $data['catatan_admin'],
            'diverifikasi_oleh' => $request->user()->id,
            'diverifikasi_pada' => now(),
        ]);

        return back()->with('sukses', 'Pengajuan '.$peminjaman->kode_trx.' ditolak.');
    }
}
