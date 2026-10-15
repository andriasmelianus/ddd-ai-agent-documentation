<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\UpdateForwarder;

use Src\Partner\Domain\ValueObjects\LegacyForwarderId;
use Src\Partner\Domain\ValueObjects\PartnerAddress;
use Src\Partner\Domain\ValueObjects\PartnerCode;
use Src\Partner\Domain\ValueObjects\PartnerContact;
use Src\Partner\Domain\ValueObjects\PartnerName;
use Src\Partner\Domain\ValueObjects\PartnerTaxIdentity;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * @see UpdateForwarderHandler
 */
final readonly class UpdateForwarderCommand implements CommandInterface
{
    public function __construct(
        public LegacyForwarderId $id,
        public PartnerName $name,
        public ?PartnerCode $code = null,
        public ?PartnerContact $contact = null,
        public ?PartnerAddress $address = null,
        public ?PartnerTaxIdentity $taxIdentity = null,
    ) {}
}
