@extends($user->isAdmin() ? 'layouts.admin' : 'layouts.siswa')
@section('judul', 'Profil saya')

@section('konten')
    @include('partials.errors')

    <div class="split">
        <div class="card">
            <div class="card-head">
                @include('partials.icon', ['name' => 'user', 'size' => 18])
                <h2 class="grow">Data akun</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profil.update') }}">
                    @csrf @method('PUT')
                    <div class="form-row">
                        <div class="field">
                            <label>NIS / kode petugas</label>
                            <input class="input" type="text" value="{{ $user->kode }}" readonly>
                            <p class="hint">Hanya petugas yang dapat mengubah nomor identitas.</p>
                        </div>
                        <div class="field">
                            <label>Kelas</label>
                            <input class="input" type="text" value="{{ $user->kelas ?: '—' }}" readonly>
                        </div>
                    </div>
                    <div class="field">
                        <label for="nama">Nama lengkap</label>
                        <input id="nama" class="input" type="text" value="{{ $user->nama }}" readonly aria-describedby="hint-nama">
                        <p class="hint" id="hint-nama">Nama tidak dapat diubah sendiri. Bila ada kesalahan penulisan, hubungi petugas perpustakaan.</p>
                    </div>
                    <div class="form-row">
                        <div class="field">
                            <label for="email">Email (opsional)</label>
                            <input id="email" class="input" type="email" name="email" value="{{ old('email', $user->email) }}"
                                   autocomplete="email" inputmode="email" placeholder="nama@contoh.com">
                        </div>
                        <div class="field">
                            <label for="telepon">Nomor telepon (opsional)</label>
                            <input id="telepon" class="input" type="tel" name="telepon" value="{{ old('telepon', $user->telepon) }}"
                                   maxlength="20" autocomplete="tel" inputmode="tel" placeholder="Contoh: 081234567890">
                        </div>
                    </div>
                    <p class="hint mb-2" style="margin-top:-4px">Email dan nomor telepon dipakai petugas untuk menghubungi Anda bila diperlukan.</p>
                    <button class="btn" type="submit">Simpan email &amp; telepon</button>
                </form>
            </div>
        </div>

        <div class="card" style="align-self:start">
            <div class="card-head">
                @include('partials.icon', ['name' => 'shield', 'size' => 18])
                <h2 class="grow">Ubah kata sandi</h2>
            </div>
            <div class="card-body">
                @if ($user->password_diganti_pada)
                    <div class="alert alert-success">
                        @include('partials.icon', ['name' => 'check-circle', 'size' => 18])
                        <div>
                            Kata sandi pribadi aktif sejak
                            {{ $user->password_diganti_pada->translatedFormat('d F Y') }}.
                            Hanya Anda yang mengetahuinya.
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('profil.password') }}">
                    @csrf @method('PUT')
                    <div class="field">
                        <label for="password_lama">Kata sandi lama</label>
                        <input id="password_lama" class="input" type="password" name="password_lama" required autocomplete="current-password">
                    </div>
                    <div class="field">
                        <label for="password">Kata sandi baru</label>
                        <input id="password" class="input" type="password" name="password" required minlength="8" autocomplete="new-password">
                        <p class="hint">
                            Minimal 8 karakter, memuat huruf dan angka. Tidak boleh sama dengan
                            {{ $user->isSiswa() ? 'NIS' : 'kode petugas' }}, potongan nama Anda,
                            atau kata sandi yang sedang dipakai.
                        </p>
                    </div>
                    <div class="field">
                        <label for="password_confirmation">Ulangi kata sandi baru</label>
                        <input id="password_confirmation" class="input" type="password" name="password_confirmation" required autocomplete="new-password">
                    </div>
                    <button class="btn" type="submit">Perbarui kata sandi</button>
                </form>
            </div>
        </div>
    </div>
@endsection
