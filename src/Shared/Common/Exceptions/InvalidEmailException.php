<?php

declare(strict_types=1);

namespace Src\Shared\Common\Exceptions;

use DomainException;

/**
 * Class InvalidEmailException
 *
 * Domain exception thrown when a value fails the validation rules
 * of the generic shared Email value object.
 */
final class InvalidEmailException extends DomainException
{
    /**
     * Factory for the "empty value" case.
     */
    public static function empty(): self
    {
        return new self('Email value cannot be empty.');
    }

    /**
     * Factory for the "invalid format" case.
     *
     * @param  string  $value  Offending value.
     */
    public static function invalidFormat(string $value): self
    {
        return new self(sprintf(
            'Email value "%s" is not a valid email address.',
            $value,
        ));
    }

    /**
     * Factory for the "too long value" case.
     *
     * @param  int  $maxLength  Maximum allowed length.
     */
    public static function tooLong(int $maxLength): self
    {
        return new self(sprintf(
            'Email value cannot be longer than %d characters.',
            $maxLength,
        ));
    }
}
