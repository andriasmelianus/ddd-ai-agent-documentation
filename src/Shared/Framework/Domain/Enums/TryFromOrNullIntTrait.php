<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\Enums;

/**
 * Trait TryFromOrNullIntTrait
 *
 * Trait pembantu (helper) yang ditujukan untuk digunakan pada backed enum
 * bertipe `int`. Trait ini menambahkan method statis `tryFromOrNull()`
 * yang mengembalikan `null` apabila nilai input adalah `null`, dan
 * memanggil `static::tryFrom()` untuk nilai integer lainnya.
 *
 * Tujuannya adalah menyederhanakan pola pemakaian enum ketika sumber data
 * (misalnya kolom database atau payload request) dapat bernilai `null`,
 * sehingga pemanggil tidak perlu menuliskan pengecekan `null` berulang-ulang.
 *
 * Contoh penggunaan pada enum:
 * ```php
 * enum StatusEnum: int
 * {
 *     use TryFromOrNullIntTrait;
 *
 *     case active = 1;
 *     case inactive = 0;
 * }
 *
 * $status = StatusEnum::tryFromOrNull($row['status'] ?? null);
 * ```
 */
trait TryFromOrNullIntTrait
{
    /**
     * Mengembalikan case enum yang cocok dengan `$value`, atau `null` bila
     * `$value` adalah `null` atau tidak sesuai dengan case manapun.
     *
     * Method ini merupakan pembungkus aman untuk {@see \BackedEnum::tryFrom()}
     * yang secara eksplisit menangani input `null`.
     *
     * @param  int|null  $value  Nilai integer yang akan dicocokkan dengan case enum,
     *                           atau `null` jika data tidak tersedia.
     * @return static|null Instance enum yang cocok, atau `null` bila input `null`
     *                     atau tidak ada case yang cocok.
     */
    public static function tryFromOrNull(?int $value): ?static
    {
        if ($value === null) {
            return null;
        }

        return static::tryFrom($value);
    }
}
