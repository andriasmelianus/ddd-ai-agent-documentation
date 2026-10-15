<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum JenisTandaPengaman
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum JenisTandaPengaman: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case KABEL = '1';
    case KUNCI_LOGAM = '2';
    case KUNCI_PLASTIK = '3';
    case LAINNYA = '4';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::KABEL => 'Kabel',
            self::KUNCI_LOGAM => 'Kunci Logam',
            self::KUNCI_PLASTIK => 'Kunci Plastik',
            self::LAINNYA => 'Lainnya',
        };
    }
}
