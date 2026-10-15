<?php

declare(strict_types=1);

namespace Src\Partner\Application\Queries\GetActiveForwarders;

use Src\Partner\Domain\ReadModels\ForwarderRM;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryInterface;

/**
 * @see GetActiveForwardersHandler
 *
 * @implements QueryInterface<ForwarderRM[]>
 */
final readonly class GetActiveForwardersQuery implements QueryInterface
{
    public function __construct(
        public ?int $companyId = null,
    ) {}
}
