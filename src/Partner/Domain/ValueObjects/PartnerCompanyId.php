<?php

declare(strict_types=1);

namespace Src\Partner\Domain\ValueObjects;

use Src\Shared\Framework\Domain\ValueObjects\IntegerIdentifier;

/**
 * Class PartnerCompanyId
 *
 * Foreign key identifier referencing the tenant Company.
 */
final class PartnerCompanyId extends IntegerIdentifier
{
    public static function fromInt(int $value): self
    {
        return self::create($value);
    }
}
