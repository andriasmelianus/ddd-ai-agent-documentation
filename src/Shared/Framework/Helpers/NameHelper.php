<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Helpers;

/**
 * NameHelper.
 *
 * Kumpulan helper statis untuk memproses nama personal. Menyediakan
 * mekanisme sederhana untuk memisahkan nama depan dan nama belakang dari
 * sebuah string tunggal.
 */
final readonly class NameHelper
{
    /**
     * Memisahkan sebuah string nama lengkap menjadi pasangan
     * `[firstName, lastName]`.
     *
     * Aturan pemisahan:
     * - String kosong menghasilkan `['.', '.']` sebagai placeholder aman.
     * - Satu kata: kata tersebut menjadi first name, last name diisi `'.'`.
     * - Dua kata: dipetakan langsung ke first name dan last name.
     * - Tiga kata atau lebih: dua kata pertama digabung menjadi first name,
     *   sisanya (digabung dengan spasi) menjadi last name.
     *
     * Penggunaan karakter `.` sebagai placeholder ditujukan untuk sistem
     * eksternal yang mewajibkan kedua field terisi tanpa nilai kosong.
     *
     * @param  string  $value  Nama lengkap yang akan dipisah.
     * @return array<string> Array 2 elemen berisi [firstName, lastName].
     */
    public static function explode(string $value): array
    {
        if ($value === '') {
            return ['.', '.'];
        }
        $words = explode(' ', $value);

        if (count($words) === 1) {
            return [$value, '.'];
        }
        if (count($words) === 2) {
            return $words;
        }
        // 3 words or more. 2 first words goes to name and the rest is the last name
        $firstName = array_shift($words);
        $firstName .= ' '.array_shift($words);
        $lastName = implode(' ', $words);

        return [$firstName, $lastName];
    }
}
