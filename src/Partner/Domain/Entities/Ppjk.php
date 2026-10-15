<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Entities;

use Src\Partner\Domain\Events\PpjkActivated;
use Src\Partner\Domain\Events\PpjkCreated;
use Src\Partner\Domain\Events\PpjkDeactivated;
use Src\Partner\Domain\Events\PpjkUpdated;
use Src\Partner\Domain\ValueObjects\LegacyPpjkId;
use Src\Partner\Domain\ValueObjects\PartnerAddress;
use Src\Partner\Domain\ValueObjects\PartnerCode;
use Src\Partner\Domain\ValueObjects\PartnerCompanyId;
use Src\Partner\Domain\ValueObjects\PartnerContact;
use Src\Partner\Domain\ValueObjects\PartnerName;
use Src\Partner\Domain\ValueObjects\PartnerTaxIdentity;
use Src\Shared\Framework\Domain\Entities\BaseAggregateRoot;

/**
 * Class Ppjk
 *
 * Aggregate Root for Customs Broker (Pengusaha Pengurusan Jasa Kepabeanan - PPJK).
 */
class Ppjk extends BaseAggregateRoot
{
    private function __construct(
        public readonly LegacyPpjkId $id,
        public readonly PartnerCompanyId $companyId,
        public private(set) ?PartnerCode $code,
        public private(set) PartnerName $name,
        public private(set) PartnerContact $contact,
        public private(set) PartnerAddress $address,
        public private(set) ?PartnerTaxIdentity $taxIdentity,
        public private(set) bool $isActive,
    ) {}

    public static function create(
        LegacyPpjkId $id,
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

        $ppjk = new self(
            id: $id,
            companyId: $companyId,
            code: $code,
            name: $name,
            contact: $contact,
            address: $address,
            taxIdentity: $taxIdentity,
            isActive: $isActive,
        );

        $ppjk->setIsNew(true);
        $ppjk->recordEvent(new PpjkCreated($id, $name));

        return $ppjk;
    }

    public static function reconstitute(
        LegacyPpjkId $id,
        PartnerCompanyId $companyId,
        PartnerName $name,
        ?PartnerCode $code,
        PartnerContact $contact,
        PartnerAddress $address,
        ?PartnerTaxIdentity $taxIdentity,
        bool $isActive,
    ): self {
        $ppjk = new self(
            id: $id,
            companyId: $companyId,
            code: $code,
            name: $name,
            contact: $contact,
            address: $address,
            taxIdentity: $taxIdentity,
            isActive: $isActive,
        );

        $ppjk->setIsNew(false);

        return $ppjk;
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

        $this->recordEvent(new PpjkUpdated($this->id));
    }

    public function activate(): void
    {
        if (! $this->isActive) {
            $this->isActive = true;
            $this->recordEvent(new PpjkActivated($this->id));
        }
    }

    public function deactivate(): void
    {
        if ($this->isActive) {
            $this->isActive = false;
            $this->recordEvent(new PpjkDeactivated($this->id));
        }
    }
}
