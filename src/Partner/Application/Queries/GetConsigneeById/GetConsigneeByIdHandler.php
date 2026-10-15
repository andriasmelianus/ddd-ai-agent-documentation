<?php

declare(strict_types=1);

namespace Src\Partner\Application\Queries\GetConsigneeById;

use Src\Partner\Domain\Exceptions\ConsigneeNotFoundException;
use Src\Partner\Domain\ReadModels\ConsigneeRM;
use Src\Partner\Domain\Repositories\ConsigneeQueryInterface;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryHandlerInterface;

final readonly class GetConsigneeByIdHandler implements QueryHandlerInterface
{
    public function __construct(
        private ConsigneeQueryInterface $query,
    ) {}

    public function __invoke(GetConsigneeByIdQuery $query): ConsigneeRM
    {
        $consignee = $this->query->findById($query->id);

        if ($consignee === null) {
            throw ConsigneeNotFoundException::withId($query->id);
        }

        return $consignee;
    }
}
