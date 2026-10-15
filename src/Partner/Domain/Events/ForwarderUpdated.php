<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Events;

use Src\Partner\Domain\ValueObjects\LegacyForwarderId;
use Src\Shared\Framework\Domain\Events\DomainEvent;

final class ForwarderUpdated extends DomainEvent
{
    public function __construct(
        public readonly LegacyForwarderId $forwarderId,
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return 'forwarder.updated';
    }
}
