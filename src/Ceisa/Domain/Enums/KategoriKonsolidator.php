<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum KategoriKonsolidator
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum KategoriKonsolidator: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case KONSOLIDATOR = '1';
    case EKSPORTIR_YANG_MELAKUKAN_SENDIRI_KONSOLIDASI_BARANG_EKSPOR = '2';
    case EKSPORTIR_DALAM_SATU_KELOMPOK_PERUSAHAAN = '3';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::KONSOLIDATOR => 'KONSOLIDATOR',
            self::EKSPORTIR_YANG_MELAKUKAN_SENDIRI_KONSOLIDASI_BARANG_EKSPOR => 'EKSPORTIR YANG MELAKUKAN SENDIRI KONSOLIDASI BARANG EKSPOR',
            self::EKSPORTIR_DALAM_SATU_KELOMPOK_PERUSAHAAN => 'EKSPORTIR DALAM SATU KELOMPOK PERUSAHAAN',
        };
    }
}
