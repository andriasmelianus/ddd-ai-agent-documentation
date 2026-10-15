<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum TipeKontainer
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum TipeKontainer: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case GENERAL_DRY_CARGO = '1';
    case TUNNE_TYPE = '2';
    case OPEN_TOP_STEEL = '3';
    case FLAT_RACK = '4';
    case REEFER_REFREGETE = '5';
    case BARGE_CONTAINER = '6';
    case BULK_CONTAINER = '7';
    case ISOTANK = '8';
    case LAIN_LAIN = '99';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::GENERAL_DRY_CARGO => 'General / Dry Cargo',
            self::TUNNE_TYPE => 'Tunne Type',
            self::OPEN_TOP_STEEL => 'Open Top Steel',
            self::FLAT_RACK => 'Flat Rack',
            self::REEFER_REFREGETE => 'Reefer/Refregete',
            self::BARGE_CONTAINER => 'Barge Container',
            self::BULK_CONTAINER => 'Bulk Container',
            self::ISOTANK => 'Isotank',
            self::LAIN_LAIN => 'Lain-lain',
        };
    }
}
