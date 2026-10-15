<?php

declare(strict_types=1);

namespace Src\Company\Domain\Events;

use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Company\Domain\ValueObjects\TaxInformation;
use Src\Shared\Framework\Domain\Events\DomainEvent;

final class CompanyTaxInformationUpdated extends DomainEvent
{
    public function __construct(
        public readonly LegacyCompanyId $companyId,
        public readonly TaxInformation $taxInformation,
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return 'company.tax_information_updated';
    }
}
