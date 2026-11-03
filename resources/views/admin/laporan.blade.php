@extends('layouts.admin')
@section('judul', 'Cetak laporan & rekap perpustakaan')
@section('subjudul', 'Periode '.$dari->translatedFormat('d F Y').' sampai '.$sampai->translatedFormat('d F Y'))

@section('konten')
    @include('partials.errors')

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.laporan.index') }}">
                <div class="searchbar" style="align-items:flex-end">
                    <div class="field" style="flex:1 1 170px;margin:0">
                        <label for="dari">Dari tanggal</label>
                        <input id="dari" class="input" type="date" name="dari" value="{{ $dari->toDateString() }}">
                    </div>
                    <div class="field" style="flex:1 1 170px;margin:0">
                        <label for="sampai">Sampai tanggal</label>
                        <input id="sampai" class="input" type="date" name="sampai" value="{{ $sampai->toDateString() }}">
                    </div>
                    <div class="field" style="flex:1 1 170px;margin:0">
                        <label for="status">Status transaksi</label>
                        <select id="status" class="select" name="status">
                            @foreach (['semua' => 'Semua status', 'pending' => 'Pending', 'dipinjam' => 'Dipinjam', 'ditolak' => 'Ditolak', 'selesai' => 'Selesai'] as $k => $label)
                                <option value="{{ $k }}" @selected($status === $k)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-lg" type="submit">Terapkan filter</button>
                </div>
            </form>

            <div class="mt-2" style="display:flex;gap:8px;flex-wrap:wrap">
                @php $params = ['dari' => $dari->toDateString(), 'sampai' => $sampai->toDateString(), 'status' => $status]; @endphp
                <a class="btn btn-ghost" target="_blank" href="{{ route('admin.laporan.cetak', $params) }}">
                    @include('partials.icon', ['name' => 'printer', 'size' => 17]) Cetak / simpan PDF
                </a>
                <a class="btn btn-ghost" href="{{ route('admin.laporan.excel', $params) }}">
                    @include('partials.icon', ['name' => 'sheet', 'size' => 17]) Export Excel (CSV)
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-4 mb-3">
        <div class="stat">
            <div class="label">Total transaksi</div>
            <div class="value">{{ number_format($ringkasan['total'], 0, ',', '.') }}</div>
        </div>
        <div class="stat is-success">
            <div class="label">Selesai dikembalikan</div>
            <div class="value">{{ number_format($ringkasan['selesai'], 0, ',', '.') }}</div>
        </div>
        <div class="stat is-warning">
            <div class="label">Masih dipinjam</div>
            <div class="value">{{ number_format($ringkasan['dipinjam'], 0, ',', '.') }}</div>
        </div>
        <div class="stat is-danger">
            <div class="label">Total denda</div>
            <div class="value">Rp {{ number_format($ringkasan['denda'], 0, ',', '.') }}</div>
            <div class="hint">{{ $ringkasan['ditolak'] }} pengajuan ditolak</div>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            @include('partials.icon', ['name' => 'chart', 'size' => 18])
            <h2 class="grow">Rincian transaksi</h2>
        </div>

        @if ($peminjamans->total() > 0)
            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr>
                            <th>Kode</th><th>Nama siswa</th><th>Kelas</th><th>Judul buku</th>
                            <th>Tgl pinjam</th><th>Jatuh tempo</th><th>Tgl kembali</th><th>Status</th><th>Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($peminjamans as $p)
                            <tr>
                                <td class="mono">{{ $p->kode_trx }}</td>
                                <td class="strong">{{ $p->user->nama }}</td>
                                <td>{{ $p->user->kelas ?: '—' }}</td>
                                <td>
                                    <div>{{ $p->buku->judul }}</div>
                                    <div class="small mt-1">Kode buku: <span class="kode-buku">{{ $p->eksemplar?->kode ?? '—' }}</span></div>
                                </td>
                                <td class="nowrap">{{ $p->tanggal_pengajuan->translatedFormat('d M Y') }}</td>
                                <td class="nowrap">{{ $p->tanggal_jatuh_tempo->translatedFormat('d M Y') }}</td>
                                <td class="nowrap">{{ $p->tanggal_kembali?->translatedFormat('d M Y') ?? '—' }}</td>
                                <td><span class="badge badge-{{ $p->status }}">{{ $p->labelStatus() }}</span></td>
                                <td class="nowrap">
                                    @if ($p->denda > 0)
                                        <span style="color:var(--danger);font-weight:600">Rp {{ number_format($p->denda, 0, ',', '.') }}</span>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $peminjamans->links() }}
        @else
            <div class="empty">
                <div class="icon">@include('partials.icon', ['name' => 'chart', 'size' => 40, 'stroke' => 1.2])</div>
                <h3>Tidak ada transaksi pada periode ini</h3>
                <p>Lebarkan rentang tanggal atau ubah filter status untuk melihat data lain.</p>
            </div>
        @endif
    </div>
@endsection
