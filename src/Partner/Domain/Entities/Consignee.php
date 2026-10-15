<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Entities;

use Src\Partner\Domain\Events\ConsigneeActivated;
use Src\Partner\Domain\Events\ConsigneeCreated;
use Src\Partner\Domain\Events\ConsigneeDeactivated;
use Src\Partner\Domain\Events\ConsigneeUpdated;
use Src\Partner\Domain\ValueObjects\LegacyConsigneeId;
use Src\Partner\Domain\ValueObjects\PartnerAddress;
use Src\Partner\Domain\ValueObjects\PartnerCode;
use Src\Partner\Domain\ValueObjects\PartnerCompanyId;
use Src\Partner\Domain\ValueObjects\PartnerContact;
use Src\Partner\Domain\ValueObjects\PartnerName;
use Src\Partner\Domain\ValueObjects\PartnerTaxIdentity;
use Src\Partner\Domain\ValueObjects\TransactionTypeCode;
use Src\Shared\Framework\Domain\Entities\BaseAggregateRoot;

/**
 * Class Consignee
 *
 * Aggregate Root for Consignee (Importer / Receiver of Cargo).
 */
class Consignee extends BaseAggregateRoot
{
    private function __construct(
        public readonly LegacyConsigneeId $id,
        public readonly PartnerCompanyId $companyId,
        public private(set) ?PartnerCode $code,
        public private(set) PartnerName $name,
        public private(set) PartnerContact $contact,
        public private(set) PartnerAddress $address,
        public private(set) ?PartnerTaxIdentity $taxIdentity,
        public private(set) TransactionTypeCode $transactionTypeCode,
        public private(set) bool $isActive,
    ) {}

    public static function create(
        LegacyConsigneeId $id,
        PartnerCompanyId $companyId,
        PartnerName $name,
        ?PartnerCode $code = null,
        ?PartnerContact $contact = null,
        ?PartnerAddress $address = null,
        ?PartnerTaxIdentity $taxIdentity = null,
        ?TransactionTypeCode $transactionTypeCode = null,
        bool $isActive = true,
    ): self {
        $contact ??= new PartnerContact;
        $address ??= new PartnerAddress;
        $transactionTypeCode ??= new TransactionTypeCode;

        $consignee = new self(
            id: $id,
            companyId: $companyId,
            code: $code,
            name: $name,
            contact: $contact,
            address: $address,
            taxIdentity: $taxIdentity,
            transactionTypeCode: $transactionTypeCode,
            isActive: $isActive,
        );

        $consignee->setIsNew(true);
        $consignee->recordEvent(new ConsigneeCreated($id, $name));

        return $consignee;
    }

    public static function reconstitute(
        LegacyConsigneeId $id,
        PartnerCompanyId $companyId,
        PartnerName $name,
        ?PartnerCode $code,
        PartnerContact $contact,
        PartnerAddress $address,
        ?PartnerTaxIdentity $taxIdentity,
        TransactionTypeCode $transactionTypeCode,
        bool $isActive,
    ): self {
        $consignee = new self(
            id: $id,
            companyId: $companyId,
            code: $code,
            name: $name,
            contact: $contact,
            address: $address,
            taxIdentity: $taxIdentity,
            transactionTypeCode: $transactionTypeCode,
            isActive: $isActive,
        );

        $consignee->setIsNew(false);

        return $consignee;
    }

    public function updateDetails(
        PartnerName $name,
        ?PartnerCode $code = null,
        ?PartnerContact $contact = null,
        ?PartnerAddress $address = null,
        ?PartnerTaxIdentity $taxIdentity = null,
        ?TransactionTypeCode $transactionTypeCode = null,
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
        if ($transactionTypeCode !== null) {
            $this->transactionTypeCode = $transactionTypeCode;
        }

        $this->recordEvent(new ConsigneeUpdated($this->id));
    }

    public function activate(): void
    {
        if (! $this->isActive) {
            $this->isActive = true;
            $this->recordEvent(new ConsigneeActivated($this->id));
        }
    }

    public function deactivate(): void
    {
        if ($this->isActive) {
            $this->isActive = false;
            $this->recordEvent(new ConsigneeDeactivated($this->id));
        }
    }
}
