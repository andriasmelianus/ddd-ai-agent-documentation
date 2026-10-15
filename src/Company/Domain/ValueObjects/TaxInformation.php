<?php

declare(strict_types=1);

namespace Src\Company\Domain\ValueObjects;

use Src\Shared\Framework\Domain\ValueObjects\CountryCode;

/**
 * Class TaxInformation
 *
 * Composite Value Object representing company tax credentials, NPWP, NITKU,
 * and e-Faktur/Coretax buyer documentation profile.
 */
readonly class TaxInformation
{
    public function __construct(
        public ?Npwp $npwp = null,
        public ?Nitku $nitku = null,
        public ?string $taxName = null,
        public ?string $taxAddress = null,
        public ?string $buyerDocument = null,
        public ?CountryCode $buyerCountry = null,
    ) {}

    public static function create(
        ?Npwp $npwp = null,
        ?Nitku $nitku = null,
        ?string $taxName = null,
        ?string $taxAddress = null,
        ?string $buyerDocument = null,
        ?CountryCode $buyerCountry = null,
    ): self {
        return new self(
            $npwp,
            $nitku,
            $taxName !== null ? trim($taxName) : null,
            $taxAddress !== null ? trim($taxAddress) : null,
            $buyerDocument !== null ? trim($buyerDocument) : null,
            $buyerCountry,
        );
    }

    public static function fromPrimitives(
        ?string $taxId = null,
        ?string $taxTkuId = null,
        ?string $taxName = null,
        ?string $taxAddress = null,
        ?string $buyerDocument = null,
        ?string $buyerCountry = null,
    ): self {
        $buyerCountryCode = null;
        if ($buyerCountry !== null && trim($buyerCountry) !== '') {
            $trimmed = trim($buyerCountry);
            if (strlen($trimmed) === 2) {
                $buyerCountryCode = CountryCode::create(strtoupper($trimmed));
            }
        }

        return self::create(
            npwp: Npwp::fromStringOrNull($taxId),
            nitku: Nitku::fromStringOrNull($taxTkuId),
            taxName: $taxName,
            taxAddress: $taxAddress,
            buyerDocument: $buyerDocument,
            buyerCountry: $buyerCountryCode,
        );
    }

    public static function empty(): self
    {
        return new self;
    }

    public function equals(self $other): bool
    {
        $npwpEquals = ($this->npwp === null && $other->npwp === null)
            || ($this->npwp !== null && $other->npwp !== null && $this->npwp->equals($other->npwp));

        $nitkuEquals = ($this->nitku === null && $other->nitku === null)
            || ($this->nitku !== null && $other->nitku !== null && $this->nitku->equals($other->nitku));

        $buyerCountryEquals = ($this->buyerCountry === null && $other->buyerCountry === null)
            || ($this->buyerCountry !== null && $other->buyerCountry !== null && $this->buyerCountry->equals($other->buyerCountry));

        return $npwpEquals
            && $nitkuEquals
            && $this->taxName === $other->taxName
            && $this->taxAddress === $other->taxAddress
            && $this->buyerDocument === $other->buyerDocument
            && $buyerCountryEquals;
    }
}
