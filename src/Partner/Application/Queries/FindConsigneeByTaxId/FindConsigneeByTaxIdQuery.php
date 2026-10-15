<?php

declare(strict_types=1);

namespace Src\Partner\Application\Queries\FindConsigneeByTaxId;

use Src\Partner\Domain\ReadModels\ConsigneeRM;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryInterface;

/**
 * @see FindConsigneeByTaxIdHandler
 *
 * @implements QueryInterface<?ConsigneeRM>
 */
final readonly class FindConsigneeByTaxIdQuery implements QueryInterface
{
    public function __construct(
        public string $taxId,
        public ?int $companyId = null,
    ) {}
}
