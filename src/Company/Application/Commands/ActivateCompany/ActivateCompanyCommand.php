<?php

declare(strict_types=1);

namespace Src\Company\Application\Commands\ActivateCompany;

use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * @see ActivateCompanyHandler
 */
final readonly class ActivateCompanyCommand implements CommandInterface
{
    public function __construct(
        public LegacyCompanyId $id,
    ) {}
}
