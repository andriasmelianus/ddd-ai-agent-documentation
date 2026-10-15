<?php

declare(strict_types=1);

namespace Src\Company\Domain\Repositories;

use Src\Company\Domain\Entities\Company;
use Src\Company\Domain\ValueObjects\LegacyCompanyId;

/**
 * Interface CompanyRepositoryInterface
 *
 * CQRS Write Port: Used exclusively by Command Handlers to load Aggregate Root
 * for state mutations and persist changes.
 */
interface CompanyRepositoryInterface
{
    /**
     * Load a Company Aggregate Root for state mutation.
     */
    public function findById(LegacyCompanyId $id): ?Company;

    /**
     * Persist a Company Aggregate Root (insert or update).
     */
    public function save(Company $company): void;

    /**
     * Delete a Company Aggregate Root by its identifier.
     */
    public function delete(LegacyCompanyId $id): void;
}
