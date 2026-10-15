<?php

declare(strict_types=1);

namespace Src\Partner\Application\Queries\GetForwarderById;

use Src\Partner\Domain\ReadModels\ForwarderRM;
use Src\Partner\Domain\ValueObjects\LegacyForwarderId;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryInterface;

/**
 * @see GetForwarderByIdHandler
 *
 * @implements QueryInterface<ForwarderRM>
 */
final readonly class GetForwarderByIdQuery implements QueryInterface
{
    public function __construct(
        public LegacyForwarderId $id,
    ) {}
}
