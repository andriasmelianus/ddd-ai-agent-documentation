<?php

declare(strict_types=1);

namespace Src\Company\Application\Commands\UpdateCompanyAddress;

use Src\Company\Domain\ValueObjects\CompanyAddress;
use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * @see UpdateCompanyAddressHandler
 */
final readonly class UpdateCompanyAddressCommand implements CommandInterface
{
    public function __construct(
        public LegacyCompanyId $id,
        public CompanyAddress $address,
    ) {}
}
