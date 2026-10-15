<?php

declare(strict_types=1);

namespace Src\Partner\Domain\ValueObjects;

use Stringable;

/**
 * Class PartnerAddress
 *
 * Immutable Value Object encapsulating street address and postal code.
 */
readonly class PartnerAddress implements Stringable
{
    public function __construct(
        public ?string $address = null,
        public ?string $postalCode = null,
    ) {}

    public static function fromPrimitives(?string $address, ?string $postalCode): self
    {
        $cleanAddress = $address !== null ? trim($address) : null;
        $cleanPostal = $postalCode !== null ? trim($postalCode) : null;

        return new self(
            address: ($cleanAddress !== null && $cleanAddress !== '') ? $cleanAddress : null,
            postalCode: ($cleanPostal !== null && $cleanPostal !== '') ? $cleanPostal : null,
        );
    }

    public function __toString(): string
    {
        return (string) ($this->address ?? '');
    }
}
