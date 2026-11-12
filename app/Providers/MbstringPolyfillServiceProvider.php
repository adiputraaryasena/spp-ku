<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MbstringPolyfillServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!function_exists('mb_split')) {
            function mb_split($pattern, $string, $limit = -1)
            {
                // Fallback sederhana menggunakan preg_split jika mb_split tidak ada
                return preg_split('/' . preg_quote($pattern, '/') . '/', $string, $limit);
            }
        }
    }
}