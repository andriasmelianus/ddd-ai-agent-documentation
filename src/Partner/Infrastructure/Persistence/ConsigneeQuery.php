<?php

declare(strict_types=1);

namespace Src\Partner\Infrastructure\Persistence;

use App\Models\Consignee as ConsigneeModel;
use Src\Partner\Domain\ReadModels\ConsigneeRM;
use Src\Partner\Domain\Repositories\ConsigneeQueryInterface;
use Src\Partner\Domain\ValueObjects\LegacyConsigneeId;
use Src\Partner\Infrastructure\Mappers\ConsigneeMapper;

final readonly class ConsigneeQuery implements ConsigneeQueryInterface
{
    public function __construct(
        private ConsigneeMapper $mapper,
    ) {}

    public function findById(LegacyConsigneeId $id): ?ConsigneeRM
    {
        /** @var ConsigneeModel|null $model */
        $model = ConsigneeModel::query()->find($id->value());

        if ($model === null) {
            return null;
        }

        return $this->mapper->toReadModel($model);
    }

    public function findByTaxId(string $taxId, ?int $companyId = null): ?ConsigneeRM
    {
        $clean = preg_replace('/[^0-9]/', '', $taxId);

        $query = ConsigneeModel::query()
            ->where(function ($q) use ($taxId, $clean) {
                $q->where('tax_id', $taxId)
                    ->orWhere('tax_id_clean', $clean);
            });

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        /** @var ConsigneeModel|null $model */
        $model = $query->first();

        if ($model === null) {
            return null;
        }

        return $this->mapper->toReadModel($model);
    }

    public function exists(LegacyConsigneeId $id): bool
    {
        return ConsigneeModel::query()->where('id', $id->value())->exists();
    }

    /**
     * @return ConsigneeRM[]
     */
    public function findAllActive(?int $companyId = null): array
    {
        $query = ConsigneeModel::query()->where('is_active', true);

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->get()
            ->map(fn (ConsigneeModel $model) => $this->mapper->toReadModel($model))
            ->all();
    }
}
