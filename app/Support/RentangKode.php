<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Mengubah rentang "dari kode" - "sampai kode" menjadi daftar kode eksemplar.
 *
 * Kode terdiri dari awalan (opsional) + angka di bagian akhir, contoh:
 *   BK-101-001 sampai BK-101-010  -> 10 kode (awalan "BK-101-")
 *   1001 sampai 1010              -> 10 kode (tanpa awalan)
 * Awalan kedua ujung harus sama. Angka dengan nol di depan dipertahankan (001, 002, ...).
 */
class RentangKode
{
    public const MAKS_KODE = 500;

    /**
     * @return array<int, array{kode: string, prefix: string, urutan: int}>
     *
     * @throws InvalidArgumentException bila rentang tidak valid (pesan siap tampil ke pengguna)
     */
    public static function buat(string $dari, string $sampai): array
    {
        $dari = trim($dari);
        $sampai = trim($sampai);

        if ($dari === '' || $sampai === '') {
            throw new InvalidArgumentException('Isi kode awal dan kode akhir rentang eksemplar.');
        }

        [$awalan1, $angka1] = self::pecah($dari);
        [$awalan2, $angka2] = self::pecah($sampai);

        if ($awalan1 !== $awalan2) {
            throw new InvalidArgumentException(
                'Awalan kode awal ("'.$awalan1.'") dan kode akhir ("'.$awalan2.'") harus sama. '
                .'Contoh: BK-101-001 sampai BK-101-010.'
            );
        }

        $mulai = (int) $angka1;
        $selesai = (int) $angka2;

        if ($selesai < $mulai) {
            throw new InvalidArgumentException('Kode akhir harus lebih besar atau sama dengan kode awal.');
        }

        $jumlah = $selesai - $mulai + 1;

        if ($jumlah > self::MAKS_KODE) {
            throw new InvalidArgumentException('Maksimal '.self::MAKS_KODE.' kode sekali input (rentang ini '.$jumlah.' kode).');
        }

        // Nol di depan dipertahankan: 001..010. Tanpa nol di depan, angka ditulis apa adanya.
        $lebar = (strlen($angka1) === strlen($angka2) || $angka1[0] === '0') ? strlen($angka1) : 0;

        $hasil = [];
        for ($n = $mulai; $n <= $selesai; $n++) {
            $kode = $awalan1.str_pad((string) $n, $lebar, '0', STR_PAD_LEFT);

            if (mb_strlen($kode) > 60) {
                throw new InvalidArgumentException('Kode terlalu panjang (maksimal 60 karakter).');
            }

            $hasil[] = ['kode' => $kode, 'prefix' => $awalan1, 'urutan' => $n];
        }

        return $hasil;
    }

    /** @return array{0: string, 1: string} [awalan, angka di bagian akhir] */
    private static function pecah(string $kode): array
    {
        if (! preg_match('/^[A-Za-z0-9._\/-]+$/', $kode)) {
            throw new InvalidArgumentException('Kode "'.$kode.'" hanya boleh huruf, angka, titik, garis bawah, garis miring, atau tanda hubung (tanpa spasi).');
        }

        if (! preg_match('/^(.*?)(\d+)$/', $kode, $m)) {
            throw new InvalidArgumentException('Kode "'.$kode.'" harus diakhiri angka agar bisa dibuat berurutan. Contoh: BK-101-001.');
        }

        if (strlen($m[2]) > 12) {
            throw new InvalidArgumentException('Angka pada kode "'.$kode.'" terlalu panjang.');
        }

        return [$m[1], $m[2]];
    }
}
