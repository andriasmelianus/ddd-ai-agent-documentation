<?php

declare(strict_types=1);

namespace Src\Company\Domain\Events;

use Src\Company\Domain\ValueObjects\CompanyName;
use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Shared\Framework\Domain\Events\DomainEvent;

final class CompanyCreated extends DomainEvent
{
    public function __construct(
        public readonly LegacyCompanyId $companyId,
        public readonly CompanyName $name,
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return 'company.created';
    }
}
