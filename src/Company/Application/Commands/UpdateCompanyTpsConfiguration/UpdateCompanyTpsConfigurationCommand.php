<?php

declare(strict_types=1);

namespace Src\Company\Application\Commands\UpdateCompanyTpsConfiguration;

use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Company\Domain\ValueObjects\TpsConfiguration;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * @see UpdateCompanyTpsConfigurationHandler
 */
final readonly class UpdateCompanyTpsConfigurationCommand implements CommandInterface
{
    public function __construct(
        public LegacyCompanyId $id,
        public TpsConfiguration $tpsConfiguration,
    ) {}
}
