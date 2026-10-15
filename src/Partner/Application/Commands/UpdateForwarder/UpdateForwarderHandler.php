<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\UpdateForwarder;

use Src\Partner\Domain\Exceptions\ForwarderNotFoundException;
use Src\Partner\Domain\Repositories\ForwarderRepositoryInterface;
use Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandHandlerInterface;

final readonly class UpdateForwarderHandler implements CommandHandlerInterface
{
    public function __construct(
        private ForwarderRepositoryInterface $repository,
    ) {}

    public function __invoke(UpdateForwarderCommand $command): void
    {
        $forwarder = $this->repository->findById($command->id);

        if ($forwarder === null) {
            throw ForwarderNotFoundException::withId($command->id);
        }

        $forwarder->updateDetails(
            name: $command->name,
            code: $command->code,
            contact: $command->contact,
            address: $command->address,
            taxIdentity: $command->taxIdentity,
        );

        $this->repository->save($forwarder);
    }
}
