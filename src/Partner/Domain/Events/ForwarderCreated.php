<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Events;

use Src\Partner\Domain\ValueObjects\LegacyForwarderId;
use Src\Partner\Domain\ValueObjects\PartnerName;
use Src\Shared\Framework\Domain\Events\DomainEvent;

final class ForwarderCreated extends DomainEvent
{
    public function __construct(
        public readonly LegacyForwarderId $forwarderId,
        public readonly PartnerName $name,
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return 'forwarder.created';
    }
}
