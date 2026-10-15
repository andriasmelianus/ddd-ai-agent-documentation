<?php

declare(strict_types=1);

namespace Src\Partner\Application\Queries\GetActivePpjks;

use Src\Partner\Domain\ReadModels\PpjkRM;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryInterface;

/**
 * @see GetActivePpjksHandler
 *
 * @implements QueryInterface<PpjkRM[]>
 */
final readonly class GetActivePpjksQuery implements QueryInterface
{
    public function __construct(
        public ?int $companyId = null,
    ) {}
}
