<?php

declare(strict_types=1);

namespace Src\Partner\Application\Queries\GetActivePpjks;

use Src\Partner\Domain\ReadModels\PpjkRM;
use Src\Partner\Domain\Repositories\PpjkQueryInterface;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryHandlerInterface;

final readonly class GetActivePpjksHandler implements QueryHandlerInterface
{
    public function __construct(
        private PpjkQueryInterface $query,
    ) {}

    /**
     * @return PpjkRM[]
     */
    public function __invoke(GetActivePpjksQuery $query): array
    {
        return $this->query->findAllActive($query->companyId);
    }
}
