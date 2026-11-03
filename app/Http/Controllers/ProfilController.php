<?php

namespace App\Http\Controllers;

use App\Support\AturanKataSandi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** FR-01 — pengubahan data profil dan kata sandi mandiri. */
class ProfilController extends Controller
{
    public function edit(): View
    {
        return view('profil', ['user' => auth()->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Nama tidak bisa diubah sendiri (hanya petugas): field 'nama' sengaja diabaikan.
        // Yang bisa diperbarui sendiri: email dan nomor telepon.
        $data = $request->validate([
            'email' => ['nullable', 'email', 'max:120', Rule::unique('users')->ignore($user->id)],
            'telepon' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{8,20}$/'],
        ], [
            'telepon.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, +, -, dan tanda kurung (8–20 karakter).',
        ], [
            'telepon' => 'nomor telepon',
        ]);

        $user->update([
            'email' => $data['email'] ?? null,
            'telepon' => isset($data['telepon']) ? trim($data['telepon']) : null,
        ]);

        return back()->with('sukses', 'Email dan nomor telepon diperbarui.');
    }

    public function ubahPassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'password_lama' => ['required', 'string'],
            'password' => AturanKataSandi::aturan(),
        ], AturanKataSandi::pesan(), [
            'password_lama' => 'kata sandi lama',
            'password' => 'kata sandi baru',
        ]);

        if (! Hash::check($data['password_lama'], $user->password)) {
            throw ValidationException::withMessages([
                'password_lama' => 'Kata sandi lama tidak cocok.',
            ]);
        }

        AturanKataSandi::periksaTambahan($user, $data['password']);
        AturanKataSandi::simpan($user, $data['password']);

        $request->session()->regenerate();

        return back()->with('sukses', 'Kata sandi diperbarui. Gunakan kata sandi baru pada login berikutnya.');
    }
}
