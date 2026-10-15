<?php

declare(strict_types=1);

namespace Src\Company\Domain\Exceptions;

use DomainException;

final class InvalidNitkuException extends DomainException
{
    public static function empty(): self
    {
        return new self('NITKU cannot be empty.');
    }

    public static function invalidLength(string $value, int $actualLength): self
    {
        return new self(sprintf(
            'NITKU "%s" must be exactly 22 digits, but got %d digits.',
            $value,
            $actualLength,
        ));
    }

    public static function notNumeric(string $value): self
    {
        return new self(sprintf(
            'NITKU "%s" must contain only digits.',
            $value,
        ));
    }
}
