<?php

declare(strict_types=1);

namespace Src\Company\Application\Queries\FindCompanyByNpwp;

use Src\Company\Domain\ReadModels\CompanyRM;
use Src\Company\Domain\Repositories\CompanyQueryInterface;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryHandlerInterface;

final readonly class FindCompanyByNpwpHandler implements QueryHandlerInterface
{
    public function __construct(
        private CompanyQueryInterface $query,
    ) {}

    public function __invoke(FindCompanyByNpwpQuery $query): ?CompanyRM
    {
        return $this->query->findByNpwp($query->npwp);
    }
}
