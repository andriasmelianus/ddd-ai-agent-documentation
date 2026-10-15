<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum KodeLaporanInventori
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum KodeLaporanInventori: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case LAPORAN_PEMASUKAN_BARANG = '1';
    case LAPORAN_PENGELUARAN_BARANG = '2';
    case LAPORAN_MUTASI_BAHAN_BAKU_DAN_BAHAN_PENOLONG = '3';
    case LAPORAN_MUTASI_BARANG_JADI = '4';
    case LAPORAN_MUTASI_BARANG_MODAL_DAN_BARANG_LAIN = '5';
    case LAPORAN_MUTASI_BARANG_YANG_DITOLAK_REJECT_DAN_SISA_PRODUKSI = '6';
    case LAPORAN_POSISI_BARANG_DALAM_PROSES = '7';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::LAPORAN_PEMASUKAN_BARANG => 'LAPORAN PEMASUKAN BARANG',
            self::LAPORAN_PENGELUARAN_BARANG => 'LAPORAN PENGELUARAN BARANG',
            self::LAPORAN_MUTASI_BAHAN_BAKU_DAN_BAHAN_PENOLONG => 'LAPORAN MUTASI BAHAN BAKU DAN BAHAN PENOLONG',
            self::LAPORAN_MUTASI_BARANG_JADI => 'LAPORAN MUTASI BARANG JADI',
            self::LAPORAN_MUTASI_BARANG_MODAL_DAN_BARANG_LAIN => 'LAPORAN MUTASI BARANG MODAL DAN BARANG LAIN',
            self::LAPORAN_MUTASI_BARANG_YANG_DITOLAK_REJECT_DAN_SISA_PRODUKSI => 'LAPORAN MUTASI BARANG YANG DITOLAK (REJECT) DAN SISA PRODUKSI',
            self::LAPORAN_POSISI_BARANG_DALAM_PROSES => 'LAPORAN POSISI BARANG DALAM PROSES',
        };
    }
}
