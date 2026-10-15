<?php

declare(strict_types=1);

namespace Src\Shared\Common\Exceptions;

use DomainException;

/**
 * Class InvalidCodeException
 *
 * Domain exception thrown when a value fails the validation rules of the
 * {@see \Src\Shared\Common\ValueObjects\Code} value object.
 */
final class InvalidCodeException extends DomainException
{
    /**
     * Factory for the "empty value" case.
     */
    public static function empty(): self
    {
        return new self('Code value cannot be empty.');
    }

    /**
     * Factory for the "disallowed characters" case.
     *
     * @param  string  $value  The offending value.
     */
    public static function invalidCharacters(string $value): self
    {
        return new self(sprintf(
            'Code value "%s" contains invalid characters. '
                .'Only alphabet (A-Z, a-z), numeric (0-9), dot (.), '
                .'dash (-), slash (/) and underscore (_) are allowed.',
            $value,
        ));
    }
}
