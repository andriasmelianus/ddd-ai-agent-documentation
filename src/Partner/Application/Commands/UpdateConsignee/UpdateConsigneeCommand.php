<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\UpdateConsignee;

use Src\Partner\Domain\ValueObjects\LegacyConsigneeId;
use Src\Partner\Domain\ValueObjects\PartnerAddress;
use Src\Partner\Domain\ValueObjects\PartnerCode;
use Src\Partner\Domain\ValueObjects\PartnerContact;
use Src\Partner\Domain\ValueObjects\PartnerName;
use Src\Partner\Domain\ValueObjects\PartnerTaxIdentity;
use Src\Partner\Domain\ValueObjects\TransactionTypeCode;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * @see UpdateConsigneeHandler
 */
final readonly class UpdateConsigneeCommand implements CommandInterface
{
    public function __construct(
        public LegacyConsigneeId $id,
        public PartnerName $name,
        public ?PartnerCode $code = null,
        public ?PartnerContact $contact = null,
        public ?PartnerAddress $address = null,
        public ?PartnerTaxIdentity $taxIdentity = null,
        public ?TransactionTypeCode $transactionTypeCode = null,
    ) {}
}
