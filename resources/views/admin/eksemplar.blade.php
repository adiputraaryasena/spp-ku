@extends('layouts.admin')
@section('judul', 'Kode eksemplar')
@section('subjudul', $buku->judul)

@section('konten')
    @include('partials.errors')

    <div style="margin-bottom: 1rem;">
        <a href="{{ route('admin.buku.index') }}" class="btn btn-sm btn-ghost">
            &larr; Kembali ke master data buku
        </a>
    </div>

    <div class="grid grid-3 mb-3">
        <div class="stat">
            <div class="label">Rentang kode</div>
            <div class="value mono" style="font-size:16px;line-height:26px;overflow-wrap:anywhere">{{ $buku->teksRentang() }}</div>
            <div class="hint">Nomor buku {{ $buku->nomor_buku }}</div>
        </div>
        <div class="stat is-success">
            <div class="label">Tersedia</div>
            <div class="value">{{ $buku->jumlah_tersedia }}</div>
            <div class="hint">dari {{ $buku->jumlah_total }} eksemplar aktif</div>
        </div>
        <div class="stat is-warning">
            <div class="label">Dipinjam berikutnya</div>
            @php $berikut = $eksemplars->firstWhere('status', 'tersedia'); @endphp
            <div class="value mono" style="font-size:18px;line-height:26px">{{ $berikut?->kode ?? '—' }}</div>
            <div class="hint">Selalu kode terkecil yang tersedia</div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-head">
            @include('partials.icon', ['name' => 'plus', 'size' => 18])
            <h2 class="grow">Tambah rentang kode</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.buku.eksemplar.store', $buku) }}" data-rentang="tambah-eks">
                @csrf
                <div class="form-row">
                    <div class="field">
                        <label for="e-awal">Dari kode</label>
                        <input id="e-awal" class="input mono" name="kode_awal" required maxlength="60" autocomplete="off"
                               placeholder="Contoh: {{ $buku->nomor_buku }}-011" value="{{ old('kode_awal') }}" data-rentang-awal>
                    </div>
                    <div class="field">
                        <label for="e-akhir">Sampai kode</label>
                        <input id="e-akhir" class="input mono" name="kode_akhir" required maxlength="60" autocomplete="off"
                               placeholder="Contoh: {{ $buku->nomor_buku }}-015" value="{{ old('kode_akhir') }}" data-rentang-akhir>
                    </div>
                </div>
                <p class="hint" data-rentang-info>
                    Kode yang sudah dipakai buku mana pun ditolak otomatis, jadi tidak akan ada kode ganda.
                </p>
                <button class="btn mt-1" type="submit">Tambahkan kode</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            @include('partials.icon', ['name' => 'book', 'size' => 18])
            <h2 class="grow">Daftar kode ({{ $eksemplars->count() }})</h2>
            @if ($eksemplars->where('status', '!=', 'nonaktif')->isNotEmpty())
                <div class="aksi-head">
                    <a class="btn btn-sm btn-soft" href="{{ route('admin.buku.barcode', $buku) }}" target="_blank" rel="noopener">
                        @include('partials.icon', ['name' => 'printer', 'size' => 15]) Cetak semua (A4)
                    </a>
                    <a class="btn btn-sm btn-ghost" href="{{ route('admin.buku.barcode', [$buku, 'mode' => 'kecil']) }}" target="_blank" rel="noopener">
                        Label kecil
                    </a>
                </div>
            @endif
        </div>

        @if ($eksemplars->isNotEmpty())
            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr><th>Kode</th><th>Status</th><th>Dipinjam oleh</th><th>Riwayat</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($eksemplars as $e)
                            <tr>
                                <td><span class="kode-buku">{{ $e->kode }}</span></td>
                                <td><span class="badge {{ $e->kelasBadge() }}">{{ $e->labelStatus() }}</span></td>
                                <td>
                                    @if ($e->peminjamanAktif)
                                        <div class="strong">{{ $e->peminjamanAktif->user->nama }}</div>
                                        <div class="small muted">
                                            {{ $e->peminjamanAktif->user->kelas }} · tempo {{ $e->peminjamanAktif->tanggal_jatuh_tempo->translatedFormat('d M Y') }}
                                        </div>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $e->peminjamans_count }} transaksi</td>
                                <td>
                                    <div class="aksi">
                                        <a class="btn btn-sm btn-soft" href="{{ route('admin.buku.barcode', [$buku, 'eksemplar' => $e->id, 'mode' => 'kecil']) }}"
                                           target="_blank" rel="noopener" title="Cetak barcode kode {{ $e->kode }}">
                                            @include('partials.icon', ['name' => 'printer', 'size' => 15]) Barcode
                                        </a>
                                        @if ($e->status !== 'dipinjam')
                                            <form method="POST" action="{{ route('admin.eksemplar.update', $e) }}"
                                                  data-konfirmasi="{{ $e->status === 'nonaktif' ? 'Aktifkan kembali kode '.$e->kode.'?' : 'Nonaktifkan kode '.$e->kode.' (rusak/hilang)? Kode ini tidak akan dipinjamkan.' }}">
                                                @csrf @method('PUT')
                                                <button class="btn btn-sm btn-ghost" type="submit">
                                                    {{ $e->status === 'nonaktif' ? 'Aktifkan' : 'Nonaktifkan' }}
                                                </button>
                                            </form>
                                        @endif
                                        @if ($e->status !== 'dipinjam' && $e->peminjamans_count === 0)
                                            <form method="POST" action="{{ route('admin.eksemplar.destroy', $e) }}"
                                                  data-konfirmasi="Hapus kode {{ $e->kode }}?">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty">
                <div class="icon">@include('partials.icon', ['name' => 'book', 'size' => 40, 'stroke' => 1.2])</div>
                <h3>Belum ada kode eksemplar</h3>
                <p>Tambahkan rentang kode di atas agar buku ini bisa dipinjam.</p>
            </div>
        @endif
    </div>
@endsection
