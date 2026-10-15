<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum CaraAngkut
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum CaraAngkut: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case LAUT = '1';
    case KERETA_API = '2';
    case DARAT = '3';
    case UDARA = '4';
    case POS = '5';
    case MULTIMODA = '6';
    case INSTALASI_PIPA = '7';
    case PERAIRAN = '8';
    case LAINNYA = '9';
    case INSTALASI = '10';
    case PIPA = '11';
    case TRANSMISI = '12';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::LAUT => 'LAUT',
            self::KERETA_API => 'KERETA API',
            self::DARAT => 'DARAT',
            self::UDARA => 'UDARA',
            self::POS => 'POS',
            self::MULTIMODA => 'MULTIMODA',
            self::INSTALASI_PIPA => 'INSTALASI / PIPA',
            self::PERAIRAN => 'PERAIRAN',
            self::LAINNYA => 'LAINNYA',
            self::INSTALASI => 'INSTALASI',
            self::PIPA => 'PIPA',
            self::TRANSMISI => 'TRANSMISI',
        };
    }
}
