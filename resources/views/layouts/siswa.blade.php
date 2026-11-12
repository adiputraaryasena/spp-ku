<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1e3a8a">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('judul', 'Beranda') — SPP-Ku</title>
    <link rel="icon" type="image/png" href="{{ asset('logo_sppku.png') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @php
        $pengguna = auth()->user();
        // Selama masih memakai kata sandi sementara, menu disembunyikan (semua halaman lain terkunci).
        $terkunci = $pengguna && $pengguna->pakaiKataSandiBawaan();
    @endphp
    <header class="topbar">
        <div class="topbar-inner">
            <a href="{{ route('home') }}" class="brand">
                <span class="brand-dot">
                    <img src="{{ asset('logo_sppku.png') }}" alt="Logo SPP-Ku" width="32">
                </span>
                SPP-Ku
            </a>

            @unless ($terkunci)
                <nav class="nav-links" aria-label="Menu utama">
                    <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Beranda</a>
                    <a href="{{ route('siswa.katalog') }}" @class(['active' => request()->routeIs('siswa.katalog', 'siswa.buku')])>Katalog</a>
                    @if ($pengguna && $pengguna->isSiswa())
                        <a href="{{ route('siswa.riwayat') }}" @class(['active' => request()->routeIs('siswa.riwayat', 'siswa.cetak')])>Riwayat</a>
                    @endif
                </nav>
            @endunless

            @if ($pengguna)
                <div class="nav-user">
                    <div class="nav-user-meta">
                        <div class="n">{{ $pengguna->nama }}</div>
                        <div class="k">{{ $pengguna->kelas ?: ($pengguna->isAdmin() ? 'Petugas' : '') }} · {{ $pengguna->kode }}</div>
                    </div>
                    @if ($pengguna->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-masuk">Panel petugas</a>
                    @endif
                    @unless ($terkunci)
                        <a href="{{ route('profil') }}" class="avatar" title="Profil saya" style="text-decoration:none;color:#fff">
                            {{ $pengguna->inisial }}
                        </a>
                    @endunless
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="linkout" type="submit" title="Keluar" aria-label="Keluar">
                            @include('partials.icon', ['name' => 'logout', 'size' => 19])
                        </button>
                    </form>
                </div>
            @elseif (! request()->routeIs('login'))
                <div class="nav-user">
                    <a href="{{ route('login') }}" class="btn btn-sm btn-masuk">
                        @include('partials.icon', ['name' => 'user', 'size' => 15]) Masuk
                    </a>
                </div>
            @endif
        </div>
    </header>

    @unless ($terkunci)
        @include('partials.pengingat-jatuh-tempo')
    @endunless

    <main @class(['page', 'page-tengah' => trim($__env->yieldContent('tengah')) !== ''])>
        @yield('konten')
    </main>

    @include('partials.footer')

    {{-- Menu bawah ala aplikasi: hanya muncul di layar ponsel (CSS), tidak tampil di laptop/PC. --}}
    @unless ($terkunci)
        <nav class="tabbar" aria-label="Menu bawah">
            <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>
                @include('partials.icon', ['name' => 'home', 'size' => 22])<span>Beranda</span>
            </a>
            <a href="{{ route('siswa.katalog') }}" @class(['active' => request()->routeIs('siswa.katalog', 'siswa.buku')])>
                @include('partials.icon', ['name' => 'books', 'size' => 22])<span>Katalog</span>
            </a>
            @if ($pengguna && $pengguna->isSiswa())
                <a href="{{ route('siswa.riwayat') }}" @class(['active' => request()->routeIs('siswa.riwayat', 'siswa.cetak')])>
                    @include('partials.icon', ['name' => 'clipboard', 'size' => 22])<span>Riwayat</span>
                </a>
            @endif
            @if (! $pengguna)
                <a href="{{ route('login') }}" @class(['active' => request()->routeIs('login')])>
                    @include('partials.icon', ['name' => 'user', 'size' => 22])<span>Masuk</span>
                </a>
            @elseif ($pengguna->isAdmin())
                <a href="{{ route('admin.dashboard') }}">
                    @include('partials.icon', ['name' => 'chart', 'size' => 22])<span>Panel</span>
                </a>
            @else
                <a href="{{ route('profil') }}" @class(['active' => request()->routeIs('profil')])>
                    @include('partials.icon', ['name' => 'user', 'size' => 22])<span>Akun</span>
                </a>
            @endif
        </nav>
    @endunless

    @include('partials.flash')
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('skrip')
</body>
</html>
