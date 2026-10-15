<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

use BackedEnum;

/**
 * Interface CeisaReferenceEnumInterface
 *
 * Kontrak standar untuk seluruh PHP Backed Enum referensi DJBC CEISA 4.0.
 */
interface CeisaReferenceEnumInterface extends BackedEnum
{
    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi terkait.
     */
    public function description(): string;

    /**
     * Mendapatkan seluruh daftar string value (kode) referensi yang valid.
     *
     * @return list<string>
     */
    public static function values(): array;

    /**
     * Mendapatkan seluruh daftar nama identifier cases enum.
     *
     * @return list<string>
     */
    public static function names(): array;

    /**
     * Mendapatkan associative array [kode => deskripsi] untuk kebutuhan form dropdown atau serialisasi DTO.
     *
     * @return array<string, string>
     */
    public static function toArray(): array;

    /**
     * Helper aman terhadap null untuk mencocokkan string nilai ke instance enum.
     */
    public static function tryFromOrNull(?string $value): ?static;
}
