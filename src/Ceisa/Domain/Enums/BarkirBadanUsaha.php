<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum BarkirBadanUsaha
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum BarkirBadanUsaha: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case BADAN_USAHA = '1';
    case NON_BADAN_USAHA = '2';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::BADAN_USAHA => 'Badan Usaha',
            self::NON_BADAN_USAHA => 'Non Badan Usaha',
        };
    }
}
