<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum JenisPengangkutan
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum JenisPengangkutan: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case SATU_SARANA_ANGKUT = '1';
    case INSTALASI_PIPA_TRANSMISI = '2';
    case ANGKUT_LANJUT = '3';
    case ANGKUT_LANJUT_MULTIMODA = '4';
    case BARANG_BAWAAN_PENUMPANG_AWAK_SARKUT = '5';
    case SARANA_ANGKUT_LAINNYA = '6';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::SATU_SARANA_ANGKUT => 'SATU SARANA ANGKUT',
            self::INSTALASI_PIPA_TRANSMISI => 'INSTALASI/PIPA/TRANSMISI',
            self::ANGKUT_LANJUT => 'ANGKUT LANJUT',
            self::ANGKUT_LANJUT_MULTIMODA => 'ANGKUT LANJUT MULTIMODA',
            self::BARANG_BAWAAN_PENUMPANG_AWAK_SARKUT => 'BARANG BAWAAN PENUMPANG / AWAK SARKUT',
            self::SARANA_ANGKUT_LAINNYA => 'SARANA ANGKUT LAINNYA',
        };
    }
}
