<?php

declare(strict_types=1);

namespace Src\Partner\Domain\ValueObjects;

/**
 * Class PartnerContact
 *
 * Immutable Value Object encapsulating contact phone and email.
 */
readonly class PartnerContact
{
    public function __construct(
        public ?string $phone = null,
        public ?string $email = null,
    ) {}

    public static function fromPrimitives(?string $phone, ?string $email): self
    {
        $cleanPhone = $phone !== null ? trim($phone) : null;
        $cleanEmail = $email !== null ? trim($email) : null;

        return new self(
            phone: ($cleanPhone !== null && $cleanPhone !== '') ? $cleanPhone : null,
            email: ($cleanEmail !== null && $cleanEmail !== '') ? $cleanEmail : null,
        );
    }
}
