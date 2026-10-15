<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum FlagPersetujuan
 *
 * Status persetujuan petugas Bea Cukai pada respon PLP CEISA 4.0.
 * - 'Y' : Disetujui
 * - 'T' : Ditolak
 */
enum FlagPersetujuan: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case SETUJU = 'Y';
    case TOLAK = 'T';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk status persetujuan ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::SETUJU => 'Disetujui',
            self::TOLAK => 'Ditolak',
        };
    }

    public function isSetuju(): bool
    {
        return $this === self::SETUJU;
    }

    public function isTolak(): bool
    {
        return $this === self::TOLAK;
    }

    public static function fromValue(self|string $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        $upper = strtoupper(trim($value));

        return match ($upper) {
            'Y', 'YES', 'TRUE', '1', 'SETUJU' => self::SETUJU,
            'T', 'NO', 'FALSE', '0', 'TOLAK' => self::TOLAK,
            default => self::from($upper),
        };
    }
}

