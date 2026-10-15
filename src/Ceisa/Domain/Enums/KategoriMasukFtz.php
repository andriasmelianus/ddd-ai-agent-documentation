<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum KategoriMasukFtz
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum KategoriMasukFtz: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case PEMASUKAN_BIASA = '001';
    case PEMASUKAN_SEMENTARA_KE_KAWASAN_BEBAS = '002';
    case PEMASUKAN_KEMBALI_KE_KAWASAN_BEBAS_DARI_LUAR_DAERAH_PABEAN = '003';
    case PEMASUKAN_KEMBALI_KE_KAWASAN_BEBAS_DARI_KAWASAN_BEBAS_L = '004';
    case PEMASUKAN_KEMBALI_KE_KAWASAN_BEBAS_DARI_TEMPAT_LAIN_DAL = '005';
    case PEMASUKAN_KHUSUS_KE_KAWASAN_BEBAS_OLEH_PENUMPANG_AWAK_S = '006';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::PEMASUKAN_BIASA => 'Pemasukan Biasa',
            self::PEMASUKAN_SEMENTARA_KE_KAWASAN_BEBAS => 'Pemasukan Sementara ke Kawasan Bebas',
            self::PEMASUKAN_KEMBALI_KE_KAWASAN_BEBAS_DARI_LUAR_DAERAH_PABEAN => 'Pemasukan Kembali ke Kawasan Bebas dari Luar Daerah Pabean',
            self::PEMASUKAN_KEMBALI_KE_KAWASAN_BEBAS_DARI_KAWASAN_BEBAS_L => 'Pemasukan Kembali ke Kawasan Bebas dari Kawasan Bebas Lainnya/TPB/KEK',
            self::PEMASUKAN_KEMBALI_KE_KAWASAN_BEBAS_DARI_TEMPAT_LAIN_DAL => 'Pemasukan Kembali ke Kawasan Bebas dari Tempat Lain Dalam Daerah Pabean',
            self::PEMASUKAN_KHUSUS_KE_KAWASAN_BEBAS_OLEH_PENUMPANG_AWAK_S => 'Pemasukan Khusus ke Kawasan Bebas oleh penumpang, awak sarana pengangkut, pelintas batas, atau barang kiriman',
        };
    }
}
