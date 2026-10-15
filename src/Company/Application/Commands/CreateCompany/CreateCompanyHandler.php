<?php

declare(strict_types=1);

namespace Src\Company\Application\Commands\CreateCompany;

use Src\Company\Domain\Entities\Company;
use Src\Company\Domain\Repositories\CompanyRepositoryInterface;
use Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandHandlerInterface;

final readonly class CreateCompanyHandler implements CommandHandlerInterface
{
    public function __construct(
        private CompanyRepositoryInterface $repository,
    ) {}

    public function __invoke(CreateCompanyCommand $command): void
    {
        $company = Company::create(
            id: $command->id,
            name: $command->name,
            phoneNumbers: $command->phoneNumbers,
            address: $command->address,
            taxInformation: $command->taxInformation,
            tpsConfiguration: $command->tpsConfiguration,
            ceisa40Credentials: $command->ceisa40Credentials,
            apiAccessConfiguration: $command->apiAccessConfiguration,
            logos: $command->logos,
            isActive: $command->isActive,
        );

        $this->repository->save($company);
    }
}
