@extends('layouts.admin')
@section('judul', 'Kelola master data buku')
@section('subjudul', 'Nomor buku dan setiap kode eksemplar bersifat unik agar tidak terjadi data ganda')

@section('konten')
    @include('partials.errors')

    <div class="card">
        <div class="card-head">
            <form method="GET" action="{{ route('admin.buku.index') }}" class="searchbar grow">
                <input type="search" name="cari" value="{{ $kata }}" class="input"
                       placeholder="Cari judul, pengarang, atau nomor buku" aria-label="Cari buku">
                <button class="btn" type="submit">Cari</button>
                @if ($kata)<a class="btn btn-ghost" href="{{ route('admin.buku.index') }}">Reset</a>@endif
            </form>
            <button class="btn" data-modal="modal-buku-tambah">
                @include('partials.icon', ['name' => 'plus', 'size' => 17]) Tambah buku baru
            </button>
        </div>

        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>Nomor buku</th><th>Judul</th><th>Pengarang</th><th>Kategori</th>
                        <th>Kode eksemplar</th><th>Stok</th><th>Rak</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bukus as $buku)
                        <tr>
                            <td class="mono">{{ $buku->nomor_buku }}</td>
                            <td>
                                <div class="strong">{{ $buku->judul }}</div>
                                <div class="small muted">{{ $buku->penerbit }} {{ $buku->tahun_terbit ? '· '.$buku->tahun_terbit : '' }}</div>
                            </td>
                            <td>{{ $buku->pengarang ?: '—' }}</td>
                            <td><span class="badge badge-sm">{{ $buku->kategori->nama }}</span></td>
                            <td>
                                <span class="mono">{{ $buku->teksRentang() }}</span>
                            </td>
                            <td class="nowrap">
                                <span class="strong">{{ $buku->jumlah_tersedia }}</span>
                                <span class="muted">/ {{ $buku->jumlah_total }}</span>
                            </td>
                            <td>{{ $buku->rak ?: '—' }}</td>
                            <td>
                                <div class="aksi">
                                    <a class="btn btn-sm btn-ghost" href="{{ route('admin.buku.eksemplar', $buku) }}">Kode</a>
                                    <button class="btn btn-sm btn-ghost" data-modal="modal-buku-ubah"
                                            data-isi-json="{{ json_encode([
                                                'nomor_buku' => $buku->nomor_buku,
                                                'judul' => $buku->judul,
                                                'pengarang' => $buku->pengarang,
                                                'penerbit' => $buku->penerbit,
                                                'tahun_terbit' => $buku->tahun_terbit,
                                                'kategori_id' => $buku->kategori_id,
                                                'rak' => $buku->rak,
                                                'deskripsi' => $buku->deskripsi,
                                                '__action' => route('admin.buku.update', $buku),
                                            ]) }}">Ubah</button>
                                    <form method="POST" action="{{ route('admin.buku.destroy', $buku) }}"
                                          data-konfirmasi="Hapus buku {{ $buku->judul }} dari master data?">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">
                            <div class="empty">
                                <div class="icon">@include('partials.icon', ['name' => 'book', 'size' => 40, 'stroke' => 1.2])</div>
                                <h3>Belum ada buku yang cocok</h3>
                                <p>Tambahkan koleksi baru atau ubah kata kunci pencarian.</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $bukus->links() }}
    </div>

    @if ($errors->any() && old('_mode') === 'tambah')
        <span data-buka-otomatis="modal-buku-tambah" class="hide"></span>
    @endif

    {{-- Modal tambah --}}
    <div class="modal" id="modal-buku-tambah" hidden>
        <div class="modal-card wide">
            <form method="POST" action="{{ route('admin.buku.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_mode" value="tambah">
                <div class="modal-head">
                    <h2>Tambah master data buku</h2>
                    <button type="button" data-tutup aria-label="Tutup">@include('partials.icon', ['name' => 'x', 'size' => 18])</button>
                </div>
                <div class="modal-body">
                    @include('admin.partials.form-buku', ['kategoris' => $kategoris, 'prefix' => 'tambah'])
                </div>
                <div class="modal-foot">
                    <button class="btn btn-ghost" type="button" data-tutup>Batal</button>
                    <button class="btn" type="submit">Simpan buku</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal ubah --}}
    <div class="modal" id="modal-buku-ubah" hidden>
        <div class="modal-card wide">
            <form method="POST" action="" data-action enctype="multipart/form-data">
                @csrf @method('PUT')
                <input type="hidden" name="_mode" value="ubah">
                <div class="modal-head">
                    <h2>Ubah data buku</h2>
                    <button type="button" data-tutup aria-label="Tutup">@include('partials.icon', ['name' => 'x', 'size' => 18])</button>
                </div>
                <div class="modal-body">
                    @include('admin.partials.form-buku', ['kategoris' => $kategoris, 'prefix' => 'ubah', 'isiData' => true])
                </div>
                <div class="modal-foot">
                    <button class="btn btn-ghost" type="button" data-tutup>Batal</button>
                    <button class="btn" type="submit">Simpan perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection