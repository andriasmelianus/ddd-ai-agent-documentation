<?php

declare(strict_types=1);

namespace Src\Company\Application\Commands\DeactivateCompany;

use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * @see DeactivateCompanyHandler
 */
final readonly class DeactivateCompanyCommand implements CommandInterface
{
    public function __construct(
        public LegacyCompanyId $id,
    ) {}
}
