@extends('layouts.siswa')
@section('judul', 'Katalog buku')

@section('konten')
    <div class="page-head">
        <div class="grow">
            <h1>Katalog buku digital</h1>
            <p>Cari berdasarkan judul, pengarang, penerbit, atau nomor buku.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('siswa.katalog') }}" class="card mb-3">
        <div class="card-body">
            <div class="searchbar">
                <input type="search" name="cari" value="{{ $kata }}" class="input"
                       placeholder="Contoh: Laravel, Tere Liye, atau RPL-002" aria-label="Kata kunci pencarian">
                <input type="hidden" name="kategori" value="{{ $slug }}">
                <button class="btn btn-lg" type="submit">
                    @include('partials.icon', ['name' => 'search', 'size' => 17]) Cari
                </button>
                @if ($kata)
                    <a class="btn btn-lg btn-ghost" href="{{ route('siswa.katalog', ['kategori' => $slug]) }}">Hapus filter</a>
                @endif
            </div>

            <div class="chips mt-2">
                <a href="{{ route('siswa.katalog', ['cari' => $kata]) }}"
                   @class(['chip', 'active' => $slug === 'semua'])>Semua kategori</a>
                @foreach ($kategoris as $k)
                    <a href="{{ route('siswa.katalog', ['kategori' => $k->slug, 'cari' => $kata]) }}"
                       @class(['chip', 'active' => $slug === $k->slug])>
                        {{ $k->nama }} <span class="n">{{ $k->bukus_count }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </form>

    @if ($bukus->total() > 0)
        <p class="muted small mb-2">{{ $bukus->total() }} judul ditemukan.</p>
        <div class="grid grid-books">
            @foreach ($bukus as $buku)
                @include('siswa.partials.kartu-buku', ['buku' => $buku])
            @endforeach
        </div>
        <div class="card mt-3">{{ $bukus->links() }}</div>
    @else
        <div class="card">
            <div class="empty">
                <div class="icon">@include('partials.icon', ['name' => 'search', 'size' => 40, 'stroke' => 1.2])</div>
                <h3>Tidak ada buku yang cocok</h3>
                <p>Coba kata kunci yang lebih pendek, atau pilih kategori lain untuk menelusuri koleksi.</p>
                <a class="btn btn-ghost" href="{{ route('siswa.katalog') }}">Tampilkan seluruh koleksi</a>
            </div>
        </div>
    @endif
@endsection
