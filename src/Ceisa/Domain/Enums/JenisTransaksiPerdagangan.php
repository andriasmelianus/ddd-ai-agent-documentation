<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum JenisTransaksiPerdagangan
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum JenisTransaksiPerdagangan: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case TRANSAKSI_PERDAGANGAN_DENGAN_IMBAL_DAGANG = 'IMB';
    case PEMBAYARAN_DILAKUKAN_DENGAN_INTEROFFICE_ACCOUNT = 'IOA';
    case PEMBAYARAN_KEMUDIAN = 'KMD';
    case PEMBAYARAN_DILAKUKAN_DENGAN_KONSINYASI = 'KON';
    case TRANSAKSI_PERDAGANGAN_ATAU_CARA_PEMBAYARAN_LAINNYA = 'LAI';
    case PEMBAYARAN_DILAKUKAN_DIMUKA = 'PMK';
    case PEMBAYARAN_DENGAN_RED_CLAUSE_LETTER_OF_CREDIT = 'RLC';
    case PEMBAYARAN_DENGAN_SIGHT_LETTER_OF_CREDIT = 'SLC';
    case PEMBAYARAN_DENGAN_USANCE_LETTER_OF_CREDIT = 'ULC';
    case PEMBAYARAN_DILAKUKAN_DENGAN_WESEL_INKASO = 'WSI';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::TRANSAKSI_PERDAGANGAN_DENGAN_IMBAL_DAGANG => 'Transaksi Perdagangan Dengan Imbal Dagang',
            self::PEMBAYARAN_DILAKUKAN_DENGAN_INTEROFFICE_ACCOUNT => 'Pembayaran Dilakukan Dengan Interoffice Account',
            self::PEMBAYARAN_KEMUDIAN => 'Pembayaran Kemudian',
            self::PEMBAYARAN_DILAKUKAN_DENGAN_KONSINYASI => 'Pembayaran Dilakukan Dengan Konsinyasi',
            self::TRANSAKSI_PERDAGANGAN_ATAU_CARA_PEMBAYARAN_LAINNYA => 'Transaksi Perdagangan Atau Cara Pembayaran Lainnya',
            self::PEMBAYARAN_DILAKUKAN_DIMUKA => 'Pembayaran Dilakukan Dimuka',
            self::PEMBAYARAN_DENGAN_RED_CLAUSE_LETTER_OF_CREDIT => 'Pembayaran Dengan Red Clause Letter Of Credit',
            self::PEMBAYARAN_DENGAN_SIGHT_LETTER_OF_CREDIT => 'Pembayaran Dengan Sight Letter Of Credit',
            self::PEMBAYARAN_DENGAN_USANCE_LETTER_OF_CREDIT => 'Pembayaran Dengan Usance Letter Of Credit',
            self::PEMBAYARAN_DILAKUKAN_DENGAN_WESEL_INKASO => 'Pembayaran Dilakukan Dengan Wesel Inkaso',
        };
    }
}
