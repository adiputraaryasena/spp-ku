<?php

namespace App\Http\Middleware;

use App\Models\Peminjaman;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pengajuan yang tidak diambil di perpustakaan dalam 24 jam ditolak otomatis.
 * Aplikasi dijalankan tanpa cron (php artisan serve / XAMPP), jadi pemeriksaan
 * dilakukan "malas" di sini, paling sering sekali per 30 detik.
 */
class TolakPengajuanKedaluwarsa
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (Cache::add('sppku.cek-kedaluwarsa', true, 30)) {
                Peminjaman::tolakKedaluwarsa();
            }
        } catch (\Throwable $e) {
            // Jangan sampai halaman gagal dibuka hanya karena pemeriksaan ini.
            report($e);
        }

        return $next($request);
    }
}
