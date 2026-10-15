<?php

declare(strict_types=1);

namespace Src\Company\Domain\ValueObjects;

use Src\Company\Domain\Exceptions\InvalidNpwpException;
use Stringable;

/**
 * Class Npwp
 *
 * Immutable Value Object representing an Indonesian Tax Identification Number (NPWP).
 * Supports both 15-digit (legacy) and 16-digit formats (PMK 112/2022).
 */
readonly class Npwp implements Stringable
{
    public string $value;

    public string $clean;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw InvalidNpwpException::empty();
        }

        // Clean digits
        $clean = preg_replace('/[^0-9]/', '', $trimmed);

        if (! is_string($clean) || ($clean === '')) {
            throw InvalidNpwpException::invalidCharacters($trimmed);
        }

        $length = strlen($clean);
        if ($length !== 15 && $length !== 16) {
            throw InvalidNpwpException::invalidLength($trimmed, $length);
        }

        $this->value = $trimmed;
        $this->clean = $clean;
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

    public function clean(): string
    {
        return $this->clean;
    }

    public function is16Digits(): bool
    {
        return strlen($this->clean) === 16;
    }

    public function formatted(): string
    {
        if (strlen($this->clean) === 15) {
            return sprintf(
                '%s.%s.%s.%s-%s.%s',
                substr($this->clean, 0, 2),
                substr($this->clean, 2, 3),
                substr($this->clean, 5, 3),
                substr($this->clean, 8, 1),
                substr($this->clean, 9, 3),
                substr($this->clean, 12, 3),
            );
        }

        return $this->clean;
    }

    public function equals(self $other): bool
    {
        return $this->clean === $other->clean;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
