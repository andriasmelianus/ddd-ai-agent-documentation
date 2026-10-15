<?php

declare(strict_types=1);

namespace Src\Company\Domain\ValueObjects;

use Src\Company\Domain\Exceptions\InvalidPhoneNumberException;
use Stringable;

/**
 * Class PhoneNumber
 *
 * Immutable Value Object representing a validated contact telephone or mobile number.
 */
readonly class PhoneNumber implements Stringable
{
    private const string PATTERN = '/\A\+?[0-9\-\s().]{6,30}\z/';

    public string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw InvalidPhoneNumberException::empty();
        }

        if (preg_match(self::PATTERN, $trimmed) !== 1) {
            throw InvalidPhoneNumberException::invalidFormat($trimmed);
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

    public function clean(): string
    {
        return (string) preg_replace('/[^0-9]/', '', $this->value);
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
