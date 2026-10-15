<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\DeactivateForwarder;

use Src\Partner\Domain\Exceptions\ForwarderNotFoundException;
use Src\Partner\Domain\Repositories\ForwarderRepositoryInterface;
use Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandHandlerInterface;

final readonly class DeactivateForwarderHandler implements CommandHandlerInterface
{
    public function __construct(
        private ForwarderRepositoryInterface $repository,
    ) {}

    public function __invoke(DeactivateForwarderCommand $command): void
    {
        $forwarder = $this->repository->findById($command->id);

        if ($forwarder === null) {
            throw ForwarderNotFoundException::withId($command->id);
        }

        $forwarder->deactivate();
        $this->repository->save($forwarder);
    }
}
