<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Exceptions;

use DomainException;

final class InvalidPartnerCodeException extends DomainException
{
    public static function empty(): self
    {
        return new self('Partner code cannot be empty.');
    }

    public static function tooLong(string $value): self
    {
        return new self(sprintf('Partner code "%s" exceeds maximum length of 50 characters.', $value));
    }
}
