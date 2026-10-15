<?php

declare(strict_types=1);

namespace Src\Company\Domain\Events;

use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Shared\Framework\Domain\Events\DomainEvent;

final class CompanyDeactivated extends DomainEvent
{
    public function __construct(
        public readonly LegacyCompanyId $companyId,
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return 'company.deactivated';
    }
}
