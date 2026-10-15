<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum FasilitasTarif
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum FasilitasTarif: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case DIBAYAR = '1';
    case DITANGGUNG_PEMERINTAH = '2';
    case DITANGGUHKAN = '3';
    case BERKALA = '4';
    case DIBEBASKAN = '5';
    case TIDAK_DIPUNGUT = '6';
    case SUDAH_DILUNASI = '7';
    case DIJAMINKAN = '8';
    case DITUNDA = '9';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::DIBAYAR => 'DIBAYAR',
            self::DITANGGUNG_PEMERINTAH => 'DITANGGUNG PEMERINTAH',
            self::DITANGGUHKAN => 'DITANGGUHKAN',
            self::BERKALA => 'BERKALA',
            self::DIBEBASKAN => 'DIBEBASKAN',
            self::TIDAK_DIPUNGUT => 'TIDAK DIPUNGUT',
            self::SUDAH_DILUNASI => 'SUDAH DILUNASI',
            self::DIJAMINKAN => 'DIJAMINKAN',
            self::DITUNDA => 'DITUNDA',
        };
    }
}
