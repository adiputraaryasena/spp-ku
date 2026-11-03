<?php

use App\Models\Peminjaman;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sppku:tolak-kedaluwarsa', function () {
    $jumlah = Peminjaman::tolakKedaluwarsa();
    $this->info($jumlah.' pengajuan yang tidak diambil dalam '.config('sppku.batas_ambil_jam').' jam ditolak otomatis.');
})->purpose('Tolak pengajuan peminjaman yang tidak diambil dalam batas waktu');

// Opsional: jalankan "php artisan schedule:work" bila ingin pemeriksaan berkala di luar akses web.
Schedule::command('sppku:tolak-kedaluwarsa')->everyFiveMinutes();
