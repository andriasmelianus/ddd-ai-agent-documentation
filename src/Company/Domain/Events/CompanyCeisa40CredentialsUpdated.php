<?php

declare(strict_types=1);

namespace Src\Company\Domain\Events;

use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Shared\Framework\Domain\Events\DomainEvent;

final class CompanyCeisa40CredentialsUpdated extends DomainEvent
{
    public function __construct(
        public readonly LegacyCompanyId $companyId,
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return 'company.ceisa40_credentials_updated';
    }
}
