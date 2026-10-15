<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Helpers;

use Exception;
use Illuminate\Support\Str;
use RuntimeException;

use function count;

/**
 * StringHelper.
 *
 * Kumpulan helper statis untuk operasi string tingkat lanjut, mencakup:
 * - Perbandingan nama personal dengan toleransi typo (Levenshtein).
 * - Parsing angka dari string bergaya berbagai lokal (koma/titik sebagai
 *   pemisah desimal atau ribuan).
 * - Pembersihan simbol mata uang.
 * - Deteksi HTML.
 * - Pemotongan string dengan elipsis.
 */
final class StringHelper
{
    /**
     * Memeriksa apakah `$needle` dan `$hayStack` merupakan nama lengkap yang
     * "cukup mirip" berdasarkan jarak Levenshtein per kata.
     *
     * Aturan pencocokan dinamis berdasarkan jumlah kata pada `$needle`:
     * - 2 kata → butuh 2 kata cocok.
     * - 3 kata → butuh 2 kata cocok (mis. `"Juan Jose Perez"` cocok `"Juan Perez"`).
     * - 4 kata → butuh 3 kata cocok (mis. `"Juan Jose Perez Ruiz"` cocok `"Juan Perez Ruiz"`).
     *
     * Setiap kata dinormalisasi dengan `Str::transliterate()` +
     * `strtolower()` sebelum dibandingkan.
     *
     * @param  string  $needle  Nama yang ingin dicocokkan.
     * @param  string  $hayStack  Nama referensi/lengkap.
     * @param  int  $distance  Toleransi Levenshtein per kata.
     * @return bool True bila kecocokan memenuhi aturan.
     */
    public static function isSimilarFullName(string $needle, string $hayStack, int $distance): bool
    {
        $wordsNeedle = explode(' ', $needle);
        $wordsHayStack = explode(' ', $hayStack);
        $found = 0;
        foreach ($wordsNeedle as $wordNeedle) {
            foreach ($wordsHayStack as $wordHayStack) {
                $wordHayStackToSearch = Str::transliterate(strtolower($wordHayStack));
                $wordNeedleToSearch = Str::transliterate(strtolower($wordNeedle));
                if ($wordHayStackToSearch && $wordNeedleToSearch && levenshtein($wordNeedleToSearch, $wordHayStackToSearch) <= $distance + 1) {
                    $found++;
                    break;
                }
            }
        }
        $countWordsNeedle = count($wordsNeedle);
        if ($found === 2 && $countWordsNeedle === 3) {
            return true;
        }
        if ($found === 3 && $countWordsNeedle === 4) {
            return true;
        }

        return $found >= $countWordsNeedle;
    }

    /**
     * Memeriksa apakah dua nama identik secara case-insensitive dan
     * setelah proses transliterasi (mis. `"José"` = `"jose"`).
     *
     * Mengembalikan `false` bila `$name1` bernilai `null`.
     *
     * @param  string|null  $name1  Nama pertama.
     * @param  string  $name2  Nama kedua.
     * @return bool True bila keduanya dianggap sama.
     */
    public static function isTheSameName(?string $name1, string $name2): bool
    {
        if ($name1 === null) {
            return false;
        }

        return Str::transliterate(strtolower($name1)) ===
            Str::transliterate(strtolower($name2));
    }

    /**
     * Memeriksa apakah dua email identik secara case-insensitive.
     * Bernilai `false` bila salah satu argumen `null`.
     *
     * @param  string|null  $email1  Email pertama.
     * @param  string|null  $email2  Email kedua.
     * @return bool True bila keduanya sama.
     */
    public static function isTheSameEmail(?string $email1, ?string $email2): bool
    {
        if ($email1 === null || $email2 === null) {
            return false;
        }

        return strtolower($email1) === strtolower($email2);
    }

    /**
     * Memeriksa apakah dua nama mirip menggunakan jarak Levenshtein terhadap
     * versi ter-transliterasi dan lowercase. Nilai `$distance` merepresentasikan
     * toleransi ambang batas (jarak sebenarnya diperbolehkan hingga
     * `$distance + 1`).
     *
     * Bernilai `false` bila `$name1` bernilai `null`.
     *
     * @param  string|null  $name1  Nama pertama.
     * @param  string  $name2  Nama kedua.
     * @param  int  $distance  Toleransi Levenshtein (default 2).
     * @return bool True bila cukup mirip.
     */
    public static function isTheSimilarName(?string $name1, string $name2, int $distance = 2): bool
    {
        if ($name1 === null) {
            return false;
        }
        $name1 = Str::transliterate(strtolower($name1));
        $name2 = Str::transliterate(strtolower($name2));

        return $name1 && $name2 && levenshtein($name1, $name2) <= $distance + 1;
    }

    /**
     * Daftar simbol mata uang beserta alias yang diperbolehkan.
     *
     * Struktur: `['KODE' => ['ALIAS1', 'ALIAS2', 'SIMBOL', ...]]`.
     *
     * @var string[][]
     */
    public static array $allowedCurrencies = [
        'EUR' => ['EUROS', 'EURO', 'EUR', '€'],
    ];

    /** Penanda pembuka baris baru untuk sintaks Markdown (dua spasi + EOL). */
    public const string MARKDOWN_BREAKLINE = '  '.PHP_EOL;

    /** Penanda garis pemisah horizontal (`---`) untuk sintaks Markdown. */
    public const string MARKDOWN_LINE_SEPARATOR = '---';

    /**
     * Mengekstrak nilai numerik dari sebuah string yang mungkin mengandung
     * simbol mata uang dan pemisah ribuan/desimal dengan format berbeda
     * (koma vs titik).
     *
     * Heuristik parsing:
     * - Ada koma dan titik + koma < titik → format Inggris (`1,000.00`).
     * - Ada koma dan titik + koma > titik → format Eropa (`1.000,00`).
     * - Hanya koma + banyak segmen → koma dianggap pemisah ribuan.
     * - Hanya koma + segmen kanan bukan 3 digit → koma dianggap desimal.
     * - Selain itu → dicoba di-cast langsung ke float.
     *
     * @param  string  $string  String angka mentah.
     * @param  bool  $throwException  Bila true, akan melempar exception
     *                                bila hasil akhir tidak numerik.
     * @return float Nilai numerik hasil parsing.
     *
     * @throws RuntimeException Bila `$throwException=true` dan string bukan numerik.
     * @throws Exception Untuk error tak terduga lainnya.
     */
    public static function getNumberFromString(string $string, bool $throwException = false): float
    {
        if (empty($string)) {
            return 0;
        }
        $string = mb_strtoupper($string, 'UTF-8');
        $string = trim($string);

        $string = str_replace(' ', '', $string);
        $string = self::removeCurrencyFromString($string);

        $coma = strpos($string, ',');

        $point = strpos($string, '.');
        if ($coma && $point) { // 1.000,00 or 1,000.00
            if ($coma < $point) { // 1,000.00
                $string = str_replace(',', '', $string);

                return (float) $string;
            }
            // 1.000,00
            $s = str_replace(['.', ','], ['', '.'], $string);

            return (float) $s;
        }
        if ($coma && ($point === false)) { // 1,000 or //10,00
            $ar = explode(',', $string);
            if (count($ar) > 2) { // 1,000,0000
                return (float) str_replace(',', '', $string);
            }

            if (strlen($ar[1]) !== 3) { // 1,00
                return (float) str_replace(',', '.', $string);
            }

            // 1,000 we are really fucked, will treat as dot up to now
            return (float) str_replace(',', '', $string);
        }
        if ($throwException && is_numeric($string) === false) {
            throw new RuntimeException('Not a numeric value');
        }

        return (float) $string;
    }

    /**
     * Menghapus seluruh simbol/alias mata uang yang terdaftar pada
     * {@see self::$allowedCurrencies} dari string.
     *
     * @param  string  $string  String input.
     * @return string String tanpa simbol mata uang.
     */
    public static function removeCurrencyFromString(string $string): string
    {
        $currencies = [];
        foreach (self::$allowedCurrencies as $allowedCurrencyAlternatives) {
            foreach ($allowedCurrencyAlternatives as $currencyAlternative) {
                $currencies[] = $currencyAlternative;
            }
        }

        return str_replace($currencies, '', $string);
    }

    /**
     * Mendeteksi apakah string mengandung tag HTML dengan cara
     * membandingkan string asli dengan hasil {@see strip_tags()}.
     *
     * @param  string  $string  String yang diperiksa.
     * @return bool True bila mengandung tag HTML.
     */
    public static function isHTML(string $string): bool
    {
        return $string !== strip_tags($string);
    }

    /**
     * Memotong string dengan elipsis Unicode (`…`) bila panjangnya melebihi
     * `$length`. Aman terhadap karakter multibyte karena menggunakan
     * `mb_strimwidth()`.
     *
     * @param  string  $string  String input.
     * @param  int  $length  Panjang maksimum (dalam width, bukan byte).
     * @return string String yang telah dipotong bila perlu.
     */
    public static function truncateWithEllipsis(string $string, int $length): string
    {
        if (mb_strlen($string) > $length) {
            return mb_strimwidth($string, 0, $length, '…');
        }

        return $string;
    }
}
