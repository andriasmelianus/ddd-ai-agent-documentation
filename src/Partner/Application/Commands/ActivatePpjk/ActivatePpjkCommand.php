<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\ActivatePpjk;

use Src\Partner\Domain\ValueObjects\LegacyPpjkId;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * @see ActivatePpjkHandler
 */
final readonly class ActivatePpjkCommand implements CommandInterface
{
    public function __construct(
        public LegacyPpjkId $id,
    ) {}
}
