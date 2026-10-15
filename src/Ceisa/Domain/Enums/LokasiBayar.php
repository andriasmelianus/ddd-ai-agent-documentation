<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum LokasiBayar
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum LokasiBayar: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case BANK = '1';
    case POS = '2';
    case KANTOR_PABEAN = '3';
    case NTPN = '4';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::BANK => 'BANK',
            self::POS => 'POS',
            self::KANTOR_PABEAN => 'KANTOR PABEAN',
            self::NTPN => 'NTPN',
        };
    }
}
