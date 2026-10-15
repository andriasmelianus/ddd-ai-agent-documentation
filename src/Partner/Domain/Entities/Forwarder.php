<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Entities;

use Src\Partner\Domain\Events\ForwarderActivated;
use Src\Partner\Domain\Events\ForwarderCreated;
use Src\Partner\Domain\Events\ForwarderDeactivated;
use Src\Partner\Domain\Events\ForwarderUpdated;
use Src\Partner\Domain\ValueObjects\LegacyForwarderId;
use Src\Partner\Domain\ValueObjects\PartnerAddress;
use Src\Partner\Domain\ValueObjects\PartnerCode;
use Src\Partner\Domain\ValueObjects\PartnerCompanyId;
use Src\Partner\Domain\ValueObjects\PartnerContact;
use Src\Partner\Domain\ValueObjects\PartnerName;
use Src\Partner\Domain\ValueObjects\PartnerTaxIdentity;
use Src\Shared\Framework\Domain\Entities\BaseAggregateRoot;

/**
 * Class Forwarder
 *
 * Aggregate Root for Freight Forwarder / Shipping Agent.
 */
class Forwarder extends BaseAggregateRoot
{
    private function __construct(
        public readonly LegacyForwarderId $id,
        public readonly PartnerCompanyId $companyId,
        public private(set) ?PartnerCode $code,
        public private(set) PartnerName $name,
        public private(set) PartnerContact $contact,
        public private(set) PartnerAddress $address,
        public private(set) ?PartnerTaxIdentity $taxIdentity,
        public private(set) bool $isActive,
    ) {}

    public static function create(
        LegacyForwarderId $id,
        PartnerCompanyId $companyId,
        PartnerName $name,
        ?PartnerCode $code = null,
        ?PartnerContact $contact = null,
        ?PartnerAddress $address = null,
        ?PartnerTaxIdentity $taxIdentity = null,
        bool $isActive = true,
    ): self {
        $contact ??= new PartnerContact;
        $address ??= new PartnerAddress;

        $forwarder = new self(
            id: $id,
            companyId: $companyId,
            code: $code,
            name: $name,
            contact: $contact,
            address: $address,
            taxIdentity: $taxIdentity,
            isActive: $isActive,
        );

        $forwarder->setIsNew(true);
        $forwarder->recordEvent(new ForwarderCreated($id, $name));

        return $forwarder;
    }

    public static function reconstitute(
        LegacyForwarderId $id,
        PartnerCompanyId $companyId,
        PartnerName $name,
        ?PartnerCode $code,
        PartnerContact $contact,
        PartnerAddress $address,
        ?PartnerTaxIdentity $taxIdentity,
        bool $isActive,
    ): self {
        $forwarder = new self(
            id: $id,
            companyId: $companyId,
            code: $code,
            name: $name,
            contact: $contact,
            address: $address,
            taxIdentity: $taxIdentity,
            isActive: $isActive,
        );

        $forwarder->setIsNew(false);

        return $forwarder;
    }

    public function updateDetails(
        PartnerName $name,
        ?PartnerCode $code = null,
        ?PartnerContact $contact = null,
        ?PartnerAddress $address = null,
        ?PartnerTaxIdentity $taxIdentity = null,
    ): void {
        $this->name = $name;
        $this->code = $code;
        if ($contact !== null) {
            $this->contact = $contact;
        }
        if ($address !== null) {
            $this->address = $address;
        }
        $this->taxIdentity = $taxIdentity;

        $this->recordEvent(new ForwarderUpdated($this->id));
    }

    public function activate(): void
    {
        if (! $this->isActive) {
            $this->isActive = true;
            $this->recordEvent(new ForwarderActivated($this->id));
        }
    }

    public function deactivate(): void
    {
        if ($this->isActive) {
            $this->isActive = false;
            $this->recordEvent(new ForwarderDeactivated($this->id));
        }
    }
}
