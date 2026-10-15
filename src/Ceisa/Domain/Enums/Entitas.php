<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum Entitas
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum Entitas: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case IMPORTIR = '1';
    case EKSPORTIR = '2';
    case PENGUSAHA = '3';
    case PPJK = '4';
    case PEMASOK = '5';
    case PEMBELI = '6';
    case PEMILIK = '7';
    case PENERIMA = '8';
    case PENGIRIM = '9';
    case PENJUAL = '10';
    case PEMUSATAN = '11';
    case PENANDATANGAN = '12';
    case VENDOR = '13';
    case PENGANGKUT = '21';
    case AGEN_PENGANGKUT = '22';
    case KONSOLIDATOR = '23';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::IMPORTIR => 'IMPORTIR',
            self::EKSPORTIR => 'EKSPORTIR',
            self::PENGUSAHA => 'PENGUSAHA',
            self::PPJK => 'PPJK',
            self::PEMASOK => 'PEMASOK',
            self::PEMBELI => 'PEMBELI',
            self::PEMILIK => 'PEMILIK',
            self::PENERIMA => 'PENERIMA',
            self::PENGIRIM => 'PENGIRIM',
            self::PENJUAL => 'PENJUAL',
            self::PEMUSATAN => 'PEMUSATAN',
            self::PENANDATANGAN => 'PENANDATANGAN',
            self::VENDOR => 'VENDOR',
            self::PENGANGKUT => 'PENGANGKUT',
            self::AGEN_PENGANGKUT => 'AGEN PENGANGKUT',
            self::KONSOLIDATOR => 'KONSOLIDATOR',
        };
    }
}
