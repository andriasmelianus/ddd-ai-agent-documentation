<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\ActivateForwarder;

use Src\Partner\Domain\ValueObjects\LegacyForwarderId;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * @see ActivateForwarderHandler
 */
final readonly class ActivateForwarderCommand implements CommandInterface
{
    public function __construct(
        public LegacyForwarderId $id,
    ) {}
}
