<?php

declare(strict_types=1);

namespace Src\Partner\Application\Queries\GetActiveConsignees;

use Src\Partner\Domain\ReadModels\ConsigneeRM;
use Src\Partner\Domain\Repositories\ConsigneeQueryInterface;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryHandlerInterface;

final readonly class GetActiveConsigneesHandler implements QueryHandlerInterface
{
    public function __construct(
        private ConsigneeQueryInterface $query,
    ) {}

    /**
     * @return ConsigneeRM[]
     */
    public function __invoke(GetActiveConsigneesQuery $query): array
    {
        return $this->query->findAllActive($query->companyId);
    }
}
