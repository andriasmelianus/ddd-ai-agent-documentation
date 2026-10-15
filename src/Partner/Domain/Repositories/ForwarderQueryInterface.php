<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Repositories;

use Src\Partner\Domain\ReadModels\ForwarderRM;
use Src\Partner\Domain\ValueObjects\LegacyForwarderId;

/**
 * Interface ForwarderQueryInterface
 *
 * CQRS Read Port: Used exclusively by Query Handlers to query optimized ReadModels
 * without returning Domain Entities.
 */
interface ForwarderQueryInterface
{
    /**
     * Query Forwarder ReadModel by ID.
     */
    public function findById(LegacyForwarderId $id): ?ForwarderRM;

    /**
     * Check if a Forwarder exists.
     */
    public function exists(LegacyForwarderId $id): bool;

    /**
     * List all active forwarders.
     *
     * @return ForwarderRM[]
     */
    public function findAllActive(?int $companyId = null): array;
}
