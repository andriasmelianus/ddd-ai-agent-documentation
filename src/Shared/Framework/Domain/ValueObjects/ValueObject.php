<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\ValueObjects;

use ReflectionClass;
use ReflectionProperty;

/**
 * Class ValueObject
 *
 * Base class praktis untuk Value Object yang butuh helper
 * {@see self::attributes()} — mengembalikan daftar nama properti public
 * (non-static) dari instance saat ini menggunakan Reflection API.
 *
 * Berguna misalnya untuk:
 *  - Serialisasi generik ke array (mengambil semua property public).
 *  - Perbandingan/diff antar dua VO tanpa harus mendaftarkan field
 *    secara manual.
 *  - Pembentukan payload untuk logging/debug.
 *
 * Mengimplementasikan {@see ValueObjectInterface} sehingga otomatis
 * dikenali sebagai Value Object oleh framework.
 */
class ValueObject implements ValueObjectInterface
{
    /**
     * Mengembalikan daftar nama properti **public** dan **non-static**
     * dari instance saat ini.
     *
     * Metode ini memakai {@see ReflectionClass::getProperties()} dengan
     * filter `ReflectionProperty::IS_PUBLIC`, lalu mengecualikan
     * properti statis.
     *
     * @return array<int, mixed> Array numerik berisi nama-nama properti
     *                           public non-static (secara efektif string[]).
     */
    public function attributes(): array
    {
        $class = new ReflectionClass($this);
        $names = [];
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if (! $property->isStatic()) {
                $names[] = $property->getName();
            }
        }

        return $names;
    }
}
