@extends('layouts.admin')
@section('judul', 'Kelola pengguna')
@section('subjudul', 'Akun siswa dan petugas perpustakaan beserta hak aksesnya')

@section('konten')
    @include('partials.errors')

    @if (session('impor_hasil'))
        @php $hasil = session('impor_hasil'); @endphp
        <div class="alert {{ $hasil['dibuat'] > 0 ? 'alert-success' : 'alert-danger' }}">
            @include('partials.icon', ['name' => $hasil['dibuat'] > 0 ? 'check-circle' : 'alert', 'size' => 18])
            <div style="min-width:0">
                <strong>Hasil impor massal:</strong>
                {{ $hasil['dibuat'] }} dari {{ $hasil['total'] }} baris berhasil dibuat menjadi akun siswa.
                @if (count($hasil['dilewati']))
                    <details class="mt-1" open>
                        <summary class="strong">{{ count($hasil['dilewati']) }} baris dilewati</summary>
                        <div class="table-wrap">
                            <table class="data" style="min-width:0">
                                <thead><tr><th>Baris</th><th>NIS</th><th>Nama</th><th>Alasan</th></tr></thead>
                                <tbody>
                                    @foreach ($hasil['dilewati'] as $l)
                                        <tr>
                                            <td>{{ $l['baris'] }}</td>
                                            <td class="mono">{{ $l['kode'] ?: '—' }}</td>
                                            <td>{{ $l['nama'] ?: '—' }}</td>
                                            <td>{{ $l['alasan'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                @endif
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-head">
            <form method="GET" action="{{ route('admin.pengguna.index') }}" class="searchbar grow">
                <input type="search" name="cari" value="{{ $kata }}" class="input"
                       placeholder="Cari nama, NIS, kelas, atau telepon" aria-label="Cari pengguna">
                <button class="btn" type="submit">Cari</button>
                @if ($kata)<a class="btn btn-ghost" href="{{ route('admin.pengguna.index') }}">Reset</a>@endif
            </form>
            <button class="btn btn-ghost" data-modal="modal-pengguna-impor">
                @include('partials.icon', ['name' => 'upload', 'size' => 17]) Impor massal
            </button>
            <button class="btn" data-modal="modal-pengguna-tambah">
                @include('partials.icon', ['name' => 'plus', 'size' => 17]) Tambah akun
            </button>
        </div>

        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr><th>NIS / Kode</th><th>Nama</th><th>Kelas</th><th>Peran</th><th>Transaksi</th><th>Kata sandi</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr>
                            <td class="mono">{{ $u->kode }}</td>
                            <td>
                                <div class="strong">{{ $u->nama }}</div>
                                <div class="small muted">{{ $u->email ?: 'tanpa email' }}@if ($u->telepon) · {{ $u->telepon }}@endif</div>
                            </td>
                            <td>{{ $u->kelas ?: '—' }}</td>
                            <td>
                                <span class="badge badge-sm {{ $u->isAdmin() ? 'badge-dipinjam' : '' }}">
                                    {{ $u->isAdmin() ? 'Petugas' : 'Siswa' }}
                                </span>
                            </td>
                            <td>{{ $u->peminjamans_count }}</td>
                            <td>
                                @if ($u->harus_ganti_password)
                                    <span class="badge badge-sm badge-pending">Sementara</span>
                                    <div class="small muted">belum diganti sendiri</div>
                                @else
                                    <span class="badge badge-sm badge-selesai">Pribadi</span>
                                    @if ($u->password_diganti_pada)
                                        <div class="small muted">{{ $u->password_diganti_pada->translatedFormat('d M Y') }}</div>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-sm {{ $u->aktif ? 'badge-selesai' : 'badge-ditolak' }}">
                                    {{ $u->aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="aksi">
                                    <button class="btn btn-sm btn-ghost" data-modal="modal-pengguna-ubah"
                                            data-isi-json="{{ json_encode([
                                                'kode' => $u->kode,
                                                'nama' => $u->nama,
                                                'email' => $u->email,
                                                'telepon' => $u->telepon,
                                                'kelas' => $u->kelas,
                                                'role' => $u->role,
                                                'aktif' => $u->aktif ? '1' : '0',
                                                '__action' => route('admin.pengguna.update', $u),
                                            ]) }}">Ubah</button>
                                    <form method="POST" action="{{ route('admin.pengguna.destroy', $u) }}"
                                          data-konfirmasi="Hapus akun {{ $u->nama }}?">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="muted center" style="padding:28px">Tidak ada pengguna yang cocok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>

    <div class="modal" id="modal-pengguna-tambah" hidden>
        <div class="modal-card">
            <form method="POST" action="{{ route('admin.pengguna.store') }}">
                @csrf
                <div class="modal-head">
                    <h2>Tambah akun pengguna</h2>
                    <button type="button" data-tutup aria-label="Tutup">@include('partials.icon', ['name' => 'x', 'size' => 18])</button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="field">
                            <label for="t-kode">NIS / kode petugas</label>
                            <input id="t-kode" class="input" type="text" name="kode" required maxlength="30" value="{{ old('kode') }}">
                        </div>
                        <div class="field">
                            <label for="t-role">Peran</label>
                            <select id="t-role" class="select" name="role" required>
                                <option value="siswa">Siswa</option>
                                <option value="admin">Petugas / Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label for="t-nama">Nama lengkap</label>
                        <input id="t-nama" class="input" type="text" name="nama" required maxlength="120" value="{{ old('nama') }}">
                    </div>
                    <div class="form-row">
                        <div class="field">
                            <label for="t-kelas">Kelas</label>
                            <input id="t-kelas" class="input" type="text" name="kelas" maxlength="30"
                                   placeholder="Contoh: XII RPL C" value="{{ old('kelas') }}">
                        </div>
                        <div class="field">
                            <label for="t-email">Email (opsional)</label>
                            <input id="t-email" class="input" type="email" name="email" maxlength="120" value="{{ old('email') }}">
                        </div>
                    </div>
                    <div class="field">
                        <label for="t-telepon">Nomor telepon (opsional)</label>
                        <input id="t-telepon" class="input" type="tel" name="telepon" maxlength="20"
                               inputmode="tel" placeholder="Contoh: 081234567890" value="{{ old('telepon') }}">
                    </div>
                    <div class="field" style="margin-bottom:0">
                        <label for="t-password">Kata sandi awal</label>
                        <input id="t-password" class="input" type="text" name="password" required minlength="6"
                               placeholder="Minimal 6 karakter">
                        <p class="hint">
                            Bersifat sementara. Saat pertama kali masuk, pengguna wajib menggantinya
                            dengan kata sandi pilihannya sendiri sebelum bisa membuka halaman lain.
                        </p>
                    </div>
                </div>
                <div class="modal-foot">
                    <button class="btn btn-ghost" type="button" data-tutup>Batal</button>
                    <button class="btn" type="submit">Buat akun</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="modal-pengguna-ubah" hidden>
        <div class="modal-card">
            <form method="POST" action="" data-action>
                @csrf @method('PUT')
                <div class="modal-head">
                    <h2>Ubah data pengguna</h2>
                    <button type="button" data-tutup aria-label="Tutup">@include('partials.icon', ['name' => 'x', 'size' => 18])</button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="field">
                            <label>NIS / kode petugas</label>
                            <input class="input" type="text" name="kode" data-isi="kode" required maxlength="30">
                        </div>
                        <div class="field">
                            <label>Peran</label>
                            <select class="select" name="role" data-isi="role" required>
                                <option value="siswa">Siswa</option>
                                <option value="admin">Petugas / Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label>Nama lengkap</label>
                        <input class="input" type="text" name="nama" data-isi="nama" required maxlength="120">
                    </div>
                    <div class="form-row">
                        <div class="field">
                            <label>Kelas</label>
                            <input class="input" type="text" name="kelas" data-isi="kelas" maxlength="30">
                        </div>
                        <div class="field">
                            <label>Email (opsional)</label>
                            <input class="input" type="email" name="email" data-isi="email" maxlength="120">
                        </div>
                    </div>
                    <div class="field">
                        <label>Nomor telepon (opsional)</label>
                        <input class="input" type="tel" name="telepon" data-isi="telepon" maxlength="20" inputmode="tel">
                    </div>
                    <div class="field">
                        <label>Setel kata sandi sementara (kosongkan bila tidak diubah)</label>
                        <input class="input" type="text" name="password" minlength="6" placeholder="Minimal 6 karakter">
                        <p class="hint">
                            Dipakai bila siswa lupa kata sandinya. Ia akan diminta membuat kata sandi
                            pribadi lagi saat login, sehingga petugas tetap tidak bisa memakai akunnya diam-diam.
                        </p>
                    </div>
                    <label class="check">
                        <input type="checkbox" name="aktif" value="1" checked>
                        Akun aktif dan boleh masuk ke sistem
                    </label>
                </div>
                <div class="modal-foot">
                    <button class="btn btn-ghost" type="button" data-tutup>Batal</button>
                    <button class="btn" type="submit">Simpan perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="modal-pengguna-impor" hidden @if (session('buka_impor')) data-buka-otomatis="modal-pengguna-impor" @endif>
        <div class="modal-card wide">
            <form method="POST" action="{{ route('admin.pengguna.impor') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-head">
                    <h2>Impor akun siswa massal</h2>
                    <button type="button" data-tutup aria-label="Tutup">@include('partials.icon', ['name' => 'x', 'size' => 18])</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        @include('partials.icon', ['name' => 'info', 'size' => 18])
                        <div>
                            Cocok untuk memasukkan satu angkatan/kelas baru sekaligus (maks. 500 akun).
                            Setiap akun dibuat sebagai <strong>siswa</strong> dengan kata sandi sementara yang sama,
                            dan wajib menggantinya saat pertama masuk.
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field">
                            <label for="i-kelas">Kelas bawaan (opsional)</label>
                            <input id="i-kelas" class="input" type="text" name="kelas" maxlength="30"
                                   placeholder="Contoh: X RPL A" value="{{ old('kelas') }}">
                            <p class="hint">Dipakai untuk baris yang tidak punya kolom kelas.</p>
                        </div>
                        <div class="field">
                            <label for="i-password">Kata sandi sementara</label>
                            <input id="i-password" class="input" type="text" name="password" required minlength="6"
                                   placeholder="Minimal 6 karakter" value="{{ old('password') }}">
                            <p class="hint">Sama untuk semua akun di daftar ini.</p>
                        </div>
                    </div>

                    <div class="field">
                        <label for="i-berkas">Cara 1 — Unggah berkas CSV</label>
                        <input id="i-berkas" class="input input-file" type="file" name="berkas" accept=".csv,.txt,.tsv">
                        <p class="hint">
                            <a href="{{ route('admin.pengguna.template') }}">@include('partials.icon', ['name' => 'download', 'size' => 13]) Unduh contoh berkas</a>
                            — buka di Excel, isi, lalu <em>Save As → CSV</em>.
                        </p>
                    </div>

                    <div class="field">
                        <label for="i-daftar">Cara 2 — Tempel daftar langsung (dari Excel / catatan)</label>
                        <textarea id="i-daftar" class="textarea mono" name="daftar" rows="6"
                                  placeholder="0012345678;Nama Siswa Satu;X RPL A&#10;0012345679;Nama Siswa Dua;X RPL A">{{ old('daftar') }}</textarea>
                    </div>

                    <div class="panduan-impor">
                        <strong>Urutan kolom:</strong> <code>NIS</code> · <code>Nama</code> · <code>Kelas</code> · <code>Email</code> · <code>Telepon</code>
                        <span class="muted">(hanya NIS dan Nama yang wajib)</span>.
                        Pemisah koma, titik koma, atau tab dikenali otomatis; baris judul boleh ada.
                        <div class="small muted mt-1">
                            Tips: format kolom NIS di Excel sebagai <strong>Text</strong> agar angka nol di depan
                            (mis. 0085192025) tidak hilang. NIS atau email yang sudah terdaftar otomatis dilewati.
                        </div>
                    </div>
                </div>
                <div class="modal-foot">
                    <button class="btn btn-ghost" type="button" data-tutup>Batal</button>
                    <button class="btn" type="submit">Impor akun</button>
                </div>
            </form>
        </div>
    </div>
@endsection
