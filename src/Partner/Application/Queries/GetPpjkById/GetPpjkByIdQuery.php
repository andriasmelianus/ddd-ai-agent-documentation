<?php

declare(strict_types=1);

namespace Src\Partner\Application\Queries\GetPpjkById;

use Src\Partner\Domain\ReadModels\PpjkRM;
use Src\Partner\Domain\ValueObjects\LegacyPpjkId;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryInterface;

/**
 * @see GetPpjkByIdHandler
 *
 * @implements QueryInterface<PpjkRM>
 */
final readonly class GetPpjkByIdQuery implements QueryInterface
{
    public function __construct(
        public LegacyPpjkId $id,
    ) {}
}
