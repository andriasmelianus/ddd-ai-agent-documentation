<?php

declare(strict_types=1);

namespace Src\Partner\Infrastructure\Persistence;

use App\Models\Ppjk as PpjkModel;
use Src\Partner\Domain\ReadModels\PpjkRM;
use Src\Partner\Domain\Repositories\PpjkQueryInterface;
use Src\Partner\Domain\ValueObjects\LegacyPpjkId;
use Src\Partner\Infrastructure\Mappers\PpjkMapper;

final readonly class PpjkQuery implements PpjkQueryInterface
{
    public function __construct(
        private PpjkMapper $mapper,
    ) {}

    public function findById(LegacyPpjkId $id): ?PpjkRM
    {
        /** @var PpjkModel|null $model */
        $model = PpjkModel::query()->find($id->value());

        if ($model === null) {
            return null;
        }

        return $this->mapper->toReadModel($model);
    }

    public function exists(LegacyPpjkId $id): bool
    {
        return PpjkModel::query()->where('id', $id->value())->exists();
    }

    /**
     * @return PpjkRM[]
     */
    public function findAllActive(?int $companyId = null): array
    {
        $query = PpjkModel::query()->where('is_active', true);

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->get()
            ->map(fn (PpjkModel $model) => $this->mapper->toReadModel($model))
            ->all();
    }
}
