<?php

declare(strict_types=1);

namespace Src\Company\Application\Queries\GetCompanyByNpwp;

use Src\Company\Domain\ReadModels\CompanyRM;
use Src\Company\Domain\ValueObjects\Npwp;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryInterface;

/**
 * @see GetCompanyByNpwpHandler
 *
 * @implements QueryInterface<CompanyRM|null>
 */
final readonly class GetCompanyByNpwpQuery implements QueryInterface
{
    public function __construct(
        public Npwp $npwp,
    ) {}
}
