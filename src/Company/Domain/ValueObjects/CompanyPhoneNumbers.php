<?php

declare(strict_types=1);

namespace Src\Company\Domain\ValueObjects;

/**
 * Class CompanyPhoneNumbers
 *
 * Composite Value Object representing company contact phone numbers.
 */
readonly class CompanyPhoneNumbers
{
    public function __construct(
        public ?PhoneNumber $primary = null,
        public ?PhoneNumber $secondary = null,
    ) {}

    public static function create(?PhoneNumber $primary, ?PhoneNumber $secondary = null): self
    {
        return new self($primary, $secondary);
    }

    public static function fromStrings(?string $primary, ?string $secondary = null): self
    {
        return new self(
            PhoneNumber::fromStringOrNull($primary),
            PhoneNumber::fromStringOrNull($secondary),
        );
    }

    public static function empty(): self
    {
        return new self(null, null);
    }

    public function hasAny(): bool
    {
        return $this->primary !== null || $this->secondary !== null;
    }

    public function equals(self $other): bool
    {
        $primaryEquals = ($this->primary === null && $other->primary === null)
            || ($this->primary !== null && $other->primary !== null && $this->primary->equals($other->primary));

        $secondaryEquals = ($this->secondary === null && $other->secondary === null)
            || ($this->secondary !== null && $other->secondary !== null && $this->secondary->equals($other->secondary));

        return $primaryEquals && $secondaryEquals;
    }
}
