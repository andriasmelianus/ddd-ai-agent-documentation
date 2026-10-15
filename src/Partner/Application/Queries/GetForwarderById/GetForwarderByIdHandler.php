<?php

declare(strict_types=1);

namespace Src\Partner\Application\Queries\GetForwarderById;

use Src\Partner\Domain\Exceptions\ForwarderNotFoundException;
use Src\Partner\Domain\ReadModels\ForwarderRM;
use Src\Partner\Domain\Repositories\ForwarderQueryInterface;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryHandlerInterface;

final readonly class GetForwarderByIdHandler implements QueryHandlerInterface
{
    public function __construct(
        private ForwarderQueryInterface $query,
    ) {}

    public function __invoke(GetForwarderByIdQuery $query): ForwarderRM
    {
        $forwarder = $this->query->findById($query->id);

        if ($forwarder === null) {
            throw ForwarderNotFoundException::withId($query->id);
        }

        return $forwarder;
    }
}
