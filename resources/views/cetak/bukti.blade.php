<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bukti peminjaman {{ $peminjaman->kode_trx }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="print-bar">
    <a class="btn btn-ghost" href="{{ route('siswa.riwayat') }}">Kembali</a>
    <button class="btn" onclick="window.print()">Cetak / simpan sebagai PDF</button>
</div>

<div class="sheet">
    <header class="kop">
        <div class="logo">@include('partials.icon', ['name' => 'books', 'size' => 26])</div>
        <div>
            <h1>Bukti Peminjaman Buku</h1>
            <p>{{ config('sppku.sekolah.perpustakaan') }} — {{ config('sppku.sekolah.nama') }}</p>
        </div>
    </header>

    <dl class="spec">
        <dt>Kode transaksi</dt><dd class="mono">{{ $peminjaman->kode_trx }}</dd>
        <dt>Nama peminjam</dt><dd>{{ $peminjaman->user->nama }}</dd>
        <dt>NIS / kelas</dt><dd>{{ $peminjaman->user->kode }} · {{ $peminjaman->user->kelas }}</dd>
        <dt>Judul buku</dt><dd>{{ $peminjaman->buku->judul }}</dd>
        <dt>Nomor buku</dt><dd class="mono">{{ $peminjaman->buku->nomor_buku }}</dd>
        <dt>Kode buku</dt><dd class="mono">{{ $peminjaman->eksemplar?->kode ?? 'ditentukan saat disetujui' }}</dd>
        <dt>Tanggal pinjam</dt><dd>{{ $peminjaman->tanggal_pengajuan->translatedFormat('d F Y') }}</dd>
        <dt>Jatuh tempo</dt><dd>{{ $peminjaman->tanggal_jatuh_tempo->translatedFormat('d F Y') }}</dd>
        <dt>Status</dt><dd>{{ $peminjaman->labelStatus() }}</dd>
        @if ($peminjaman->tanggal_kembali)
            <dt>Tanggal kembali</dt><dd>{{ $peminjaman->tanggal_kembali->translatedFormat('d F Y') }}</dd>
        @endif
    </dl>

    <p class="muted small mt-3" style="max-width:62ch">
        Buku wajib dikembalikan paling lambat pada tanggal jatuh tempo. Keterlambatan dikenai denda
        Rp {{ number_format(config('sppku.denda_per_hari'), 0, ',', '.') }} per hari. Simpan bukti ini dan
        tunjukkan kepada petugas saat mengambil atau mengembalikan buku.
    </p>

    <div class="ttd">
        <div>
            Surakarta, {{ now()->translatedFormat('d F Y') }}<br>
            Peminjam,
            <div class="garis">{{ $peminjaman->user->nama }}</div>
        </div>
    </div>
</div>
</body>
</html>
