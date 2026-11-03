@extends('layouts.siswa')
@section('judul', $buku->judul)

@section('konten')
    <p class="mb-2"><a href="{{ route('siswa.katalog') }}" class="small">&larr; Kembali ke katalog</a></p>
    @php $pengguna = auth()->user(); @endphp

    @include('partials.errors')

    <div class="card mb-3">
        <div class="card-body">
            <div class="detail-grid">
                <div>
                    <div class="cover-lg">
                        @if ($buku->cover)
                            <img src="{{ asset($buku->cover) }}" alt="Sampul {{ $buku->judul }}" loading="lazy">
                        @else
                            @include('partials.icon', ['name' => 'book', 'size' => 56, 'stroke' => 1])
                        @endif
                    </div>
                    <div class="mt-2">
                        <span class="badge {{ $buku->tersedia() ? 'badge-tersedia' : 'badge-habis' }}">
                            {{ $buku->tersedia() ? $buku->jumlah_tersedia.' dari '.$buku->jumlah_total.' eksemplar tersedia' : 'Semua eksemplar sedang dipinjam' }}
                        </span>
                    </div>
                </div>

                <div>
                    <h1>{{ $buku->judul }}</h1>
                    <p class="muted mb-2">{{ $buku->pengarang ?: 'Pengarang tidak dicantumkan' }}</p>

                    <dl class="spec mb-2">
                        <dt>Nomor buku</dt><dd class="mono">{{ $buku->nomor_buku }}</dd>
                        <dt>Kode eksemplar</dt>
                        <dd class="mono">{{ $buku->teksRentang() }}</dd>
                        <dt>Penerbit</dt><dd>{{ $buku->penerbit ?: '—' }}</dd>
                        <dt>Tahun terbit</dt><dd>{{ $buku->tahun_terbit ?: '—' }}</dd>
                        <dt>Kategori</dt><dd>{{ $buku->kategori->nama }}</dd>
                        <dt>Lokasi rak</dt><dd>{{ $buku->rak ?: '—' }}</dd>
                    </dl>

                    <h3 class="mb-1">Deskripsi</h3>
                    <p class="muted" style="max-width:68ch">{{ $buku->deskripsi ?: 'Deskripsi belum diisi oleh petugas perpustakaan.' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            @include('partials.icon', ['name' => 'clipboard', 'size' => 18])
            <h2 class="grow">Formulir pengajuan peminjaman</h2>
        </div>

        <div class="card-body">
            @if (! $pengguna)
                <div class="alert alert-info" style="margin-bottom:14px">
                    @include('partials.icon', ['name' => 'info', 'size' => 18])
                    <div>
                        Anda bisa melihat katalog tanpa akun, tetapi <strong>meminjam buku mengharuskan login</strong>
                        dengan NIS dan kata sandi Anda.
                    </div>
                </div>
                <a class="btn btn-lg" href="{{ route('siswa.pinjam', $buku) }}">
                    @include('partials.icon', ['name' => 'user', 'size' => 17]) Masuk untuk meminjam buku ini
                </a>
                <p class="hint mt-1">Setelah masuk, Anda langsung kembali ke halaman ini.</p>
            @elseif ($pengguna->isAdmin())
                <div class="alert alert-info" style="margin:0">
                    @include('partials.icon', ['name' => 'info', 'size' => 18])
                    <div>Akun petugas tidak dipakai untuk meminjam. Pengajuan dilakukan oleh akun siswa.</div>
                </div>
            @elseif ($pengajuanAktif)
                <div class="alert alert-info" style="margin:0">
                    @include('partials.icon', ['name' => 'info', 'size' => 18])
                    <div>
                        Anda sudah punya pengajuan aktif untuk buku ini.
                        <a href="{{ route('siswa.riwayat') }}">Lihat statusnya di riwayat</a>.
                    </div>
                </div>
            @elseif (! $buku->tersedia())
                <div class="alert alert-warning" style="margin:0">
                    @include('partials.icon', ['name' => 'alert', 'size' => 18])
                    <div>Seluruh eksemplar sedang dipinjam. Silakan cek lagi setelah ada yang dikembalikan.</div>
                </div>
            @else
                <form method="POST" action="{{ route('siswa.ajukan', $buku) }}">
                    @csrf

                    @php
                        $default = now()->toDateString();
                        $tempo = \Carbon\Carbon::parse($default)->addDays((int) config('sppku.durasi_pinjam'));
                        $batasJam = (int) config('sppku.batas_ambil_jam');
                        $bukaSekarang = \App\Support\JamOperasional::sedangBuka();
                        $ringkasJam = collect(\App\Support\JamOperasional::jadwal())
                            ->reject(fn ($j) => $j['libur'])
                            ->map(fn ($j) => $j['hari'].' '.$j['buka'].'–'.$j['tutup'])
                            ->implode('; ');
                    @endphp

                    <div class="alert alert-warning">
                        @include('partials.icon', ['name' => 'clock', 'size' => 18])
                        <div>
                            <strong>Ambil buku di perpustakaan dalam {{ $batasJam }} jam.</strong>
                            Pengajuan yang tidak diambil sampai batas itu akan <strong>ditolak otomatis</strong>.
                            @unless ($bukaSekarang)
                                <br>Perpustakaan sedang tutup — {{ \App\Support\JamOperasional::status()['detail'] }}
                            @endunless
                            <div class="small mt-1">Jam buka: {{ $ringkasJam }} WIB.</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field">
                            <label for="tanggal_pengajuan">Tanggal pengajuan</label>
                            <input id="tanggal_pengajuan" type="text" class="input" readonly
                                   value="{{ now()->translatedFormat('d F Y') }} (hari ini)">
                            <p class="hint">Ambil buku di perpustakaan sebelum {{ now()->addHours($batasJam)->translatedFormat('d F Y, H.i') }} WIB.</p>
                        </div>
                        <div class="field">
                            <label for="tempo">Perkiraan tanggal pengembalian</label>
                            <input id="tempo" type="text" class="input" readonly
                                   value="{{ $tempo->translatedFormat('d F Y') }} ({{ config('sppku.durasi_pinjam') }} hari)">
                            <p class="hint">Terlambat dikenai denda Rp {{ number_format(config('sppku.denda_per_hari'), 0, ',', '.') }} per hari.</p>
                        </div>
                    </div>

                    <div class="field">
                        <label for="catatan_siswa">Catatan atau keperluan peminjaman (opsional)</label>
                        <textarea id="catatan_siswa" name="catatan_siswa" class="textarea" maxlength="500"
                                  placeholder="Contoh: untuk referensi projek akhir semester.">{{ old('catatan_siswa') }}</textarea>
                    </div>

                    <button class="btn btn-lg" type="submit">Kirim pengajuan peminjaman</button>
                </form>
            @endif
        </div>
    </div>

    @if ($serupa->isNotEmpty())
        <h2 class="mt-3 mb-2">Buku lain di kategori {{ $buku->kategori->nama }}</h2>
        <div class="grid grid-books">
            @foreach ($serupa as $lain)
                @include('siswa.partials.kartu-buku', ['buku' => $lain])
            @endforeach
        </div>
    @endif
@endsection
