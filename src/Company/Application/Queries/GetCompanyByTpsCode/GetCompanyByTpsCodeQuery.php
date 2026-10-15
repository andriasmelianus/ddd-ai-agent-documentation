<?php

declare(strict_types=1);

namespace Src\Company\Application\Queries\GetCompanyByTpsCode;

use Src\Company\Domain\ReadModels\CompanyRM;
use Src\Company\Domain\ValueObjects\TpsCode;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryInterface;

/**
 * @see GetCompanyByTpsCodeHandler
 *
 * @implements QueryInterface<CompanyRM|null>
 */
final readonly class GetCompanyByTpsCodeQuery implements QueryInterface
{
    public function __construct(
        public TpsCode $tpsCode,
    ) {}
}
