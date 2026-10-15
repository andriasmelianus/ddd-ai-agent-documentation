<?php

declare(strict_types=1);

namespace Src\Company\Application\Queries\FindCompanyByNpwp;

use Src\Company\Domain\ReadModels\CompanyRM;
use Src\Company\Domain\ValueObjects\Npwp;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryInterface;

/**
 * @see FindCompanyByNpwpHandler
 *
 * @implements QueryInterface<CompanyRM|null>
 */
final readonly class FindCompanyByNpwpQuery implements QueryInterface
{
    public function __construct(
        public Npwp $npwp,
    ) {}
}
