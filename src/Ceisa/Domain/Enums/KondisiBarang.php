<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum KondisiBarang
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum KondisiBarang: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case BARU = '1';
    case BUKAN_BARU = '2';
    case BAIK = '3';
    case SEGAR = '4';
    case BEKU = '5';
    case BAIK_BARU = '6';
    case BAIK_BEKU = '7';
    case BAIK_BEKAS = '8';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::BARU => 'Baru',
            self::BUKAN_BARU => 'Bukan Baru',
            self::BAIK => 'Baik',
            self::SEGAR => 'Segar',
            self::BEKU => 'Beku',
            self::BAIK_BARU => 'Baik/Baru',
            self::BAIK_BEKU => 'Baik/Beku',
            self::BAIK_BEKAS => 'Baik/Bekas',
        };
    }
}
