<?php

declare(strict_types=1);

namespace Src\Shared\Common\ValueObjects;

use Src\Shared\Common\Exceptions\InvalidCodeException;
use Stringable;

/**
 * Class Code
 *
 * Immutable Value Object representing a generic business "Code" identifier
 * that is shared across bounded contexts.
 *
 * Invariants enforced by the constructor:
 *  - The value MUST NOT be empty (after trimming).
 *  - The value is automatically trimmed of leading/trailing whitespace.
 *  - The value MUST only contain the following characters:
 *      * Alphabet A-Z, a-z
 *      * Numeric  0-9
 *      * Dot        .
 *      * Dash       -
 *      * Slash      /
 *      * Underscore _
 */
readonly class BaseCode implements Stringable
{
    /**
     * Regular expression describing the allowed character set.
     *
     * Allowed characters: A-Z, a-z, 0-9, dot (.), dash (-),
     * slash (/) and underscore (_).
     */
    private const string PATTERN = '/\A[A-Za-z0-9._\-\/]+\z/';

    /**
     * The validated, immutable code value (trimmed).
     */
    public string $value;

    /**
     * Build a new Code value object.
     *
     * The provided value is automatically trimmed of leading/trailing
     * whitespace before validation.
     *
     * @param  string  $value  Raw code string.
     *
     * @throws InvalidCodeException When the value is empty after trimming
     *                              or contains disallowed characters.
     */
    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw InvalidCodeException::empty();
        }

        if (preg_match(self::PATTERN, $trimmed) !== 1) {
            throw InvalidCodeException::invalidCharacters($trimmed);
        }

        $this->value = $trimmed;
    }

    /**
     * Named constructor for a more expressive call-site.
     *
     * Uses `new static()` so subclasses (e.g. {@see \Src\Company\Domain\ValueObjects\CompanyCode})
     * that call this inherited factory receive an instance of the concrete
     * subclass rather than a {@see BaseCode} instance.
     *
     * @param  string  $value  Raw code string.
     *
     * @throws InvalidCodeException When validation fails.
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }

    /**
     * Return the wrapped string value.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Structural equality: two Code objects are equal when their
     * underlying string values are strictly identical.
     *
     * @param  BaseCode  $other  The other Code instance to compare with.
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation of the Code (returns the raw value).
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
