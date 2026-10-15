<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum JenisPibProsedur
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum JenisPibProsedur: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case BIASA = '1';
    case BERKALA = '2';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::BIASA => 'BIASA',
            self::BERKALA => 'BERKALA',
        };
    }
}
