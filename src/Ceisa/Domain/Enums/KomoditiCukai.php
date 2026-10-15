<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum KomoditiCukai
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum KomoditiCukai: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case HASIL_TEMBAKAU = '1';
    case MMEA = '2';
    case EA = '3';
    case TEST = '4';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::HASIL_TEMBAKAU => 'HASIL TEMBAKAU',
            self::MMEA => 'MMEA',
            self::EA => 'EA',
            self::TEST => 'test',
        };
    }
}
