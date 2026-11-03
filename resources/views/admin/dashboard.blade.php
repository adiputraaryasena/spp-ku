@extends('layouts.admin')
@section('judul', 'Dashboard ringkasan perpustakaan')
@section('subjudul', 'Kondisi koleksi dan transaksi per '.now()->translatedFormat('d F Y'))

@section('konten')
    <div class="grid grid-4 mb-3">
        <div class="stat">
            <div class="label">Total judul buku</div>
            <div class="value">{{ number_format($statistik['judul_buku'], 0, ',', '.') }}</div>
            <div class="hint">{{ number_format($statistik['eksemplar'], 0, ',', '.') }} eksemplar terdaftar</div>
        </div>
        <div class="stat is-success">
            <div class="label">Siswa terdaftar</div>
            <div class="value">{{ number_format($statistik['siswa'], 0, ',', '.') }}</div>
            <div class="hint">Punya akses katalog mandiri</div>
        </div>
        <div class="stat is-warning">
            <div class="label">Perlu verifikasi</div>
            <div class="value">{{ number_format($statistik['perlu_verifikasi'], 0, ',', '.') }}</div>
            <div class="hint">Pengajuan menunggu tanggapan</div>
        </div>
        <div class="stat">
            <div class="label">Total transaksi</div>
            <div class="value">{{ number_format($statistik['total_transaksi'], 0, ',', '.') }}</div>
            <div class="hint">{{ $statistik['sedang_dipinjam'] }} buku sedang dipinjam</div>
        </div>
    </div>

    @if ($terlambat->isNotEmpty())
        <div class="alert alert-danger">
            @include('partials.icon', ['name' => 'alert', 'size' => 18])
            <div>
                <strong>{{ $terlambat->count() }} peminjaman melewati jatuh tempo.</strong>
                Denda berjalan Rp {{ number_format($terlambat->sum(fn ($p) => $p->hitungDenda()), 0, ',', '.') }}.
                <a href="{{ route('admin.pengembalian.index') }}">Proses pengembalian</a>.
            </div>
        </div>
    @endif

    <div class="split">
        <div class="card">
            <div class="card-head">
                @include('partials.icon', ['name' => 'clipboard', 'size' => 18])
                <h2 class="grow">Antrean approval terbaru</h2>
                <a class="btn btn-sm btn-ghost" href="{{ route('admin.approval.index') }}">Buka halaman approval</a>
            </div>

            @if ($antrean->isNotEmpty())
                <div class="table-wrap">
                    <table class="data">
                        <thead>
                            <tr><th>Kode</th><th>Siswa</th><th>Judul buku</th><th>Tgl ajuan</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($antrean as $p)
                                <tr>
                                    <td class="mono">{{ $p->kode_trx }}</td>
                                    <td>
                                        <div class="strong">{{ $p->user->nama }}</div>
                                        <div class="small muted">{{ $p->user->kelas }}</div>
                                    </td>
                                    <td>{{ $p->buku->judul }}</td>
                                    <td class="nowrap">{{ $p->tanggal_pengajuan->translatedFormat('d M Y') }}</td>
                                    <td><span class="badge badge-pending">Pending</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty">
                    <div class="icon">@include('partials.icon', ['name' => 'check-circle', 'size' => 40, 'stroke' => 1.2])</div>
                    <h3>Antrean kosong</h3>
                    <p>Semua pengajuan siswa sudah diverifikasi.</p>
                </div>
            @endif
        </div>

        <div class="card" style="align-self:start">
            <div class="card-head">
                @include('partials.icon', ['name' => 'chart', 'size' => 18])
                <h2 class="grow">Buku paling sering dipinjam</h2>
            </div>
            <div class="card-body">
                @forelse ($bukuPopuler as $i => $buku)
                    <div style="display:flex;gap:12px;align-items:center;padding:9px 0;border-bottom:1px solid var(--line-soft)">
                        <span class="badge badge-sm">{{ $i + 1 }}</span>
                        <div style="flex:1;min-width:0">
                            <div class="strong" style="font-size:13px">{{ $buku->judul }}</div>
                            <div class="small muted mono">{{ $buku->nomor_buku }}</div>
                        </div>
                        <span class="small muted nowrap">{{ $buku->peminjamans_count }}&times;</span>
                    </div>
                @empty
                    <p class="muted">Belum ada data peminjaman.</p>
                @endforelse

                <div class="mt-2 small muted">
                    Denda terkumpul sepanjang waktu:
                    <strong style="color:var(--ink)">Rp {{ number_format($statistik['denda_terkumpul'], 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>
@endsection
