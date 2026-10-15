<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum KodeJenisImpor
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum KodeJenisImpor: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case UNTUK_DIPAKAI = '1';
    case SEMENTARA = '2';
    case REIMPOR = '3';
    case TPB = '4';
    case PELAYANAN_SEGERA = '5';
    case VOORUITSLAG = '6';
    case GABUNGAN = '7';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::UNTUK_DIPAKAI => 'UNTUK DIPAKAI',
            self::SEMENTARA => 'SEMENTARA',
            self::REIMPOR => 'REIMPOR',
            self::TPB => 'TPB',
            self::PELAYANAN_SEGERA => 'PELAYANAN SEGERA',
            self::VOORUITSLAG => 'VOORUITSLAG',
            self::GABUNGAN => 'GABUNGAN',
        };
    }
}
