<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum TpsKodeDokumenInOut
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum TpsKodeDokumenInOut: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case SPPB_BC_2_0 = '1';
    case SPPB_BC_2_3 = '2';
    case PLP = '3';
    case SPPB_BC_1_2 = '4';
    case SPPF_BCF_2_6 = '5';
    case NPE = '6';
    case PKBE = '7';
    case PPB = '8';
    case BCF_1_5_BARANG_TIDAK_DIKUASAI = '9';
    case SPPB_EMPTY_CONTAINER = '10';
    case SPPBE_BATAL_EKSPOR = '11';
    case SPPBE_PINDAH_MUAT_BARANG_EKSPOR = '12';
    case SPPB_PIBK = '13';
    case RETURNABLE_PACKAGE = '14';
    case PENIMBUNAN_DILUAR_KAWASAN_PABEAN = '15';
    case SPJM_GRAHA_SEGARA = '19';
    case BC_1_1_A_SP3B = '20';
    case SPPB_PIB_MANUAL = '21';
    case PENGELUARAN_BARANG_PAKET_POS = '22';
    case SKEP_PEMUSNAHAN_BARANG_IMPOR = '23';
    case SURAT_PENGELUARAN_BARANG_UNTUK_BARANG_BUKTI_KE_PENGADILAN = '24';
    case PENGELUARAN_BARANG_HIBAH = '25';
    case SURAT_IJIN_PENGELUARAN_BARANG_UNTUK_LELANG = '26';
    case PENGELUARAN_BARANG_PERS_RELEASE = '27';
    case DOKUMEN_BC_1_2 = '28';
    case SPPB_EKS_PERGANTIAN_KONTAINER = '29';
    case PENGELUARAN_BARANG_PENEGAHAN_SEBAGIAN = '30';
    case PENGELUARAN_BARANG_PENEGAHAN_SELURUHNYA = '31';
    case EMPTY_CONTAINER_EKSPOR = '32';
    case DOK_IJIN_EKSPOR_RETURNABLE_PACKAGE = '33';
    case SPPB_BC_12_KPPT = '34';
    case ATA_CARNET_IMPOR = '35';
    case CPD_CARNET_IMPOR = '36';
    case ATA_CARNET_EKSPOR = '37';
    case CPD_CARNET_EKSPOR = '38';
    case PERSETUJUAN_PART_OF_IMPORTIR_MITA_AEO = '17';
    case PERSETUJUAN_PART_OF_IMPORTIR_NON_MITA_AEO = '18';
    case PERSETUJUAN_SHORT_SHIP = '16';
    case DOKUMEN_PEMERIKSAAN_KARANTINA = '39';
    case PENGELUARAN_EMPTY_CONTIANER_EX_STRIPPING = '40';
    case BC_1_6 = '41';
    case PERSETUJUAN_KELUAR_BARANG_KIRIMAN = '42';
    case SPPBMC = '43';
    case DOKUMEN_PENGELUARAN_BARANG_IMPOR_DENGAN_BC_1_1_OUTWARD = '44';
    case NPP3BET_NOTA_PERSETUJUAN_PENGELUARAN_P3BET = '45';
    case PEMASUKAN_SEBAGIAN_BARANG_EKSPOR = '46';
    case PPFTZ_01_ASAL_LDP_IMPOR = '47';
    case PPFTZ_01_TUJUAN_TLDDP = '48';
    case PPFTZ_01_TUJUAN_LDP_EKSPOR = '49';
    case PPFTZ_02_MASUK = '50';
    case PPFTZ_02_KELUAR = '51';
    case PPFTZ_03 = '52';
    case IZIN_PENGELUARAN_PENDAHULUAN = '53';
    case SP3K_KONTAINER_KOSONG = '54';
    case DOKUMEN_BC_1_1B_SP3B_EKSPOR = '55';
    case DOKUMEN_PEMASUKAN_BARANG_EKSPOR_DENGAN_BC_1_1_INWARD_MANIFES = '56';
    case DOKUMEN_BC_1_3 = '57';
    case PENYERAHAN_KE_KEMENTRIAN_LEMBAGA_LAIN = '58';
    case KEK_PEMASUKAN_DARI_LDP = '61';
    case SURAT_PERSETUJUAN_PENGELUARAN_BARANG_EKSPOR_DAN_TRANSHI = '68';
    case DOKUMEN_PENGELUARAN_LAINNYA = '99';
    case KEK_PEMASUKAN_DARI_TLDDP = '63';
    case KEK_PENGELUARAN_KE_KEK_TPB_FTZ = '64';
    case KEK_PENGELUARAN_KE_LDP = '65';
    case KEK_PENGELUARAN_KE_TLDDP = '66';
    case DOKUMEN_BC_1_2_FTZ = '67';
    case DOKUMEN_NPE_EKSPOR_BARANG_KIRIMAN = '69';
    case DOKUMEN_BC_1_4 = '59';
    case SPPB_RUSH_HANDLING = '60';
    case KEK_PEMASUKAN_MELALUI_BARANG_KIRIMAN = '62';
    case ATA_CARNET_RE_IMPOR = '73';
    case CPD_CARNET_RE_IMPOR = '74';
    case ATA_CARNET_RE_EKSPOR = '75';
    case CPD_CARNET_RE_EKSPOR = '76';
    case SPPB_CN_FTZ_LDP_FTZ = '70';
    case SPPB_CN_FTZ_FTZ_TLDPP = '71';
    case SPPB_CN_FTZ_TLDPP_FTZ = '72';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::SPPB_BC_2_0 => 'SPPB BC 2.0',
            self::SPPB_BC_2_3 => 'SPPB BC 2.3',
            self::PLP => 'PLP',
            self::SPPB_BC_1_2 => 'SPPB BC 1.2',
            self::SPPF_BCF_2_6 => 'SPPF / BCF 2.6',
            self::NPE => 'NPE',
            self::PKBE => 'PKBE',
            self::PPB => 'PPB',
            self::BCF_1_5_BARANG_TIDAK_DIKUASAI => 'BCF 1.5 / Barang Tidak Dikuasai',
            self::SPPB_EMPTY_CONTAINER => 'SPPB Empty Container',
            self::SPPBE_BATAL_EKSPOR => 'SPPBE Batal Ekspor',
            self::SPPBE_PINDAH_MUAT_BARANG_EKSPOR => 'SPPBE Pindah Muat Barang Ekspor',
            self::SPPB_PIBK => 'SPPB PIBK',
            self::RETURNABLE_PACKAGE => 'Returnable Package',
            self::PENIMBUNAN_DILUAR_KAWASAN_PABEAN => 'Penimbunan diluar Kawasan Pabean',
            self::SPJM_GRAHA_SEGARA => 'SPJM Graha Segara',
            self::BC_1_1_A_SP3B => 'BC 1.1.A / SP3B',
            self::SPPB_PIB_MANUAL => 'SPPB PIB Manual',
            self::PENGELUARAN_BARANG_PAKET_POS => 'Pengeluaran Barang Paket POS',
            self::SKEP_PEMUSNAHAN_BARANG_IMPOR => 'Skep Pemusnahan Barang Impor',
            self::SURAT_PENGELUARAN_BARANG_UNTUK_BARANG_BUKTI_KE_PENGADILAN => 'Surat Pengeluaran Barang Untuk Barang Bukti ke Pengadilan',
            self::PENGELUARAN_BARANG_HIBAH => 'Pengeluaran Barang Hibah',
            self::SURAT_IJIN_PENGELUARAN_BARANG_UNTUK_LELANG => 'Surat Ijin Pengeluaran Barang Untuk Lelang',
            self::PENGELUARAN_BARANG_PERS_RELEASE => 'Pengeluaran Barang Pers Release',
            self::DOKUMEN_BC_1_2 => 'Dokumen BC 1.2',
            self::SPPB_EKS_PERGANTIAN_KONTAINER => 'SPPB eks Pergantian Kontainer',
            self::PENGELUARAN_BARANG_PENEGAHAN_SEBAGIAN => 'Pengeluaran Barang Penegahan (Sebagian)',
            self::PENGELUARAN_BARANG_PENEGAHAN_SELURUHNYA => 'Pengeluaran Barang Penegahan (Seluruhnya)',
            self::EMPTY_CONTAINER_EKSPOR => 'Empty Container (Ekspor)',
            self::DOK_IJIN_EKSPOR_RETURNABLE_PACKAGE => 'Dok. Ijin Ekspor Returnable Package',
            self::SPPB_BC_12_KPPT => 'SPPB BC.12 KPPT',
            self::ATA_CARNET_IMPOR => 'ATA CARNET Impor',
            self::CPD_CARNET_IMPOR => 'CPD CARNET Impor',
            self::ATA_CARNET_EKSPOR => 'ATA CARNET Ekspor',
            self::CPD_CARNET_EKSPOR => 'CPD CARNET Ekspor',
            self::PERSETUJUAN_PART_OF_IMPORTIR_MITA_AEO => 'Persetujuan Part Of Importir MITA/AEO',
            self::PERSETUJUAN_PART_OF_IMPORTIR_NON_MITA_AEO => 'Persetujuan Part Of Importir Non MITA/AEO',
            self::PERSETUJUAN_SHORT_SHIP => 'Persetujuan Short Ship',
            self::DOKUMEN_PEMERIKSAAN_KARANTINA => 'Dokumen Pemeriksaan Karantina',
            self::PENGELUARAN_EMPTY_CONTIANER_EX_STRIPPING => 'Pengeluaran Empty Contianer ex. Stripping',
            self::BC_1_6 => 'BC 1.6',
            self::PERSETUJUAN_KELUAR_BARANG_KIRIMAN => 'Persetujuan Keluar Barang Kiriman',
            self::SPPBMC => 'SPPBMC',
            self::DOKUMEN_PENGELUARAN_BARANG_IMPOR_DENGAN_BC_1_1_OUTWARD => 'Dokumen Pengeluaran Barang Impor dengan BC 1.1 Outward Manifes',
            self::NPP3BET_NOTA_PERSETUJUAN_PENGELUARAN_P3BET => 'NPP3BET(Nota Persetujuan Pengeluaran P3BET)',
            self::PEMASUKAN_SEBAGIAN_BARANG_EKSPOR => 'Pemasukan sebagian Barang Ekspor',
            self::PPFTZ_01_ASAL_LDP_IMPOR => 'PPFTZ-01 Asal LDP (Impor)',
            self::PPFTZ_01_TUJUAN_TLDDP => 'PPFTZ-01 Tujuan TLDDP',
            self::PPFTZ_01_TUJUAN_LDP_EKSPOR => 'PPFTZ-01 Tujuan LDP (Ekspor)',
            self::PPFTZ_02_MASUK => 'PPFTZ-02 Masuk',
            self::PPFTZ_02_KELUAR => 'PPFTZ-02 Keluar',
            self::PPFTZ_03 => 'PPFTZ-03',
            self::IZIN_PENGELUARAN_PENDAHULUAN => 'Izin Pengeluaran Pendahuluan',
            self::SP3K_KONTAINER_KOSONG => 'SP3K (kontainer kosong)',
            self::DOKUMEN_BC_1_1B_SP3B_EKSPOR => 'Dokumen BC. 1.1B/SP3B Ekspor',
            self::DOKUMEN_PEMASUKAN_BARANG_EKSPOR_DENGAN_BC_1_1_INWARD_MANIFES => 'Dokumen Pemasukan Barang Ekspor dengan BC 1.1 Inward Manifes',
            self::DOKUMEN_BC_1_3 => 'Dokumen BC 1.3',
            self::PENYERAHAN_KE_KEMENTRIAN_LEMBAGA_LAIN => 'Penyerahan ke Kementrian/Lembaga lain',
            self::KEK_PEMASUKAN_DARI_LDP => 'KEK- Pemasukan dari LDP',
            self::SURAT_PERSETUJUAN_PENGELUARAN_BARANG_EKSPOR_DAN_TRANSHI => 'Surat Persetujuan Pengeluaran Barang Ekspor dan Transhipment (SPPBE Batal P3BET)',
            self::DOKUMEN_PENGELUARAN_LAINNYA => 'Dokumen Pengeluaran Lainnya....',
            self::KEK_PEMASUKAN_DARI_TLDDP => 'KEK- Pemasukan dari TLDDP',
            self::KEK_PENGELUARAN_KE_KEK_TPB_FTZ => 'KEK- Pengeluaran ke KEK/TPB/FTZ',
            self::KEK_PENGELUARAN_KE_LDP => 'KEK- Pengeluaran ke LDP',
            self::KEK_PENGELUARAN_KE_TLDDP => 'KEK- Pengeluaran ke TLDDP',
            self::DOKUMEN_BC_1_2_FTZ => 'Dokumen BC 1.2 FTZ',
            self::DOKUMEN_NPE_EKSPOR_BARANG_KIRIMAN => 'Dokumen NPE Ekspor Barang Kiriman',
            self::DOKUMEN_BC_1_4 => 'Dokumen BC 1.4',
            self::SPPB_RUSH_HANDLING => 'SPPB Rush Handling',
            self::KEK_PEMASUKAN_MELALUI_BARANG_KIRIMAN => 'KEK- Pemasukan melalui Barang Kiriman',
            self::ATA_CARNET_RE_IMPOR => 'ATA CARNET Re-Impor',
            self::CPD_CARNET_RE_IMPOR => 'CPD CARNET Re-Impor',
            self::ATA_CARNET_RE_EKSPOR => 'ATA CARNET Re-Ekspor',
            self::CPD_CARNET_RE_EKSPOR => 'CPD CARNET Re-Ekspor',
            self::SPPB_CN_FTZ_LDP_FTZ => 'SPPB CN FTZ LDP - FTZ',
            self::SPPB_CN_FTZ_FTZ_TLDPP => 'SPPB CN FTZ FTZ - TLDPP',
            self::SPPB_CN_FTZ_TLDPP_FTZ => 'SPPB CN FTZ TLDPP - FTZ',
        };
    }

    /**
     * Mendapatkan keterangan dokumen.
     */
    public function keterangan(): ?string
    {
        return match ($this) {
            self::SPPB_BC_2_0 => 'Dok. SPPB PIB BC 2.0',
            self::SPPB_BC_2_3 => 'Dok. SPPB BC 2.3',
            self::PLP => 'Dok. PLP/OB (A11)',
            self::SPPB_BC_1_2 => 'Dok. Angkut Lanjut',
            self::SPPF_BCF_2_6 => 'Dok. Pemeriksaan dilokasi Importir',
            self::NPE => 'Dok. NPE',
            self::PKBE => null,
            self::PPB => 'Dok. Periksa Fisik',
            self::BCF_1_5_BARANG_TIDAK_DIKUASAI => 'Container Timbun Lewat Waktu',
            self::SPPB_EMPTY_CONTAINER => 'Dok. Pengeluaran Impor Container Kosong',
            self::SPPBE_BATAL_EKSPOR => 'Surat Persetujuan Pengeluaran Barang Ekspor (Batal Ekspor)',
            self::SPPBE_PINDAH_MUAT_BARANG_EKSPOR => 'Surat Persetujuan Pengeluaran Barang Ekspor (Pindah Muat)',
            self::SPPB_PIBK => null,
            self::RETURNABLE_PACKAGE => 'Ijin Impor Returnable Package',
            self::PENIMBUNAN_DILUAR_KAWASAN_PABEAN => 'Ijin Timbun diluar Kawasan Pabean',
            self::SPJM_GRAHA_SEGARA => 'PJM ke Graha Segara',
            self::BC_1_1_A_SP3B => 'Pengeluaran Tujuan KPPT',
            self::SPPB_PIB_MANUAL => 'SPPB PIB Manual',
            self::PENGELUARAN_BARANG_PAKET_POS => 'Pengeluaran Barang Paket POS',
            self::SKEP_PEMUSNAHAN_BARANG_IMPOR => 'Skep Pemusnahan Barang Impor Oleh Kepala Kantor',
            self::SURAT_PENGELUARAN_BARANG_UNTUK_BARANG_BUKTI_KE_PENGADILAN => 'Surat Penyerahan Barang Bukti untuk Proses Pengadilan',
            self::PENGELUARAN_BARANG_HIBAH => 'Surat Persetujuan Hibah dari Menteri Keuangan',
            self::SURAT_IJIN_PENGELUARAN_BARANG_UNTUK_LELANG => 'Pengeluaran Barang Milik Negara dan barang Tidak Dikuasai yang di Lelang',
            self::PENGELUARAN_BARANG_PERS_RELEASE => 'Pengeluaran Barang Pers Release',
            self::DOKUMEN_BC_1_2 => 'Re-Ekspor (BC 1.2) Belum aju PIB',
            self::SPPB_EKS_PERGANTIAN_KONTAINER => null,
            self::PENGELUARAN_BARANG_PENEGAHAN_SEBAGIAN => 'SPPB',
            self::PENGELUARAN_BARANG_PENEGAHAN_SELURUHNYA => 'Surat Perintah Penegahan',
            self::EMPTY_CONTAINER_EKSPOR => 'Dok. Ijin Pemasukan Empty Container (Ekspor)',
            self::DOK_IJIN_EKSPOR_RETURNABLE_PACKAGE => 'Dok. Ijin Ekspor Returnable Package',
            self::SPPB_BC_12_KPPT => 'SPPB Angkut Lanjut Tujuan KPPT',
            self::ATA_CARNET_IMPOR => 'Dokumen pengeluaran khusus untuk impor sementara bukan kendaraan bermotor',
            self::CPD_CARNET_IMPOR => 'Dokumen pengeluaran untuk impor sementara khusus kendaraan bermotor',
            self::ATA_CARNET_EKSPOR => 'Dokumen Pemasukan Khusus ATA Carnet',
            self::CPD_CARNET_EKSPOR => 'Dokumen Pemasukan khusus CPD Carnet Ekspor',
            self::PERSETUJUAN_PART_OF_IMPORTIR_MITA_AEO => 'SPPB + Surat Persetujuan Kabid PPCII',
            self::PERSETUJUAN_PART_OF_IMPORTIR_NON_MITA_AEO => 'SPPB + Surat Persetujuan Kabid PPCII + PLP Striping',
            self::PERSETUJUAN_SHORT_SHIP => 'SPPB + Surat Persetujuan Kabid PPCII',
            self::DOKUMEN_PEMERIKSAAN_KARANTINA => 'Dokumen Pemeriksaan Karantina',
            self::PENGELUARAN_EMPTY_CONTIANER_EX_STRIPPING => 'Tidak ada dok ijin Pengeluaran empty container ex. stripping',
            self::BC_1_6 => 'SPPB PLB - BC 1.6',
            self::PERSETUJUAN_KELUAR_BARANG_KIRIMAN => null,
            self::SPPBMC => null,
            self::DOKUMEN_PENGELUARAN_BARANG_IMPOR_DENGAN_BC_1_1_OUTWARD => null,
            self::NPP3BET_NOTA_PERSETUJUAN_PENGELUARAN_P3BET => null,
            self::PEMASUKAN_SEBAGIAN_BARANG_EKSPOR => 'Sesuai Peraturan nomor P- 01/KPU.1/2019',
            self::PPFTZ_01_ASAL_LDP_IMPOR => null,
            self::PPFTZ_01_TUJUAN_TLDDP => null,
            self::PPFTZ_01_TUJUAN_LDP_EKSPOR => null,
            self::PPFTZ_02_MASUK => null,
            self::PPFTZ_02_KELUAR => null,
            self::PPFTZ_03 => null,
            self::IZIN_PENGELUARAN_PENDAHULUAN => null,
            self::SP3K_KONTAINER_KOSONG => null,
            self::DOKUMEN_BC_1_1B_SP3B_EKSPOR => null,
            self::DOKUMEN_PEMASUKAN_BARANG_EKSPOR_DENGAN_BC_1_1_INWARD_MANIFES => null,
            self::DOKUMEN_BC_1_3 => 'Pemberitahuan Pengangkutan Barang Asal Daerah Pabean dari Satu Tempat ke Tempat Lain Melalui Luar Daerah Pabean dan/atau Tempat Penimbunan Berikat',
            self::PENYERAHAN_KE_KEMENTRIAN_LEMBAGA_LAIN => 'Penyerahan ke Kementrian/Lembaga lain',
            self::KEK_PEMASUKAN_DARI_LDP => 'KEK- Pemasukan dari LDP',
            self::SURAT_PERSETUJUAN_PENGELUARAN_BARANG_EKSPOR_DAN_TRANSHI => 'Surat Persetujuan PengeluaraNg Barang Ekspor dan Transhipment (Batal Ekspr P3BET)',
            self::DOKUMEN_PENGELUARAN_LAINNYA => null,
            self::KEK_PEMASUKAN_DARI_TLDDP => 'KEK- Pemasukan dari TLDDP',
            self::KEK_PENGELUARAN_KE_KEK_TPB_FTZ => 'KEK- Pengeluaran ke KEK/TPB/FTZ',
            self::KEK_PENGELUARAN_KE_LDP => 'KEK- Pengeluaran ke LDP',
            self::KEK_PENGELUARAN_KE_TLDDP => 'KEK- Pengeluaran ke TLDDP',
            self::DOKUMEN_BC_1_2_FTZ => 'Dokumen BC 1.2 FTZ',
            self::DOKUMEN_NPE_EKSPOR_BARANG_KIRIMAN => null,
            self::DOKUMEN_BC_1_4 => 'Dokumen BC 1.4',
            self::SPPB_RUSH_HANDLING => 'SPPB Rush Handling',
            self::KEK_PEMASUKAN_MELALUI_BARANG_KIRIMAN => 'KEK- Pemasukan melalui Barang Kiriman',
            self::ATA_CARNET_RE_IMPOR => 'Dokumen pengeluaran khusus untuk impor sementara bukan kendaraan bermotor',
            self::CPD_CARNET_RE_IMPOR => 'Dokumen pengeluaran untuk impor sementara khusus kendaraan bermotor',
            self::ATA_CARNET_RE_EKSPOR => 'Dokumen Pemasukan Khusus ATA Carnet',
            self::CPD_CARNET_RE_EKSPOR => 'Dokumen Pemasukan khusus CPD Carnet Ekspor',
            self::SPPB_CN_FTZ_LDP_FTZ => 'Barkir FTZ batam',
            self::SPPB_CN_FTZ_FTZ_TLDPP => 'Barkir FTZ batam',
            self::SPPB_CN_FTZ_TLDPP_FTZ => 'Barkir FTZ batam',
        };
    }

    /**
     * Mendapatkan tipe arus barang ('I' = Impor, 'E' = Ekspor).
     */
    public function tipe(): ?string
    {
        return match ($this) {
            self::SPPB_BC_2_0 => 'I',
            self::SPPB_BC_2_3 => 'I',
            self::PLP => 'I',
            self::SPPB_BC_1_2 => 'I',
            self::SPPF_BCF_2_6 => 'I',
            self::NPE => 'E',
            self::PKBE => 'E',
            self::PPB => 'E',
            self::BCF_1_5_BARANG_TIDAK_DIKUASAI => 'I',
            self::SPPB_EMPTY_CONTAINER => 'I',
            self::SPPBE_BATAL_EKSPOR => 'E',
            self::SPPBE_PINDAH_MUAT_BARANG_EKSPOR => 'E',
            self::SPPB_PIBK => 'I',
            self::RETURNABLE_PACKAGE => 'I',
            self::PENIMBUNAN_DILUAR_KAWASAN_PABEAN => 'I',
            self::SPJM_GRAHA_SEGARA => 'I',
            self::BC_1_1_A_SP3B => 'I',
            self::SPPB_PIB_MANUAL => 'I',
            self::PENGELUARAN_BARANG_PAKET_POS => 'I',
            self::SKEP_PEMUSNAHAN_BARANG_IMPOR => 'I',
            self::SURAT_PENGELUARAN_BARANG_UNTUK_BARANG_BUKTI_KE_PENGADILAN => 'I',
            self::PENGELUARAN_BARANG_HIBAH => 'I',
            self::SURAT_IJIN_PENGELUARAN_BARANG_UNTUK_LELANG => 'I',
            self::PENGELUARAN_BARANG_PERS_RELEASE => 'I',
            self::DOKUMEN_BC_1_2 => 'I',
            self::SPPB_EKS_PERGANTIAN_KONTAINER => 'I',
            self::PENGELUARAN_BARANG_PENEGAHAN_SEBAGIAN => 'I',
            self::PENGELUARAN_BARANG_PENEGAHAN_SELURUHNYA => 'I',
            self::EMPTY_CONTAINER_EKSPOR => 'E',
            self::DOK_IJIN_EKSPOR_RETURNABLE_PACKAGE => 'E',
            self::SPPB_BC_12_KPPT => 'E',
            self::ATA_CARNET_IMPOR => 'I',
            self::CPD_CARNET_IMPOR => 'I',
            self::ATA_CARNET_EKSPOR => 'E',
            self::CPD_CARNET_EKSPOR => 'E',
            self::PERSETUJUAN_PART_OF_IMPORTIR_MITA_AEO => 'I',
            self::PERSETUJUAN_PART_OF_IMPORTIR_NON_MITA_AEO => 'I',
            self::PERSETUJUAN_SHORT_SHIP => 'I',
            self::DOKUMEN_PEMERIKSAAN_KARANTINA => 'I',
            self::PENGELUARAN_EMPTY_CONTIANER_EX_STRIPPING => 'I',
            self::BC_1_6 => 'I',
            self::PERSETUJUAN_KELUAR_BARANG_KIRIMAN => 'I',
            self::SPPBMC => 'I',
            self::DOKUMEN_PENGELUARAN_BARANG_IMPOR_DENGAN_BC_1_1_OUTWARD => 'I',
            self::NPP3BET_NOTA_PERSETUJUAN_PENGELUARAN_P3BET => 'E',
            self::PEMASUKAN_SEBAGIAN_BARANG_EKSPOR => 'E',
            self::PPFTZ_01_ASAL_LDP_IMPOR => 'I',
            self::PPFTZ_01_TUJUAN_TLDDP => 'E',
            self::PPFTZ_01_TUJUAN_LDP_EKSPOR => 'E',
            self::PPFTZ_02_MASUK => 'I',
            self::PPFTZ_02_KELUAR => 'E',
            self::PPFTZ_03 => 'I',
            self::IZIN_PENGELUARAN_PENDAHULUAN => 'I',
            self::SP3K_KONTAINER_KOSONG => 'I',
            self::DOKUMEN_BC_1_1B_SP3B_EKSPOR => 'E',
            self::DOKUMEN_PEMASUKAN_BARANG_EKSPOR_DENGAN_BC_1_1_INWARD_MANIFES => 'E',
            self::DOKUMEN_BC_1_3 => 'E',
            self::PENYERAHAN_KE_KEMENTRIAN_LEMBAGA_LAIN => 'I',
            self::KEK_PEMASUKAN_DARI_LDP => 'I',
            self::SURAT_PERSETUJUAN_PENGELUARAN_BARANG_EKSPOR_DAN_TRANSHI => 'E',
            self::DOKUMEN_PENGELUARAN_LAINNYA => 'E/I',
            self::KEK_PEMASUKAN_DARI_TLDDP => 'I',
            self::KEK_PENGELUARAN_KE_KEK_TPB_FTZ => 'I',
            self::KEK_PENGELUARAN_KE_LDP => 'I',
            self::KEK_PENGELUARAN_KE_TLDDP => 'I',
            self::DOKUMEN_BC_1_2_FTZ => 'I',
            self::DOKUMEN_NPE_EKSPOR_BARANG_KIRIMAN => 'E',
            self::DOKUMEN_BC_1_4 => 'I',
            self::SPPB_RUSH_HANDLING => 'I',
            self::KEK_PEMASUKAN_MELALUI_BARANG_KIRIMAN => 'I',
            self::ATA_CARNET_RE_IMPOR => 'I',
            self::CPD_CARNET_RE_IMPOR => 'I',
            self::ATA_CARNET_RE_EKSPOR => 'E',
            self::CPD_CARNET_RE_EKSPOR => 'E',
            self::SPPB_CN_FTZ_LDP_FTZ => 'I',
            self::SPPB_CN_FTZ_FTZ_TLDPP => 'I',
            self::SPPB_CN_FTZ_TLDPP_FTZ => 'I',
        };
    }

    /**
     * Mendapatkan status segel ('Y' = Ya, 'N' = Tidak).
     */
    public function segel(): ?string
    {
        return match ($this) {
            self::SPPB_BC_2_0 => 'N',
            self::SPPB_BC_2_3 => 'Y',
            self::PLP => 'N',
            self::SPPB_BC_1_2 => 'Y',
            self::SPPF_BCF_2_6 => 'N',
            self::NPE => 'N',
            self::PKBE => 'N',
            self::PPB => 'N',
            self::BCF_1_5_BARANG_TIDAK_DIKUASAI => 'Y',
            self::SPPB_EMPTY_CONTAINER => 'N',
            self::SPPBE_BATAL_EKSPOR => 'N',
            self::SPPBE_PINDAH_MUAT_BARANG_EKSPOR => 'N',
            self::SPPB_PIBK => 'N',
            self::RETURNABLE_PACKAGE => 'N',
            self::PENIMBUNAN_DILUAR_KAWASAN_PABEAN => 'Y',
            self::SPJM_GRAHA_SEGARA => 'Y',
            self::BC_1_1_A_SP3B => 'N',
            self::SPPB_PIB_MANUAL => 'N',
            self::PENGELUARAN_BARANG_PAKET_POS => 'N',
            self::SKEP_PEMUSNAHAN_BARANG_IMPOR => 'Y',
            self::SURAT_PENGELUARAN_BARANG_UNTUK_BARANG_BUKTI_KE_PENGADILAN => 'Y',
            self::PENGELUARAN_BARANG_HIBAH => 'Y',
            self::SURAT_IJIN_PENGELUARAN_BARANG_UNTUK_LELANG => 'Y',
            self::PENGELUARAN_BARANG_PERS_RELEASE => 'Y',
            self::DOKUMEN_BC_1_2 => 'Y',
            self::SPPB_EKS_PERGANTIAN_KONTAINER => 'N',
            self::PENGELUARAN_BARANG_PENEGAHAN_SEBAGIAN => 'Y',
            self::PENGELUARAN_BARANG_PENEGAHAN_SELURUHNYA => 'Y',
            self::EMPTY_CONTAINER_EKSPOR => 'N',
            self::DOK_IJIN_EKSPOR_RETURNABLE_PACKAGE => 'N',
            self::SPPB_BC_12_KPPT => 'N',
            self::ATA_CARNET_IMPOR => 'N',
            self::CPD_CARNET_IMPOR => 'N',
            self::ATA_CARNET_EKSPOR => 'N',
            self::CPD_CARNET_EKSPOR => 'N',
            self::PERSETUJUAN_PART_OF_IMPORTIR_MITA_AEO => 'N',
            self::PERSETUJUAN_PART_OF_IMPORTIR_NON_MITA_AEO => 'N',
            self::PERSETUJUAN_SHORT_SHIP => 'N',
            self::DOKUMEN_PEMERIKSAAN_KARANTINA => 'N',
            self::PENGELUARAN_EMPTY_CONTIANER_EX_STRIPPING => 'N',
            self::BC_1_6 => 'Y',
            self::PERSETUJUAN_KELUAR_BARANG_KIRIMAN => 'N',
            self::SPPBMC => 'N',
            self::DOKUMEN_PENGELUARAN_BARANG_IMPOR_DENGAN_BC_1_1_OUTWARD => 'Y',
            self::NPP3BET_NOTA_PERSETUJUAN_PENGELUARAN_P3BET => 'N',
            self::PEMASUKAN_SEBAGIAN_BARANG_EKSPOR => 'N',
            self::PPFTZ_01_ASAL_LDP_IMPOR => 'N',
            self::PPFTZ_01_TUJUAN_TLDDP => 'N',
            self::PPFTZ_01_TUJUAN_LDP_EKSPOR => 'N',
            self::PPFTZ_02_MASUK => 'N',
            self::PPFTZ_02_KELUAR => 'N',
            self::PPFTZ_03 => 'N',
            self::IZIN_PENGELUARAN_PENDAHULUAN => 'N',
            self::SP3K_KONTAINER_KOSONG => 'N',
            self::DOKUMEN_BC_1_1B_SP3B_EKSPOR => 'N',
            self::DOKUMEN_PEMASUKAN_BARANG_EKSPOR_DENGAN_BC_1_1_INWARD_MANIFES => 'Y',
            self::DOKUMEN_BC_1_3 => 'N',
            self::PENYERAHAN_KE_KEMENTRIAN_LEMBAGA_LAIN => 'N',
            self::KEK_PEMASUKAN_DARI_LDP => 'Y',
            self::SURAT_PERSETUJUAN_PENGELUARAN_BARANG_EKSPOR_DAN_TRANSHI => 'N',
            self::DOKUMEN_PENGELUARAN_LAINNYA => 'N',
            self::KEK_PEMASUKAN_DARI_TLDDP => 'N',
            self::KEK_PENGELUARAN_KE_KEK_TPB_FTZ => 'N',
            self::KEK_PENGELUARAN_KE_LDP => 'N',
            self::KEK_PENGELUARAN_KE_TLDDP => 'N',
            self::DOKUMEN_BC_1_2_FTZ => 'N',
            self::DOKUMEN_NPE_EKSPOR_BARANG_KIRIMAN => 'N',
            self::DOKUMEN_BC_1_4 => 'N',
            self::SPPB_RUSH_HANDLING => 'N',
            self::KEK_PEMASUKAN_MELALUI_BARANG_KIRIMAN => 'N',
            self::ATA_CARNET_RE_IMPOR => 'N',
            self::CPD_CARNET_RE_IMPOR => 'N',
            self::ATA_CARNET_RE_EKSPOR => 'N',
            self::CPD_CARNET_RE_EKSPOR => 'N',
            self::SPPB_CN_FTZ_LDP_FTZ => 'N',
            self::SPPB_CN_FTZ_FTZ_TLDPP => 'N',
            self::SPPB_CN_FTZ_TLDPP_FTZ => 'N',
        };
    }
}
