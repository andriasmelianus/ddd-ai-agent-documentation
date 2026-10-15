<?php

declare(strict_types=1);

namespace Src\Company\Application\Queries\FindCompanyByTpsCode;

use Src\Company\Domain\ReadModels\CompanyRM;
use Src\Company\Domain\ValueObjects\TpsCode;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryInterface;

/**
 * @see FindCompanyByTpsCodeHandler
 *
 * @implements QueryInterface<CompanyRM|null>
 */
final readonly class FindCompanyByTpsCodeQuery implements QueryInterface
{
    public function __construct(
        public TpsCode $tpsCode,
    ) {}
}
