<?php

declare(strict_types=1);

namespace Src\Company\Application\Queries\GetCompanyById;

use Src\Company\Domain\ReadModels\CompanyRM;
use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryInterface;

/**
 * @see GetCompanyByIdHandler
 *
 * @implements QueryInterface<CompanyRM>
 */
final readonly class GetCompanyByIdQuery implements QueryInterface
{
    public function __construct(
        public LegacyCompanyId $id,
    ) {}
}
