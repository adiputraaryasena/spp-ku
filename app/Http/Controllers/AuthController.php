<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * FR-01 — Autentikasi: login (password terenkripsi bcrypt) dan logout.
 * Siswa masuk dengan NIS, petugas dengan kode petugas.
 */
class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('beranda');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string'],
        ], [], [
            'kode' => 'NIS / kode petugas',
            'password' => 'kata sandi',
        ]);

        $kredensial = [
            'kode' => $data['kode'],
            'password' => $data['password'],
            'aktif' => true,
        ];

        if (! Auth::attempt($kredensial, $request->boolean('ingat'))) {
            throw ValidationException::withMessages([
                'kode' => 'NIS/kode petugas atau kata sandi tidak cocok.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('beranda'))
            ->with('sukses', 'Selamat datang kembali, '.Auth::user()->nama.'.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('sukses', 'Anda telah keluar.');
    }

    /** Pintu masuk tunggal: arahkan sesuai role. */
    public function beranda(): RedirectResponse
    {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('home');
    }
}
