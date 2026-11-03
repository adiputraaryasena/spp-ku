<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

/** Dulu halaman terpisah; kini beranda siswa sama dengan beranda publik. */
class DashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('home');
    }
}
