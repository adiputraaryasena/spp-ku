@extends('layouts.admin')
@section('judul', 'Kategori koleksi')
@section('subjudul', 'Pengelompokan buku yang dipakai pada filter katalog siswa')

@section('konten')
    @include('partials.errors')

    <div class="split">
        <div class="card">
            <div class="card-head">
                @include('partials.icon', ['name' => 'tag', 'size' => 18])
                <h2 class="grow">Daftar kategori</h2>
            </div>
            <div class="table-wrap">
                <table class="data">
                    <thead><tr><th>Nama</th><th>Keterangan</th><th>Jumlah buku</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @forelse ($kategoris as $k)
                            <tr>
                                <td class="strong">{{ $k->nama }}</td>
                                <td class="muted">{{ $k->keterangan ?: '—' }}</td>
                                <td>{{ $k->bukus_count }}</td>
                                <td>
                                    <div class="aksi">
                                        <button class="btn btn-sm btn-ghost" data-modal="modal-kategori-ubah"
                                                data-isi-json="{{ json_encode(['nama' => $k->nama, 'keterangan' => $k->keterangan, '__action' => route('admin.kategori.update', $k)]) }}">
                                            Ubah
                                        </button>
                                        <form method="POST" action="{{ route('admin.kategori.destroy', $k) }}"
                                              data-konfirmasi="Hapus kategori {{ $k->nama }}?">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="muted center" style="padding:28px">Belum ada kategori.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $kategoris->links() }}
        </div>

        <div class="card" style="align-self:start">
            <div class="card-head">
                @include('partials.icon', ['name' => 'plus', 'size' => 18])
                <h2 class="grow">Tambah kategori</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.kategori.store') }}">
                    @csrf
                    <div class="field">
                        <label for="nama">Nama kategori</label>
                        <input id="nama" class="input" type="text" name="nama" required maxlength="80"
                               placeholder="Contoh: Rekayasa Perangkat Lunak">
                    </div>
                    <div class="field">
                        <label for="keterangan">Keterangan</label>
                        <textarea id="keterangan" class="textarea" name="keterangan" maxlength="255"
                                  placeholder="Penjelasan singkat isi kategori."></textarea>
                    </div>
                    <button class="btn btn-block" type="submit">Simpan kategori</button>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="modal-kategori-ubah" hidden>
        <div class="modal-card">
            <form method="POST" action="" data-action>
                @csrf @method('PUT')
                <div class="modal-head">
                    <h2>Ubah kategori</h2>
                    <button type="button" data-tutup aria-label="Tutup">@include('partials.icon', ['name' => 'x', 'size' => 18])</button>
                </div>
                <div class="modal-body">
                    <div class="field">
                        <label>Nama kategori</label>
                        <input class="input" type="text" name="nama" data-isi="nama" required maxlength="80">
                    </div>
                    <div class="field">
                        <label>Keterangan</label>
                        <textarea class="textarea" name="keterangan" data-isi="keterangan" maxlength="255"></textarea>
                    </div>
                </div>
                <div class="modal-foot">
                    <button class="btn btn-ghost" type="button" data-tutup>Batal</button>
                    <button class="btn" type="submit">Simpan perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection