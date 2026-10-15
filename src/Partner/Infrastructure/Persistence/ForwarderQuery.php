<?php

declare(strict_types=1);

namespace Src\Partner\Infrastructure\Persistence;

use App\Models\Forwarder as ForwarderModel;
use Src\Partner\Domain\ReadModels\ForwarderRM;
use Src\Partner\Domain\Repositories\ForwarderQueryInterface;
use Src\Partner\Domain\ValueObjects\LegacyForwarderId;
use Src\Partner\Infrastructure\Mappers\ForwarderMapper;

final readonly class ForwarderQuery implements ForwarderQueryInterface
{
    public function __construct(
        private ForwarderMapper $mapper,
    ) {}

    public function findById(LegacyForwarderId $id): ?ForwarderRM
    {
        /** @var ForwarderModel|null $model */
        $model = ForwarderModel::query()->find($id->value());

        if ($model === null) {
            return null;
        }

        return $this->mapper->toReadModel($model);
    }

    public function exists(LegacyForwarderId $id): bool
    {
        return ForwarderModel::query()->where('id', $id->value())->exists();
    }

    /**
     * @return ForwarderRM[]
     */
    public function findAllActive(?int $companyId = null): array
    {
        $query = ForwarderModel::query()->where('is_active', true);

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->get()
            ->map(fn (ForwarderModel $model) => $this->mapper->toReadModel($model))
            ->all();
    }
}
