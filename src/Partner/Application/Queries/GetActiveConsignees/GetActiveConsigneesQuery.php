<?php

declare(strict_types=1);

namespace Src\Partner\Application\Queries\GetActiveConsignees;

use Src\Partner\Domain\ReadModels\ConsigneeRM;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryInterface;

/**
 * @see GetActiveConsigneesHandler
 *
 * @implements QueryInterface<ConsigneeRM[]>
 */
final readonly class GetActiveConsigneesQuery implements QueryInterface
{
    public function __construct(
        public ?int $companyId = null,
    ) {}
}
