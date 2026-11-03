<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Tamu diarahkan ke halaman login; pengguna yang sudah masuk ke beranda sesuai role.
        $middleware->redirectTo(guests: '/login', users: '/beranda');

        // Kunci seluruh halaman selama kata sandi bawaan belum diganti
        $middleware->web(append: [
            \App\Http\Middleware\TolakPengajuanKedaluwarsa::class,
            \App\Http\Middleware\PaksaGantiPassword::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
