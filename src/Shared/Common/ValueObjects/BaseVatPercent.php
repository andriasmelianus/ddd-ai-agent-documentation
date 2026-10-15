<?php

declare(strict_types=1);

namespace Src\Shared\Common\ValueObjects;

use Src\Shared\Common\Exceptions\InvalidVatPercentException;
use Stringable;

/**
 * Class BaseVatPercent
 *
 * Immutable Value Object representing a generic VAT (Value Added Tax) percentage
 * shared across bounded contexts.
 *
 * Invariants enforced by the constructor:
 * - The value MUST be numeric (int, float, or numeric string).
 * - The value MUST be finite (NaN and Infinity are rejected).
 * - The value MUST be greater than or equal to {@see self::MIN_VALUE} (0).
 * - The value MUST be less than or equal to {@see self::MAX_VALUE} (100).
 * - The value is normalised to a fixed decimal precision of {@see self::PRECISION} (4).
 *
 * The stored value is always rounded (half-up) to 4 decimal places to guarantee
 * stable equality semantics across arithmetic representations.
 */
readonly class BaseVatPercent implements Stringable
{
    /**
     * Minimum allowed VAT percent value (inclusive).
     */
    public const float MIN_VALUE = 0.0;

    /**
     * Maximum allowed VAT percent value (inclusive).
     */
    public const float MAX_VALUE = 100.0;

    /**
     * Number of decimal digits preserved for the wrapped value.
     */
    public const int PRECISION = 4;

    /**
     * Validated, immutable VAT percent value, normalised to {@see self::PRECISION} decimals.
     */
    public float $value;

    /**
     * Build a new BaseVatPercent value object.
     *
     * The provided value is normalised to 4-decimal precision using half-up rounding
     * before being stored, to guarantee stable equality semantics.
     *
     * @param  int|float|string  $value  Raw VAT percent value. Numeric strings are accepted
     *                                   so the caller can preserve arbitrary decimal precision.
     *
     * @throws InvalidVatPercentException When the value is not numeric, not finite,
     *                                    or falls outside the inclusive [0, 100] range.
     */
    public function __construct(int|float|string $value)
    {
        if (is_string($value)) {
            $trimmed = trim($value);

            if ($trimmed === '' || ! is_numeric($trimmed)) {
                throw InvalidVatPercentException::notNumeric($value);
            }

            $numeric = (float) $trimmed;
        } else {
            $numeric = (float) $value;
        }

        if (! is_finite($numeric)) {
            throw InvalidVatPercentException::notFinite();
        }

        if ($numeric < self::MIN_VALUE) {
            throw InvalidVatPercentException::belowMinimum(
                (string) $value,
                self::formatBound(self::MIN_VALUE),
            );
        }

        if ($numeric > self::MAX_VALUE) {
            throw InvalidVatPercentException::aboveMaximum(
                (string) $value,
                self::formatBound(self::MAX_VALUE),
            );
        }

        $this->value = round($numeric, self::PRECISION);
    }

    /**
     * Named constructor for a more expressive call-site.
     *
     * @param  int|float|string  $value  Raw VAT percent value.
     *
     * @throws InvalidVatPercentException When validation fails.
     */
    public static function fromNumeric(int|float|string $value): self
    {
        return new self($value);
    }

    /**
     * Convenience factory returning a zero-valued VAT percent.
     */
    public static function zero(): self
    {
        return new self(0);
    }

    /**
     * Return the wrapped float value (rounded to {@see self::PRECISION} decimals).
     */
    public function value(): float
    {
        return $this->value;
    }

    /**
     * Return the value formatted as a fixed-precision string with exactly
     * {@see self::PRECISION} decimal places (e.g. "11.0000").
     */
    public function toFormattedString(): string
    {
        return number_format($this->value, self::PRECISION, '.', '');
    }

    /**
     * Structural equality: two BaseVatPercent objects are equal when their
     * underlying values are identical after being normalised to
     * {@see self::PRECISION} decimals.
     *
     * @param  BaseVatPercent  $other  The other VAT percent instance to compare with.
     */
    public function equals(self $other): bool
    {
        return $this->toFormattedString() === $other->toFormattedString();
    }

    /**
     * String representation of the VAT percent (fixed 4-decimal precision).
     */
    public function __toString(): string
    {
        return $this->toFormattedString();
    }

    /**
     * Format a boundary value for inclusion in exception messages using the
     * configured decimal precision.
     */
    private static function formatBound(float $bound): string
    {
        return number_format($bound, self::PRECISION, '.', '');
    }
}
