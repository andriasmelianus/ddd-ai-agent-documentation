<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\ValueObjects;

use Src\Ceisa\Domain\Exceptions\InvalidPasswordException;
use Src\Shared\Framework\Domain\ValueObjects\ValueObjectInterface;
use Stringable;

/**
 * Class AuthPassword
 *
 * Immutable Value Object representing CEISA 4.0 account Password.
 *
 * Constraints:
 * - Minimum 5 characters.
 * - Maximum 16 characters.
 * - Must contain at least one letter and at least one number.
 * - Must not contain whitespace.
 */
final readonly class AuthPassword implements Stringable, ValueObjectInterface
{
    public const int MIN_LENGTH = 5;

    public const int MAX_LENGTH = 16;

    public string $value;

    public function __construct(string $value)
    {
        if ($value === '' || trim($value) === '') {
            throw InvalidPasswordException::empty();
        }

        if (preg_match('/\s/', $value) === 1) {
            throw InvalidPasswordException::containsWhitespace();
        }

        $length = mb_strlen($value);

        if ($length < self::MIN_LENGTH) {
            throw InvalidPasswordException::tooShort($value, self::MIN_LENGTH);
        }

        if ($length > self::MAX_LENGTH) {
            throw InvalidPasswordException::tooLong($value, self::MAX_LENGTH);
        }

        if (preg_match('/[a-zA-Z]/', $value) !== 1) {
            throw InvalidPasswordException::missingLetter();
        }

        if (preg_match('/[0-9]/', $value) !== 1) {
            throw InvalidPasswordException::missingDigit();
        }

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
