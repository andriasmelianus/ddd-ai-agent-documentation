<?php

declare(strict_types=1);

namespace Src\Company\Application\Commands\CreateCompany;

use Src\Company\Domain\ValueObjects\ApiAccessConfiguration;
use Src\Company\Domain\ValueObjects\Ceisa40Credentials;
use Src\Company\Domain\ValueObjects\CompanyAddress;
use Src\Company\Domain\ValueObjects\CompanyLogos;
use Src\Company\Domain\ValueObjects\CompanyName;
use Src\Company\Domain\ValueObjects\CompanyPhoneNumbers;
use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Company\Domain\ValueObjects\TaxInformation;
use Src\Company\Domain\ValueObjects\TpsConfiguration;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * @see CreateCompanyHandler
 */
final readonly class CreateCompanyCommand implements CommandInterface
{
    public function __construct(
        public LegacyCompanyId $id,
        public CompanyName $name,
        public ?CompanyPhoneNumbers $phoneNumbers = null,
        public ?CompanyAddress $address = null,
        public ?TaxInformation $taxInformation = null,
        public ?TpsConfiguration $tpsConfiguration = null,
        public ?Ceisa40Credentials $ceisa40Credentials = null,
        public ?ApiAccessConfiguration $apiAccessConfiguration = null,
        public ?CompanyLogos $logos = null,
        public bool $isActive = true,
    ) {}
}
