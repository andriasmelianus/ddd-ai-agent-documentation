<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum TujuanPengiriman
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum TujuanPengiriman: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case PENYERAHAN_BKP = '1';
    case PENYERAHAN_JKP = '2';
    case RETUR = '3';
    case NON_PENYERAHAN = '4';
    case LAINNYA = '5';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::PENYERAHAN_BKP => 'PENYERAHAN BKP',
            self::PENYERAHAN_JKP => 'PENYERAHAN JKP',
            self::RETUR => 'RETUR',
            self::NON_PENYERAHAN => 'NON PENYERAHAN',
            self::LAINNYA => 'LAINNYA',
        };
    }

    /**
     * Mendapatkan deskripsi spesifik sesuai kode dokumen yang digunakan.
     */
    public function descriptionForDokumen(?string $kodeDokumen = null): string
    {
        if ($kodeDokumen === null) {
            return $this->description();
        }

        return match ($this) {
            self::PENYERAHAN_BKP => ['25' => 'PENYERAHAN BKP', '261' => 'DIPERBAIKI', '27' => 'PENYERAHAN BKP', '40' => 'PENYERAHAN BKP', '41' => 'PENYERAHAN BKP', '512' => 'PENYERAHAN BKP', '513' => 'PENYERAHAN BKP', '522' => 'PENYERAHAN BKP'][$kodeDokumen] ?? $this->description(),
            self::PENYERAHAN_JKP => ['25' => 'PENYERAHAN JKP', '261' => 'DISUBKONTRAKKAN', '27' => 'PENYERAHAN JKP', '40' => 'PENYERAHAN JKP', '41' => 'PENYERAHAN JKP'][$kodeDokumen] ?? $this->description(),
            self::RETUR => ['25' => 'RETUR', '261' => 'DIPINJAMKAN', '27' => 'RETUR', '40' => 'RETUR', '41' => 'RETUR'][$kodeDokumen] ?? $this->description(),
            self::NON_PENYERAHAN => ['25' => 'NON PENYERAHAN', '261' => 'BARANG RETURN', '27' => 'NON PENYERAHAN', '40' => 'NON PENYERAHAN', '41' => 'NON PENYERAHAN', '512' => 'NON PENYERAHAN', '513' => 'NON PENYERAHAN', '522' => 'NON PENYERAHAN'][$kodeDokumen] ?? $this->description(),
            self::LAINNYA => ['25' => 'LAINNYA', '261' => 'LAINNYA', '27' => 'LAINNYA', '40' => 'LAINNYA', '41' => 'LAINNYA'][$kodeDokumen] ?? $this->description(),
        };
    }
}
