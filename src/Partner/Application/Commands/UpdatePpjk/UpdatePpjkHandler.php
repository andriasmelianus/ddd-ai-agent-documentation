<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\UpdatePpjk;

use Src\Partner\Domain\Exceptions\PpjkNotFoundException;
use Src\Partner\Domain\Repositories\PpjkRepositoryInterface;
use Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandHandlerInterface;

final readonly class UpdatePpjkHandler implements CommandHandlerInterface
{
    public function __construct(
        private PpjkRepositoryInterface $repository,
    ) {}

    public function __invoke(UpdatePpjkCommand $command): void
    {
        $ppjk = $this->repository->findById($command->id);

        if ($ppjk === null) {
            throw PpjkNotFoundException::withId($command->id);
        }

        $ppjk->updateDetails(
            name: $command->name,
            code: $command->code,
            contact: $command->contact,
            address: $command->address,
            taxIdentity: $command->taxIdentity,
        );

        $this->repository->save($ppjk);
    }
}
