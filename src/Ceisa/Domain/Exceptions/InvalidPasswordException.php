<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Exceptions;

use DomainException;
use Src\Shared\Framework\Domain\Exceptions\VisibleExceptionInterface;

final class InvalidPasswordException extends DomainException implements VisibleExceptionInterface
{
    public static function empty(): self
    {
        return new self('Password cannot be empty.');
    }

    public static function containsWhitespace(): self
    {
        return new self('Password must not contain whitespace.');
    }

    public static function tooShort(string $value, int $minLength): self
    {
        return new self(sprintf(
            'Password is too short. Minimum length is %d characters.',
            $minLength,
        ));
    }

    public static function tooLong(string $value, int $maxLength): self
    {
        return new self(sprintf(
            'Password is too long. Maximum length is %d characters.',
            $maxLength,
        ));
    }

    public static function missingLetter(): self
    {
        return new self('Password must contain at least one letter.');
    }

    public static function missingDigit(): self
    {
        return new self('Password must contain at least one digit/number.');
    }
}
