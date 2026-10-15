<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Repositories;

use Src\Partner\Domain\Entities\Consignee;
use Src\Partner\Domain\ValueObjects\LegacyConsigneeId;

/**
 * Interface ConsigneeRepositoryInterface
 *
 * CQRS Write Port: Used exclusively by Command Handlers to load Consignee Aggregate Root
 * for state mutations and persist changes.
 */
interface ConsigneeRepositoryInterface
{
    /**
     * Load a Consignee Aggregate Root for state mutation.
     */
    public function findById(LegacyConsigneeId $id): ?Consignee;

    /**
     * Persist a Consignee Aggregate Root (insert or update).
     */
    public function save(Consignee $consignee): void;

    /**
     * Delete a Consignee Aggregate Root by its identifier.
     */
    public function delete(LegacyConsigneeId $id): void;
}
