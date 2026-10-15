<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\ValueObjects;

use Src\Ceisa\Domain\Exceptions\InvalidUsernameException;
use Src\Shared\Framework\Domain\ValueObjects\ValueObjectInterface;
use Stringable;

/**
 * Class AuthUsername
 *
 * Immutable Value Object representing CEISA 4.0 account Username.
 *
 * Constraints:
 * - Must consist of letters and numbers only (alphanumeric: A-Z, a-z, 0-9).
 * - Minimum 5 characters.
 * - Maximum 16 characters.
 */
final readonly class AuthUsername implements Stringable, ValueObjectInterface
{
    public const int MIN_LENGTH = 5;

    public const int MAX_LENGTH = 16;

    private const string PATTERN = '/\A[a-zA-Z0-9]+\z/';

    public string $value;

    public function __construct(string $value)
    {
        if ($value === '' || trim($value) === '') {
            throw InvalidUsernameException::empty();
        }

        $length = mb_strlen($value);

        if ($length < self::MIN_LENGTH) {
            throw InvalidUsernameException::tooShort($value, self::MIN_LENGTH);
        }

        if ($length > self::MAX_LENGTH) {
            throw InvalidUsernameException::tooLong($value, self::MAX_LENGTH);
        }

        if (preg_match(self::PATTERN, $value) !== 1) {
            throw InvalidUsernameException::invalidCharacters($value);
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
