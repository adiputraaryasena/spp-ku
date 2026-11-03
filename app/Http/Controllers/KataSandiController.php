<?php

namespace App\Http\Controllers;

use App\Support\AturanKataSandi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Halaman wajib ganti kata sandi.
 *
 * Akun siswa dibuat petugas dengan kata sandi sementara yang sama untuk semua
 * orang. Selama kata sandi itu belum diganti, akun belum benar-benar pribadi.
 * Middleware PaksaGantiPassword mengunci seluruh halaman lain sampai siswa
 * menetapkan kata sandinya sendiri di sini.
 */
class KataSandiController extends Controller
{
    public function form(): View|RedirectResponse
    {
        if (! Auth::user()->pakaiKataSandiBawaan()) {
            return redirect()->route('beranda');
        }

        return view('auth.ganti-password', ['user' => Auth::user()]);
    }

    public function simpan(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->pakaiKataSandiBawaan()) {
            return redirect()->route('beranda');
        }

        $data = $request->validate([
            'password_lama' => ['required', 'string'],
            'password' => AturanKataSandi::aturan(),
        ], AturanKataSandi::pesan(), [
            'password_lama' => 'kata sandi sementara',
            'password' => 'kata sandi baru',
        ]);

        if (! Hash::check($data['password_lama'], $user->password)) {
            throw ValidationException::withMessages([
                'password_lama' => 'Kata sandi sementara tidak cocok. Tanyakan kembali ke petugas perpustakaan.',
            ]);
        }

        AturanKataSandi::periksaTambahan($user, $data['password']);
        AturanKataSandi::simpan($user, $data['password']);

        // Sesi lama dibuang agar perangkat lain yang masih memakai kata sandi
        // sementara ikut terputus.
        $request->session()->regenerate();

        return redirect()->route('beranda')->with(
            'sukses',
            'Kata sandi berhasil diganti. Mulai sekarang hanya Anda yang bisa masuk ke akun ini.'
        );
    }
}
