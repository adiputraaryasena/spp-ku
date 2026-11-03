@extends('layouts.siswa')
@section('judul', 'Riwayat peminjaman')

@section('konten')
    <div class="page-head">
        <div class="grow">
            <h1>Riwayat transaksi peminjaman</h1>
            <p>Pantau status pengajuan, batas pengembalian, dan denda Anda.</p>
        </div>
        <a class="btn btn-ghost" href="{{ route('siswa.katalog') }}">Cari buku lain</a>
    </div>

    @include('partials.pengingat-ambil', ['menunggu' => $menunggu])

    <div class="card">
        <div class="card-head" style="padding-bottom:0">
            <nav class="tabs grow" style="border:0">
                @foreach (['semua' => 'Semua', 'dipinjam' => 'Dipinjam', 'pending' => 'Pending', 'ditolak' => 'Ditolak', 'selesai' => 'Selesai'] as $key => $label)
                    <a href="{{ route('siswa.riwayat', ['status' => $key]) }}"
                       @class(['active' => $status === $key])>{{ $label }}</a>
                @endforeach
            </nav>
        </div>

        @if ($peminjamans->total() > 0)
            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr>
                            <th>Kode transaksi</th>
                            <th>Judul buku</th>
                            <th>Tanggal ajuan</th>
                            <th>Jatuh tempo</th>
                            <th>Status</th>
                            <th>Denda</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($peminjamans as $p)
                            <tr>
                                <td class="mono">{{ $p->kode_trx }}</td>
                                <td>
                                    <div class="strong">{{ $p->buku->judul }}</div>
                                    @if ($p->eksemplar)
                                        <div class="small mt-1">Kode buku: <span class="kode-buku">{{ $p->eksemplar->kode }}</span></div>
                                    @elseif ($p->status === 'pending')
                                        <div class="small muted mt-1">Kode buku ditetapkan saat disetujui</div>
                                    @else
                                        <div class="small muted mono">{{ $p->buku->nomor_buku }}</div>
                                    @endif
                                    @if ($p->catatan_admin)
                                        <div class="small muted mt-1" style="max-width:38ch">Catatan petugas: {{ $p->catatan_admin }}</div>
                                    @endif
                                </td>
                                <td class="nowrap">{{ $p->tanggal_pengajuan->translatedFormat('d M Y') }}</td>
                                <td class="nowrap">
                                    {{ $p->tanggal_jatuh_tempo->translatedFormat('d M Y') }}
                                    @if ($p->jumlah_perpanjangan > 0)
                                        <div class="small muted">sudah diperpanjang</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $p->status }}">{{ $p->labelStatus() }}</span>
                                    @if ($p->status === 'pending' && $p->batasAmbil())
                                        <div class="small mt-1" style="color:var(--warning)">
                                            Ambil sebelum {{ $p->batasAmbil()->translatedFormat('d M, H.i') }} WIB
                                        </div>
                                    @endif
                                    @if ($p->perluDiingatkan() && ! $p->terlambat())
                                        <div class="small" style="color:var(--warning);margin-top:4px;font-weight:600">
                                            {{ $p->sisaHari() === 0 ? 'Jatuh tempo hari ini' : ($p->sisaHari() === 1 ? 'Jatuh tempo besok' : $p->sisaHari().' hari lagi') }}
                                        </div>
                                    @endif
                                    @if ($p->terlambat())
                                        <div class="small" style="color:var(--danger);margin-top:4px">
                                            Terlambat {{ $p->hariTerlambat() }} hari
                                        </div>
                                    @endif
                                </td>
                                <td class="nowrap">
                                    @php $estimasi = $p->status === 'selesai' ? $p->denda : $p->hitungDenda(); @endphp
                                    @if ($estimasi > 0)
                                        <span style="color:var(--danger);font-weight:600">Rp {{ number_format($estimasi, 0, ',', '.') }}</span>
                                        @if ($p->status !== 'selesai')<div class="small muted">estimasi hari ini</div>@endif
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="aksi">
                                        @if ($p->bolehDiperpanjang())
                                            <form method="POST" action="{{ route('siswa.perpanjang', $p) }}"
                                                  data-konfirmasi="Perpanjang jatuh tempo {{ config('sppku.durasi_perpanjangan') }} hari?">
                                                @csrf
                                                <button class="btn btn-sm btn-ghost" type="submit">Perpanjang</button>
                                            </form>
                                        @endif
                                        @if (in_array($p->status, ['dipinjam', 'selesai'], true))
                                            <a class="btn btn-sm btn-ghost" target="_blank" href="{{ route('siswa.cetak', $p) }}">Cetak</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $peminjamans->links() }}
        @else
            <div class="empty">
                <div class="icon">@include('partials.icon', ['name' => 'inbox', 'size' => 40, 'stroke' => 1.2])</div>
                <h3>Belum ada transaksi di kategori ini</h3>
                <p>Pengajuan peminjaman Anda akan muncul di sini beserta status verifikasinya.</p>
                <a class="btn" href="{{ route('siswa.katalog') }}">Telusuri katalog</a>
            </div>
        @endif
    </div>
@endsection
