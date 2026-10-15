<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum JenisKontainer
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum JenisKontainer: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case EMPTY = '4';
    case FCL = '8';
    case LCL = '7';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::EMPTY => 'EMPTY',
            self::FCL => 'FCL',
            self::LCL => 'LCL',
        };
    }
}
