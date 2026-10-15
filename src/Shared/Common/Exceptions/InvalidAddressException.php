<?php

declare(strict_types=1);

namespace Src\Shared\Common\Exceptions;

use DomainException;

/**
 * Class InvalidAddressException
 *
 * Domain exception thrown when value fails validation rules
 * for generic shared Address value object.
 */
final class InvalidAddressException extends DomainException
{
    /**
     * Factory "empty value" case.
     */
    public static function empty(): self
    {
        return new self('Address value cannot be empty.');
    }

    /**
     * Factory "disallowed characters" case.
     *
     * @param  string  $value  Offending value.
     */
    public static function invalidCharacters(string $value): self
    {
        return new self(sprintf(
            'Address value "%s" contains invalid characters. Only alphabet (A-Z, a-z), numeric (0-9), spaces, dot (.), dash (-), slash (/), and parentheses allowed.',
            $value,
        ));
    }

    /**
     * Factory "too long value" case.
     *
     * @param  int  $maxLength  Maximum allowed length.
     */
    public static function tooLong(int $maxLength): self
    {
        return new self(sprintf(
            'Address value cannot be longer than %d characters.',
            $maxLength,
        ));
    }
}
