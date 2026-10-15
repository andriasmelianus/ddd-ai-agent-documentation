<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Exceptions;

use DomainException;
use Src\Shared\Framework\Domain\Exceptions\VisibleExceptionInterface;

final class InvalidKodePelabuhanException extends DomainException implements VisibleExceptionInterface
{
    public static function empty(): self
    {
        return new self('Kode pelabuhan cannot be empty.');
    }

    public static function invalidLength(string $value, int $actualLength): self
    {
        return new self(sprintf(
            'Kode pelabuhan "%s" has invalid length (%d). It must be exactly 5 characters according to UN/LOCODE standard.',
            $value,
            $actualLength,
        ));
    }

    public static function tooShort(string $value, int $minLength = 5): self
    {
        return new self(sprintf(
            'Kode pelabuhan "%s" is too short. Minimum length is %d characters.',
            $value,
            $minLength,
        ));
    }

    public static function tooLong(string $value, int $maxLength = 5): self
    {
        return new self(sprintf(
            'Kode pelabuhan "%s" is too long. Maximum length is %d characters.',
            $value,
            $maxLength,
        ));
    }

    public static function invalidFormat(string $value): self
    {
        return new self(sprintf(
            'Kode pelabuhan "%s" does not match UN/LOCODE standard format (2 letters country code followed by 3 alphanumeric location code, e.g. "IDTPP").',
            $value,
        ));
    }

    public static function invalidCountryCode(string $value, string $countryCode): self
    {
        return new self(sprintf(
            'Kode pelabuhan "%s" has invalid country code "%s". Country code must consist of 2 uppercase letters (ISO 3166-1 alpha-2).',
            $value,
            $countryCode,
        ));
    }

    public static function invalidLocationCode(string $value, string $locationCode): self
    {
        return new self(sprintf(
            'Kode pelabuhan "%s" has invalid location code "%s". Location code must consist of 3 alphanumeric characters.',
            $value,
            $locationCode,
        ));
    }

    public static function invalidCharacters(string $value): self
    {
        return new self(sprintf(
            'Kode pelabuhan "%s" contains invalid characters. Only alphanumeric characters according to UN/LOCODE standard are allowed.',
            $value,
        ));
    }
}

class_alias(InvalidKodePelabuhanException::class, 'Src\Ceisa\Domain\Exceptions\InvalidPortCodeException');

