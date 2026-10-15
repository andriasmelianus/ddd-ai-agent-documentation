<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Repositories;

use Src\Partner\Domain\Entities\Ppjk;
use Src\Partner\Domain\ValueObjects\LegacyPpjkId;

/**
 * Interface PpjkRepositoryInterface
 *
 * CQRS Write Port: Used exclusively by Command Handlers to load PPJK Aggregate Root
 * for state mutations and persist changes.
 */
interface PpjkRepositoryInterface
{
    /**
     * Load a PPJK Aggregate Root for state mutation.
     */
    public function findById(LegacyPpjkId $id): ?Ppjk;

    /**
     * Persist a PPJK Aggregate Root (insert or update).
     */
    public function save(Ppjk $ppjk): void;

    /**
     * Delete a PPJK Aggregate Root by its identifier.
     */
    public function delete(LegacyPpjkId $id): void;
}
