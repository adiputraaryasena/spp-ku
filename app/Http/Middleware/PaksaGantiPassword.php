<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Selama akun masih memakai kata sandi bawaan dari petugas, seluruh halaman
 * dikunci dan pengguna diarahkan ke formulir penggantian kata sandi.
 *
 * Tujuannya: kata sandi awal (siswa123) diketahui petugas dan tertulis di
 * dokumentasi, sehingga akun belum benar-benar milik siswa sampai ia
 * menetapkan kata sandinya sendiri.
 */
class PaksaGantiPassword
{
    /** Halaman yang tetap boleh diakses agar tidak terjadi perulangan redirect. */
    private const DIIZINKAN = ['password.wajib', 'password.wajib.simpan', 'logout'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // PERBAIKAN: Menghapus tanda titik tiga (...) sebelum self::DIIZINKAN
        // Laravel routeIs() dapat menerima string atau array secara langsung.
        if ($user && $user->pakaiKataSandiBawaan() && ! $request->routeIs(self::DIIZINKAN)) {
            return redirect()->route('password.wajib');
        }

        return $next($request);
    }
}