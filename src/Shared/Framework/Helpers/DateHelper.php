<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Helpers;

use DateTime;
use Src\Shared\Framework\Domain\Enums\LanguageEnum;

/**
 * DateHelper.
 *
 * Kumpulan helper statis untuk manipulasi waktu berbasis Unix timestamp
 * (integer detik) dan konversi antar satuan waktu.
 *
 * Menyediakan konstanta durasi yang sering dipakai (DAY, WEEK, HOUR, YEAR)
 * serta beberapa formatter durasi/relative-time untuk kebutuhan tampilan
 * (multi-bahasa: English/Spanish).
 */
final class DateHelper
{
    /** Durasi satu hari dalam detik. */
    public const int DAY = 24 * 60 * 60;

    /** Durasi satu minggu dalam detik. */
    public const int WEEK = 7 * 24 * 60 * 60;

    /** Durasi satu jam dalam detik. */
    public const int HOUR = 60 * 60;

    /** Durasi satu tahun (365 hari) dalam detik. */
    public const int YEAR = 24 * 60 * 60 * 365;

    /**
     * Memformat durasi (detik) menjadi bentuk ringkas mingguan dan harian,
     * mis. `"3S 2D"` (3 minggu 2 hari).
     *
     * Aturan:
     * - Nilai negatif atau lebih dari 10 tahun → string kosong.
     * - Kurang dari 1 hari → `"1D"`.
     * - Selain itu → kombinasi minggu (`S`) dan hari (`D`).
     *
     * @param  int  $seconds  Durasi dalam detik.
     * @return string Representasi ringkas durasi.
     */
    public static function getDurationWeeksAndDays(int $seconds): string
    {
        if ($seconds < 0 || $seconds > self::YEAR * 10) {
            return '';
        }
        if ($seconds < self::DAY) {
            return '1D';
        }
        $weeks = $seconds / (self::DAY * 7);
        $rest = $weeks - floor($weeks);
        $days = $rest * 7;

        return (floor($weeks) ? floor($weeks).'S' : '').' '.($days ? (floor($days).'D') : '');
    }

    /**
     * Memformat durasi menjadi bentuk ringkas hari + jam (bahasa Inggris),
     * mis. `"3 d 5 h"`.
     *
     * Aturan:
     * - Kurang dari 1 hari → hasilnya dalam jam (`"X h"`).
     * - Kurang dari 4 hari → gabungan hari + jam.
     * - Sisanya → hanya jumlah hari.
     *
     * @param  int  $seconds  Durasi dalam detik.
     * @return string Representasi durasi ringkas (multi-satuan).
     */
    public static function getShortDurationDaysAndHours(int $seconds): string
    {
        if ($seconds < 0 || $seconds > self::YEAR * 10) {
            return '';
        }
        if ($seconds < self::DAY) {
            return round(($seconds / (60 * 60)), 1).' h';
        }
        if ($seconds < self::DAY * 4) {
            $days = floor($seconds / self::DAY);
            $hours = round(($seconds - $days * self::DAY) / (60 * 60), 1);

            return $days.' d '.$hours.' h';
        }

        return floor($seconds / self::DAY).' d';
    }

    /**
     * Serupa dengan {@see self::getShortDurationDaysAndHours()} namun
     * menggunakan kata utuh (`"days"`/`"hours"`) dan mendukung bahasa
     * Spanyol (`"días"`/`"horas"`).
     *
     * @param  int  $seconds  Durasi dalam detik.
     * @param  LanguageEnum  $lang  Bahasa hasil format (default: spanish).
     * @return string Representasi durasi versi panjang.
     */
    public static function getDurationDaysAndHours(int $seconds, LanguageEnum $lang = LanguageEnum::spanish): string
    {
        if ($seconds < 0 || $seconds > self::YEAR * 10) {
            return '';
        }
        if ($seconds < self::DAY) {
            if ($lang === LanguageEnum::spanish) {
                return round(($seconds / (60 * 60)), 1).' horas';
            }

            return round(($seconds / (60 * 60)), 1).' hours';
        }
        if ($seconds < self::DAY * 4) {
            $days = floor($seconds / self::DAY);
            $hours = round(($seconds - $days * self::DAY) / (60 * 60), 1);

            if ($lang === LanguageEnum::spanish) {
                return $days.' días y '.$hours.' horas';
            }

            return $days.' days and '.$hours.' hours';
        }

        if ($lang === LanguageEnum::spanish) {
            return floor($seconds / self::DAY).' días';
        }

        return floor($seconds / self::DAY).' days';
    }

    /**
     * Menghasilkan representasi "relative time" ala manusia (mis.
     * `"5 minutes ago"` atau `"hace 2 horas"`) dari sebuah Unix timestamp.
     *
     * Aturan gradasi:
     * - Sama dengan waktu sekarang → "just now" / "Justo ahora".
     * - < 60 detik   → detik.
     * - < 1 jam      → menit.
     * - < 1 hari     → jam.
     * - < 4 hari     → hari + jam.
     * - < 5 hari     → hari saja.
     * - < 30 hari    → format `"l d M, Y"`.
     * - Selain itu   → format ISO `"Y-m-d H:i:s"`.
     *
     * @param  int  $date  Unix timestamp yang akan diformat.
     * @param  bool  $addSuffix  Jika true, tambahkan sufiks
     *                           `"ago"`/prefix `"hace"`.
     * @param  LanguageEnum  $lang  Bahasa (default: english).
     * @return string String relative time.
     */
    public static function gePrettyDate(
        int $date,
        bool $addSuffix = false,
        LanguageEnum $lang = LanguageEnum::english
    ): string {
        $seconds = time() - $date;
        if ($seconds === 0) {
            return $lang === LanguageEnum::spanish ? 'Justo ahora' : 'just now';
        }
        if ($seconds < 60) { // dont know, check later why 10 years
            if ($lang === LanguageEnum::spanish) {
                return ($addSuffix ? ' hace ' : '').$seconds.' segundos';
            }

            return $seconds.' seconds'.($addSuffix ? ' ago' : '');
        }
        if ($seconds < self::HOUR) {
            if ($lang === LanguageEnum::spanish) {
                return ($addSuffix ? ' hace ' : '').round(($seconds / 60), 0).' minutos';
            }

            return round(($seconds / 60), 0).' minutes'.($addSuffix ? ' ago' : '');
        }
        if ($seconds < self::DAY) {
            if ($lang === LanguageEnum::spanish) {
                return ($addSuffix ? ' hace ' : '').round(($seconds / (60 * 60)), 1).' horas';
            }

            return round(($seconds / (60 * 60)), 1).' hours'.($addSuffix ? ' ago' : '');
        }
        if ($seconds < self::DAY * 4) {
            $days = floor($seconds / self::DAY);
            $hours = round(($seconds - $days * self::DAY) / (60 * 60), 1);

            if ($lang === LanguageEnum::spanish) {
                return ($addSuffix ? ' hace ' : '').$days.' días '.$hours.' horas';
            }

            return $days.' days '.$hours.' hours'.($addSuffix ? ' ago' : '');
        }
        $days = (floor($seconds / self::DAY));
        if ($days < 5) {
            if ($lang === LanguageEnum::spanish) {
                return ($addSuffix ? ' hace ' : '').$days.' días';
            }

            return $days.' days'.($addSuffix ? ' ago' : '');
        }

        if ($days < 30) {
            return date('l d M, Y', $date);
        }

        return date('Y-m-d H:i:s', $date);
    }

    /**
     * Mengonversi jumlah jam ke detik.
     *
     * @param  int  $hours  Jumlah jam.
     * @return int Jumlah detik yang setara.
     */
    public static function convertHourToSeconds(int $hours): int
    {
        return $hours * 3600;
    }

    /**
     * Mengonversi jumlah minggu ke detik.
     *
     * @param  int  $weeks  Jumlah minggu.
     * @return int Jumlah detik yang setara.
     */
    public static function convertWeeksToSeconds(int $weeks): int
    {
        return $weeks * 604800;
    }

    /**
     * Menghitung usia (dalam tahun) berdasarkan Unix timestamp tanggal lahir.
     *
     * @param  int  $dob  Tanggal lahir dalam bentuk Unix timestamp.
     * @return int Usia dalam tahun (dihitung dari sekarang).
     */
    public static function getAge(int $dob): int
    {
        $birthDate = new DateTime('@'.$dob);
        $now = new DateTime; // current date

        return $now->diff($birthDate)->y;
    }

    /**
     * Mengembalikan nama hari dalam bahasa Inggris (mis. `"Monday"`) sesuai
     * indeks numerik.
     *
     * Indeks di-normalisasi via modulo 7 lalu ditambah 1 sehingga selalu
     * berada di rentang valid PHP (1..7).
     *
     * @param  int  $day  Indeks hari.
     * @return string Nama hari dalam bahasa Inggris.
     */
    public static function getDayName(int $day): string
    {
        $day = ($day % 7) + 1; // Convert the day number to match PHP's system

        return date('l', (int) mktime(0, 0, 0, 1, $day));
    }

    /**
     * Membungkus {@see strtotime()} agar selalu mengembalikan integer.
     *
     * Bernilai `0` bila input `null` atau {@see strtotime()} gagal mem-parse
     * string. Berguna sebagai perlindungan tipe pada data yang tidak
     * konsisten.
     *
     * @param  string|null  $time  String tanggal/waktu untuk diparse.
     * @return int Unix timestamp atau 0 bila gagal.
     */
    public static function safeStrToTime(?string $time): int
    {
        if ($time === null) {
            return 0;
        }
        $date = strtotime($time);
        if ($date === false) {
            return 0;
        }

        return $date;
    }
}
