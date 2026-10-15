<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\CreateConsignee;

use Src\Partner\Domain\ValueObjects\LegacyConsigneeId;
use Src\Partner\Domain\ValueObjects\PartnerAddress;
use Src\Partner\Domain\ValueObjects\PartnerCode;
use Src\Partner\Domain\ValueObjects\PartnerCompanyId;
use Src\Partner\Domain\ValueObjects\PartnerContact;
use Src\Partner\Domain\ValueObjects\PartnerName;
use Src\Partner\Domain\ValueObjects\PartnerTaxIdentity;
use Src\Partner\Domain\ValueObjects\TransactionTypeCode;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * @see CreateConsigneeHandler
 */
final readonly class CreateConsigneeCommand implements CommandInterface
{
    public function __construct(
        public LegacyConsigneeId $id,
        public PartnerCompanyId $companyId,
        public PartnerName $name,
        public ?PartnerCode $code = null,
        public ?PartnerContact $contact = null,
        public ?PartnerAddress $address = null,
        public ?PartnerTaxIdentity $taxIdentity = null,
        public ?TransactionTypeCode $transactionTypeCode = null,
        public bool $isActive = true,
    ) {}
}
