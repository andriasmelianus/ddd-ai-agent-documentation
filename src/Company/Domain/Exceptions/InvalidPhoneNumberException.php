<?php

declare(strict_types=1);

namespace Src\Company\Domain\Exceptions;

use DomainException;

final class InvalidPhoneNumberException extends DomainException
{
    public static function empty(): self
    {
        return new self('Phone number cannot be empty.');
    }

    public static function invalidFormat(string $value): self
    {
        return new self(sprintf(
            'Phone number "%s" format is invalid. Only digits, +, -, and spaces are allowed.',
            $value,
        ));
    }
}
