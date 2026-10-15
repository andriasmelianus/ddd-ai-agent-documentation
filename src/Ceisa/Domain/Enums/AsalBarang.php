<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum AsalBarang
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum AsalBarang: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case SEPENUHNYA_DIPEROLEH_DAN_ATAU_DIPRODUKSI_DI_LUAR_DAERAH = '1';
    case SEPENUHNYA_DIPEROLEH_DAN_ATAU_DIPRODUKSI_DI_TEMPAT_LAIN = '2';
    case KAWASAN_PABEAN = '3';
    case LAINNYA = '4';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::SEPENUHNYA_DIPEROLEH_DAN_ATAU_DIPRODUKSI_DI_LUAR_DAERAH => 'Sepenuhnya diperoleh dan/atau diproduksi di luar daerah pabean',
            self::SEPENUHNYA_DIPEROLEH_DAN_ATAU_DIPRODUKSI_DI_TEMPAT_LAIN => 'Sepenuhnya diperoleh dan/atau diproduksi di tempat lain dalam daerah pabean',
            self::KAWASAN_PABEAN => 'Kawasan Pabean',
            self::LAINNYA => 'Lainnya',
        };
    }
}
