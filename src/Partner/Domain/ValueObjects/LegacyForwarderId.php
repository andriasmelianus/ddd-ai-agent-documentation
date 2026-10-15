<?php

declare(strict_types=1);

namespace Src\Partner\Domain\ValueObjects;

use Src\Shared\Framework\Domain\ValueObjects\AutoIncrementIdentifier;

/**
 * Class LegacyForwarderId
 *
 * Identifier Value Object for Forwarder aggregate using legacy auto-increment integer ID.
 */
final class LegacyForwarderId extends AutoIncrementIdentifier {}
