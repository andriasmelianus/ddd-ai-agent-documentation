<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Events;

use Src\Partner\Domain\ValueObjects\LegacyPpjkId;
use Src\Shared\Framework\Domain\Events\DomainEvent;

final class PpjkDeactivated extends DomainEvent
{
    public function __construct(
        public readonly LegacyPpjkId $ppjkId,
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return 'ppjk.deactivated';
    }
}
