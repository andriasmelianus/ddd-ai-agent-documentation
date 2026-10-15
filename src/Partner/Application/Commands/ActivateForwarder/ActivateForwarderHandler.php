<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\ActivateForwarder;

use Src\Partner\Domain\Exceptions\ForwarderNotFoundException;
use Src\Partner\Domain\Repositories\ForwarderRepositoryInterface;
use Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandHandlerInterface;

final readonly class ActivateForwarderHandler implements CommandHandlerInterface
{
    public function __construct(
        private ForwarderRepositoryInterface $repository,
    ) {}

    public function __invoke(ActivateForwarderCommand $command): void
    {
        $forwarder = $this->repository->findById($command->id);

        if ($forwarder === null) {
            throw ForwarderNotFoundException::withId($command->id);
        }

        $forwarder->activate();
        $this->repository->save($forwarder);
    }
}
