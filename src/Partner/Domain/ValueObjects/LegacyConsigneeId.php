<?php

declare(strict_types=1);

namespace Src\Partner\Domain\ValueObjects;

use Src\Shared\Framework\Domain\ValueObjects\AutoIncrementIdentifier;

/**
 * Class LegacyConsigneeId
 *
 * Identifier Value Object for Consignee aggregate using legacy auto-increment integer ID.
 */
final class LegacyConsigneeId extends AutoIncrementIdentifier {}
