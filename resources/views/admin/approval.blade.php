@extends('layouts.admin')
@section('judul', 'Verifikasi & approval peminjaman')
@section('subjudul', $jumlahPending.' pengajuan menunggu tanggapan petugas')

@section('konten')
    @include('partials.errors')

    <div class="card">
        <div class="card-head" style="padding-bottom:0">
            <nav class="tabs grow" style="border:0">
                @foreach (['pending' => 'Menunggu verifikasi', 'dipinjam' => 'Disetujui', 'ditolak' => 'Ditolak', 'selesai' => 'Selesai', 'semua' => 'Semua'] as $key => $label)
                    <a href="{{ route('admin.approval.index', ['status' => $key]) }}"
                       @class(['active' => $status === $key])>{{ $label }}</a>
                @endforeach
            </nav>
        </div>

        @if ($peminjamans->total() > 0)
            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr>
                            <th>Kode</th><th>Siswa &amp; kelas</th><th>Buku</th>
                            <th>Tgl ajuan</th><th>Jatuh tempo</th><th>Catatan siswa</th><th>Aksi / verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($peminjamans as $p)
                            <tr>
                                <td class="mono">{{ $p->kode_trx }}</td>
                                <td>
                                    <div class="strong">{{ $p->user->nama }}</div>
                                    <div class="small muted">{{ $p->user->kelas }} · {{ $p->user->kode }}</div>
                                </td>
                                <td>
                                    <div class="strong">{{ $p->buku->judul }}</div>
                                    <div class="small muted mono">{{ $p->buku->nomor_buku }} · sisa {{ $p->buku->jumlah_tersedia }} eks.</div>
                                    @if ($p->eksemplar)
                                        <div class="small mt-1">Kode buku: <span class="kode-buku">{{ $p->eksemplar->kode }}</span></div>
                                    @elseif ($p->status === 'pending')
                                        @php $berikut = $p->buku->eksemplarBerikutnya(); @endphp
                                        <div class="small mt-1">
                                            Kode yang diserahkan:
                                            @if ($berikut)<span class="kode-buku">{{ $berikut->kode }}</span>@else<span style="color:var(--danger)">stok habis</span>@endif
                                        </div>
                                    @endif
                                </td>
                                <td class="nowrap">
                                    {{ $p->tanggal_pengajuan->translatedFormat('d M Y') }}
                                    @if ($p->status === 'pending' && $p->batasAmbil())
                                        <div class="small" style="color:var(--warning)">batas ambil {{ $p->batasAmbil()->translatedFormat('d M, H.i') }}</div>
                                    @endif
                                </td>
                                <td class="nowrap">{{ $p->tanggal_jatuh_tempo->translatedFormat('d M Y') }}</td>
                                <td class="muted" style="max-width:26ch">{{ $p->catatan_siswa ?: '—' }}</td>
                                <td>
                                    @if ($p->status === 'pending')
                                        <div class="aksi">
                                            <button class="btn btn-sm btn-success" data-modal="modal-setujui"
                                                    data-isi-json="{{ json_encode([
                                                        'kode' => $p->kode_trx,
                                                        'ringkas' => $p->buku->judul.' — '.$p->user->nama,
                                                        '__action' => route('admin.approval.setujui', $p),
                                                    ]) }}">Setujui</button>
                                            <button class="btn btn-sm btn-danger" data-modal="modal-tolak"
                                                    data-isi-json="{{ json_encode([
                                                        'kode' => $p->kode_trx,
                                                        'ringkas' => $p->buku->judul.' — '.$p->user->nama,
                                                        '__action' => route('admin.approval.tolak', $p),
                                                    ]) }}">Tolak</button>
                                        </div>
                                    @else
                                        <span class="badge badge-{{ $p->status }}">{{ $p->labelStatus() }}</span>
                                        @if ($p->petugas)
                                            <div class="small muted mt-1">oleh {{ $p->petugas->nama }}</div>
                                        @elseif ($p->status === 'ditolak' && $p->diverifikasi_pada)
                                            <div class="small muted mt-1">oleh sistem</div>
                                        @endif
                                        @if ($p->catatan_admin)
                                            <div class="small muted" style="max-width:26ch">{{ $p->catatan_admin }}</div>
                                        @endif
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
                <div class="icon">@include('partials.icon', ['name' => 'check-circle', 'size' => 40, 'stroke' => 1.2])</div>
                <h3>Tidak ada transaksi pada tab ini</h3>
                <p>Pengajuan baru dari siswa akan muncul di sini untuk diverifikasi.</p>
            </div>
        @endif
    </div>

    <div class="modal" id="modal-setujui" hidden>
        <div class="modal-card">
            <form method="POST" action="" data-action>
                @csrf
                <div class="modal-head">
                    @include('partials.icon', ['name' => 'check-circle', 'size' => 20])
                    <h2>Setujui pengajuan</h2>
                    <button type="button" data-tutup aria-label="Tutup">@include('partials.icon', ['name' => 'x', 'size' => 18])</button>
                </div>
                <div class="modal-body">
                    <p class="small muted">Transaksi <span class="mono" data-isi="kode"></span></p>
                    <p class="strong mb-2" data-isi="ringkas"></p>
                    <div class="alert alert-info">
                        @include('partials.icon', ['name' => 'info', 'size' => 18])
                        <div>Stok tersedia berkurang satu eksemplar dan status siswa berubah menjadi "Sedang dipinjam".</div>
                    </div>
                    <div class="field" style="margin-bottom:0">
                        <label>Catatan untuk siswa (opsional)</label>
                        <textarea class="textarea" name="catatan_admin" maxlength="500"
                                  placeholder="Contoh: Ambil buku di meja sirkulasi mulai pukul 08.00."></textarea>
                    </div>
                </div>
                <div class="modal-foot">
                    <button class="btn btn-ghost" type="button" data-tutup>Batal</button>
                    <button class="btn btn-success" type="submit">Setujui peminjaman</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="modal-tolak" hidden>
        <div class="modal-card">
            <form method="POST" action="" data-action>
                @csrf
                <div class="modal-head">
                    @include('partials.icon', ['name' => 'x-circle', 'size' => 20])
                    <h2>Tolak pengajuan</h2>
                    <button type="button" data-tutup aria-label="Tutup">@include('partials.icon', ['name' => 'x', 'size' => 18])</button>
                </div>
                <div class="modal-body">
                    <p class="small muted">Transaksi <span class="mono" data-isi="kode"></span></p>
                    <p class="strong mb-2" data-isi="ringkas"></p>
                    <div class="field" style="margin-bottom:0">
                        <label>Alasan penolakan</label>
                        <textarea class="textarea" name="catatan_admin" required maxlength="500"
                                  placeholder="Jelaskan alasannya agar siswa tahu langkah berikutnya."></textarea>
                        <p class="hint">Alasan ini tampil di halaman riwayat siswa.</p>
                    </div>
                </div>
                <div class="modal-foot">
                    <button class="btn btn-ghost" type="button" data-tutup>Batal</button>
                    <button class="btn btn-danger" type="submit">Tolak pengajuan</button>
                </div>
            </form>
        </div>
    </div>
@endsection