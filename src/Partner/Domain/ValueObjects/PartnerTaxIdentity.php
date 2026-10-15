<?php

declare(strict_types=1);

namespace Src\Partner\Domain\ValueObjects;

use Src\Company\Domain\ValueObjects\Nitku;
use Src\Company\Domain\ValueObjects\Npwp;

/**
 * Class PartnerTaxIdentity
 *
 * Immutable Value Object representing the Indonesian tax identity for partners
 * (NPWP 15/16 digit, NITKU 22 digit, tax address, and international buyer document).
 */
readonly class PartnerTaxIdentity
{
    public ?string $taxId;

    public ?string $taxIdClean;

    public ?string $taxTkuId;

    public ?string $taxName;

    public ?string $taxAddress;

    public ?string $taxBuyerDocument;

    public ?string $taxBuyerCountry;

    public function __construct(
        ?string $taxId = null,
        ?string $taxIdClean = null,
        ?string $taxTkuId = null,
        ?string $taxName = null,
        ?string $taxAddress = null,
        ?string $taxBuyerDocument = null,
        ?string $taxBuyerCountry = null,
    ) {
        $this->taxId = ($taxId !== null && trim($taxId) !== '') ? trim($taxId) : null;

        if ($taxIdClean !== null && trim($taxIdClean) !== '') {
            $this->taxIdClean = trim($taxIdClean);
        } elseif ($this->taxId !== null) {
            $digits = preg_replace('/[^0-9]/', '', $this->taxId);
            $this->taxIdClean = is_string($digits) && $digits !== '' ? $digits : null;
        } else {
            $this->taxIdClean = null;
        }

        $this->taxTkuId = ($taxTkuId !== null && trim($taxTkuId) !== '') ? trim($taxTkuId) : null;
        $this->taxName = ($taxName !== null && trim($taxName) !== '') ? trim($taxName) : null;
        $this->taxAddress = ($taxAddress !== null && trim($taxAddress) !== '') ? trim($taxAddress) : null;
        $this->taxBuyerDocument = ($taxBuyerDocument !== null && trim($taxBuyerDocument) !== '') ? trim($taxBuyerDocument) : null;
        $this->taxBuyerCountry = ($taxBuyerCountry !== null && trim($taxBuyerCountry) !== '') ? trim($taxBuyerCountry) : null;
    }

    public static function fromPrimitives(
        ?string $taxId = null,
        ?string $taxTkuId = null,
        ?string $taxName = null,
        ?string $taxAddress = null,
        ?string $taxBuyerDocument = null,
        ?string $taxBuyerCountry = null,
    ): ?self {
        if ($taxId === null && $taxTkuId === null && $taxName === null && $taxAddress === null && $taxBuyerDocument === null) {
            return null;
        }

        return new self(
            taxId: $taxId,
            taxTkuId: $taxTkuId,
            taxName: $taxName,
            taxAddress: $taxAddress,
            taxBuyerDocument: $taxBuyerDocument,
            taxBuyerCountry: $taxBuyerCountry,
        );
    }
}
