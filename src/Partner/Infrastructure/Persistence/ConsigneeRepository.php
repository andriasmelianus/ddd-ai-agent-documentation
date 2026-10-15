<?php

declare(strict_types=1);

namespace Src\Partner\Infrastructure\Persistence;

use App\Models\Consignee as ConsigneeModel;
use Src\Partner\Domain\Entities\Consignee;
use Src\Partner\Domain\Repositories\ConsigneeRepositoryInterface;
use Src\Partner\Domain\ValueObjects\LegacyConsigneeId;
use Src\Partner\Infrastructure\Mappers\ConsigneeMapper;
use Src\Shared\Framework\Infrastructure\Bus\EventBus\EventBusInterface;

final readonly class ConsigneeRepository implements ConsigneeRepositoryInterface
{
    public function __construct(
        private ConsigneeMapper $mapper,
        private ?EventBusInterface $eventBus = null,
    ) {}

    public function findById(LegacyConsigneeId $id): ?Consignee
    {
        /** @var ConsigneeModel|null $model */
        $model = ConsigneeModel::query()->find($id->value());

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function save(Consignee $consignee): void
    {
        $data = $this->mapper->toModel($consignee);

        if ($consignee->isNew()) {
            /** @var ConsigneeModel $model */
            $model = ConsigneeModel::query()->create($data);
            $consignee->id->setAutoIncrement((int) $model->getKey());
            $consignee->setIsNew(false);
        } else {
            ConsigneeModel::query()->where('id', $consignee->id->value())->update($data);
        }

        $events = $consignee->pullEvents();
        if ($this->eventBus !== null && ! empty($events)) {
            $this->eventBus->publishEvents($events);
        }
    }

    public function delete(LegacyConsigneeId $id): void
    {
        ConsigneeModel::query()->where('id', $id->value())->delete();
    }
}
