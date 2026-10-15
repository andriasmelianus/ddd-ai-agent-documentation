<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum JenisPungutan
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum JenisPungutan: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case BEA_KELUAR = 'BK';
    case BEA_MASUK = 'BM';
    case BEA_MASUK_ANTI_DUMPING = 'BMAD';
    case BEA_MASUK_IMBALAN = 'BMI';
    case BEA_MASUK_KITE = 'BMKITE';
    case BEA_MASUK_PEMBALASAN = 'BMP';
    case BEA_MASUK_TINDAKAN_PENGAMANAN = 'BMTP';
    case CUKAI_ETIL_ALKOHOL = 'CEA';
    case CUKAI_MINUMAN_MENGANDUNG_ETIL_ALKOHOL = 'CMEA';
    case CUKAI_TEMBAKAU = 'CTEM';
    case DENDA_ADMINISTRASI_PABEAN = 'DENDA';
    case DANA_SAWIT = 'DS';
    case PENERIMAAN_NEGARA_BUKAN_PAJAK = 'PNBP';
    case PPH_IMPOR = 'PPH';
    case PPH_EKSPOR = 'PPHEKSPOR';
    case PPN_IMPOR = 'PPN';
    case PPNBM_IMPOR = 'PPNBM';
    case PPN_DALAM_NEGERI_HASIL_TEMBAKAU = 'PPNLOKAL';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::BEA_KELUAR => 'Bea Keluar',
            self::BEA_MASUK => 'Bea Masuk',
            self::BEA_MASUK_ANTI_DUMPING => 'Bea Masuk Anti Dumping',
            self::BEA_MASUK_IMBALAN => 'Bea Masuk Imbalan',
            self::BEA_MASUK_KITE => 'Bea Masuk KITE',
            self::BEA_MASUK_PEMBALASAN => 'Bea Masuk Pembalasan',
            self::BEA_MASUK_TINDAKAN_PENGAMANAN => 'Bea Masuk Tindakan Pengamanan',
            self::CUKAI_ETIL_ALKOHOL => 'Cukai Etil Alkohol',
            self::CUKAI_MINUMAN_MENGANDUNG_ETIL_ALKOHOL => 'Cukai Minuman Mengandung Etil Alkohol',
            self::CUKAI_TEMBAKAU => 'Cukai Tembakau',
            self::DENDA_ADMINISTRASI_PABEAN => 'Denda Administrasi Pabean',
            self::DANA_SAWIT => 'Dana Sawit',
            self::PENERIMAAN_NEGARA_BUKAN_PAJAK => 'Penerimaan Negara Bukan Pajak',
            self::PPH_IMPOR => 'PPh Impor',
            self::PPH_EKSPOR => 'PPh Ekspor',
            self::PPN_IMPOR => 'PPN Impor',
            self::PPNBM_IMPOR => 'PPnBM Impor',
            self::PPN_DALAM_NEGERI_HASIL_TEMBAKAU => 'PPN Dalam Negeri / Hasil Tembakau',
        };
    }
}
