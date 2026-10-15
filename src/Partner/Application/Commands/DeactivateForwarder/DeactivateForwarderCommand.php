<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\DeactivateForwarder;

use Src\Partner\Domain\ValueObjects\LegacyForwarderId;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * @see DeactivateForwarderHandler
 */
final readonly class DeactivateForwarderCommand implements CommandInterface
{
    public function __construct(
        public LegacyForwarderId $id,
    ) {}
}
