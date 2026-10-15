<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\ValueObjects;

use Src\Ceisa\Domain\Exceptions\InvalidKodeTpsException;
use Src\Shared\Framework\Domain\ValueObjects\ValueObjectInterface;
use Stringable;

/**
 * Class KodeTps
 *
 * Immutable Value Object representing CEISA 4.0 TPS Code (Kode TPS).
 *
 * Constraints:
 * - Minimum 4 characters.
 * - Maximum 6 characters.
 * - Must consist of letters and numbers only (alphanumeric: A-Z, a-z, 0-9).
 */
final readonly class KodeTps implements Stringable, ValueObjectInterface
{
    public const int MIN_LENGTH = 4;

    public const int MAX_LENGTH = 6;

    private const string PATTERN = '/\A[a-zA-Z0-9]+\z/';

    public string $value;

    public function __construct(string $value)
    {
        if ($value === '' || trim($value) === '') {
            throw InvalidKodeTpsException::empty();
        }

        $length = mb_strlen($value);

        if ($length < self::MIN_LENGTH) {
            throw InvalidKodeTpsException::tooShort($value, self::MIN_LENGTH);
        }

        if ($length > self::MAX_LENGTH) {
            throw InvalidKodeTpsException::tooLong($value, self::MAX_LENGTH);
        }

        if (preg_match(self::PATTERN, $value) !== 1) {
            throw InvalidKodeTpsException::invalidCharacters($value);
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
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

class_alias(KodeTps::class, 'Src\Ceisa\Domain\ValueObjects\TpsCode');
