<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Repositories;

use Src\Partner\Domain\ReadModels\PpjkRM;
use Src\Partner\Domain\ValueObjects\LegacyPpjkId;

/**
 * Interface PpjkQueryInterface
 *
 * CQRS Read Port: Used exclusively by Query Handlers to query optimized ReadModels
 * without returning Domain Entities.
 */
interface PpjkQueryInterface
{
    /**
     * Query PPJK ReadModel by ID.
     */
    public function findById(LegacyPpjkId $id): ?PpjkRM;

    /**
     * Check if a PPJK exists.
     */
    public function exists(LegacyPpjkId $id): bool;

    /**
     * List all active PPJKs.
     *
     * @return PpjkRM[]
     */
    public function findAllActive(?int $companyId = null): array;
}
