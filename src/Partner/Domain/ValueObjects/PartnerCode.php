<?php

declare(strict_types=1);

namespace Src\Partner\Domain\ValueObjects;

use Src\Partner\Domain\Exceptions\InvalidPartnerCodeException;
use Stringable;

/**
 * Class PartnerCode
 *
 * Immutable Value Object representing external partner code.
 */
readonly class PartnerCode implements Stringable
{
    public string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw InvalidPartnerCodeException::empty();
        }

        if (strlen($trimmed) > 50) {
            throw InvalidPartnerCodeException::tooLong($trimmed);
        }

        $this->value = $trimmed;
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

    public function __toString(): string
    {
        return $this->value;
    }
}
