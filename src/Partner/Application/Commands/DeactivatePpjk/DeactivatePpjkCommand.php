<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\DeactivatePpjk;

use Src\Partner\Domain\ValueObjects\LegacyPpjkId;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * @see DeactivatePpjkHandler
 */
final readonly class DeactivatePpjkCommand implements CommandInterface
{
    public function __construct(
        public LegacyPpjkId $id,
    ) {}
}
