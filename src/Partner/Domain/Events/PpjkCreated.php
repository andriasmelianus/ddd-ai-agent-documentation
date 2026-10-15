<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Events;

use Src\Partner\Domain\ValueObjects\LegacyPpjkId;
use Src\Partner\Domain\ValueObjects\PartnerName;
use Src\Shared\Framework\Domain\Events\DomainEvent;

final class PpjkCreated extends DomainEvent
{
    public function __construct(
        public readonly LegacyPpjkId $ppjkId,
        public readonly PartnerName $name,
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return 'ppjk.created';
    }
}
