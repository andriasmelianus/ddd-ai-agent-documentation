<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum AsalBarangFtz
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum AsalBarangFtz: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case SEPENUHNYA_DIPEROLEH_DAN_ATAU_DIPRODUKSI_DI_LUAR_DAERAH = '1';
    case SEPENUHNYA_DIPEROLEH_DAN_ATAU_DIPRODUKSI_DI_TEMPAT_LAIN = '2';
    case SEPENUHNYA_DIPEROLEH_DAN_ATAU_DIPRODUKSI_DI_KAWASAN_BEB = '3';
    case SEPENUHNYA_DIPEROLEH_DAN_ATAU_DIPRODUKSI_DI_TEMPAT_PENI = '4';
    case LAINNYA = '5';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::SEPENUHNYA_DIPEROLEH_DAN_ATAU_DIPRODUKSI_DI_LUAR_DAERAH => 'sepenuhnya diperoleh dan/atau diproduksi di luar Daerah Pabean',
            self::SEPENUHNYA_DIPEROLEH_DAN_ATAU_DIPRODUKSI_DI_TEMPAT_LAIN => 'sepenuhnya diperoleh dan/atau diproduksi di tempat lain dalam Daerah Pabean',
            self::SEPENUHNYA_DIPEROLEH_DAN_ATAU_DIPRODUKSI_DI_KAWASAN_BEB => 'sepenuhnya diperoleh dan/atau diproduksi di Kawasan Bebas atau Kawasan Bebas lainnya dengan menggunakan bahan baku dan/atau bahan penolong yang sepenuhnya berasal dari Kawasan Bebas atau Kawasan Bebas lainnya',
            self::SEPENUHNYA_DIPEROLEH_DAN_ATAU_DIPRODUKSI_DI_TEMPAT_PENI => 'sepenuhnya diperoleh dan/atau diproduksi di Tempat Penimbunan Berikat atau Kawasan Ekonomi Khusus',
            self::LAINNYA => 'lainnya',
        };
    }
}
