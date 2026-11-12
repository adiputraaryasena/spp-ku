<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Pembuat barcode Code 128 (SVG) tanpa library tambahan.
 *
 * - Semua karakter ASCII 32–126 didukung (huruf, angka, tanda hubung, titik, garis miring).
 * - Kode yang seluruhnya angka & berjumlah genap otomatis memakai Code Set C (lebih pendek,
 *   lebih mudah dipindai); selain itu memakai Code Set B.
 * - Hasil berupa SVG vektor: tajam di layar maupun saat dicetak, berapa pun ukurannya.
 */
class Code128
{
    /** Pola bar/spasi (lebar modul) untuk nilai 0–105, lalu stop (106). */
    private const POLA = [
        '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213',
        '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132',
        '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211',
        '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313',
        '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331',
        '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111',
        '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214',
        '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111',
        '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141',
        '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141',
        '114131', '311141', '411131', '211412', '211214', '211232', '2331112',
    ];

    private const START_B = 104;
    private const START_C = 105;
    private const CODE_C_DARI_B = 99;
    private const STOP = 106;

    /** Zona tenang (modul) di kiri & kanan; standar minimal 10 modul. */
    private const ZONA_TENANG = 10;

    /**
     * Daftar nilai simbol (termasuk start & checksum, tanpa stop) untuk sebuah teks.
     *
     * @return int[]
     */
    public static function simbol(string $teks): array
    {
        if ($teks === '') {
            throw new InvalidArgumentException('Teks barcode tidak boleh kosong.');
        }

        if (! preg_match('/^[\x20-\x7E]+$/', $teks)) {
            throw new InvalidArgumentException('Barcode Code 128 hanya mendukung karakter ASCII yang dapat dicetak.');
        }

        $nilai = [];

        if (ctype_digit($teks) && strlen($teks) % 2 === 0) {
            $nilai[] = self::START_C;
            foreach (str_split($teks, 2) as $pasang) {
                $nilai[] = (int) $pasang;
            }
        } else {
            $nilai[] = self::START_B;
            foreach (str_split($teks) as $huruf) {
                $nilai[] = ord($huruf) - 32;
            }
        }

        // Checksum: nilai start + (nilai simbol × posisinya), modulo 103.
        $jumlah = $nilai[0];
        for ($i = 1, $n = count($nilai); $i < $n; $i++) {
            $jumlah += $nilai[$i] * $i;
        }
        $nilai[] = $jumlah % 103;

        return $nilai;
    }

    /** Barisan lebar modul bar/spasi selang-seling (bar dulu) untuk seluruh barcode. */
    public static function modul(string $teks): array
    {
        $lebar = [];
        foreach (array_merge(self::simbol($teks), [self::STOP]) as $v) {
            foreach (str_split(self::POLA[$v]) as $w) {
                $lebar[] = (int) $w;
            }
        }

        return $lebar;
    }

    /** Total lebar dalam modul, termasuk zona tenang kiri-kanan. */
    public static function totalModul(string $teks): int
    {
        return array_sum(self::modul($teks)) + (self::ZONA_TENANG * 2);
    }

    /**
     * SVG barcode. Lebar bawaan mengikuti jumlah modul; tinggi diatur lewat CSS pemakai
     * (preserveAspectRatio="none" sehingga batang hanya memanjang ke atas-bawah).
     */
    public static function svg(string $teks, string $kelas = 'bc-svg'): string
    {
        $lebar = self::modul($teks);
        $total = array_sum($lebar) + (self::ZONA_TENANG * 2);

        $x = self::ZONA_TENANG;
        $batang = '';
        foreach ($lebar as $i => $w) {
            if ($i % 2 === 0) {
                $batang .= 'M'.$x.' 0h'.$w.'v1h-'.$w.'z';
            }
            $x += $w;
        }

        return '<svg class="'.e($kelas).'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$total.' 1"'
            .' preserveAspectRatio="none" shape-rendering="crispEdges" role="img"'
            .' aria-label="Barcode '.e($teks).'" data-modul="'.$total.'">'
            .'<rect width="'.$total.'" height="1" fill="#fff"/>'
            .'<path d="'.$batang.'" fill="#000"/>'
            .'</svg>';
    }
}
