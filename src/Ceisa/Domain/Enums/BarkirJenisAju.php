<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum BarkirJenisAju
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum BarkirJenisAju: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case CN = '1';
    case PIBK = '2';
    case CN_MARKETPLACE_E_COMMERCE = '3';
    case CN_MARKETPLACE_E_COMMERCE_KHUSUS_PENYELENGGARA_POS_YANG = '4';
    case CN_PMI = '5';
    case CN_HAJI = '6';
    case CN_HADIAH_LOMBA_PENGHARGAAN_INTERNASIONAL = '15';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::CN => 'CN',
            self::PIBK => 'PIBK',
            self::CN_MARKETPLACE_E_COMMERCE => 'CN Marketplace E-Commerce',
            self::CN_MARKETPLACE_E_COMMERCE_KHUSUS_PENYELENGGARA_POS_YANG => 'CN Marketplace E-Commerce Khusus Penyelenggara Pos yang ditunjuk',
            self::CN_PMI => 'CN PMI',
            self::CN_HAJI => 'CN HAJI',
            self::CN_HADIAH_LOMBA_PENGHARGAAN_INTERNASIONAL => 'CN HADIAH LOMBA / PENGHARGAAN INTERNASIONAL',
        };
    }

    /**
     * Mendapatkan keterangan tambahan dari referensi.
     */
    public function keterangan(): ?string
    {
        return match ($this) {
            self::CN => 'Consignment Note',
            self::PIBK => 'Pemberitahuan Impor Barang Khusus',
            self::CN_MARKETPLACE_E_COMMERCE => 'Marketplace E-commerce untuk PJT',
            self::CN_MARKETPLACE_E_COMMERCE_KHUSUS_PENYELENGGARA_POS_YANG => 'Marketplace E-commerce untuk PT. Pos Indonesia',
            self::CN_PMI => 'Consignment Note dengan fasilitas untuk Pekerja Migran Indonesia (PMI)',
            self::CN_HAJI => 'Consigment Note dengan fasilitas untuk Jemaah Haji',
            self::CN_HADIAH_LOMBA_PENGHARGAAN_INTERNASIONAL => 'Consigment Note dengan fasilitas untuk penerima hadiah perlombaan / penghargaan Internasional',
        };
    }
}
