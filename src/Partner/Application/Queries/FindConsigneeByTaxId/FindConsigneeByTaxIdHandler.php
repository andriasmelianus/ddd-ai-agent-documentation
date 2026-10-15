<?php

declare(strict_types=1);

namespace Src\Partner\Application\Queries\FindConsigneeByTaxId;

use Src\Partner\Domain\ReadModels\ConsigneeRM;
use Src\Partner\Domain\Repositories\ConsigneeQueryInterface;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryHandlerInterface;

final readonly class FindConsigneeByTaxIdHandler implements QueryHandlerInterface
{
    public function __construct(
        private ConsigneeQueryInterface $query,
    ) {}

    public function __invoke(FindConsigneeByTaxIdQuery $query): ?ConsigneeRM
    {
        return $this->query->findByTaxId($query->taxId, $query->companyId);
    }
}
