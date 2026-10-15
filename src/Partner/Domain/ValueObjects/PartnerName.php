<?php

declare(strict_types=1);

namespace Src\Partner\Domain\ValueObjects;

use Src\Partner\Domain\Exceptions\InvalidPartnerNameException;
use Stringable;

/**
 * Class PartnerName
 *
 * Immutable Value Object representing the partner name, generating a normalized name key
 * for customs matching and deduplication.
 */
readonly class PartnerName implements Stringable
{
    public string $value;

    public string $key;

    public function __construct(string $value, ?string $key = null)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw InvalidPartnerNameException::empty();
        }

        if (strlen($trimmed) > 255) {
            throw InvalidPartnerNameException::tooLong($trimmed);
        }

        $this->value = $trimmed;

        if ($key !== null && trim($key) !== '') {
            $this->key = trim($key);
        } else {
            $clean = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($trimmed));
            $this->key = is_string($clean) && $clean !== '' ? $clean : strtoupper($trimmed);
        }
    }

    public static function fromString(string $value, ?string $key = null): self
    {
        return new self($value, $key);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
