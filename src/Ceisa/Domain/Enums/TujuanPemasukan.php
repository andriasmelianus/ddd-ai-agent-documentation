<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum TujuanPemasukan
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum TujuanPemasukan: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case EKS_DIPERBAIKI = '1';
    case EKS_DISUBKONTRAKKAN = '2';
    case EKS_DIPINJAMKAN = '3';
    case RETURN_BAHAN_BAKU = '4';
    case PEMASUKAN_MELEWATI_JANGKA_WAKTU = '5';
    case LAINNYA = '6';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::EKS_DIPERBAIKI => 'EKS-DIPERBAIKI',
            self::EKS_DISUBKONTRAKKAN => 'EKS-DISUBKONTRAKKAN',
            self::EKS_DIPINJAMKAN => 'EKS-DIPINJAMKAN',
            self::RETURN_BAHAN_BAKU => 'RETURN BAHAN BAKU',
            self::PEMASUKAN_MELEWATI_JANGKA_WAKTU => 'PEMASUKAN MELEWATI JANGKA WAKTU',
            self::LAINNYA => 'LAINNYA',
        };
    }

    /**
     * Mendapatkan kode dokumen yang berlaku (BC 2.6.2).
     */
    public function kodeDokumen(): string
    {
        return '262';
    }
}
