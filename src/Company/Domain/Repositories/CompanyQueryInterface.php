<?php

declare(strict_types=1);

namespace Src\Company\Domain\Repositories;

use Src\Company\Domain\ReadModels\CompanyRM;
use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Company\Domain\ValueObjects\Npwp;
use Src\Company\Domain\ValueObjects\TpsCode;

/**
 * Interface CompanyQueryInterface
 *
 * CQRS Read Port: Used exclusively by Query Handlers to query optimized ReadModels
 * and projections without returning Domain Entities.
 */
interface CompanyQueryInterface
{
    /**
     * Query Company ReadModel by ID.
     */
    public function findById(LegacyCompanyId $id): ?CompanyRM;

    /**
     * Query Company ReadModel by Customs TPS code.
     */
    public function findByTpsCode(TpsCode $tpsCode): ?CompanyRM;

    /**
     * Query Company ReadModel by Tax Identification Number (NPWP).
     */
    public function findByNpwp(Npwp $npwp): ?CompanyRM;

    /**
     * Check if a Company exists.
     */
    public function exists(LegacyCompanyId $id): bool;

    /**
     * List all active companies.
     *
     * @return CompanyRM[]
     */
    public function findAllActive(): array;
}
