<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\CreatePpjk;

use Src\Partner\Domain\ValueObjects\LegacyPpjkId;
use Src\Partner\Domain\ValueObjects\PartnerAddress;
use Src\Partner\Domain\ValueObjects\PartnerCode;
use Src\Partner\Domain\ValueObjects\PartnerCompanyId;
use Src\Partner\Domain\ValueObjects\PartnerContact;
use Src\Partner\Domain\ValueObjects\PartnerName;
use Src\Partner\Domain\ValueObjects\PartnerTaxIdentity;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * @see CreatePpjkHandler
 */
final readonly class CreatePpjkCommand implements CommandInterface
{
    public function __construct(
        public LegacyPpjkId $id,
        public PartnerCompanyId $companyId,
        public PartnerName $name,
        public ?PartnerCode $code = null,
        public ?PartnerContact $contact = null,
        public ?PartnerAddress $address = null,
        public ?PartnerTaxIdentity $taxIdentity = null,
        public bool $isActive = true,
    ) {}
}
