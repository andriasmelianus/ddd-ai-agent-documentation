<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\CreateConsignee;

use Src\Partner\Domain\Entities\Consignee;
use Src\Partner\Domain\Repositories\ConsigneeRepositoryInterface;
use Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandHandlerInterface;

final readonly class CreateConsigneeHandler implements CommandHandlerInterface
{
    public function __construct(
        private ConsigneeRepositoryInterface $repository,
    ) {}

    public function __invoke(CreateConsigneeCommand $command): void
    {
        $consignee = Consignee::create(
            id: $command->id,
            companyId: $command->companyId,
            name: $command->name,
            code: $command->code,
            contact: $command->contact,
            address: $command->address,
            taxIdentity: $command->taxIdentity,
            transactionTypeCode: $command->transactionTypeCode,
            isActive: $command->isActive,
        );

        $this->repository->save($consignee);
    }
}
