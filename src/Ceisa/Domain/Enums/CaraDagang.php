<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum CaraDagang
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum CaraDagang: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case BIASA = '1';
    case IMB_IMBAL_DAGANG = '2';
    case PMK_PEMBAYARAN_DIMUKA_ADVANCE_PAYMENT = '3';
    case KMD_BERTAHAP_PEMBAYARAN_KEMUDIAN_OPEN_ACCOUNT_TUNAI = '4';
    case KMD_TUNAI_PEMBAYARAN_KEMUDIAN_OPEN_ACCOUNT_TUNAI = '5';
    case SLC_SIGHT_LETTER_OF_CREDIT = '6';
    case ULC_USANCE_LETTER_OF_CREDIT = '7';
    case RLC_RED_CLAUSE_LETTER_OF_CREDIT = '8';
    case WSI_WESSEL_INKASO_COLLECTION_DRAFT = '9';
    case KON_KONSINYASI_CONSIGNMENT = '10';
    case ICA_INTER_COMPANY_ACCOUNT = '11';
    case PDN_TUNAI_PEMBAYARAN_DI_DALAM_NEGERI_TUNAI = '12';
    case TT_PEMBAYARAN_DI_DALAM_NEGERI_MELALUI_TELEGRAPH_TRANSFER = '13';
    case NCV_DILAKUKAN_TANPA_PEMBAYARAN = '14';
    case LAINNYA = '15';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::BIASA => 'Biasa',
            self::IMB_IMBAL_DAGANG => 'IMB - Imbal Dagang',
            self::PMK_PEMBAYARAN_DIMUKA_ADVANCE_PAYMENT => 'PMK - Pembayaran dimuka / Advance Payment',
            self::KMD_BERTAHAP_PEMBAYARAN_KEMUDIAN_OPEN_ACCOUNT_TUNAI => 'KMD Bertahap - Pembayaran Kemudian / Open Account Tunai',
            self::KMD_TUNAI_PEMBAYARAN_KEMUDIAN_OPEN_ACCOUNT_TUNAI => 'KMD Tunai - Pembayaran Kemudian / Open Account Tunai',
            self::SLC_SIGHT_LETTER_OF_CREDIT => 'SLC - Sight Letter of Credit',
            self::ULC_USANCE_LETTER_OF_CREDIT => 'ULC - Usance Letter of Credit',
            self::RLC_RED_CLAUSE_LETTER_OF_CREDIT => 'RLC - Red Clause Letter of Credit',
            self::WSI_WESSEL_INKASO_COLLECTION_DRAFT => 'WSI - Wessel Inkaso / Collection Draft',
            self::KON_KONSINYASI_CONSIGNMENT => 'KON - Konsinyasi / Consignment',
            self::ICA_INTER_COMPANY_ACCOUNT => 'ICA - Inter Company Account',
            self::PDN_TUNAI_PEMBAYARAN_DI_DALAM_NEGERI_TUNAI => 'PDN Tunai - Pembayaran di Dalam Negeri Tunai',
            self::TT_PEMBAYARAN_DI_DALAM_NEGERI_MELALUI_TELEGRAPH_TRANSFER => 'TT - Pembayaran di Dalam Negeri melalui Telegraph Transfer',
            self::NCV_DILAKUKAN_TANPA_PEMBAYARAN => 'NCV - Dilakukan tanpa pembayaran',
            self::LAINNYA => 'Lainnya',
        };
    }
}
