<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Repositories;

use Src\Partner\Domain\Entities\Forwarder;
use Src\Partner\Domain\ValueObjects\LegacyForwarderId;

/**
 * Interface ForwarderRepositoryInterface
 *
 * CQRS Write Port: Used exclusively by Command Handlers to load Forwarder Aggregate Root
 * for state mutations and persist changes.
 */
interface ForwarderRepositoryInterface
{
    /**
     * Load a Forwarder Aggregate Root for state mutation.
     */
    public function findById(LegacyForwarderId $id): ?Forwarder;

    /**
     * Persist a Forwarder Aggregate Root (insert or update).
     */
    public function save(Forwarder $forwarder): void;

    /**
     * Delete a Forwarder Aggregate Root by its identifier.
     */
    public function delete(LegacyForwarderId $id): void;
}
