<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Helpers;

/**
 * TemplateHelper.
 *
 * Mesin template minimalis untuk memproses string berisi placeholder dan
 * ekspresi khusus. Mendukung tiga bentuk sintaks utama:
 *
 * 1. **Substitusi variabel**: `{{namaVariabel}}` diganti dengan nilai dari
 *    array `$variables`.
 * 2. **Nilai acak**: `[opsi1|opsi2|opsi3]` — memilih salah satu opsi
 *    non-kosong dan bukan `#ERROR#` secara acak.
 * 3. **Kondisional if/then/else**: `[a==b>hasilJikaSama>hasilJikaBeda]`.
 * 4. **Fallback nilai pertama non-kosong**: `[a>b>c]` — memilih token
 *    pertama yang non-kosong (dan bukan `#ERROR#`).
 *
 * Karakter kurung siku `[`/`]` di dalam teks input dapat di-escape dengan
 * `((` dan `))` yang di akhir proses akan dikembalikan menjadi `[` dan `]`
 * secara berturut-turut.
 */
final class TemplateHelper
{
    /**
     * Memproses template dengan substitusi variabel dan ekspresi.
     *
     * Urutan pemrosesan:
     * 1. Ganti setiap `{{key}}` dengan `$variables[key]` (dicasting ke string).
     * 2. Evaluasi seluruh ekspresi bersayap kurung siku via
     *    {@see self::parseText()}.
     * 3. Ganti escape `((` → `[` dan `))` → `]`.
     *
     * @param  string  $template  Template string yang akan diproses.
     * @param  array<string,string|int|float>  $variables  Peta nama→nilai variabel.
     * @return string Hasil akhir setelah seluruh substitusi.
     */
    public static function processTemplate(string $template, array $variables): string
    {
        $result = $template;
        foreach ($variables as $variable => $value) {
            $result = str_replace('{{'.$variable.'}}', (string) $value, $result);
        }
        $result = self::parseText($result);

        return str_replace(['((', '))'], ['[', ']'], $result);
    }

    /**
     * Menelusuri seluruh ekspresi bertanda `[...]` di dalam teks dan
     * mengeksekusinya sesuai jenis operator yang terdeteksi:
     *
     * - `|`  → nilai acak (delegasi ke {@see self::randomValues()}).
     * - `==` → kondisional if/then/else (delegasi ke {@see self::ifThenElse()}).
     * - `>`  → fallback nilai pertama non-kosong (delegasi ke
     *          {@see self::firstNonEmptyValue()}).
     *
     * @param  string  $text  Teks yang berpotensi berisi ekspresi bersayap `[]`.
     * @return string Teks setelah seluruh ekspresi dievaluasi.
     */
    private static function parseText(string $text): string
    {
        preg_match_all('/(?<=\[).+?(?=\])/', $text, $matches);

        foreach ($matches['0'] as $match) {

            if (strpos($match, '|')) {
                $text = self::randomValues($match, $text);

                continue;
            }

            if (strpos($match, '==')) {
                $text = self::ifThenElse($match, $text);

                continue;
            }
            if (strpos($match, '>') !== false) {
                $text = self::firstNonEmptyValue($match, $text);
            }
        }

        return $text;
    }

    /**
     * Mengganti ekspresi `[a|b|c]` dengan salah satu nilai (a/b/c) yang
     * dipilih secara acak, mengabaikan token kosong atau yang mengandung
     * `#ERROR#`. Bila seluruh token tidak valid, teks dikembalikan tanpa
     * perubahan.
     *
     * @param  string  $match  Konten di dalam kurung siku (tanpa `[`/`]`).
     * @param  string  $text  Teks penuh yang berisi ekspresi tersebut.
     * @return string Teks setelah ekspresi diganti.
     */
    private static function randomValues(string $match, string $text): string
    {
        $values = explode('|', $match);
        $values = array_filter($values, static function (string $item) {
            $item = trim(MixedHelper::getString($item));
            if (str_contains($item, '#ERROR#')) {
                return false;
            }
            if (trim($item) === '') {
                return false;
            }

            return true;
        });
        if (count($values) === 0) {
            return $text;
        }
        $text = str_replace('['.$match.']', $values[array_rand($values)], $text);

        return $text;
    }

    /**
     * Mengganti ekspresi `[a>b>c]` dengan token pertama yang tidak kosong
     * dan tidak mengandung `#ERROR#`. Bila tidak ada yang valid, digunakan
     * placeholder `#ERROR#`.
     *
     * @param  string  $match  Konten di dalam kurung siku (tanpa `[`/`]`).
     * @param  string  $text  Teks penuh yang berisi ekspresi tersebut.
     * @return string Teks setelah ekspresi diganti.
     */
    private static function firstNonEmptyValue(string $match, string $text): string
    {
        $values = explode('>', $match);
        $values = array_filter($values, static function ($item) {
            if (str_contains($item, '#ERROR#')) {
                return false;
            }
            if (trim($item) === '') {
                return false;
            }

            return true;
        });
        $firstItem = current($values);
        $text = str_replace('['.$match.']', $firstItem ?: '#ERROR#', $text);

        return $text;
    }

    /**
     * Mengevaluasi ekspresi kondisional `[a==b>then>else]`:
     * - Bila `a === b` (perbandingan string), gunakan `then`.
     * - Selain itu gunakan `else`.
     *
     * @param  string  $match  Konten di dalam kurung siku (tanpa `[`/`]`).
     * @param  string  $text  Teks penuh yang berisi ekspresi tersebut.
     * @return string Teks setelah ekspresi diganti.
     */
    private static function ifThenElse(string $match, string $text): string
    {
        [$comparison, $then, $else] = explode('>', $match);

        [$value1, $value2] = explode('==', $comparison);

        $result = $else;
        if ($value1 === $value2) {
            $result = $then;
        }

        $text = str_replace('['.$match.']', $result, $text);

        return $text;
    }
}
