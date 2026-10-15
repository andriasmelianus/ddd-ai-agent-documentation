<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum JenisIdentitas
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum JenisIdentitas: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case PASPOR = '2';
    case KTP = '3';
    case LAINNYA = '4';
    case NPWP_15_DIGIT = '5';
    case NPWP_16_DIGIT = '6';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::PASPOR => 'PASPOR',
            self::KTP => 'KTP',
            self::LAINNYA => 'LAINNYA',
            self::NPWP_15_DIGIT => 'NPWP 15 DIGIT',
            self::NPWP_16_DIGIT => 'NPWP 16 DIGIT',
        };
    }
}
