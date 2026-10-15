<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\ValueObjects;

use JsonSerializable;
use Src\Ceisa\Domain\Exceptions\InvalidNomorPosBc11Exception;
use Src\Shared\Framework\Domain\ValueObjects\ValueObjectInterface;
use Stringable;

/**
 * Class NomorPosBc11
 *
 * Immutable Value Object representing CEISA 4.0 Pos BC 1.1 Number (Nomor Pos BC 1.1).
 *
 * Constraints:
 * - Minimum 4 characters.
 * - Maximum 16 characters.
 * - Numbers only in string format (digits: 0-9).
 */
final readonly class NomorPosBc11 implements JsonSerializable, Stringable, ValueObjectInterface
{
    public const int MIN_LENGTH = 4;

    public const int MAX_LENGTH = 16;

    private const string PATTERN = '/\A[0-9]+\z/';

    public string $value;

    public function __construct(string $value)
    {
        if ($value === '' || trim($value) === '') {
            throw InvalidNomorPosBc11Exception::empty();
        }

        $length = mb_strlen($value);

        if ($length < self::MIN_LENGTH) {
            throw InvalidNomorPosBc11Exception::tooShort($value, self::MIN_LENGTH);
        }

        if ($length > self::MAX_LENGTH) {
            throw InvalidNomorPosBc11Exception::tooLong($value, self::MAX_LENGTH);
        }

        if (preg_match(self::PATTERN, $value) !== 1) {
            throw InvalidNomorPosBc11Exception::invalidCharacters($value);
        }

        $this->value = $value;
    }

    public static function create(string $value): self
    {
        return new self($value);
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

    public static function fromInt(int $value, int $padLength = self::MIN_LENGTH): self
    {
        if ($value < 0) {
            throw InvalidNomorPosBc11Exception::invalidCharacters((string) $value);
        }

        $str = (string) $value;
        if (strlen($str) < $padLength) {
            $str = str_pad($str, $padLength, '0', STR_PAD_LEFT);
        }

        return new self($str);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function toInt(): int
    {
        return (int) $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

class_alias(NomorPosBc11::class, 'Src\Ceisa\Domain\ValueObjects\PosBc11');
class_alias(NomorPosBc11::class, 'Src\Ceisa\Domain\ValueObjects\PosManifestBc11');
