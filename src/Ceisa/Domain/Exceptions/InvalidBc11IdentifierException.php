<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Exceptions;

use DomainException;
use Src\Shared\Framework\Domain\Exceptions\VisibleExceptionInterface;
use Throwable;

final class InvalidBc11IdentifierException extends DomainException implements VisibleExceptionInterface
{
    public static function emptyNomorBc11(): self
    {
        return new self('BC 1.1 number cannot be empty.');
    }

    public static function nomorBc11TooLong(string $nomorBc11, int $maxLength): self
    {
        return new self(sprintf(
            'BC 1.1 number "%s" is too long. Maximum length is %d characters.',
            $nomorBc11,
            $maxLength,
        ));
    }

    public static function invalidNomorBc11Characters(string $nomorBc11): self
    {
        return new self(sprintf(
            'BC 1.1 number "%s" contains invalid characters.',
            $nomorBc11,
        ));
    }

    public static function emptyTanggalBc11(): self
    {
        return new self('BC 1.1 date cannot be empty.');
    }

    public static function invalidTanggalBc11(string $tanggalBc11, ?Throwable $previous = null): self
    {
        return new self(
            sprintf('BC 1.1 date "%s" is invalid.', $tanggalBc11),
            0,
            $previous,
        );
    }

    public static function emptyNumber(): self
    {
        return self::emptyNomorBc11();
    }

    public static function numberTooLong(string $number, int $maxLength): self
    {
        return self::nomorBc11TooLong($number, $maxLength);
    }

    public static function invalidNumberCharacters(string $number): self
    {
        return self::invalidNomorBc11Characters($number);
    }

    public static function emptyDate(): self
    {
        return self::emptyTanggalBc11();
    }

    public static function invalidDate(string $date, ?Throwable $previous = null): self
    {
        return self::invalidTanggalBc11($date, $previous);
    }
}
