<?php

declare(strict_types=1);

namespace Src\Company\Application\Commands\DeactivateCompany;

use Src\Company\Domain\Exceptions\CompanyNotFoundException;
use Src\Company\Domain\Repositories\CompanyRepositoryInterface;
use Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandHandlerInterface;

final readonly class DeactivateCompanyHandler implements CommandHandlerInterface
{
    public function __construct(
        private CompanyRepositoryInterface $repository,
    ) {}

    public function __invoke(DeactivateCompanyCommand $command): void
    {
        $company = $this->repository->findById($command->id);

        if ($company === null) {
            throw CompanyNotFoundException::withId($command->id);
        }

        $company->deactivate();

        $this->repository->save($company);
    }
}
