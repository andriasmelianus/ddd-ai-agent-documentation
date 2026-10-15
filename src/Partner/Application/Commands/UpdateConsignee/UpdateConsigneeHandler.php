<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\UpdateConsignee;

use Src\Partner\Domain\Exceptions\ConsigneeNotFoundException;
use Src\Partner\Domain\Repositories\ConsigneeRepositoryInterface;
use Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandHandlerInterface;

final readonly class UpdateConsigneeHandler implements CommandHandlerInterface
{
    public function __construct(
        private ConsigneeRepositoryInterface $repository,
    ) {}

    public function __invoke(UpdateConsigneeCommand $command): void
    {
        $consignee = $this->repository->findById($command->id);

        if ($consignee === null) {
            throw ConsigneeNotFoundException::withId($command->id);
        }

        $consignee->updateDetails(
            name: $command->name,
            code: $command->code,
            contact: $command->contact,
            address: $command->address,
            taxIdentity: $command->taxIdentity,
            transactionTypeCode: $command->transactionTypeCode,
        );

        $this->repository->save($consignee);
    }
}
