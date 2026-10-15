<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum TutupPu
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum TutupPu: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case BC_1_1 = '11';
    case BC_1_2 = '12';
    case BC_1_4 = '14';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::BC_1_1 => 'BC 1.1',
            self::BC_1_2 => 'BC 1.2',
            self::BC_1_4 => 'BC 1.4',
        };
    }
}
