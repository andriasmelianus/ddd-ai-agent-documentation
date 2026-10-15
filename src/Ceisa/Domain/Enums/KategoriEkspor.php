<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum KategoriEkspor
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum KategoriEkspor: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case BKC_YANG_BELUM_DILUNASI_CUKAINYA = '61';
    case PLB = '51';
    case TPB_DARI_KAWASAN_DAUR_ULANG_BERIKAT = '46';
    case TPB_DARI_TEMPAT_LELANG_BERIKAT = '45';
    case TPB_DARI_TOKO_BEBAS_BEA = '44';
    case TPB_DARI_TEMPAT_PAMERAN_BERIKAT = '43';
    case TPB_DARI_GUDANG_BERIKAT = '42';
    case TPB_DARI_KAWASAN_BERIKAT = '41';
    case KHUSUS_BRG_KEPERLUAN_PENELITIAN = '38';
    case KHUSUS_BRG_CONTOH = '37';
    case KHUSUS_BRG_CINDERAMATA = '36';
    case KHUSUS_BRG_KEPERLUAN_IBADAH_UTK_UMUM_SOSIAL_PENDIDIKAN = '35';
    case KHUSUS_BRG_PINDAHAN = '34';
    case KHUSUS_BRG_KIRIMAN = '33';
    case KHUSUS_BRG_PERWAKILAN_BADAN_INTERNASIONAL = '32';
    case KHUSUS_BRG_PERWAKILAN_NEGARA_ASING = '31';
    case KITE_DENGAN_PEMBEBASAN_DAN_PENGEMBALIAN = '23';
    case YG_PD_SAAT_IMP_MNDPT_FAS_PENGEMBALIAN_BM_NIPER_DGN_PENG = '22';
    case YG_PD_SAAT_IMP_MNDPT_FAS_PEMBEBASAN_BM_NIPER_DGN_PEMBEBASAN = '21';
    case UMUM = '10';
    case EKSPOR_KITE_IKM = '24';
    case EKSPOR_BADAN_USAHA = '12';
    case EKSPOR_PERORANGAN = '11';
    case EKSPOR_DARI_ASAL_KB = '4';
    case EKSPOR_MELALUI_ASAL_KB = '3';
    case EKSPOR_DARI_UMUM = '2';
    case EKSPOR_MELALUI_UMUM = '1';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::BKC_YANG_BELUM_DILUNASI_CUKAINYA => 'BKC Yang Belum Dilunasi Cukainya',
            self::PLB => 'PLB',
            self::TPB_DARI_KAWASAN_DAUR_ULANG_BERIKAT => 'TPB Dari Kawasan Daur Ulang Berikat',
            self::TPB_DARI_TEMPAT_LELANG_BERIKAT => 'TPB Dari Tempat Lelang Berikat',
            self::TPB_DARI_TOKO_BEBAS_BEA => 'TPB Dari Toko Bebas Bea',
            self::TPB_DARI_TEMPAT_PAMERAN_BERIKAT => 'TPB Dari Tempat Pameran Berikat',
            self::TPB_DARI_GUDANG_BERIKAT => 'TPB Dari Gudang Berikat',
            self::TPB_DARI_KAWASAN_BERIKAT => 'TPB Dari Kawasan Berikat',
            self::KHUSUS_BRG_KEPERLUAN_PENELITIAN => 'Khusus Brg Keperluan Penelitian',
            self::KHUSUS_BRG_CONTOH => 'Khusus Brg Contoh',
            self::KHUSUS_BRG_CINDERAMATA => 'Khusus Brg Cinderamata',
            self::KHUSUS_BRG_KEPERLUAN_IBADAH_UTK_UMUM_SOSIAL_PENDIDIKAN => 'Khusus Brg Keperluan Ibadah Utk Umum Sosial Pendidikan Budaya/Olahraga dan Bencana Alam',
            self::KHUSUS_BRG_PINDAHAN => 'Khusus Brg Pindahan',
            self::KHUSUS_BRG_KIRIMAN => 'Khusus Brg Kiriman',
            self::KHUSUS_BRG_PERWAKILAN_BADAN_INTERNASIONAL => 'Khusus Brg Perwakilan Badan Internasional',
            self::KHUSUS_BRG_PERWAKILAN_NEGARA_ASING => 'Khusus Brg Perwakilan Negara Asing',
            self::KITE_DENGAN_PEMBEBASAN_DAN_PENGEMBALIAN => 'KITE dengan pembebasan dan pengembalian',
            self::YG_PD_SAAT_IMP_MNDPT_FAS_PENGEMBALIAN_BM_NIPER_DGN_PENG => 'Yg pd saat imp mndpt fas pengembalian BM(NIPER dgn pengembalian)',
            self::YG_PD_SAAT_IMP_MNDPT_FAS_PEMBEBASAN_BM_NIPER_DGN_PEMBEBASAN => 'Yg pd saat imp mndpt fas pembebasan BM(NIPER dgn pembebasan)',
            self::UMUM => 'Umum',
            self::EKSPOR_KITE_IKM => 'Ekspor KITE IKM',
            self::EKSPOR_BADAN_USAHA => 'Ekspor Badan Usaha',
            self::EKSPOR_PERORANGAN => 'Ekspor Perorangan',
            self::EKSPOR_DARI_ASAL_KB => 'Ekspor Dari - Asal KB',
            self::EKSPOR_MELALUI_ASAL_KB => 'Ekspor Melalui - Asal KB',
            self::EKSPOR_DARI_UMUM => 'Ekspor Dari - Umum',
            self::EKSPOR_MELALUI_UMUM => 'Ekspor Melalui - Umum',
        };
    }

    /**
     * Mendapatkan daftar kode dokumen yang dapat menggunakan kategori ekspor ini.
     *
     * @return list<string>
     */
    public function dokumen(): array
    {
        return match ($this) {
            self::BKC_YANG_BELUM_DILUNASI_CUKAINYA => ['30'],
            self::PLB => ['30'],
            self::TPB_DARI_KAWASAN_DAUR_ULANG_BERIKAT => ['30', '302'],
            self::TPB_DARI_TEMPAT_LELANG_BERIKAT => ['30', '302'],
            self::TPB_DARI_TOKO_BEBAS_BEA => ['30', '302'],
            self::TPB_DARI_TEMPAT_PAMERAN_BERIKAT => ['30', '302'],
            self::TPB_DARI_GUDANG_BERIKAT => ['30', '302'],
            self::TPB_DARI_KAWASAN_BERIKAT => ['30', '302'],
            self::KHUSUS_BRG_KEPERLUAN_PENELITIAN => ['30'],
            self::KHUSUS_BRG_CONTOH => ['30'],
            self::KHUSUS_BRG_CINDERAMATA => ['30'],
            self::KHUSUS_BRG_KEPERLUAN_IBADAH_UTK_UMUM_SOSIAL_PENDIDIKAN => ['30'],
            self::KHUSUS_BRG_PINDAHAN => ['30'],
            self::KHUSUS_BRG_KIRIMAN => ['30'],
            self::KHUSUS_BRG_PERWAKILAN_BADAN_INTERNASIONAL => ['30'],
            self::KHUSUS_BRG_PERWAKILAN_NEGARA_ASING => ['30'],
            self::KITE_DENGAN_PEMBEBASAN_DAN_PENGEMBALIAN => ['30'],
            self::YG_PD_SAAT_IMP_MNDPT_FAS_PENGEMBALIAN_BM_NIPER_DGN_PENG => ['30', '302'],
            self::YG_PD_SAAT_IMP_MNDPT_FAS_PEMBEBASAN_BM_NIPER_DGN_PEMBEBASAN => ['30', '302'],
            self::UMUM => ['30'],
            self::EKSPOR_KITE_IKM => ['302'],
            self::EKSPOR_BADAN_USAHA => ['302'],
            self::EKSPOR_PERORANGAN => ['302'],
            self::EKSPOR_DARI_ASAL_KB => ['33'],
            self::EKSPOR_MELALUI_ASAL_KB => ['33'],
            self::EKSPOR_DARI_UMUM => ['33'],
            self::EKSPOR_MELALUI_UMUM => ['33'],
        };
    }
}
