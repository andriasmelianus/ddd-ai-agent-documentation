<?php

declare(strict_types=1);

namespace Src\Partner\Application\Queries\GetPpjkById;

use Src\Partner\Domain\Exceptions\PpjkNotFoundException;
use Src\Partner\Domain\ReadModels\PpjkRM;
use Src\Partner\Domain\Repositories\PpjkQueryInterface;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryHandlerInterface;

final readonly class GetPpjkByIdHandler implements QueryHandlerInterface
{
    public function __construct(
        private PpjkQueryInterface $query,
    ) {}

    public function __invoke(GetPpjkByIdQuery $query): PpjkRM
    {
        $ppjk = $this->query->findById($query->id);

        if ($ppjk === null) {
            throw PpjkNotFoundException::withId($query->id);
        }

        return $ppjk;
    }
}
