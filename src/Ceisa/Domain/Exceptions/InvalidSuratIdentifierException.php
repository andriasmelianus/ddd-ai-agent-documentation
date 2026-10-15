<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Exceptions;

use DomainException;
use Src\Shared\Framework\Domain\Exceptions\VisibleExceptionInterface;

final class InvalidSuratIdentifierException extends DomainException implements VisibleExceptionInterface
{
    public static function emptyNomorSurat(): self
    {
        return new self('Surat number cannot be empty.');
    }

    public static function nomorSuratTooLong(string $nomorSurat, int $maxLength): self
    {
        return new self(sprintf(
            'Surat number "%s" is too long. Maximum length is %d characters.',
            $nomorSurat,
            $maxLength,
        ));
    }

    public static function emptyTanggalSurat(): self
    {
        return new self('Surat date cannot be empty.');
    }

    public static function invalidTanggalSuratFormat(string $tanggalSurat): self
    {
        return new self(sprintf(
            'Surat date "%s" is invalid or could not be parsed.',
            $tanggalSurat,
        ));
    }

    public static function emptyNumber(): self
    {
        return self::emptyNomorSurat();
    }

    public static function numberTooLong(string $number, int $maxLength): self
    {
        return self::nomorSuratTooLong($number, $maxLength);
    }

    public static function emptyDate(): self
    {
        return self::emptyTanggalSurat();
    }

    public static function invalidDateFormat(string $date): self
    {
        return self::invalidTanggalSuratFormat($date);
    }
}

