<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum BarkirJenisPibkCnFtz
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum BarkirJenisPibkCnFtz: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case BARANG_E_COMMERCE_FTZ = '11';
    case BARANG_HASIL_PRODUKSI_FTZ_IKM = '12';
    case BARANG_TRANSIT_FTZ = '13';
    case BARANG_RETUR_FTZ = '14';
    case BARANG_PERSONEL_EFFECT_FTZ = '15';
    case LAINNYA = '9';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::BARANG_E_COMMERCE_FTZ => 'Barang e-Commerce FTZ',
            self::BARANG_HASIL_PRODUKSI_FTZ_IKM => 'Barang Hasil Produksi FTZ (IKM)',
            self::BARANG_TRANSIT_FTZ => 'Barang Transit FTZ',
            self::BARANG_RETUR_FTZ => 'Barang Retur FTZ',
            self::BARANG_PERSONEL_EFFECT_FTZ => 'Barang Personel Effect FTZ',
            self::LAINNYA => 'Lainnya',
        };
    }
}
