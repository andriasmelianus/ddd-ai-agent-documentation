<?php

declare(strict_types=1);

namespace Src\Company\Domain\ValueObjects;

use Src\Shared\Common\ValueObjects\BaseCode;

/**
 * Class TpsWarehouseCode
 *
 * Value Object representing Customs TPS Warehouse Code (Kode Gudang TPS).
 */
readonly class TpsWarehouseCode extends BaseCode
{
    public static function fromStringOrNull(?string $value): ?self
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return new self($value);
    }
}
