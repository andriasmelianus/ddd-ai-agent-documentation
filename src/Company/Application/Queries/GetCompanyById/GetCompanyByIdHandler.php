<?php

declare(strict_types=1);

namespace Src\Company\Application\Queries\GetCompanyById;

use Src\Company\Domain\Exceptions\CompanyNotFoundException;
use Src\Company\Domain\ReadModels\CompanyRM;
use Src\Company\Domain\Repositories\CompanyQueryInterface;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryHandlerInterface;

final readonly class GetCompanyByIdHandler implements QueryHandlerInterface
{
    public function __construct(
        private CompanyQueryInterface $query,
    ) {}

    public function __invoke(GetCompanyByIdQuery $query): CompanyRM
    {
        $company = $this->query->findById($query->id);

        if ($company === null) {
            throw CompanyNotFoundException::withId($query->id);
        }

        return $company;
    }
}
