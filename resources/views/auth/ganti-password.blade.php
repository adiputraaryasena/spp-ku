@extends('layouts.siswa')
@section('judul', 'Ganti kata sandi')
@section('tengah', '1')

@section('konten')
    <div class="login-card" style="max-width:440px">
        <div class="login-mark">@include('partials.icon', ['name' => 'shield', 'size' => 26])</div>

        <h1>Buat kata sandi Anda sendiri</h1>
        <p class="login-sub">
            Halo, {{ explode(' ', $user->nama)[0] }}. Akun <strong>{{ $user->kode }}</strong> masih memakai
            kata sandi sementara dari petugas.
        </p>

        <div class="alert alert-warning mt-2" style="margin-bottom:0">
            @include('partials.icon', ['name' => 'alert', 'size' => 18])
            <div>
                Kata sandi sementara sama untuk semua siswa dan diketahui petugas, jadi akun ini
                belum benar-benar pribadi. Tetapkan kata sandi yang hanya Anda yang tahu untuk melanjutkan.
            </div>
        </div>

        @include('partials.errors')

        <form method="POST" action="{{ route('password.wajib.simpan') }}" style="margin-top:18px">
            @csrf

            <div class="field">
                <label for="password_lama">Kata sandi sementara</label>
                <input id="password_lama" name="password_lama" type="password" required autofocus
                       autocomplete="current-password" placeholder="Kata sandi yang diberikan petugas"
                       @class(['input', 'is-error' => $errors->has('password_lama')])>
                @error('password_lama') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="password">Kata sandi baru</label>
                <input id="password" name="password" type="password" required minlength="8"
                       autocomplete="new-password" placeholder="Minimal 8 karakter, ada huruf dan angka"
                       @class(['input', 'is-error' => $errors->has('password')])>
                @error('password') <p class="error">{{ $message }}</p> @enderror
                <p class="hint">
                    Tidak boleh sama dengan NIS, potongan nama Anda, atau kata sandi sementara tadi.
                </p>
            </div>

            <div class="field">
                <label for="password_confirmation">Ulangi kata sandi baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       autocomplete="new-password" placeholder="Ketik ulang untuk memastikan" class="input">
            </div>

            <button class="btn btn-lg btn-block" type="submit">Simpan dan lanjutkan</button>
        </form>

        <div class="login-foot">
            Lupa kata sandi sementaranya? Temui petugas perpustakaan untuk meminta yang baru.
            Petugas hanya bisa memberi kata sandi sementara — kata sandi pribadi Anda tersimpan
            dalam bentuk terenkripsi dan tidak bisa dibaca siapa pun.
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-2 center">
            @csrf
            <button type="submit" class="small muted"
                    style="background:none;border:0;cursor:pointer;text-decoration:underline">
                Keluar dan masuk dengan akun lain
            </button>
        </form>
    </div>
@endsection
