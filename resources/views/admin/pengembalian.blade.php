@extends('layouts.admin')
@section('judul', 'Pengembalian buku & kalkulasi denda')
@section('subjudul', 'Denda otomatis Rp '.number_format(config('sppku.denda_per_hari'), 0, ',', '.').' per hari keterlambatan')

@section('konten')
    @include('partials.errors')

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.pengembalian.index') }}" class="searchbar">
                <input type="search" name="kode" value="{{ $kode }}" class="input"
                       placeholder="Kode transaksi (TRX-000000001), kode eksemplar (RPL-002-001), atau nomor buku"
                       aria-label="Cari transaksi" autofocus>
                <button class="btn btn-lg" type="submit">
                    @include('partials.icon', ['name' => 'search', 'size' => 17]) Cari transaksi
                </button>
                @if ($kode)<a class="btn btn-lg btn-ghost" href="{{ route('admin.pengembalian.index') }}">Reset</a>@endif
            </form>
        </div>
    </div>

    @if ($kode && ! $ditemukan)
        <div class="alert alert-warning">
            @include('partials.icon', ['name' => 'alert', 'size' => 18])
            <div>Tidak ada peminjaman aktif dengan kode <strong>{{ $kode }}</strong>. Periksa kembali kode transaksi atau nomor bukunya.</div>
        </div>
    @endif

    @if ($ditemukan)
        @php
            $hariTerlambat = $ditemukan->hariTerlambat();
            $dendaBerjalan = $ditemukan->hitungDenda();
        @endphp
        <div class="card mb-3">
            <div class="card-head">
                @include('partials.icon', ['name' => 'return', 'size' => 18])
                <h2 class="grow">Rincian peminjaman ditemukan</h2>
                <span class="badge badge-dipinjam mono">{{ $ditemukan->kode_trx }}</span>
            </div>
            <div class="card-body">
                <div class="grid grid-4 mb-3">
                    <div>
                        <div class="label">Data peminjam</div>
                        <div class="strong">{{ $ditemukan->user->nama }}</div>
                        <div class="small muted">{{ $ditemukan->user->kelas }} · {{ $ditemukan->user->kode }}</div>
                    </div>
                    <div>
                        <div class="label">Data buku</div>
                        <div class="strong">{{ $ditemukan->buku->judul }}</div>
                        <div class="small mt-1">Kode buku: <span class="kode-buku">{{ $ditemukan->eksemplar?->kode ?? '—' }}</span></div>
                    </div>
                    <div>
                        <div class="label">Tanggal pinjam</div>
                        <div class="strong">{{ $ditemukan->tanggal_pengajuan->translatedFormat('d F Y') }}</div>
                    </div>
                    <div>
                        <div class="label">Jatuh tempo</div>
                        <div class="strong">{{ $ditemukan->tanggal_jatuh_tempo->translatedFormat('d F Y') }}</div>
                        @if ($ditemukan->jumlah_perpanjangan > 0)
                            <div class="small muted">termasuk 1 kali perpanjangan</div>
                        @endif
                    </div>
                </div>

                @if ($hariTerlambat > 0)
                    <div class="callout-denda mb-3">
                        @include('partials.icon', ['name' => 'coins', 'size' => 22])
                        <div>
                            <div class="strong">Terlambat {{ $hariTerlambat }} hari</div>
                            <div class="small">
                                {{ $hariTerlambat }} hari &times; Rp {{ number_format(config('sppku.denda_per_hari'), 0, ',', '.') }}
                                (dihitung otomatis sampai hari ini)
                            </div>
                        </div>
                        <div class="nominal">Rp {{ number_format($dendaBerjalan, 0, ',', '.') }}</div>
                    </div>
                @else
                    <div class="alert alert-success">
                        @include('partials.icon', ['name' => 'check-circle', 'size' => 18])
                        <div>Belum melewati jatuh tempo, sehingga tidak ada denda bila dikembalikan hari ini.</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.pengembalian.konfirmasi', $ditemukan) }}">
                    @csrf
                    <div class="form-row">
                        <div class="field">
                            <label for="tanggal_kembali">Tanggal buku diterima kembali</label>
                            <input id="tanggal_kembali" class="input" type="date" name="tanggal_kembali"
                                   value="{{ now()->toDateString() }}" required>
                            <p class="hint">Denda dihitung ulang mengikuti tanggal ini.</p>
                        </div>
                        <div class="field">
                            <label for="catatan_kembali">Catatan kondisi buku (opsional)</label>
                            <input id="catatan_kembali" class="input" type="text" name="catatan_admin" maxlength="500"
                                   placeholder="Contoh: sampul sedikit terlipat.">
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        <button class="btn btn-lg btn-success" type="submit">Konfirmasi pengembalian</button>
                        <a class="btn btn-lg btn-ghost" target="_blank"
                           href="{{ route('admin.pengembalian.kuitansi', $ditemukan) }}">
                            @include('partials.icon', ['name' => 'printer', 'size' => 17]) Cetak bukti pengembalian
                        </a>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-head">
            @include('partials.icon', ['name' => 'book', 'size' => 18])
            <h2 class="grow">Buku yang sedang dipinjam</h2>
        </div>

        @if ($sedangDipinjam->total() > 0)
            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr><th>Kode transaksi</th><th>Siswa</th><th>Buku &amp; kode</th><th>Jatuh tempo</th><th>Keterlambatan</th><th>Denda berjalan</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($sedangDipinjam as $p)
                            @php $telat = $p->hariTerlambat(); @endphp
                            <tr>
                                <td class="mono">{{ $p->kode_trx }}</td>
                                <td>
                                    <div class="strong">{{ $p->user->nama }}</div>
                                    <div class="small muted">{{ $p->user->kelas }}</div>
                                </td>
                                <td>
                                    <div>{{ $p->buku->judul }}</div>
                                    <div class="small mt-1"><span class="kode-buku">{{ $p->eksemplar?->kode ?? '—' }}</span></div>
                                </td>
                                <td class="nowrap">{{ $p->tanggal_jatuh_tempo->translatedFormat('d M Y') }}</td>
                                <td>
                                    @if ($telat > 0)
                                        <span class="badge badge-ditolak">{{ $telat }} hari</span>
                                    @else
                                        <span class="badge badge-selesai">Tepat waktu</span>
                                    @endif
                                </td>
                                <td class="nowrap">
                                    @if ($telat > 0)
                                        <span style="color:var(--danger);font-weight:600">Rp {{ number_format($p->hitungDenda(), 0, ',', '.') }}</span>
                                    @else
                                        <span class="muted">Rp 0</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-ghost"
                                       href="{{ route('admin.pengembalian.index', ['kode' => $p->kode_trx]) }}">Proses</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $sedangDipinjam->links() }}
        @else
            <div class="empty">
                <div class="icon">@include('partials.icon', ['name' => 'check-circle', 'size' => 40, 'stroke' => 1.2])</div>
                <h3>Semua buku sudah kembali</h3>
                <p>Tidak ada eksemplar yang sedang berada di tangan siswa.</p>
            </div>
        @endif
    </div>
@endsection
