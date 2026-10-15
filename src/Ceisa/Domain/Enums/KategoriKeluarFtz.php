<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum KategoriKeluarFtz
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum KategoriKeluarFtz: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case PENGELUARAN_BIASA = '001';
    case PENGELUARAN_KHUSUS_BARANG_KIRIMAN_POS_ATAU_JASA_TITIPAN = '002';
    case PENGELUARAN_KHUSUS_BARANG_PINDAHAN = '003';
    case PENGELUARAN_KHUSUS_BARANG_PERWAKILAN_NEGARA_ASING_ATAU = '004';
    case PENGELUARAN_KHUSUS_BARANG_UNTUK_KEPERLUAN_IBADAH_UNTUK = '005';
    case PENGELUARAN_KHUSUS_BARANG_CINDERAMATA = '006';
    case PENGELUARAN_KHUSUS_BARANG_CONTOH = '007';
    case PENGELUARAN_KHUSUS_BARANG_UNTUK_KEPERLUAN_PENELITIAN = '008';
    case PENGELUARAN_SEMENTARA_DARI_KAWASAN_BEBAS = '009';
    case PENGELUARAN_KEMBALI_DARI_KAWASAN_BEBAS_KE_LUAR_DAERAH_P = '010';
    case PENGELUARAN_KEMBALI_DARI_KAWASAN_BEBAS_KE_KAWASAN_BEBAS = '011';
    case PENGELUARAN_KEMBALI_DARI_KAWASAN_BEBAS_KE_TEMPAT_LAIN_D = '012';
    case PENGELUARAN_KHUSUS_KE_KAWASAN_BEBAS_OLEH_PENUMPANG_AWAK = '013';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::PENGELUARAN_BIASA => 'Pengeluaran Biasa',
            self::PENGELUARAN_KHUSUS_BARANG_KIRIMAN_POS_ATAU_JASA_TITIPAN => 'Pengeluaran Khusus Barang Kiriman (Pos atau Jasa Titipan)',
            self::PENGELUARAN_KHUSUS_BARANG_PINDAHAN => 'Pengeluaran Khusus Barang Pindahan',
            self::PENGELUARAN_KHUSUS_BARANG_PERWAKILAN_NEGARA_ASING_ATAU => 'Pengeluaran Khusus Barang Perwakilan Negara Asing atau Badan Internasional',
            self::PENGELUARAN_KHUSUS_BARANG_UNTUK_KEPERLUAN_IBADAH_UNTUK => 'Pengeluaran Khusus Barang untuk Keperluan Ibadah untuk Umum, Sosial, Pendidikan, Kebudayaan, Olah Raga, atau Bencana Alam',
            self::PENGELUARAN_KHUSUS_BARANG_CINDERAMATA => 'Pengeluaran Khusus Barang Cinderamata',
            self::PENGELUARAN_KHUSUS_BARANG_CONTOH => 'Pengeluaran Khusus Barang Contoh',
            self::PENGELUARAN_KHUSUS_BARANG_UNTUK_KEPERLUAN_PENELITIAN => 'Pengeluaran Khusus Barang untuk Keperluan Penelitian',
            self::PENGELUARAN_SEMENTARA_DARI_KAWASAN_BEBAS => 'Pengeluaran Sementara dari Kawasan Bebas',
            self::PENGELUARAN_KEMBALI_DARI_KAWASAN_BEBAS_KE_LUAR_DAERAH_P => 'Pengeluaran Kembali dari Kawasan Bebas ke Luar Daerah Pabean atas Barang yang Nyata-nyata Berasal dari Luar Daerah Pabean',
            self::PENGELUARAN_KEMBALI_DARI_KAWASAN_BEBAS_KE_KAWASAN_BEBAS => 'Pengeluaran Kembali dari Kawasan Bebas ke Kawasan Bebas Lainnya/TPB/KEK atas Barang yang Nyata-nyata Berasal dari Kawasan Bebas Lainnya/TPB/KEK',
            self::PENGELUARAN_KEMBALI_DARI_KAWASAN_BEBAS_KE_TEMPAT_LAIN_D => 'Pengeluaran Kembali dari Kawasan Bebas ke Tempat Lain Dalam Daerah Pabean atas Barang yang Nyata-nyata Berasal dari Tempat Lain Dalam Daerah Pabean',
            self::PENGELUARAN_KHUSUS_KE_KAWASAN_BEBAS_OLEH_PENUMPANG_AWAK => 'Pengeluaran Khusus ke Kawasan Bebas oleh penumpang, awak sarana pengangkut, pelintas batas, atau barang kiriman',
        };
    }
}
