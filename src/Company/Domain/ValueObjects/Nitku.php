<?php

declare(strict_types=1);

namespace Src\Company\Domain\ValueObjects;

use Src\Company\Domain\Exceptions\InvalidNitkuException;
use Stringable;

/**
 * Class Nitku
 *
 * Immutable Value Object representing Nomor Induk Tempat Kegiatan Usaha (NITKU / TKU ID).
 * 22 digits: 16-digit NPWP + 6-digit branch/sub-entity code.
 */
readonly class Nitku implements Stringable
{
    public const int LENGTH = 22;

    public string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw InvalidNitkuException::empty();
        }

        $clean = preg_replace('/[^0-9]/', '', $trimmed);

        if (! is_string($clean) || strlen($clean) !== self::LENGTH) {
            throw InvalidNitkuException::invalidLength($trimmed, is_string($clean) ? strlen($clean) : 0);
        }

        $this->value = $clean;
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
