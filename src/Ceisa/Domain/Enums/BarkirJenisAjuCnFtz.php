<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum BarkirJenisAjuCnFtz
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum BarkirJenisAjuCnFtz: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case CN_PEMASUKAN_DARI_LDP_KE_FTZ = '7';
    case CN_PENGELUARAN_DARI_FTZ_KE_TLDDP = '8';
    case CN_PEMASUKAN_DARI_TLDDP_KE_FTZ = '9';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::CN_PEMASUKAN_DARI_LDP_KE_FTZ => 'CN PEMASUKAN DARI LDP KE FTZ',
            self::CN_PENGELUARAN_DARI_FTZ_KE_TLDDP => 'CN PENGELUARAN DARI FTZ KE TLDDP',
            self::CN_PEMASUKAN_DARI_TLDDP_KE_FTZ => 'CN PEMASUKAN DARI TLDDP KE FTZ',
        };
    }

    /**
     * Mendapatkan keterangan tambahan dari referensi.
     */
    public function keterangan(): ?string
    {
        return match ($this) {
            self::CN_PEMASUKAN_DARI_LDP_KE_FTZ => 'Consignment Note Untuk Pengiriman dari LDP ke FTZ',
            self::CN_PENGELUARAN_DARI_FTZ_KE_TLDDP => 'Consignment Note Untuk Pengiriman dari FTZ ke TLDDP',
            self::CN_PEMASUKAN_DARI_TLDDP_KE_FTZ => 'Consignment Note Untuk Pengiriman dari TLDDP ke FTZ',
        };
    }
}
