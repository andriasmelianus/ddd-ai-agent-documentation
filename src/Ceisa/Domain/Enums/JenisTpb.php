<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum JenisTpb
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum JenisTpb: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case KAWASAN_BERIKAT = '1';
    case GUDANG_BERIKAT = '2';
    case TPPB = '3';
    case TBB = '4';
    case TLB = '5';
    case KDUB = '6';
    case LAINNYA = '7';
    case KAWASAN_BEBAS = '8';
    case KAWASAN_EKONOMI_KHUSUS = '9';
    case KAWASAN_EKONOMI_LAINNYA = '10';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::KAWASAN_BERIKAT => 'KAWASAN BERIKAT',
            self::GUDANG_BERIKAT => 'GUDANG BERIKAT',
            self::TPPB => 'TPPB',
            self::TBB => 'TBB',
            self::TLB => 'TLB',
            self::KDUB => 'KDUB',
            self::LAINNYA => 'LAINNYA',
            self::KAWASAN_BEBAS => 'KAWASAN BEBAS',
            self::KAWASAN_EKONOMI_KHUSUS => 'KAWASAN EKONOMI KHUSUS',
            self::KAWASAN_EKONOMI_LAINNYA => 'KAWASAN EKONOMI LAINNYA',
        };
    }
}
