<?php

declare(strict_types=1);

namespace Src\Company\Application\Queries\FindCompanyByTpsCode;

use Src\Company\Domain\ReadModels\CompanyRM;
use Src\Company\Domain\Repositories\CompanyQueryInterface;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryHandlerInterface;

final readonly class FindCompanyByTpsCodeHandler implements QueryHandlerInterface
{
    public function __construct(
        private CompanyQueryInterface $query,
    ) {}

    public function __invoke(FindCompanyByTpsCodeQuery $query): ?CompanyRM
    {
        return $this->query->findByTpsCode($query->tpsCode);
    }
}
