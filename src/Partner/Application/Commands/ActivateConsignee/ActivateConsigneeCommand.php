<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\ActivateConsignee;

use Src\Partner\Domain\ValueObjects\LegacyConsigneeId;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * @see ActivateConsigneeHandler
 */
final readonly class ActivateConsigneeCommand implements CommandInterface
{
    public function __construct(
        public LegacyConsigneeId $id,
    ) {}
}
