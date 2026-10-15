<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum JenisEkspor
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum JenisEkspor: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case BIASA = '1';
    case BERKALA = '2';
    case FASILITAS = '3';
    case RE_IMPOR = '4';
    case RE_EKSPOR = '5';
    case EX_IMPOR_SEMENTARA = '6';
    case EKSPOR_GABUNGAN = '7';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::BIASA => 'BIASA',
            self::BERKALA => 'BERKALA',
            self::FASILITAS => 'FASILITAS',
            self::RE_IMPOR => 'RE-IMPOR',
            self::RE_EKSPOR => 'RE-EKSPOR',
            self::EX_IMPOR_SEMENTARA => 'EX IMPOR SEMENTARA',
            self::EKSPOR_GABUNGAN => 'EKSPOR GABUNGAN',
        };
    }
}
