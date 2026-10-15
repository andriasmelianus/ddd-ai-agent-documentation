<?php

declare(strict_types=1);

namespace Src\Company\Application\Commands\UpdateCompanyTaxInformation;

use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Company\Domain\ValueObjects\TaxInformation;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * @see UpdateCompanyTaxInformationHandler
 */
final readonly class UpdateCompanyTaxInformationCommand implements CommandInterface
{
    public function __construct(
        public LegacyCompanyId $id,
        public TaxInformation $taxInformation,
    ) {}
}
