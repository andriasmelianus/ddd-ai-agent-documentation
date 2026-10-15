<?php

declare(strict_types=1);

namespace Src\Company\Domain\Exceptions;

use DomainException;

final class InvalidWarehouseCapacityException extends DomainException
{
    public static function negative(float $value): self
    {
        return new self(sprintf(
            'Warehouse capacity cannot be negative, got %.2f.',
            $value,
        ));
    }
}
