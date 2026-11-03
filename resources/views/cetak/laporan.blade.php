<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan perpustakaan {{ $dari->format('d-m-Y') }} s.d. {{ $sampai->format('d-m-Y') }}</title>
    <style>
        /* Setting Kertas Landscape A4 */
        @page {
            size: A4 landscape;
            margin: 10mm 12mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #1e293b;
            background: #fff;
            padding: 20px;
        }

        /* Tombol Cetak / Navigasi (Hilang saat cetak) */
        .print-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1000px;
            margin: 0 auto 20px auto;
            padding: 10px 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            font-size: 9pt;
            font-weight: 600;
            color: #fff;
            background-color: #2563eb;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-ghost {
            background: transparent;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        /* Lembar Kerja Cetak */
        .sheet {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
        }

        /* Kop Laporan */
        .kop {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 2px solid #0f172a;
            margin-bottom: 15px;
        }

        .kop h1 {
            font-size: 14pt;
            font-weight: bold;
            color: #0f172a;
        }

        .kop p {
            font-size: 9.5pt;
            color: #475569;
        }

        /* Spesifikasi / Ringkasan */
        .spec {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 4px 20px;
            margin-bottom: 15px;
            background: #f8fafc;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-size: 8.5pt;
        }

        .spec-item {
            display: flex;
        }

        .spec-item strong {
            width: 130px;
            color: #334155;
        }

        /* Tabel Laporan Rapi */
        table.laporan-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 20px;
        }

        table.laporan-table th, 
        table.laporan-table td {
            border: 1px solid #94a3b8;
            padding: 6px 8px;
            vertical-align: middle;
            text-align: left;
        }

        table.laporan-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
        }

        table.laporan-table tfoot th {
            background-color: #f8fafc;
        }

        /* Utility Class */
        .center { text-align: center !important; }
        .right { text-align: right !important; }
        .nowrap { white-space: nowrap !important; }
        .mono { font-family: monospace; font-size: 8.5pt; }

        /* Tanda Tangan */
        .ttd-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .ttd-box {
            text-align: center;
            font-size: 9pt;
            width: 220px;
        }

        .ttd-box .nama {
            margin-top: 55px;
            font-weight: bold;
            text-decoration: underline;
        }

        /* CSS KHUSUS PRINTER / SIMPAN PDF */
        @media print {
            .print-bar {
                display: none !important;
            }
            body {
                padding: 0 !important;
                background: #fff !important;
            }
            .sheet {
                max-width: 100% !important;
                width: 100% !important;
            }
            table.laporan-table th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="print-bar">
    <a class="btn btn-ghost" href="{{ url()->previous() }}">Kembali</a>
    <button class="btn" onclick="window.print()">Cetak / simpan sebagai PDF</button>
</div>

<div class="sheet">
    <header class="kop">
        <div>
            <h1>Laporan Peminjaman &amp; Pengembalian Buku</h1>
            <p>{{ config('sppku.sekolah.perpustakaan') }} — {{ config('sppku.sekolah.nama') }}</p>
        </div>
    </header>

    <div class="spec">
        <div class="spec-item"><strong>Periode</strong>: {{ $dari->translatedFormat('d F Y') }} s.d. {{ $sampai->translatedFormat('d F Y') }}</div>
        <div class="spec-item"><strong>Jumlah Transaksi</strong>: {{ $peminjamans->count() }} transaksi</div>
        <div class="spec-item"><strong>Filter Status</strong>: {{ $status === 'semua' ? 'Semua status' : ucfirst($status) }}</div>
        <div class="spec-item"><strong>Total Denda</strong>: Rp {{ number_format($totalDenda, 0, ',', '.') }}</div>
    </div>

    <table class="laporan-table">
        <thead>
            <tr>
                <th width="3%" class="center">No</th>
                <th width="12%">Kode</th>
                <th width="16%">Nama Siswa</th>
                <th width="8%">Kelas</th>
                <th>Judul Buku</th>
                <th width="11%">Kode Buku</th>
                <th width="8%" class="center">Pinjam</th>
                <th width="8%" class="center">Tempo</th>
                <th width="8%" class="center">Kembali</th>
                <th width="11%">Status</th>
                <th width="8%" class="right">Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($peminjamans as $i => $p)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td class="mono nowrap">{{ $p->kode_trx }}</td>
                    <td>{{ $p->user->nama }}</td>
                    <td>{{ $p->user->kelas ?: '—' }}</td>
                    <td>{{ $p->buku->judul }}</td>
                    <td class="mono nowrap">{{ $p->eksemplar?->kode ?? '—' }}</td>
                    <td class="center nowrap">{{ $p->tanggal_pengajuan->format('d/m/y') }}</td>
                    <td class="center nowrap">{{ $p->tanggal_jatuh_tempo->format('d/m/y') }}</td>
                    <td class="center nowrap">{{ $p->tanggal_kembali?->format('d/m/y') ?? '—' }}</td>
                    <td class="nowrap">{{ $p->labelStatus() }}</td>
                    <td class="right nowrap">{{ $p->denda > 0 ? number_format($p->denda, 0, ',', '.') : '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="center" style="padding:20px; color:#64748b;">Tidak ada transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($peminjamans->isNotEmpty())
            <tfoot>
                <tr>
                    <th colspan="10" class="right">Total Denda</th>
                    <th class="right nowrap">Rp {{ number_format($totalDenda, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="ttd-container">
        <div class="ttd-box">
            Surakarta, {{ now()->translatedFormat('d F Y') }}<br>
            Petugas Perpustakaan,
            <div class="nama">{{ auth()->user()->nama }}</div>
        </div>
    </div>
</div>

</body>
</html>