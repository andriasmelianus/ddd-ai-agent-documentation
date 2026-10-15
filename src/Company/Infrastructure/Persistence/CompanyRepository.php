<?php

declare(strict_types=1);

namespace Src\Company\Infrastructure\Persistence;

use App\Models\Company as CompanyModel;
use Src\Company\Domain\Entities\Company;
use Src\Company\Domain\Repositories\CompanyRepositoryInterface;
use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Company\Infrastructure\Mappers\CompanyMapper;
use Src\Shared\Framework\Infrastructure\Bus\EventBus\EventBusInterface;

/**
 * Class CompanyRepository
 *
 * Infrastructure implementation of CompanyRepositoryInterface (CQRS Write Port).
 */
final readonly class CompanyRepository implements CompanyRepositoryInterface
{
    public function __construct(
        private CompanyMapper $mapper,
        private ?EventBusInterface $eventBus = null,
    ) {}

    public function findById(LegacyCompanyId $id): ?Company
    {
        /** @var CompanyModel|null $model */
        $model = CompanyModel::query()->find($id->value());

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function save(Company $company): void
    {
        $data = $this->mapper->toModel($company);

        if ($company->isNew()) {
            /** @var CompanyModel $model */
            $model = CompanyModel::query()->create($data);
            $company->id->setAutoIncrement((int) $model->getKey());
            $company->setIsNew(false);
        } else {
            CompanyModel::query()->where('id', $company->id->value())->update($data);
        }

        // Release and dispatch domain events
        $events = $company->pullEvents();
        if ($this->eventBus !== null && ! empty($events)) {
            $this->eventBus->publishEvents($events);
        }
    }

    public function delete(LegacyCompanyId $id): void
    {
        CompanyModel::query()->where('id', $id->value())->delete();
    }
}
