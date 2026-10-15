<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum JenisTarif
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum JenisTarif: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case ADVALORUM = '1';
    case SPESIFIK = '2';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::ADVALORUM => 'ADVALORUM',
            self::SPESIFIK => 'SPESIFIK',
        };
    }
}
