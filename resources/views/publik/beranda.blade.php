@extends('layouts.siswa')
@section('judul', 'Beranda')

@section('konten')
    @php $pengguna = auth()->user(); @endphp
    <section class="hero mb-3">
        <h1>
            @if ($pengguna)
                Selamat datang, {{ explode(' ', $pengguna->nama)[0] }}.
            @else
                Selamat datang di {{ config('sppku.sekolah.perpustakaan') }}
            @endif
        </h1>
        <p>
            Telusuri katalog buku pelajaran, TKA, novel, dan referensi di {{ config('sppku.sekolah.perpustakaan') }}.
            @if ($pengguna)
                Ajukan peminjaman dari halaman detail buku, lalu ambil di perpustakaan.
            @else
                Katalog terbuka untuk umum; login hanya diperlukan saat Anda ingin meminjam buku.
            @endif
        </p>

        <form method="GET" action="{{ route('siswa.katalog') }}" class="hero-cari">
            <input type="search" name="cari" class="input" placeholder="Cari judul, pengarang, atau nomor buku"
                   aria-label="Cari buku">
            <button class="btn btn-lg btn-cari" type="submit">
                @include('partials.icon', ['name' => 'search', 'size' => 17]) Cari
            </button>
        </form>

        <div class="hero-aksi">
            <a class="btn btn-light" href="{{ route('siswa.katalog') }}">Telusuri katalog</a>
            @if (! $pengguna)
                <a class="btn btn-outline-light" href="{{ route('login') }}">Masuk untuk meminjam</a>
            @elseif ($pengguna->isAdmin())
                <a class="btn btn-outline-light" href="{{ route('admin.dashboard') }}">Buka panel petugas</a>
            @else
                <a class="btn btn-outline-light" href="{{ route('siswa.riwayat') }}">Riwayat peminjaman saya</a>
            @endif
        </div>
        <p class="hero-status">@include('partials.jam-status')</p>
    </section>

    @if ($saya)
        @include('partials.pengingat-ambil', ['menunggu' => $saya['menunggu']])

        <div class="grid grid-3 mb-3">
            <a href="{{ route('siswa.riwayat', ['status' => 'dipinjam']) }}" class="stat stat-link">
                <div class="label">Buku saya yang dipinjam</div>
                <div class="value">{{ $saya['dipinjam'] }}</div>
                <div class="hint">Maksimal {{ config('sppku.maks_pinjam_aktif') }} buku aktif</div>
            </a>
            <a href="{{ route('siswa.riwayat', ['status' => 'pending']) }}" class="stat stat-link is-warning">
                <div class="label">Menunggu diambil</div>
                <div class="value">{{ $saya['pending'] }}</div>
                <div class="hint">Ambil maks. {{ config('sppku.batas_ambil_jam') }} jam</div>
            </a>
            <a href="{{ route('siswa.riwayat', ['status' => 'selesai']) }}" class="stat stat-link is-success">
                <div class="label">Transaksi selesai</div>
                <div class="value">{{ $saya['selesai'] }}</div>
                <div class="hint">Buku sudah dikembalikan</div>
            </a>
        </div>

        @if ($saya['jatuhTempo']->isNotEmpty())
            <div class="card mb-3">
                <div class="card-head">
                    @include('partials.icon', ['name' => 'clock', 'size' => 18])
                    <h2 class="grow">Batas pengembalian terdekat</h2>
                </div>
                <div class="table-wrap">
                    <table class="data">
                        <thead>
                            <tr><th>Judul buku</th><th>Kode buku</th><th>Jatuh tempo</th><th>Sisa waktu</th><th></th></tr>
                        </thead>
                        <tbody>
                            @foreach ($saya['jatuhTempo'] as $p)
                                @php $sisa = (int) round(now()->startOfDay()->diffInDays($p->tanggal_jatuh_tempo, false)); @endphp
                                <tr>
                                    <td class="strong">{{ $p->buku->judul }}</td>
                                    <td><span class="kode-buku">{{ $p->eksemplar?->kode ?? '—' }}</span></td>
                                    <td>{{ $p->tanggal_jatuh_tempo->translatedFormat('d M Y') }}</td>
                                    <td>
                                        @if ($sisa < 0)
                                            <span class="badge badge-ditolak">Terlambat {{ abs($sisa) }} hari</span>
                                        @elseif ($sisa === 0)
                                            <span class="badge badge-pending">Jatuh tempo hari ini</span>
                                        @else
                                            <span class="badge badge-dipinjam">{{ $sisa }} hari lagi</span>
                                        @endif
                                    </td>
                                    <td class="right">
                                        <a class="btn btn-sm btn-ghost" href="{{ route('siswa.cetak', $p) }}" target="_blank">Cetak bukti</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif

    <div class="grid grid-3 mb-3">
        <div class="stat">
            <div class="label">Judul buku</div>
            <div class="value">{{ number_format($statistik['judul'], 0, ',', '.') }}</div>
            <div class="hint">Koleksi yang bisa ditelusuri</div>
        </div>
        <div class="stat is-success">
            <div class="label">Eksemplar tersedia</div>
            <div class="value">{{ number_format($statistik['eksemplar'], 0, ',', '.') }}</div>
            <div class="hint">Siap dipinjam hari ini</div>
        </div>
        <div class="stat is-warning">
            <div class="label">Kategori</div>
            <div class="value">{{ $statistik['kategori'] }}</div>
            <div class="hint">Pelajaran, TKA, novel, referensi</div>
        </div>
    </div>

    <div class="split mb-3">
        <section class="card">
            <div class="card-head">
                @include('partials.icon', ['name' => 'clipboard', 'size' => 18])
                <h2 class="grow">Cara meminjam buku</h2>
            </div>
            <div class="card-body">
                <ol class="langkah">
                    <li><strong>Cari buku</strong> di katalog, lalu buka halaman detailnya.</li>
                    <li>
                        <strong>Masuk</strong> dengan NIS dan kata sandi Anda
                        @if ($pengguna) (Anda sudah masuk) @endif,
                        lalu kirim pengajuan peminjaman.
                    </li>
                    <li>
                        <strong>Datang ke perpustakaan dalam {{ config('sppku.batas_ambil_jam') }} jam</strong>
                        pada jam operasional untuk mengambil buku.
                        Jika tidak datang, pengajuan <u>otomatis ditolak</u>.
                    </li>
                    <li>Kembalikan buku paling lambat {{ config('sppku.durasi_pinjam') }} hari setelahnya.
                        Terlambat dikenai denda Rp {{ number_format(config('sppku.denda_per_hari'), 0, ',', '.') }} per hari.</li>
                </ol>
            </div>
        </section>

        @include('partials.jam-operasional')
    </div>

    <div class="card mb-3">
        <div class="card-head">
            @include('partials.icon', ['name' => 'tag', 'size' => 18])
            <h2 class="grow">Jelajahi kategori</h2>
        </div>
        <div class="card-body">
            <div class="chips">
                @foreach ($kategoris as $k)
                    <a class="chip" href="{{ route('siswa.katalog', ['kategori' => $k->slug]) }}">
                        {{ $k->nama }} <span class="n">{{ $k->bukus_count }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="page-head" style="margin-bottom:12px">
        <div class="grow">
            <h2>Rekomendasi buku</h2>
            <p>Koleksi yang stoknya tersedia hari ini.</p>
        </div>
        <a class="btn btn-ghost" href="{{ route('siswa.katalog') }}">Lihat semua katalog</a>
    </div>

    <div class="grid grid-books">
        @forelse ($rekomendasi as $buku)
            @include('siswa.partials.kartu-buku', ['buku' => $buku])
        @empty
            <p class="muted">Belum ada buku yang tersedia.</p>
        @endforelse
    </div>
@endsection
