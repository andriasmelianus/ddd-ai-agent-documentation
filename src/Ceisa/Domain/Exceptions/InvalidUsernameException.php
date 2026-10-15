<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Exceptions;

use DomainException;
use Src\Shared\Framework\Domain\Exceptions\VisibleExceptionInterface;

final class InvalidUsernameException extends DomainException implements VisibleExceptionInterface
{
    public static function empty(): self
    {
        return new self('Username cannot be empty.');
    }

    public static function tooShort(string $value, int $minLength): self
    {
        return new self(sprintf(
            'Username "%s" is too short. Minimum length is %d characters.',
            $value,
            $minLength,
        ));
    }

    public static function tooLong(string $value, int $maxLength): self
    {
        return new self(sprintf(
            'Username "%s" is too long. Maximum length is %d characters.',
            $value,
            $maxLength,
        ));
    }

    public static function invalidCharacters(string $value): self
    {
        return new self(sprintf(
            'Username "%s" contains invalid characters. Only letters and numbers are allowed.',
            $value,
        ));
    }
}
