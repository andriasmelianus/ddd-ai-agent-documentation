<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Exceptions;

use DomainException;
use Src\Shared\Framework\Domain\Exceptions\VisibleExceptionInterface;

final class InvalidNomorPosBc11Exception extends DomainException implements VisibleExceptionInterface
{
    public static function empty(): self
    {
        return new self('Nomor pos BC 1.1 cannot be empty.');
    }

    public static function tooShort(string $value, int $minLength): self
    {
        return new self(sprintf(
            'Nomor pos BC 1.1 "%s" is too short. Minimum length is %d characters.',
            $value,
            $minLength,
        ));
    }

    public static function tooLong(string $value, int $maxLength): self
    {
        return new self(sprintf(
            'Nomor pos BC 1.1 "%s" is too long. Maximum length is %d characters.',
            $value,
            $maxLength,
        ));
    }

    public static function invalidCharacters(string $value): self
    {
        return new self(sprintf(
            'Nomor pos BC 1.1 "%s" contains invalid characters. Only numbers are allowed.',
            $value,
        ));
    }

    public static function nonNumeric(string $value): self
    {
        return self::invalidCharacters($value);
    }
}

class_alias(InvalidNomorPosBc11Exception::class, 'Src\Ceisa\Domain\Exceptions\InvalidPosBc11Exception');
