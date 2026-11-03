<article class="book">
    <div class="book-cover">
        <span class="nomor">{{ $buku->nomor_buku }}</span>
        @if ($buku->cover)
            <img src="{{ asset($buku->cover) }}" alt="Sampul {{ $buku->judul }}" loading="lazy">
        @else
            @include('partials.icon', ['name' => 'book', 'size' => 38, 'stroke' => 1.2])
        @endif
    </div>
    <div class="book-info">
        <h3 class="book-title">{{ $buku->judul }}</h3>
        <p class="book-meta">{{ $buku->pengarang ?: 'Pengarang tidak dicantumkan' }}</p>
        <span class="badge badge-sm {{ $buku->tersedia() ? 'badge-tersedia' : 'badge-habis' }}">
            {{ $buku->tersedia() ? 'Tersedia '.$buku->jumlah_tersedia.' eks.' : 'Stok habis' }}
        </span>
        <a class="btn btn-sm btn-block" href="{{ route('siswa.buku', $buku) }}">Detail &amp; pinjam</a>
    </div>
</article>
