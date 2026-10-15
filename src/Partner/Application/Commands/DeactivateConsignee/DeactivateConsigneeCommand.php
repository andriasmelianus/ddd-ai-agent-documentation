<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\DeactivateConsignee;

use Src\Partner\Domain\ValueObjects\LegacyConsigneeId;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * @see DeactivateConsigneeHandler
 */
final readonly class DeactivateConsigneeCommand implements CommandInterface
{
    public function __construct(
        public LegacyConsigneeId $id,
    ) {}
}
