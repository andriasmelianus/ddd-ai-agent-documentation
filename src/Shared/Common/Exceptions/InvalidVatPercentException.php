<?php

declare(strict_types=1);

namespace Src\Shared\Common\Exceptions;

use DomainException;

/**
 * Class InvalidVatPercentException
 *
 * Domain exception thrown when a value fails the validation rules of
 * the {@see \Src\Shared\Common\ValueObjects\BaseVatPercent} value object.
 */
final class InvalidVatPercentException extends DomainException
{
    /**
     * Factory for the "value is not numeric" case.
     *
     * @param  string  $value  The offending raw value.
     */
    public static function notNumeric(string $value): self
    {
        return new self(sprintf(
            'VAT percent value "%s" is not a valid numeric value.',
            $value,
        ));
    }

    /**
     * Factory for the "value is not finite" case (NaN or Infinity).
     */
    public static function notFinite(): self
    {
        return new self('VAT percent value must be a finite number.');
    }

    /**
     * Factory for the "value below the minimum" case.
     *
     * @param  string  $value  The offending value (as string, to preserve precision).
     * @param  string  $minimum  The minimum allowed value (inclusive).
     */
    public static function belowMinimum(string $value, string $minimum): self
    {
        return new self(sprintf(
            'VAT percent value "%s" must be greater than or equal to %s.',
            $value,
            $minimum,
        ));
    }

    /**
     * Factory for the "value above the maximum" case.
     *
     * @param  string  $value  The offending value (as string, to preserve precision).
     * @param  string  $maximum  The maximum allowed value (inclusive).
     */
    public static function aboveMaximum(string $value, string $maximum): self
    {
        return new self(sprintf(
            'VAT percent value "%s" must be less than or equal to %s.',
            $value,
            $maximum,
        ));
    }
}
