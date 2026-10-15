<?php

declare(strict_types=1);

namespace Src\Shared\Common\Exceptions;

use DomainException;

/**
 * Class InvalidNameException
 *
 * Domain exception thrown when value fails validation rules
 * for generic shared Name value object.
 */
final class InvalidNameException extends DomainException
{
    /**
     * Factory "empty value" case.
     */
    public static function empty(): self
    {
        return new self('Name value cannot be empty.');
    }

    /**
     * Factory "too long" case.
     *
     * @param  string  $value  Offending value.
     * @param  int  $maxLength  Maximum allowed character length.
     */
    public static function tooLong(string $value, int $maxLength): self
    {
        return new self(sprintf(
            'Name value "%s" cannot exceed %d characters.',
            $value,
            $maxLength,
        ));
    }

    /**
     * Factory "disallowed characters" case.
     *
     * @param  string  $value  Offending value.
     */
    public static function invalidCharacters(string $value): self
    {
        return new self(sprintf(
            'Name value "%s" contains invalid characters. Only alphabet (A-Z, a-z), spaces, and dot (.) are allowed.',
            $value,
        ));
    }
}
