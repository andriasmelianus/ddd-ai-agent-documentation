<?php

declare(strict_types=1);

namespace Src\Partner\Infrastructure\Persistence;

use App\Models\Forwarder as ForwarderModel;
use Src\Partner\Domain\Entities\Forwarder;
use Src\Partner\Domain\Repositories\ForwarderRepositoryInterface;
use Src\Partner\Domain\ValueObjects\LegacyForwarderId;
use Src\Partner\Infrastructure\Mappers\ForwarderMapper;
use Src\Shared\Framework\Infrastructure\Bus\EventBus\EventBusInterface;

final readonly class ForwarderRepository implements ForwarderRepositoryInterface
{
    public function __construct(
        private ForwarderMapper $mapper,
        private ?EventBusInterface $eventBus = null,
    ) {}

    public function findById(LegacyForwarderId $id): ?Forwarder
    {
        /** @var ForwarderModel|null $model */
        $model = ForwarderModel::query()->find($id->value());

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function save(Forwarder $forwarder): void
    {
        $data = $this->mapper->toModel($forwarder);

        if ($forwarder->isNew()) {
            /** @var ForwarderModel $model */
            $model = ForwarderModel::query()->create($data);
            $forwarder->id->setAutoIncrement((int) $model->getKey());
            $forwarder->setIsNew(false);
        } else {
            ForwarderModel::query()->where('id', $forwarder->id->value())->update($data);
        }

        $events = $forwarder->pullEvents();
        if ($this->eventBus !== null && ! empty($events)) {
            $this->eventBus->publishEvents($events);
        }
    }

    public function delete(LegacyForwarderId $id): void
    {
        ForwarderModel::query()->where('id', $id->value())->delete();
    }
}
