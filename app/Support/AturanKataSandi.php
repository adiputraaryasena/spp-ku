<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * Satu tempat untuk seluruh aturan kata sandi, dipakai baik oleh halaman
 * "wajib ganti kata sandi" maupun menu profil, supaya keduanya tidak pernah
 * berbeda standar.
 */
class AturanKataSandi
{
    /** Kata sandi bawaan yang tercantum di dokumentasi — tidak boleh dipakai ulang. */
    private const BAWAAN = ['siswa123', 'admin123', 'password', '12345678', 'qwerty123'];

    /** @return array<int, mixed> */
    public static function aturan(): array
    {
        return [
            'required',
            'string',
            'confirmed',
            Password::min(8)->letters()->numbers(),
        ];
    }

    /**
     * Hanya aturan bawaan yang bisa ditimpa di sini. Pesan untuk Rules\Password
     * (letters/numbers) diambil dari lang/id/validation.php pada kunci
     * validation.password.*, sesuai cara kerja aturan tersebut.
     */
    public static function pesan(): array
    {
        return [
            'password.confirmed' => 'Ulangan kata sandi belum sama.',
        ];
    }

    /**
     * Pemeriksaan yang tidak bisa diwakili aturan bawaan Laravel:
     * kata sandi tidak boleh tebakan mudah dari identitas pengguna.
     *
     * @throws ValidationException
     */
    public static function periksaTambahan(User $user, string $baru): void
    {
        $rendah = mb_strtolower($baru);
        $galat = null;

        if (in_array($rendah, self::BAWAAN, true)) {
            $galat = 'Kata sandi itu terlalu umum dan tercantum di panduan. Pilih kata sandi lain.';
        } elseif ($rendah === mb_strtolower($user->kode)) {
            $galat = 'Kata sandi tidak boleh sama dengan NIS Anda — itu hal pertama yang akan ditebak orang.';
        } elseif (mb_strlen($rendah) >= 4 && str_contains(mb_strtolower($user->nama), $rendah)) {
            $galat = 'Kata sandi tidak boleh berupa potongan nama Anda sendiri.';
        } elseif (Hash::check($baru, $user->password)) {
            $galat = 'Kata sandi baru harus berbeda dari kata sandi sekarang.';
        }

        if ($galat) {
            throw ValidationException::withMessages(['password' => $galat]);
        }
    }

    /** Menyimpan kata sandi baru sekaligus mencabut status "wajib ganti". */
    public static function simpan(User $user, string $baru): void
    {
        $user->forceFill([
            'password' => $baru,                    // di-hash otomatis oleh cast 'hashed'
            'harus_ganti_password' => false,
            'password_diganti_pada' => now(),
        ])->save();
    }
}
