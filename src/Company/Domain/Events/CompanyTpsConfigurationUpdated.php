<?php

declare(strict_types=1);

namespace Src\Company\Domain\Events;

use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Company\Domain\ValueObjects\TpsConfiguration;
use Src\Shared\Framework\Domain\Events\DomainEvent;

final class CompanyTpsConfigurationUpdated extends DomainEvent
{
    public function __construct(
        public readonly LegacyCompanyId $companyId,
        public readonly TpsConfiguration $tpsConfiguration,
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return 'company.tps_configuration_updated';
    }
}
