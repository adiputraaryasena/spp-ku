<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kuitansi pengembalian {{ $peminjaman->kode_trx }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@php
    $acuan = $peminjaman->tanggal_kembali ?: now()->startOfDay();
    $telat = $peminjaman->hariTerlambat($acuan);
    $denda = $peminjaman->status === 'selesai' ? $peminjaman->denda : $peminjaman->hitungDenda($acuan);
@endphp

<div class="print-bar">
    <a class="btn btn-ghost" href="{{ route('admin.pengembalian.index') }}">Kembali</a>
    <button class="btn" onclick="window.print()">Cetak / simpan sebagai PDF</button>
</div>

<div class="sheet">
    <header class="kop">
        <div class="logo">@include('partials.icon', ['name' => 'books', 'size' => 26])</div>
        <div>
            <h1>Kuitansi Pengembalian Buku</h1>
            <p>{{ config('sppku.sekolah.perpustakaan') }} — {{ config('sppku.sekolah.nama') }}</p>
        </div>
    </header>

    <dl class="spec">
        <dt>Kode transaksi</dt><dd class="mono">{{ $peminjaman->kode_trx }}</dd>
        <dt>Nama peminjam</dt><dd>{{ $peminjaman->user->nama }}</dd>
        <dt>NIS / kelas</dt><dd>{{ $peminjaman->user->kode }} · {{ $peminjaman->user->kelas }}</dd>
        <dt>Judul buku</dt><dd>{{ $peminjaman->buku->judul }} ({{ $peminjaman->buku->nomor_buku }})</dd>
        <dt>Kode buku</dt><dd class="mono">{{ $peminjaman->eksemplar?->kode ?? '—' }}</dd>
        <dt>Tanggal pinjam</dt><dd>{{ $peminjaman->tanggal_pengajuan->translatedFormat('d F Y') }}</dd>
        <dt>Jatuh tempo</dt><dd>{{ $peminjaman->tanggal_jatuh_tempo->translatedFormat('d F Y') }}</dd>
        <dt>Tanggal kembali</dt><dd>{{ $acuan->translatedFormat('d F Y') }}</dd>
        <dt>Keterlambatan</dt><dd>{{ $telat > 0 ? $telat.' hari' : 'Tidak terlambat' }}</dd>
    </dl>

    <div class="callout-denda mt-3">
        <div>
            <div class="strong">Denda keterlambatan</div>
            <div class="small">
                {{ $telat }} hari &times; Rp {{ number_format(config('sppku.denda_per_hari'), 0, ',', '.') }} per hari
            </div>
        </div>
        <div class="nominal">Rp {{ number_format($denda, 0, ',', '.') }}</div>
    </div>

    <div class="ttd">
        <div>
            Surakarta, {{ now()->translatedFormat('d F Y') }}<br>
            Petugas Perpustakaan,
            <div class="garis">{{ $peminjaman->petugas->nama ?? auth()->user()->nama }}</div>
        </div>
    </div>
</div>
</body>
</html>
