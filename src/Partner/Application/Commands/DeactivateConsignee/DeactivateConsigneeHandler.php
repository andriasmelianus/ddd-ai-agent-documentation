<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\DeactivateConsignee;

use Src\Partner\Domain\Exceptions\ConsigneeNotFoundException;
use Src\Partner\Domain\Repositories\ConsigneeRepositoryInterface;
use Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandHandlerInterface;

final readonly class DeactivateConsigneeHandler implements CommandHandlerInterface
{
    public function __construct(
        private ConsigneeRepositoryInterface $repository,
    ) {}

    public function __invoke(DeactivateConsigneeCommand $command): void
    {
        $consignee = $this->repository->findById($command->id);

        if ($consignee === null) {
            throw ConsigneeNotFoundException::withId($command->id);
        }

        $consignee->deactivate();
        $this->repository->save($consignee);
    }
}
