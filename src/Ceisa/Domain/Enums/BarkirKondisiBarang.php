<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum BarkirKondisiBarang
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum BarkirKondisiBarang: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case BARU = '1';
    case BEKAS = '2';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::BARU => 'Baru',
            self::BEKAS => 'Bekas',
        };
    }
}
