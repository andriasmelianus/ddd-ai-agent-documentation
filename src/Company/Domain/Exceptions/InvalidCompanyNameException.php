<?php

declare(strict_types=1);

namespace Src\Company\Domain\Exceptions;

use DomainException;

final class InvalidCompanyNameException extends DomainException
{
    public static function empty(): self
    {
        return new self('Company name cannot be empty.');
    }

    public static function tooLong(string $value, int $maxLength): self
    {
        return new self(sprintf(
            'Company name "%s" exceeds maximum allowed length of %d characters.',
            $value,
            $maxLength,
        ));
    }

    public static function invalidCharacters(string $value): self
    {
        return new self(sprintf(
            'Company name "%s" contains invalid characters.',
            $value,
        ));
    }
}
