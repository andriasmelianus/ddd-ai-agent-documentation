<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Trait CeisaReferenceEnumTrait
 *
 * Menyediakan implementasi standar method helper untuk Backed Enum referensi CEISA 4.0.
 */
trait CeisaReferenceEnumTrait
{
    /**
     * Mengembalikan instance enum berdasarkan kode string, atau null jika parameter null / tidak cocok.
     */
    public static function tryFromOrNull(?string $value): ?static
    {
        if ($value === null) {
            return null;
        }

        return static::tryFrom($value);
    }

    /**
     * Mengembalikan seluruh string nilai (kode) enum yang didukung.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Mengembalikan seluruh nama case identifier enum.
     *
     * @return list<string>
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Mengembalikan associative array pasangan [kode => deskripsi].
     *
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->description();
        }

        return $options;
    }

    /**
     * Alias dari toArray() untuk opsi dropdown/select UI.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return self::toArray();
    }
}
