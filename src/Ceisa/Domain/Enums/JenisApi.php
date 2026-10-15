<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum JenisApi
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum JenisApi: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case ANGKA_PENGENAL_IMPORTIR_UMUM_APIU = '1';
    case ANGKA_PENGENAL_IMPORTIR_PERSEROAN_APIP = '2';
    case ANGKA_PENGENAL_IMPORTIR_TERBATAS_APIT = '4';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::ANGKA_PENGENAL_IMPORTIR_UMUM_APIU => 'ANGKA PENGENAL IMPORTIR UMUM (APIU)',
            self::ANGKA_PENGENAL_IMPORTIR_PERSEROAN_APIP => 'ANGKA PENGENAL IMPORTIR PERSEROAN (APIP)',
            self::ANGKA_PENGENAL_IMPORTIR_TERBATAS_APIT => 'ANGKA PENGENAL IMPORTIR TERBATAS (APIT)',
        };
    }
}
