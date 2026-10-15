<?php

declare(strict_types=1);

namespace Src\Company\Application\Commands\UpdateCompanyBasicInfo;

use Src\Company\Domain\ValueObjects\CompanyName;
use Src\Company\Domain\ValueObjects\CompanyPhoneNumbers;
use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * @see UpdateCompanyBasicInfoHandler
 */
final readonly class UpdateCompanyBasicInfoCommand implements CommandInterface
{
    public function __construct(
        public LegacyCompanyId $id,
        public CompanyName $name,
        public CompanyPhoneNumbers $phoneNumbers,
    ) {}
}
