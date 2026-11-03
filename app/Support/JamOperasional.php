<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Jam buka/tutup perpustakaan. Jadwal dibaca dari config/sppku.php
 * (kunci jam_operasional) sehingga mudah diubah tanpa menyentuh kode lain.
 */
class JamOperasional
{
    public const HARI = [
        1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
        5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu',
    ];

    /** Jadwal satu pekan, siap ditampilkan. */
    public static function jadwal(?Carbon $sekarang = null): array
    {
        $sekarang = $sekarang ?? now();
        $konfig = config('sppku.jam_operasional', []);
        $hasil = [];

        foreach (self::HARI as $no => $nama) {
            $jam = $konfig[$no] ?? null;
            $hasil[] = [
                'no' => $no,
                'hari' => $nama,
                'libur' => empty($jam),
                'buka' => $jam ? self::format($jam[0]) : null,
                'tutup' => $jam ? self::format($jam[1]) : null,
                'hari_ini' => $sekarang->dayOfWeekIso === $no,
            ];
        }

        return $hasil;
    }

    /** "07:00" -> "07.00" */
    public static function format(?string $jam): string
    {
        return $jam ? str_replace(':', '.', $jam) : '';
    }

    /** @return array{0: Carbon, 1: Carbon}|null  [buka, tutup] pada tanggal tersebut */
    private static function rentang(Carbon $tanggal): ?array
    {
        $jam = (config('sppku.jam_operasional', []))[$tanggal->dayOfWeekIso] ?? null;

        if (empty($jam)) {
            return null;
        }

        return [
            $tanggal->copy()->setTimeFromTimeString($jam[0]),
            $tanggal->copy()->setTimeFromTimeString($jam[1]),
        ];
    }

    /** Apakah perpustakaan buka pada tanggal tersebut (tanpa memandang jam)? */
    public static function bukaPada(Carbon $tanggal): bool
    {
        return self::rentang($tanggal) !== null;
    }

    public static function sedangBuka(?Carbon $sekarang = null): bool
    {
        $sekarang = $sekarang ?? now();
        $r = self::rentang($sekarang);

        return $r !== null && $sekarang->greaterThanOrEqualTo($r[0]) && $sekarang->lessThan($r[1]);
    }

    /** @return array{buka: bool, detail: string} */
    public static function status(?Carbon $sekarang = null): array
    {
        $sekarang = $sekarang ?? now();
        $r = self::rentang($sekarang);

        if ($r !== null && $sekarang->greaterThanOrEqualTo($r[0]) && $sekarang->lessThan($r[1])) {
            return [
                'buka' => true,
                'detail' => 'Buka hari ini sampai pukul '.self::format($r[1]->format('H:i')).' WIB.',
            ];
        }

        if ($r !== null && $sekarang->lessThan($r[0])) {
            return [
                'buka' => false,
                'detail' => 'Buka hari ini mulai pukul '.self::format($r[0]->format('H:i')).' WIB.',
            ];
        }

        for ($i = 1; $i <= 7; $i++) {
            $tgl = $sekarang->copy()->addDays($i)->startOfDay();
            $besok = self::rentang($tgl);

            if ($besok !== null) {
                $nama = $i === 1 ? 'besok' : self::HARI[$tgl->dayOfWeekIso];

                return [
                    'buka' => false,
                    'detail' => 'Buka kembali '.$nama.' pukul '.self::format($besok[0]->format('H:i')).' WIB.',
                ];
            }
        }

        return ['buka' => false, 'detail' => 'Jadwal operasional belum diatur.'];
    }

    /** Apakah perpustakaan buka setidaknya sebentar dalam $jam jam ke depan? */
    public static function adaJamBukaDalam(Carbon $dari, int $jam): bool
    {
        $sampai = $dari->copy()->addHours($jam);
        $hari = (int) ceil($jam / 24) + 1;

        for ($i = 0; $i <= $hari; $i++) {
            $r = self::rentang($dari->copy()->addDays($i)->startOfDay());

            if ($r !== null && $r[0]->lt($sampai) && $r[1]->gt($dari)) {
                return true;
            }
        }

        return false;
    }
}
