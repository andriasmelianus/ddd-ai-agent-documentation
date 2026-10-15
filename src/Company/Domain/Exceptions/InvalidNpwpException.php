<?php

declare(strict_types=1);

namespace Src\Company\Domain\Exceptions;

use DomainException;

final class InvalidNpwpException extends DomainException
{
    public static function empty(): self
    {
        return new self('NPWP cannot be empty.');
    }

    public static function invalidLength(string $value, int $actualLength): self
    {
        return new self(sprintf(
            'NPWP "%s" has invalid length (%d digits). NPWP must be 15 or 16 digits.',
            $value,
            $actualLength,
        ));
    }

    public static function invalidCharacters(string $value): self
    {
        return new self(sprintf(
            'NPWP "%s" contains invalid characters.',
            $value,
        ));
    }
}
