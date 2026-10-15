<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum TujuanPengeluaran
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum TujuanPengeluaran: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case DIJUAL = '1';
    case DIPERBAIKI = '2';
    case DIREKONDISI = '3';
    case DIUJI = '4';
    case DIKALIBRASI = '5';
    case PENGERJAAN_PROYEK = '6';
    case KEPERLUAAN_PERAGAAN_ATAU_DEMONSTRASI = '7';
    case DIKEMBALIKAN = '8';
    case LAINNYA = '9';
    case DIKEMBALIKAN_10 = '10';
    case LAINNYA_11 = '11';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::DIJUAL => 'DIJUAL',
            self::DIPERBAIKI => 'DIPERBAIKI',
            self::DIREKONDISI => 'DIREKONDISI',
            self::DIUJI => 'DIUJI',
            self::DIKALIBRASI => 'DIKALIBRASI',
            self::PENGERJAAN_PROYEK => 'PENGERJAAN PROYEK',
            self::KEPERLUAAN_PERAGAAN_ATAU_DEMONSTRASI => 'KEPERLUAAN PERAGAAN ATAU DEMONSTRASI',
            self::DIKEMBALIKAN => 'DIKEMBALIKAN',
            self::LAINNYA => 'LAINNYA',
            self::DIKEMBALIKAN_10 => 'DIKEMBALIKAN',
            self::LAINNYA_11 => 'LAINNYA',
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
            self::DIJUAL => ['512' => 'DIJUAL', '513' => 'DIJUAL', '522' => 'DITIMBUN'][$kodeDokumen] ?? $this->description(),
            self::DIPERBAIKI => ['512' => 'DIPERBAIKI', '513' => 'DIPERGUNAKAN', '522' => 'DIJUAL'][$kodeDokumen] ?? $this->description(),
            self::DIREKONDISI => ['512' => 'DIREKONDISI', '513' => 'DIOLAH', '522' => 'DIPROSES'][$kodeDokumen] ?? $this->description(),
            self::DIUJI => ['512' => 'DIUJI', '513' => 'DIPERBAIKI', '522' => 'DISUBKONTRAKAN'][$kodeDokumen] ?? $this->description(),
            self::DIKALIBRASI => ['512' => 'DIKALIBRASI', '513' => 'DIREKONDISI', '522' => 'DIPINJAMKAN'][$kodeDokumen] ?? $this->description(),
            self::PENGERJAAN_PROYEK => ['512' => 'PENGERJAAN PROYEK', '513' => 'DIUJI', '522' => 'DIPERBAIKI'][$kodeDokumen] ?? $this->description(),
            self::KEPERLUAAN_PERAGAAN_ATAU_DEMONSTRASI => ['512' => 'KEPERLUAAN PERAGAAN ATAU DEMONSTRASI', '513' => 'DIKALIBRASI', '522' => 'DIPAMERKAN'][$kodeDokumen] ?? $this->description(),
            self::DIKEMBALIKAN => ['512' => 'DIKEMBALIKAN', '513' => 'PENGERJAAN PROYEK', '522' => 'PENGEMBALIAN SUBKONTRAK'][$kodeDokumen] ?? $this->description(),
            self::LAINNYA => ['512' => 'LAINNYA', '513' => 'KEPERLUAAN PERAGAAN ATAU DEMONSTRASI', '522' => 'PENGEMBALIAN PINJAMAN'][$kodeDokumen] ?? $this->description(),
            self::DIKEMBALIKAN_10 => ['513' => 'DIKEMBALIKAN', '522' => 'PENGEMBALIAN PERBAIKAN'][$kodeDokumen] ?? $this->description(),
            self::LAINNYA_11 => ['513' => 'LAINNYA', '522' => 'LAINNYA'][$kodeDokumen] ?? $this->description(),
        };
    }
}
