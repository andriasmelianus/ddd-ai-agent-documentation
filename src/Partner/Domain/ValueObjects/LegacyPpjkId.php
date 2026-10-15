<?php

declare(strict_types=1);

namespace Src\Partner\Domain\ValueObjects;

use Src\Shared\Framework\Domain\ValueObjects\AutoIncrementIdentifier;

/**
 * Class LegacyPpjkId
 *
 * Identifier Value Object for PPJK aggregate using legacy auto-increment integer ID.
 */
final class LegacyPpjkId extends AutoIncrementIdentifier {}
