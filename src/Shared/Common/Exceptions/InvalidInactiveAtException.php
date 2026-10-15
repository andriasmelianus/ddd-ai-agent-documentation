<?php

declare(strict_types=1);

namespace Src\Shared\Common\Exceptions;

use DomainException;
use Throwable;

/**
 * Class InvalidInactiveAtException
 *
 * Domain exception thrown when a value fails the validation rules
 * of the {@see \Src\Shared\Common\ValueObjects\BaseInactiveAt} value object.
 */
final class InvalidInactiveAtException extends DomainException
{
    /**
     * Factory for the "value is an empty string" case.
     */
    public static function emptyString(): self
    {
        return new self('InactiveAt value cannot be an empty string.');
    }

    /**
     * Factory for the "string value cannot be parsed as a date/time" case.
     *
     * @param  string  $value  The offending raw value.
     * @param  Throwable|null  $previous  The underlying parser exception, if any.
     */
    public static function unparseableString(string $value, ?Throwable $previous = null): self
    {
        return new self(
            sprintf('InactiveAt value "%s" is not a parseable date/time string.', $value),
            0,
            $previous,
        );
    }

    /**
     * Factory for the "timestamp is out of range" case.
     *
     * @param  int  $value  The offending Unix timestamp.
     */
    public static function invalidTimestamp(int $value): self
    {
        return new self(sprintf('InactiveAt timestamp "%d" is not a valid Unix timestamp.', $value));
    }
}
