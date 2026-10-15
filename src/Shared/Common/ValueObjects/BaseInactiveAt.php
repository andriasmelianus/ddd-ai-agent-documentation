<?php

declare(strict_types=1);

namespace Src\Shared\Common\ValueObjects;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use Src\Shared\Common\Exceptions\InvalidInactiveAtException;
use Stringable;

/**
 * Class BaseInactiveAt
 *
 * Immutable Value Object representing the point in time at which a
 * domain entity was marked as inactive. Shared across bounded
 * contexts (e.g. `Company`, `Partner`, `User`) whenever an entity has
 * an "inactivated at" audit-trail field.
 *
 * The wrapped value is always stored as a {@see DateTimeImmutable} in
 * UTC, so equality semantics and serialisation are stable regardless
 * of the caller's default timezone.
 *
 * Invariants enforced by the constructor / factories:
 * - When constructed from a string, the value MUST NOT be empty after
 *   trimming and MUST be parseable by {@see DateTimeImmutable}.
 * - When constructed from a Unix timestamp, the value MUST be within
 *   the range supported by {@see DateTimeImmutable} on the current
 *   platform.
 * - {@see DateTimeInterface} inputs are cloned and normalised to UTC
 *   to guarantee immutability and stable equality.
 */
readonly class BaseInactiveAt implements Stringable
{
    /**
     * Validated, immutable "inactivated at" instant, normalised to UTC.
     */
    public DateTimeImmutable $value;

    /**
     * Build a new BaseInactiveAt value object.
     *
     * Accepts:
     * - {@see DateTimeInterface} — the value is cloned to guarantee immutability.
     * - `string` — parsed via {@see DateTimeImmutable::__construct()}.
     * - `int`    — interpreted as a Unix timestamp (seconds since epoch).
     *
     * The result is always normalised to UTC.
     *
     * @param  DateTimeInterface|string|int  $value  Raw "inactivated at" value.
     *
     * @throws InvalidInactiveAtException When the value is an empty string,
     *                                    cannot be parsed as a date/time,
     *                                    or is an out-of-range timestamp.
     */
    public function __construct(DateTimeInterface|string|int $value)
    {
        $this->value = self::normalise($value);
    }

    /**
     * Named constructor accepting any {@see DateTimeInterface} instance.
     *
     * @param  DateTimeInterface  $value  The source date/time instance.
     *
     * @throws InvalidInactiveAtException When the value cannot be normalised.
     */
    public static function fromDateTime(DateTimeInterface $value): static
    {
        return new static($value);
    }

    /**
     * Named constructor accepting a string parseable by {@see DateTimeImmutable}.
     *
     * @param  string  $value  Raw date/time string (e.g. "2026-01-31 12:00:00", "2026-01-31T12:00:00Z").
     *
     * @throws InvalidInactiveAtException When the string is empty or unparseable.
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }

    /**
     * Named constructor accepting a Unix timestamp (seconds since epoch).
     *
     * @param  int  $timestamp  Seconds since the Unix epoch.
     *
     * @throws InvalidInactiveAtException When the timestamp is out of range.
     */
    public static function fromTimestamp(int $timestamp): static
    {
        return new static($timestamp);
    }

    /**
     * Convenience factory returning a BaseInactiveAt initialised to the
     * current instant in UTC.
     */
    public static function now(): static
    {
        return new static(new DateTimeImmutable('now', new DateTimeZone('UTC')));
    }

    /**
     * Return the wrapped {@see DateTimeImmutable} value (UTC-normalised).
     */
    public function value(): DateTimeImmutable
    {
        return $this->value;
    }

    /**
     * Alias for {@see self::value()} that reads more naturally at call sites.
     */
    public function toDateTimeImmutable(): DateTimeImmutable
    {
        return $this->value;
    }

    /**
     * Return the Unix timestamp (seconds since epoch) of the wrapped value.
     */
    public function timestamp(): int
    {
        return $this->value->getTimestamp();
    }

    /**
     * Return the value formatted according to the given format string.
     *
     * @param  string  $format  Any format string supported by {@see DateTimeImmutable::format()}.
     */
    public function format(string $format): string
    {
        return $this->value->format($format);
    }

    /**
     * Return the value formatted as an ISO 8601 / RFC 3339 string in UTC
     * (e.g. `"2026-01-31T12:00:00+00:00"`).
     */
    public function toIso8601(): string
    {
        return $this->value->format(DateTimeInterface::ATOM);
    }

    /**
     * Structural equality: two BaseInactiveAt objects are equal when
     * their underlying Unix timestamps are identical.
     *
     * Using timestamps (rather than string representations) means two
     * instances that describe the same instant in different original
     * timezones are considered equal, which matches the domain intent
     * of an "inactivated at" instant.
     *
     * @param  BaseInactiveAt  $other  The other instance to compare with.
     */
    public function equals(self $other): bool
    {
        return $this->value->getTimestamp() === $other->value->getTimestamp();
    }

    /**
     * Return true when the wrapped instant is strictly before the other's.
     *
     * @param  BaseInactiveAt  $other  The other instance to compare with.
     */
    public function isBefore(self $other): bool
    {
        return $this->value->getTimestamp() < $other->value->getTimestamp();
    }

    /**
     * Return true when the wrapped instant is strictly after the other's.
     *
     * @param  BaseInactiveAt  $other  The other instance to compare with.
     */
    public function isAfter(self $other): bool
    {
        return $this->value->getTimestamp() > $other->value->getTimestamp();
    }

    /**
     * String representation: ISO 8601 / RFC 3339 in UTC.
     */
    public function __toString(): string
    {
        return $this->toIso8601();
    }

    /**
     * Normalise any accepted input type into a UTC {@see DateTimeImmutable}.
     *
     * @param  DateTimeInterface|string|int  $value  Raw input value.
     *
     * @throws InvalidInactiveAtException When the input cannot be normalised.
     */
    private static function normalise(DateTimeInterface|string|int $value): DateTimeImmutable
    {
        $utc = new DateTimeZone('UTC');

        if ($value instanceof DateTimeInterface) {
            if ($value instanceof DateTimeImmutable) {
                return $value->setTimezone($utc);
            }

            return DateTimeImmutable::createFromInterface($value)->setTimezone($utc);
        }

        if (is_int($value)) {
            try {
                $dateTime = (new DateTimeImmutable('@'.$value))->setTimezone($utc);
            } catch (Exception $e) {
                throw InvalidInactiveAtException::invalidTimestamp($value);
            }

            return $dateTime;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            throw InvalidInactiveAtException::emptyString();
        }

        try {
            return (new DateTimeImmutable($trimmed))->setTimezone($utc);
        } catch (Exception $e) {
            throw InvalidInactiveAtException::unparseableString($trimmed, $e);
        }
    }
}
