<?php

declare(strict_types=1);

namespace Src\Shared\Common\ValueObjects;

use Src\Shared\Common\Exceptions\InvalidNameException;
use Stringable;

/**
 * Class BaseName
 *
 * Immutable Value Object representing generic business "Name" text
 * shared across bounded contexts.
 *
 * Invariants enforced by constructor:
 * - value MUST NOT be empty (after trimming).
 * - value automatically trimmed leading/trailing whitespace.
 * - value MUST NOT exceed {@see self::MAX_LENGTH} characters.
 * - value MUST only contain the following characters:
 *   - Alphabet A-Z, a-z
 *   - Spaces
 *   - Dot .
 */
readonly class BaseName implements Stringable
{
    /**
     * Maximum allowed length for name value (inclusive).
     */
    public const int MAX_LENGTH = 255;

    /**
     * Regular expression describing allowed character set.
     *
     * Allowed characters: A-Z, a-z, spaces, and dot (.).
     */
    private const string PATTERN = '/\A[A-Za-z .]+\z/';

    /**
     * Validated, immutable name value (trimmed).
     */
    public string $value;

    /**
     * Build new Name value object.
     *
     * Provided value automatically trimmed leading/trailing whitespace before validation.
     *
     * @param  string  $value  Raw name string.
     *
     * @throws InvalidNameException When value is empty after trimming, exceeds maximum length,
     *                              or contains disallowed characters.
     */
    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw InvalidNameException::empty();
        }

        if (mb_strlen($trimmed) > self::MAX_LENGTH) {
            throw InvalidNameException::tooLong($trimmed, self::MAX_LENGTH);
        }

        if (preg_match(self::PATTERN, $trimmed) !== 1) {
            throw InvalidNameException::invalidCharacters($trimmed);
        }

        $this->value = $trimmed;
    }

    /**
     * Named constructor for more expressive call-site.
     *
     * @param  string  $value  Raw name string.
     *
     * @throws InvalidNameException When validation fails.
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Return wrapped string value.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Structural equality: two Name objects are equal when
     * underlying string values are strictly identical.
     *
     * @param  BaseName  $other  Other Name instance to compare with.
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation of Name (returns the raw value).
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
