<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\ValueObjects;

use RuntimeException;

/**
 * Class CountryCode
 *
 * Value Object khusus untuk kode negara **ISO 3166-1 alpha-2**
 * (misalnya `ID`, `US`, `SG`). Mewarisi {@see StringIdentifier} sehingga
 * juga tergolong Identifier.
 *
 * Aturan validasi:
 *  - Panjang wajib **tepat 2 karakter**.
 *  - Nilai secara otomatis dinormalisasi ke UPPERCASE agar konsisten.
 *
 * Bersifat `final` — untuk mencegah subclassing yang bisa merusak
 * invariants (2 karakter, uppercase).
 */
final class CountryCode extends StringIdentifier
{
    /**
     * Factory statis dengan validasi kode negara.
     *
     * @param  string  $value  Kode negara mentah (case-insensitive).
     * @return self Instance {@see CountryCode} yang sudah di-uppercase.
     *
     * @throws RuntimeException Jika `strlen($value) !== 2`.
     */
    public static function create($value): self
    {
        if (strlen((string) $value) !== 2) {
            throw new RuntimeException('Invalid value for country '.$value);
        }

        return parent::create(strtoupper((string) $value));
    }
}
