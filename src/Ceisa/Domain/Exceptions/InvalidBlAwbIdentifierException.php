<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Exceptions;

use DomainException;
use Src\Shared\Framework\Domain\Exceptions\VisibleExceptionInterface;
use Throwable;

final class InvalidBlAwbIdentifierException extends DomainException implements VisibleExceptionInterface
{
    public static function emptyNomorBlAwb(): self
    {
        return new self('BL/AWB number cannot be empty.');
    }

    public static function nomorBlAwbTooLong(string $nomorBlAwb, int $maxLength): self
    {
        return new self(sprintf(
            'BL/AWB number "%s" is too long. Maximum length is %d characters.',
            $nomorBlAwb,
            $maxLength,
        ));
    }

    public static function invalidNomorBlAwbCharacters(string $nomorBlAwb): self
    {
        return new self(sprintf(
            'BL/AWB number "%s" contains invalid characters.',
            $nomorBlAwb,
        ));
    }

    public static function emptyTanggalBlAwb(): self
    {
        return new self('BL/AWB date cannot be empty.');
    }

    public static function invalidTanggalBlAwb(string $tanggalBlAwb, ?Throwable $previous = null): self
    {
        return new self(
            sprintf('BL/AWB date "%s" is invalid or could not be parsed.', $tanggalBlAwb),
            0,
            $previous,
        );
    }

    // Aliases
    public static function emptyNumber(): self
    {
        return self::emptyNomorBlAwb();
    }

    public static function numberTooLong(string $number, int $maxLength): self
    {
        return self::nomorBlAwbTooLong($number, $maxLength);
    }

    public static function invalidNumberCharacters(string $number): self
    {
        return self::invalidNomorBlAwbCharacters($number);
    }

    public static function emptyDate(): self
    {
        return self::emptyTanggalBlAwb();
    }

    public static function invalidDate(string $date, ?Throwable $previous = null): self
    {
        return self::invalidTanggalBlAwb($date, $previous);
    }
}
