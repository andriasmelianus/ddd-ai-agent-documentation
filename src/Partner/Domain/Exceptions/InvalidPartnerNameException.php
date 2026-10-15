<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Exceptions;

use DomainException;

final class InvalidPartnerNameException extends DomainException
{
    public static function empty(): self
    {
        return new self('Partner name cannot be empty.');
    }

    public static function tooLong(string $value): self
    {
        return new self(sprintf('Partner name "%s" exceeds maximum length of 255 characters.', $value));
    }
}
