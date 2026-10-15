<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\ValueObjects;

use Src\Ceisa\Domain\Exceptions\InvalidAuthApiKeyException;
use Src\Shared\Framework\Domain\ValueObjects\ValueObjectInterface;
use Stringable;

/**
 * Class AuthApiKey
 *
 * Immutable Value Object representing CEISA 4.0 API Key.
 *
 * Constraints:
 * - Cannot be empty or whitespace only.
 * - Maximum 255 characters.
 */
final readonly class AuthApiKey implements Stringable, ValueObjectInterface
{
    public const int MAX_LENGTH = 255;

    public string $value;

    public function __construct(string $value)
    {
        if ($value === '' || trim($value) === '') {
            throw InvalidAuthApiKeyException::empty();
        }

        $length = mb_strlen($value);

        if ($length > self::MAX_LENGTH) {
            throw InvalidAuthApiKeyException::tooLong(self::MAX_LENGTH);
        }

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromStringOrNull(?string $value): ?self
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return hash_equals($this->value, $other->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
