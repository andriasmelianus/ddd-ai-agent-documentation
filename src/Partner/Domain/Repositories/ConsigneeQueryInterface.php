<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Repositories;

use Src\Partner\Domain\ReadModels\ConsigneeRM;
use Src\Partner\Domain\ValueObjects\LegacyConsigneeId;

/**
 * Interface ConsigneeQueryInterface
 *
 * CQRS Read Port: Used exclusively by Query Handlers to query optimized ReadModels
 * without returning Domain Entities.
 */
interface ConsigneeQueryInterface
{
    /**
     * Query Consignee ReadModel by ID.
     */
    public function findById(LegacyConsigneeId $id): ?ConsigneeRM;

    /**
     * Query Consignee ReadModel by Tax Identification Number (NPWP).
     */
    public function findByTaxId(string $taxId, ?int $companyId = null): ?ConsigneeRM;

    /**
     * Check if a Consignee exists.
     */
    public function exists(LegacyConsigneeId $id): bool;

    /**
     * List all active consignees.
     *
     * @return ConsigneeRM[]
     */
    public function findAllActive(?int $companyId = null): array;
}
