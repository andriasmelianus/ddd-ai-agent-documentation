<?php

declare(strict_types=1);

namespace Src\Company\Application\Queries\GetActiveCompanies;

use Src\Company\Domain\ReadModels\CompanyRM;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryInterface;

/**
 * @see GetActiveCompaniesHandler
 *
 * @implements QueryInterface<array<CompanyRM>>
 */
final readonly class GetActiveCompaniesQuery implements QueryInterface {}
