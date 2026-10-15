<?php

declare(strict_types=1);

namespace Src\Company\Domain\Events;

use Src\Company\Domain\ValueObjects\CompanyName;
use Src\Company\Domain\ValueObjects\CompanyPhoneNumbers;
use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Shared\Framework\Domain\Events\DomainEvent;

final class CompanyBasicInfoUpdated extends DomainEvent
{
    public function __construct(
        public readonly LegacyCompanyId $companyId,
        public readonly CompanyName $name,
        public readonly CompanyPhoneNumbers $phoneNumbers,
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return 'company.basic_info_updated';
    }
}
