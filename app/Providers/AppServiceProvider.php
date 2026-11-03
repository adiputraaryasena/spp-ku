<?php

namespace App\Providers;

use App\Models\Peminjaman;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Batas index MySQL lama (XAMPP/MariaDB < 10.2)
        Schema::defaultStringLength(191);

        // Paginasi memakai komponen SPP-Ku sendiri, bukan Tailwind/Bootstrap bawaan
        Paginator::defaultView('pagination.sppku');
        Paginator::defaultSimpleView('pagination.simple');

        // Pengingat jatuh tempo: ditampilkan di semua halaman situs untuk siswa yang login.
        View::composer('layouts.siswa', function ($view) {
            $pengingat = collect();
            $user = auth()->user();

            if ($user && $user->isSiswa()) {
                try {
                    $batas = now()->addDays((int) config('sppku.pengingat_hari'))->toDateString();

                    $pengingat = Peminjaman::with('buku', 'eksemplar')
                        ->where('user_id', $user->id)
                        ->where('status', Peminjaman::STATUS_DIPINJAM)
                        ->where('tanggal_jatuh_tempo', '<=', $batas)
                        ->orderBy('tanggal_jatuh_tempo')
                        ->get();
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            $view->with('pengingatTempo', $pengingat);
        });
    }
}
