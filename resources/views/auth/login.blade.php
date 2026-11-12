@extends('layouts.siswa')
@section('judul', 'Masuk')
@section('tengah', '1')

@section('konten')
    <div class="login-card">
        <div class="login-mark">
            <img src="{{ asset('logo_sppku.png') }}" alt="Logo SPP-Ku" width="32">
        </div>

        <h1>Masuk ke SPP-Ku</h1>
        <p class="login-sub">Sistem Peminjaman &amp; Pengembalian Buku<br>{{ config('sppku.sekolah.perpustakaan') }}</p>

        <form method="POST" action="{{ route('login.proses') }}" style="margin-top:24px">
            @csrf

            <div class="field">
                <label for="kode">NIS siswa atau kode petugas</label>
                <input id="kode" name="kode" type="text" value="{{ old('kode') }}" required autofocus
                       autocomplete="username" placeholder="Contoh: 2024001"
                       @class(['input', 'is-error' => $errors->has('kode')])>
                @error('kode') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="password">Kata sandi</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                       placeholder="Masukkan kata sandi"
                       @class(['input', 'is-error' => $errors->has('password')])>
                @error('password') <p class="error">{{ $message }}</p> @enderror
            </div>

            <label class="check mb-2">
                <input type="checkbox" name="ingat" value="1" @checked(old('ingat'))>
                Ingat saya di perangkat ini
            </label>

            <button class="btn btn-lg btn-block" type="submit">Masuk</button>
        </form>

        <div class="login-foot" style="margin-top: 20px;">
            <a href="{{ route('siswa.katalog') }}" class="btn btn-sm btn-ghost">&larr; Lihat katalog tanpa login</a>
        </div>
    </div>
@endsection
