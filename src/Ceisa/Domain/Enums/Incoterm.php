<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum Incoterm
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum Incoterm: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case COST_AND_FREIGHT = 'CFR';
    case COST_INSURANCE_AND_FREIGHT = 'CIF';
    case CARRIAGE_AND_INSURANCE_PAID_TO = 'CIP';
    case CARRIAGE_PAID_TO = 'CPT';
    case DELIVERED_AT_FRONTIER = 'DAF';
    case DELIVERED_AT_PLACE = 'DAP';
    case DELIVERED_AT_TERMINAL = 'DAT';
    case DELIVERED_DUTY_PAID = 'DDP';
    case DELIVERED_DUTY_UNPAID = 'DDU';
    case DELIVERED_EX_QUAY = 'DEQ';
    case DELIVERED_EX_SHIP = 'DES';
    case EX_WORKS = 'EXW';
    case FREE_ALONGSIDE_SHIP = 'FAS';
    case FREE_CARRIER = 'FCA';
    case FREE_ON_BOARD = 'FOB';
    case LAINNYA = 'LAN';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::COST_AND_FREIGHT => 'Cost and Freight',
            self::COST_INSURANCE_AND_FREIGHT => 'Cost, Insurance and Freight',
            self::CARRIAGE_AND_INSURANCE_PAID_TO => 'Carriage and Insurance Paid to',
            self::CARRIAGE_PAID_TO => 'Carriage Paid To',
            self::DELIVERED_AT_FRONTIER => 'Delivered At Frontier',
            self::DELIVERED_AT_PLACE => 'Delivered At Place',
            self::DELIVERED_AT_TERMINAL => 'Delivered At Terminal',
            self::DELIVERED_DUTY_PAID => 'Delivered Duty Paid',
            self::DELIVERED_DUTY_UNPAID => 'Delivered Duty Unpaid',
            self::DELIVERED_EX_QUAY => 'Delivered Ex Quay',
            self::DELIVERED_EX_SHIP => 'Delivered Ex Ship',
            self::EX_WORKS => 'Ex Works',
            self::FREE_ALONGSIDE_SHIP => 'Free Alongside Ship',
            self::FREE_CARRIER => 'Free Carrier',
            self::FREE_ON_BOARD => 'Free on Board',
            self::LAINNYA => 'LAINNYA',
        };
    }
}
