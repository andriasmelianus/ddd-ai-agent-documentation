<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Helpers;

/**
 * EmailHelper.
 *
 * Kumpulan helper statis untuk memproses dan memvalidasi alamat email.
 * Bertindak sebagai lapisan utilitas ringan agar logika terkait email
 * (normalisasi, koreksi typo umum, validasi format) tidak tersebar di
 * seluruh aplikasi.
 */
final class EmailHelper
{
    /**
     * Membersihkan/normalisasi alamat email agar konsisten sebelum disimpan
     * atau dibandingkan.
     *
     * Langkah normalisasi:
     * - Menghapus whitespace di awal/akhir (trim).
     * - Mengubah seluruh karakter menjadi huruf kecil (lowercase).
     * - Memperbaiki typo umum akhiran ".con" menjadi ".com".
     *
     * @param  string  $email  Alamat email mentah yang akan dinormalisasi.
     * @return string Alamat email yang telah dibersihkan.
     */
    public static function cleanEmail(string $email): string
    {
        $email = strtolower(trim($email));
        if (str_ends_with($email, '.con')) {
            $email = substr_replace($email, '.com', -4);
        }

        return $email;
    }

    /**
     * Memeriksa apakah nilai yang diberikan merupakan alamat email yang valid
     * secara format menurut filter bawaan PHP (FILTER_VALIDATE_EMAIL).
     *
     * Bernilai `false` jika input `null` atau string kosong. Validasi ini
     * bersifat sintaksis saja dan tidak melakukan pengecekan DNS atau
     * verifikasi eksistensi mailbox.
     *
     * @param  string|null  $email  Alamat email yang akan diperiksa.
     * @return bool True bila format email valid, false bila tidak.
     */
    public static function isEmail(?string $email): bool
    {
        if (empty($email)) {
            return false;
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
