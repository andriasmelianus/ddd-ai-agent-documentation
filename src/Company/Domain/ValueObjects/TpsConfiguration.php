<?php

declare(strict_types=1);

namespace Src\Company\Domain\ValueObjects;

/**
 * Class TpsConfiguration
 *
 * Composite Value Object representing Customs TPS Online configuration,
 * including TPS codes, decree, warehouse capacity, and TPS Online credentials.
 */
readonly class TpsConfiguration
{
    public function __construct(
        public ?TpsCode $code = null,
        public ?TpsWarehouseCode $warehouseCode = null,
        public ?TpsDecree $decree = null,
        public ?WarehouseCapacity $capacity = null,
        public ?string $username = null,
        public ?string $password = null,
    ) {}

    public static function create(
        ?TpsCode $code = null,
        ?TpsWarehouseCode $warehouseCode = null,
        ?TpsDecree $decree = null,
        ?WarehouseCapacity $capacity = null,
        ?string $username = null,
        ?string $password = null,
    ): self {
        return new self(
            $code,
            $warehouseCode,
            $decree,
            $capacity,
            $username !== null ? trim($username) : null,
            $password,
        );
    }

    public static function fromPrimitives(
        ?string $code = null,
        ?string $warehouseCode = null,
        ?string $decreeNumber = null,
        ?string $decreeDate = null,
        ?float $capacity = null,
        ?string $username = null,
        ?string $password = null,
    ): self {
        $decree = null;
        if ($decreeNumber !== null && trim($decreeNumber) !== '') {
            $decree = TpsDecree::fromPrimitives($decreeNumber, $decreeDate);
        }

        return self::create(
            code: TpsCode::fromStringOrNull($code),
            warehouseCode: TpsWarehouseCode::fromStringOrNull($warehouseCode),
            decree: $decree,
            capacity: $capacity !== null ? WarehouseCapacity::fromFloat($capacity) : null,
            username: $username,
            password: $password,
        );
    }

    public static function empty(): self
    {
        return new self;
    }

    public function equals(self $other): bool
    {
        $codeEquals = ($this->code === null && $other->code === null)
            || ($this->code !== null && $other->code !== null && $this->code->equals($other->code));

        $warehouseEquals = ($this->warehouseCode === null && $other->warehouseCode === null)
            || ($this->warehouseCode !== null && $other->warehouseCode !== null && $this->warehouseCode->equals($other->warehouseCode));

        $decreeEquals = ($this->decree === null && $other->decree === null)
            || ($this->decree !== null && $other->decree !== null && $this->decree->equals($other->decree));

        $capacityEquals = ($this->capacity === null && $other->capacity === null)
            || ($this->capacity !== null && $other->capacity !== null && $this->capacity->equals($other->capacity));

        return $codeEquals
            && $warehouseEquals
            && $decreeEquals
            && $capacityEquals
            && $this->username === $other->username
            && $this->password === $other->password;
    }
}
