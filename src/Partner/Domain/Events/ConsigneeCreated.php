<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Events;

use Src\Partner\Domain\ValueObjects\LegacyConsigneeId;
use Src\Partner\Domain\ValueObjects\PartnerName;
use Src\Shared\Framework\Domain\Events\DomainEvent;

final class ConsigneeCreated extends DomainEvent
{
    public function __construct(
        public readonly LegacyConsigneeId $consigneeId,
        public readonly PartnerName $name,
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return 'consignee.created';
    }
}
