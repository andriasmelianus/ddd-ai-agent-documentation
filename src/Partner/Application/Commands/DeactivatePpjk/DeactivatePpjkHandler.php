<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\DeactivatePpjk;

use Src\Partner\Domain\Exceptions\PpjkNotFoundException;
use Src\Partner\Domain\Repositories\PpjkRepositoryInterface;
use Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandHandlerInterface;

final readonly class DeactivatePpjkHandler implements CommandHandlerInterface
{
    public function __construct(
        private PpjkRepositoryInterface $repository,
    ) {}

    public function __invoke(DeactivatePpjkCommand $command): void
    {
        $ppjk = $this->repository->findById($command->id);

        if ($ppjk === null) {
            throw PpjkNotFoundException::withId($command->id);
        }

        $ppjk->deactivate();
        $this->repository->save($ppjk);
    }
}
