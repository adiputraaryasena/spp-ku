<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#172554">
    <title>@yield('judul', 'Dashboard') — SPP-Ku Admin</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📚</text></svg>">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@php
    $antreanPending = \App\Models\Peminjaman::where('status', 'pending')->count();
@endphp
<div class="shell">
    <div class="sidebar-overlay" data-menu-tutup></div>
    <aside class="sidebar" id="menu-samping">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            <span class="brand-dot">@include('partials.icon', ['name' => 'books', 'size' => 15])</span>
            SPP-Ku <span style="font-weight:500;opacity:.6;font-size:12px">Admin</span>
        </a>

        <div class="sidebar-label">Ringkasan</div>
        <a href="{{ route('admin.dashboard') }}" @class(['side-link', 'active' => request()->routeIs('admin.dashboard')])>
            @include('partials.icon', ['name' => 'home', 'size' => 18]) Dashboard
        </a>

        <a href="{{ route('home') }}" class="side-link">
            @include('partials.icon', ['name' => 'books', 'size' => 18]) Lihat situs perpustakaan
        </a>

        <div class="sidebar-label">Master data</div>
        <a href="{{ route('admin.buku.index') }}" @class(['side-link', 'active' => request()->routeIs('admin.buku.*')])>
            @include('partials.icon', ['name' => 'book', 'size' => 18]) Data buku
        </a>
        <a href="{{ route('admin.kategori.index') }}" @class(['side-link', 'active' => request()->routeIs('admin.kategori.*')])>
            @include('partials.icon', ['name' => 'tag', 'size' => 18]) Kategori
        </a>
        <a href="{{ route('admin.pengguna.index') }}" @class(['side-link', 'active' => request()->routeIs('admin.pengguna.*')])>
            @include('partials.icon', ['name' => 'users', 'size' => 18]) Pengguna
        </a>

        <div class="sidebar-label">Transaksi</div>
        <a href="{{ route('admin.approval.index') }}" @class(['side-link', 'active' => request()->routeIs('admin.approval.*')])>
            @include('partials.icon', ['name' => 'clipboard', 'size' => 18]) Approval peminjaman
            @if ($antreanPending > 0)<span class="side-count">{{ $antreanPending }}</span>@endif
        </a>
        <a href="{{ route('admin.pengembalian.index') }}" @class(['side-link', 'active' => request()->routeIs('admin.pengembalian.*')])>
            @include('partials.icon', ['name' => 'return', 'size' => 18]) Pengembalian &amp; denda
        </a>
        <a href="{{ route('admin.laporan.index') }}" @class(['side-link', 'active' => request()->routeIs('admin.laporan.*')])>
            @include('partials.icon', ['name' => 'chart', 'size' => 18]) Laporan
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="side-logout" type="submit">
                @include('partials.icon', ['name' => 'logout', 'size' => 17]) Keluar
            </button>
        </form>
    </aside>

    <div class="main">
        <header class="main-head">
            <button type="button" class="menu-toggle" data-menu-toggle aria-controls="menu-samping" aria-expanded="false" aria-label="Buka menu">
                @include('partials.icon', ['name' => 'menu', 'size' => 22])
            </button>
            <div class="grow" style="flex:1">
                <h1>@yield('judul', 'Dashboard')</h1>
                @hasSection('subjudul')
                    <p class="muted small" style="margin-top:2px">@yield('subjudul')</p>
                @endif
            </div>
            <a href="{{ route('profil') }}" class="avatar" title="Profil petugas" style="text-decoration:none">
                {{ auth()->user()->inisial }}
            </a>
        </header>

        <div class="main-body">
            @yield('konten')
        </div>
    </div>
</div>

@include('partials.flash')
<script src="{{ asset('js/app.js') }}"></script>
@stack('skrip')
</body>
</html>
