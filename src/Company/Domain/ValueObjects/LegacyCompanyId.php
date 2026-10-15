<?php

declare(strict_types=1);

namespace Src\Company\Domain\ValueObjects;

use Src\Shared\Framework\Domain\ValueObjects\AutoIncrementIdentifier;

/**
 * Class LegacyCompanyId
 *
 * Identifier Value Object for Company aggregate using legacy auto-increment integer ID.
 */
final class LegacyCompanyId extends AutoIncrementIdentifier {}
