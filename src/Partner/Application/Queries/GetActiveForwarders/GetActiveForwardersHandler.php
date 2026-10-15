<?php

declare(strict_types=1);

namespace Src\Partner\Application\Queries\GetActiveForwarders;

use Src\Partner\Domain\ReadModels\ForwarderRM;
use Src\Partner\Domain\Repositories\ForwarderQueryInterface;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryHandlerInterface;

final readonly class GetActiveForwardersHandler implements QueryHandlerInterface
{
    public function __construct(
        private ForwarderQueryInterface $query,
    ) {}

    /**
     * @return ForwarderRM[]
     */
    public function __invoke(GetActiveForwardersQuery $query): array
    {
        return $this->query->findAllActive($query->companyId);
    }
}
