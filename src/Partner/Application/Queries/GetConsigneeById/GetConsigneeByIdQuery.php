<?php

declare(strict_types=1);

namespace Src\Partner\Application\Queries\GetConsigneeById;

use Src\Partner\Domain\ReadModels\ConsigneeRM;
use Src\Partner\Domain\ValueObjects\LegacyConsigneeId;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryInterface;

/**
 * @see GetConsigneeByIdHandler
 *
 * @implements QueryInterface<ConsigneeRM>
 */
final readonly class GetConsigneeByIdQuery implements QueryInterface
{
    public function __construct(
        public LegacyConsigneeId $id,
    ) {}
}
