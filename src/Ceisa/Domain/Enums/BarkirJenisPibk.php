<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum BarkirJenisPibk
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum BarkirJenisPibk: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case BARANG_PINDAHAN = '1';
    case BARANG_KIRIMAN_MELALUI_PJT = '2';
    case BARANG_IMPOR_SEMENTARA_DIBAWA_PENUMPANG = '3';
    case BARANG_IMPOR_TERTENTU = '4';
    case BARANG_PRIBADI_PENUMPANG = '5';
    case LAINNYA = '9';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::BARANG_PINDAHAN => 'Barang Pindahan',
            self::BARANG_KIRIMAN_MELALUI_PJT => 'Barang Kiriman Melalui PJT',
            self::BARANG_IMPOR_SEMENTARA_DIBAWA_PENUMPANG => 'Barang Impor Sementara Dibawa Penumpang',
            self::BARANG_IMPOR_TERTENTU => 'Barang Impor Tertentu',
            self::BARANG_PRIBADI_PENUMPANG => 'Barang Pribadi Penumpang',
            self::LAINNYA => 'Lainnya',
        };
    }
}
