<?php

declare(strict_types=1);

namespace Src\Partner\Application\Commands\CreatePpjk;

use Src\Partner\Domain\Entities\Ppjk;
use Src\Partner\Domain\Repositories\PpjkRepositoryInterface;
use Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandHandlerInterface;

final readonly class CreatePpjkHandler implements CommandHandlerInterface
{
    public function __construct(
        private PpjkRepositoryInterface $repository,
    ) {}

    public function __invoke(CreatePpjkCommand $command): void
    {
        $ppjk = Ppjk::create(
            id: $command->id,
            companyId: $command->companyId,
            name: $command->name,
            code: $command->code,
            contact: $command->contact,
            address: $command->address,
            taxIdentity: $command->taxIdentity,
            isActive: $command->isActive,
        );

        $this->repository->save($ppjk);
    }
}
