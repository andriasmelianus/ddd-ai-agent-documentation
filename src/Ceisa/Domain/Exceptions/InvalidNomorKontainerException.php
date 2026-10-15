<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Exceptions;

use DomainException;
use Src\Shared\Framework\Domain\Exceptions\VisibleExceptionInterface;

final class InvalidNomorKontainerException extends DomainException implements VisibleExceptionInterface
{
    public static function empty(): self
    {
        return new self('Nomor kontainer cannot be empty.');
    }

    public static function invalidLength(string $value, int $length): self
    {
        return new self(sprintf(
            'Nomor kontainer "%s" is invalid. Length must be exactly 11 characters (got %d).',
            $value,
            $length,
        ));
    }

    public static function invalidFormat(string $value): self
    {
        return new self(sprintf(
            'Nomor kontainer "%s" does not match standard ISO 6346 format (4 letters followed by 7 digits, e.g. CSQU3054383).',
            $value,
        ));
    }

    public static function invalidCategoryIdentifier(string $value, string $category): self
    {
        return new self(sprintf(
            'Nomor kontainer "%s" has invalid equipment category identifier "%s". Must be "U" (freight container), "J" (detachable equipment), or "Z" (trailer/chassis).',
            $value,
            $category,
        ));
    }

    public static function invalidCheckDigit(string $value, int $expected, int $actual): self
    {
        return new self(sprintf(
            'Nomor kontainer "%s" has invalid check digit %d (expected %d based on ISO 6346 modulo 11 algorithm).',
            $value,
            $actual,
            $expected,
        ));
    }

    public static function emptyContainerNumber(): self
    {
        return self::empty();
    }
}

class_alias(InvalidNomorKontainerException::class, 'Src\Ceisa\Domain\Exceptions\InvalidContainerNumberException');
