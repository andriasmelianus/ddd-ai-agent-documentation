<?php

declare(strict_types=1);

namespace Src\Company\Domain\ValueObjects;

use Src\Company\Domain\Exceptions\InvalidWarehouseCapacityException;
use Stringable;

/**
 * Class WarehouseCapacity
 *
 * Immutable Value Object representing TPS warehouse capacity in cubic meters (m³).
 * Invariant: capacity must be greater than or equal to 0.
 */
readonly class WarehouseCapacity implements Stringable
{
    public float $value;

    public function __construct(float $value)
    {
        if ($value < 0.0) {
            throw InvalidWarehouseCapacityException::negative($value);
        }

        $this->value = round($value, 2);
    }

    public static function fromFloat(float $value): self
    {
        return new self($value);
    }

    public static function zero(): self
    {
        return new self(0.0);
    }

    public function value(): float
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return abs($this->value - $other->value) < 0.0001;
    }

    public function __toString(): string
    {
        return number_format($this->value, 2, '.', '');
    }
}
