<?php

declare(strict_types=1);

namespace Src\Company\Application\Queries\GetActiveCompanies;

use Src\Company\Domain\ReadModels\CompanyRM;
use Src\Company\Domain\Repositories\CompanyQueryInterface;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryHandlerInterface;

final readonly class GetActiveCompaniesHandler implements QueryHandlerInterface
{
    public function __construct(
        private CompanyQueryInterface $query,
    ) {}

    /**
     * @return CompanyRM[]
     */
    public function __invoke(GetActiveCompaniesQuery $query): array
    {
        return $this->query->findAllActive();
    }
}
