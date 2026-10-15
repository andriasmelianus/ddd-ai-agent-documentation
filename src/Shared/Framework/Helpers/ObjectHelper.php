<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Helpers;

use ReflectionClass;
use ReflectionProperty;

use function array_key_exists;

/**
 * ObjectHelper.
 *
 * Kumpulan helper statis berbasis Reflection untuk manipulasi objek pada
 * tingkat metadata: menyalin properti publik antar objek, memeriksa
 * pewarisan interface, dan operasi introspektif serupa.
 */
final class ObjectHelper
{
    /**
     * Menyalin seluruh properti publik dari objek `$source` ke objek `$target`.
     *
     * Hanya properti dengan modifier `public` yang disalin. Properti pada
     * `$target` yang tidak memiliki padanan di `$source` akan tetap
     * dipertahankan nilainya. Sebaliknya, properti di `$source` yang tidak
     * dideklarasikan di `$target` akan dilewati.
     *
     * Cocok digunakan untuk sinkronisasi ringan antara DTO/VO dengan Entity
     * atau objek dengan struktur properti serupa.
     *
     * @param  object  $source  Objek sumber (properti publik akan dibaca).
     * @param  object  $target  Objek tujuan (properti publik akan ditulis).
     */
    public static function copyPublicProperties(object $source, object $target): void
    {
        $sourceReflection = new ReflectionClass($source);
        $targetReflection = new ReflectionClass($target);

        foreach ($sourceReflection->getProperties(ReflectionProperty::IS_PUBLIC) as $sourceProperty) {
            $propertyName = $sourceProperty->getName();
            if ($targetReflection->hasProperty($propertyName)) {
                $target->{$propertyName} = $source->{$propertyName};
            }
        }
    }

    /**
     * Memeriksa apakah `$instance` mengimplementasikan interface `$className`.
     *
     * Menggunakan `class_implements()` untuk mendapatkan daftar seluruh
     * interface yang direalisasikan oleh instance (termasuk hasil pewarisan).
     * Mengembalikan `false` bila daftar interface gagal diambil.
     *
     * @param  object  $instance  Instance objek yang akan diperiksa.
     * @param  class-string  $className  Nama kelas/interface yang ingin dicek.
     * @return bool True bila `$instance` mengimplementasikan `$className`.
     */
    public static function implements(object $instance, string $className): bool
    {
        /** @var array<string, class-string>|false $classes */
        $classes = class_implements($instance);
        if ($classes === false) {
            return false;
        }

        return array_key_exists($className, $classes);
    }
}
