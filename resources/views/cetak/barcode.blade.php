<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Barcode — {{ $buku->judul }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    {{-- Ukuran kertas mengikuti pilihan: lembar A4 berisi banyak label, atau satu label kecil per halaman. --}}
    <style>
        @media print {
            @if ($mode === 'kecil')
                @page { size: 50mm 30mm; margin: 0; }
            @else
                @page { size: A4 portrait; margin: 8mm; }
            @endif
        }
    </style>
</head>
<body class="bc-body">
@php
    $sekolah = config('sppku.sekolah.perpustakaan');
    $jumlah = $eksemplars->count();
    $urlLembar = route('admin.buku.barcode', array_filter(['buku' => $buku, 'mode' => 'lembar', 'eksemplar' => request('eksemplar')]));
    $urlKecil = route('admin.buku.barcode', array_filter(['buku' => $buku, 'mode' => 'kecil', 'eksemplar' => request('eksemplar')]));
@endphp

<header class="bc-bar">
    <div class="bc-bar-inner">
        <div class="bc-bar-info">
            <a class="bc-back" href="{{ route('admin.buku.eksemplar', $buku) }}">&larr; Kembali</a>
            <h1 class="bc-title">Cetak barcode eksemplar</h1>
            <p class="bc-sub">{{ $buku->judul }} &middot; <strong>{{ $jumlah }}</strong> barcode{{ $tunggal ? ' (satu kode)' : '' }}</p>
        </div>

        <div class="bc-seg" role="group" aria-label="Pilih ukuran cetak">
            <a href="{{ $urlLembar }}" class="{{ $mode === 'lembar' ? 'is-on' : '' }}" aria-current="{{ $mode === 'lembar' ? 'true' : 'false' }}">
                <span class="bc-seg-t">Satu lembar</span><span class="bc-seg-s">A4 &middot; banyak barcode</span>
            </a>
            <a href="{{ $urlKecil }}" class="{{ $mode === 'kecil' ? 'is-on' : '' }}" aria-current="{{ $mode === 'kecil' ? 'true' : 'false' }}">
                <span class="bc-seg-t">Label kecil</span><span class="bc-seg-s">50 &times; 30 mm &middot; 1 barcode</span>
            </a>
        </div>

        <button class="btn bc-print" type="button" onclick="window.print()" {{ $jumlah === 0 ? 'disabled' : '' }}>
            @include('partials.icon', ['name' => 'printer', 'size' => 17]) Cetak
        </button>
    </div>
    <p class="bc-tip">
        @if ($mode === 'kecil')
            Pada kotak cetak pilih ukuran kertas <strong>50 &times; 30 mm</strong> (atau ukuran label printer Anda), margin <strong>Tidak ada</strong>, skala <strong>100%</strong>.
        @else
            Pada kotak cetak pilih kertas <strong>A4</strong>, margin <strong>Bawaan</strong>, skala <strong>100%</strong> agar ukuran barcode tetap akurat.
        @endif
    </p>
</header>

@if ($jumlah === 0)
    <div class="bc-kosong">
        <div class="icon">@include('partials.icon', ['name' => 'book', 'size' => 40, 'stroke' => 1.2])</div>
        <h2>Belum ada kode yang bisa dicetak</h2>
        <p>Tambahkan rentang kode eksemplar terlebih dahulu (kode nonaktif tidak ikut dicetak).</p>
    </div>
@else
    <main class="bc-sheet bc-{{ $mode }}">
        @foreach ($eksemplars as $e)
            <figure class="bc-label" style="--m: {{ \App\Support\Code128::totalModul($e->kode) }}">
                <figcaption class="bc-lib">{{ $sekolah }}</figcaption>
                {!! \App\Support\Code128::svg($e->kode) !!}
                <div class="bc-kode">{{ $e->kode }}</div>
                <div class="bc-judul">{{ $buku->judul }}</div>
            </figure>
        @endforeach
    </main>
@endif
</body>
</html>
