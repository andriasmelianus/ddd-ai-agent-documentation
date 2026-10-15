<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Events;

use Src\Partner\Domain\ValueObjects\LegacyConsigneeId;
use Src\Shared\Framework\Domain\Events\DomainEvent;

final class ConsigneeActivated extends DomainEvent
{
    public function __construct(
        public readonly LegacyConsigneeId $consigneeId,
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return 'consignee.activated';
    }
}
