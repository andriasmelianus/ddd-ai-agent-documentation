<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum TpsKodeDokumenCoarriCodeco
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum TpsKodeDokumenCoarriCodeco: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case COARI_DISCHARGE = '1';
    case COARI_LOADING = '2';
    case CODECO_IMPOR = '3';
    case CODECO_EKSPOR = '4';
    case GATE_IN_IMPOR_LINI_2 = '5';
    case GATE_OUT_IMPOR_LINI_2 = '6';
    case GATE_IN_EKSPOR_LINI_2 = '7';
    case GATE_OUT_EKSPOR_LINI_2 = '8';
    case CODECO_OUT_EKSPOR_LINI_2 = '9';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::COARI_DISCHARGE => 'Coari Discharge',
            self::COARI_LOADING => 'Coari Loading',
            self::CODECO_IMPOR => 'Codeco Impor',
            self::CODECO_EKSPOR => 'Codeco Ekspor',
            self::GATE_IN_IMPOR_LINI_2 => 'Gate In Impor (Lini 2)',
            self::GATE_OUT_IMPOR_LINI_2 => 'Gate Out Impor (Lini 2)',
            self::GATE_IN_EKSPOR_LINI_2 => 'Gate In Ekspor (Lini 2)',
            self::GATE_OUT_EKSPOR_LINI_2 => 'Gate Out Ekspor (Lini 2)',
            self::CODECO_OUT_EKSPOR_LINI_2 => 'Codeco Out Ekspor (Lini 2)',
        };
    }

    /**
     * Mendapatkan keterangan transaksi.
     */
    public function keterangan(): ?string
    {
        return match ($this) {
            self::COARI_DISCHARGE => 'Daftar Timbun Impor',
            self::COARI_LOADING => 'Daftar Muat Ekspor',
            self::CODECO_IMPOR => 'Gate Out Impor Lini 1',
            self::CODECO_EKSPOR => 'Gate In Ekspor Lini 1',
            self::GATE_IN_IMPOR_LINI_2 => 'Gate In Impor Lini 2',
            self::GATE_OUT_IMPOR_LINI_2 => 'Gate Out Impor Lini 2',
            self::GATE_IN_EKSPOR_LINI_2 => 'Gate In Ekspor Lini 2',
            self::GATE_OUT_EKSPOR_LINI_2 => 'Gate Out Ekspor Lini 2',
            self::CODECO_OUT_EKSPOR_LINI_2 => 'Gate Out Ekspor Lini 1 (Batal Ekspor)',
        };
    }
}
