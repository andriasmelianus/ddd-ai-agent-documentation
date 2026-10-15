<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum Fasilitas
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum Fasilitas: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case TANPA_FASILITAS = '00';
    case PMA = '01';
    case PMDN = '02';
    case KITE_KEMUDAHAN_IMPOR_TUJUAN_EKSPOR_KITE_PEMBEBASAN = '03';
    case INSENTIF_OTOMOTIF = '04';
    case PTNI = '05';
    case ATIGA = '06';
    case KEPERLUAN_PERTAHANAN_DAN_KEAMANAN = '07';
    case INDUSTRI_STRATEGIS = '08';
    case PROYEK_PEMERINTAH_YANG_DIBIAYAI_DENGAN_HIBAH_ATAU_DANA = '09';
    case PERWAKILAN_NEGARA_ASING_DAN_PEJABATNYA = '10';
    case BADAN_INTERNASIONAL_DAN_PEJABATNYA = '11';
    case KODE_12 = '12';
    case HADIAH_UNTUK_IBADAH_UMUM_AMAL_SOSIAL_ATAU_KEBUDAYAAN = '13';
    case KEPERLUAN_MUSEUM_KEBUN_BINATANG_DAN_TEMPAT_LAIN_YANG_SE = '14';
    case KEPERLUAN_KHUSUS_TUNA_NETRA_DAN_PENYANDANG_CACAT = '15';
    case PENELITIAN_DAN_PENGEMBANGAN_ILMU_PENGETAHUAN = '16';
    case BARANG_CONTOH_TIDAK_UNTUK_DIPERDAGANGKAN = '17';
    case BARANG_RE_IMPOR_YANG_MENDAPAT_FASILITAS_BAPEKSTA = '18';
    case PENCEGAHAN_PENCEMARAN_LINGKUNGAN = '19';
    case BIBIT_DAN_BENIH_UNTUK_PENGEMBANGAN_PERTANIAN_PETERNAKAN = '20';
    case HASIL_LAUT = '21';
    case BARANG_YANG_SEMULA_DIEKSPOR_UNTUK_PERBAIKAN_PENGERJAAN = '22';
    case BAHAN_TERAPI_MANUSIA_PENGELOMPOKAN_DARAH_DAN_BAHAN_PENJ = '23';
    case DIIMPOR_PLEH_PEMERINTAH_PUSAT_DAERAH_UNTUK_KEPENTINGAN_UMUM = '24';
    case BERASAL_DARI_KAWASAN_BERIKAT = '25';
    case BERASAL_DARI_GUDANG_BERIKAT = '26';
    case KODE_27 = '27';
    case KEPUTUSAN_LAINNYA_SELAIN_01_S_D_24_DAN_29_S_D_36 = '28';
    case PERTAMINA = '29';
    case PEMBANGUNAN_ATAU_PENGEMBANGAN_INDUSTRI_NON_PMA_PMDN = '30';
    case BARANG_REIMPOR_YANG_TIDAK_MENDAPATKAN_FASILITAS_BAPEKSTA = '31';
    case BARANG_YANG_SEMULA_DIEKSPOR_UNTUK_PENGERJAAN_PROYEK_PAM = '32';
    case BERASAL_DARI_TOKO_BEBAS_BEA_TBB = '33';
    case BERASAL_DARI_ENTREPORT_TUJUAN_PAMERAN_ETP = '34';
    case IMPOR_SEMENTARA = '35';
    case AIDA = '36';
    case BKPM = '37';
    case PERTAMBANGAN = '38';
    case PENUNDAAN_BEA_MASUK_CUKAI_DAN_ATAU_PAJAK = '39';
    case KITE_PENGEMBALIAN = '40';
    case KITE_IKM = '41';
    case KITE_IKM_MESIN = '42';
    case KITE_BARANG_CONTOH = '43';
    case KETERANGAN_KARANTINA = '50';
    case KETERANGAN_KESEHATAN_POM = '51';
    case KETERANGAN_PAJAK = '52';
    case KETERANGAN_LAINNYA_SELAIN_50_S_D_52 = '53';
    case PREFERENSI_TARIF_IMPORTASI_ASEAN_CHINA_ACFTA = '54';
    case PREFERENSI_TARIF_IMPORTASI_ASEAN_KOREA_AKFTA = '55';
    case PREFERENSI_TARIF_INDONESIA_JAPAN_IJ_EPA = '56';
    case PREFERENSI_TARIF_IMPORTASI_ASEAN_INDIA_AIFTA = '57';
    case PREFERENSI_TARIF_ASEAN_AUSTRALIA_NEW_ZEALAND_AANZFTA = '58';
    case PREFERENSI_TARIF_INDONESIA_PAKISTAN_FTA = '59';
    case USDFS = '60';
    case AJCEP = '61';
    case PREFERENSI_TARIF_INDONESIA_PALESTINA = '62';
    case PREFERENSI_TARIF_INDONESIA_CHILI_ICCEPA = '63';
    case PREFERENSI_TARIF_ASEAN_HONGKONG_CHINA_FTA = '64';
    case PREFERENSI_TARIF_INDONESIA_AUSTRALIA_IACEPA = '65';
    case IECEPA = '67';
    case PTA_D8 = '68';
    case RCEP = '69';
    case PEMBEBASAN_BEA_MASUK_BARANG_DAN_BAHAN_UNTUK_PEMBANGUNAN = '70';
    case PEMBEBASAN_BEA_MASUK_BARANG_DAN_BAHAN_DALAM_RANGKA_PEMB = '71';
    case IKCEPA = '72';
    case USDFS_IKCEPA = '73';
    case IUAECEPA = '74';
    case TARIFF_RATE_QUOTA_TRQ = '79';
    case PENANGGULANGAN_BENCANA = '80';
    case BM_DTP_KHUSUS = '81';
    case VAKSIN_COVID_19 = '83';
    case KEK = '84';
    case PREFERENSI_NILAI_KANDUNGAN_LOKAL = '88';
    case INSENTIF_KBLBB_SKEMA_PPNBM_DTP = '89';
    case TERDAPAT_BEBERAPA_FASILITAS_PEMENUHAN_PERYARATAN_IMPOR = '99';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::TANPA_FASILITAS => 'Tanpa Fasilitas',
            self::PMA => 'PMA',
            self::PMDN => 'PMDN',
            self::KITE_KEMUDAHAN_IMPOR_TUJUAN_EKSPOR_KITE_PEMBEBASAN => 'KITE/Kemudahan Impor Tujuan Ekspor - KITE PEMBEBASAN',
            self::INSENTIF_OTOMOTIF => 'Insentif Otomotif',
            self::PTNI => 'PTNI',
            self::ATIGA => 'ATIGA',
            self::KEPERLUAN_PERTAHANAN_DAN_KEAMANAN => 'Keperluan Pertahanan dan Keamanan',
            self::INDUSTRI_STRATEGIS => 'Industri Strategis',
            self::PROYEK_PEMERINTAH_YANG_DIBIAYAI_DENGAN_HIBAH_ATAU_DANA => 'Proyek pemerintah yang dibiayai dengan hibah atau dana pinjaman luar negeri',
            self::PERWAKILAN_NEGARA_ASING_DAN_PEJABATNYA => 'Perwakilan Negara Asing dan Pejabatnya',
            self::BADAN_INTERNASIONAL_DAN_PEJABATNYA => 'Badan Internasional dan Pejabatnya',
            self::KODE_12 => '--',
            self::HADIAH_UNTUK_IBADAH_UMUM_AMAL_SOSIAL_ATAU_KEBUDAYAAN => 'Hadiah untuk ibadah umum, amal,sosial, atau kebudayaan',
            self::KEPERLUAN_MUSEUM_KEBUN_BINATANG_DAN_TEMPAT_LAIN_YANG_SE => 'Keperluan museum, kebun binatang dan tempat lain yang semacamnya untuk umum',
            self::KEPERLUAN_KHUSUS_TUNA_NETRA_DAN_PENYANDANG_CACAT => 'Keperluan khusus tuna netra dan penyandang cacat',
            self::PENELITIAN_DAN_PENGEMBANGAN_ILMU_PENGETAHUAN => 'Penelitian dan Pengembangan Ilmu Pengetahuan',
            self::BARANG_CONTOH_TIDAK_UNTUK_DIPERDAGANGKAN => 'Barang contoh tidak untuk diperdagangkan',
            self::BARANG_RE_IMPOR_YANG_MENDAPAT_FASILITAS_BAPEKSTA => 'Barang re-impor yang mendapat fasilitas Bapeksta',
            self::PENCEGAHAN_PENCEMARAN_LINGKUNGAN => 'Pencegahan Pencemaran Lingkungan',
            self::BIBIT_DAN_BENIH_UNTUK_PENGEMBANGAN_PERTANIAN_PETERNAKAN => 'Bibit dan benih untuk pengembangan pertanian, peternakan dan perikanan',
            self::HASIL_LAUT => 'Hasil laut',
            self::BARANG_YANG_SEMULA_DIEKSPOR_UNTUK_PERBAIKAN_PENGERJAAN => 'Barang yang semula diekspor untuk perbaikan, pengerjaan dan pengujian',
            self::BAHAN_TERAPI_MANUSIA_PENGELOMPOKAN_DARAH_DAN_BAHAN_PENJ => 'Bahan terapi manusia, pengelompokan darah, dan bahan penjenisan jaringan',
            self::DIIMPOR_PLEH_PEMERINTAH_PUSAT_DAERAH_UNTUK_KEPENTINGAN_UMUM => 'Diimpor pleh pemerintah pusat/ daerah untuk kepentingan umum',
            self::BERASAL_DARI_KAWASAN_BERIKAT => 'Berasal dari Kawasan Berikat',
            self::BERASAL_DARI_GUDANG_BERIKAT => 'Berasal dari Gudang Berikat',
            self::KODE_27 => '--',
            self::KEPUTUSAN_LAINNYA_SELAIN_01_S_D_24_DAN_29_S_D_36 => 'Keputusan lainnya (selain 01 s/d 24 dan 29 s/d 36)',
            self::PERTAMINA => 'Pertamina',
            self::PEMBANGUNAN_ATAU_PENGEMBANGAN_INDUSTRI_NON_PMA_PMDN => 'Pembangunan atau pengembangan Industri (non PMA/PMDN)',
            self::BARANG_REIMPOR_YANG_TIDAK_MENDAPATKAN_FASILITAS_BAPEKSTA => 'Barang reimpor yang tidak mendapatkan fasilitas Bapeksta',
            self::BARANG_YANG_SEMULA_DIEKSPOR_UNTUK_PENGERJAAN_PROYEK_PAM => 'Barang yang semula diekspor untuk pengerjaan proyek, pameran dan pengemasan',
            self::BERASAL_DARI_TOKO_BEBAS_BEA_TBB => 'Berasal dari Toko Bebas Bea (TBB)',
            self::BERASAL_DARI_ENTREPORT_TUJUAN_PAMERAN_ETP => 'Berasal dari Entreport Tujuan Pameran (ETP)',
            self::IMPOR_SEMENTARA => 'Impor Sementara',
            self::AIDA => 'AIDA',
            self::BKPM => 'BKPM',
            self::PERTAMBANGAN => 'Pertambangan',
            self::PENUNDAAN_BEA_MASUK_CUKAI_DAN_ATAU_PAJAK => 'Penundaan Bea Masuk, Cukai dan/atau Pajak',
            self::KITE_PENGEMBALIAN => 'KITE Pengembalian',
            self::KITE_IKM => 'KITE IKM',
            self::KITE_IKM_MESIN => 'KITE IKM (Mesin)',
            self::KITE_BARANG_CONTOH => 'KITE (Barang Contoh)',
            self::KETERANGAN_KARANTINA => 'Keterangan Karantina',
            self::KETERANGAN_KESEHATAN_POM => 'Keterangan Kesehatan / POM',
            self::KETERANGAN_PAJAK => 'Keterangan Pajak',
            self::KETERANGAN_LAINNYA_SELAIN_50_S_D_52 => 'Keterangan lainnya (selain 50 s/d 52)',
            self::PREFERENSI_TARIF_IMPORTASI_ASEAN_CHINA_ACFTA => 'Preferensi Tarif Importasi Asean-China (ACFTA)',
            self::PREFERENSI_TARIF_IMPORTASI_ASEAN_KOREA_AKFTA => 'Preferensi Tarif Importasi Asean-Korea (AKFTA)',
            self::PREFERENSI_TARIF_INDONESIA_JAPAN_IJ_EPA => 'Preferensi Tarif Indonesia-Japan (IJ-EPA)',
            self::PREFERENSI_TARIF_IMPORTASI_ASEAN_INDIA_AIFTA => 'Preferensi Tarif Importasi Asean-India (AIFTA)',
            self::PREFERENSI_TARIF_ASEAN_AUSTRALIA_NEW_ZEALAND_AANZFTA => 'Preferensi Tarif Asean-Australia-New Zealand (AANZFTA)',
            self::PREFERENSI_TARIF_INDONESIA_PAKISTAN_FTA => 'Preferensi Tarif Indonesia Pakistan FTA',
            self::USDFS => 'USDFS',
            self::AJCEP => 'AJCEP',
            self::PREFERENSI_TARIF_INDONESIA_PALESTINA => 'Preferensi Tarif Indonesia - Palestina',
            self::PREFERENSI_TARIF_INDONESIA_CHILI_ICCEPA => 'Preferensi Tarif Indonesia - Chili (ICCEPA)',
            self::PREFERENSI_TARIF_ASEAN_HONGKONG_CHINA_FTA => 'Preferensi Tarif ASEAN - Hongkong, China FTA',
            self::PREFERENSI_TARIF_INDONESIA_AUSTRALIA_IACEPA => 'Preferensi Tarif Indonesia Australia (IACEPA)',
            self::IECEPA => 'IECEPA',
            self::PTA_D8 => 'PTA-D8',
            self::RCEP => 'RCEP',
            self::PEMBEBASAN_BEA_MASUK_BARANG_DAN_BAHAN_UNTUK_PEMBANGUNAN => 'Pembebasan Bea Masuk barang dan bahan untuk pembangunan dan pengembangan industri dalam rangka penanaman modal',
            self::PEMBEBASAN_BEA_MASUK_BARANG_DAN_BAHAN_DALAM_RANGKA_PEMB => 'Pembebasan Bea Masuk barang dan bahan dalam rangka pembangunan dan pengembangan industri untuk jangka waktu tertentu',
            self::IKCEPA => 'IKCEPA',
            self::USDFS_IKCEPA => 'USDFS-IKCEPA',
            self::IUAECEPA => 'IUAECEPA',
            self::TARIFF_RATE_QUOTA_TRQ => 'Tariff Rate Quota (TRQ)',
            self::PENANGGULANGAN_BENCANA => 'Penanggulangan Bencana',
            self::BM_DTP_KHUSUS => 'BM DTP KHUSUS',
            self::VAKSIN_COVID_19 => 'Vaksin Covid 19',
            self::KEK => 'KEK',
            self::PREFERENSI_NILAI_KANDUNGAN_LOKAL => 'Preferensi Nilai Kandungan Lokal',
            self::INSENTIF_KBLBB_SKEMA_PPNBM_DTP => 'Insentif KBLBB Skema PPnBM DTP',
            self::TERDAPAT_BEBERAPA_FASILITAS_PEMENUHAN_PERYARATAN_IMPOR => 'Terdapat beberapa fasilitas/ pemenuhan peryaratan impor untuk 1 dokumen',
        };
    }
}
