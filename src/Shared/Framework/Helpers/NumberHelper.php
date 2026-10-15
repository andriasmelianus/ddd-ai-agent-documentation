<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Helpers;

use InvalidArgumentException;

/**
 * NumberHelper.
 *
 * Kumpulan helper statis untuk operasi numerik yang sering dibutuhkan
 * lintas domain, seperti perbandingan floating-point yang aman terhadap
 * presisi, perhitungan selisih persentase dengan threshold, serta format
 * angka bergaya ringkas ala Instagram (K/M).
 */
final readonly class NumberHelper
{
    /**
     * Membandingkan dua bilangan floating-point dengan toleransi presisi
     * (`delta`) untuk menghindari galat pembulatan yang inheren pada tipe
     * `float`.
     *
     * Dua nilai dianggap sama bila `|a - b| < delta`.
     *
     * @param  float  $a  Bilangan pertama.
     * @param  float  $b  Bilangan kedua.
     * @param  float  $delta  Toleransi selisih maksimum (default 0.00001).
     * @return bool True bila kedua nilai dianggap sama dalam toleransi.
     */
    public static function equals(float $a, float $b, float $delta = 0.00001): bool
    {
        $diff = abs($a - $b);

        return $diff < $delta;
    }

    /**
     * Menghitung selisih persentase antara dua bilangan `a` dan `b` relatif
     * terhadap `a`.
     *
     * Metode ini akan mengembalikan `false` bila:
     * - Selisih absolut `|a - b|` kurang dari `minDiff` (perubahan terlalu kecil), atau
     * - Persentase perubahan kurang dari `minPercent`.
     *
     * Bila `a === 0.0`, akan dilempar {@see InvalidArgumentException} karena
     * pembagian dengan nol tidak terdefinisi.
     *
     * Rumus: `percent = |(b - a) / a| * 100`.
     *
     * @param  float  $a  Nilai awal (basis perbandingan, tidak boleh 0).
     * @param  float  $b  Nilai baru.
     * @param  float  $minDiff  Ambang selisih absolut minimum.
     * @param  float  $minPercent  Ambang persentase minimum.
     * @return false|float Persentase perubahan, atau false bila di bawah ambang.
     *
     * @throws InvalidArgumentException Bila `a` bernilai 0.0.
     */
    public static function percentageDifference(float $a, float $b, float $minDiff, float $minPercent): false|float
    {
        if (abs($a - $b) < $minDiff) {
            return false;
        }
        if ($a === 0.0) {
            throw new InvalidArgumentException('El número inicial no puede ser 0.');
        }

        $percent = abs(($b - $a) / $a) * 100;
        if ($percent < $minPercent) {
            return false;
        }

        return $percent;
    }

    /**
     * Memformat bilangan bulat ke representasi ringkas bergaya media sosial
     * (Instagram):
     * - >= 1.000.000  → nilai dibagi 1.000.000 dengan sufiks `M` (mis. `1.2M`).
     * - >= 10.000     → nilai dibagi 1.000    dengan sufiks `K` (mis. `12.3K`).
     * - Lainnya       → dikembalikan apa adanya sebagai integer.
     *
     * Pembulatan dilakukan dengan satu angka desimal.
     *
     * @param  int  $number  Bilangan bulat yang akan diformat.
     * @return string|int String hasil format (dengan sufiks) atau integer bila di bawah ambang.
     */
    public static function formatInstagramNumber(int $number): string|int
    {
        if ($number >= 1000000) {
            return round($number / 1000000, 1).'M';
        }

        if ($number >= 10000) {
            return round($number / 1000, 1).'K';
        }

        return $number;
    }
}
