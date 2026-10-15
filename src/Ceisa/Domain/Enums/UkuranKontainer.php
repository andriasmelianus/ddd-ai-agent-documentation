<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum UkuranKontainer
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum UkuranKontainer: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case KODE_20_FEET = '20';
    case KODE_40_FEET = '40';
    case KODE_45_FEET = '45';
    case KODE_60_FEET = '60';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::KODE_20_FEET => '20 FEET',
            self::KODE_40_FEET => '40 FEET',
            self::KODE_45_FEET => '45 FEET',
            self::KODE_60_FEET => '60 FEET',
        };
    }
}
