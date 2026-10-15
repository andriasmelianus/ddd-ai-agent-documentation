<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\Enums;

/**
 * Trait TryFromOrNullTrait
 *
 * Trait pembantu (helper) yang ditujukan untuk digunakan pada backed enum
 * bertipe `string`. Trait ini menambahkan method statis `tryFromOrNull()`
 * yang mengembalikan `null` apabila nilai input adalah `null`, dan
 * memanggil `static::tryFrom()` untuk nilai string lainnya.
 *
 * Trait ini menyederhanakan konversi dari sumber data yang mungkin
 * bernilai `null` (misalnya kolom database opsional, atau parameter
 * request yang tidak wajib) menjadi enum tanpa perlu pengecekan `null`
 * yang berulang.
 *
 * Contoh penggunaan pada enum:
 * ```php
 * enum RoleEnum: string
 * {
 *     use TryFromOrNullTrait;
 *
 *     case admin = 'admin';
 *     case user  = 'user';
 * }
 *
 * $role = RoleEnum::tryFromOrNull($request->input('role'));
 * ```
 */
trait TryFromOrNullTrait
{
    /**
     * Mengembalikan case enum yang cocok dengan `$value`, atau `null` bila
     * `$value` adalah `null` atau tidak sesuai dengan case manapun.
     *
     * Method ini merupakan pembungkus aman untuk {@see \BackedEnum::tryFrom()}
     * yang secara eksplisit menangani input `null`.
     *
     * @param  string|null  $value  Nilai string yang akan dicocokkan dengan case
     *                              enum, atau `null` jika data tidak tersedia.
     * @return static|null Instance enum yang cocok, atau `null` bila input
     *                     `null` atau tidak ada case yang cocok.
     */
    public static function tryFromOrNull(?string $value): ?static
    {
        if ($value === null) {
            return null;
        }

        return static::tryFrom($value);
    }
}
