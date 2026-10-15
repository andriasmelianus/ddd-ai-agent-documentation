<?php

declare(strict_types=1);

namespace Src\Partner\Infrastructure\Persistence;

use App\Models\Ppjk as PpjkModel;
use Src\Partner\Domain\Entities\Ppjk;
use Src\Partner\Domain\Repositories\PpjkRepositoryInterface;
use Src\Partner\Domain\ValueObjects\LegacyPpjkId;
use Src\Partner\Infrastructure\Mappers\PpjkMapper;
use Src\Shared\Framework\Infrastructure\Bus\EventBus\EventBusInterface;

final readonly class PpjkRepository implements PpjkRepositoryInterface
{
    public function __construct(
        private PpjkMapper $mapper,
        private ?EventBusInterface $eventBus = null,
    ) {}

    public function findById(LegacyPpjkId $id): ?Ppjk
    {
        /** @var PpjkModel|null $model */
        $model = PpjkModel::query()->find($id->value());

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function save(Ppjk $ppjk): void
    {
        $data = $this->mapper->toModel($ppjk);

        if ($ppjk->isNew()) {
            /** @var PpjkModel $model */
            $model = PpjkModel::query()->create($data);
            $ppjk->id->setAutoIncrement((int) $model->getKey());
            $ppjk->setIsNew(false);
        } else {
            PpjkModel::query()->where('id', $ppjk->id->value())->update($data);
        }

        $events = $ppjk->pullEvents();
        if ($this->eventBus !== null && ! empty($events)) {
            $this->eventBus->publishEvents($events);
        }
    }

    public function delete(LegacyPpjkId $id): void
    {
        PpjkModel::query()->where('id', $id->value())->delete();
    }
}
