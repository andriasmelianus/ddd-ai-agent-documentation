<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\ActivatePpjk;

use Src\Partner\Domain\Exceptions\PpjkNotFoundException;
use Src\Partner\Domain\Repositories\PpjkRepositoryInterface;
use Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandHandlerInterface;

final readonly class ActivatePpjkHandler implements CommandHandlerInterface
{
    public function __construct(
        private PpjkRepositoryInterface $repository,
    ) {}

    public function __invoke(ActivatePpjkCommand $command): void
    {
        $ppjk = $this->repository->findById($command->id);

        if ($ppjk === null) {
            throw PpjkNotFoundException::withId($command->id);
        }

        $ppjk->activate();
        $this->repository->save($ppjk);
    }
}
