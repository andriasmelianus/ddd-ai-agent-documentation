<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\CreateForwarder;

use Src\Partner\Domain\Entities\Forwarder;
use Src\Partner\Domain\Repositories\ForwarderRepositoryInterface;
use Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandHandlerInterface;

final readonly class CreateForwarderHandler implements CommandHandlerInterface
{
    public function __construct(
        private ForwarderRepositoryInterface $repository,
    ) {}

    public function __invoke(CreateForwarderCommand $command): void
    {
        $forwarder = Forwarder::create(
            id: $command->id,
            companyId: $command->companyId,
            name: $command->name,
            code: $command->code,
            contact: $command->contact,
            address: $command->address,
            taxIdentity: $command->taxIdentity,
            isActive: $command->isActive,
        );

        $this->repository->save($forwarder);
    }
}
