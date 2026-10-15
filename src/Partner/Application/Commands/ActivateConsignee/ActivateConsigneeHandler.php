<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\ActivateConsignee;

use Src\Partner\Domain\Exceptions\ConsigneeNotFoundException;
use Src\Partner\Domain\Repositories\ConsigneeRepositoryInterface;
use Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandHandlerInterface;

final readonly class ActivateConsigneeHandler implements CommandHandlerInterface
{
    public function __construct(
        private ConsigneeRepositoryInterface $repository,
    ) {}

    public function __invoke(ActivateConsigneeCommand $command): void
    {
        $consignee = $this->repository->findById($command->id);

        if ($consignee === null) {
            throw ConsigneeNotFoundException::withId($command->id);
        }

        $consignee->activate();
        $this->repository->save($consignee);
    }
}
