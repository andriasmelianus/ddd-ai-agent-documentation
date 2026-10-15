<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum StatusPengusaha
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum StatusPengusaha: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case KOPERASI = '1';
    case PMDN_MIGAS = '2';
    case PMDN_NON_MIGAS = '3';
    case PMA_MIGAS = '4';
    case PMA_NON_MIGAS = '5';
    case BUMN = '6';
    case BUMD = '7';
    case PERORANGAN = '8';
    case USAHA_KECIL_MIKRO_DAN_MENENGAH = '9';
    case LAINNYA = '10';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::KOPERASI => 'Koperasi',
            self::PMDN_MIGAS => 'PMDN (Migas)',
            self::PMDN_NON_MIGAS => 'PMDN (Non Migas)',
            self::PMA_MIGAS => 'PMA (Migas)',
            self::PMA_NON_MIGAS => 'PMA (Non Migas)',
            self::BUMN => 'BUMN',
            self::BUMD => 'BUMD',
            self::PERORANGAN => 'Perorangan',
            self::USAHA_KECIL_MIKRO_DAN_MENENGAH => 'Usaha Kecil, Mikro, dan Menengah',
            self::LAINNYA => 'Lainnya',
        };
    }
}
