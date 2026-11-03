<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** FR-05 — rekap laporan, cetak PDF (via print dialog) dan ekspor Excel/CSV. */
class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        [$dari, $sampai, $status] = $this->filter($request);

        $query = $this->query($dari, $sampai, $status);

        $peminjamans = (clone $query)->paginate(20)->withQueryString();

        $ringkasan = [
            'total' => (clone $query)->count(),
            'dipinjam' => (clone $query)->where('status', Peminjaman::STATUS_DIPINJAM)->count(),
            'selesai' => (clone $query)->where('status', Peminjaman::STATUS_SELESAI)->count(),
            'ditolak' => (clone $query)->where('status', Peminjaman::STATUS_DITOLAK)->count(),
            'denda' => (int) (clone $query)->sum('denda'),
        ];

        return view('admin.laporan', compact('peminjamans', 'ringkasan', 'dari', 'sampai', 'status'));
    }

    /** Halaman siap cetak; siswa/petugas tinggal Ctrl+P lalu "Save as PDF". */
    public function cetak(Request $request): View
    {
        [$dari, $sampai, $status] = $this->filter($request);

        $peminjamans = $this->query($dari, $sampai, $status)->get();
        $totalDenda = $peminjamans->sum('denda');

        return view('cetak.laporan', compact('peminjamans', 'dari', 'sampai', 'status', 'totalDenda'));
    }

    /** Ekspor CSV, langsung terbuka di Microsoft Excel / LibreOffice. */
    public function excel(Request $request): StreamedResponse
    {
        [$dari, $sampai, $status] = $this->filter($request);

        $peminjamans = $this->query($dari, $sampai, $status)->get();
        $namaFile = 'laporan-spp-ku-'.$dari->format('Ymd').'-'.$sampai->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($peminjamans) {
            $out = fopen('php://output', 'w');

            // BOM UTF-8 supaya huruf beraksen rapi saat dibuka di Excel
            fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($out, [
                'Kode Transaksi', 'NIS', 'Nama Siswa', 'Kelas', 'Nomor Buku', 'Kode Eksemplar', 'Judul Buku',
                'Tanggal Pengajuan', 'Jatuh Tempo', 'Tanggal Kembali', 'Status', 'Denda (Rp)', 'Catatan Petugas',
            ], ';');

            foreach ($peminjamans as $p) {
                fputcsv($out, [
                    $p->kode_trx,
                    $p->user?->kode,
                    $p->user?->nama,
                    $p->user?->kelas,
                    $p->buku?->nomor_buku,
                    $p->eksemplar?->kode ?? '-',
                    $p->buku?->judul,
                    $p->tanggal_pengajuan?->format('d/m/Y'),
                    $p->tanggal_jatuh_tempo?->format('d/m/Y'),
                    $p->tanggal_kembali?->format('d/m/Y') ?? '-',
                    $p->labelStatus(),
                    $p->denda,
                    $p->catatan_admin,
                ], ';');
            }

            fclose($out);
        }, $namaFile, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /** @return array{0: Carbon, 1: Carbon, 2: string} */
    private function filter(Request $request): array
    {
        $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date'],
        ]);

        $dari = $request->filled('dari')
            ? Carbon::parse($request->query('dari'))->startOfDay()
            : Carbon::today()->startOfMonth();

        $sampai = $request->filled('sampai')
            ? Carbon::parse($request->query('sampai'))->endOfDay()
            : Carbon::today()->endOfDay();

        if ($sampai->lessThan($dari)) {
            [$dari, $sampai] = [$sampai->copy()->startOfDay(), $dari->copy()->endOfDay()];
        }

        return [$dari, $sampai, (string) $request->query('status', 'semua')];
    }

    private function query(Carbon $dari, Carbon $sampai, string $status)
    {
        return Peminjaman::with('user', 'buku', 'eksemplar')
            ->whereBetween('tanggal_pengajuan', [$dari->toDateString(), $sampai->toDateString()])
            ->status($status)
            ->latest('tanggal_pengajuan')
            ->latest('id');
    }
}
