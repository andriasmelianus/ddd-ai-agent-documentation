<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum BarkirKategoriBarang
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum BarkirKategoriBarang: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case PERDAGANGAN = '1';
    case NON_PERDAGANGAN = '2';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::PERDAGANGAN => 'Perdagangan',
            self::NON_PERDAGANGAN => 'Non Perdagangan',
        };
    }

    /**
     * Mendapatkan keterangan tambahan dari referensi.
     */
    public function keterangan(): ?string
    {
        return match ($this) {
            self::PERDAGANGAN => 'Untuk CN Perdagangan',
            self::NON_PERDAGANGAN => 'Untuk CN Non Perdagangan',
        };
    }
}
