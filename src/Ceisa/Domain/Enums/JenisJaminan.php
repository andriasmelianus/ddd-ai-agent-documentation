<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum JenisJaminan
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum JenisJaminan: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case TUNAI = '1';
    case BANK_GARANSI = '2';
    case CUSTOMS_BOND = '3';
    case TERTULIS_CG = '4';
    case STTJ = '5';
    case PUSAT = '6';
    case INDONESIA_EXIMBANK = '7';
    case PERUSAHAAN_PENJAMINAN = '8';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::TUNAI => 'Tunai',
            self::BANK_GARANSI => 'Bank Garansi',
            self::CUSTOMS_BOND => 'Customs Bond',
            self::TERTULIS_CG => 'Tertulis/CG',
            self::STTJ => 'STTJ',
            self::PUSAT => 'Pusat',
            self::INDONESIA_EXIMBANK => 'Indonesia EximBank',
            self::PERUSAHAAN_PENJAMINAN => 'Perusahaan Penjaminan',
        };
    }
}
