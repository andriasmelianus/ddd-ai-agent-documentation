<?php

declare(strict_types=1);

namespace Src\Shared\Common\ValueObjects;

use Src\Shared\Common\Exceptions\InvalidAddressException;
use Stringable;

/**
 * Class BaseAddress
 *
 * Immutable Value Object representing generic business "Address" text
 * shared across bounded contexts.
 *
 * Invariants enforced constructor:
 * value MUST NOT be empty (after trimming).
 * value automatically trimmed leading/trailing whitespace.
 * value MUST NOT be longer than 255 characters.
 * value MUST only contain following characters:
 * Alphabet A-Z, a-z
 * Numeric 0-9
 * Spaces
 * Dot .
 * Comma ,
 * Dash -
 * Slash /
 * Parentheses ()
 * Hash #
 * Single quote '
 * Ampersand &
 * Colon :
 * Plus sign +
 * Underscore _
 */
readonly class BaseAddress implements Stringable
{
    /**
     * Maximum address length.
     */
    private const int MAX_LENGTH = 255;

    /**
     * Regular expression describing allowed character set.
     *
     * Allowed characters: A-Z, a-z, 0-9, spaces, dot (.), comma (,), dash (-),
     * slash (/), parentheses (), hash (#), single quote ('), ampersand (&),
     * colon (:), plus (+), and underscore (_).
     */
    private const string PATTERN = '/\A[A-Za-z0-9 .,\-\/()#\'&:+_]+\z/';

    /**
     * Validated, immutable address value (trimmed).
     */
    public string $value;

    /**
     * Build new Address value object.
     *
     * Provided value automatically trimmed leading/trailing whitespace before validation.
     *
     * @param  string  $value  Raw address string.
     *
     * @throws InvalidAddressException When value empty after trimming, too long, or contains disallowed characters.
     */
    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw InvalidAddressException::empty();
        }

        if (mb_strlen($trimmed) > self::MAX_LENGTH) {
            throw InvalidAddressException::tooLong(self::MAX_LENGTH);
        }

        if (preg_match(self::PATTERN, $trimmed) !== 1) {
            throw InvalidAddressException::invalidCharacters($trimmed);
        }

        $this->value = $trimmed;
    }

    /**
     * Named constructor more expressive call-site.
     *
     * @param  string  $value  Raw address string.
     *
     * @throws InvalidAddressException When validation fails.
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
     * Structural equality: two Address objects equal when
     * underlying string values strictly identical.
     *
     * @param  BaseAddress  $other  Other Address instance compare with.
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation Address (returns the raw value).
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
